<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\User;
use App\Models\SystemSetting;
use App\Models\ActivityLog;
use App\Notifications\SubjectValidated;
use App\Notifications\TeacherAssigned;
use App\Notifications\SubjectRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepartmentCpController extends Controller
{
    /**
     * Dashboard du CP (Boîte de réception, arbitrage, soutenances)
     */
    public function index()
    {
        $user = Auth::user();
        $departmentId = $user->department_id;

        // 1. Sujets en attente (Arbitrage)
        // Regrouper les propositions par étudiant
        $pendingSubjects = Subject::with('student')
            ->where('department_id', $departmentId)
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->groupBy('student_id');
            
        // Pour chaque sujet en attente, on calcule une similarité textuelle avec les sujets validés du département
        foreach ($pendingSubjects as $studentId => $subjects) {
            foreach ($subjects as $subject) {
                // Recherche de similarité via recherche plein texte native PostgreSQL
                $similarSubjects = DB::select("
                    SELECT id, title,
                    ts_rank(to_tsvector('french', title || ' ' || COALESCE(description, '')), plainto_tsquery('french', ?)) AS score
                    FROM subjects
                    WHERE status = 'validated' AND department_id = ?
                    ORDER BY score DESC
                    LIMIT 1
                ", [$subject->title . ' ' . $subject->description, $departmentId]);
                
                $topScore = !empty($similarSubjects) ? $similarSubjects[0]->score : 0;
                
                // Conversion arbitraire du score pour l'indicateur visuel (0-100)
                $subject->similarity_score = min(100, round($topScore * 100));
            }
        }

        // 2. Soutenances à planifier
        $defenseSubjects = Subject::with(['student', 'teacher', 'defenseSchedule'])
            ->where('department_id', $departmentId)
            ->where('defense_validated', true)
            ->latest()
            ->get();

        // 3. Enseignants du département pour l'assignation du directeur ou du jury
        $teachers = User::role('Enseignant')
            ->where('department_id', $departmentId)
            ->withCount(['supervisedSubjects' => function ($query) {
                $query->whereIn('status', ['validated']);
            }])
            ->get();

        $maxStudents = SystemSetting::get('max_students_per_teacher', 5);

        // 4. Tous les sujets du département (pour le tableau récapitulatif)
        $allSubjects = Subject::where('department_id', $departmentId)
            ->with(['student', 'teacher', 'thesisFiles', 'defenseSchedule'])
            ->latest()
            ->get();

        return view('cp.dashboard', compact('pendingSubjects', 'defenseSubjects', 'allSubjects', 'teachers', 'maxStudents'));
    }

    /**
     * Détail d'un enseignant : liste de ses étudiants dirigés avec leurs sujets et jalons.
     */
    public function teacherDetail(User $teacher)
    {
        $user = Auth::user();

        // Sécurité : le CP ne peut voir que les enseignants de son département
        if ($teacher->department_id !== $user->department_id) {
            abort(403, 'Cet enseignant ne fait pas partie de votre département.');
        }

        // S'assurer que c'est bien un enseignant
        if (!$teacher->hasRole('Enseignant')) {
            abort(404, 'Cet utilisateur n\'est pas un enseignant.');
        }

        $teacher->load([
            'supervisedSubjects' => function ($query) {
                $query->with(['student', 'milestones', 'thesisFiles', 'defenseSchedule'])
                      ->orderBy('created_at', 'desc');
            }
        ]);

        return view('cp.teacher-detail', compact('teacher'));
    }

    /**
     * Arbitrage et Validation (Acceptation d'un sujet, rejet des autres)
     */
    public function arbitrate(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);
        $user = Auth::user();

        // Check policy
        if ($subject->department_id !== $user->department_id) {
            abort(403, 'Vous ne pouvez valider que les sujets de votre filière.');
        }

        $request->validate([
            'teacher_id' => 'required|integer|exists:users,id',
        ]);

        $teacher = User::role('Enseignant')
            ->where('department_id', $subject->department_id)
            ->find($request->integer('teacher_id'));

        if (!$teacher) {
            return back()->withErrors(['teacher_id' => 'L\'enseignant sélectionné est invalide pour cette filière.'])->withInput();
        }

        // Quota
        $maxStudents = SystemSetting::get('max_students_per_teacher', 5);
        $currentWorkload = $teacher->supervisedSubjects()->where('status', 'validated')->count();

        if ($currentWorkload >= $maxStudents) {
            return back()->withErrors(['teacher_id' => 'Cet enseignant a atteint le quota maximum.'])->withInput();
        }

        DB::beginTransaction();
        try {
            // Validate selected subject
            $subject->update([
                'status' => 'validated',
                'teacher_id' => $teacher->id,
            ]);

            ActivityLog::log(
                'VALIDATION_SUJET',
                'Le sujet "' . $subject->title . '" (ID #' . $subject->id . ') a été validé par le CP ' . $user->name . ' et rattaché au Directeur ID #' . $teacher->id . '.',
                $subject
            );

            // Reject other pending proposals for this student
            $otherSubjects = Subject::where('student_id', $subject->student_id)
                ->where('status', 'pending')
                ->where('id', '!=', $subject->id)
                ->get();

            foreach ($otherSubjects as $otherSubject) {
                $otherSubject->update([
                    'status' => 'rejected',
                    'rejection_reason' => 'Une autre proposition de sujet a été validée pour vous.',
                ]);
                
                if ($otherSubject->student) {
                    $otherSubject->student->notify(new SubjectRejected($otherSubject));
                }
            }

            DB::commit();

            // Notifications
            if ($subject->student) {
                $subject->student->notify(new SubjectValidated($subject));
            }
            $teacher->notify(new TeacherAssigned($subject));

            return back()->with('success', 'Sujet validé avec succès. Les autres propositions de l\'étudiant ont été classées sans suite.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de l\'arbitrage.');
        }
    }

    /**
     * Planification de la soutenance (Date, Salle, Jury JSONB)
     */
    public function scheduleDefense(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);
        $user = Auth::user();

        if ($subject->department_id !== $user->department_id) {
            abort(403, 'Vous ne pouvez planifier que les sujets de votre filière.');
        }

        if (!$subject->defense_validated) {
            return back()->with('error', 'Le directeur de mémoire doit d\'abord autoriser la soutenance.');
        }

        $request->validate([
            'defense_date' => 'required|date|after:today',
            'defense_room' => 'required|string|max:100',
            'president_id' => 'required|integer|exists:users,id',
            'secretary_id' => 'required|integer|exists:users,id',
            'member_id'    => 'required|integer|exists:users,id',
        ]);

        $president = User::find($request->president_id);
        $secretary = User::find($request->secretary_id);
        $member = User::find($request->member_id);

        $jury = [
            'president' => ['user_id' => $president->id, 'name' => $president->name],
            'secretary' => ['user_id' => $secretary->id, 'name' => $secretary->name],
            'membre'    => ['user_id' => $member->id, 'name' => $member->name],
        ];

        \App\Models\DefenseSchedule::updateOrCreate(
            ['subject_id' => $subject->id],
            [
                'defense_date' => $request->defense_date,
                'room'         => $request->defense_room,
                'jury_members' => $jury,
            ]
        );

        ActivityLog::log(
            'PLANIFICATION_SOUTENANCE',
            'Soutenance planifiée pour le sujet ID #' . $subject->id . ' le ' . $request->defense_date . ' en salle ' . $request->defense_room . ' par le CP ' . $user->name . '.',
            $subject
        );

        return back()->with('success', 'Soutenance planifiée avec succès.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Subject;
use App\Models\User;
use App\Notifications\DefenseAuthorized;
use App\Notifications\DefenseAuthorizationRevoked;
use App\Notifications\NewSubjectSubmitted;
use App\Notifications\SubjectRejected;
use App\Notifications\SubjectValidated;
use App\Notifications\TeacherAssigned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class SubjectController extends Controller
{
    /**
     * Afficher les sujets (filtré selon le rôle).
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('Etudiant')) {
            $baseQuery = Subject::where('student_id', $user->id);
        } elseif ($user->hasRole('Chef de département')) {
            $baseQuery = Subject::where('department_id', $user->department_id);
        } elseif ($user->hasRole('Enseignant')) {
            $baseQuery = Subject::where('teacher_id', $user->id);
        } else {
            $baseQuery = Subject::query();
        }

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('student', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('status')) {
            $baseQuery->where('status', $request->status);
        }
        if ($request->filled('department_id') && $user->hasRole('Admin')) {
            $baseQuery->where('department_id', $request->department_id);
        }

        $countQuery = clone $baseQuery;
        $subjects = (clone $baseQuery)->with(['student', 'teacher', 'department'])->latest()->paginate(15)->withQueryString();

        $counts = [
            'pending' => (clone $countQuery)->where('status', 'pending')->count(),
            'validated' => (clone $countQuery)->where('status', 'validated')->count(),
            'rejected' => (clone $countQuery)->where('status', 'rejected')->count(),
        ];

        $departments = $user->hasRole('Admin') ? Department::orderBy('name')->get() : collect();

        return view('subjects.index', compact('subjects', 'counts', 'departments'));
    }

    /**
     * Afficher le détail d'un sujet.
     */
    public function show(Subject $subject)
    {
        $user = Auth::user();

        // Vérifier l'accès selon le rôle
        if ($user->hasRole('Etudiant') && $subject->student_id !== $user->id) {
            abort(403, 'Vous ne pouvez consulter que vos propres sujets.');
        }
        if ($user->hasRole('Chef de département') && $subject->department_id !== $user->department_id) {
            abort(403, 'Ce sujet ne fait pas partie de votre filière.');
        }
        if ($user->hasRole('Enseignant') && $subject->teacher_id !== $user->id) {
            abort(403, 'Vous n\'êtes pas l\'encadreur de ce sujet.');
        }

        $subject->load(['student', 'teacher', 'department', 'academicYear', 'thesisFiles.aiReport']);

        return view('subjects.show', compact('subject'));
    }

    /**
     * Exporter les sujets en CSV.
     */
    public function export(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('Etudiant')) {
            $subjects = Subject::where('student_id', $user->id)->with(['student', 'teacher', 'department'])->get();
        } elseif ($user->hasRole('Chef de département')) {
            $subjects = Subject::where('department_id', $user->department_id)->with(['student', 'teacher', 'department'])->get();
        } elseif ($user->hasRole('Enseignant')) {
            $subjects = Subject::where('teacher_id', $user->id)->with(['student', 'teacher', 'department'])->get();
        } else {
            $subjects = Subject::with(['student', 'teacher', 'department'])->get();
        }

        $csvData = "Titre;Type;Etudiant;Matricule;Encadreur;Filiere;Statut;Date\n";
        foreach ($subjects as $s) {
            $title = str_replace('"', '""', $s->title);
            $csvData .= implode(';', [
                '"' . $title . '"',
                $s->subject_type === 'tfc' ? 'TFC' : ($s->subject_type === 'memoire' ? 'Memoire' : '-'),
                '"' . ($s->student->name ?? '-') . '"',
                $s->student->matricule ?? '-',
                '"' . ($s->teacher->name ?? 'Non assigne') . '"',
                '"' . ($s->department->name ?? '-') . '"',
                $s->status === 'validated' ? 'Valide' : ($s->status === 'pending' ? 'En attente' : 'Rejete'),
                $s->created_at->format('d/m/Y'),
            ]) . "\n";
        }

        return Response::make("\xEF\xBB\xBF" . $csvData, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="sujets-export-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Formulaire de soumission de sujet (wizard 5 étapes).
     */
    public function create()
    {
        $user = Auth::user();
        $user->load('department');

        return view('subjects.create', compact('user'));
    }

    /**
     * L'étudiant soumet un sujet structuré (status = pending).
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'               => 'required|string|max:255',
            'subject_type'        => 'nullable|in:tfc,memoire',
            'context_relevance'   => 'required|string|min:30',
            'challenges'          => 'required|string|min:30',
            'research_question'   => 'required|string|max:500',
            'hypothesis'          => 'required|string|min:20',
            'general_objective'   => 'required|string|min:20',
            'specific_objectives' => 'required|array|min:1',
            'specific_objectives.*' => 'required|string|min:5',
            'state_of_art'        => 'nullable|array',
            'state_of_art.*.author'       => 'nullable|string|max:255',
            'state_of_art.*.institution'  => 'nullable|string|max:255',
            'state_of_art.*.contribution' => 'nullable|string|max:500',
            'demarcations'        => 'nullable|string',
            'methodologies'       => 'nullable|string',
        ]);

        $user = Auth::user();

        // Vérifier que l'étudiant n'a pas déjà un sujet validé
        $existingValidated = Subject::where('student_id', $user->id)
            ->where('status', 'validated')
            ->first();

        if ($existingValidated) {
            return back()->with('error', 'Vous avez déjà un sujet validé.');
        }

        // Vérifier qu'il n'a pas déjà un sujet en attente
        $existingPending = Subject::where('student_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingPending) {
            return back()->with('error', 'Vous avez déjà un sujet en attente de validation.');
        }

        // Filtrer les objectifs vides et les références vides
        $objectives = array_values(array_filter($request->specific_objectives, fn($o) => trim($o) !== ''));
        $stateOfArt = collect($request->state_of_art ?? [])->filter(fn($r) => !empty(trim($r['author'] ?? '')))->values()->toArray();

        // Générer une description synthétique à partir des champs structurés
        $description = "Contexte : " . \Illuminate\Support\Str::limit($request->context_relevance, 200)
            . "\nProblématique : " . $request->research_question
            . "\nHypothèse : " . \Illuminate\Support\Str::limit($request->hypothesis, 200)
            . "\nObjectif : " . \Illuminate\Support\Str::limit($request->general_objective, 200);

        $subject = Subject::create([
            'title'               => $request->title,
            'subject_type'        => $request->subject_type,
            'description'         => $description,
            'context_relevance'   => $request->context_relevance,
            'challenges'          => $request->challenges,
            'research_question'   => $request->research_question,
            'hypothesis'          => $request->hypothesis,
            'general_objective'   => $request->general_objective,
            'specific_objectives' => $objectives,
            'state_of_art'        => $stateOfArt,
            'demarcations'        => $request->demarcations,
            'methodologies'       => $request->methodologies,
            'status'              => 'pending',
            'student_id'          => $user->id,
            'department_id'       => $user->department_id,
            'academic_year_id'    => AcademicYear::current()?->id,
        ]);

        // Notifier le(s) Chef(s) de Département
        $subject->load('student');
        $chefsDept = User::role('Chef de département')
            ->where('department_id', $user->department_id)
            ->get();
        foreach ($chefsDept as $chef) {
            $chef->notify(new NewSubjectSubmitted($subject));
        }

        return redirect()->route('dashboard')->with('success', 'Fiche de proposition soumise avec succès ! En attente de validation.');
    }

    /**
     * Le CP valide un sujet et assigne un enseignant.
     */
    public function validateSubject(Request $request, Subject $subject)
    {
        // Vérifier que le CP appartient au même département que le sujet
        if ($subject->department_id !== Auth::user()->department_id) {
            abort(403, 'Vous ne pouvez valider que les sujets de votre filière.');
        }

        $request->validate([
            'teacher_id' => 'required|integer|exists:users,id',
        ]);

        // Sécurité métier: l'enseignant assigné doit être un Enseignant de la même filière.
        $teacher = User::role('Enseignant')
            ->where('department_id', $subject->department_id)
            ->find($request->integer('teacher_id'));

        if (!$teacher) {
            return back()
                ->withErrors(['teacher_id' => 'L\'enseignant sélectionné est invalide pour cette filière.'])
                ->withInput();
        }

        $subject->update([
            'status' => 'validated',
            'teacher_id' => $teacher->id,
        ]);

        // Notifier l'étudiant
        $subject->load('teacher');
        if ($subject->student) {
            $subject->student->notify(new SubjectValidated($subject));
        }

        // Notifier l'enseignant assigné
        $subject->load('student');
        $teacher->notify(new TeacherAssigned($subject));

        return back()->with('success', 'Sujet validé et enseignant assigné avec succès.');
    }

    /**
     * Le CP rejette un sujet.
     */
    public function rejectSubject(Request $request, Subject $subject)
    {
        // Vérifier que le CP appartient au même département que le sujet
        if ($subject->department_id !== Auth::user()->department_id) {
            abort(403, 'Vous ne pouvez rejeter que les sujets de votre filière.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        $subject->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Notifier l'étudiant
        $subject->student->notify(new SubjectRejected($subject));

        return back()->with('success', 'Sujet rejeté.');
    }

    /**
     * Le CP programme la date et la salle de la soutenance.
     */
    public function scheduleDefense(Request $request, Subject $subject)
    {
        // Vérifier que le CP appartient au même département que le sujet
        if ($subject->department_id !== Auth::user()->department_id) {
            abort(403, 'Vous ne pouvez planifier que les sujets de votre filière.');
        }

        // Vérifier si le sujet a le feu vert
        if (!$subject->defense_validated) {
            return back()->with('error', 'Le directeur de mémoire doit d\'abord autoriser la soutenance.');
        }

        $request->validate([
            'defense_date' => 'required|date|after:today',
            'defense_room' => 'required|string|max:100',
        ]);

        $subject->update([
            'defense_date' => $request->defense_date,
            'defense_room' => $request->defense_room,
        ]);

        return back()->with('success', 'Date et salle de soutenance enregistrées avec succès.');
    }

    /**
     * L'Enseignant autorise la soutenance (Feu Vert).
     */
    public function authorizeDefense(Subject $subject)
    {
        $user = Auth::user();

        // Vérifier que l'enseignant est bien l'encadreur du sujet
        if ($subject->teacher_id !== $user->id) {
            abort(403, 'Vous n\'êtes pas l\'encadreur de ce sujet.');
        }

        // Vérifier que le sujet est validé
        if ($subject->status !== 'validated') {
            return back()->with('error', 'Ce sujet n\'est pas encore validé.');
        }

        // Vérifier qu'il n'est pas déjà autorisé
        if ($subject->defense_validated) {
            return back()->with('info', 'La soutenance est déjà autorisée pour ce sujet.');
        }

        $subject->update([
            'defense_validated' => true,
            'defense_revocation_reason' => null,
        ]);

        // Notifier l'étudiant du Feu Vert
        $subject->load('teacher');
        $subject->student->notify(new DefenseAuthorized($subject));

        return back()->with('success', 'Soutenance autorisée avec succès (Feu Vert) !');
    }

    /**
     * L'Enseignant retire l'autorisation de soutenance (retrait du Feu Vert).
     */
    public function revokeDefenseAuthorization(Request $request, Subject $subject)
    {
        $user = Auth::user();

        // Vérifier que l'enseignant est bien l'encadreur du sujet
        if ($subject->teacher_id !== $user->id) {
            abort(403, 'Vous n\'êtes pas l\'encadreur de ce sujet.');
        }

        // Vérifier qu'un Feu Vert a déjà été accordé
        if (!$subject->defense_validated) {
            return back()->with('info', 'La soutenance n\'est pas encore autorisée pour ce sujet.');
        }

        // Empêcher le retrait si la version finale est déjà déposée
        $hasFinalVersion = $subject->thesisFiles()
            ->where('version_type', 'final')
            ->exists();

        if ($hasFinalVersion) {
            return back()->with('error', 'Impossible de retirer le Feu Vert après le dépôt de la version finale.');
        }

        $validated = $request->validate([
            'defense_revocation_reason' => 'required|string|min:10|max:1000',
        ], [
            'defense_revocation_reason.required' => 'Le motif du retrait est obligatoire.',
            'defense_revocation_reason.min' => 'Le motif du retrait doit contenir au moins 10 caractères.',
            'defense_revocation_reason.max' => 'Le motif du retrait ne peut pas dépasser 1000 caractères.',
        ]);

        $subject->update([
            'defense_validated' => false,
            'defense_date' => null,
            'defense_room' => null,
            'defense_revocation_reason' => $validated['defense_revocation_reason'],
        ]);

        // Notifier l'étudiant du retrait
        $subject->load('teacher');
        if ($subject->student) {
            $subject->student->notify(new DefenseAuthorizationRevoked($subject));
        }

        return back()->with('success', 'Autorisation de soutenance retirée avec succès.');
    }
}

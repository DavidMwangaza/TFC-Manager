<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Milestone;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Affiche le dashboard approprié selon le rôle.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('Admin')) {
            return $this->adminDashboard();
        } elseif ($user->hasRole('Doyen')) {
            return $this->doyenDashboard($user);
        } elseif ($user->hasRole('Chef de département')) {
            return $this->cpDashboard($user);
        } elseif ($user->hasRole('Enseignant')) {
            return $this->teacherDashboard($user);
        } elseif ($user->hasRole('Appariteur')) {
            return $this->appariteurDashboard($user);
        } else {
            return $this->studentDashboard($user);
        }
    }

    /**
     * Dashboard Admin.
     */
    private function adminDashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_subjects' => Subject::count(),
            'pending_subjects' => Subject::where('status', 'pending')->count(),
            'validated_subjects' => Subject::where('status', 'validated')->count(),
            'rejected_subjects' => Subject::where('status', 'rejected')->count(),
            'total_departments' => Department::count(),
            'total_faculties' => \App\Models\Faculty::count(),
            'blocked_users' => User::where('is_blocked', true)->count(),
        ];

        // Données pour les graphiques
        $chartSubjectsByStatus = [
            'labels' => ['En attente', 'Validés', 'Rejetés'],
            'data' => [$stats['pending_subjects'], $stats['validated_subjects'], $stats['rejected_subjects']],
            'colors' => ['#f59e0b', '#10b981', '#ef4444'],
        ];

        $departments = Department::withCount('subjects')->get();
        $chartSubjectsByDept = [
            'labels' => $departments->pluck('name')->toArray(),
            'data' => $departments->pluck('subjects_count')->toArray(),
        ];

        $currentYear = AcademicYear::current();
        $recentLogs = ActivityLog::with('user')->latest()->take(10)->get();
        $recentUsers = User::with(['department', 'roles'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'currentYear', 'recentLogs', 'recentUsers', 'chartSubjectsByStatus', 'chartSubjectsByDept'));
    }

    /**
     * Dashboard Chef de département.
     */
    private function cpDashboard(User $user)
    {
        $pendingSubjects = Subject::where('department_id', $user->department_id)
            ->where('status', 'pending')
            ->with('student')
            ->latest()
            ->get();

        $allSubjects = Subject::where('department_id', $user->department_id)
            ->with(['student', 'teacher', 'thesisFiles', 'defenseSchedule'])
            ->latest()
            ->get();

        $teachers = User::role('Enseignant')
            ->where('department_id', $user->department_id)
            ->with('supervisedSubjects')
            ->get();

        return view('cp.dashboard', compact('pendingSubjects', 'allSubjects', 'teachers'));
    }

    /**
     * Dashboard Enseignant.
     */
    private function teacherDashboard(User $user)
    {
        $supervisedSubjects = Subject::where('teacher_id', $user->id)
            ->with(['student', 'thesisFiles.aiReport'])
            ->get();

        // Jalons en attente de correction par cet enseignant
        $pendingMilestones = Milestone::where('status', 'submitted')
            ->whereHas('subject', function($q) use ($user) {
                $q->where('teacher_id', $user->id);
            })
            ->with(['subject.student', 'thesisFile'])
            ->orderBy('submission_date', 'asc')
            ->get();

        // Stats jalons globales pour cet enseignant
        $subjectFilter = function($q) use ($user) {
            $q->where('teacher_id', $user->id);
        };

        $milestoneStats = [
            'total'     => Milestone::whereHas('subject', $subjectFilter)->count(),
            'pending'   => Milestone::whereHas('subject', $subjectFilter)->where('status', 'pending')->count(),
            'submitted' => $pendingMilestones->count(),
            'validated' => Milestone::whereHas('subject', $subjectFilter)->where('status', 'validated')->count(),
            'rejected'  => Milestone::whereHas('subject', $subjectFilter)->where('status', 'rejected')->count(),
        ];

        return view('teacher.dashboard', compact('supervisedSubjects', 'pendingMilestones', 'milestoneStats'));
    }

    /**
     * Dashboard Étudiant.
     */
    private function studentDashboard(User $user)
    {
        $subject = Subject::where('student_id', $user->id)
            ->with(['teacher', 'thesisFiles.aiReport', 'milestones', 'defenseSchedule'])
            ->first();

        // Calcul de la progression des jalons
        $milestoneProgress = null;
        if ($subject && $subject->milestones->count() > 0) {
            $total = $subject->milestones->count();
            $validated = $subject->milestones->where('status', 'validated')->count();
            $submitted = $subject->milestones->where('status', 'submitted')->count();
            $rejected = $subject->milestones->where('status', 'rejected')->count();
            $pending = $subject->milestones->where('status', 'pending')->count();
            $percent = $total > 0 ? round(($validated / $total) * 100) : 0;
            $milestoneProgress = compact('total', 'validated', 'submitted', 'rejected', 'pending', 'percent');
        }

        return view('student.dashboard', compact('subject', 'milestoneProgress'));
    }

    /**
     * Dashboard Doyen.
     */
    private function doyenDashboard(User $user)
    {
        // On récupère la faculté du Doyen
        $facultyId = $user->faculty_id;

        if (!$facultyId) {
            abort(403, 'Vous n\'êtes rattaché à aucune faculté.');
        }

        $departments = Department::where('faculty_id', $facultyId)->pluck('id');

        $stats = [
            'total_subjects' => Subject::whereIn('department_id', $departments)->count(),
            'pending_subjects' => Subject::whereIn('department_id', $departments)->where('status', 'pending')->count(),
            'validated_subjects' => Subject::whereIn('department_id', $departments)->where('status', 'validated')->count(),
            'total_students' => User::role('Etudiant')->whereIn('department_id', $departments)->count(),
            'total_teachers' => User::role('Enseignant')->whereIn('department_id', $departments)->count(),
        ];

        // Sujets en retard (jalons dont la due_date est dépassée et status != 'validated')
        $delayedSubjects = Subject::whereIn('department_id', $departments)
            ->whereHas('milestones', function ($query) {
                $query->where('due_date', '<', now())
                      ->where('status', '!=', 'validated');
            })
            ->with(['student', 'department', 'teacher'])
            ->get();

        // Charge par enseignant (top 10)
        $teachersWorkload = User::role('Enseignant')
            ->whereIn('department_id', $departments)
            ->withCount('supervisedSubjects')
            ->orderByDesc('supervised_subjects_count')
            ->take(10)
            ->get();

        // Stats par département pour le graphique
        $departmentsData = Department::where('faculty_id', $facultyId)
            ->withCount([
                'subjects as validated_count' => fn($q) => $q->where('status', 'validated'),
                'subjects as pending_count' => fn($q) => $q->where('status', 'pending'),
                'subjects as rejected_count' => fn($q) => $q->where('status', 'rejected'),
            ])
            ->get();

        return view('doyen.dashboard', compact('stats', 'delayedSubjects', 'teachersWorkload', 'departmentsData'));
    }

    /**
     * Dashboard Appariteur.
     */
    private function appariteurDashboard(User $user)
    {
        // L'appariteur voit les sujets des étudiants (qui ont déposé un sujet).
        // On filtre par la faculté si l'appariteur a une faculty_id, sinon tout (cas admin-like)
        $query = Subject::with(['student', 'department']);

        if ($user->faculty_id) {
            $query->whereHas('department', function($q) use ($user) {
                $q->where('faculty_id', $user->faculty_id);
            });
        }

        // On peut séparer en listes : en attente de validation financière, et déjà validés.
        $pendingFinancial = (clone $query)->where('financial_status', 'pending')->latest()->get();
        $validatedFinancial = (clone $query)->where('financial_status', 'validated')->latest()->take(50)->get();
        $rejectedFinancial = (clone $query)->where('financial_status', 'rejected')->latest()->take(50)->get();

        return view('appariteur.dashboard', compact('pendingFinancial', 'validatedFinancial', 'rejectedFinancial'));
    }
}

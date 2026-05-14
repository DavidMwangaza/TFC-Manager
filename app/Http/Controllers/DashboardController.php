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
        } elseif ($user->hasRole('Chef de département')) {
            return $this->cpDashboard($user);
        } elseif ($user->hasRole('Enseignant')) {
            return $this->teacherDashboard($user);
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
            ->with(['student', 'teacher', 'thesisFiles'])
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
            ->whereHas('subject', fn($q) => $q->where('teacher_id', $user->id))
            ->with(['subject.student', 'thesisFile'])
            ->orderBy('submission_date', 'asc')
            ->get();

        // Stats jalons globales pour cet enseignant
        $milestoneStats = [
            'total'     => Milestone::whereHas('subject', fn($q) => $q->where('teacher_id', $user->id))->count(),
            'pending'   => Milestone::whereHas('subject', fn($q) => $q->where('teacher_id', $user->id))->where('status', 'pending')->count(),
            'submitted' => $pendingMilestones->count(),
            'validated' => Milestone::whereHas('subject', fn($q) => $q->where('teacher_id', $user->id))->where('status', 'validated')->count(),
            'rejected'  => Milestone::whereHas('subject', fn($q) => $q->where('teacher_id', $user->id))->where('status', 'rejected')->count(),
        ];

        return view('teacher.dashboard', compact('supervisedSubjects', 'pendingMilestones', 'milestoneStats'));
    }

    /**
     * Dashboard Étudiant.
     */
    private function studentDashboard(User $user)
    {
        $subject = Subject::where('student_id', $user->id)
            ->with(['teacher', 'thesisFiles.aiReport', 'milestones'])
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
}

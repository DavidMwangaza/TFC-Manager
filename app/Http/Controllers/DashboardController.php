<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ActivityLog;
use App\Models\Department;
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
        } elseif ($user->hasRole('Chef Departement')) {
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
     * Dashboard Chef de Filière.
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

        return view('teacher.dashboard', compact('supervisedSubjects'));
    }

    /**
     * Dashboard Étudiant.
     */
    private function studentDashboard(User $user)
    {
        $subject = Subject::where('student_id', $user->id)
            ->with(['teacher', 'thesisFiles.aiReport'])
            ->first();

        return view('student.dashboard', compact('subject'));
    }
}

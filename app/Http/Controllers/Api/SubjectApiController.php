<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubjectApiController extends Controller
{
    /**
     * Get the list of subjects based on user role.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Simulate random DB delay for the skeleton to be visible
        if (app()->environment('local')) {
            usleep(rand(500000, 1500000));
        }

        // Simulate DB error for testing (e.g., if ?force_error=1)
        if ($request->has('force_error')) {
            return response()->json(['message' => 'Database connection timeout.'], 500);
        }

        if ($user->hasRole('Etudiant')) {
            $baseQuery = Subject::where('student_id', $user->id);
        } elseif ($user->hasRole('Chef de département')) {
            $baseQuery = Subject::where('department_id', $user->department_id);
        } elseif ($user->hasRole('Enseignant')) {
            $baseQuery = Subject::where('teacher_id', $user->id);
        } else {
            $baseQuery = Subject::query();
        }

        if ($request->filled('status')) {
            $baseQuery->where('status', $request->status);
        }

        $subjects = $baseQuery->with(['student', 'teacher', 'department'])
            ->latest()
            ->paginate($request->get('per_page', 10));

        return response()->json($subjects);
    }
}

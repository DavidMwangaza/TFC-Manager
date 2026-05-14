<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChapterApiController extends Controller
{
    public function index(Subject $subject)
    {
        $user = Auth::user();
        if (! $user->can('view', $subject)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $chapters = $subject->chapters()->with('versions.author')->get();
        return response()->json($chapters);
    }

    public function store(Request $request, Subject $subject)
    {
        $user = Auth::user();
        if (! ($user->hasRole('Etudiant') && $subject->student_id === $user->id)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'position' => 'nullable|integer',
        ]);

        $chapter = Chapter::create([
            'subject_id' => $subject->id,
            'title' => $request->title,
            'position' => $request->integer('position') ?? 0,
        ]);

        return response()->json($chapter, 201);
    }
}

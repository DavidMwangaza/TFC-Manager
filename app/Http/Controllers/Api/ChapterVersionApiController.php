<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\ChapterVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChapterVersionApiController extends Controller
{
    public function index(Chapter $chapter)
    {
        $user = Auth::user();
        if (! $user->can('view', $chapter)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $versions = $chapter->versions()->with('author')->orderBy('created_at', 'desc')->get();
        return response()->json($versions);
    }

    public function store(Request $request, Chapter $chapter)
    {
        $user = Auth::user();

        $subject = $chapter->subject;
        $isOwnerStudent = $user->hasRole('Etudiant') && $subject->student_id === $user->id;
        $isAssignedTeacher = $user->hasRole('Enseignant') && $subject->teacher_id === $user->id;

        if (! ($isOwnerStudent || $isAssignedTeacher || $user->hasRole('Admin'))) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'content' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
        ]);

        $path = null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('chapter_versions', 'public');
        }

        $checksum = null;
        if ($path) {
            try {
                $checksum = md5_file(storage_path('app/public/' . $path));
            } catch (\Throwable $e) {
                $checksum = null;
            }
        }

        $version = ChapterVersion::create([
            'chapter_id' => $chapter->id,
            'content' => $request->input('content'),
            'file_ref' => $path,
            'created_by' => $user->id,
            'checksum' => $checksum,
        ]);

        return response()->json($version, 201);
    }
}

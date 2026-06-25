<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventArchivedSubjectModification
{
    /**
     * Handle an incoming request.
     * Empêche toute modification (POST, PUT, PATCH, DELETE) si le sujet est archivé.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ne s'applique qu'aux requêtes de modification
        if ($request->isMethod('GET') || $request->isMethod('HEAD') || $request->isMethod('OPTIONS')) {
            return $next($request);
        }

        // Récupérer le sujet de la route s'il existe
        $subject = $request->route('subject');

        // Si la route a injecté un Milestone, on peut remonter au sujet
        if (!$subject && $request->route('milestone')) {
            $subject = $request->route('milestone')->subject ?? null;
        }

        // Vérifier si c'est un upload général de thèse (l'étudiant dépose un fichier)
        if (!$subject && $request->routeIs('thesis.upload')) {
            $user = $request->user();
            if ($user) {
                // On prend le sujet validé de l'étudiant
                $subject = \App\Models\Subject::where('student_id', $user->id)
                    ->whereIn('status', ['validated', 'soutenu', 'archived'])
                    ->first();
            }
        }

        // Si on a un sujet et qu'il est archivé, on bloque l'action
        if ($subject && $subject->status === 'archived') {
            abort(403, 'Action impossible : Ce sujet est archivé et verrouillé en lecture seule.');
        }

        return $next($request);
    }
}

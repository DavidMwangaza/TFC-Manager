<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppariteurController extends Controller
{
    /**
     * Valide la situation financière de l'étudiant.
     */
    public function validateFinancial(Request $request, Subject $subject)
    {
        $user = Auth::user();

        if (!$user->hasRole('Appariteur')) {
            abort(403, 'Accès non autorisé.');
        }

        $request->validate([
            'financial_notes' => 'nullable|string|max:1000',
        ]);

        $subject->update([
            'financial_status' => 'validated',
            'financial_validated_by' => $user->id,
            'financial_validated_at' => now(),
            'financial_notes' => $request->financial_notes,
        ]);

        ActivityLog::log('financial_validated', 'Validation financière accordée', $subject);

        return back()->with('success', 'Situation financière de l\'étudiant validée avec succès.');
    }

    /**
     * Rejette la situation financière de l'étudiant.
     */
    public function rejectFinancial(Request $request, Subject $subject)
    {
        $user = Auth::user();

        if (!$user->hasRole('Appariteur')) {
            abort(403, 'Accès non autorisé.');
        }

        $request->validate([
            'financial_notes' => 'required|string|max:1000',
        ], [
            'financial_notes.required' => 'Le motif du rejet est obligatoire.',
        ]);

        $subject->update([
            'financial_status' => 'rejected',
            'financial_validated_by' => $user->id,
            'financial_validated_at' => now(),
            'financial_notes' => $request->financial_notes,
        ]);

        ActivityLog::log('financial_rejected', 'Validation financière refusée', $subject);

        return back()->with('success', 'Situation financière de l\'étudiant rejetée.');
    }
}

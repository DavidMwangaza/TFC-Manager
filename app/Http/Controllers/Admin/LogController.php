<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Affiche le journal d'activité.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        // Filtre par action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filtre par utilisateur
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtre par date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(30)->withQueryString();

        // Actions distinctes pour le filtre
        $actions = ActivityLog::distinct()
            ->pluck('action')
            ->sort()
            ->values();

        return view('admin.logs.index', compact('logs', 'actions'));
    }
}

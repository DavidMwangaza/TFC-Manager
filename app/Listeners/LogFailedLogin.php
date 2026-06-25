<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Failed;

/**
 * Enregistre dans l'audit log chaque tentative de connexion échouée.
 * Action auditée : AUTH_FAILED (section 4.1 du cahier des charges)
 */
class LogFailedLogin
{
    public function handle(Failed $event): void
    {
        $identifier = $event->credentials['email']
            ?? $event->credentials['username']
            ?? 'inconnu';

        // On crée l'entrée sans user_id (utilisateur non authentifié)
        ActivityLog::create([
            'user_id'    => $event->user?->id ?? null,
            'action'     => 'AUTH_FAILED',
            'model_type' => null,
            'model_id'   => null,
            'description' => 'Tentative de connexion échouée pour l\'identifiant : ' . $identifier,
            'ip_address'  => request()?->ip() ?? null,
        ]);
    }
}

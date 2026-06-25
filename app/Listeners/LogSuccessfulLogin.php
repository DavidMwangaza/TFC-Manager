<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Login;

/**
 * Enregistre dans l'audit log chaque connexion réussie.
 * Action auditée : LOGIN (section 4.1 du cahier des charges)
 */
class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        ActivityLog::log(
            'LOGIN',
            'Connexion réussie de l\'utilisateur ' . $event->user->name . ' (' . $event->user->email . ')',
            null,
            null,
            null
        );
    }
}

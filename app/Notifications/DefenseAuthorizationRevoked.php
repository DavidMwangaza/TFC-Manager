<?php

namespace App\Notifications;

use App\Models\Subject;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DefenseAuthorizationRevoked extends Notification
{
    use Queueable;

    public function __construct(
        protected Subject $subject
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Autorisation de soutenance retiree - ' . $this->subject->title)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Votre encadreur **' . $this->subject->teacher->name . '** a retire le Feu Vert pour votre sujet.')
            ->line('Sujet: "' . $this->subject->title . '"')
            ->line('Motif du retrait: ' . ($this->subject->defense_revocation_reason ?? 'Non precise'))
            ->action('Acceder a mon tableau de bord', url('/dashboard'))
            ->line('Le depot final est temporairement bloque jusqu a une nouvelle autorisation.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'defense_authorization_revoked',
            'icon' => '',
            'title' => 'Feu Vert retire',
            'message' => "Votre encadreur {$this->subject->teacher->name} a retire l'autorisation de soutenance pour votre sujet \"{$this->subject->title}\".",
            'reason' => $this->subject->defense_revocation_reason,
            'subject_id' => $this->subject->id,
        ];
    }
}

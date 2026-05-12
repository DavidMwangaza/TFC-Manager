<?php

namespace App\Notifications;

use App\Models\Milestone;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MilestoneValidated extends Notification
{
    use Queueable;

    public function __construct(protected Milestone $milestone) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Jalon validé — ' . $this->milestone->title)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Votre dépôt pour le jalon « ' . $this->milestone->title . ' » a été validé par votre encadreur.')
            ->action('Voir le sujet', url('/subjects/' . $this->milestone->subject->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'milestone_validated',
            'title' => 'Jalon validé',
            'message' => "Votre dépôt pour '{$this->milestone->title}' a été validé.",
            'subject_id' => $this->milestone->subject_id,
            'milestone_id' => $this->milestone->id,
        ];
    }
}

<?php

namespace App\Notifications;

use App\Models\Milestone;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MilestoneProfessorOverdue extends Notification
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
            ->subject('Correction en retard — ' . $this->milestone->title)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('L\'enseignant encadreur n\'a pas encore corrigé le jalon « ' . $this->milestone->title . ' » soumis le ' . ($this->milestone->submission_date?->format('d/m/Y H:i') ?? '-'))
            ->action('Voir le sujet', url('/subjects/' . $this->milestone->subject->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'milestone_professor_overdue',
            'title' => 'Correction en retard',
            'message' => "L'enseignant n'a pas corrigé '{$this->milestone->title}' à temps.",
            'subject_id' => $this->milestone->subject_id,
            'milestone_id' => $this->milestone->id,
        ];
    }
}

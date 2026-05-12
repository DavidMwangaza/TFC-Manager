<?php

namespace App\Notifications;

use App\Models\Milestone;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MilestoneSubmitted extends Notification
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
            ->subject('Jalon soumis — ' . $this->milestone->title)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('L\'étudiant(e) ' . $this->milestone->subject->student->name . ' a soumis le jalon :')
            ->line('« **' . $this->milestone->title . '** » pour le sujet « ' . $this->milestone->subject->title . ' »')
            ->action('Voir le sujet', url('/subjects/' . $this->milestone->subject->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'milestone_submitted',
            'title' => 'Jalon soumis',
            'message' => "{$this->milestone->subject->student->name} a soumis le jalon '{$this->milestone->title}'.",
            'subject_id' => $this->milestone->subject_id,
            'milestone_id' => $this->milestone->id,
        ];
    }
}

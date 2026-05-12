<?php

namespace App\Notifications;

use App\Models\Milestone;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MilestoneStudentOverdue extends Notification
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
            ->subject('Dépôt en retard — ' . $this->milestone->title)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('L\'étudiant ' . $this->milestone->subject->student->name . ' n\'a pas déposé le jalon « ' . $this->milestone->title . ' » avant la date limite.')
            ->action('Voir le sujet', url('/subjects/' . $this->milestone->subject->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'milestone_student_overdue',
            'title' => 'Dépôt en retard',
            'message' => "L'étudiant {$this->milestone->subject->student->name} n'a pas déposé '{$this->milestone->title}' à temps.",
            'subject_id' => $this->milestone->subject_id,
            'milestone_id' => $this->milestone->id,
        ];
    }
}

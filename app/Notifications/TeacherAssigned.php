<?php

namespace App\Notifications;

use App\Models\Subject;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeacherAssigned extends Notification
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
            ->subject('Nouvelle supervision — ' . $this->subject->title)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Vous avez été désigné(e) comme encadreur du sujet :')
            ->line('« **' . $this->subject->title . '** »')
            ->line('Étudiant(e) : **' . $this->subject->student->name . '**')
            ->action('Voir le sujet', url('/subjects/' . $this->subject->id))
            ->line('Merci pour votre encadrement.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'teacher_assigned',
            'icon' => '',
            'title' => 'Nouvelle supervision assignée',
            'message' => "Vous avez été désigné(e) comme encadreur du sujet « {$this->subject->title} » de l'étudiant(e) {$this->subject->student->name}.",
            'subject_id' => $this->subject->id,
        ];
    }
}

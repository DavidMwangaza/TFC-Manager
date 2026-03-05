<?php

namespace App\Notifications;

use App\Models\Subject;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSubjectSubmitted extends Notification
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
            ->subject('Nouveau sujet soumis — ' . $this->subject->title)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('L\'étudiant(e) **' . $this->subject->student->name . '** a soumis un nouveau sujet :')
            ->line('« **' . $this->subject->title . '** »')
            ->action('Voir les sujets en attente', url('/subjects'))
            ->line('Ce sujet est en attente de votre validation.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_subject',
            'icon' => '',
            'title' => 'Nouveau sujet soumis',
            'message' => "L'étudiant {$this->subject->student->name} a soumis le sujet « {$this->subject->title} ». En attente de votre validation.",
            'subject_id' => $this->subject->id,
        ];
    }
}

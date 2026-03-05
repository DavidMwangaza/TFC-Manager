<?php

namespace App\Notifications;

use App\Models\Subject;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DefenseAuthorized extends Notification
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
            ->subject('Soutenance autorisée — ' . $this->subject->title)
            ->greeting('Félicitations ' . $notifiable->name . ' !')
            ->line('Votre encadreur **' . $this->subject->teacher->name . '** a autorisé la soutenance pour votre sujet :')
            ->line('« **' . $this->subject->title . '** »')
            ->action('Accéder à mon tableau de bord', url('/dashboard'))
            ->line('Vous pouvez maintenant déposer la version finale de votre TFC.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'defense_authorized',
            'icon' => '',
            'title' => 'Soutenance autorisée — Feu Vert !',
            'message' => "Votre encadreur {$this->subject->teacher->name} a autorisé la soutenance pour votre sujet « {$this->subject->title} ». Vous pouvez maintenant déposer la version finale de votre TFC.",
            'subject_id' => $this->subject->id,
        ];
    }
}

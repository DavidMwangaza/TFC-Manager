<?php

namespace App\Notifications;

use App\Models\Subject;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubjectValidated extends Notification
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
            ->subject('Sujet validé — ' . $this->subject->title)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Votre sujet « **' . $this->subject->title . '** » a été validé par le Chef de Filière.')
            ->line('Encadreur assigné : **' . ($this->subject->teacher->name ?? 'En cours') . '**')
            ->action('Voir mon tableau de bord', url('/dashboard'))
            ->line('Bonne continuation dans votre travail !');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'subject_validated',
            'icon' => '',
            'title' => 'Sujet validé',
            'message' => "Votre sujet « {$this->subject->title} » a été validé. Encadreur assigné : {$this->subject->teacher->name}.",
            'subject_id' => $this->subject->id,
        ];
    }
}

<?php

namespace App\Notifications;

use App\Models\Subject;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubjectRejected extends Notification
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
            ->subject('Sujet rejeté — ' . $this->subject->title)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Votre sujet « **' . $this->subject->title . '** » a été rejeté.')
            ->line('**Motif :** ' . $this->subject->rejection_reason)
            ->action('Soumettre un nouveau sujet', url('/subjects/create'))
            ->line('Vous pouvez proposer un nouveau sujet en tenant compte des remarques.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'subject_rejected',
            'icon' => '',
            'title' => 'Sujet rejeté',
            'message' => "Votre sujet « {$this->subject->title} » a été rejeté. Motif : {$this->subject->rejection_reason}.",
            'subject_id' => $this->subject->id,
        ];
    }
}

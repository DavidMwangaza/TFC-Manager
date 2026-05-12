<?php

namespace App\Notifications;

use App\Models\Milestone;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MilestoneRejected extends Notification
{
    use Queueable;

    public function __construct(protected Milestone $milestone) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $msg = 'Votre dépôt pour le jalon « ' . $this->milestone->title . ' » nécessite des corrections.';
        if (!empty($this->milestone->comments)) {
            $msg .= '\nCommentaires : ' . $this->milestone->comments;
        }

        return (new MailMessage)
            ->subject('Jalon à reprendre — ' . $this->milestone->title)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line($msg)
            ->action('Voir le sujet', url('/subjects/' . $this->milestone->subject->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'milestone_rejected',
            'title' => 'Jalon à reprendre',
            'message' => "Votre dépôt pour '{$this->milestone->title}' nécessite des corrections.",
            'subject_id' => $this->milestone->subject_id,
            'milestone_id' => $this->milestone->id,
            'comments' => $this->milestone->comments,
        ];
    }
}

<?php

namespace App\Notifications;

use App\Models\Milestone;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MilestoneAssigned extends Notification
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
            ->subject('Nouveau jalon assigné — ' . $this->milestone->title)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Un nouveau jalon a été créé pour votre sujet :')
            ->line('« **' . $this->milestone->title . '** »')
            ->line('Échéance : ' . $this->milestone->due_date->format('d/m/Y H:i'))
            ->action('Voir le sujet', url('/subjects/' . $this->milestone->subject->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'milestone_assigned',
            'title' => 'Nouveau jalon',
            'message' => "Un jalon '{$this->milestone->title}' a été assigné pour votre sujet.",
            'subject_id' => $this->milestone->subject_id,
            'milestone_id' => $this->milestone->id,
        ];
    }
}

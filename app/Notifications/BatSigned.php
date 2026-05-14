<?php

namespace App\Notifications;

use App\Models\Subject;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class BatSigned extends Notification
{
    use Queueable;

    protected Subject $subject;

    public function __construct(Subject $subject)
    {
        $this->subject = $subject;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'subject_id' => $this->subject->id,
            'message' => 'Le Directeur a signé le Bon à Tirer (BAT) pour votre travail.',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}

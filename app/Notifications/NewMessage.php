<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NewMessage extends Notification
{
    use Queueable;

    public function __construct(public Message $message) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $subject = $this->message->subject;
        $sender  = $this->message->sender;

        return [
            'title'      => '💬 Nouveau message de ' . $sender->name,
            'body'       => \Illuminate\Support\Str::limit($this->message->body, 100),
            'url'        => route('subjects.show', $subject) . '#messagerie',
            'subject_id' => $subject->id,
            'sender_id'  => $sender->id,
        ];
    }
}

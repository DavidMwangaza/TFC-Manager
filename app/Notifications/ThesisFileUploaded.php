<?php

namespace App\Notifications;

use App\Models\ThesisFile;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ThesisFileUploaded extends Notification
{
    use Queueable;

    public function __construct(
        protected ThesisFile $thesisFile
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->thesisFile->subject;
        $student = $subject->student;
        $versionLabel = $this->thesisFile->version_type === 'jury' ? 'Version Jury' : 'Version Finale';

        return (new MailMessage)
            ->subject('Nouveau fichier TFC — ' . $versionLabel)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('L\'étudiant(e) **' . $student->name . '** a déposé la **' . $versionLabel . '** de son TFC :')
            ->line('« **' . $subject->title . '** »')
            ->action('Voir le sujet', url('/subjects/' . $subject->id))
            ->line('Merci de vérifier le document déposé.');
    }

    public function toArray(object $notifiable): array
    {
        $subject = $this->thesisFile->subject;
        $student = $subject->student;
        $versionLabel = $this->thesisFile->version_type === 'jury' ? 'Version Jury' : 'Version Finale';

        return [
            'type' => 'thesis_uploaded',
            'icon' => '',
            'title' => 'Nouveau fichier TFC déposé',
            'message' => "{$student->name} a déposé la {$versionLabel} de son TFC « {$subject->title} ».",
            'subject_id' => $subject->id,
            'thesis_file_id' => $this->thesisFile->id,
        ];
    }
}

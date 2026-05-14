<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Milestone;
use App\Models\User;
use App\Notifications\MilestoneProfessorOverdue;
use App\Notifications\MilestoneStudentOverdue;
use Illuminate\Console\Command;

class CheckMilestonesDeadlines extends Command
{
    protected $signature = 'milestones:check-deadlines';

    protected $description = 'Vérifie les deadlines des jalons et notifie les retards (étudiant/professeur).';

    public function handle(): int
    {
        $now = now();

        // Étudiants en retard — état = pending mais due_date dépassée
        $studentOverdues = Milestone::where('status', 'pending')
            ->where('due_date', '<', $now)
            ->with('subject.student')
            ->get();

        foreach ($studentOverdues as $m) {
            // éviter doublons en vérifiant le journal d'activité
            $exists = ActivityLog::where('action', 'milestone_student_late')
                ->where('model_type', get_class($m))
                ->where('model_id', $m->id)
                ->exists();
            if ($exists) continue;

            ActivityLog::log('milestone_student_late', "Dépôt en retard pour le jalon '{$m->title}'", $m);

            // Notifier les Chefs de département de la filière
            $chefs = User::role('Chef de département')
                ->where('department_id', $m->subject->department_id)
                ->get();
            foreach ($chefs as $chef) {
                $chef->notify(new MilestoneStudentOverdue($m));
            }
        }

        // Enseignants en retard pour corriger — état = submitted mais correction_deadline dépassée
        $profOverdues = Milestone::where('status', 'submitted')
            ->whereNotNull('correction_deadline')
            ->where('correction_deadline', '<', $now)
            ->with('subject.teacher')
            ->get();

        foreach ($profOverdues as $m) {
            $exists = ActivityLog::where('action', 'milestone_professor_late')
                ->where('model_type', get_class($m))
                ->where('model_id', $m->id)
                ->exists();
            if ($exists) continue;

            ActivityLog::log('milestone_professor_late', "Correction en retard pour le jalon '{$m->title}'", $m);

            $chefs = User::role('Chef de département')
                ->where('department_id', $m->subject->department_id)
                ->get();
            foreach ($chefs as $chef) {
                $chef->notify(new MilestoneProfessorOverdue($m));
            }
        }

        $this->info('Vérification des jalons terminée.');

        return self::SUCCESS;
    }
}

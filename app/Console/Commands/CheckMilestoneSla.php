<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Milestone;
use App\Models\User;
use App\Notifications\MilestoneProfessorOverdue;
use App\Notifications\MilestoneStudentOverdue;

class CheckMilestoneSla extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:milestone-sla';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifie les SLA (48h pour le prof) et les retards étudiants, puis envoie des relances automatiques';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Vérification des SLA de correction et des délais de dépôt...');

        // 1. Délais de correction des enseignants (SLA 48h)
        // Les jalons sont 'submitted' depuis plus de 48 heures sans être validés ni rejetés.
        $overdueProfessorMilestones = Milestone::where('status', 'submitted')
            ->where('submission_date', '<', now()->subHours(48))
            ->with(['subject.teacher', 'subject.student', 'subject.department'])
            ->get();

        foreach ($overdueProfessorMilestones as $milestone) {
            $teacher = $milestone->subject->teacher;
            if ($teacher) {
                // Relance au professeur
                $teacher->notify(new MilestoneProfessorOverdue($milestone));
                $this->info('Relance envoyée au professeur ' . $teacher->name . ' pour le jalon ' . $milestone->title);

                // Optionnel : Notifier le CP de la violation du SLA
                $cpUsers = User::role('Chef de département')
                    ->where('department_id', $milestone->subject->department_id)
                    ->get();
                
                foreach ($cpUsers as $cp) {
                    $cp->notify(new MilestoneProfessorOverdue($milestone)); // Ou créer une notif spécifique CP
                }
            }
        }

        // 2. Retards étudiants (dépassement de la due_date)
        $overdueStudentMilestones = Milestone::where('status', 'pending')
            ->where('due_date', '<', now())
            ->with('subject.student')
            ->get();

        foreach ($overdueStudentMilestones as $milestone) {
            $student = $milestone->subject->student;
            if ($student) {
                $student->notify(new MilestoneStudentOverdue($milestone));
                $this->info('Relance envoyée à l\'étudiant ' . $student->name . ' pour le jalon ' . $milestone->title);
            }
        }

        $this->info('Vérification des SLA terminée.');
    }
}

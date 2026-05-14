<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Notification;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class NotificationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_utilisateur_peut_marquer_seulement_ses_notifications_comme_lues(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $ownerNotificationId = $this->createDatabaseNotification($owner, 'Notif A');
        $otherNotificationId = $this->createDatabaseNotification($other, 'Notif B');

        $this->actingAs($owner)
            ->post(route('notifications.markAsRead', $otherNotificationId))
            ->assertNotFound();

        $this->assertNull($other->notifications()->findOrFail($otherNotificationId)->read_at);

        $this->actingAs($owner)
            ->post(route('notifications.markAsRead', $ownerNotificationId))
            ->assertRedirect();

        $this->assertNotNull($owner->notifications()->findOrFail($ownerNotificationId)->read_at);
    }

    public function test_marquer_tout_lu_ne_marque_que_notifications_utilisateur_courant(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $ownerFirst = $this->createDatabaseNotification($owner, 'Notif A1');
        $ownerSecond = $this->createDatabaseNotification($owner, 'Notif A2');
        $otherNotification = $this->createDatabaseNotification($other, 'Notif B1');

        $this->actingAs($owner)
            ->post(route('notifications.markAllRead'))
            ->assertRedirect();

        $this->assertNotNull($owner->notifications()->findOrFail($ownerFirst)->read_at);
        $this->assertNotNull($owner->notifications()->findOrFail($ownerSecond)->read_at);
        $this->assertNull($other->notifications()->findOrFail($otherNotification)->read_at);
    }

    public function test_utilisateur_peut_supprimer_seulement_ses_notifications(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $ownerNotificationId = $this->createDatabaseNotification($owner, 'Notif A');
        $otherNotificationId = $this->createDatabaseNotification($other, 'Notif B');

        $this->actingAs($owner)
            ->delete(route('notifications.destroy', $otherNotificationId))
            ->assertNotFound();

        $this->assertDatabaseHas('notifications', [
            'id' => $otherNotificationId,
            'notifiable_id' => $other->id,
            'notifiable_type' => User::class,
        ]);

        $this->actingAs($owner)
            ->delete(route('notifications.destroy', $ownerNotificationId))
            ->assertRedirect();

        $this->assertDatabaseMissing('notifications', [
            'id' => $ownerNotificationId,
            'notifiable_id' => $owner->id,
            'notifiable_type' => User::class,
        ]);
    }

    public function test_supprimer_toutes_notifications_supprime_que_celles_utilisateur_courant(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $ownerFirst = $this->createDatabaseNotification($owner, 'Notif A1');
        $ownerSecond = $this->createDatabaseNotification($owner, 'Notif A2');
        $otherNotification = $this->createDatabaseNotification($other, 'Notif B1');

        $this->actingAs($owner)
            ->delete(route('notifications.destroyAll'))
            ->assertRedirect();

        $this->assertDatabaseMissing('notifications', ['id' => $ownerFirst]);
        $this->assertDatabaseMissing('notifications', ['id' => $ownerSecond]);
        $this->assertDatabaseHas('notifications', ['id' => $otherNotification]);
    }

    public function test_cp_assigne_seulement_enseignant_meme_departement_et_notifications_ciblent_utilisateurs_corrects(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'Chef de département']);
        Role::firstOrCreate(['name' => 'Enseignant']);
        Role::firstOrCreate(['name' => 'Etudiant']);

        $departmentA = Department::create([
            'name' => 'Informatique',
            'code' => 'INFO',
            'description' => 'Département informatique',
        ]);

        $departmentB = Department::create([
            'name' => 'Mathématiques',
            'code' => 'MATH',
            'description' => 'Département mathématiques',
        ]);

        $cp = User::factory()->create(['department_id' => $departmentA->id]);
        $cp->assignRole('Chef de département');

        $student = User::factory()->create(['department_id' => $departmentA->id]);
        $student->assignRole('Etudiant');

        $nonTeacherSameDept = User::factory()->create(['department_id' => $departmentA->id]);
        $nonTeacherSameDept->assignRole('Etudiant');

        $teacherOtherDept = User::factory()->create(['department_id' => $departmentB->id]);
        $teacherOtherDept->assignRole('Enseignant');

        $teacherSameDept = User::factory()->create(['department_id' => $departmentA->id]);
        $teacherSameDept->assignRole('Enseignant');

        $subject = Subject::create([
            'title' => 'Système de gestion TFC',
            'description' => 'Sujet test',
            'status' => 'pending',
            'student_id' => $student->id,
            'department_id' => $departmentA->id,
        ]);

        $this->actingAs($cp)
            ->post(route('subjects.validate', $subject), ['teacher_id' => $nonTeacherSameDept->id])
            ->assertSessionHasErrors('teacher_id');

        $subject->refresh();
        $this->assertSame('pending', $subject->status);
        $this->assertNull($subject->teacher_id);

        $this->actingAs($cp)
            ->post(route('subjects.validate', $subject), ['teacher_id' => $teacherOtherDept->id])
            ->assertSessionHasErrors('teacher_id');

        $subject->refresh();
        $this->assertSame('pending', $subject->status);
        $this->assertNull($subject->teacher_id);

        $this->actingAs($cp)
            ->post(route('subjects.validate', $subject), ['teacher_id' => $teacherSameDept->id])
            ->assertSessionHasNoErrors();

        $subject->refresh();

        $this->assertSame('validated', $subject->status);
        $this->assertSame($teacherSameDept->id, $subject->teacher_id);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $student->id,
            'notifiable_type' => User::class,
            'type' => \App\Notifications\SubjectValidated::class,
        ]);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $teacherSameDept->id,
            'notifiable_type' => User::class,
            'type' => \App\Notifications\TeacherAssigned::class,
        ]);

        $this->assertDatabaseMissing('notifications', [
            'notifiable_id' => $teacherOtherDept->id,
            'notifiable_type' => User::class,
            'type' => \App\Notifications\TeacherAssigned::class,
        ]);
    }

    private function createDatabaseNotification(User $user, string $title): string
    {
        $user->notify(new class($title) extends Notification
        {
            public function __construct(private readonly string $title) {}

            public function via(object $notifiable): array
            {
                return ['database'];
            }

            public function toArray(object $notifiable): array
            {
                return [
                    'title' => $this->title,
                    'message' => 'Notification de test',
                ];
            }
        });

        return $user->notifications()->latest()->firstOrFail()->id;
    }
}

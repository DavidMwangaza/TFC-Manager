<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Subject;
use App\Models\ThesisFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class TeacherDefenseAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private const REVOCATION_REASON = 'Le rapport IA signale des sections a corriger avant toute soutenance.';

    public function test_enseignant_peut_revoquer_autorisation_soutenance_si_aucune_version_finale(): void
    {
        [$teacher, $student, $subject] = $this->createValidatedSubjectForTeacher(defenseValidated: true);

        ThesisFile::create([
            'subject_id' => $subject->id,
            'file_path' => 'thesis_files/jury.pdf',
            'original_name' => 'jury.pdf',
            'version_type' => 'jury',
        ]);

        $this->actingAs($teacher)
            ->delete(route('subjects.revoke-defense', $subject), [
                'defense_revocation_reason' => self::REVOCATION_REASON,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $subject->refresh();

        $this->assertFalse((bool) $subject->defense_validated);
        $this->assertNull($subject->defense_date);
        $this->assertNull($subject->defense_room);
        $this->assertSame(self::REVOCATION_REASON, $subject->defense_revocation_reason);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $student->id,
            'notifiable_type' => User::class,
            'type' => \App\Notifications\DefenseAuthorizationRevoked::class,
        ]);
    }

    public function test_enseignant_ne_peut_pas_revoquer_apres_depot_final(): void
    {
        [$teacher, , $subject] = $this->createValidatedSubjectForTeacher(defenseValidated: true);

        ThesisFile::create([
            'subject_id' => $subject->id,
            'file_path' => 'thesis_files/final.pdf',
            'original_name' => 'final.pdf',
            'version_type' => 'final',
        ]);

        $this->actingAs($teacher)
            ->delete(route('subjects.revoke-defense', $subject), [
                'defense_revocation_reason' => self::REVOCATION_REASON,
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $subject->refresh();

        $this->assertTrue((bool) $subject->defense_validated);
        $this->assertNotNull($subject->defense_date);
        $this->assertNotNull($subject->defense_room);
    }

    public function test_enseignant_ne_peut_pas_revoquer_pour_sujet_non_assigne(): void
    {
        [$teacher, , $subject] = $this->createValidatedSubjectForTeacher(defenseValidated: true);

        $otherTeacher = User::factory()->create(['department_id' => $teacher->department_id]);
        $otherTeacher->assignRole('Enseignant');

        $this->actingAs($otherTeacher)
            ->delete(route('subjects.revoke-defense', $subject), [
                'defense_revocation_reason' => self::REVOCATION_REASON,
            ])
            ->assertForbidden();

        $this->assertTrue((bool) $subject->fresh()->defense_validated);
    }

    public function test_enseignant_ne_peut_pas_revoquer_si_non_autorise(): void
    {
        [$teacher, , $subject] = $this->createValidatedSubjectForTeacher(defenseValidated: false);

        $this->actingAs($teacher)
            ->delete(route('subjects.revoke-defense', $subject), [
                'defense_revocation_reason' => self::REVOCATION_REASON,
            ])
            ->assertRedirect()
            ->assertSessionHas('info');

        $this->assertFalse((bool) $subject->fresh()->defense_validated);
    }

    public function test_enseignant_doit_fournir_motif_retrait(): void
    {
        [$teacher, , $subject] = $this->createValidatedSubjectForTeacher(defenseValidated: true);

        $this->actingAs($teacher)
            ->from('/dashboard')
            ->delete(route('subjects.revoke-defense', $subject), [
                'defense_revocation_reason' => '',
            ])
            ->assertRedirect('/dashboard')
            ->assertSessionHasErrors('defense_revocation_reason');

        $this->assertTrue((bool) $subject->fresh()->defense_validated);
    }

    /**
     * @return array{0: User, 1: User, 2: Subject}
     */
    private function createValidatedSubjectForTeacher(bool $defenseValidated): array
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'Enseignant']);
        Role::firstOrCreate(['name' => 'Etudiant']);

        $department = Department::create([
            'name' => 'Informatique',
            'code' => 'INFO',
            'description' => 'Departement informatique',
        ]);

        $teacher = User::factory()->create(['department_id' => $department->id]);
        $teacher->assignRole('Enseignant');

        $student = User::factory()->create(['department_id' => $department->id]);
        $student->assignRole('Etudiant');

        $subject = Subject::create([
            'title' => 'Sujet test soutenance',
            'description' => 'Description test',
            'status' => 'validated',
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'department_id' => $department->id,
            'defense_validated' => $defenseValidated,
            'defense_date' => $defenseValidated ? now()->addDays(7) : null,
            'defense_room' => $defenseValidated ? 'Salle A' : null,
        ]);

        return [$teacher, $student, $subject];
    }
}

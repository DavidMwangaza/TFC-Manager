<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class BatSigningTest extends TestCase
{
    use RefreshDatabase;

    public function test_director_peut_signer_bat(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'Enseignant']);
        Role::firstOrCreate(['name' => 'Etudiant']);

        $department = Department::create([
            'name' => 'Science',
            'code' => 'SCI',
            'description' => 'Dept',
        ]);

        $teacher = User::factory()->create(['department_id' => $department->id]);
        $teacher->assignRole('Enseignant');

        $student = User::factory()->create(['department_id' => $department->id]);
        $student->assignRole('Etudiant');

        $subject = Subject::create([
            'title' => 'Sujet BAT',
            'description' => 'Desc',
            'status' => 'validated',
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'department_id' => $department->id,
            'defense_validated' => true,
        ]);

        $this->actingAs($teacher)
            ->post(route('subjects.bat.sign', $subject))
            ->assertRedirect()
            ->assertSessionHas('success');

        $subject->refresh();

        $this->assertNotNull($subject->bat_signed_at);
        $this->assertNotNull($subject->bat_signature_hash);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $student->id,
            'notifiable_type' => User::class,
            'type' => \App\Notifications\BatSigned::class,
        ]);
    }

    public function test_autre_enseignant_ne_peut_pas_signer(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'Enseignant']);
        Role::firstOrCreate(['name' => 'Etudiant']);

        $department = Department::create([
            'name' => 'Science2',
            'code' => 'S2',
            'description' => 'Dept',
        ]);

        $teacher = User::factory()->create(['department_id' => $department->id]);
        $teacher->assignRole('Enseignant');

        $otherTeacher = User::factory()->create(['department_id' => $department->id]);
        $otherTeacher->assignRole('Enseignant');

        $student = User::factory()->create(['department_id' => $department->id]);
        $student->assignRole('Etudiant');

        $subject = Subject::create([
            'title' => 'Sujet BAT 2',
            'description' => 'Desc',
            'status' => 'validated',
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'department_id' => $department->id,
            'defense_validated' => true,
        ]);

        $this->actingAs($otherTeacher)
            ->post(route('subjects.bat.sign', $subject))
            ->assertForbidden();
    }
}

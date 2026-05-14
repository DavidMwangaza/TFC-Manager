<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Milestone;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MilestoneTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create minimal roles used by the tests
        Role::firstOrCreate(['name' => 'Etudiant']);
        Role::firstOrCreate(['name' => 'Enseignant']);
        Role::firstOrCreate(['name' => 'Chef de département']);
    }

    public function test_enseignant_peut_creer_un_jalon()
    {
        $department = Department::create(['faculty' => 'ESIS', 'name' => 'Génie Logiciel', 'code' => 'GL']);

        $student = User::factory()->create(['department_id' => $department->id]);
        $student->assignRole('Etudiant');

        $teacher = User::factory()->create(['department_id' => $department->id]);
        $teacher->assignRole('Enseignant');

        $subject = Subject::create([
            'title' => 'Sujet test',
            'description' => 'Desc',
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'department_id' => $department->id,
        ]);

        $response = $this->actingAs($teacher)->post(route('milestones.store', $subject), [
            'title' => 'Introduction',
            'due_date' => now()->addDays(7)->format('Y-m-d H:i:s'),
            'correction_deadline' => now()->addDays(10)->format('Y-m-d H:i:s'),
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('milestones', [
            'subject_id' => $subject->id,
            'title' => 'Introduction',
            'status' => 'pending',
        ]);
    }

    public function test_etudiant_peut_soumettre_un_jalon()
    {
        $department = Department::create(['faculty' => 'ESIS', 'name' => 'Génie Logiciel', 'code' => 'GL2']);

        $student = User::factory()->create(['department_id' => $department->id]);
        $student->assignRole('Etudiant');

        $teacher = User::factory()->create(['department_id' => $department->id]);
        $teacher->assignRole('Enseignant');

        $subject = Subject::create([
            'title' => 'Sujet test 2',
            'description' => 'Desc',
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'department_id' => $department->id,
        ]);

        $milestone = Milestone::create([
            'subject_id' => $subject->id,
            'title' => 'Chapitre 1',
            'due_date' => now()->addDays(3),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($student)->post(route('milestones.submit', $milestone));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('milestones', [
            'id' => $milestone->id,
            'status' => 'submitted',
        ]);
    }

    public function test_enseignant_peut_valider_un_jalon()
    {
        $department = Department::create(['faculty' => 'ESIS', 'name' => 'Génie Logiciel', 'code' => 'GL3']);

        $student = User::factory()->create(['department_id' => $department->id]);
        $student->assignRole('Etudiant');

        $teacher = User::factory()->create(['department_id' => $department->id]);
        $teacher->assignRole('Enseignant');

        $subject = Subject::create([
            'title' => 'Sujet test 3',
            'description' => 'Desc',
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'department_id' => $department->id,
        ]);

        $milestone = Milestone::create([
            'subject_id' => $subject->id,
            'title' => 'Chapitre 2',
            'due_date' => now()->addDays(3),
            'status' => 'submitted',
            'submission_date' => now(),
        ]);

        $response = $this->actingAs($teacher)->post(route('milestones.validate', $milestone), [
            'comments' => 'Bien reçu, validé.'
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('milestones', [
            'id' => $milestone->id,
            'status' => 'validated',
            'comments' => 'Bien reçu, validé.',
        ]);
    }
}

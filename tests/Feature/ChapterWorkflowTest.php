<?php

namespace Tests\Feature;

use App\Models\Chapter;
use App\Models\ChapterVersion;
use App\Models\Department;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ChapterWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_etudiant_peut_creer_chapitre_et_version(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'Etudiant']);
        Role::firstOrCreate(['name' => 'Enseignant']);

        $department = Department::create([
            'name' => 'Test Dept',
            'code' => 'TST',
            'description' => 'Dept test',
        ]);

        $teacher = User::factory()->create(['department_id' => $department->id]);
        $teacher->assignRole('Enseignant');

        $student = User::factory()->create(['department_id' => $department->id]);
        $student->assignRole('Etudiant');

        $subject = Subject::create([
            'title' => 'Sujet chapitre test',
            'description' => 'Desc',
            'status' => 'validated',
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'department_id' => $department->id,
        ]);

        // L'étudiant crée un chapitre
        $this->actingAs($student)
            ->post(route('chapters.store', $subject), [
                'title' => 'Chapitre 1',
                'position' => 1,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('chapters', [
            'subject_id' => $subject->id,
            'title' => 'Chapitre 1',
        ]);

        $chapter = Chapter::where('subject_id', $subject->id)->first();

        // L'étudiant ajoute une version textuelle
        $this->actingAs($student)
            ->post(route('chapter_versions.store', $chapter), [
                'content' => 'Version initiale du chapitre',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('chapter_versions', [
            'chapter_id' => $chapter->id,
            'content' => 'Version initiale du chapitre',
            'created_by' => $student->id,
        ]);
    }

    public function test_autre_etudiant_ne_peut_pas_creer_chapitre(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'Etudiant']);

        $department = Department::create([
            'name' => 'Test Dept 2',
            'code' => 'T2',
            'description' => 'Dept test',
        ]);

        $student = User::factory()->create(['department_id' => $department->id]);
        $student->assignRole('Etudiant');

        $otherStudent = User::factory()->create(['department_id' => $department->id]);
        $otherStudent->assignRole('Etudiant');

        $subject = Subject::create([
            'title' => 'Sujet autre',
            'description' => 'Desc',
            'status' => 'validated',
            'student_id' => $student->id,
            'department_id' => $department->id,
        ]);

        $this->actingAs($otherStudent)
            ->post(route('chapters.store', $subject), [
                'title' => 'Tentative Chapitre',
            ])
            ->assertStatus(403);
    }
}

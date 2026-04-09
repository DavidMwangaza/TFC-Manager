<?php

use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\DepartmentController as AdminDepartmentController;
use App\Http\Controllers\Admin\FacultyController as AdminFacultyController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ThesisFileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Archives des travaux défendus (accès public)
Route::get('/archives', [ArchiveController::class, 'index'])->name('archives.index');
Route::get('/archives/{thesisFile}/download', [ArchiveController::class, 'download'])->name('archives.download');

// Dashboard dynamique selon le rôle
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    // === SUJETS (Étudiant) ===
    Route::middleware('role:Etudiant')->group(function () {
        Route::get('/subjects/create', [SubjectController::class, 'create'])->name('subjects.create');
        Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
    });

    // === UPLOAD TFC (Étudiant) ===
    Route::middleware('role:Etudiant')->group(function () {
        Route::post('/thesis/upload', [ThesisFileController::class, 'upload'])->name('thesis.upload');
    });

    // === VALIDATION SUJETS (Chef de Filière) ===
    Route::middleware('role:Chef Departement')->group(function () {
        Route::post('/subjects/{subject}/validate', [SubjectController::class, 'validateSubject'])->name('subjects.validate');
        Route::post('/subjects/{subject}/reject', [SubjectController::class, 'rejectSubject'])->name('subjects.reject');
    });

    // === TÉLÉCHARGEMENT (Enseignant, CP, Admin) ===
    Route::get('/thesis/{thesisFile}/download', [ThesisFileController::class, 'download'])->name('thesis.download');

    // === AUTORISATION SOUTENANCE (Enseignant) ===
    Route::middleware('role:Enseignant')->group(function () {
        Route::post('/subjects/{subject}/authorize-defense', [SubjectController::class, 'authorizeDefense'])->name('subjects.authorize-defense');
    });

    // === LISTE DES SUJETS ===
    Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
    Route::get('/subjects/export', [SubjectController::class, 'export'])->name('subjects.export')->middleware('role:Admin|Chef Departement');
    Route::get('/subjects/{subject}', [SubjectController::class, 'show'])->name('subjects.show');

    // === NOTIFICATIONS ===
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');

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
        Route::get('/faculties', [AdminFacultyController::class, 'index'])->name('faculties.index');
        Route::get('/faculties/create', [AdminFacultyController::class, 'create'])->name('faculties.create');
        Route::post('/faculties', [AdminFacultyController::class, 'store'])->name('faculties.store');
        Route::get('/faculties/{faculty}/edit', [AdminFacultyController::class, 'edit'])->name('faculties.edit');
        Route::put('/faculties/{faculty}', [AdminFacultyController::class, 'update'])->name('faculties.update');
        Route::delete('/faculties/{faculty}', [AdminFacultyController::class, 'destroy'])->name('faculties.destroy');

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

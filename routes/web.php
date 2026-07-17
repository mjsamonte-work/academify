<?php

use App\Http\Controllers\Academic\AcademicSetupController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Enrollment\EnrollmentController;
use App\Http\Controllers\Records\GuardianController;
use App\Http\Controllers\Records\StudentController;
use App\Http\Controllers\Records\StudentGuardianController;
use App\Http\Controllers\Records\TeacherController;
use App\Http\Controllers\Teacher\ProfileController as TeacherProfileController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::resource('admin/users', UserController::class)
        ->except(['destroy'])
        ->middleware('permission:users.view')
        ->names('admin.users');

    Route::prefix('academic')
        ->name('academic.')
        ->middleware('permission:academic_setup.view')
        ->group(function () {
            foreach ([
                'school-years' => 'school-years',
                'terms' => 'terms',
                'grade-levels' => 'grade-levels',
                'sections' => 'sections',
                'subjects' => 'subjects',
                'classrooms' => 'classrooms',
            ] as $uri => $name) {
                Route::get($uri, [AcademicSetupController::class, 'index'])->name($name.'.index')->defaults('resource', $uri);
                Route::get($uri.'/create', [AcademicSetupController::class, 'create'])->name($name.'.create')->defaults('resource', $uri);
                Route::post($uri, [AcademicSetupController::class, 'store'])->name($name.'.store')->defaults('resource', $uri);
                Route::get($uri.'/{record}/edit', [AcademicSetupController::class, 'edit'])->name($name.'.edit')->defaults('resource', $uri);
                Route::put($uri.'/{record}', [AcademicSetupController::class, 'update'])->name($name.'.update')->defaults('resource', $uri);
            }
        });

    Route::prefix('records')->name('records.')->group(function () {
        Route::resource('students', StudentController::class)
            ->except(['destroy'])
            ->middleware('permission:students.view');

        Route::post('students/{student}/guardians', [StudentGuardianController::class, 'store'])
            ->middleware('permission:students.update')
            ->name('students.guardians.store');
        Route::put('students/{student}/guardians/{guardian}', [StudentGuardianController::class, 'update'])
            ->middleware('permission:students.update')
            ->name('students.guardians.update');
        Route::delete('students/{student}/guardians/{guardian}', [StudentGuardianController::class, 'destroy'])
            ->middleware('permission:students.update')
            ->name('students.guardians.destroy');

        Route::resource('guardians', GuardianController::class)
            ->except(['destroy'])
            ->middleware('permission:guardians.view');

        Route::resource('teachers', TeacherController::class)
            ->except(['destroy'])
            ->middleware('permission:teachers.view');
    });

    Route::get('teacher/profile', [TeacherProfileController::class, 'show'])
        ->middleware('permission:teachers.view_own')
        ->name('teacher.profile');

    Route::prefix('enrollment')->name('enrollment.')->group(function () {
        Route::resource('enrollments', EnrollmentController::class)
            ->except(['destroy'])
            ->middleware('permission:enrollments.view');
    });
});

require __DIR__.'/settings.php';

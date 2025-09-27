<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\EnrollmentController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Liste
Route::get('/courses', [CourseController::class,'index'])->name('courses.index');

// Admin & Instructor için kurs CRUD
Route::middleware(['auth','role:admin,instructor'])->group(function () {
    Route::get('/courses/create', [CourseController::class,'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class,'store'])->name('courses.store');
    Route::get('/courses/{slug}/edit', [CourseController::class,'edit'])->name('courses.edit');
    Route::patch('/courses/{slug}', [CourseController::class,'update'])->name('courses.update');
    Route::delete('/courses/{slug}', [CourseController::class,'destroy'])->name('courses.destroy');

    // Lesson CRUD (web route üzerinden çalışsın diye)
    Route::post('/lessons', [LessonController::class,'store'])->name('lessons.store');
    Route::patch('/lessons/{lesson}', [LessonController::class,'update'])->name('lessons.update');
    Route::delete('/lessons/{lesson}', [LessonController::class,'destroy'])->name('lessons.destroy');
});

// Enrollment (öğrenci kursa kaydol/iptal etsin)
Route::middleware(['auth'])->group(function () {
    Route::post('/enrollments', [EnrollmentController::class,'store'])->name('enrollments.store');
    Route::delete('/enrollments/{enrollment}', [EnrollmentController::class,'destroy'])->name('enrollments.destroy');
});

// Dinamik detay EN SONA (sıra önemli!)
Route::get('/courses/{slug}', [CourseController::class,'show'])->name('courses.show');

// Breeze default
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\LessonController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Liste
Route::get('/courses', [CourseController::class,'index'])->name('courses.index');

Route::middleware('auth')->group(function () {
    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Course create/store
    Route::get('/courses/create', [CourseController::class,'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class,'store'])->name('courses.store');

    // Section ekle (slug ile binding)
    Route::post('/courses/{course:slug}/sections', [SectionController::class,'store'])->name('sections.store');
    Route::post('/sections/{section}/lessons', [LessonController::class, 'store'])->name('lessons.store');
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

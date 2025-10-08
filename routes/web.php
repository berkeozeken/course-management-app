<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;        // <<< eklendi
use App\Models\User;                // <<< eklendi

Route::get('/_ping', fn() => response('pong', 200));

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn () => inertia('Dashboard'))->name('dashboard');

    // everyone
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');

    // student only
    Route::get('/my-courses', [CourseController::class, 'my'])
        ->middleware('role:student')
        ->name('courses.my');

    // instructor/admin only
    Route::get('/my-teachings', [CourseController::class, 'myTeachings'])
        ->middleware('role:admin,instructor')
        ->name('courses.myTeachings');

    // PROFILE
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'updateAccount'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // admin + instructor
    Route::middleware(['role:admin,instructor'])->group(function () {
        Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
        Route::post('/courses',        [CourseController::class, 'store'])->name('courses.store');
        Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->whereNumber('course')->name('courses.edit');
        Route::put('/courses/{course}', [CourseController::class, 'update'])->whereNumber('course')->name('courses.update');
        Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->whereNumber('course')->name('courses.destroy');
        Route::post('/courses/{course}/toggle-publish', [CourseController::class, 'togglePublish'])->whereNumber('course')->name('courses.togglePublish');

        Route::get('/courses/{course}/participants', [CourseController::class, 'participants'])
            ->whereNumber('course')->name('courses.participants');

        // sections
        Route::get('/courses/{course}/sections/create', [SectionController::class, 'create'])->whereNumber('course')->name('sections.create');
        Route::post('/courses/{course}/sections',       [SectionController::class, 'store'])->whereNumber('course')->name('sections.store');
        Route::get('/sections/{section}/edit',          [SectionController::class, 'edit'])->whereNumber('section')->name('sections.edit');
        Route::put('/sections/{section}',               [SectionController::class, 'update'])->whereNumber('section')->name('sections.update');
        Route::delete('/sections/{section}',            [SectionController::class, 'destroy'])->whereNumber('section')->name('sections.destroy');

        // lessons
        Route::get('/sections/{section}/lessons/create', [LessonController::class, 'create'])->whereNumber('section')->name('lessons.create');
        Route::post('/sections/{section}/lessons',       [LessonController::class, 'store'])->whereNumber('section')->name('lessons.store');
        Route::get('/lessons/{lesson}/edit',             [LessonController::class, 'edit'])->whereNumber('lesson')->name('lessons.edit');
        Route::put('/lessons/{lesson}',                  [LessonController::class, 'update'])->whereNumber('lesson')->name('lessons.update');
        Route::delete('/lessons/{lesson}',               [LessonController::class, 'destroy'])->whereNumber('lesson')->name('lessons.destroy');
    });

    // enroll
    Route::post('/courses/{course}/enroll',   [EnrollmentController::class, 'store'])->whereNumber('course')->name('courses.enroll');
    Route::delete('/courses/{course}/enroll', [EnrollmentController::class, 'destroy'])->whereNumber('course')->name('courses.unenroll');

    // lesson show
    Route::get('/lessons/{lesson}', [LessonController::class, 'show'])->whereNumber('lesson')->name('lessons.show');

    // course show
    Route::get('/courses/{course}', [CourseController::class, 'show'])->whereNumber('course')->name('courses.show');
});

/*
|--------------------------------------------------------------------------
| One-time admin grant route (temporary)
|--------------------------------------------------------------------------
| Token: mySecretAdminPass2025
| Mail : admin@gmail.com
| KULLANDIKTAN SONRA BU BLOĞU SİL!
*/
Route::get('/one-time-make-admin', function (Request $request) {
    abort_unless(app()->environment('production'), 403);

    $token = env('ADMIN_GRANT_TOKEN');
    abort_unless($token && hash_equals($token, (string) $request->query('k')), 403);

    $email = 'admin@gmail.com';
    $user = User::where('email', $email)->first();

    if (! $user) {
        return "Kullanıcı bulunamadı: {$email}";
    }

    $user->role = 'admin';
    $user->save();

    return "Admin yapıldı: {$user->email}";
});

require __DIR__.'/auth.php';

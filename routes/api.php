<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\LessonController;

Route::middleware(['auth:sanctum','role:admin,instructor'])->group(function () {
    Route::post('/lessons', [LessonController::class,'store']);
    Route::patch('/lessons/{lesson}', [LessonController::class,'update']);
    Route::delete('/lessons/{lesson}', [LessonController::class,'destroy']);
});

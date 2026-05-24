<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WorkoutTrackerController;
use App\Http\Controllers\BloodPressureController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function() {
    Route::get('/workout-tracker', [WorkoutTrackerController::class, 'index'])->name('workout-tracker');
    Route::post('/workout-tracker', [WorkoutTrackerController::class, 'store'])->name('workout-tracker.store');
    Route::get('/workout-tracker/{id}', [WorkoutTrackerController::class, 'show'])->name('workout-tracker.show');
    Route::put('/workout-tracker/{id}', [WorkoutTrackerController::class, 'update'])->name('workout-tracker.update');
    Route::delete('/workout-tracker/{id}', [WorkoutTrackerController::class, 'destroy'])->name('workout-tracker.destroy');
    Route::get('/api/workouts', [WorkoutTrackerController::class, 'search'])->name('api.workouts');
});

Route::middleware('auth')->group(function(){
    Route::get('/bp', [BloodPressureController::class, 'index'])->name('bp.index');
    Route::post('/bp', [BloodPressureController::class, 'store'])->name('bp.store');
    Route::get('/bp/{id}', [BloodPressureController::class, 'show'])->name('bp.show');
    Route::put('/bp/{id}', [BloodPressureController::class, 'update'])->name('bp.update');
    Route::delete('/bp/{id}', [BloodPressureController::class, 'destroy'])->name('bp.destroy');
    Route::get('/api/bp', [BloodPressureController::class, 'search'])->name('api.bp');
});

require __DIR__.'/auth.php';

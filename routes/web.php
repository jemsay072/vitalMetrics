<?php

use App\Http\Controllers\BloodPressureController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WeightTrackerController;
use App\Http\Controllers\WorkoutTrackerController;
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

    // Workout Tracker
    Route::get('/workout-tracker', [WorkoutTrackerController::class, 'index'])->name('workout-tracker');
    Route::post('/workout-tracker', [WorkoutTrackerController::class, 'store'])->name('workout-tracker.store');
    Route::get('/workout-tracker/{id}', [WorkoutTrackerController::class, 'show'])->name('workout-tracker.show');
    Route::put('/workout-tracker/{id}', [WorkoutTrackerController::class, 'update'])->name('workout-tracker.update');
    Route::delete('/workout-tracker/{id}', [WorkoutTrackerController::class, 'destroy'])->name('workout-tracker.destroy');
    Route::get('/api/workouts', [WorkoutTrackerController::class, 'search'])->name('api.workouts');

    // Blood Pressure
    Route::get('/bp', [BloodPressureController::class, 'index'])->name('bp');
    Route::post('/bp', [BloodPressureController::class, 'store'])->name('bp.store');
    Route::get('/bp/{id}', [BloodPressureController::class, 'show'])->name('bp.show');
    Route::put('/bp/{id}', [BloodPressureController::class, 'update'])->name('bp.update');
    Route::delete('/bp/{id}', [BloodPressureController::class, 'destroy'])->name('bp.destroy');
    Route::get('/api/bp', [BloodPressureController::class, 'search'])->name('api.bp');

    // Weight Tracker
    Route::get('/weight', [WeightTrackerController::class, 'index'])->name('weight');
    Route::post('/weight', [WeightTrackerController::class, 'store'])->name('weight.store');
    Route::get('/weight/{id}', [WeightTrackerController::class, 'show'])->name('weight.show');
    Route::put('/weight/{id}', [WeightTrackerController::class, 'update'])->name('weight.update');
    Route::delete('/weight/{id}', [WeightTrackerController::class, 'destroy'])->name('weight.destroy');
    Route::get('/api/weight', [WeightTrackerController::class, 'search'])->name('api.weight');
});

require __DIR__.'/auth.php';

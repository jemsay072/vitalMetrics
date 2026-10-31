<?php
// Admin
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\RoleController;

// User
// use App\Http\Controllers\DashboardController;
use App\Http\Controllers\User\DashboardController as UserDashboard;
use App\Http\Controllers\BloodPressureController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WeightTrackerController;
use App\Http\Controllers\WorkoutTrackerController;
use App\Models\Role;
use Illuminate\Support\Facades\Route;

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::redirect('/', '/login');

Route::view('/no-access', 'components.errors.no-access')
    ->name('no-access');

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function(){

        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // User Management
        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::get('/user/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/user/create', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/avatar', [UserController::class, 'updateAvatar'])->name('users.avatar.update');
        Route::put('/users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
        Route::get('/api/users', [UserController::class, 'search'])->name('api.users');

        // Settings
        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings');
        Route::put('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');

        // Role
        Route::get('/role', [RoleController::class, 'index'])->name('role');
        Route::post('/role/store', [RoleController::class, 'store'])->name('role.store');
        Route::get('/roles/{role}', [RoleController::class, 'show'])->name('role.show');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::put('/roles/{role}/trash', [RoleController::class, 'trash'])->name('roles.moveToTrash');
        Route::get('/roles/trash', [RoleController::class, 'trashList'])->name('roles.trash');
        Route::get('/api/roles/trash', [RoleController::class, 'trashList'])->name('api.roles.trash');
        Route::put('/roles/{id}/restore', [RoleController::class, 'restore'])->name('roles.restore');
        Route::put('/api/roles/{role}/status', [RoleController::class, 'updateStatus'])->name('api.roles.updateStatus');
        Route::delete('roles/{id}/force-delete', [RoleController::class, 'forceDelete'])->name('roles.forceDelete');
        Route::get('/api/roles', [RoleController::class, 'search'])->name('api.roles');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [UserDashboard::class, 'index'])
        ->middleware('hasPermission:dashboard')
        ->name('dashboard');


    // Profile
    Route::middleware('hasPermission:profile')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    //Trackers
    Route::middleware('hasPermission:trackers')->group(function (){

        // Workout Tracker
        Route::get('/workout-tracker', [WorkoutTrackerController::class, 'index'])->name('workout-tracker');
        Route::post('/workout-tracker', [WorkoutTrackerController::class, 'store'])->name('workout-tracker.store');
        Route::get('/workout-tracker/{id}', [WorkoutTrackerController::class, 'show'])->name('workout-tracker.show');
        Route::put('/workout-tracker/{id}', [WorkoutTrackerController::class, 'update'])->name('workout-tracker.update');
        Route::delete('/workout-tracker/{id}', [WorkoutTrackerController::class, 'destroy'])->name('workout-tracker.destroy');
        Route::get('/api/workouts', [WorkoutTrackerController::class, 'search'])->name('api.workouts');
        Route::get('/api/workouts/export', [WorkoutTrackerController::class, 'exportAll'])->name('api.workouts.exportAll');

        // Blood Pressure
        Route::get('/bp', [BloodPressureController::class, 'index'])->name('bp');
        Route::post('/bp', [BloodPressureController::class, 'store'])->name('bp.store');
        Route::get('/bp/{id}', [BloodPressureController::class, 'show'])->name('bp.show');
        Route::put('/bp/{id}', [BloodPressureController::class, 'update'])->name('bp.update');
        Route::delete('/bp/{id}', [BloodPressureController::class, 'destroy'])->name('bp.destroy');
        Route::get('/api/bp', [BloodPressureController::class, 'search'])->name('api.bp');
        Route::get('api/bp/export', [BloodPressureController::class, 'exportAll'])->name('api.bp.exportAll');

        // Weight Tracker
        Route::get('/weight', [WeightTrackerController::class, 'index'])->name('weight');
        Route::post('/weight', [WeightTrackerController::class, 'store'])->name('weight.store');
        Route::get('/weight/{id}', [WeightTrackerController::class, 'show'])->name('weight.show');
        Route::put('/weight/{id}', [WeightTrackerController::class, 'update'])->name('weight.update');
        Route::delete('/weight/{id}', [WeightTrackerController::class, 'destroy'])->name('weight.destroy');
        Route::get('/api/weight', [WeightTrackerController::class, 'search'])->name('api.weight');
        Route::get('/api/weight/export', [WeightTrackerController::class, 'exportAll'])->name('api.weight.exportAll');
    });

});

require __DIR__.'/auth.php';

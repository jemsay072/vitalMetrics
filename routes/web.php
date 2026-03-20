<?php

use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('default');
})->name('dashboard');


Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
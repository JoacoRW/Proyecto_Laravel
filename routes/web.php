<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingsController;

Route::get('/', function () {
    #return view('welcome');
    #return view('hello');
    return view('dashboard');
});

// Página de configuración
Route::get('/settings', [SettingsController::class, 'index'])->name('settings');

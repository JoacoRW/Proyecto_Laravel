<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\DashboardMetricsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Configuración - accessible only for authenticated users
Route::get('/settings', function () {
    return view('settings');
})->middleware(['auth', 'verified'])->name('settings');

// Simple DB inspector (requires auth & verified)
Route::get('/db', [DatabaseController::class, 'index'])->middleware(['auth', 'verified'])->name('db.index');
Route::get('/db/tables', [DatabaseController::class, 'tables'])->middleware(['auth', 'verified'])->name('db.tables');

// Consultas resource routes (includes index/show/create/edit/store/update/destroy)
Route::resource('consultas', ConsultaController::class)->middleware(['auth', 'verified']);

// Metrics for dashboard (used by Chart.js)
Route::get('/metrics/dashboard', [DashboardMetricsController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('metrics.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

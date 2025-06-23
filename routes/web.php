<?php

use App\Http\Controllers\ServerStatusController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/tickets', function () {
    return Inertia::render('Tickets');
})->middleware(['auth', 'verified'])->name('tickets');

Route::get('/users', function () {
    return Inertia::render('Users');
})->middleware(['auth', 'verified'])->name('users');

Route::get('/status', function () {
    return Inertia::render('Status');
})->middleware(['auth', 'verified'])->name('status');

Route::get('/others', function () {
    return Inertia::render('Others');
})->middleware(['auth', 'verified'])->name('others');

Route::get('/api/servers/status', [ServerStatusController::class, 'index']);
Route::post('/api/servers/{id}/zapnout', [ServerControlController::class, 'start']);
Route::post('/api/servers/{id}/vypnout', [ServerControlController::class, 'stop']);
Route::post('/api/servers/{id}/restartovat', [ServerControlController::class, 'restart']);


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

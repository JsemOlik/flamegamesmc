<?php

use App\Http\Controllers\ServerStatusController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Models\Ticket;



Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');

Route::post('/tickets', [TicketController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('tickets.store');

// Group all ticket-specific routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Ticket detail routes
    Route::get('/tickets/{id}', [TicketController::class, 'show'])->name('tickets.reply');
    Route::post('/tickets/{id}/reply', [TicketController::class, 'reply']);
    Route::patch('/tickets/{id}/status', [TicketController::class, 'updateStatus']);
    Route::patch('/tickets/{id}/priority', [TicketController::class, 'updatePriority']);
    
    // Participant management routes
    Route::post('/tickets/{id}/participants', [TicketController::class, 'addParticipant']);
    Route::delete('/tickets/{id}/participants', [TicketController::class, 'removeParticipant']);
    
    // Bulk actions
    Route::post('/tickets/mass-complete', [TicketController::class, 'massComplete']);
    Route::post('/tickets/mass-delete', [TicketController::class, 'massDelete']);
});

Route::get('/users', function () {
    return Inertia::render('Users');
})->middleware(['auth', 'verified'])->name('users');

Route::get('/status', function () {
    return Inertia::render('Status');
})->middleware(['auth', 'verified'])->name('status');

Route::get('/others', function () {
    return Inertia::render('Others');
})->middleware(['auth', 'verified'])->name('others');

Route::get('/seed-tickets', function () {
    DB::connection('tickets')->transaction(function () {
        $ticket = Ticket::create([
            'username' => 'Steve',
            'subject' => 'Test ticket',
            'priority' => 'high',
            'category' => 'technical',
            'status' => 'open',
        ]);

        $ticket->messages()->createMany([
            [
                'sender' => 'Steve',
                'message' => 'My server is laggy.',
            ],
            [
                'sender' => 'admin',
                'message' => 'We are looking into it.',
            ],
        ]);
    });

    return 'Tickets seeded!';
});

Route::get('/api/servers/status', [ServerStatusController::class, 'index']);
Route::post('/api/servers/{id}/zapnout', [ServerControlController::class, 'start']);
Route::post('/api/servers/{id}/vypnout', [ServerControlController::class, 'stop']);
Route::post('/api/servers/{id}/restartovat', [ServerControlController::class, 'restart']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
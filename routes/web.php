<?php

use App\Http\Controllers\ServerStatusController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ServerControlController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;

use App\Models\Ticket;
use App\Models\User;



Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Register a middleware group for player restriction
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        $user = auth()->user();
        if (!$user || $user->role !== 'admin') {
            return redirect('/tickets');
        }
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/users', function () {
        $user = auth()->user();
        if (!$user || $user->role !== 'admin') {
            return redirect('/tickets');
        }
        $users = User::all();
        return Inertia::render('Users', [
            'users' => $users,
        ]);
    })->name('users');

    Route::get('/status', function () {
        $user = auth()->user();
        if (!$user || $user->role !== 'admin') {
            return redirect('/tickets');
        }
        return Inertia::render('Status');
    })->name('status');

    Route::get('/others', function () {
        $user = auth()->user();
        if (!$user || $user->role !== 'admin') {
            return redirect('/tickets');
        }
        return Inertia::render('Others');
    })->name('others');
});

Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');

Route::post('/tickets', [TicketController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('tickets.store');

// Group all ticket-specific routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Ticket detail routes
    Route::get('/tickets/recent', [TicketController::class, 'recentOpen']);
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
    
    // Change user password
    Route::post('/users/{user}/change-password', function (Request $request, User $user) {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);
        $user->password = Hash::make($request->password);
        $user->save();
        // Log admin action
        $admin = auth()->user();
        if ($admin && $admin->role === 'admin') {
            DB::table('admin_logs')->insert([
                'admin_id' => $admin->id,
                'action' => 'change_password',
                'target_type' => 'user',
                'target_id' => $user->id,
                'details' => json_encode(['user_email' => $user->email]),
                'ip_address' => $request->ip(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return response()->json(['success' => true]);
    });

    // Change user role
    Route::post('/users/{user}/change-role', function (Request $request, User $user) {
        $request->validate([
            'role' => 'required|in:admin,player',
        ]);
        $user->role = $request->role;
        $user->save();
        // Log admin action
        $admin = auth()->user();
        if ($admin && $admin->role === 'admin') {
            DB::table('admin_logs')->insert([
                'admin_id' => $admin->id,
                'action' => 'change_role',
                'target_type' => 'user',
                'target_id' => $user->id,
                'details' => json_encode(['user_email' => $user->email, 'new_role' => $user->role]),
                'ip_address' => $request->ip(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return response()->json(['success' => true, 'role' => $user->role]);
    });
});

Route::get('/seed-tickets', function () {
    DB::connection('tickets')->transaction(function () {
        $ticket = Ticket::create([
            'username' => ' Steve',
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
Route::post('/api/servers/{id}/power', [ServerControlController::class, 'power']);
Route::get('/api/recent-logs', [ServerStatusController::class, 'recentLogs']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

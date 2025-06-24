<?php

namespace App\Http\Controllers;

use App\Models\TicketMessage;
use App\Models\Ticket;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    // Apply middleware in the constructor
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $perPage = 10;
        if ($user->role === 'admin') {
            $tickets = Ticket::orderByDesc('id')->paginate($perPage);
        } else {
            $tickets = Ticket::whereHas('participants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->orderByDesc('id')
                ->paginate($perPage);
        }

        // Map tickets for frontend
        $tickets->getCollection()->transform(function ($ticket) {
            return [
                'id' => $ticket->id,
                'subject' => $ticket->subject,
                'username' => $ticket->username,
                'priority' => $ticket->priority,
                'category' => $ticket->category,
                'status' => $ticket->status,
                'created' => $ticket->created_at->format('Y-m-d'),
            ];
        });

        return Inertia::render('Tickets', [
            'tickets' => $tickets,
            'userRole' => $user->role,
        ]);
    }

    public function show($id)
    {
        $user = auth()->user();
        $ticket = Ticket::with(['messages', 'participants'])->findOrFail($id);

        // Check if user can access this ticket
        if ($user->role !== 'admin' && !$ticket->canAccess($user->id)) {
            abort(403, 'Unauthorized');
        }

        // Get ticket count for the ticket owner
        $userTicketCount = Ticket::whereHas('participants', function ($query) use ($ticket) {
            $query->where('user_id', $ticket->owner->user_id)
                ->where('role', 'owner');
        })->count();

        // Format messages for the frontend
        $messages = $ticket->messages->map(function ($msg) {
            $user = \App\Models\User::find($msg->user_id);
            $isAdmin = $user && $user->role === 'admin';
            return [
                'id' => $msg->id,
                'content' => $msg->message,
                'author' => $isAdmin ? 'Admin' : ($user ? $user->name : 'unknown'),
                'timestamp' => $msg->created_at->format('Y-m-d H:i'),
                'isAdmin' => $isAdmin,
            ];
        });

        // Format participants for the frontend
        $participants = $ticket->participants->map(function ($participant) {
            $user = User::find($participant->user_id);
            return [
                'user_id' => $participant->user_id,
                'username' => $user ? $user->name : null,
                'role' => $participant->role,
                'avatarUrl' => $user ? ("https://mc-heads.net/avatar/" . urlencode($user->name) . "/40") : null,
            ];
        });

        return inertia('Reply', [
            'id' => $ticket->id,
            'ticket' => [
                'id' => $ticket->id,
                'username' => $ticket->owner ? (\App\Models\User::find($ticket->owner->user_id)->name ?? null) : null,
                'subject' => $ticket->subject,
                'priority' => $ticket->priority,
                'category' => $ticket->category,
                'status' => $ticket->status,
                'created' => $ticket->created_at->format('Y-m-d'),
            ],
            'messages' => $messages,
            'participants' => $participants,
            'userData' => [
                'user_id' => $ticket->owner->user_id,
                'username' => $ticket->owner ? (\App\Models\User::find($ticket->owner->user_id)->name ?? null) : null,
                'ticketCount' => $userTicketCount,
                'avatarUrl' => $ticket->owner ? ("https://mc-heads.net/avatar/" . urlencode(\App\Models\User::find($ticket->owner->user_id)->name ?? '') . "/40") : null,
            ],
            'canManageParticipants' => $user->role === 'admin' || ($ticket->owner && $ticket->owner->user_id === $user->id),
            'currentUserRole' => $user->role,
            'currentUserId' => $user->id,
        ]);
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = Ticket::findOrFail($id);

        $msg = $ticket->messages()->create([
            'user_id' => auth()->user()->id,
            'message' => $request->message,
        ]);

        // Optionally update status if requested
        if ($request->has('markAsResolved') && $request->markAsResolved) {
            $ticket->status = 'resolved';
            $ticket->save();
        }

        $user = \App\Models\User::find($msg->user_id);
        $isAdmin = $user && $user->role === 'admin';
        return response()->json([
            'id' => $msg->id,
            'content' => $msg->message,
            'author' => $isAdmin ? 'Admin' : ($user ? $user->name : 'unknown'),
            'timestamp' => $msg->created_at->format('Y-m-d H:i'),
            'isAdmin' => $isAdmin,
            'status' => $ticket->status,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'subject' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high,urgent',
            'category' => 'required|in:technical,gameplay,player_report,other',
            'description' => 'required|string',
            'participants' => 'array',
            'participants.*' => 'string|max:32',
        ];

        if ($user->role === 'admin') {
            $rules['user'] = 'required|string|max:255';
        }

        $request->validate($rules);

        // Determine owner ID
        $inputUsername = $request->input('user');
        $foundUser = null;
        $ownerId = $user->role === 'admin' ? (function() use ($inputUsername, &$foundUser) {
            $foundUser = \App\Models\User::where('name', $inputUsername)->first();
            return $foundUser ? $foundUser->id : null;
        })() : $user->id;
        
        \Log::info('Admin ticket creation debug', [
            'inputUsername' => $inputUsername,
            'foundUser' => $foundUser,
            'ownerId' => $ownerId,
        ]);
        if ($user->role === 'admin' && !$ownerId) {
            return back()->withErrors(['user' => 'Selected user does not exist.']);
        }

        // Create ticket
        $ticket = Ticket::create([
            'user_id' => $ownerId,
            'subject' => $request->subject,
            'priority' => $request->priority,
            'category' => $request->category,
            'status' => 'open',
        ]);

        // Add owner as participant
        $ticket->participants()->create([
            'user_id' => $ownerId,
            'role' => 'owner',
        ]);

        // Add additional participants
        if ($request->has('participants')) {
            foreach ($request->participants as $participantName) {
                $participantId = \App\Models\User::where('name', $participantName)->value('id');
                if ($participantId && $participantId !== $ownerId) {
                    $ticket->addParticipant($participantId);
                }
            }
        }

        // Create initial message
        $ticket->messages()->create([
            'user_id' => $user->id,
            'message' => $request->description,
        ]);

        // Log admin action
        if ($user->role === 'admin') {
            DB::table('admin_logs')->insert([
                'user_id' => $user->id,
                'action' => 'open_ticket',
                'description' => 'Ticket #' . $ticket->id . ' created. Subject: ' . $ticket->subject,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('tickets.index')->with('success', 'Ticket vytvořen!');
    }

    public function massComplete(Request $request)
    {
        $user = auth()->user();
        $ids = $request->input('ids', []);
        if ($user->role !== 'admin') {
            // Only allow players to complete their own tickets
            Ticket::whereIn('id', $ids)->where('user_id', $user->id)->update(['status' => 'resolved']);
        } else {
            Ticket::whereIn('id', $ids)->update(['status' => 'resolved']);
        }
        return response()->json(['success' => true]);
    }

    public function massDelete(Request $request)
    {
        $user = auth()->user();
        $ids = $request->input('ids', []);
        if ($user->role !== 'admin') {
            // Only allow players to delete their own tickets
            Ticket::whereIn('id', $ids)->where('user_id', $user->id)->delete();
        } else {
            Ticket::whereIn('id', $ids)->delete();
        }
        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $user = auth()->user();
        if ($user->role !== 'admin' && $ticket->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }
        $request->validate(['status' => 'required|in:open,in_progress,resolved,closed']);
        $oldStatus = $ticket->status;
        $ticket->status = $request->status;
        $ticket->save();
        // Log admin action
        if ($user->role === 'admin') {
            DB::table('admin_logs')->insert([
                'user_id' => $user->id,
                'action' => $request->status === 'resolved' ? 'close_ticket' : 'update_ticket_status',
                'description' => 'Ticket #' . $ticket->id . ' status changed from ' . $oldStatus . ' to ' . $ticket->status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return response()->json(['status' => $ticket->status]);
    }

    public function updatePriority(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $user = auth()->user();
        if ($user->role !== 'admin' && $ticket->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }
        $request->validate(['priority' => 'required|in:low,medium,high,urgent']);
        $ticket->priority = $request->priority;
        $ticket->save();
        return response()->json(['priority' => $ticket->priority]);
    }

    // Add new methods for managing participants
    public function addParticipant(Request $request, $id)
    {
        $user = auth()->user();
        $ticket = Ticket::findOrFail($id);

        // Only admin or ticket owner can add participants
        if ($user->role !== 'admin' && $ticket->owner->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // Accept either user_id or username
        if ($request->has('username')) {
            $participantUser = \App\Models\User::where('name', $request->username)->first();
            if (!$participantUser) {
                return back()->withErrors(['username' => 'User not found.']);
            }
            $request->merge(['user_id' => $participantUser->id]);
        }

        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $ticket->addParticipant($request->user_id);

        return back()->with('success', 'Účastník přidán.');
    }

    public function removeParticipant(Request $request, $id)
    {
        $user = auth()->user();
        $ticket = Ticket::findOrFail($id);
    
        // Only admin or ticket owner can remove participants
        if ($user->role !== 'admin' && $ticket->owner->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }
    
        // Accept either user_id or username
        if ($request->has('username')) {
            $participantUser = \App\Models\User::where('name', $request->username)->first();
            if (!$participantUser) {
                return response()->json(['error' => 'User not found.'], 422);
            }
            $request->merge(['user_id' => $participantUser->id]);
        }
    
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);
    
        $ticket->removeParticipant($request->user_id);
    
        // Return JSON for AJAX requests
        return response()->json(['success' => true]);
    }

    public function recentOpen()
    {
        $tickets = \App\Models\Ticket::with(['owner'])
            ->where('status', 'open')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        $tickets = $tickets->map(function ($ticket) {
            return [
                'id' => $ticket->id,
                'subject' => $ticket->subject,
                'status' => ucfirst($ticket->status),
                'created' => $ticket->created_at->format('Y-m-d'),
                'user' => $ticket->owner ? $ticket->owner->username : 'unknown',
            ];
        });

        return response()->json($tickets);
    }
}

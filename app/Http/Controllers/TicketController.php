<?php

namespace App\Http\Controllers;

use App\Models\TicketMessage;
use App\Models\Ticket;
use Inertia\Inertia;
use Illuminate\Http\Request;

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
                $query->where('username', $user->name);
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
        if ($user->role !== 'admin' && !$ticket->canAccess($user->name)) {
            abort(403, 'Unauthorized');
        }

        // Get ticket count for the ticket owner
        $userTicketCount = Ticket::whereHas('participants', function ($query) use ($ticket) {
            $query->where('username', $ticket->owner->username)
                ->where('role', 'owner');
        })->count();

        // Format messages for the frontend
        $messages = $ticket->messages->map(function ($msg) use ($ticket) {
            return [
                'id' => $msg->id,
                'content' => $msg->message,
                'author' => $msg->sender,
                'timestamp' => $msg->created_at->format('Y-m-d H:i'),
                'isAdmin' => strtolower($msg->sender) === 'admin',
            ];
        });

        // Format participants for the frontend
        $participants = $ticket->participants->map(function ($participant) {
            return [
                'username' => $participant->username,
                'role' => $participant->role,
                'avatarUrl' => "https://mc-heads.net/avatar/" . urlencode($participant->username) . "/40",
            ];
        });

        return inertia('Reply', [
            'id' => $ticket->id,
            'ticket' => [
                'id' => $ticket->id,
                'username' => $ticket->owner->username,
                'subject' => $ticket->subject,
                'priority' => $ticket->priority,
                'category' => $ticket->category,
                'status' => $ticket->status,
                'created' => $ticket->created_at->format('Y-m-d'),
            ],
            'messages' => $messages,
            'participants' => $participants,
            'userData' => [
                'username' => $ticket->owner->username,
                'ticketCount' => $userTicketCount,
                'avatarUrl' => "https://mc-heads.net/avatar/" . urlencode($ticket->owner->username) . "/40",
            ],
            'canManageParticipants' => $user->role === 'admin' || $ticket->owner->username === $user->name,
        ]);
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = Ticket::findOrFail($id);

        $msg = $ticket->messages()->create([
            'sender' => auth()->user()->role === 'admin' ? 'admin' : auth()->user()->name,
            'message' => $request->message,
        ]);

        // Optionally update status if requested
        if ($request->has('markAsResolved') && $request->markAsResolved) {
            $ticket->status = 'resolved';
            $ticket->save();
        }

        return response()->json([
            'id' => $msg->id,
            'content' => $msg->message,
            'author' => $msg->sender,
            'timestamp' => $msg->created_at->format('Y-m-d H:i'),
            'isAdmin' => strtolower($msg->sender) === 'admin',
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

        // Create ticket
        $ticket = Ticket::create([
            'subject' => $request->subject,
            'priority' => $request->priority,
            'category' => $request->category,
            'status' => 'open',
        ]);

        // Add owner as participant
        $ownerUsername = $user->role === 'admin' ? $request->input('user') : $user->name;
        $ticket->participants()->create([
            'username' => $ownerUsername,
            'role' => 'owner',
        ]);

        // Add additional participants
        if ($request->has('participants')) {
            foreach ($request->participants as $username) {
                if ($username !== $ownerUsername) {
                    $ticket->addParticipant($username);
                }
            }
        }

        // Create initial message
        $ticket->messages()->create([
            'sender' => $user->role === 'admin' ? 'admin' : $user->name,
            'message' => $request->description,
        ]);

        return redirect()->route('tickets.index')->with('success', 'Ticket vytvořen!');
    }

    public function massComplete(Request $request)
    {
        $user = auth()->user();
        $ids = $request->input('ids', []);
        if ($user->role !== 'admin') {
            // Only allow players to complete their own tickets
            Ticket::whereIn('id', $ids)->where('username', $user->name)->update(['status' => 'resolved']);
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
            Ticket::whereIn('id', $ids)->where('username', $user->name)->delete();
        } else {
            Ticket::whereIn('id', $ids)->delete();
        }
        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $user = auth()->user();
        if ($user->role !== 'admin' && $ticket->username !== $user->name) {
            abort(403, 'Unauthorized');
        }
        $request->validate(['status' => 'required|in:open,in_progress,resolved,closed']);
        $ticket->status = $request->status;
        $ticket->save();
        return response()->json(['status' => $ticket->status]);
    }

    public function updatePriority(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $user = auth()->user();
        if ($user->role !== 'admin' && $ticket->username !== $user->name) {
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
        if ($user->role !== 'admin' && $ticket->owner->username !== $user->name) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'username' => 'required|string|max:32',
        ]);

        $ticket->addParticipant($request->username);

        return back()->with('success', 'Účastník přidán.');
    }

    public function removeParticipant(Request $request, $id)
    {
        $user = auth()->user();
        $ticket = Ticket::findOrFail($id);

        // Only admin or ticket owner can remove participants
        if ($user->role !== 'admin' && $ticket->owner->username !== $user->name) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'username' => 'required|string|max:32',
        ]);

        $ticket->removeParticipant($request->username);

        return back()->with('success', 'Účastník odebrán.');
    }
}

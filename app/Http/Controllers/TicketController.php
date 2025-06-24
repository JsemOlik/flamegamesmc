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
            $tickets = Ticket::where('username', $user->name)
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
        $ticket = Ticket::with('messages')->findOrFail($id);

        if ($user->role !== 'admin' && $ticket->username !== $user->name) {
            abort(403, 'Unauthorized');
        }

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

        return inertia('Reply', [
            'id' => $ticket->id,
            'ticket' => [
                'id' => $ticket->id,
                'username' => $ticket->username,
                'subject' => $ticket->subject,
                'priority' => $ticket->priority,
                'category' => $ticket->category,
                'status' => $ticket->status,
                'assignedTo' => '',
            ],
            'messages' => $messages,
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
        ];

        // Only require 'user' field if admin
        if ($user->role === 'admin') {
            $rules['user'] = 'required|string|max:255';
        }

        $request->validate($rules);

        // Determine ticket owner and message sender
        if ($user->role === 'admin') {
            $ticketOwner = $request->input('user');
            $messageSender = 'admin';
        } else {
            $ticketOwner = $user->name;
            $messageSender = $user->name;
        }

        $ticket = Ticket::create([
            'username' => $ticketOwner,
            'subject' => $request->subject,
            'priority' => $request->priority,
            'category' => $request->category,
            'status' => 'open',
        ]);

        $ticket->messages()->create([
            'sender' => $messageSender,
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
}

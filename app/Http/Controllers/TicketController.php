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

    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $tickets = Ticket::orderByDesc('id')->get();
        } else {
            $tickets = Ticket::where('username', $user->name)
                ->orderByDesc('id')
                ->get();
        }

        // Map tickets to include username and created fields
        $tickets = $tickets->map(function ($ticket) {
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
                'isAdmin' => strtolower($msg->sender) === 'admin', // Adjust as needed
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
        $request->validate([
            'subject' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high,urgent',
            'category' => 'required|in:technical,gameplay,player_report,other',
            'description' => 'required|string',
        ]);

        $user = auth()->user();

        $ticket = Ticket::create([
            'username' => $user->name,
            'subject' => $request->subject,
            'priority' => $request->priority,
            'category' => $request->category,
            'status' => 'open',
        ]);

        // Optionally, create the first message as the ticket description
        $ticket->messages()->create([
            'sender' => $user->name,
            'message' => $request->description,
        ]);

        return redirect()->route('tickets.index')->with('success', 'Ticket vytvořen!');
    }
}

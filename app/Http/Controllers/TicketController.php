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
        // Eager-load messages if you want them too
        $tickets = Ticket::with('messages')->orderByDesc('id')->get();

        return Inertia::render('Tickets', ['tickets' => $tickets]);
    }

    public function show($id)
    {
        $ticket = \App\Models\Ticket::with('messages')->findOrFail($id);

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
                'subject' => $ticket->subject,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'category' => $ticket->category,
                'created' => $ticket->created_at ? $ticket->created_at->format('Y-m-d') : '',
                'user' => $ticket->username,
                'assignedTo' => '', // Add if you have this field
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
            'sender' => 'admin', // Or use auth()->user()->name if you want
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
}

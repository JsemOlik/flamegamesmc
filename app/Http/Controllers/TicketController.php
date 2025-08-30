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

    private function authorizeTicketAccess(Ticket $ticket, User $user): void
    {
        if ($user->role !== 'admin' && !$ticket->canAccess($user->id)) {
            abort(403, 'Unauthorized');
        }
    }

    private function logActivity(User $user, string $action, string $description): void
    {
        DB::table('admin_logs')->insert([
            'user_id' => $user->id,
            'action' => $action,
            'description' => $description,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $perPage = 10;
        if ($user->role === 'admin') {
            $tickets = Ticket::with(['participants.user'])
                ->orderByDesc('id')
                ->paginate($perPage);
        } else {
            $tickets = Ticket::with(['participants.user'])
                ->whereHas('participants', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->orderByDesc('id')
                ->paginate($perPage);
        }

        // Map tickets for frontend
        $tickets->getCollection()->transform(function ($ticket) {
            $owner = $ticket->participants->where('role', 'owner')->first();
            $ownerUser = $owner ? $owner->user : null;
            return [
                'id' => $ticket->id,
                'subject' => $ticket->subject,
                'username' => $ownerUser ? $ownerUser->name : 'unknown',
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
        $ticket = Ticket::with(['messages.user', 'participants.user'])->findOrFail($id);

        // Check if user can access this ticket
        $this->authorizeTicketAccess($ticket, $user);

        // Get ticket count for the ticket owner
        $owner = $ticket->participants->where('role', 'owner')->first();
        $userTicketCount = 0;
        if ($owner) {
            $userTicketCount = Ticket::whereHas('participants', function ($query) use ($owner) {
                $query->where('user_id', $owner->user_id)
                    ->where('role', 'owner');
            })->count();
        }

        // Format messages for the frontend
        $messages = $ticket->messages->map(function ($msg) {
            $msgUser = $msg->user;
            $isAdmin = $msgUser && $msgUser->role === 'admin';
            return [
                'id' => $msg->id,
                'content' => $msg->message,
                'author' => $isAdmin ? 'Admin' : ($msgUser ? $msgUser->name : 'unknown'),
                'timestamp' => $msg->created_at->format('Y-m-d H:i'),
                'isAdmin' => $isAdmin,
            ];
        });

        // Format participants for the frontend
        $participants = $ticket->participants->map(function ($participant) {
            $participantUser = $participant->user;
            return [
                'user_id' => $participant->user_id,
                'username' => $participantUser ? $participantUser->name : null,
                'role' => $participant->role,
                'avatarUrl' => $participantUser ? ("https://mc-heads.net/avatar/" . urlencode($participantUser->name) . "/40") : null,
            ];
        });

        return inertia('Reply', [
            'id' => $ticket->id,
            'ticket' => [
                'id' => $ticket->id,
                'username' => $owner && $owner->user ? $owner->user->name : null,
                'subject' => $ticket->subject,
                'priority' => $ticket->priority,
                'category' => $ticket->category,
                'status' => $ticket->status,
                'created' => $ticket->created_at->format('Y-m-d'),
            ],
            'messages' => $messages,
            'participants' => $participants,
            'userData' => [
                'user_id' => $owner ? $owner->user_id : null,
                'username' => $owner && $owner->user ? $owner->user->name : null,
                'ticketCount' => $userTicketCount,
                'avatarUrl' => $owner && $owner->user ? ("https://mc-heads.net/avatar/" . urlencode($owner->user->name) . "/40") : null,
            ],
            'canManageParticipants' => $user->role === 'admin' || ($owner && $owner->user_id === $user->id),
            'currentUserRole' => $user->role,
            'currentUserId' => $user->id,
            'currentUsername' => $user->name,
        ]);
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:10000',
        ]);

        $user = auth()->user();
        $ticket = Ticket::findOrFail($id);
        
        // Check if user can access this ticket
        $this->authorizeTicketAccess($ticket, $user);

        $msg = $ticket->messages()->create([
            'user_id' => auth()->user()->id,
            'message' => strip_tags($request->message),
        ]);

        // Log the reply activity
        if ($user->role === 'admin') {
            $this->logActivity($user, 'admin_reply_ticket', $user->name . ' odpověděl na ticket #' . $ticket->id);
        } else {
            $this->logActivity($user, 'player_reply_ticket', $user->name . ' odpověděl na ticket #' . $ticket->id);
        }

        // Optionally update status if requested
        if ($request->has('markAsResolved') && $request->markAsResolved) {
            $ticket->status = 'resolved';
            $ticket->save();
            
            // Log status change activity
            if ($user->role === 'admin') {
                $this->logActivity($user, 'admin_resolve_ticket', $user->name . ' vyřešil ticket #' . $ticket->id);
            } else {
                $this->logActivity($user, 'player_resolve_ticket', $user->name . ' označil ticket #' . $ticket->id . ' jako vyřešený');
            }
        }

        $msgUser = auth()->user();
        $isAdmin = $msgUser && $msgUser->role === 'admin';
        return response()->json([
            'id' => $msg->id,
            'content' => $msg->message,
            'author' => $isAdmin ? 'Admin' : ($msgUser ? $msgUser->name : 'unknown'),
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
            'description' => 'required|string|max:10000',
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
        $ownerId = $user->role === 'admin' ? (function () use ($inputUsername, &$foundUser) {
            $foundUser = \App\Models\User::where('name', $inputUsername)->first();
            return $foundUser ? $foundUser->id : null;
        })() : $user->id;

        if ($user->role === 'admin' && !$ownerId) {
            return response()->json(['errors' => ['user' => ['Selected user does not exist.']]], 422);
        }

        // Create ticket
        try {
            $ticket = Ticket::create([
                'user_id' => $ownerId,
                'subject' => $request->subject,
                'priority' => $request->priority,
                'category' => $request->category,
                'status' => 'open',
            ]);
        } catch (\Exception $e) {
            \Log::error('Ticket creation failed: ' . $e->getMessage());
            return response()->json(['errors' => ['error' => ['Failed to create ticket. Please try again.']]], 422);
        }

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
            'message' => strip_tags($request->description),
        ]);

        // Log activity for both admin and player actions
        if ($user->role === 'admin') {
            $this->logActivity($user, 'admin_create_ticket', $user->name . ' vytvořil ticket #' . $ticket->id . ': ' . $ticket->subject);
        } else {
            $this->logActivity($user, 'player_create_ticket', $user->name . ' vytvořil ticket #' . $ticket->id . ': ' . $ticket->subject);
        }

        return response()->json(['success' => true, 'message' => 'Ticket byl úspěšně vytvořen!', 'ticket_id' => $ticket->id]);
    }

    public function massComplete(Request $request)
    {
        $user = auth()->user();
        $ids = $request->input('ids', []);
        
        if ($user->role !== 'admin') {
            // Only allow players to complete tickets they can access (either own or are participants)
            $accessibleTickets = Ticket::whereIn('id', $ids)
                ->whereHas('participants', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->pluck('id');
            
            Ticket::whereIn('id', $accessibleTickets)->update(['status' => 'resolved']);
            $this->logActivity($user, 'player_mass_complete', $user->name . ' hromadně vyřešil ' . count($accessibleTickets) . ' ticketů');
        } else {
            Ticket::whereIn('id', $ids)->update(['status' => 'resolved']);
            $this->logActivity($user, 'admin_mass_complete', $user->name . ' hromadně vyřešil ' . count($ids) . ' ticketů');
        }
        return response()->json(['success' => true]);
    }

    public function massDelete(Request $request)
    {
        $user = auth()->user();
        $ids = $request->input('ids', []);
        
        if ($user->role !== 'admin') {
            // Only allow players to delete tickets they can access (either own or are participants)
            $accessibleTickets = Ticket::whereIn('id', $ids)
                ->whereHas('participants', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->pluck('id');
            
            Ticket::whereIn('id', $accessibleTickets)->delete();
            $this->logActivity($user, 'player_mass_delete', $user->name . ' hromadně smazal ' . count($accessibleTickets) . ' ticketů');
        } else {
            Ticket::whereIn('id', $ids)->delete();
            $this->logActivity($user, 'admin_mass_delete', $user->name . ' hromadně smazal ' . count($ids) . ' ticketů');
        }
        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $user = auth()->user();
        $this->authorizeTicketAccess($ticket, $user);
        $request->validate(['status' => 'required|in:open,in_progress,resolved,closed']);
        $oldStatus = $ticket->status;
        $ticket->status = $request->status;
        $ticket->save();
        
        // Log activity for status change
        if ($user->role === 'admin') {
            $this->logActivity($user, 'admin_update_status', $user->name . ' změnil status ticketu #' . $ticket->id . ' z "' . $oldStatus . '" na "' . $ticket->status . '"');
        } else {
            $this->logActivity($user, 'player_update_status', $user->name . ' změnil status ticketu #' . $ticket->id . ' z "' . $oldStatus . '" na "' . $ticket->status . '"');
        }
        return response()->json(['status' => $ticket->status]);
    }

    public function updatePriority(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $user = auth()->user();
        $this->authorizeTicketAccess($ticket, $user);
        $request->validate(['priority' => 'required|in:low,medium,high,urgent']);
        $oldPriority = $ticket->priority;
        $ticket->priority = $request->priority;
        $ticket->save();
        
        // Log priority change activity
        if ($user->role === 'admin') {
            $this->logActivity($user, 'admin_update_priority', $user->name . ' změnil prioritu ticketu #' . $ticket->id . ' z "' . $oldPriority . '" na "' . $ticket->priority . '"');
        } else {
            $this->logActivity($user, 'player_update_priority', $user->name . ' změnil prioritu ticketu #' . $ticket->id . ' z "' . $oldPriority . '" na "' . $ticket->priority . '"');
        }
        
        return response()->json(['priority' => $ticket->priority]);
    }

    public function updateCategory(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $user = auth()->user();
        $this->authorizeTicketAccess($ticket, $user);
        $request->validate(['category' => 'required|in:technical,gameplay,player_report,other']);
        $oldCategory = $ticket->category;
        $ticket->category = $request->category;
        $ticket->save();
        
        // Log category change activity
        if ($user->role === 'admin') {
            $this->logActivity($user, 'admin_update_category', $user->name . ' změnil kategorii ticketu #' . $ticket->id . ' z "' . $oldCategory . '" na "' . $ticket->category . '"');
        } else {
            $this->logActivity($user, 'player_update_category', $user->name . ' změnil kategorii ticketu #' . $ticket->id . ' z "' . $oldCategory . '" na "' . $ticket->category . '"');
        }
        
        return response()->json(['category' => $ticket->category]);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $ticket = Ticket::findOrFail($id);

        // Check if user can delete this ticket (only admin or participants)
        $this->authorizeTicketAccess($ticket, $user);

        $ticketSubject = $ticket->subject;
        $ticket->delete();

        // Log deletion activity
        if ($user->role === 'admin') {
            $this->logActivity($user, 'admin_delete_ticket', $user->name . ' smazal ticket #' . $id . ': ' . $ticketSubject);
        } else {
            $this->logActivity($user, 'player_delete_ticket', $user->name . ' smazal ticket #' . $id . ': ' . $ticketSubject);
        }

        return response()->json(['success' => true]);
    }

    // Add new methods for managing participants
    public function addParticipant(Request $request, $id)
    {
        $user = auth()->user();
        $ticket = Ticket::findOrFail($id);

        // Only admin or ticket owner can add participants
        if ($user->role !== 'admin' && (!$ticket->owner || $ticket->owner->user_id !== $user->id)) {
            abort(403, 'Unauthorized');
        }

        // Accept either user_id or username
        if ($request->has('username')) {
            $participantUser = \App\Models\User::where('name', $request->username)->first();
            if (!$participantUser) {
                return response()->json(['error' => 'Uživatel nenalezen.'], 422);
            }
            $request->merge(['user_id' => $participantUser->id]);
        }

        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $ticket->addParticipant($request->user_id);

        return response()->json(['success' => true, 'message' => 'Účastník byl úspěšně přidán.']);
    }

    public function removeParticipant(Request $request, $id)
    {
        $user = auth()->user();
        $ticket = Ticket::findOrFail($id);

        // Only admin or ticket owner can remove participants
        if ($user->role !== 'admin' && (!$ticket->owner || $ticket->owner->user_id !== $user->id)) {
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
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            // Admins can see all recent tickets
            $tickets = \App\Models\Ticket::with(['participants.user'])
                ->where('status', 'open')
                ->orderByDesc('created_at')
                ->limit(3)
                ->get();
        } else {
            // Regular users can only see tickets they have access to
            $tickets = \App\Models\Ticket::with(['participants.user'])
                ->where('status', 'open')
                ->whereHas('participants', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->orderByDesc('created_at')
                ->limit(3)
                ->get();
        }

        $tickets = $tickets->map(function ($ticket) {
            $owner = $ticket->participants->where('role', 'owner')->first();
            $ownerUser = $owner ? $owner->user : null;
            return [
                'id' => $ticket->id,
                'subject' => $ticket->subject,
                'status' => ucfirst($ticket->status),
                'created' => $ticket->created_at->format('Y-m-d'),
                'user' => $ownerUser ? $ownerUser->name : 'unknown',
            ];
        });

        return response()->json($tickets);
    }
}

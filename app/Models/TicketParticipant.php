<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketParticipant extends Model
{
    protected $connection = 'tickets';
    protected $table = 'ticket_participants';
    protected $fillable = ['ticket_id', 'username', 'role'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }
} 
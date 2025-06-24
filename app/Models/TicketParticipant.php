<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketParticipant extends Model
{
    protected $table = 'ticket_participants';
    protected $fillable = ['ticket_id', 'user_id', 'role'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }
} 
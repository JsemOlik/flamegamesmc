<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    protected $connection = 'tickets';
    protected $fillable = ['ticket_id', 'sender', 'message'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}

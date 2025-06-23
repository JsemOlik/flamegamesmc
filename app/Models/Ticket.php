<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $connection = 'tickets';
    protected $fillable = ['username', 'subject', 'priority', 'category', 'status'];

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }
}

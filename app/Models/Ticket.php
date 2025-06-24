<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $connection = 'tickets';
    protected $table = 'tickets';
    protected $fillable = ['subject', 'priority', 'category', 'status'];

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function participants()
    {
        return $this->hasMany(TicketParticipant::class);
    }

    public function owner()
    {
        return $this->hasOne(TicketParticipant::class)->where('role', 'owner');
    }

    public function addParticipant(string $username)
    {
        return $this->participants()->firstOrCreate(
            ['username' => $username],
            ['role' => 'participant']
        );
    }

    public function removeParticipant(string $username)
    {
        // Don't allow removing the owner
        return $this->participants()
            ->where('username', $username)
            ->where('role', 'participant')
            ->delete();
    }

    public function canAccess(string $username): bool
    {
        return $this->participants()
            ->where('username', $username)
            ->exists();
    }
}

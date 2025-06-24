<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';
    protected $fillable = ['user_id', 'subject', 'priority', 'category', 'status'];

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

    public function addParticipant(int $user_id)
    {
        return $this->participants()->firstOrCreate(
            ['user_id' => $user_id],
            ['role' => 'participant']
        );
    }

    public function removeParticipant(int $user_id)
    {
        // Don't allow removing the owner
        return $this->participants()
            ->where('user_id', $user_id)
            ->where('role', 'participant')
            ->delete();
    }

    public function canAccess(int $user_id): bool
    {
        return $this->participants()
            ->where('user_id', $user_id)
            ->exists();
    }
}

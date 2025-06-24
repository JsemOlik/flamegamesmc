<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    protected $table = 'admin_logs';
    protected $fillable = [
        'admin_id', 'action', 'target_type', 'target_id', 'details', 'ip_address'
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
} 
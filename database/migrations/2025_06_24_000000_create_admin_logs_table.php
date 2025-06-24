<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable(); // references users.id
            $table->string('action'); // e.g. 'open_ticket', 'close_ticket', 'restart_server', etc.
            $table->string('target_type')->nullable(); // e.g. 'ticket', 'server', 'user'
            $table->string('target_id')->nullable(); // e.g. ticket id, server id, user id
            $table->text('details')->nullable(); // optional JSON or text for extra info
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_logs');
    }
}; 
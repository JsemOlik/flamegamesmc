<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('player');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // // Tickets table
        // Schema::create('tickets', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        //     $table->string('subject');
        //     $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
        //     $table->enum('category', ['technical', 'gameplay', 'player_report', 'other'])->default('technical');
        //     $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
        //     $table->timestamps();
        // });

        // // Ticket messages table
        // Schema::create('ticket_messages', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
        //     $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        //     $table->text('message');
        //     $table->timestamps();
        // });

        // // Ticket participants table
        // Schema::create('ticket_participants', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
        //     $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        //     $table->enum('role', ['owner', 'participant'])->default('participant');
        //     $table->timestamps();
        //     $table->unique(['ticket_id', 'user_id']);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        // Schema::dropIfExists('tickets');
        // Schema::dropIfExists('ticket_messages');
        // Schema::dropIfExists('ticket_participants');
    }
};

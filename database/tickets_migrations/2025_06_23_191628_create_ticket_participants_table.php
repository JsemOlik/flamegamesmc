<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop both tables if they exist to clean up
        Schema::connection('tickets')->dropIfExists('ticket_participants');
        Schema::connection('tickets')->dropIfExists('s159_ticket_participants');

        // Create the correct table without foreign key constraint
        Schema::connection('tickets')->create('ticket_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->string('username', 32);
            $table->enum('role', ['owner', 'participant'])->default('participant');
            $table->timestamps();

            // Just create an index for better performance
            $table->index('ticket_id');
            $table->unique(['ticket_id', 'username']);
        });

        // Move existing ticket owners to participants table
        $tickets = DB::connection('tickets')
            ->table('tickets')
            ->get();

        foreach ($tickets as $ticket) {
            DB::connection('tickets')
                ->table('ticket_participants')
                ->insert([
                    'ticket_id' => $ticket->id,
                    'username' => $ticket->username,
                    'role' => 'owner',
                    'created_at' => $ticket->created_at,
                    'updated_at' => $ticket->updated_at
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('tickets')->dropIfExists('ticket_participants');
    }
}; 
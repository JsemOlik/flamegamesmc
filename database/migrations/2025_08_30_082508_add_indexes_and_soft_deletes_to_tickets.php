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
        Schema::table('tickets', function (Blueprint $table) {
            $table->softDeletes();
            $table->index(['status']);
            $table->index(['priority']);
            $table->index(['user_id']);
            $table->index(['created_at']);
        });
        
        Schema::table('ticket_participants', function (Blueprint $table) {
            $table->index(['ticket_id', 'user_id']);
            $table->index(['user_id']);
        });
        
        Schema::table('ticket_messages', function (Blueprint $table) {
            $table->index(['ticket_id']);
            $table->index(['user_id']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex(['status']);
            $table->dropIndex(['priority']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
        });
        
        Schema::table('ticket_participants', function (Blueprint $table) {
            $table->dropIndex(['ticket_id', 'user_id']);
            $table->dropIndex(['user_id']);
        });
        
        Schema::table('ticket_messages', function (Blueprint $table) {
            $table->dropIndex(['ticket_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
        });
    }
};

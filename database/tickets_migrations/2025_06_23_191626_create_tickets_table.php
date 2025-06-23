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
        Schema::create('tickets', function (Blueprint $table) {
        $table->id();
        $table->string('username', 32); // Temporary, no FK since DBs are separate
        $table->string('subject');
        $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
        $table->enum('category', ['technical', 'gameplay', 'player_report', 'other'])->default('technical');
        $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

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
        Schema::create('workout_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('client_program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('program_workout_id')->constrained()->cascadeOnDelete();
            $table->datetime('completed_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('client_id');
            $table->index('client_program_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_logs');
    }
};

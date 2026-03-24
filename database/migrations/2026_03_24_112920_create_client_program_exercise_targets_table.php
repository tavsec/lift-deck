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
        Schema::create('client_program_exercise_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_program_id')->constrained('client_programs')->cascadeOnDelete();
            $table->foreignId('workout_exercise_id')->constrained('workout_exercises')->cascadeOnDelete();
            $table->decimal('target_weight', 8, 2);
            $table->timestamps();

            $table->unique(['client_program_id', 'workout_exercise_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_program_exercise_targets');
    }
};

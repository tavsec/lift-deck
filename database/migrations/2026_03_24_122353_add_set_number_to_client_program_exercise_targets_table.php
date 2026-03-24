<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_program_exercise_targets', function (Blueprint $table) {
            $table->integer('set_number')->after('workout_exercise_id')->default(1);

            $table->dropUnique(['client_program_id', 'workout_exercise_id']);
            $table->unique(['client_program_id', 'workout_exercise_id', 'set_number']);
        });
    }

    public function down(): void
    {
        Schema::table('client_program_exercise_targets', function (Blueprint $table) {
            $table->dropUnique(['client_program_id', 'workout_exercise_id', 'set_number']);
            $table->dropColumn('set_number');
            $table->unique(['client_program_id', 'workout_exercise_id']);
        });
    }
};

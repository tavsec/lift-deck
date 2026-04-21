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
            $table->index('client_program_id');
        });

        Schema::table('client_program_exercise_targets', function (Blueprint $table) {
            $table->dropUnique('cpet_program_exercise_unique');
            $table->unique(['client_program_id', 'workout_exercise_id', 'set_number'], 'cpet_program_exercise_set_unique');
        });
    }

    public function down(): void
    {
        Schema::table('client_program_exercise_targets', function (Blueprint $table) {
            $table->index('client_program_id');
        });

        Schema::table('client_program_exercise_targets', function (Blueprint $table) {
            $table->dropUnique('cpet_program_exercise_set_unique');
            $table->dropIndex(['client_program_id']);
            $table->dropColumn('set_number');
            $table->unique(['client_program_id', 'workout_exercise_id'], 'cpet_program_exercise_unique');
        });
    }
};

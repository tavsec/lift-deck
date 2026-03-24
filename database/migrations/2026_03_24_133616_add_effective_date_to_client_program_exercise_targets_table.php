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
        Schema::table('client_program_exercise_targets', function (Blueprint $table) {
            $table->date('effective_date')->nullable()->after('set_number');

            $table->dropUnique(['client_program_id', 'workout_exercise_id', 'set_number']);
            $table->unique(['client_program_id', 'workout_exercise_id', 'set_number', 'effective_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_program_exercise_targets', function (Blueprint $table) {
            $table->dropUnique(['client_program_id', 'workout_exercise_id', 'set_number', 'effective_date']);
            $table->dropColumn('effective_date');
            $table->unique(['client_program_id', 'workout_exercise_id', 'set_number']);
        });
    }
};

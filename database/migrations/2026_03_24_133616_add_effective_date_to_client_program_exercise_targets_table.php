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

            $table->dropUnique('cpet_program_exercise_set_unique');
            $table->unique(['client_program_id', 'workout_exercise_id', 'set_number', 'effective_date'], 'cpet_program_exercise_set_date_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_program_exercise_targets', function (Blueprint $table) {
            $table->dropUnique('cpet_program_exercise_set_date_unique');
            $table->dropColumn('effective_date');
            $table->unique(['client_program_id', 'workout_exercise_id', 'set_number'], 'cpet_program_exercise_set_unique');
        });
    }
};

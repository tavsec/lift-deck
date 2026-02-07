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
        Schema::table('workout_logs', function (Blueprint $table) {
            $table->foreignId('client_program_id')->nullable()->change();
            $table->foreignId('program_workout_id')->nullable()->change();
            $table->string('custom_name')->nullable()->after('program_workout_id');
        });

        Schema::table('exercise_logs', function (Blueprint $table) {
            $table->foreignId('workout_exercise_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workout_logs', function (Blueprint $table) {
            $table->dropColumn('custom_name');
            $table->foreignId('client_program_id')->nullable(false)->change();
            $table->foreignId('program_workout_id')->nullable(false)->change();
        });

        Schema::table('exercise_logs', function (Blueprint $table) {
            $table->foreignId('workout_exercise_id')->nullable(false)->change();
        });
    }
};

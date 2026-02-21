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
        Schema::create('xp_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('xp_event_type_id')->constrained('xp_event_types')->cascadeOnDelete();
            $table->integer('xp_amount');
            $table->integer('points_amount');
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['user_id', 'xp_event_type_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('xp_transactions');
    }
};

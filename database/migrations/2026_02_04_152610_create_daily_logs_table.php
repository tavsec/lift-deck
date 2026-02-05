<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tracking_metric_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->text('value');
            $table->timestamps();

            $table->unique(['client_id', 'tracking_metric_id', 'date']);
            $table->index(['client_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_logs');
    }
};

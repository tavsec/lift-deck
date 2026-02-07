<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coach_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('type'); // number, scale, boolean, text
            $table->string('unit')->nullable();
            $table->unsignedTinyInteger('scale_min')->nullable()->default(1);
            $table->unsignedTinyInteger('scale_max')->nullable()->default(5);
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('coach_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_metrics');
    }
};

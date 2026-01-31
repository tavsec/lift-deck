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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['coach', 'client'])->default('coach')->after('email');
            $table->foreignId('coach_id')->nullable()->after('role')->constrained('users')->nullOnDelete();
            $table->string('phone')->nullable();
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->string('gym_name')->nullable();
            $table->string('logo')->nullable();
            $table->string('primary_color', 7)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['coach_id']);
            $table->dropColumn([
                'role',
                'coach_id',
                'phone',
                'bio',
                'avatar',
                'gym_name',
                'logo',
                'primary_color',
            ]);
        });
    }
};

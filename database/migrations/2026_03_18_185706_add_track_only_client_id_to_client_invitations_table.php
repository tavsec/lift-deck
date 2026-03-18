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
        Schema::table('client_invitations', function (Blueprint $table) {
            $table->foreignId('track_only_client_id')->nullable()->after('coach_id')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_invitations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('track_only_client_id');
        });
    }
};

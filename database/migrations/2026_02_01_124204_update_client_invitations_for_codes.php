<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_invitations', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('token', 8)->change();
        });
    }

    public function down(): void
    {
        Schema::table('client_invitations', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
            $table->string('token', 64)->change();
        });
    }
};

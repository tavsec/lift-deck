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
            $table->string('secondary_color')->nullable()->after('primary_color');
            $table->text('description')->nullable()->after('bio');
            $table->text('welcome_email_text')->nullable()->after('description');
            $table->text('onboarding_welcome_text')->nullable()->after('welcome_email_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'secondary_color',
                'description',
                'welcome_email_text',
                'onboarding_welcome_text',
            ]);
        });
    }
};

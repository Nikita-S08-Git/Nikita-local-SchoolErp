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
        if (!Schema::hasColumn('teacher_profiles', 'notification_email')) {
            Schema::table('teacher_profiles', function (Blueprint $table) {
                $table->boolean('notification_email')->default(true)->after('linkedin_url');
                $table->boolean('notification_sms')->default(false)->after('notification_email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('teacher_profiles', 'notification_email')) {
            Schema::table('teacher_profiles', function (Blueprint $table) {
                $table->dropColumn(['notification_email', 'notification_sms']);
            });
        }
    }
};

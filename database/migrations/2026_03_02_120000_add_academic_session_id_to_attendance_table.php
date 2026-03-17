<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add academic_session_id column to attendance table
     * This column is required by App\Models\Academic\Attendance
     */
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance', 'academic_session_id')) {
                $table->foreignId('academic_session_id')
                    ->after('division_id')
                    ->nullable()
                    ->constrained('academic_sessions')
                    ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            if (Schema::hasColumn('attendance', 'academic_session_id')) {
                $table->dropForeign(['academic_session_id']);
                $table->dropColumn('academic_session_id');
            }
        });
    }
};

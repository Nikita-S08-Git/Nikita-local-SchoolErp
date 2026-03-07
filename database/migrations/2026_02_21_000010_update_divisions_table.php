<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('divisions', function (Blueprint $table) {
            $table->foreignId('program_id')->after('id')->constrained('programs');
            $table->foreignId('session_id')->after('program_id')->constrained('academic_sessions');
            $table->dropUnique(['academic_year_id', 'division_name']);
            $table->unique(['program_id', 'session_id', 'division_name']);
        });
    }

    public function down(): void
    {
        Schema::table('divisions', function (Blueprint $table) {
            $table->dropForeign(['program_id']);
            $table->dropForeign(['session_id']);
            $table->dropUnique(['program_id', 'session_id', 'division_name']);
            $table->dropColumn(['program_id', 'session_id']);
            $table->unique(['academic_year_id', 'division_name']);
        });
    }
};

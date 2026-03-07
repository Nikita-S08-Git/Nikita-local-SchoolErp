<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds the room_number column to the timetables table.
     * The Timetable model expects this column but it was missing from the database.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('timetables', 'room_number')) {
            Schema::table('timetables', function (Blueprint $table) {
                $table->string('room_number', 50)->nullable()->after('room_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            if (Schema::hasColumn('timetables', 'room_number')) {
                $table->dropColumn('room_number');
            }
        });
    }
};

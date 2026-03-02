<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add soft deletes column to timetables table
     */
    public function up(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            // Add soft deletes column
            if (!Schema::hasColumn('timetables', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            if (Schema::hasColumn('timetables', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};

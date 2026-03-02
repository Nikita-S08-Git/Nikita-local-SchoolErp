<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds start_date and end_date columns to the academic_years table
     * which are needed for ordering and display purposes.
     */
    public function up(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            if (!Schema::hasColumn('academic_years', 'start_date')) {
                $table->date('start_date')->nullable()->after('semester_end');
            }
            if (!Schema::hasColumn('academic_years', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }
            if (!Schema::hasColumn('academic_years', 'name')) {
                $table->string('name', 100)->nullable()->after('year_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            if (Schema::hasColumn('academic_years', 'start_date')) {
                $table->dropColumn('start_date');
            }
            if (Schema::hasColumn('academic_years', 'end_date')) {
                $table->dropColumn('end_date');
            }
            if (Schema::hasColumn('academic_years', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};

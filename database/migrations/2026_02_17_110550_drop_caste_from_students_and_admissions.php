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
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'caste')) {
                $table->dropColumn('caste');
            }
        });

        Schema::table('admissions', function (Blueprint $table) {
            if (Schema::hasColumn('admissions', 'caste')) {
                $table->dropColumn('caste');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (! Schema::hasColumn('students', 'caste')) {
                $table->string('caste', 50)->nullable()->after('religion');
            }
        });

        Schema::table('admissions', function (Blueprint $table) {
            if (! Schema::hasColumn('admissions', 'caste')) {
                $table->string('caste')->nullable();
            }
        });
    }
};

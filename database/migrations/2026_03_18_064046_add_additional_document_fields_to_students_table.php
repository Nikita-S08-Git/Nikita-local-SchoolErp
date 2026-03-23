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
            // Add additional document fields for student documents
            $table->string('aadhar_path')->nullable()->after('marksheet_path');
            $table->string('income_certificate_path')->nullable()->after('aadhar_path');
            $table->string('domicile_certificate_path')->nullable()->after('income_certificate_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['aadhar_path', 'income_certificate_path', 'domicile_certificate_path']);
        });
    }
};

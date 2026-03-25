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
        Schema::table('scholarship_applications', function (Blueprint $table) {
            // Add document_path if not exists
            if (!Schema::hasColumn('scholarship_applications', 'document_path')) {
                $table->string('document_path')->nullable()->after('remarks')
                      ->comment('Path to uploaded scholarship documents');
            }
            
            // Add approved_at if not exists
            if (!Schema::hasColumn('scholarship_applications', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('status')
                      ->comment('Timestamp when application was approved');
            }
            
            // Add rejection_reason if not exists
            if (!Schema::hasColumn('scholarship_applications', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('approved_at')
                      ->comment('Reason for rejection if application is rejected');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scholarship_applications', function (Blueprint $table) {
            $table->dropColumn(['document_path', 'rejection_reason']);
        });
    }
};

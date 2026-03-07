<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admissions', function (Blueprint $table) {
            // Add rejected_by column if it doesn't exist
            if (!Schema::hasColumn('admissions', 'rejected_by')) {
                $table->foreignId('rejected_by')->nullable()->constrained('users');
            }
            
            // Add rejected_at column if it doesn't exist
            if (!Schema::hasColumn('admissions', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('admissions', function (Blueprint $table) {
            $table->dropForeign(['rejected_by']);
            $table->dropColumn(['rejected_by', 'rejected_at']);
        });
    }
};

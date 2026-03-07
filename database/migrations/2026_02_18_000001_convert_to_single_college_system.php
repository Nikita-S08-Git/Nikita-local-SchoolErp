<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove college_id from tables if exists
        $tables = ['departments', 'programs', 'divisions', 'students', 'users'];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'college_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign(['college_id']);
                    $table->dropColumn('college_id');
                });
            }
        }
    }

    public function down(): void
    {
        // Rollback: Add college_id back if needed
        $tables = ['departments', 'programs', 'divisions', 'students', 'users'];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->unsignedBigInteger('college_id')->nullable()->after('id');
                });
            }
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the column doesn't exist before adding
        $columns = DB::getSchemaBuilder()->getColumnListing('permissions');
        
        if (!in_array('group_name', $columns)) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->string('group_name')->nullable()->after('name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('group_name');
        });
    }
};

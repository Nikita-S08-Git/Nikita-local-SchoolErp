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
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'semester')) {
                $table->integer('semester')->after('program_id');
            }
            if (!Schema::hasColumn('subjects', 'credit')) {
                $table->decimal('credit', 3, 1)->after('code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'semester')) {
                $table->dropColumn('semester');
            }
            if (Schema::hasColumn('subjects', 'credit')) {
                $table->dropColumn('credit');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Change type column from enum to string to allow custom examination types
     */
    public function up(): void
    {
        Schema::table('examinations', function (Blueprint $table) {
            $table->string('type', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('examinations', function (Blueprint $table) {
            $table->enum('type', ['internal', 'external', 'practical'])->change();
        });
    }
};

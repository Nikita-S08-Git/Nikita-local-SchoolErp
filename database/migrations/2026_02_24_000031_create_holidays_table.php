<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('type', ['public_holiday', 'school_holiday', 'event', 'program'])->default('public_holiday');
            $table->boolean('is_recurring')->default(false);
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->foreignId('program_incharge_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('location')->nullable();
            $table->string('attachment_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes for performance
            $table->index(['start_date', 'end_date']);
            $table->index(['type', 'is_active']);
            $table->index(['academic_year_id', 'is_active']);
            $table->index(['program_incharge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skip if table already exists (from earlier migration)
        if (Schema::hasTable('timetables')) {
            return;
        }

        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->string('period_name')->nullable(); // Period 1, Period 2, etc.
            $table->string('room_number')->nullable();
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->boolean('is_break_time')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes for performance
            $table->index(['division_id', 'day_of_week']);
            $table->index(['teacher_id', 'day_of_week']);
            $table->index(['academic_year_id', 'is_active']);
            
            // Prevent overlapping time slots for same division and day
            $table->unique(['division_id', 'day_of_week', 'start_time', 'end_time'], 'unique_division_day_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
};

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
        Schema::create('marks_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('exam_id')->constrained('examinations')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->decimal('marks', 10, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Unique constraint to prevent duplicate drafts
            $table->unique(['teacher_id', 'exam_id', 'subject_id', 'student_id'], 'marks_drafts_unique');

            // Index for faster queries
            $table->index(['teacher_id', 'exam_id']);
            $table->index(['exam_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marks_drafts');
    }
};

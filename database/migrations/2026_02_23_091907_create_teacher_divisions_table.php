<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_divisions', function (Blueprint $table) {
            $table->id();
            
            // Teacher (user_id)
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            
            // Division
            $table->foreignId('division_id')->constrained('divisions')->onDelete('cascade');
            
            // Academic Session (optional, for tracking which session this assignment is for)
            $table->foreignId('academic_session_id')->nullable()->constrained('academic_sessions')->onDelete('set null');
            
            // Is primary class teacher for this division
            $table->boolean('is_class_teacher')->default(false);
            
            // Status
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Unique constraint - teacher can't be assigned same division twice
            $table->unique(['teacher_id', 'division_id', 'academic_session_id'], 'td_teacher_div_session_unique');
            
            // Indexes
            $table->index(['teacher_id', 'is_active']);
            $table->index(['division_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_divisions');
    }
};

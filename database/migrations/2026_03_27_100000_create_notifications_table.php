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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['general', 'urgent', 'holiday', 'exam', 'fee', 'timetable', 'attendance'])->default('general');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('audience', ['all', 'students', 'teachers', 'staff', 'parents'])->default('all');
            $table->json('target_users')->nullable(); // Specific user IDs if needed
            $table->boolean('is_active')->default(true);
            $table->timestamp('publish_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['audience', 'is_active']);
            $table->index(['type', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

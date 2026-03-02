<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_profiles', function (Blueprint $table) {
            $table->id();
            
            // Link to users table
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Personal Information
            $table->string('employee_id', 50)->unique();
            $table->string('phone', 15)->nullable();
            $table->string('alternate_phone', 15)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('marital_status', 20)->default('single')->nullable();
            
            // Address
            $table->text('current_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('pincode', 10)->nullable();
            
            // Professional Information
            $table->string('qualification', 255)->nullable();
            $table->string('specialization', 255)->nullable();
            $table->integer('experience_years')->default(0);
            $table->date('joining_date')->nullable();
            $table->string('designation', 100)->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            
            // Documents
            $table->string('photo_path', 500)->nullable();
            $table->string('resume_path', 500)->nullable();
            $table->json('certificates')->nullable(); // Store certificate paths
            
            // Emergency Contact
            $table->string('emergency_contact_name', 100)->nullable();
            $table->string('emergency_contact_relation', 50)->nullable();
            $table->string('emergency_contact_phone', 15)->nullable();
            
            // Social Links
            $table->string('linkedin_url', 255)->nullable();
            $table->string('research_gate_url', 255)->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
            
            // Indexes
            $table->index(['user_id', 'is_active']);
            $table->index(['employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_profiles');
    }
};

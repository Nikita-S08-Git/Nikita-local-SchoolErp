<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            
            // Link to students table
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            
            // Parent/Guardian Information
            $table->string('father_name', 100)->nullable();
            $table->string('father_phone', 15)->nullable();
            $table->string('father_occupation', 100)->nullable();
            
            $table->string('mother_name', 100)->nullable();
            $table->string('mother_phone', 15)->nullable();
            $table->string('mother_occupation', 100)->nullable();
            
            $table->string('guardian_name', 100)->nullable();
            $table->string('guardian_phone', 15)->nullable();
            $table->string('guardian_relation', 50)->nullable();
            
            // Emergency Contact
            $table->string('emergency_contact_name', 100)->nullable();
            $table->string('emergency_contact_phone', 15)->nullable();
            $table->string('emergency_contact_relation', 50)->nullable();
            
            // Additional Student Info
            $table->string('blood_group', 5)->nullable();
            $table->string('nationality', 50)->default('Indian')->nullable();
            $table->string('mother_tongue', 50)->nullable();
            $table->string('religion', 50)->nullable();
            
            // Medical Information
            $table->text('medical_conditions')->nullable(); // JSON or text for allergies, conditions
            $table->boolean('has_medical_conditions')->default(false);
            
            // Transport
            $table->boolean('uses_transport')->default(false);
            $table->string('transport_type', 50)->nullable(); // Bus, Van, etc.
            $table->string('pickup_point', 255)->nullable();
            
            // Hostel
            $table->boolean('is_hosteler')->default(false);
            $table->string('hostel_name', 100)->nullable();
            $table->string('room_number', 20)->nullable();
            
            // Bank Details (for scholarships/refunds)
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_ifsc_code', 20)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_branch', 100)->nullable();
            
            // Documents
            $table->json('documents')->nullable(); // Additional document paths
            
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
            
            // Indexes
            $table->index(['student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};

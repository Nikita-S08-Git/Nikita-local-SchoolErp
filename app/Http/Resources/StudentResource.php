<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Student API Resource
 * 
 * This class transforms Student model data into consistent JSON responses.
 * Benefits:
 * - Consistent API response format
 * - Control over which data is exposed
 * - Easy to add computed fields
 * - Conditional field inclusion
 * - Nested resource transformation
 */
class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            
            // Identification
            'admission_number' => $this->admission_number,
            'roll_number' => $this->roll_number,
            'prn' => $this->prn,
            'university_seat_number' => $this->university_seat_number,
            
            // Personal Information
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'age' => $this->date_of_birth?->age,
            'gender' => $this->gender,
            'blood_group' => $this->blood_group,
            'religion' => $this->religion,
            // caste removed from resource output
            'category' => $this->category,
            
            // Contact Information
            'mobile_number' => $this->mobile_number,
            'email' => $this->email,
            'current_address' => $this->current_address,
            'permanent_address' => $this->permanent_address,
            
            // Academic Information
            'program' => new ProgramResource($this->whenLoaded('program')),
            'division' => new DivisionResource($this->whenLoaded('division')),
            'academic_session' => new AcademicSessionResource($this->whenLoaded('academicSession')),
            'academic_year' => $this->academic_year,
            'admission_date' => $this->admission_date?->format('Y-m-d'),
            
            // Status
            'student_status' => $this->student_status,
            'status_label' => $this->getStatusLabel(),
            
            // Documents
            'photo_url' => $this->photo_path ? asset('storage/' . $this->photo_path) : null,
            'signature_url' => $this->signature_path ? asset('storage/' . $this->signature_path) : null,
            
            // Relationships (conditionally loaded)
            'guardians' => GuardianResource::collection($this->whenLoaded('guardians')),
            'fees' => StudentFeeResource::collection($this->whenLoaded('fees')),
            
            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            
            // Additional computed fields
            'is_active' => $this->student_status === 'active',
            'has_guardians' => $this->whenLoaded('guardians', fn() => $this->guardians->isNotEmpty()),
        ];
    }

    /**
     * Get human-readable status label
     * 
     * @return string
     */
    private function getStatusLabel(): string
    {
        return match($this->student_status) {
            'active' => 'Active',
            'graduated' => 'Graduated',
            'dropped' => 'Dropped Out',
            'suspended' => 'Suspended',
            'tc_issued' => 'TC Issued',
            default => 'Unknown',
        };
    }

    /**
     * Get additional data to be included at the top level
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'success' => true,
            'timestamp' => now()->toISOString(),
        ];
    }
}

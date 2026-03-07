<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StudentProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'father_name',
        'father_phone',
        'father_occupation',
        'mother_name',
        'mother_phone',
        'mother_occupation',
        'guardian_name',
        'guardian_phone',
        'guardian_relation',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'blood_group',
        'nationality',
        'mother_tongue',
        'religion',
        'medical_conditions',
        'has_medical_conditions',
        'uses_transport',
        'transport_type',
        'pickup_point',
        'is_hosteler',
        'hostel_name',
        'room_number',
        'bank_account_number',
        'bank_ifsc_code',
        'bank_name',
        'bank_branch',
        'documents',
    ];

    protected $casts = [
        'has_medical_conditions' => 'boolean',
        'uses_transport' => 'boolean',
        'is_hosteler' => 'boolean',
        'medical_conditions' => 'array',
        'documents' => 'array',
    ];

    /**
     * Get the student that owns this profile
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User\Student::class);
    }

    /**
     * Get attendance percentage for this student
     */
    public function getAttendancePercentageAttribute(): ?float
    {
        return self::getAttendancePercentageForStudent($this->student->id, $this->student->division_id);
    }

    /**
     * Get attendance percentage for a student
     */
    public static function getAttendancePercentageForStudent(int $studentId, int $divisionId): ?float
    {
        $totalDays = \App\Models\Academic\Attendance::where('division_id', $divisionId)
            ->distinct('date')
            ->count('date');

        if ($totalDays === 0) {
            return 0;
        }

        $presentDays = \App\Models\Academic\Attendance::where('student_id', $studentId)
            ->whereIn('status', ['present', 'late'])
            ->count();

        return round(($presentDays / $totalDays) * 100, 2);
    }

    /**
     * Get parent name (father or mother)
     */
    public function getParentNameAttribute(): ?string
    {
        return $this->father_name ?? $this->mother_name ?? $this->guardian_name;
    }

    /**
     * Get parent phone
     */
    public function getParentPhoneAttribute(): ?string
    {
        return $this->father_phone ?? $this->mother_phone ?? $this->guardian_phone;
    }

    /**
     * Scope: Search by student name
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereHas('student', function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('admission_number', 'like', "%{$search}%")
                ->orWhere('roll_number', 'like', "%{$search}%");
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeacherProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'employee_id',
        'phone',
        'alternate_phone',
        'blood_group',
        'date_of_birth',
        'gender',
        'marital_status',
        'current_address',
        'permanent_address',
        'city',
        'state',
        'pincode',
        'qualification',
        'specialization',
        'experience_years',
        'joining_date',
        'designation',
        'salary',
        'photo_path',
        'resume_path',
        'certificates',
        'emergency_contact_name',
        'emergency_contact_relation',
        'emergency_contact_phone',
        'linkedin_url',
        'research_gate_url',
        'is_active',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'experience_years' => 'integer',
        'salary' => 'decimal:2',
        'has_medical_conditions' => 'boolean',
        'is_active' => 'boolean',
        'certificates' => 'array',
    ];

    /**
     * Get the user that owns the teacher profile
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get divisions assigned to this teacher
     */
    public function divisions(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Academic\Division::class,
            'teacher_divisions',
            'teacher_id',
            'division_id'
        )
            ->withPivot(['is_class_teacher', 'is_active', 'academic_session_id'])
            ->withTimestamps();
    }

    /**
     * Get active divisions assigned to this teacher
     */
    public function activeDivisions(): BelongsToMany
    {
        return $this->divisions()->wherePivot('is_active', true);
    }

    /**
     * Get subjects this teacher handles
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(\App\Models\Result\Subject::class, 'teacher_id', 'user_id');
    }

    /**
     * Get attendance records marked by this teacher
     */
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(\App\Models\Academic\Attendance::class, 'marked_by');
    }

    /**
     * Scope: Only active teachers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get full name from user
     */
    public function getFullNameAttribute(): string
    {
        return $this->user->name ?? 'N/A';
    }

    /**
     * Get formatted experience
     */
    public function getFormattedExperienceAttribute(): string
    {
        $years = $this->experience_years;
        if ($years >= 12) {
            return floor($years / 12) . ' years ' . ($years % 12) . ' months';
        }
        return $years . ' months';
    }

    /**
     * Check if teacher is class teacher of any division
     */
    public function isClassTeacher(): bool
    {
        return $this->divisions()->wherePivot('is_class_teacher', true)->exists();
    }

    /**
     * Get class teacher divisions
     */
    public function classTeacherDivisions()
    {
        return $this->divisions()->wherePivot('is_class_teacher', true);
    }

    /**
     * Get teacher assignments
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(TeacherAssignment::class, 'teacher_id', 'user_id');
    }

    /**
     * Get assigned divisions from assignments table
     */
    public function assignedDivisions()
    {
        return $this->assignments()
            ->where('assignment_type', 'division')
            ->with('division')
            ->get()
            ->pluck('division');
    }

    /**
     * Get assigned departments from assignments table
     */
    public function assignedDepartments()
    {
        return $this->assignments()
            ->where('assignment_type', 'department')
            ->with('department')
            ->get()
            ->pluck('department');
    }
}

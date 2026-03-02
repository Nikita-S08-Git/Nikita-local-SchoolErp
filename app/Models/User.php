<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne; // 👈 ADD THIS

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function reportExports()
    {
        return $this->hasMany(\App\Models\Reports\ReportExport::class);
    }

    public function reportTemplates()
    {
        return $this->hasMany(\App\Models\Reports\ReportTemplate::class, 'created_by');
    }

    // 👇 ADD THIS RELATIONSHIP
    /**
     * A user can have one student profile
     */
    public function student(): HasOne
    {
        return $this->hasOne(\App\Models\User\Student::class);
    }

    /**
     * A user can have one teacher profile
     */
    public function teacherProfile(): HasOne
    {
        return $this->hasOne(TeacherProfile::class);
    }

    /**
     * Get divisions assigned to this teacher
     */
    public function teacherDivisions()
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
     * A teacher can be assigned to one division as class teacher
     */
    public function assignedDivision(): HasOne
    {
        return $this->hasOne(\App\Models\Academic\Division::class, 'class_teacher_id');
    }
}

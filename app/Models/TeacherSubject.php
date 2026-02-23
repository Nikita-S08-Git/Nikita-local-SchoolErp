<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherSubject extends Model
{
    protected $fillable = [
        'user_id',
        'subject_id',
        'division_id',
        'academic_year',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Result\Subject::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\Division::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

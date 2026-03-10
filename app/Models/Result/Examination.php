<?php

namespace App\Models\Result;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Examination extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\ExaminationFactory::new();
    }

    protected $fillable = [
        'name', 'code', 'type', 'start_date', 'end_date', 'academic_year', 'status', 'subject_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function studentMarks(): HasMany
    {
        return $this->hasMany(StudentMark::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\Subject::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }
}
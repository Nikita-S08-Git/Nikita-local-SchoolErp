<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'holiday_id',
        'student_id',
        'teacher_id',
        'role',
        'notes',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Holiday::class, 'holiday_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}

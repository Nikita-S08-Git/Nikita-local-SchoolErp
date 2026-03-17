<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'slot_name',
        'slot_code',
        'start_time',
        'end_time',
        'slot_type',
        'sequence_order',
        'is_active',
        'is_break',
        'break_type',
        'academic_session_id',
        'is_default',
        'applicable_days',
        'assigned_room_id',
        'requires_room',
        'assigned_teacher_id',
        'available_for_classes',
        'available_for_exams',
        'description',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_break' => 'boolean',
        'is_default' => 'boolean',
        'requires_room' => 'boolean',
        'available_for_classes' => 'boolean',
        'available_for_exams' => 'boolean',
        'applicable_days' => 'array',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    /**
     * Get all timetables using this time slot
     */
    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class, 'start_time', 'start_time');
    }

    /**
     * Get the academic session this time slot belongs to
     */
    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Get the assigned room
     */
    public function assignedRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'assigned_room_id');
    }

    /**
     * Get the assigned teacher
     */
    public function assignedTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_teacher_id');
    }

    /**
     * Scope: Only active time slots
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Order by sequence and start time
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sequence_order')->orderBy('start_time');
    }

    /**
     * Scope: Only instructional slots (not breaks)
     */
    public function scopeInstructional($query)
    {
        return $query->where('is_break', false)
                     ->where('slot_type', 'instructional');
    }

    /**
     * Get duration in minutes
     */
    public function getDurationAttribute(): int
    {
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        return $start->diffInMinutes($end);
    }

    /**
     * Get formatted time range
     */
    public function getFormattedTimeRangeAttribute(): string
    {
        return substr($this->start_time, 0, 5) . ' - ' . substr($this->end_time, 0, 5);
    }

    /**
     * Check if time slot is available for scheduling
     */
    public function isAvailable(): bool
    {
        return $this->is_active && $this->available_for_classes;
    }

    /**
     * Check if time slot is a break
     */
    public function isBreak(): bool
    {
        return $this->is_break;
    }

    /**
     * Get break type label
     */
    public function getBreakTypeLabelAttribute(): ?string
    {
        if (!$this->is_break) {
            return null;
        }

        return match($this->break_type) {
            'short_break' => 'Short Break',
            'lunch' => 'Lunch Break',
            'long_break' => 'Long Break',
            default => 'Break',
        };
    }
}

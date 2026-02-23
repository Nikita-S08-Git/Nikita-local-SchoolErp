<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

/**
 * TimeSlot Model
 *
 * Represents standardized time slots for class periods, breaks, and other
 * scheduled activities. Essential for conflict-free timetable generation.
 *
 * Key Features:
 * - Standardized time slot definitions
 * - No overlapping validation
 * - Break/instructional slot differentiation
 * - Academic session mapping
 * - Availability constraints
 *
 * @package App\Models\Academic
 */
class TimeSlot extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Slot type constants
     */
    const TYPE_INSTRUCTIONAL = 'instructional';
    const TYPE_BREAK = 'break';
    const TYPE_ASSEMBLY = 'assembly';
    const TYPE_EXAM = 'exam';
    const TYPE_LAB = 'lab';
    const TYPE_TUTORIAL = 'tutorial';
    const TYPE_OTHER = 'other';

    /**
     * Break type constants
     */
    const BREAK_TYPE_SHORT = 'short_break';
    const BREAK_TYPE_LUNCH = 'lunch';
    const BREAK_TYPE_LONG = 'long_break';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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
        'assigned_teacher_id',
        'min_gap_before',
        'min_gap_after',
        'available_for_classes',
        'available_for_exams',
        'max_parallel_divisions',
        'current_utilization',
        'description',
        'notes',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sequence_order' => 'integer',
        'is_active' => 'boolean',
        'is_break' => 'boolean',
        'is_default' => 'boolean',
        'applicable_days' => 'array',
        'requires_room' => 'boolean',
        'min_gap_before' => 'integer',
        'min_gap_after' => 'integer',
        'available_for_classes' => 'boolean',
        'available_for_exams' => 'boolean',
        'max_parallel_divisions' => 'integer',
        'current_utilization' => 'integer',
    ];

    /**
     * Get the academic session this time slot applies to.
     */
    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Get the assigned room (if any).
     */
    public function assignedRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'assigned_room_id');
    }

    /**
     * Get the assigned teacher (if any).
     */
    public function assignedTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_teacher_id');
    }

    /**
     * Get the user who created this time slot.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this time slot.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the timetables using this time slot.
     */
    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }

    /**
     * Scope to get active time slots.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get instructional slots (non-break).
     */
    public function scopeInstructional($query)
    {
        return $query->where('is_break', false)
                     ->where('slot_type', self::TYPE_INSTRUCTIONAL);
    }

    /**
     * Scope to get break slots.
     */
    public function scopeBreaks($query)
    {
        return $query->where('is_break', true);
    }

    /**
     * Scope to get slots for a specific academic session.
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where(function ($q) use ($sessionId) {
            $q->where('academic_session_id', $sessionId)
              ->orWhere('is_default', true);
        });
    }

    /**
     * Scope to get slots available for classes.
     */
    public function scopeAvailableForClasses($query)
    {
        return $query->where('is_active', true)
                     ->where('available_for_classes', true)
                     ->where('is_break', false);
    }

    /**
     * Scope to get slots available for exams.
     */
    public function scopeAvailableForExams($query)
    {
        return $query->where('is_active', true)
                     ->where('available_for_exams', true)
                     ->where('is_break', false);
    }

    /**
     * Scope to get slots ordered by sequence.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sequence_order');
    }

    /**
     * Scope to get slots for a specific day.
     */
    public function scopeForDay($query, $day)
    {
        return $query->where(function ($q) use ($day) {
            $q->whereNull('applicable_days')
              ->orWhereJsonContains('applicable_days', $day);
        });
    }

    /**
     * Get the duration in minutes.
     *
     * @return int
     */
    public function getDurationMinutesAttribute(): int
    {
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        
        return $start->diffInMinutes($end);
    }

    /**
     * Get the formatted start time.
     *
     * @return string
     */
    public function getFormattedStartTimeAttribute(): string
    {
        return \Carbon\Carbon::parse($this->start_time)->format('g:i A');
    }

    /**
     * Get the formatted end time.
     *
     * @return string
     */
    public function getFormattedEndTimeAttribute(): string
    {
        return \Carbon\Carbon::parse($this->end_time)->format('g:i A');
    }

    /**
     * Get the formatted time range.
     *
     * @return string
     */
    public function getFormattedTimeRangeAttribute(): string
    {
        return "{$this->formatted_start_time} - {$this->formatted_end_time}";
    }

    /**
     * Check if this slot is a break.
     *
     * @return bool
     */
    public function isBreak(): bool
    {
        return $this->is_break;
    }

    /**
     * Check if this slot is instructional.
     *
     * @return bool
     */
    public function isInstructional(): bool
    {
        return !$this->is_break && $this->slot_type === self::TYPE_INSTRUCTIONAL;
    }

    /**
     * Check if this slot is for lab.
     *
     * @return bool
     */
    public function isLab(): bool
    {
        return $this->slot_type === self::TYPE_LAB;
    }

    /**
     * Check if this slot is for exam.
     *
     * @return bool
     */
    public function isExam(): bool
    {
        return $this->slot_type === self::TYPE_EXAM;
    }

    /**
     * Check if this slot is available on a specific day.
     *
     * @param string $day
     * @return bool
     */
    public function isAvailableOnDay(string $day): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (is_array($this->applicable_days) && !in_array($day, $this->applicable_days)) {
            return false;
        }

        return true;
    }

    /**
     * Check if this slot overlaps with another slot.
     *
     * @param TimeSlot $otherSlot
     * @return bool
     */
    public function overlapsWith(TimeSlot $otherSlot): bool
    {
        $thisStart = \Carbon\Carbon::parse($this->start_time);
        $thisEnd = \Carbon\Carbon::parse($this->end_time);
        $otherStart = \Carbon\Carbon::parse($otherSlot->start_time);
        $otherEnd = \Carbon\Carbon::parse($otherSlot->end_time);

        return $thisStart < $otherEnd && $otherStart < $thisEnd;
    }

    /**
     * Check if there's sufficient gap before this slot.
     *
     * @param TimeSlot $previousSlot
     * @return bool
     */
    public function hasSufficientGapBefore(TimeSlot $previousSlot): bool
    {
        if ($this->min_gap_before <= 0) {
            return true;
        }

        $previousEnd = \Carbon\Carbon::parse($previousSlot->end_time);
        $thisStart = \Carbon\Carbon::parse($this->start_time);

        return $previousStart->diffInMinutes($thisStart) >= $this->min_gap_before;
    }

    /**
     * Get the slot type label.
     *
     * @return string
     */
    public function getSlotTypeLabelAttribute(): string
    {
        $labels = [
            self::TYPE_INSTRUCTIONAL => 'Instructional',
            self::TYPE_BREAK => 'Break',
            self::TYPE_ASSEMBLY => 'Assembly',
            self::TYPE_EXAM => 'Exam',
            self::TYPE_LAB => 'Laboratory',
            self::TYPE_TUTORIAL => 'Tutorial',
            self::TYPE_OTHER => 'Other',
        ];

        return $labels[$this->slot_type] ?? ucfirst($this->slot_type);
    }

    /**
     * Get the break type label.
     *
     * @return string|null
     */
    public function getBreakTypeLabelAttribute(): ?string
    {
        if (!$this->is_break) {
            return null;
        }

        $labels = [
            self::BREAK_TYPE_SHORT => 'Short Break',
            self::BREAK_TYPE_LUNCH => 'Lunch Break',
            self::BREAK_TYPE_LONG => 'Long Break',
        ];

        return $labels[$this->break_type] ?? 'Break';
    }

    /**
     * Get the full slot description.
     *
     * @return string
     */
    public function getFullDescriptionAttribute(): string
    {
        $description = "{$this->slot_name} ({$this->slot_code})";
        $description .= " - {$this->formatted_time_range}";
        
        if ($this->duration_minutes > 0) {
            $description .= " ({$this->duration_minutes} min)";
        }

        return $description;
    }

    /**
     * Check if this is the default time slot.
     *
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->is_default;
    }
}

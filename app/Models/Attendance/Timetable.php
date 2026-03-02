<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Academic\Division;
use App\Models\Result\Subject;
use App\Models\User;

class Timetable extends Model
{
    use HasFactory;

    protected $fillable = [
        'division_id', 
        'subject_id', 
        'day_of_week', 
        'start_time', 
        'end_time', 
        'room', 
        'teacher_id', 
        'is_active'
    ];

    protected $casts = [
        'start_time' => 'string',
        'end_time' => 'string',
        'is_active' => 'boolean',
    ];

    /**
     * Get the division this timetable belongs to
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * Get the subject for this timetable
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the teacher for this timetable
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the room (if rooms table exists in future)
     * Currently returns a mock relation for consistency
     */
    public function room(): BelongsTo
    {
        // Placeholder for future room model
        return $this->belongsTo(User::class, 'teacher_id'); // Temporary
    }

    /**
     * Scope: Only active timetables
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter by day of week
     */
    public function scopeForDay($query, $day)
    {
        return $query->where('day_of_week', $day);
    }

    /**
     * Scope: Filter by division
     */
    public function scopeForDivision($query, $divisionId)
    {
        return $query->where('division_id', $divisionId);
    }

    /**
     * Scope: Filter by teacher
     */
    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    /**
     * Scope: Check for time conflicts
     */
    public function scopeCheckConflict($query, $divisionId, $dayOfWeek, $startTime, $endTime, $excludeId = null)
    {
        $query->where('division_id', $divisionId)
              ->where('day_of_week', $dayOfWeek)
              ->where(function($q) use ($startTime, $endTime) {
                  $q->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function($q2) use ($startTime, $endTime) {
                        $q2->where('start_time', '<=', $startTime)
                           ->where('end_time', '>=', $endTime);
                    });
              });
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query;
    }

    /**
     * Check if this timetable has a time conflict with another
     */
    public function hasConflict($excludeId = null)
    {
        return self::checkConflict(
            $this->division_id,
            $this->day_of_week,
            $this->start_time,
            $this->end_time,
            $excludeId ?? $this->id
        )->exists();
    }

    /**
     * Get formatted time range
     */
    public function getFormattedTimeRangeAttribute()
    {
        return substr($this->start_time, 0, 5) . ' - ' . substr($this->end_time, 0, 5);
    }

    /**
     * Get display information for this timetable entry
     */
    public function getDisplayInfoAttribute()
    {
        return [
            'subject' => $this->subject->name ?? 'N/A',
            'teacher' => $this->teacher->name ?? 'No Teacher',
            'room' => $this->room ?? 'TBA',
            'time' => $this->formatted_time_range,
            'day' => $this->day_of_week,
        ];
    }
}
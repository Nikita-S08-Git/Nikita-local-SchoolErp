<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User\Student;
use App\Models\User;

class Attendance extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'attendance';

    /**
     * Status constants
     */
    const STATUS_PRESENT = 'present';
    const STATUS_ABSENT = 'absent';
    const STATUS_LATE = 'late';

    protected $fillable = [
        'student_id',
        'division_id',
        'academic_session_id',
        'date',
        'status',
        'marked_by',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the student for this attendance record
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the division for this attendance record
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * Get the academic session for this attendance record
     */
    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Get the teacher who marked this attendance
     */
    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    /**
     * Scope: Filter by date
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope: Filter by division
     */
    public function scopeByDivision($query, $divisionId)
    {
        return $query->where('division_id', $divisionId);
    }

    /**
     * Scope: Filter by student
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope: Only present records
     */
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    /**
     * Scope: Only absent records
     */
    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    /**
     * Scope: Only late records
     */
    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    /**
     * Scope: Filter by teacher who marked
     */
    public function scopeMarkedBy($query, $teacherId)
    {
        return $query->where('marked_by', $teacherId);
    }

    /**
     * Check if attendance is present or late
     */
    public function isPresent(): bool
    {
        return in_array($this->status, ['present', 'late']);
    }

    /**
     * Get attendance percentage for a student in a division
     */
    public static function getPercentageForStudent(int $studentId, int $divisionId): float
    {
        $totalDays = self::where('division_id', $divisionId)
            ->distinct('date')
            ->count('date');

        if ($totalDays === 0) {
            return 0;
        }

        $presentDays = self::where('student_id', $studentId)
            ->whereIn('status', ['present', 'late'])
            ->count();

        return round(($presentDays / $totalDays) * 100, 2);
    }

    /**
     * Create or update attendance record (ensures uniqueness)
     */
    public static function updateOrCreateAttendance(array $attributes, array $values): self
    {
        return self::updateOrCreate(
            [
                'student_id' => $attributes['student_id'],
                'date' => $attributes['date'],
            ],
            array_merge($attributes, $values)
        );
    }

    /**
     * Get attendance statistics for a division
     */
    public static function getDivisionStats(int $divisionId, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = self::where('division_id', $divisionId);

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        $total = $query->count();
        $present = $query->whereIn('status', ['present', 'late'])->count();
        $absent = $query->where('status', 'absent')->count();

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'percentage' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Get attendance statistics for a student
     */
    public static function getStudentStats(int $studentId, ?int $divisionId = null, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = self::where('student_id', $studentId);

        if ($divisionId) {
            $query->where('division_id', $divisionId);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        $total = $query->count();
        $present = $query->whereIn('status', ['present', 'late'])->count();
        $absent = $query->where('status', 'absent')->count();

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'percentage' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Get attendance for a specific date and division
     */
    public static function getByDateAndDivision(string $date, int $divisionId): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('division_id', $divisionId)
            ->whereDate('date', $date)
            ->with(['student.user'])
            ->get();
    }

    /**
     * Get students without attendance for a specific date and division
     */
    public static function getStudentsWithoutAttendance(string $date, int $divisionId, array $studentIds): array
    {
        $markedStudents = self::where('division_id', $divisionId)
            ->whereDate('date', $date)
            ->pluck('student_id')
            ->toArray();

        return array_diff($studentIds, $markedStudents);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PRESENT => 'success',
            self::STATUS_ABSENT => 'danger',
            self::STATUS_LATE => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status);
    }
}

<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User\Student;
use App\Models\Academic\Program;
use App\Models\Academic\Division;
use App\Models\Academic\AcademicSession;
use App\Models\User;

/**
 * Student Academic Record Model
 *
 * Represents a student's academic record for a specific academic session.
 * This allows tracking student progression across multiple sessions while
 * maintaining historical integrity.
 *
 * Key Features:
 * - Session-wise academic tracking
 * - Result status management (PASS/FAIL/ATKT)
 * - Promotion status tracking
 * - ATKT backlog tracking
 * - Attendance summary
 * - Fee clearance status
 * - Record locking for finalized sessions
 *
 * @package App\Models\Academic
 */
class StudentAcademicRecord extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Result status constants
     */
    const STATUS_PROSPECT = 'prospect';
    const STATUS_ACTIVE = 'active';
    const STATUS_EXAM_PENDING = 'exam_pending';
    const STATUS_PASS = 'pass';
    const STATUS_ATKT = 'atkt';
    const STATUS_FAIL = 'fail';
    const STATUS_TC_ISSUED = 'tc_issued';
    const STATUS_COMPLETED = 'completed';

    /**
     * Promotion status constants
     */
    const PROMOTION_NOT_ELIGIBLE = 'not_eligible';
    const PROMOTION_ELIGIBLE = 'eligible';
    const PROMOTION_PROMOTED = 'promoted';
    const PROMOTION_CONDITIONALLY_PROMOTED = 'conditionally_promoted';
    const PROMOTION_REPEATED = 'repeated';
    const PROMOTION_TRANSFERRED = 'transferred';

    /**
     * Attendance status constants
     */
    const ATTENDANCE_ELIGIBLE = 'eligible';
    const ATTENDANCE_NOT_ELIGIBLE = 'not_eligible';
    const ATTENDANCE_CONDONABLE = 'condonable';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'academic_session_id',
        'program_id',
        'academic_year',
        'division_id',
        'result_status',
        'promotion_status',
        'backlog_count',
        'max_atkt_attempts',
        'current_atkt_attempt',
        'attendance_percentage',
        'attendance_status',
        'fee_cleared',
        'outstanding_amount',
        'is_locked',
        'locked_at',
        'locked_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'backlog_count' => 'integer',
        'max_atkt_attempts' => 'integer',
        'current_atkt_attempt' => 'integer',
        'attendance_percentage' => 'decimal:2',
        'fee_cleared' => 'boolean',
        'outstanding_amount' => 'decimal:2',
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
    ];

    /**
     * Get the student that owns this academic record.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the academic session for this record.
     */
    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Get the program for this academic record.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the division for this academic record.
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * Get the user who locked this record.
     */
    public function lockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    /**
     * Get the promotion logs for this academic record.
     */
    public function promotionLogs(): HasMany
    {
        return $this->hasMany(PromotionLog::class, 'new_academic_record_id');
    }

    /**
     * Get the backlog subjects for this record (ATKT tracking).
     * This would be implemented in Phase 5.
     */
    public function backlogSubjects(): HasMany
    {
        // Placeholder for Phase 5 - ATKT Implementation
        return $this->hasMany(BacklogSubject::class);
    }

    /**
     * Scope to get records for a specific academic session.
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('academic_session_id', $sessionId);
    }

    /**
     * Scope to get records with a specific result status.
     */
    public function scopeWithResultStatus($query, $status)
    {
        return $query->where('result_status', $status);
    }

    /**
     * Scope to get records eligible for promotion.
     */
    public function scopeEligibleForPromotion($query)
    {
        return $query->where('promotion_status', self::PROMOTION_ELIGIBLE);
    }

    /**
     * Scope to get ATKT records.
     */
    public function scopeAtkt($query)
    {
        return $query->where('result_status', self::STATUS_ATKT);
    }

    /**
     * Scope to get locked records.
     */
    public function scopeLocked($query)
    {
        return $query->where('is_locked', true);
    }

    /**
     * Scope to get unlocked records.
     */
    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', false);
    }

    /**
     * Check if this record is eligible for promotion.
     *
     * @return bool
     */
    public function isEligibleForPromotion(): bool
    {
        return $this->promotion_status === self::PROMOTION_ELIGIBLE ||
               $this->promotion_status === self::PROMOTION_CONDITIONALLY_PROMOTED;
    }

    /**
     * Check if this record has ATKT status.
     *
     * @return bool
     */
    public function hasAtkt(): bool
    {
        return $this->result_status === self::STATUS_ATKT;
    }

    /**
     * Check if this record has backlogs.
     *
     * @return bool
     */
    public function hasBacklogs(): bool
    {
        return $this->backlog_count > 0;
    }

    /**
     * Check if attendance is eligible.
     *
     * @return bool
     */
    public function hasEligibleAttendance(): bool
    {
        return $this->attendance_status === self::ATTENDANCE_ELIGIBLE;
    }

    /**
     * Check if fees are cleared.
     *
     * @return bool
     */
    public function hasFeesCleared(): bool
    {
        return $this->fee_cleared && $this->outstanding_amount == 0;
    }

    /**
     * Lock this academic record.
     *
     * @param int|null $userId
     * @return bool
     */
    public function lock(?int $userId = null): bool
    {
        $this->update([
            'is_locked' => true,
            'locked_at' => now(),
            'locked_by' => $userId ?? auth()->id(),
        ]);

        return true;
    }

    /**
     * Unlock this academic record.
     *
     * @return bool
     */
    public function unlock(): bool
    {
        $this->update([
            'is_locked' => false,
            'locked_at' => null,
            'locked_by' => null,
        ]);

        return true;
    }

    /**
     * Update result status.
     *
     * @param string $status
     * @return bool
     */
    public function updateResultStatus(string $status): bool
    {
        $validStatuses = [
            self::STATUS_PROSPECT,
            self::STATUS_ACTIVE,
            self::STATUS_EXAM_PENDING,
            self::STATUS_PASS,
            self::STATUS_ATKT,
            self::STATUS_FAIL,
            self::STATUS_TC_ISSUED,
            self::STATUS_COMPLETED,
        ];

        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException("Invalid result status: {$status}");
        }

        return $this->update(['result_status' => $status]);
    }

    /**
     * Update promotion status.
     *
     * @param string $status
     * @return bool
     */
    public function updatePromotionStatus(string $status): bool
    {
        $validStatuses = [
            self::PROMOTION_NOT_ELIGIBLE,
            self::PROMOTION_ELIGIBLE,
            self::PROMOTION_PROMOTED,
            self::PROMOTION_CONDITIONALLY_PROMOTED,
            self::PROMOTION_REPEATED,
            self::PROMOTION_TRANSFERRED,
        ];

        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException("Invalid promotion status: {$status}");
        }

        return $this->update(['promotion_status' => $status]);
    }

    /**
     * Get the full academic session name.
     *
     * @return string
     */
    public function getFullSessionNameAttribute(): string
    {
        return "{$this->academicSession->name} - {$this->academicYear}";
    }

    /**
     * Get the formatted division name.
     *
     * @return string
     */
    public function getFormattedDivisionAttribute(): string
    {
        return "{$this->division->division_name}";
    }

    /**
     * Get the complete academic identifier.
     *
     * @return string
     */
    public function getAcademicIdentifierAttribute(): string
    {
        return "{$this->program->name} - {$this->academicYear} - {$this->division->division_name}";
    }
}

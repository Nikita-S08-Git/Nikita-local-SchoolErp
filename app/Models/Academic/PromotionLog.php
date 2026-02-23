<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User\Student;
use App\Models\Academic\Program;
use App\Models\Academic\Division;
use App\Models\Academic\AcademicSession;
use App\Models\User;

/**
 * Promotion Log Model
 *
 * Tracks all student promotion events for audit and historical purposes.
 * Every promotion, demotion, or status change is logged with complete
 * details about the transition.
 *
 * Key Features:
 * - Complete before/after snapshot
 * - Eligibility tracking
 * - Override authorization
 * - Status tracking (pending/completed/cancelled/rolled_back)
 *
 * @package App\Models\Academic
 */
class PromotionLog extends Model
{
    use HasFactory;

    /**
     * Promotion type constants
     */
    const TYPE_PROMOTED = 'promoted';
    const TYPE_CONDITIONALLY_PROMOTED = 'conditionally_promoted';
    const TYPE_REPEATED = 'repeated';
    const TYPE_DEMOTED = 'demoted';
    const TYPE_TRANSFERRED = 'transferred';
    const TYPE_TC_ISSUED = 'tc_issued';

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_ROLLED_BACK = 'rolled_back';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'from_academic_session_id',
        'from_program_id',
        'from_academic_year',
        'from_division_id',
        'from_result_status',
        'to_academic_session_id',
        'to_program_id',
        'to_academic_year',
        'to_division_id',
        'to_result_status',
        'promotion_type',
        'was_eligible',
        'attendance_percentage',
        'fee_cleared',
        'backlog_count',
        'promoted_by',
        'promoted_by_role',
        'is_override',
        'override_reason',
        'override_approved_by',
        'new_academic_record_id',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'was_eligible' => 'boolean',
        'attendance_percentage' => 'decimal:2',
        'fee_cleared' => 'boolean',
        'backlog_count' => 'integer',
        'is_override' => 'boolean',
    ];

    /**
     * Get the student that was promoted.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the "from" academic session.
     */
    public function fromAcademicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class, 'from_academic_session_id');
    }

    /**
     * Get the "from" program.
     */
    public function fromProgram(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'from_program_id');
    }

    /**
     * Get the "from" division.
     */
    public function fromDivision(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'from_division_id');
    }

    /**
     * Get the "to" academic session.
     */
    public function toAcademicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class, 'to_academic_session_id');
    }

    /**
     * Get the "to" program.
     */
    public function toProgram(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'to_program_id');
    }

    /**
     * Get the "to" division.
     */
    public function toDivision(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'to_division_id');
    }

    /**
     * Get the user who performed the promotion.
     */
    public function promotedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'promoted_by');
    }

    /**
     * Get the user who approved the override (if applicable).
     */
    public function overrideApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'override_approved_by');
    }

    /**
     * Get the new academic record created (if applicable).
     */
    public function newAcademicRecord(): BelongsTo
    {
        return $this->belongsTo(StudentAcademicRecord::class, 'new_academic_record_id');
    }

    /**
     * Scope to get promotions for a specific student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope to get promotions for a specific academic session.
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('from_academic_session_id', $sessionId)
                     ->orWhere('to_academic_session_id', $sessionId);
    }

    /**
     * Scope to get promotions by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('promotion_type', $type);
    }

    /**
     * Scope to get override promotions.
     */
    public function scopeOverrides($query)
    {
        return $query->where('is_override', true);
    }

    /**
     * Scope to get completed promotions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope to get pending promotions.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Check if this promotion was an override.
     *
     * @return bool
     */
    public function isOverride(): bool
    {
        return $this->is_override;
    }

    /**
     * Check if this promotion was eligible.
     *
     * @return bool
     */
    public function wasEligible(): bool
    {
        return $this->was_eligible;
    }

    /**
     * Check if this promotion is completed.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if this promotion is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if this promotion was rolled back.
     *
     * @return bool
     */
    public function isRolledBack(): bool
    {
        return $this->status === self::STATUS_ROLLED_BACK;
    }

    /**
     * Mark this promotion as completed.
     *
     * @return bool
     */
    public function markCompleted(): bool
    {
        return $this->update(['status' => self::STATUS_COMPLETED]);
    }

    /**
     * Mark this promotion as cancelled.
     *
     * @return bool
     */
    public function markCancelled(): bool
    {
        return $this->update(['status' => self::STATUS_CANCELLED]);
    }

    /**
     * Mark this promotion as rolled back.
     *
     * @return bool
     */
    public function markRolledBack(): bool
    {
        return $this->update(['status' => self::STATUS_ROLLED_BACK]);
    }

    /**
     * Get the promotion type label.
     *
     * @return string
     */
    public function getPromotionTypeLabelAttribute(): string
    {
        $labels = [
            self::TYPE_PROMOTED => 'Promoted',
            self::TYPE_CONDITIONALLY_PROMOTED => 'Conditionally Promoted (ATKT)',
            self::TYPE_REPEATED => 'Repeated Year',
            self::TYPE_DEMOTED => 'Demoted',
            self::TYPE_TRANSFERRED => 'Transferred',
            self::TYPE_TC_ISSUED => 'Transfer Certificate Issued',
        ];

        return $labels[$this->promotion_type] ?? ucfirst($this->promotion_type);
    }

    /**
     * Get the from academic details as string.
     *
     * @return string
     */
    public function getFromDetailsAttribute(): string
    {
        return "{$this->fromAcademicSession->name} - {$this->fromProgram->name} - {$this->fromAcademicYear} - {$this->fromDivision->division_name}";
    }

    /**
     * Get the to academic details as string.
     *
     * @return string
     */
    public function getToDetailsAttribute(): string
    {
        return "{$this->toAcademicSession->name} - {$this->toProgram->name} - {$this->toAcademicYear} - {$this->toDivision->division_name}";
    }

    /**
     * Get the full promotion summary.
     *
     * @return string
     */
    public function getPromotionSummaryAttribute(): string
    {
        $summary = "{$this->student->full_name}: {$this->promotion_type_label}";
        
        if ($this->is_override) {
            $summary .= " (Override)";
        }

        return $summary;
    }
}

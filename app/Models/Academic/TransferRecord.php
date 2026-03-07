<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User\Student;
use App\Models\Academic\Program;
use App\Models\Academic\Division;
use App\Models\Academic\AcademicSession;
use App\Models\User;

/**
 * Transfer Record Model
 *
 * Tracks student transfers (leaving certificate / transfer certificate).
 * Provides complete audit trail when students leave the institution.
 *
 * Key Features:
 * - Transfer certificate tracking
 * - Reason for leaving
 * - Academic standing at transfer
 * - Destination institution tracking
 * - Fee clearance verification
 * - Override authorization
 *
 * @package App\Models\Academic
 */
class TransferRecord extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Transfer type constants
     */
    const TYPE_VOLUNTARY = 'voluntary';
    const TYPE_EXPULSION = 'expulsion';
    const TYPE_ACADEMIC_DISMISSAL = 'academic_dismissal';
    const TYPE_FINANCIAL = 'financial';
    const TYPE_MEDICAL = 'medical';
    const TYPE_FAMILY_RELOCATION = 'family_relocation';
    const TYPE_COURSE_COMPLETED = 'course_completed';
    const TYPE_OTHER = 'other';

    /**
     * Conduct rating constants
     */
    const CONDUCT_EXCELLENT = 'excellent';
    const CONDUCT_GOOD = 'good';
    const CONDUCT_FAIR = 'fair';
    const CONDUCT_POOR = 'poor';

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_ISSUED = 'issued';
    const STATUS_CANCELLED = 'cancelled';

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
        'tc_number',
        'transfer_type',
        'reason',
        'tc_issue_date',
        'last_attendance_date',
        'conduct',
        'eligible_for_readmission',
        'readmission_remarks',
        'result_status',
        'attendance_percentage',
        'fee_cleared',
        'outstanding_fees',
        'backlog_count',
        'destination_institution',
        'destination_city',
        'destination_state',
        'destination_course',
        'approved_by',
        'processed_by',
        'status',
        'tc_document_path',
        'additional_documents',
        'is_override',
        'override_reason',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tc_issue_date' => 'date',
        'last_attendance_date' => 'date',
        'eligible_for_readmission' => 'boolean',
        'attendance_percentage' => 'decimal:2',
        'fee_cleared' => 'boolean',
        'outstanding_fees' => 'decimal:2',
        'backlog_count' => 'integer',
        'additional_documents' => 'array',
        'is_override' => 'boolean',
    ];

    /**
     * Get the student that was transferred.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the academic session at time of transfer.
     */
    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Get the program at time of transfer.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the division at time of transfer.
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * Get the user who approved the transfer.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who processed the transfer.
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scope to get transfers for a specific student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope to get transfers for a specific academic session.
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('academic_session_id', $sessionId);
    }

    /**
     * Scope to get transfers by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('transfer_type', $type);
    }

    /**
     * Scope to get transfers by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get issued transfer certificates.
     */
    public function scopeIssued($query)
    {
        return $query->where('status', self::STATUS_ISSUED);
    }

    /**
     * Scope to get pending transfers.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope to get override transfers.
     */
    public function scopeOverrides($query)
    {
        return $query->where('is_override', true);
    }

    /**
     * Check if this transfer was an override.
     *
     * @return bool
     */
    public function isOverride(): bool
    {
        return $this->is_override;
    }

    /**
     * Check if student is eligible for readmission.
     *
     * @return bool
     */
    public function isEligibleForReadmission(): bool
    {
        return $this->eligible_for_readmission;
    }

    /**
     * Check if fees are cleared.
     *
     * @return bool
     */
    public function hasFeesCleared(): bool
    {
        return $this->fee_cleared && $this->outstanding_fees == 0;
    }

    /**
     * Check if transfer has backlogs.
     *
     * @return bool
     */
    public function hasBacklogs(): bool
    {
        return $this->backlog_count > 0;
    }

    /**
     * Check if transfer is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if transfer is approved.
     *
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if transfer certificate is issued.
     *
     * @return bool
     */
    public function isIssued(): bool
    {
        return $this->status === self::STATUS_ISSUED;
    }

    /**
     * Check if transfer is cancelled.
     *
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Approve this transfer.
     *
     * @param int $userId
     * @return bool
     */
    public function approve(int $userId): bool
    {
        return $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $userId,
        ]);
    }

    /**
     * Mark transfer certificate as issued.
     *
     * @return bool
     */
    public function markIssued(): bool
    {
        return $this->update(['status' => self::STATUS_ISSUED]);
    }

    /**
     * Cancel this transfer.
     *
     * @return bool
     */
    public function cancel(): bool
    {
        return $this->update(['status' => self::STATUS_CANCELLED]);
    }

    /**
     * Get the transfer type label.
     *
     * @return string
     */
    public function getTransferTypeLabelAttribute(): string
    {
        $labels = [
            self::TYPE_VOLUNTARY => 'Voluntary Transfer',
            self::TYPE_EXPULSION => 'Expulsion',
            self::TYPE_ACADEMIC_DISMISSAL => 'Academic Dismissal',
            self::TYPE_FINANCIAL => 'Financial Reasons',
            self::TYPE_MEDICAL => 'Medical Reasons',
            self::TYPE_FAMILY_RELOCATION => 'Family Relocation',
            self::TYPE_COURSE_COMPLETED => 'Course Completed',
            self::TYPE_OTHER => 'Other',
        ];

        return $labels[$this->transfer_type] ?? ucfirst(str_replace('_', ' ', $this->transfer_type));
    }

    /**
     * Get the conduct label.
     *
     * @return string
     */
    public function getConductLabelAttribute(): string
    {
        $labels = [
            self::CONDUCT_EXCELLENT => 'Excellent',
            self::CONDUCT_GOOD => 'Good',
            self::CONDUCT_FAIR => 'Fair',
            self::CONDUCT_POOR => 'Poor',
        ];

        return $labels[$this->conduct] ?? ucfirst($this->conduct);
    }

    /**
     * Get the full student academic details at transfer.
     *
     * @return string
     */
    public function getStudentAcademicDetailsAttribute(): string
    {
        return "{$this->student->full_name} - {$this->program->name} {$this->academic_year} ({$this->division->division_name})";
    }

    /**
     * Get the complete destination details.
     *
     * @return string|null
     */
    public function getDestinationDetailsAttribute(): ?string
    {
        if (!$this->destination_institution) {
            return null;
        }

        $parts = [$this->destination_institution];
        
        if ($this->destination_city) {
            $parts[] = $this->destination_city;
        }
        
        if ($this->destination_state) {
            $parts[] = $this->destination_state;
        }

        return implode(', ', $parts);
    }

    /**
     * Generate a unique TC number.
     *
     * @param string $prefix
     * @return string
     */
    public static function generateTcNumber(string $prefix = 'TC'): string
    {
        $year = date('Y');
        $random = strtoupper(substr(uniqid(), -6));
        
        return "{$prefix}/{$year}/{$random}";
    }
}

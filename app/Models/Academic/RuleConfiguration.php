<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

/**
 * Rule Configuration Model
 *
 * Stores institution-specific configuration values for academic rules.
 * Allows different academic sessions, programs, or departments to have
 * different rule values.
 *
 * Key Features:
 * - Context-specific rule values (by session, program, department)
 * - Override tracking with approval
 * - Effective date range
 * - Inheritance from default rule values
 *
 * @package App\Models\Academic
 */
class RuleConfiguration extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'academic_rule_id',
        'academic_session_id',
        'program_id',
        'department_id',
        'value',
        'is_override',
        'override_reason',
        'override_approved_by',
        'override_approved_at',
        'effective_from',
        'effective_to',
        'is_active',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_override' => 'boolean',
        'is_active' => 'boolean',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'override_approved_at' => 'datetime',
    ];

    /**
     * Get the academic rule this configuration belongs to.
     */
    public function academicRule(): BelongsTo
    {
        return $this->belongsTo(AcademicRule::class, 'academic_rule_id');
    }

    /**
     * Get the academic session this configuration applies to.
     */
    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Get the program this configuration applies to.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the department this configuration applies to.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the user who created this configuration.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this configuration.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who approved the override.
     */
    public function overrideApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'override_approved_by');
    }

    /**
     * Scope to get active configurations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get configurations for a specific rule.
     */
    public function scopeForRule($query, $ruleId)
    {
        return $query->where('academic_rule_id', $ruleId);
    }

    /**
     * Scope to get configurations for a specific academic session.
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('academic_session_id', $sessionId);
    }

    /**
     * Scope to get configurations for a specific program.
     */
    public function scopeForProgram($query, $programId)
    {
        return $query->where('program_id', $programId);
    }

    /**
     * Scope to get configurations for a specific department.
     */
    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope to get override configurations.
     */
    public function scopeOverrides($query)
    {
        return $query->where('is_override', true);
    }

    /**
     * Scope to get configurations effective on a given date.
     */
    public function scopeEffectiveOn($query, $date = null)
    {
        $date = $date ?? now();
        
        return $query->where(function ($q) use ($date) {
            $q->whereNull('effective_from')
              ->orWhere('effective_from', '<=', $date);
        })->where(function ($q) use ($date) {
            $q->whereNull('effective_to')
              ->orWhere('effective_to', '>=', $date);
        });
    }

    /**
     * Get the typed value of this configuration.
     *
     * @return mixed
     */
    public function getTypedValueAttribute()
    {
        $rule = $this->academicRule;
        
        if (!$rule) {
            return $this->value;
        }

        return match ($rule->value_type) {
            AcademicRule::VALUE_TYPE_BOOLEAN => (bool) $this->value,
            AcademicRule::VALUE_TYPE_INTEGER => (int) $this->value,
            AcademicRule::VALUE_TYPE_DECIMAL => (float) $this->value,
            AcademicRule::VALUE_TYPE_JSON => json_decode($this->value, true),
            AcademicRule::VALUE_TYPE_ARRAY => json_decode($this->value, true),
            default => $this->value,
        };
    }

    /**
     * Check if this configuration is currently effective.
     *
     * @return bool
     */
    public function isCurrentlyEffective(): bool
    {
        $now = now();
        
        if ($this->effective_from && $this->effective_from > $now) {
            return false;
        }
        
        if ($this->effective_to && $this->effective_to < $now) {
            return false;
        }
        
        return true;
    }

    /**
     * Check if this configuration is active and effective.
     *
     * @return bool
     */
    public function isActiveAndEffective(): bool
    {
        return $this->is_active && $this->isCurrentlyEffective();
    }

    /**
     * Get the context description (which session/program/department this applies to).
     *
     * @return string
     */
    public function getContextDescriptionAttribute(): string
    {
        $parts = [];
        
        if ($this->academicSession) {
            $parts[] = "Session: {$this->academicSession->name}";
        } else {
            $parts[] = "All Sessions";
        }
        
        if ($this->program) {
            $parts[] = "Program: {$this->program->name}";
        }
        
        if ($this->department) {
            $parts[] = "Department: {$this->department->name}";
        }
        
        return implode(' / ', $parts);
    }

    /**
     * Get the effective value (this value or default from rule).
     *
     * @return mixed
     */
    public function getEffectiveValueAttribute()
    {
        if ($this->value !== null && $this->value !== '') {
            return $this->typed_value;
        }
        
        return $this->academicRule?->typed_default_value;
    }
}

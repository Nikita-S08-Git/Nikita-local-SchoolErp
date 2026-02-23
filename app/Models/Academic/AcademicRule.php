<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

/**
 * Academic Rule Model
 *
 * Represents a configurable academic rule such as passing percentage,
 * ATKT maximum subjects, attendance requirements, etc.
 *
 * Key Features:
 * - Rule categorization (result, attendance, promotion, atkt, etc.)
 * - Value type validation (boolean, integer, decimal, string, json)
 * - Effective date range
 * - Override support with approval
 * - Priority-based evaluation
 *
 * @package App\Models\Academic
 */
class AcademicRule extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Rule category constants
     */
    const CATEGORY_RESULT = 'result';
    const CATEGORY_ATTENDANCE = 'attendance';
    const CATEGORY_PROMOTION = 'promotion';
    const CATEGORY_FEE = 'fee';
    const CATEGORY_ATKT = 'atkt';
    const CATEGORY_EXAMINATION = 'examination';
    const CATEGORY_GENERAL = 'general';

    /**
     * Value type constants
     */
    const VALUE_TYPE_BOOLEAN = 'boolean';
    const VALUE_TYPE_INTEGER = 'integer';
    const VALUE_TYPE_DECIMAL = 'decimal';
    const VALUE_TYPE_STRING = 'string';
    const VALUE_TYPE_JSON = 'json';
    const VALUE_TYPE_ARRAY = 'array';

    /**
     * Predefined rule codes
     */
    const RULE_PASS_PERCENTAGE = 'PASS_PERCENTAGE';
    const RULE_MIN_ATTENDANCE = 'MIN_ATTENDANCE';
    const RULE_ATTENDANCE_GRACE = 'ATTENDANCE_GRACE';
    const RULE_ATKT_MAX_SUBJECTS = 'ATKT_MAX_SUBJECTS';
    const RULE_ATKT_MAX_ATTEMPTS = 'ATKT_MAX_ATTEMPTS';
    const RULE_GRACE_MARKS = 'GRACE_MARKS';
    const RULE_FEE_CLEARANCE_REQUIRED = 'FEE_CLEARANCE_REQUIRED';
    const RULE_COMPLUSORY_SUBJECTS = 'COMPULSORY_SUBJECTS';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'rule_code',
        'name',
        'description',
        'category',
        'value_type',
        'value',
        'default_value',
        'min_value',
        'max_value',
        'allowed_values',
        'validation_pattern',
        'effective_from',
        'effective_to',
        'is_active',
        'is_mandatory',
        'is_institution_specific',
        'priority',
        'display_order',
        'parent_rule_id',
        'metadata',
        'tags',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'allowed_values' => 'array',
        'metadata' => 'array',
        'tags' => 'array',
        'is_active' => 'boolean',
        'is_mandatory' => 'boolean',
        'is_institution_specific' => 'boolean',
        'priority' => 'integer',
        'display_order' => 'integer',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the user who created this rule.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this rule.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who approved this rule.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the parent rule (if this rule depends on another).
     */
    public function parentRule(): BelongsTo
    {
        return $this->belongsTo(AcademicRule::class, 'parent_rule_id');
    }

    /**
     * Get child rules that depend on this rule.
     */
    public function childRules(): HasMany
    {
        return $this->hasMany(AcademicRule::class, 'parent_rule_id');
    }

    /**
     * Get rule configurations for this rule.
     */
    public function configurations(): HasMany
    {
        return $this->hasMany(RuleConfiguration::class, 'academic_rule_id');
    }

    /**
     * Scope to get active rules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get rules by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to get rules effective on a given date.
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
     * Scope to get rules by code.
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('rule_code', $code);
    }

    /**
     * Scope to get mandatory rules.
     */
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    /**
     * Scope to get rules ordered by priority.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('priority')->orderBy('display_order');
    }

    /**
     * Get the typed value of the rule.
     *
     * @return mixed
     */
    public function getTypedValueAttribute()
    {
        return $this->castValue($this->value);
    }

    /**
     * Get the typed default value.
     *
     * @return mixed
     */
    public function getTypedDefaultValueAttribute()
    {
        return $this->default_value ? $this->castValue($this->default_value) : null;
    }

    /**
     * Cast value based on value_type.
     *
     * @param string $value
     * @return mixed
     */
    protected function castValue(string $value)
    {
        return match ($this->value_type) {
            self::VALUE_TYPE_BOOLEAN => (bool) $value,
            self::VALUE_TYPE_INTEGER => (int) $value,
            self::VALUE_TYPE_DECIMAL => (float) $value,
            self::VALUE_TYPE_JSON => json_decode($value, true),
            self::VALUE_TYPE_ARRAY => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Check if rule is currently effective.
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
     * Check if rule is active and effective.
     *
     * @return bool
     */
    public function isActiveAndEffective(): bool
    {
        return $this->is_active && $this->isCurrentlyEffective();
    }

    /**
     * Validate a value against this rule's constraints.
     *
     * @param mixed $value
     * @return array
     */
    public function validateValue($value): array
    {
        $result = [
            'valid' => true,
            'errors' => [],
        ];

        // Check allowed values
        if (is_array($this->allowed_values)) {
            $typedValue = $this->castValue($value);
            if (!in_array($typedValue, $this->allowed_values, true)) {
                $result['valid'] = false;
                $result['errors'][] = "Value must be one of: " . implode(', ', $this->allowed_values);
            }
        }

        // Check min/max for numeric values
        if (in_array($this->value_type, [self::VALUE_TYPE_INTEGER, self::VALUE_TYPE_DECIMAL])) {
            $numericValue = (float) $value;
            
            if ($this->min_value !== null && $numericValue < (float) $this->min_value) {
                $result['valid'] = false;
                $result['errors'][] = "Value must be at least {$this->min_value}";
            }
            
            if ($this->max_value !== null && $numericValue > (float) $this->max_value) {
                $result['valid'] = false;
                $result['errors'][] = "Value must be at most {$this->max_value}";
            }
        }

        // Check validation pattern for strings
        if ($this->value_type === self::VALUE_TYPE_STRING && $this->validation_pattern) {
            if (!preg_match($this->validation_pattern, $value)) {
                $result['valid'] = false;
                $result['errors'][] = "Value does not match required pattern";
            }
        }

        return $result;
    }

    /**
     * Get the category label.
     *
     * @return string
     */
    public function getCategoryLabelAttribute(): string
    {
        $labels = [
            self::CATEGORY_RESULT => 'Result',
            self::CATEGORY_ATTENDANCE => 'Attendance',
            self::CATEGORY_PROMOTION => 'Promotion',
            self::CATEGORY_FEE => 'Fee',
            self::CATEGORY_ATKT => 'ATKT',
            self::CATEGORY_EXAMINATION => 'Examination',
            self::CATEGORY_GENERAL => 'General',
        ];

        return $labels[$this->category] ?? ucfirst($this->category);
    }

    /**
     * Get the value type label.
     *
     * @return string
     */
    public function getValueTypeLabelAttribute(): string
    {
        $labels = [
            self::VALUE_TYPE_BOOLEAN => 'Yes/No',
            self::VALUE_TYPE_INTEGER => 'Number',
            self::VALUE_TYPE_DECIMAL => 'Decimal',
            self::VALUE_TYPE_STRING => 'Text',
            self::VALUE_TYPE_JSON => 'JSON',
            self::VALUE_TYPE_ARRAY => 'List',
        ];

        return $labels[$this->value_type] ?? ucfirst($this->value_type);
    }

    /**
     * Get the formatted value for display.
     *
     * @return string
     */
    public function getFormattedValueAttribute(): string
    {
        return match ($this->value_type) {
            self::VALUE_TYPE_BOOLEAN => $this->typed_value ? 'Yes' : 'No',
            self::VALUE_TYPE_DECIMAL => number_format((float) $this->value, 2),
            self::VALUE_TYPE_JSON => json_encode($this->typed_value),
            self::VALUE_TYPE_ARRAY => implode(', ', $this->typed_value),
            default => (string) $this->value,
        };
    }
}

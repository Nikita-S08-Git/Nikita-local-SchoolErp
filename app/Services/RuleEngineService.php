<?php

namespace App\Services;

use App\Models\Academic\AcademicRule;
use App\Models\Academic\RuleConfiguration;
use App\Models\Academic\AcademicSession;
use App\Models\Academic\Program;
use App\Models\Academic\Department;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Rule Engine Service
 *
 * Central service for evaluating academic rules.
 * Provides configurable rule evaluation for pass/fail/ATKT decisions.
 *
 * Key Features:
 * - Rule retrieval with context awareness
 * - Rule value caching
 * - Rule evaluation pipeline
 * - Override support
 *
 * @package App\Services
 */
class RuleEngineService
{
    /**
     * Cache key prefix
     */
    const CACHE_PREFIX = 'academic_rules.';

    /**
     * Cache TTL in seconds (1 hour)
     */
    const CACHE_TTL = 3600;

    /**
     * Get a rule by code.
     *
     * @param string $ruleCode
     * @param int|null $sessionId
     * @param int|null $programId
     * @param int|null $departmentId
     * @return AcademicRule|null
     */
    public function getRule(
        string $ruleCode,
        ?int $sessionId = null,
        ?int $programId = null,
        ?int $departmentId = null
    ): ?AcademicRule {
        $cacheKey = $this->getCacheKey($ruleCode, $sessionId, $programId, $departmentId);
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use (
            $ruleCode,
            $sessionId,
            $programId,
            $departmentId
        ) {
            $rule = AcademicRule::byCode($ruleCode)
                ->active()
                ->effectiveOn()
                ->first();

            return $rule;
        });
    }

    /**
     * Get rule value with context awareness.
     *
     * @param string $ruleCode
     * @param int|null $sessionId
     * @param int|null $programId
     * @param int|null $departmentId
     * @return mixed
     */
    public function getRuleValue(
        string $ruleCode,
        ?int $sessionId = null,
        ?int $programId = null,
        ?int $departmentId = null
    ) {
        $rule = $this->getRule($ruleCode, $sessionId, $programId, $departmentId);
        
        if (!$rule) {
            return null;
        }

        // Check for context-specific configuration
        $config = $this->getConfiguration($rule->id, $sessionId, $programId, $departmentId);
        
        if ($config && $config->isActiveAndEffective()) {
            return $config->effective_value;
        }

        return $rule->typed_value;
    }

    /**
     * Get rule value as boolean.
     *
     * @param string $ruleCode
     * @param mixed $default
     * @param int|null $sessionId
     * @param int|null $programId
     * @param int|null $departmentId
     * @return bool
     */
    public function getBoolean(
        string $ruleCode,
        bool $default = false,
        ?int $sessionId = null,
        ?int $programId = null,
        ?int $departmentId = null
    ): bool {
        $value = $this->getRuleValue($ruleCode, $sessionId, $programId, $departmentId);
        return $value !== null ? (bool) $value : $default;
    }

    /**
     * Get rule value as integer.
     *
     * @param string $ruleCode
     * @param int $default
     * @param int|null $sessionId
     * @param int|null $programId
     * @param int|null $departmentId
     * @return int
     */
    public function getInteger(
        string $ruleCode,
        int $default = 0,
        ?int $sessionId = null,
        ?int $programId = null,
        ?int $departmentId = null
    ): int {
        $value = $this->getRuleValue($ruleCode, $sessionId, $programId, $departmentId);
        return $value !== null ? (int) $value : $default;
    }

    /**
     * Get rule value as float/decimal.
     *
     * @param string $ruleCode
     * @param float $default
     * @param int|null $sessionId
     * @param int|null $programId
     * @param int|null $departmentId
     * @return float
     */
    public function getDecimal(
        string $ruleCode,
        float $default = 0.0,
        ?int $sessionId = null,
        ?int $programId = null,
        ?int $departmentId = null
    ): float {
        $value = $this->getRuleValue($ruleCode, $sessionId, $programId, $departmentId);
        return $value !== null ? (float) $value : $default;
    }

    /**
     * Get rule value as string.
     *
     * @param string $ruleCode
     * @param string $default
     * @param int|null $sessionId
     * @param int|null $programId
     * @param int|null $departmentId
     * @return string
     */
    public function getString(
        string $ruleCode,
        string $default = '',
        ?int $sessionId = null,
        ?int $programId = null,
        ?int $departmentId = null
    ): string {
        $value = $this->getRuleValue($ruleCode, $sessionId, $programId, $departmentId);
        return $value !== null ? (string) $value : $default;
    }

    /**
     * Get rule value as array.
     *
     * @param string $ruleCode
     * @param array $default
     * @param int|null $sessionId
     * @param int|null $programId
     * @param int|null $departmentId
     * @return array
     */
    public function getArray(
        string $ruleCode,
        array $default = [],
        ?int $sessionId = null,
        ?int $programId = null,
        ?int $departmentId = null
    ): array {
        $value = $this->getRuleValue($ruleCode, $sessionId, $programId, $departmentId);
        return is_array($value) ? $value : $default;
    }

    /**
     * Get all rules for a category.
     *
     * @param string $category
     * @param int|null $sessionId
     * @param int|null $programId
     * @param int|null $departmentId
     * @return array
     */
    public function getRulesByCategory(
        string $category,
        ?int $sessionId = null,
        ?int $programId = null,
        ?int $departmentId = null
    ): array {
        $rules = AcademicRule::byCategory($category)
            ->active()
            ->effectiveOn()
            ->ordered()
            ->get();

        $result = [];
        
        foreach ($rules as $rule) {
            $config = $this->getConfiguration($rule->id, $sessionId, $programId, $departmentId);
            
            $result[$rule->rule_code] = [
                'rule' => $rule,
                'value' => $config?->effective_value ?? $rule->typed_value,
                'configuration' => $config,
            ];
        }

        return $result;
    }

    /**
     * Get configuration for a rule in a specific context.
     *
     * @param int $ruleId
     * @param int|null $sessionId
     * @param int|null $programId
     * @param int|null $departmentId
     * @return RuleConfiguration|null
     */
    public function getConfiguration(
        int $ruleId,
        ?int $sessionId = null,
        ?int $programId = null,
        ?int $departmentId = null
    ): ?RuleConfiguration {
        $query = RuleConfiguration::forRule($ruleId)
            ->active()
            ->effectiveOn();

        // Try to find most specific configuration first
        $config = (clone $query)
            ->where('academic_session_id', $sessionId)
            ->where('program_id', $programId)
            ->where('department_id', $departmentId)
            ->first();

        if ($config) {
            return $config;
        }

        // Try session-level configuration
        $config = (clone $query)
            ->where('academic_session_id', $sessionId)
            ->whereNull('program_id')
            ->whereNull('department_id')
            ->first();

        if ($config) {
            return $config;
        }

        // Try global configuration (no context)
        return $query
            ->whereNull('academic_session_id')
            ->whereNull('program_id')
            ->whereNull('department_id')
            ->first();
    }

    /**
     * Set or update a rule configuration.
     *
     * @param string $ruleCode
     * @param mixed $value
     * @param int|null $sessionId
     * @param int|null $programId
     * @param int|null $departmentId
     * @param bool $isOverride
     * @param string|null $overrideReason
     * @param int|null $approvedBy
     * @return RuleConfiguration
     */
    public function setConfiguration(
        string $ruleCode,
        $value,
        ?int $sessionId = null,
        ?int $programId = null,
        ?int $departmentId = null,
        bool $isOverride = false,
        ?string $overrideReason = null,
        ?int $approvedBy = null
    ): RuleConfiguration {
        $rule = $this->getRule($ruleCode);
        
        if (!$rule) {
            throw new \Exception("Rule not found: {$ruleCode}");
        }

        // Validate value
        $validation = $rule->validateValue($value);
        
        if (!$validation['valid']) {
            throw new \Exception(
                "Invalid rule value: " . implode(', ', $validation['errors'])
            );
        }

        $config = RuleConfiguration::updateOrCreate(
            [
                'academic_rule_id' => $rule->id,
                'academic_session_id' => $sessionId,
                'program_id' => $programId,
                'department_id' => $departmentId,
            ],
            [
                'value' => $this->serializeValue($value, $rule->value_type),
                'is_override' => $isOverride,
                'override_reason' => $overrideReason,
                'override_approved_by' => $approvedBy,
                'override_approved_at' => $approvedBy ? now() : null,
            ]
        );

        // Clear cache
        $this->clearCache($ruleCode, $sessionId, $programId, $departmentId);

        Log::info('Rule configuration updated', [
            'rule_code' => $ruleCode,
            'value' => $value,
            'session_id' => $sessionId,
            'program_id' => $programId,
            'is_override' => $isOverride,
        ]);

        return $config;
    }

    /**
     * Clear cached rule.
     *
     * @param string $ruleCode
     * @param int|null $sessionId
     * @param int|null $programId
     * @param int|null $departmentId
     * @return void
     */
    public function clearCache(
        string $ruleCode,
        ?int $sessionId = null,
        ?int $programId = null,
        ?int $departmentId = null
    ): void {
        $cacheKey = $this->getCacheKey($ruleCode, $sessionId, $programId, $departmentId);
        Cache::forget($cacheKey);
    }

    /**
     * Clear all rule cache.
     *
     * @return void
     */
    public function clearAllCache(): void
    {
        Cache::tags([self::CACHE_PREFIX])->flush();
    }

    /**
     * Get cache key for a rule.
     *
     * @param string $ruleCode
     * @param int|null $sessionId
     * @param int|null $programId
     * @param int|null $departmentId
     * @return string
     */
    protected function getCacheKey(
        string $ruleCode,
        ?int $sessionId = null,
        ?int $programId = null,
        ?int $departmentId = null
    ): string {
        $parts = [self::CACHE_PREFIX, $ruleCode];
        
        if ($sessionId) {
            $parts[] = "s{$sessionId}";
        }
        
        if ($programId) {
            $parts[] = "p{$programId}";
        }
        
        if ($departmentId) {
            $parts[] = "d{$departmentId}";
        }

        return implode('.', $parts);
    }

    /**
     * Serialize value for storage.
     *
     * @param mixed $value
     * @param string $valueType
     * @return string
     */
    protected function serializeValue($value, string $valueType): string
    {
        return match ($valueType) {
            AcademicRule::VALUE_TYPE_BOOLEAN => $value ? '1' : '0',
            AcademicRule::VALUE_TYPE_JSON => json_encode($value),
            AcademicRule::VALUE_TYPE_ARRAY => json_encode($value),
            default => (string) $value,
        };
    }

    /**
     * Get common academic rules.
     *
     * @return array
     */
    public function getCommonRules(): array
    {
        return [
            'pass_percentage' => $this->getDecimal(AcademicRule::RULE_PASS_PERCENTAGE, 40.0),
            'min_attendance' => $this->getInteger(AcademicRule::RULE_MIN_ATTENDANCE, 75),
            'attendance_grace' => $this->getInteger(AcademicRule::RULE_ATTENDANCE_GRACE, 5),
            'atkt_max_subjects' => $this->getInteger(AcademicRule::RULE_ATKT_MAX_SUBJECTS, 3),
            'atkt_max_attempts' => $this->getInteger(AcademicRule::RULE_ATKT_MAX_ATTEMPTS, 3),
            'grace_marks' => $this->getInteger(AcademicRule::RULE_GRACE_MARKS, 5),
            'fee_clearance_required' => $this->getBoolean(AcademicRule::RULE_FEE_CLEARANCE_REQUIRED, false),
        ];
    }
}

<?php

namespace App\Services;

use App\Models\Academic\AcademicRule;
use Illuminate\Support\Facades\Cache;

/**
 * Academic Rule Service
 * 
 * Provides static methods to retrieve academic rules dynamically.
 * This service replaces hardcoded values with configurable rules
 * from the database, allowing administrators to change values
 * without modifying code.
 */
class AcademicRuleService
{
    /**
     * Cache duration in minutes
     */
    const CACHE_TTL = 60;

    /**
     * Get a rule value by its key
     * 
     * @param string $key The rule code (e.g., 'PASS_PERCENTAGE')
     * @param mixed $default Default value if rule not found
     * @param string|null $category Optional category filter
     * @return mixed
     */
    public static function get(string $key, mixed $default = null, ?string $category = null): mixed
    {
        $cacheKey = "academic_rule_{$key}_{$category}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL * 60, function () use ($key, $default, $category) {
            $query = AcademicRule::active()
                ->byCode($key);
            
            if ($category) {
                $query->byCategory($category);
            }
            
            $rule = $query->first();
            
            if (!$rule) {
                return $default;
            }
            
            return $rule->typed_value ?? $default;
        });
    }

    /**
     * Get pass percentage with default fallback
     * 
     * @param int $default Default pass percentage (default: 40)
     * @return int
     */
    public static function getPassPercentage(int $default = 40): int
    {
        return self::get(AcademicRule::RULE_PASS_PERCENTAGE, $default, AcademicRule::CATEGORY_RESULT);
    }

    /**
     * Get minimum attendance requirement
     * 
     * @param int $default Default minimum attendance (default: 75)
     * @return int
     */
    public static function getMinAttendance(int $default = 75): int
    {
        return self::get(AcademicRule::RULE_MIN_ATTENDANCE, $default, AcademicRule::CATEGORY_ATTENDANCE);
    }

    /**
     * Get attendance grace period
     * 
     * @param int $default Default grace period (default: 0)
     * @return int
     */
    public static function getAttendanceGrace(int $default = 0): int
    {
        return self::get(AcademicRule::RULE_ATTENDANCE_GRACE, $default, AcademicRule::CATEGORY_ATTENDANCE);
    }

    /**
     * Get maximum ATKT subjects allowed
     * 
     * @param int $default Default max ATKT (default: 2)
     * @return int
     */
    public static function getAtktMaxSubjects(int $default = 2): int
    {
        return self::get(AcademicRule::RULE_ATKT_MAX_SUBJECTS, $default, AcademicRule::CATEGORY_ATKT);
    }

    /**
     * Get maximum ATKT attempts allowed
     * 
     * @param int $default Default max attempts (default: 3)
     * @return int
     */
    public static function getAtktMaxAttempts(int $default = 3): int
    {
        return self::get(AcademicRule::RULE_ATKT_MAX_ATTEMPTS, $default, AcademicRule::CATEGORY_ATKT);
    }

    /**
     * Get grace marks configuration
     * 
     * @param bool $default Default grace marks allowed (default: false)
     * @return bool
     */
    public static function getGraceMarks(bool $default = false): bool
    {
        return self::get(AcademicRule::RULE_GRACE_MARKS, $default, AcademicRule::CATEGORY_RESULT);
    }

    /**
     * Check if fee clearance is required for promotion
     * 
     * @param bool $default Default value (default: true)
     * @return bool
     */
    public static function getFeeClearanceRequired(bool $default = true): bool
    {
        return self::get(AcademicRule::RULE_FEE_CLEARANCE_REQUIRED, $default, AcademicRule::CATEGORY_PROMOTION);
    }

    /**
     * Clear cache for a specific rule
     * 
     * @param string $key The rule code
     * @param string|null $category Optional category
     * @return void
     */
    public static function clearCache(string $key, ?string $category = null): void
    {
        $cacheKey = "academic_rule_{$key}_{$category}";
        Cache::forget($cacheKey);
    }

    /**
     * Clear all academic rule caches
     * 
     * @return void
     */
    public static function clearAllCache(): void
    {
        Cache::flush();
    }

    /**
     * Check if a student has passed based on percentage
     * 
     * @param float $percentage The obtained percentage
     * @return bool
     */
    public static function hasPassed(float $percentage): bool
    {
        $passPercentage = self::getPassPercentage();
        return $percentage >= $passPercentage;
    }

    /**
     * Check if a student has met minimum attendance
     * 
     * @param float $attendancePercentage The obtained attendance percentage
     * @return bool
     */
    public static function hasMetAttendanceRequirement(float $attendancePercentage): bool
    {
        $minAttendance = self::getMinAttendance();
        return $attendancePercentage >= $minAttendance;
    }
}

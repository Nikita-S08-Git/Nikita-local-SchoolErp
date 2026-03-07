<?php

namespace App\Services;

use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Holiday Service
 * 
 * Handles all holiday-related operations including:
 * - Checking if a date is a holiday
 * - Getting holiday details for a date
 * - Validating attendance and timetable operations
 * - Cache management for performance
 * 
 * @package App\Services
 */
class HolidayService
{
    /**
     * Cache key prefix
     */
    private const CACHE_PREFIX = 'holidays.';

    /**
     * Cache TTL in minutes (24 hours)
     */
    private const CACHE_TTL = 1440;

    /**
     * Check if a given date is a holiday
     * 
     * @param Carbon|string $date The date to check
     * @param int|null $academicYearId Optional academic year filter
     * @return bool True if the date is a holiday
     */
    public function isHoliday(Carbon|string $date, ?int $academicYearId = null): bool
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        $cacheKey = self::CACHE_PREFIX . 'is_holiday.' . $date->format('Y-m-d') . '.' . ($academicYearId ?? 'all');

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($date, $academicYearId) {
            return Holiday::where('is_active', true)
                ->where('start_date', '<=', $date)
                ->where('end_date', '>=', $date)
                ->when($academicYearId, fn($q) => $q->where('academic_year_id', $academicYearId))
                ->whereIn('type', ['public_holiday', 'school_holiday'])
                ->exists();
        });
    }

    /**
     * Get holiday details for a specific date
     * 
     * @param Carbon|string $date The date to check
     * @param int|null $academicYearId Optional academic year filter
     * @return array|null Holiday details or null if not a holiday
     */
    public function getHolidayDetails(Carbon|string $date, ?int $academicYearId = null): ?array
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        $cacheKey = self::CACHE_PREFIX . 'details.' . $date->format('Y-m-d') . '.' . ($academicYearId ?? 'all');

        $holiday = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($date, $academicYearId) {
            return Holiday::where('is_active', true)
                ->where('start_date', '<=', $date)
                ->where('end_date', '>=', $date)
                ->when($academicYearId, fn($q) => $q->where('academic_year_id', $academicYearId))
                ->first();
        });

        if (!$holiday) {
            return null;
        }

        return [
            'id' => $holiday->id,
            'title' => $holiday->title,
            'description' => $holiday->description,
            'type' => $holiday->type,
            'type_label' => $holiday->type_label,
            'start_date' => $holiday->start_date->format('Y-m-d'),
            'end_date' => $holiday->end_date->format('Y-m-d'),
            'is_recurring' => $holiday->is_recurring,
            'location' => $holiday->location,
        ];
    }

    /**
     * Validate if attendance can be marked on a date
     * 
     * @param Carbon|string $date The date to validate
     * @param int|null $academicYearId Optional academic year filter
     * @return array Validation result with status and message
     */
    public function validateAttendanceDate(Carbon|string $date, ?int $academicYearId = null): array
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        
        if ($this->isHoliday($date, $academicYearId)) {
            $holidayDetails = $this->getHolidayDetails($date, $academicYearId);
            
            return [
                'valid' => false,
                'is_holiday' => true,
                'message' => 'This date is marked as Holiday. Attendance and Timetable cannot be added.',
                'holiday_title' => $holidayDetails['title'] ?? 'Holiday',
                'holiday_type' => $holidayDetails['type_label'] ?? 'Holiday',
            ];
        }

        // Check if date is in the past (optional business rule)
        if ($date->isPast()) {
            return [
                'valid' => true,
                'is_holiday' => false,
                'message' => 'Date is in the past',
                'warning' => 'Marking attendance for a past date',
            ];
        }

        // Check if date is in the future (optional business rule)
        if ($date->isFuture()) {
            return [
                'valid' => true,
                'is_holiday' => false,
                'message' => 'Date is in the future',
                'warning' => 'Marking attendance for a future date',
            ];
        }

        return [
            'valid' => true,
            'is_holiday' => false,
            'message' => 'Attendance can be marked',
        ];
    }

    /**
     * Check if timetable should load for a date
     * 
     * @param Carbon|string $date The date to check
     * @param int|null $academicYearId Optional academic year filter
     * @return array Timetable availability status
     */
    public function checkTimetableAvailability(Carbon|string $date, ?int $academicYearId = null): array
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        
        if ($this->isHoliday($date, $academicYearId)) {
            $holidayDetails = $this->getHolidayDetails($date, $academicYearId);
            
            return [
                'status' => 'holiday',
                'available' => false,
                'message' => 'Holiday - No Classes Scheduled',
                'holiday_title' => $holidayDetails['title'] ?? 'Holiday',
                'holiday_type' => $holidayDetails['type_label'] ?? 'Holiday',
                'periods' => [],
            ];
        }

        return [
            'status' => 'active',
            'available' => true,
            'message' => 'Timetable available',
            'periods' => null, // Will be populated by timetable logic
        ];
    }

    /**
     * Get all holidays within a date range
     * 
     * @param Carbon|string $startDate Start of date range
     * @param Carbon|string $endDate End of date range
     * @param int|null $academicYearId Optional academic year filter
     * @param bool $onlyActive Only return active holidays
     * @return Collection
     */
    public function getHolidaysInRange(
        Carbon|string $startDate,
        Carbon|string $endDate,
        ?int $academicYearId = null,
        bool $onlyActive = true
    ): Collection {
        $startDate = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $endDate = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);

        $cacheKey = self::CACHE_PREFIX . 'range.' . $startDate->format('Y-m-d') . '.' . $endDate->format('Y-m-d');

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($startDate, $endDate, $academicYearId, $onlyActive) {
            $query = Holiday::where('is_active', $onlyActive)
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($q2) use ($startDate, $endDate) {
                          $q2->where('start_date', '<=', $startDate)
                             ->where('end_date', '>=', $endDate);
                      });
                });

            if ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            }

            return $query->orderBy('start_date')->get();
        });
    }

    /**
     * Get all holiday dates within a range (flattened list)
     * 
     * @param Carbon|string $startDate Start of date range
     * @param Carbon|string $endDate End of date range
     * @param int|null $academicYearId Optional academic year filter
     * @return array Array of date strings (Y-m-d format)
     */
    public function getHolidayDatesInRange(
        Carbon|string $startDate,
        Carbon|string $endDate,
        ?int $academicYearId = null
    ): array {
        $holidays = $this->getHolidaysInRange($startDate, $endDate, $academicYearId);
        
        $holidayDates = [];
        
        foreach ($holidays as $holiday) {
            $period = new \DatePeriod(
                $holiday->start_date,
                new \DateInterval('P1D'),
                $holiday->end_date->addDay()
            );
            
            foreach ($period as $date) {
                $holidayDates[] = Carbon::instance($date)->format('Y-m-d');
            }
        }
        
        return array_unique($holidayDates);
    }

    /**
     * Get upcoming holidays
     * 
     * @param int $limit Number of holidays to return
     * @param int|null $academicYearId Optional academic year filter
     * @return Collection
     */
    public function getUpcomingHolidays(int $limit = 10, ?int $academicYearId = null): Collection
    {
        $today = Carbon::today();
        
        $query = Holiday::where('is_active', true)
            ->where('end_date', '>=', $today)
            ->whereIn('type', ['public_holiday', 'school_holiday'])
            ->orderBy('start_date');

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        return $query->limit($limit)->get();
    }

    /**
     * Bulk validate multiple dates
     * 
     * @param array $dates Array of dates to validate
     * @param int|null $academicYearId Optional academic year filter
     * @return array Validation results keyed by date
     */
    public function bulkValidateDates(array $dates, ?int $academicYearId = null): array
    {
        $results = [];
        
        foreach ($dates as $date) {
            $results[$date] = $this->validateAttendanceDate($date, $academicYearId);
        }
        
        return $results;
    }

    /**
     * Clear holiday cache
     * Useful after creating/updating/deleting holidays
     * 
     * @return void
     */
    public function clearCache(): void
    {
        Cache::tags(['holidays'])->flush();
        
        // Fallback for cache drivers that don't support tags
        Cache::forget(self::CACHE_PREFIX . '*');
    }

    /**
     * Check if a date range contains any holidays
     * 
     * @param Carbon|string $startDate Start of date range
     * @param Carbon|string $endDate End of date range
     * @param int|null $academicYearId Optional academic year filter
     * @return bool True if any holiday exists in range
     */
    public function hasHolidaysInRange(
        Carbon|string $startDate,
        Carbon|string $endDate,
        ?int $academicYearId = null
    ): bool {
        $startDate = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $endDate = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);

        return Holiday::where('is_active', true)
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->whereIn('type', ['public_holiday', 'school_holiday'])
            ->when($academicYearId, fn($q) => $q->where('academic_year_id', $academicYearId))
            ->exists();
    }

    /**
     * Get working days count in a date range (excluding holidays)
     * 
     * @param Carbon|string $startDate Start of date range
     * @param Carbon|string $endDate End of date range
     * @param int|null $academicYearId Optional academic year filter
     * @return int Number of working days
     */
    public function getWorkingDaysCount(
        Carbon|string $startDate,
        Carbon|string $endDate,
        ?int $academicYearId = null
    ): int {
        $startDate = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $endDate = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);
        
        $totalDays = $startDate->diffInDays($endDate) + 1;
        $holidayDates = $this->getHolidayDatesInRange($startDate, $endDate, $academicYearId);
        
        return $totalDays - count($holidayDates);
    }
}

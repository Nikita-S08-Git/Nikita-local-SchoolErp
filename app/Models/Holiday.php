<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
use App\Models\Academic\AcademicYear;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'type',
        'is_recurring',
        'academic_year_id',
        'program_incharge_id',
        'location',
        'attachment_path',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function programIncharge(): BelongsTo
    {
        return $this->belongsTo(User::class, 'program_incharge_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ProgramParticipant::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByAcademicYear($query, $yearId)
    {
        return $query->where('academic_year_id', $yearId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function ($q2) use ($startDate, $endDate) {
                  $q2->where('start_date', '<=', $startDate)
                     ->where('end_date', '>=', $endDate);
              });
        });
    }

    public function scopePrograms($query)
    {
        return $query->whereIn('type', ['event', 'program']);
    }

    public function scopeHolidays($query)
    {
        return $query->whereIn('type', ['public_holiday', 'school_holiday']);
    }

    public function isHoliday(Carbon $date): bool
    {
        return $date->betweenIncluded($this->start_date, $this->end_date);
    }

    public static function isDateHoliday(Carbon $date, $academicYearId = null): bool
    {
        $query = self::where('is_active', true)
            ->where(function ($q) use ($date) {
                $q->whereBetween('start_date', [$date, $date])
                  ->orWhereBetween('end_date', [$date, $date])
                  ->orWhere(function ($q2) use ($date) {
                      $q2->where('start_date', '<=', $date)
                         ->where('end_date', '>=', $date);
                  });
            });

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        return $query->exists();
    }

    public static function getHolidayTitle(Carbon $date): ?string
    {
        $holiday = self::where('is_active', true)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();

        return $holiday ? $holiday->title : null;
    }

    public function getDurationAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'public_holiday' => 'Public Holiday',
            'school_holiday' => 'School Holiday',
            'event' => 'Event',
            'program' => 'Program',
            default => ucfirst($this->type),
        };
    }
}

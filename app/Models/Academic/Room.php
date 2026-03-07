<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

/**
 * Room Model
 *
 * Represents classrooms, laboratories, and other rooms used for scheduling.
 * Essential for conflict-free timetable generation.
 *
 * Key Features:
 * - Room capacity tracking
 * - Facility/equipment tracking
 * - Availability constraints
 * - Department assignment
 * - Utilization tracking
 *
 * @package App\Models\Academic
 */
class Room extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Room type constants
     */
    const TYPE_CLASSROOM = 'classroom';
    const TYPE_LAB = 'lab';
    const TYPE_SEMINAR_HALL = 'seminar_hall';
    const TYPE_AUDITORIUM = 'auditorium';
    const TYPE_LIBRARY_ROOM = 'library_room';
    const TYPE_OTHER = 'other';

    /**
     * Status constants
     */
    const STATUS_AVAILABLE = 'available';
    const STATUS_UNDER_MAINTENANCE = 'under_maintenance';
    const STATUS_BLOCKED = 'blocked';
    const STATUS_DEPRECATED = 'deprecated';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'room_number',
        'name',
        'room_type',
        'capacity',
        'floor_number',
        'building_block',
        'has_projector',
        'has_smart_board',
        'has_computers',
        'computer_count',
        'has_ac',
        'is_wheelchair_accessible',
        'status',
        'maintenance_notes',
        'unavailable_days',
        'unavailable_time_slots',
        'min_booking_duration',
        'max_booking_duration',
        'department_id',
        'is_department_specific',
        'total_hours_used',
        'utilization_percentage',
        'description',
        'notes',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'capacity' => 'integer',
        'floor_number' => 'integer',
        'has_projector' => 'boolean',
        'has_smart_board' => 'boolean',
        'has_computers' => 'boolean',
        'computer_count' => 'integer',
        'has_ac' => 'boolean',
        'is_wheelchair_accessible' => 'boolean',
        'is_department_specific' => 'boolean',
        'total_hours_used' => 'integer',
        'utilization_percentage' => 'decimal:2',
        'unavailable_days' => 'array',
        'unavailable_time_slots' => 'array',
    ];

    /**
     * Get the department that primarily uses this room.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the user who created this room.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this room.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the timetables using this room.
     */
    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }

    /**
     * Scope to get available rooms.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    /**
     * Scope to get rooms by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('room_type', $type);
    }

    /**
     * Scope to get rooms with minimum capacity.
     */
    public function scopeWithMinimumCapacity($query, $capacity)
    {
        return $query->where('capacity', '>=', $capacity);
    }

    /**
     * Scope to get rooms with specific facilities.
     */
    public function scopeWithFacility($query, $facility)
    {
        return $query->where($facility, true);
    }

    /**
     * Scope to get department-specific rooms.
     */
    public function scopeDepartmentSpecific($query)
    {
        return $query->where('is_department_specific', true);
    }

    /**
     * Scope to get rooms for a specific department.
     */
    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Check if room is available for scheduling.
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    /**
     * Check if room is under maintenance.
     *
     * @return bool
     */
    public function isUnderMaintenance(): bool
    {
        return $this->status === self::STATUS_UNDER_MAINTENANCE;
    }

    /**
     * Check if room has a specific facility.
     *
     * @param string $facility
     * @return bool
     */
    public function hasFacility(string $facility): bool
    {
        $facilities = [
            'projector' => 'has_projector',
            'smart_board' => 'has_smart_board',
            'computers' => 'has_computers',
            'ac' => 'has_ac',
            'wheelchair_access' => 'is_wheelchair_accessible',
        ];

        if (!isset($facilities[$facility])) {
            return false;
        }

        return $this->{$facilities[$facility]};
    }

    /**
     * Check if room is available on a specific day.
     *
     * @param string $day
     * @return bool
     */
    public function isAvailableOnDay(string $day): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }

        if (is_array($this->unavailable_days) && in_array($day, $this->unavailable_days)) {
            return false;
        }

        return true;
    }

    /**
     * Check if room can accommodate a given number of students.
     *
     * @param int $studentCount
     * @return bool
     */
    public function canAccommodate(int $studentCount): bool
    {
        return $this->capacity >= $studentCount;
    }

    /**
     * Get the room type label.
     *
     * @return string
     */
    public function getRoomTypeLabelAttribute(): string
    {
        $labels = [
            self::TYPE_CLASSROOM => 'Classroom',
            self::TYPE_LAB => 'Laboratory',
            self::TYPE_SEMINAR_HALL => 'Seminar Hall',
            self::TYPE_AUDITORIUM => 'Auditorium',
            self::TYPE_LIBRARY_ROOM => 'Library Room',
            self::TYPE_OTHER => 'Other',
        ];

        return $labels[$this->room_type] ?? ucfirst($this->room_type);
    }

    /**
     * Get the full room identifier.
     *
     * @return string
     */
    public function getFullIdentifierAttribute(): string
    {
        $parts = [];
        
        if ($this->building_block) {
            $parts[] = $this->building_block;
        }
        
        if ($this->floor_number) {
            $parts[] = "Floor {$this->floor_number}";
        }
        
        $parts[] = $this->room_number;

        return implode(' - ', $parts);
    }

    /**
     * Get the complete room description.
     *
     * @return string
     */
    public function getCompleteDescriptionAttribute(): string
    {
        $description = "{$this->room_type_label}: {$this->room_number}";
        
        if ($this->name) {
            $description .= " ({$this->name})";
        }
        
        $description .= " - Capacity: {$this->capacity}";

        return $description;
    }
}

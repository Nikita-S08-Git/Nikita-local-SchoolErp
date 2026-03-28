<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'message',
        'type',
        'priority',
        'audience',
        'target_users',
        'is_active',
        'publish_at',
        'expires_at',
        'created_by',
    ];

    protected $casts = [
        'target_users' => 'array',
        'is_active' => 'boolean',
        'publish_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user who created the notification
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for active notifications
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('publish_at')
                  ->orWhere('publish_at', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', now());
            });
    }

    /**
     * Scope for specific audience
     */
    public function scopeForAudience($query, $audience)
    {
        return $query->where(function($q) use ($audience) {
            $q->where('audience', 'all')
              ->orWhere('audience', $audience);
        });
    }

    /**
     * Get badge color based on type
     */
    public function getBadgeColorAttribute(): string
    {
        return match($this->type) {
            'urgent' => 'danger',
            'holiday' => 'info',
            'exam' => 'warning',
            'fee' => 'success',
            'timetable' => 'primary',
            'attendance' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get icon based on type
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            'urgent' => 'bi-exclamation-triangle',
            'holiday' => 'bi-calendar-x',
            'exam' => 'bi-calendar-event',
            'fee' => 'bi-currency-dollar',
            'timetable' => 'bi-calendar-week',
            'attendance' => 'bi-calendar-check',
            default => 'bi-bell',
        };
    }
}

<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicSession extends Model
{
    protected $fillable = [
        'session_name',
        'start_date',
        'end_date',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean'
    ];

    /**
     * Accessor for backward compatibility - allows using $session->name
     */
    public function getNameAttribute(): string
    {
        return $this->session_name;
    }

    public function students(): HasMany
    {
        return $this->hasMany(\App\Models\User\Student::class);
    }

    /**
     * SCOPE - only active sessions
     *
     * Usage: AcademicSession::active()->get();
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * BOOTED EVENT - when a session is saved and marked active,
     * automatically deactivate any other sessions so that only one
     * can ever be active at a time. This enforces the business rule
     * "abhi 2025-26 chal raha hai to only this is active".
     */
    protected static function booted()
    {
        static::saving(function (AcademicSession $session) {
            if ($session->is_active) {
                AcademicSession::where('id', '!=', $session->id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }
        });
    }

    /**
     * Recalculate which session should be active based on today's date.
     *
     * This can be invoked from a scheduler or manually to ensure the
     * correct record stays active. It will deactivate any sessions that
     * do not cover the current date and activate the one that does.
     */
    public static function refreshActiveByDate(): void
    {
        $today = now()->toDateString();

        // deactivate all first
        AcademicSession::query()->update(['is_active' => false]);

        // then activate the one matching the current period
        AcademicSession::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->update(['is_active' => true]);
    }
}
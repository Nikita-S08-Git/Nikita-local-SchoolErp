<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        // Get all settings from database or config
        $settings = [
            'school_name' => config('app.name', 'School ERP'),
            'school_email' => config('mail.from.address', 'info@schoolerp.com'),
            'school_phone' => config('app.phone', ''),
            'school_address' => config('app.address', ''),
            'academic_year_start' => config('app.academic_year_start', '01-06'),
            'attendance_required' => config('app.attendance_required', 75),
            'fee_late_fee_percent' => config('app.fee_late_fee_percent', 5),
            'library_fine_per_day' => config('app.library_fine_per_day', 5),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'school_name' => 'required|string|max:255',
            'school_email' => 'required|email|max:255',
            'school_phone' => 'nullable|string|max:15',
            'school_address' => 'nullable|string|max:500',
            'academic_year_start' => 'nullable|date_format:m-d',
            'attendance_required' => 'nullable|integer|min:0|max:100',
            'fee_late_fee_percent' => 'nullable|integer|min:0|max:100',
            'library_fine_per_day' => 'nullable|integer|min:0',
        ]);

        // Update .env file or database settings
        // For now, we'll store in cache/database
        foreach ($validated as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now()]
            );
        }

        return redirect()->route('admin.settings')
            ->with('success', 'Settings updated successfully!');
    }

    /**
     * System information
     */
    public function system()
    {
        $systemInfo = [
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'database' => config('database.default'),
            'timezone' => config('app.timezone'),
            'debug_mode' => config('app.debug'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'mail_driver' => config('mail.default'),
        ];

        return view('admin.settings.system', compact('systemInfo'));
    }
}

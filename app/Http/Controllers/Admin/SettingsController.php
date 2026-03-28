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
            'college_name' => config('app.name', 'College ERP'),
            'college_email' => config('mail.from.address', 'info@collegeerp.com'),
            'college_phone' => config('app.phone', ''),
            'college_address' => config('app.address', ''),
            'affiliation_number' => config('app.affiliation', ''),
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
            'college_name' => 'required|string|max:255',
            'college_email' => 'required|email|max:255',
            'college_phone' => 'nullable|string|max:15',
            'college_address' => 'nullable|string|max:500',
            'affiliation_number' => 'nullable|string|max:100',
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

    /**
     * Clear application cache
     */
    public function clearCache()
    {
        \Artisan::call('cache:clear');
        \Artisan::call('view:clear');
        \Artisan::call('config:clear');
        \Artisan::call('route:clear');

        return redirect()->route('admin.settings.system')
            ->with('success', 'All caches cleared successfully!');
    }
}

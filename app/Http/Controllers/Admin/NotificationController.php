<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display notifications list
     */
    public function index()
    {
        $notifications = Notification::with('creator')
            ->latest()
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $users = User::all();
        return view('admin.notifications.create', compact('users'));
    }

    /**
     * Store new notification
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:general,urgent,holiday,exam,fee,timetable,attendance',
            'priority' => 'required|in:low,medium,high',
            'audience' => 'required|in:all,students,teachers,staff,parents',
            'target_users' => 'nullable|array',
            'publish_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:publish_at',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['target_users'] = $validated['target_users'] ?? null;

        Notification::create($validated);

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification created successfully!');
    }

    /**
     * Show notification details
     */
    public function show(Notification $notification)
    {
        $notification->load('creator');
        return view('admin.notifications.show', compact('notification'));
    }

    /**
     * Show edit form
     */
    public function edit(Notification $notification)
    {
        $users = User::all();
        return view('admin.notifications.edit', compact('notification', 'users'));
    }

    /**
     * Update notification
     */
    public function update(Request $request, Notification $notification)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:general,urgent,holiday,exam,fee,timetable,attendance',
            'priority' => 'required|in:low,medium,high',
            'audience' => 'required|in:all,students,teachers,staff,parents',
            'target_users' => 'nullable|array',
            'publish_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:publish_at',
        ]);

        $notification->update($validated);

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification updated successfully!');
    }

    /**
     * Delete notification
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification deleted successfully!');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(Notification $notification)
    {
        $notification->update(['is_active' => !$notification->is_active]);

        return redirect()->back()
            ->with('success', 'Notification status updated!');
    }

    /**
     * Get notifications for current user's dashboard
     */
    public function getDashboardNotifications()
    {
        $user = Auth::user();
        
        // Determine audience based on user role
        $role = $user->roles->first()->name ?? 'student';
        $audienceMap = [
            'admin' => 'all',
            'principal' => 'all',
            'teacher' => 'teachers',
            'student' => 'students',
            'staff' => 'staff',
        ];
        
        $userAudience = $audienceMap[$role] ?? 'all';

        $notifications = Notification::active()
            ->forAudience($userAudience)
            ->latest()
            ->limit(5)
            ->get();

        return $notifications;
    }
}

<?php

namespace App\Http\Controllers\Web\Academic;

use App\Http\Controllers\Controller;
use App\Models\Academic\AcademicSession;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class AcademicSessionController extends Controller
{
    public function index(Request $request): View
    {
        $query = AcademicSession::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('session_name', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('is_active', $request->status === 'active');
            });

        $sessions = $query->orderBy('start_date', 'desc')->paginate(15);
        return view('web.academic.sessions.index', compact('sessions'));
    }

    public function create(): View
    {
        return view('web.academic.sessions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'session_name' => [
                'required',
                'string',
                'max:255',
                'unique:academic_sessions,session_name'
            ],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'nullable|boolean'
        ], [
            'session_name.required' => 'Session name is required.',
            'session_name.unique' => 'This session name already exists.',
            'start_date.required' => 'Start date is required.',
            'end_date.required' => 'End date is required.',
            'end_date.after' => 'End date must be after start date.',
        ]);

        // If marking as active, check if dates include today
        if ($validated['is_active'] ?? false) {
            $today = now()->toDateString();
            if ($today < $validated['start_date'] || $today > $validated['end_date']) {
                return back()->withErrors([
                    'is_active' => 'An active session must include today\'s date.'
                ])->withInput();
            }
        }

        AcademicSession::create([
            'session_name' => $validated['session_name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $validated['is_active'] ?? false,
        ]);

        return redirect()->route('academic.sessions.index')
                         ->with('success', 'Academic session created successfully.');
    }

    public function edit(AcademicSession $session): View
    {
        return view('web.academic.sessions.edit', compact('session'));
    }

    public function update(Request $request, AcademicSession $session): RedirectResponse
    {
        $validated = $request->validate([
            'session_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('academic_sessions', 'session_name')->ignore($session->id)
            ],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'nullable|boolean'
        ], [
            'session_name.required' => 'Session name is required.',
            'session_name.unique' => 'This session name already exists.',
            'start_date.required' => 'Start date is required.',
            'end_date.required' => 'End date is required.',
            'end_date.after' => 'End date must be after start date.',
        ]);

        // If marking as active, check if dates include today
        if ($validated['is_active'] ?? false) {
            $today = now()->toDateString();
            if ($today < $validated['start_date'] || $today > $validated['end_date']) {
                return back()->withErrors([
                    'is_active' => 'An active session must include today\'s date.'
                ])->withInput();
            }
        }

        $session->update([
            'session_name' => $validated['session_name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $validated['is_active'] ?? $session->is_active,
        ]);

        return redirect()->route('academic.sessions.index')
                         ->with('success', 'Academic session updated successfully.');
    }

    public function destroy(AcademicSession $session): RedirectResponse
    {
        // Check if session has associated divisions or students
        if ($session->students()->count() > 0) {
            return redirect()->back()
                           ->with('error', 'Cannot delete session with associated students.');
        }

        $session->delete();
        return redirect()->route('academic.sessions.index')
                         ->with('success', 'Academic session deleted successfully.');
    }

    public function toggleStatus(AcademicSession $session): RedirectResponse
    {
        // If activating, check if dates include today
        if (!$session->is_active) {
            $today = now()->toDateString();
            if ($today < $session->start_date || $today > $session->end_date) {
                return back()->with('error', 'Cannot activate session that doesn\'t include today\'s date.');
            }
        }

        $session->update(['is_active' => !$session->is_active]);
        
        $status = $session->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
                         ->with('success', "Academic session {$status} successfully!");
    }
}
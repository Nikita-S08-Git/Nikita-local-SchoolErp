<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function index()
    {
        $leaves = Leave::with(['user', 'approver'])
            ->latest()
            ->paginate(15);
        return view('leaves.index', compact('leaves'));
    }

    public function myLeaves()
    {
        $leaves = Leave::where('user_id', auth()->id())
            ->latest()
            ->paginate(15);
        return view('leaves.my-leaves', compact('leaves'));
    }

    public function create()
    {
        return view('leaves.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_type' => 'required|in:sick,casual,earned,maternity,unpaid',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        Leave::create([
            'user_id' => auth()->id(),
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_days' => $totalDays,
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return redirect()->route('leaves.my-leaves')
            ->with('success', 'Leave application submitted successfully!');
    }

    public function approve(Leave $leave)
    {
        $leave->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('leaves.index')
            ->with('success', 'Leave approved successfully!');
    }

    public function reject(Request $request, Leave $leave)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $leave->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'rejection_reason' => $validated['rejection_reason'],
            'approved_at' => now(),
        ]);

        return redirect()->route('leaves.index')
            ->with('success', 'Leave rejected!');
    }

    public function destroy(Leave $leave)
    {
        if ($leave->user_id !== auth()->id() && !auth()->user()->hasRole(['admin', 'principal'])) {
            abort(403);
        }

        $leave->delete();
        return redirect()->route('leaves.my-leaves')
            ->with('success', 'Leave application deleted!');
    }
}

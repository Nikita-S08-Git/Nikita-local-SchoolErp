<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Academic\Subject;
use App\Models\Academic\Program;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::with('program')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('program_id'), function ($q) use ($request) {
                $q->where('program_id', $request->program_id);
            });

        $subjects = $query->paginate(15);
        $programs = Program::where('is_active', true)->get();

        return view('academic.subjects.index', compact('subjects', 'programs'));
    }

    public function create()
    {
        $programs = Program::where('is_active', true)->get();
        return view('academic.subjects.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'required|unique:subjects,code',
            'program_id' => 'required|exists:programs,id',
            'semester' => 'required|integer|min:1',
            'type' => 'required|in:Theory,Practical',
            'credit' => 'required|numeric|min:1'
        ]);

        $validated['academic_year_id'] = 1; // Default to first academic year

        Subject::create($validated);

        return redirect()->route('academic.subjects.index')
                         ->with('success', 'Subject created successfully.');
    }

    public function show(Subject $subject)
    {
        $subject->load('program');
        return view('academic.subjects.show', compact('subject'));
    }

    public function edit(Subject $subject)
    {
        $programs = Program::where('is_active', true)->get();
        return view('academic.subjects.edit', compact('subject', 'programs'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => ['required', Rule::unique('subjects')->ignore($subject->id)],
            'program_id' => 'required|exists:programs,id',
            'semester' => 'required|integer|min:1',
            'type' => 'required|in:Theory,Practical',
            'credit' => 'required|numeric|min:1'
        ]);

        $validated['academic_year_id'] = 1; // Default to first academic year

        $subject->update($validated);

        return redirect()->route('academic.subjects.index')
                         ->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject)
    {
        if ($subject->timetables()->count() > 0) {
            return redirect()->back()
                           ->with('error', 'Cannot delete subject with existing timetable entries.');
        }

        $subject->delete();
        return redirect()->route('academic.subjects.index')
                         ->with('success', 'Subject deleted successfully.');
    }
}
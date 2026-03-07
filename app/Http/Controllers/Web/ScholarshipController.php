<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Fee\Scholarship;
use App\Models\Fee\ScholarshipApplication;
use Illuminate\Http\Request;

class ScholarshipController extends Controller
{
    public function index()
    {
        $scholarships = Scholarship::paginate(10);
        return view('fees.scholarships.index', compact('scholarships'));
    }

    public function create()
    {
        return view('fees.scholarships.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:scholarships',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0'
        ]);

        Scholarship::create($request->all());
        return redirect()->route('fees.scholarships.index')->with('success', 'Scholarship created successfully');
    }

    public function show(Scholarship $scholarship)
    {
        $applications = ScholarshipApplication::with('student')
            ->where('scholarship_id', $scholarship->id)
            ->paginate(10);
        return view('fees.scholarships.show', compact('scholarship', 'applications'));
    }

    public function edit(Scholarship $scholarship)
    {
        return view('fees.scholarships.edit', compact('scholarship'));
    }

    public function update(Request $request, Scholarship $scholarship)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:scholarships,code,' . $scholarship->id,
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0'
        ]);

        $scholarship->update($request->all());
        return redirect()->route('fees.scholarships.index')->with('success', 'Scholarship updated successfully');
    }

    public function destroy(Scholarship $scholarship)
    {
        $scholarship->delete();
        return redirect()->route('fees.scholarships.index')->with('success', 'Scholarship deleted successfully');
    }
}
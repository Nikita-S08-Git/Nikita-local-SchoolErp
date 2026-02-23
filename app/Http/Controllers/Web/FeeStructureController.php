<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Fee\FeeStructure;
use App\Models\Fee\FeeHead;
use App\Models\Academic\Program;
use Illuminate\Http\Request;

class FeeStructureController extends Controller
{
    public function index()
    {
        $feeStructures = FeeStructure::with(['program', 'feeHead'])->paginate(10);
        return view('fees.structures.index', compact('feeStructures'));
    }

    public function create()
    {
        $programs = Program::active()->get();
        $feeHeads = FeeHead::active()->get();
        return view('fees.structures.create', compact('programs', 'feeHeads'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'academic_year' => 'required|string|max:20',
            'fee_head_id' => 'required|exists:fee_heads,id',
            'amount' => 'required|numeric|min:0',
            'installments' => 'required|integer|min:1|max:12'
        ]);

        FeeStructure::create($request->all());
        return redirect()->route('fees.structures.index')->with('success', 'Fee structure created successfully');
    }

    public function show(FeeStructure $structure)
    {
        $structure->load(['program', 'feeHead']);
        return view('fees.structures.show', compact('structure'));
    }

    public function edit(FeeStructure $structure)
    {
        $programs = Program::active()->get();
        $feeHeads = FeeHead::active()->get();
        return view('fees.structures.edit', compact('structure', 'programs', 'feeHeads'));
    }

    public function update(Request $request, FeeStructure $structure)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'academic_year' => 'required|string|max:20',
            'fee_head_id' => 'required|exists:fee_heads,id',
            'amount' => 'required|numeric|min:0',
            'installments' => 'required|integer|min:1|max:12'
        ]);

        $structure->update($request->all());
        return redirect()->route('fees.structures.index')->with('success', 'Fee structure updated successfully');
    }

    public function destroy(FeeStructure $structure)
    {
        $structure->delete();
        return redirect()->route('fees.structures.index')->with('success', 'Fee structure deleted successfully');
    }
}
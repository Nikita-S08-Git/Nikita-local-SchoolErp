<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Academic\AcademicRule;
use App\Services\AcademicRuleService;
use Illuminate\Http\Request;

class AcademicRuleController extends Controller
{
    /**
     * Display a listing of academic rules.
     */
    public function index(Request $request)
    {
        $query = AcademicRule::query();
        
        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        // Search by name or code
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('rule_code', 'like', "%{$request->search}%");
            });
        }
        
        $rules = $query->orderBy('category')->orderBy('display_order')->paginate(15);
        $categories = AcademicRule::CATEGORY_RESULT;
        
        return view('academic.rules.index', compact('rules', 'categories'));
    }

    /**
     * Show the form for creating a new academic rule.
     */
    public function create()
    {
        return view('academic.rules.create');
    }

    /**
     * Store a newly created academic rule.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rule_code' => 'required|string|max:50|unique:academic_rules',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:result,attendance,promotion,fee,atkt,examination,general',
            'value_type' => 'required|in:boolean,integer,decimal,string,json,array',
            'value' => 'required',
            'default_value' => 'nullable',
            'min_value' => 'nullable|numeric',
            'max_value' => 'nullable|numeric',
            'allowed_values' => 'nullable|array',
            'validation_pattern' => 'nullable|string',
            'effective_from' => 'nullable|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'is_active' => 'boolean',
            'is_mandatory' => 'boolean',
            'priority' => 'nullable|integer|min:0',
            'display_order' => 'nullable|integer|min:0',
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        $validated['is_mandatory'] = $request->has('is_mandatory');
        $validated['created_by'] = auth()->id();
        
        // Handle allowed values
        if ($request->filled('allowed_values')) {
            $validated['allowed_values'] = json_encode($request->allowed_values);
        }
        
        AcademicRule::create($validated);
        
        // Clear cache for this rule
        AcademicRuleService::clearCache($validated['rule_code'], $validated['category']);
        
        return redirect()->route('academic.rules.index')
            ->with('success', 'Academic rule created successfully!');
    }

    /**
     * Display the specified academic rule.
     */
    public function show(AcademicRule $rule)
    {
        return view('academic.rules.show', compact('rule'));
    }

    /**
     * Show the form for editing the specified academic rule.
     */
    public function edit(AcademicRule $rule)
    {
        return view('academic.rules.edit', compact('rule'));
    }

    /**
     * Update the specified academic rule.
     */
    public function update(Request $request, AcademicRule $rule)
    {
        $validated = $request->validate([
            'rule_code' => 'required|string|max:50|unique:academic_rules,rule_code,' . $rule->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:result,attendance,promotion,fee,atkt,examination,general',
            'value_type' => 'required|in:boolean,integer,decimal,string,json,array',
            'value' => 'required',
            'default_value' => 'nullable',
            'min_value' => 'nullable|numeric',
            'max_value' => 'nullable|numeric',
            'allowed_values' => 'nullable|array',
            'validation_pattern' => 'nullable|string',
            'effective_from' => 'nullable|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'is_active' => 'boolean',
            'is_mandatory' => 'boolean',
            'priority' => 'nullable|integer|min:0',
            'display_order' => 'nullable|integer|min:0',
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        $validated['is_mandatory'] = $request->has('is_mandatory');
        $validated['updated_by'] = auth()->id();
        
        // Handle allowed values
        if ($request->filled('allowed_values')) {
            $validated['allowed_values'] = json_encode($request->allowed_values);
        }
        
        $oldCode = $rule->rule_code;
        $oldCategory = $rule->category;
        
        $rule->update($validated);
        
        // Clear cache for old and new rule
        AcademicRuleService::clearCache($oldCode, $oldCategory);
        AcademicRuleService::clearCache($rule->rule_code, $rule->category);
        
        return redirect()->route('academic.rules.index')
            ->with('success', 'Academic rule updated successfully!');
    }

    /**
     * Remove the specified academic rule.
     */
    public function destroy(AcademicRule $rule)
    {
        $ruleCode = $rule->rule_code;
        $category = $rule->category;
        
        $rule->delete();
        
        // Clear cache
        AcademicRuleService::clearCache($ruleCode, $category);
        
        return redirect()->route('academic.rules.index')
            ->with('success', 'Academic rule deleted successfully!');
    }

    /**
     * Toggle rule status.
     */
    public function toggleStatus(AcademicRule $rule)
    {
        $rule->update(['is_active' => !$rule->is_active]);
        
        // Clear cache
        AcademicRuleService::clearCache($rule->rule_code, $rule->category);
        
        return redirect()->back()
            ->with('success', 'Rule status updated!');
    }

    /**
     * Clear all academic rule cache.
     */
    public function clearCache()
    {
        AcademicRuleService::clearAllCache();
        
        return redirect()->route('academic.rules.index')
            ->with('success', 'All caches cleared successfully!');
    }
}

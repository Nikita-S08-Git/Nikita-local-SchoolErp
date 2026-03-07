<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Academic\AcademicRule;
use App\Models\AuditLog;
use App\Services\AcademicRuleService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        
        $rule = AcademicRule::create($validated);
        
        // Log audit event
        AuditLog::logEvent(
            $rule,
            'created',
            null,
            $rule->toArray()
        );
        
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
        // Validate based on value type
        $validationRules = $this->getValidationRules($request, $rule);
        
        $validated = $request->validate($validationRules);
        
        $validated['is_active'] = $request->has('is_active');
        $validated['is_mandatory'] = $request->has('is_mandatory');
        $validated['updated_by'] = auth()->id();
        
        // Handle allowed values
        if ($request->filled('allowed_values')) {
            $validated['allowed_values'] = json_encode($request->allowed_values);
        }
        
        // Validate value based on type
        $valueValidation = $this->validateValueByType($request->value, $rule->value_type, $rule);
        if (!$valueValidation['valid']) {
            return back()->withErrors(['value' => $valueValidation['error']])->withInput();
        }
        
        // Check date overlap with existing rules
        $dateOverlapError = $this->checkDateOverlap($rule, $request->effective_from, $request->effective_to);
        if ($dateOverlapError) {
            return back()->withErrors(['effective_from' => $dateOverlapError])->withInput();
        }
        
        $oldValues = $rule->toArray();
        $oldCode = $rule->rule_code;
        $oldCategory = $rule->category;
        
        $rule->update($validated);
        
        // Log audit event
        AuditLog::logEvent(
            $rule,
            'updated',
            $oldValues,
            $rule->toArray()
        );
        
        // Clear cache for old and new rule
        AcademicRuleService::clearCache($oldCode, $oldCategory);
        AcademicRuleService::clearCache($rule->rule_code, $rule->category);
        
        return redirect()->route('academic.rules.index')
            ->with('success', 'Academic rule updated successfully!');
    }
    
    /**
     * Get validation rules based on value type.
     */
    private function getValidationRules(Request $request, AcademicRule $rule): array
    {
        $rules = [
            'rule_code' => ['required', 'string', 'max:50', Rule::unique('academic_rules')->ignore($rule->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'in:result,attendance,promotion,fee,atkt,examination,general'],
            'value_type' => ['required', 'in:boolean,integer,decimal,string,json,array'],
            'value' => ['required'],
            'default_value' => ['nullable'],
            'min_value' => ['nullable', 'numeric'],
            'max_value' => ['nullable', 'numeric'],
            'allowed_values' => ['nullable', 'array'],
            'validation_pattern' => ['nullable', 'string'],
            'effective_from' => ['nullable', 'date'],
            'effective_to' => ['nullable', 'date', 'after:effective_from'],
            'is_active' => ['boolean'],
            'is_mandatory' => ['boolean'],
            'priority' => ['nullable', 'integer', 'min:0'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ];
        
        // Add type-specific validation
        if ($rule->value_type === AcademicRule::VALUE_TYPE_INTEGER) {
            $rules['value'][] = 'integer';
            if ($rule->min_value !== null) {
                $rules['value'][] = 'min:' . $rule->min_value;
            }
            if ($rule->max_value !== null) {
                $rules['value'][] = 'max:' . $rule->max_value;
            }
        } elseif ($rule->value_type === AcademicRule::VALUE_TYPE_DECIMAL) {
            $rules['value'][] = 'numeric';
            if ($rule->min_value !== null) {
                $rules['value'][] = 'min:' . $rule->min_value;
            }
            if ($rule->max_value !== null) {
                $rules['value'][] = 'max:' . $rule->max_value;
            }
        } elseif ($rule->value_type === AcademicRule::VALUE_TYPE_BOOLEAN) {
            $rules['value'][] = 'boolean';
        }
        
        return $rules;
    }
    
    /**
     * Validate value based on type.
     */
    private function validateValueByType($value, string $valueType, AcademicRule $rule): array
    {
        $result = ['valid' => true, 'error' => null];
        
        switch ($valueType) {
            case AcademicRule::VALUE_TYPE_INTEGER:
                if (!is_numeric($value) || (int)$value != $value) {
                    $result = ['valid' => false, 'error' => 'Value must be an integer'];
                } elseif ($rule->min_value !== null && (int)$value < (int)$rule->min_value) {
                    $result = ['valid' => false, 'error' => 'Value must be at least ' . $rule->min_value];
                } elseif ($rule->max_value !== null && (int)$value > (int)$rule->max_value) {
                    $result = ['valid' => false, 'error' => 'Value must be at most ' . $rule->max_value];
                }
                break;
                
            case AcademicRule::VALUE_TYPE_DECIMAL:
                if (!is_numeric($value)) {
                    $result = ['valid' => false, 'error' => 'Value must be a number'];
                } elseif ($rule->min_value !== null && (float)$value < (float)$rule->min_value) {
                    $result = ['valid' => false, 'error' => 'Value must be at least ' . $rule->min_value];
                } elseif ($rule->max_value !== null && (float)$value > (float)$rule->max_value) {
                    $result = ['valid' => false, 'error' => 'Value must be at most ' . $rule->max_value];
                }
                break;
                
            case AcademicRule::VALUE_TYPE_BOOLEAN:
                if (!in_array($value, ['0', '1', 'true', 'false', 'yes', 'no'], true)) {
                    $result = ['valid' => false, 'error' => 'Value must be a boolean (0, 1, true, false, yes, no)'];
                }
                break;
        }
        
        return $result;
    }
    
    /**
     * Check for date overlap with existing rules.
     */
    private function checkDateOverlap(AcademicRule $rule, ?string $effectiveFrom, ?string $effectiveTo): ?string
    {
        if (!$effectiveFrom && !$effectiveTo) {
            return null;
        }
        
        $overlappingRules = AcademicRule::where('id', '!=', $rule->id)
            ->where('rule_code', $rule->rule_code)
            ->where('is_active', true)
            ->where(function ($query) use ($effectiveFrom, $effectiveTo) {
                if ($effectiveFrom) {
                    $query->where(function ($q) use ($effectiveFrom) {
                        $q->whereNull('effective_to')
                          ->orWhere('effective_to', '>=', $effectiveFrom);
                    });
                }
                if ($effectiveTo) {
                    $query->where(function ($q) use ($effectiveTo) {
                        $q->whereNull('effective_from')
                          ->orWhere('effective_from', '<=', $effectiveTo);
                    });
                }
            })
            ->exists();
        
        if ($overlappingRules) {
            return 'The effective date range overlaps with an existing active rule. Please adjust the dates.';
        }
        
        return null;
    }
    
    /**
     * Display rule history.
     */
    public function history(Request $request)
    {
        $ruleId = $request->get('rule_id');
        
        if ($ruleId) {
            // Get history for a specific rule
            $rule = AcademicRule::findOrFail($ruleId);
            $auditLogs = AuditLog::where('auditable_type', AcademicRule::class)
                ->where('auditable_id', $ruleId)
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            
            return view('academic.rules.history', compact('rule', 'auditLogs'));
        }
        
        // Get all rule history
        $auditLogs = AuditLog::where('auditable_type', AcademicRule::class)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('academic.rules.history', compact('auditLogs'));
    }

    /**
     * Remove the specified academic rule.
     */
    public function destroy(AcademicRule $rule)
    {
        $ruleCode = $rule->rule_code;
        $category = $rule->category;
        $oldValues = $rule->toArray();
        
        $rule->delete();
        
        // Log audit event
        AuditLog::logEvent(
            $rule,
            'deleted',
            $oldValues,
            null
        );
        
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
        $oldValues = ['is_active' => $rule->is_active];
        $newValues = ['is_active' => !$rule->is_active];
        
        $rule->update(['is_active' => !$rule->is_active]);
        
        // Log audit event
        AuditLog::logEvent(
            $rule,
            'status_toggled',
            $oldValues,
            $newValues
        );
        
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

<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\PayrollWorkingDaysSetting;
use Illuminate\Http\Request;

class CalculationRulesController extends Controller
{
    /**
     * Display a listing of Working Days & Payroll Calculation Rules.
     */
    public function index()
    {
        $data['title'] = 'Masters / Calculation Rules & Working Days';
        $data['rules'] = PayrollWorkingDaysSetting::orderBy('is_default', 'desc')
            ->orderBy('id', 'asc')
            ->get();
        $data['activeRule'] = PayrollWorkingDaysSetting::getActiveSetting();

        return view('home.payroll.calculation-rules.index', $data);
    }

    /**
     * Show the form for creating a new calculation rule.
     */
    public function create()
    {
        $data['title'] = 'Masters / Add Calculation Rule';
        return view('home.payroll.calculation-rules.create', $data);
    }

    /**
     * Store a newly created calculation rule in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'policy_name' => 'required|string|max:100',
            'calculation_basis' => 'required|in:fixed_26_days,calendar_days,fixed_30_days,actual_working_days',
            'weekly_off_pattern' => 'required|in:sunday_only,saturday_sunday,alternate_saturday_sunday',
            'deduct_unpaid_leave_only' => 'nullable|boolean',
            'include_holidays_in_payable' => 'nullable|boolean',
            'include_weekly_off_in_payable' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'status' => 'required|in:Active,Inactive',
            'description' => 'nullable|string|max:500',
        ]);

        $isDefault = $request->boolean('is_default');

        if ($isDefault) {
            // Unset previous defaults
            PayrollWorkingDaysSetting::where('is_default', true)->update(['is_default' => false]);
        }

        PayrollWorkingDaysSetting::create([
            'policy_name' => $validated['policy_name'],
            'calculation_basis' => $validated['calculation_basis'],
            'weekly_off_pattern' => $validated['weekly_off_pattern'],
            'deduct_unpaid_leave_only' => $request->has('deduct_unpaid_leave_only'),
            'include_holidays_in_payable' => $request->has('include_holidays_in_payable'),
            'include_weekly_off_in_payable' => $request->has('include_weekly_off_in_payable'),
            'is_default' => $isDefault,
            'status' => $validated['status'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('masters.payroll.calculation-rules')
            ->with('success', 'Calculation & Working Days Rule created successfully.');
    }

    /**
     * Show the form for editing the specified calculation rule.
     */
    public function edit(string $id)
    {
        $rule = PayrollWorkingDaysSetting::findOrFail($id);
        $data['title'] = 'Masters / Edit Calculation Rule';
        $data['rule'] = $rule;

        return view('home.payroll.calculation-rules.edit', $data);
    }

    /**
     * Update the specified calculation rule in storage.
     */
    public function update(Request $request, string $id)
    {
        $rule = PayrollWorkingDaysSetting::findOrFail($id);

        $validated = $request->validate([
            'policy_name' => 'required|string|max:100',
            'calculation_basis' => 'required|in:fixed_26_days,calendar_days,fixed_30_days,actual_working_days',
            'weekly_off_pattern' => 'required|in:sunday_only,saturday_sunday,alternate_saturday_sunday',
            'deduct_unpaid_leave_only' => 'nullable|boolean',
            'include_holidays_in_payable' => 'nullable|boolean',
            'include_weekly_off_in_payable' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'status' => 'required|in:Active,Inactive',
            'description' => 'nullable|string|max:500',
        ]);

        $isDefault = $request->boolean('is_default');

        if ($isDefault && !$rule->is_default) {
            PayrollWorkingDaysSetting::where('is_default', true)->update(['is_default' => false]);
        }

        $rule->update([
            'policy_name' => $validated['policy_name'],
            'calculation_basis' => $validated['calculation_basis'],
            'weekly_off_pattern' => $validated['weekly_off_pattern'],
            'deduct_unpaid_leave_only' => $request->has('deduct_unpaid_leave_only'),
            'include_holidays_in_payable' => $request->has('include_holidays_in_payable'),
            'include_weekly_off_in_payable' => $request->has('include_weekly_off_in_payable'),
            'is_default' => $isDefault,
            'status' => $validated['status'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('masters.payroll.calculation-rules')
            ->with('success', 'Calculation Rule updated successfully.');
    }

    /**
     * Set a policy as the active default calculation rule.
     */
    public function setDefault(string $id)
    {
        $rule = PayrollWorkingDaysSetting::findOrFail($id);

        PayrollWorkingDaysSetting::where('is_default', true)->update(['is_default' => false]);

        $rule->update([
            'is_default' => true,
            'status' => 'Active',
        ]);

        return redirect()->route('masters.payroll.calculation-rules')
            ->with('success', "Policy '{$rule->policy_name}' has been activated as the active Default Rule for payroll calculations.");
    }

    /**
     * Remove the specified calculation rule from storage.
     */
    public function destroy(string $id)
    {
        $rule = PayrollWorkingDaysSetting::findOrFail($id);

        if ($rule->is_default) {
            return redirect()->back()->with('error', 'The active default policy cannot be deleted. Please set another policy as default first.');
        }

        $rule->delete();

        return redirect()->route('masters.payroll.calculation-rules')
            ->with('success', 'Calculation Rule deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\PayrollApplicability;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ApplicabilityController extends Controller
{
    /**
     * Display a listing of payroll applicability rules.
     */
    public function index(Request $request)
    {
        $data['title'] = 'Payroll Applicability Rules';

        // Handle AJAX DataTables request
        if ($request->ajax()) {
            $query = PayrollApplicability::query();

            // Status filter
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Applies To filter
            if ($request->filled('applies_to') && $request->applies_to !== 'all') {
                $query->where('applies_to', $request->applies_to);
            }

            // Keyword search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('rule_name', 'like', "%{$search}%")
                        ->orWhere('rule_code', 'like', "%{$search}%")
                        ->orWhere('salary_condition', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $rules = $query->orderBy('id', 'asc')->get();

            $formatted = $rules->map(function ($item, $index) {
                return [
                    'index'            => $index + 1,
                    'id'               => $item->id,
                    'rule_name'        => $item->rule_name,
                    'rule_code'        => $item->rule_code,
                    'applies_to'       => $item->applies_to,
                    'salary_condition' => $item->salary_condition ?: 'No specific threshold',
                    'min_salary'       => (float) $item->min_salary,
                    'max_salary'       => (float) $item->max_salary,
                    'min_service_days' => (int) $item->min_service_days,
                    'effective_from'   => $item->effective_from ? $item->effective_from->format('Y-m-d') : null,
                    'effective_display'=> $item->effective_from ? $item->effective_from->format('d M, Y') : 'Immediate',
                    'status'           => $item->status,
                    'description'      => $item->description ?: 'Standard statutory applicability policy.',
                    'edit_url'         => route('masters.payroll.applicability.edit', $item->id),
                    'update_url'       => route('masters.payroll.applicability.update', $item->id),
                    'delete_url'       => route('masters.payroll.applicability.destroy', $item->id),
                    'toggle_url'       => route('masters.payroll.applicability.toggle-status', $item->id),
                ];
            });

            return response()->json([
                'success' => true,
                'data'    => $formatted,
            ]);
        }

        // Summary KPI Metrics
        $data['totalRules']       = PayrollApplicability::count();
        $data['activeRules']      = PayrollApplicability::where('status', 'Active')->count();
        $data['permanentRules']   = PayrollApplicability::where('applies_to', 'permanent')->count();
        $data['contractualRules'] = PayrollApplicability::whereIn('applies_to', ['contractual', 'intern'])->count();
        $data['applicabilities']  = PayrollApplicability::orderBy('id', 'asc')->get();

        return view('home.payroll.applicability.index', $data);
    }

    /**
     * Show the form for creating a new applicability rule.
     */
    public function create()
    {
        $data['title'] = 'Add Applicability Rule';
        return view('home.payroll.applicability.create', $data);
    }

    /**
     * Store a newly created applicability rule in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rule_name'        => 'required|string|max:150',
            'rule_code'        => 'required|string|max:50|unique:payroll_applicabilities,rule_code',
            'applies_to'       => 'required|in:all,permanent,contractual,intern,probation',
            'salary_condition' => 'nullable|string|max:150',
            'min_salary'       => 'nullable|numeric|min:0',
            'max_salary'       => 'nullable|numeric|min:0',
            'min_service_days' => 'nullable|integer|min:0',
            'effective_from'   => 'nullable|date',
            'status'           => 'required|in:Active,Inactive',
            'description'      => 'nullable|string|max:1000',
        ]);

        $rule = PayrollApplicability::create([
            'rule_name'        => trim($validated['rule_name']),
            'rule_code'        => strtoupper(trim($validated['rule_code'])),
            'applies_to'       => $validated['applies_to'],
            'salary_condition' => $validated['salary_condition'] ?? null,
            'min_salary'       => $validated['min_salary'] ?? 0.00,
            'max_salary'       => $validated['max_salary'] ?? 0.00,
            'min_service_days' => $validated['min_service_days'] ?? 0,
            'effective_from'   => $validated['effective_from'] ?? null,
            'status'           => $validated['status'],
            'description'      => $validated['description'] ?? null,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Applicability rule "' . $rule->rule_name . '" created successfully!',
                'data'    => $rule,
            ]);
        }

        return redirect()->route('masters.payroll.applicability')
            ->with('success', 'Applicability rule created successfully!');
    }

    /**
     * Display the specified applicability rule.
     */
    public function show(string $id)
    {
        $rule = PayrollApplicability::findOrFail($id);
        return response()->json([
            'success'       => true,
            'applicability' => $rule,
        ]);
    }

    /**
     * Show the form for editing the specified applicability rule.
     */
    public function edit(string $id)
    {
        $rule = PayrollApplicability::findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success'       => true,
                'applicability' => $rule,
            ]);
        }

        $data['title']         = 'Edit Applicability Rule: ' . $rule->rule_name;
        $data['applicability'] = $rule;

        return view('home.payroll.applicability.create', $data);
    }

    /**
     * Update the specified applicability rule in storage.
     */
    public function update(Request $request, string $id)
    {
        $rule = PayrollApplicability::findOrFail($id);

        $validated = $request->validate([
            'rule_name'        => 'required|string|max:150',
            'rule_code'        => ['required', 'string', 'max:50', Rule::unique('payroll_applicabilities', 'rule_code')->ignore($rule->id)],
            'applies_to'       => 'required|in:all,permanent,contractual,intern,probation',
            'salary_condition' => 'nullable|string|max:150',
            'min_salary'       => 'nullable|numeric|min:0',
            'max_salary'       => 'nullable|numeric|min:0',
            'min_service_days' => 'nullable|integer|min:0',
            'effective_from'   => 'nullable|date',
            'status'           => 'required|in:Active,Inactive',
            'description'      => 'nullable|string|max:1000',
        ]);

        $rule->update([
            'rule_name'        => trim($validated['rule_name']),
            'rule_code'        => strtoupper(trim($validated['rule_code'])),
            'applies_to'       => $validated['applies_to'],
            'salary_condition' => $validated['salary_condition'] ?? null,
            'min_salary'       => $validated['min_salary'] ?? 0.00,
            'max_salary'       => $validated['max_salary'] ?? 0.00,
            'min_service_days' => $validated['min_service_days'] ?? 0,
            'effective_from'   => $validated['effective_from'] ?? null,
            'status'           => $validated['status'],
            'description'      => $validated['description'] ?? null,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Applicability rule "' . $rule->rule_name . '" updated successfully!',
                'data'    => $rule,
            ]);
        }

        return redirect()->route('masters.payroll.applicability')
            ->with('success', 'Applicability rule updated successfully!');
    }

    /**
     * Remove the specified applicability rule.
     */
    public function destroy(Request $request, string $id)
    {
        $rule = PayrollApplicability::findOrFail($id);
        $name = $rule->rule_name;
        $rule->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Applicability rule '{$name}' deleted successfully.",
            ]);
        }

        return redirect()->route('masters.payroll.applicability')
            ->with('success', "Applicability rule '{$name}' deleted successfully.");
    }

    /**
     * Toggle active/inactive status of an applicability rule.
     */
    public function toggleStatus(Request $request, string $id)
    {
        $rule = PayrollApplicability::findOrFail($id);
        $newStatus = $rule->status === 'Active' ? 'Inactive' : 'Active';
        $rule->status = $newStatus;
        $rule->save();

        return response()->json([
            'success'   => true,
            'newStatus' => $newStatus,
            'message'   => "Status of '{$rule->rule_name}' changed to {$newStatus}.",
        ]);
    }
}

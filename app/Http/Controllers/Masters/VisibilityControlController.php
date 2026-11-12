<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\PayrollVisibilityControl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VisibilityControlController extends Controller
{
    /**
     * Display a listing of payroll visibility and access control policies.
     */
    public function index(Request $request)
    {
        $data['title'] = 'Payroll Visibility & Access Control';

        // Handle AJAX DataTables query
        if ($request->ajax()) {
            $query = PayrollVisibilityControl::query();

            // Status filter
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Access level filter
            if ($request->filled('access_level') && $request->access_level !== 'all') {
                $query->where('access_level', $request->access_level);
            }

            // Role / applies_to filter
            if ($request->filled('applies_to') && $request->applies_to !== 'all') {
                $query->where('applies_to', $request->applies_to);
            }

            // Keyword search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('rule_name', 'like', "%{$search}%")
                        ->orWhere('rule_code', 'like', "%{$search}%")
                        ->orWhere('applies_to', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $rules = $query->orderBy('id', 'asc')->get();

            $formatted = $rules->map(function ($item, $index) {
                return [
                    'index'                     => $index + 1,
                    'id'                        => $item->id,
                    'rule_name'                 => $item->rule_name,
                    'rule_code'                 => $item->rule_code,
                    'applies_to'                => $item->applies_to,
                    'access_level'              => $item->access_level,
                    'can_view_salary'           => (bool) $item->can_view_salary,
                    'can_edit_structure'        => (bool) $item->can_edit_structure,
                    'can_process_payroll'       => (bool) $item->can_process_payroll,
                    'can_download_reports'      => (bool) $item->can_download_reports,
                    'mask_sensitive_components' => (bool) $item->mask_sensitive_components,
                    'status'                    => $item->status,
                    'description'               => $item->description ?: 'Role-based payroll confidentiality policy.',
                    'edit_url'                  => route('masters.payroll.visibility.edit', $item->id),
                    'update_url'                => route('masters.payroll.visibility.update', $item->id),
                    'delete_url'                => route('masters.payroll.visibility.destroy', $item->id),
                    'toggle_url'                => route('masters.payroll.visibility.toggle-status', $item->id),
                ];
            });

            return response()->json([
                'success' => true,
                'data'    => $formatted,
            ]);
        }

        // Summary KPI Metrics
        $data['totalRules']       = PayrollVisibilityControl::count();
        $data['activeRules']      = PayrollVisibilityControl::where('status', 'Active')->count();
        $data['fullAccessRules']  = PayrollVisibilityControl::where('access_level', 'Full Access')->count();
        $data['restrictedRules']  = PayrollVisibilityControl::whereIn('access_level', ['Restricted', 'Self Only'])->count();

        // Roles & Cohorts for Filter and Drawer
        $data['rolesList'] = DB::table('roles')->pluck('name')->toArray();
        if (empty($data['rolesList'])) {
            $data['rolesList'] = ['Super Admin', 'Admin', 'HR', 'Employee', 'Client', 'Supervisor', 'Manager'];
        }

        $data['departmentsList'] = DB::table('departments')->pluck('department_name')->toArray();

        return view('home.payroll.visibility.index', $data);
    }

    /**
     * Show the form for creating a new visibility control rule.
     */
    public function create()
    {
        $data['title']           = 'Add Visibility & Access Rule';
        $data['rolesList']       = DB::table('roles')->pluck('name')->toArray();
        $data['departmentsList'] = DB::table('departments')->pluck('department_name')->toArray();
        return view('home.payroll.visibility.create', $data);
    }

    /**
     * Store a newly created visibility control rule in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rule_name'                 => 'required|string|max:150',
            'rule_code'                 => 'required|string|max:50|unique:payroll_visibility_controls,rule_code',
            'applies_to'                => 'required|string|max:100',
            'access_level'              => 'required|in:Full Access,Read Only,Restricted,Self Only',
            'can_view_salary'           => 'nullable|boolean',
            'can_edit_structure'        => 'nullable|boolean',
            'can_process_payroll'       => 'nullable|boolean',
            'can_download_reports'      => 'nullable|boolean',
            'mask_sensitive_components' => 'nullable|boolean',
            'status'                    => 'required|in:Active,Inactive',
            'description'               => 'nullable|string|max:1000',
        ]);

        $rule = PayrollVisibilityControl::create([
            'rule_name'                 => trim($validated['rule_name']),
            'rule_code'                 => strtoupper(trim($validated['rule_code'])),
            'applies_to'                => trim($validated['applies_to']),
            'access_level'              => $validated['access_level'],
            'can_view_salary'           => $request->boolean('can_view_salary', true),
            'can_edit_structure'        => $request->boolean('can_edit_structure', false),
            'can_process_payroll'       => $request->boolean('can_process_payroll', false),
            'can_download_reports'      => $request->boolean('can_download_reports', false),
            'mask_sensitive_components' => $request->boolean('mask_sensitive_components', false),
            'status'                    => $validated['status'],
            'description'               => $validated['description'] ?? null,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Visibility Rule "' . $rule->rule_name . '" created successfully!',
                'data'    => $rule,
            ]);
        }

        return redirect()->route('masters.payroll.visibility')
            ->with('success', 'Visibility Rule created successfully!');
    }

    /**
     * Display the specified visibility control rule.
     */
    public function show(string $id)
    {
        $rule = PayrollVisibilityControl::findOrFail($id);
        return response()->json([
            'success'    => true,
            'visibility' => $rule,
        ]);
    }

    /**
     * Show the form for editing the specified visibility rule.
     */
    public function edit(string $id)
    {
        $rule = PayrollVisibilityControl::findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success'    => true,
                'visibility' => $rule,
            ]);
        }

        $data['title']           = 'Edit Visibility Rule: ' . $rule->rule_name;
        $data['visibility']      = $rule;
        $data['rolesList']       = DB::table('roles')->pluck('name')->toArray();
        $data['departmentsList'] = DB::table('departments')->pluck('department_name')->toArray();

        return view('home.payroll.visibility.create', $data);
    }

    /**
     * Update the specified visibility rule in storage.
     */
    public function update(Request $request, string $id)
    {
        $rule = PayrollVisibilityControl::findOrFail($id);

        $validated = $request->validate([
            'rule_name'                 => 'required|string|max:150',
            'rule_code'                 => ['required', 'string', 'max:50', Rule::unique('payroll_visibility_controls', 'rule_code')->ignore($rule->id)],
            'applies_to'                => 'required|string|max:100',
            'access_level'              => 'required|in:Full Access,Read Only,Restricted,Self Only',
            'can_view_salary'           => 'nullable|boolean',
            'can_edit_structure'        => 'nullable|boolean',
            'can_process_payroll'       => 'nullable|boolean',
            'can_download_reports'      => 'nullable|boolean',
            'mask_sensitive_components' => 'nullable|boolean',
            'status'                    => 'required|in:Active,Inactive',
            'description'               => 'nullable|string|max:1000',
        ]);

        $rule->update([
            'rule_name'                 => trim($validated['rule_name']),
            'rule_code'                 => strtoupper(trim($validated['rule_code'])),
            'applies_to'                => trim($validated['applies_to']),
            'access_level'              => $validated['access_level'],
            'can_view_salary'           => $request->boolean('can_view_salary', true),
            'can_edit_structure'        => $request->boolean('can_edit_structure', false),
            'can_process_payroll'       => $request->boolean('can_process_payroll', false),
            'can_download_reports'      => $request->boolean('can_download_reports', false),
            'mask_sensitive_components' => $request->boolean('mask_sensitive_components', false),
            'status'                    => $validated['status'],
            'description'               => $validated['description'] ?? null,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Visibility Rule "' . $rule->rule_name . '" updated successfully!',
                'data'    => $rule,
            ]);
        }

        return redirect()->route('masters.payroll.visibility')
            ->with('success', 'Visibility Rule updated successfully!');
    }

    /**
     * Remove the specified visibility rule.
     */
    public function destroy(Request $request, string $id)
    {
        $rule = PayrollVisibilityControl::findOrFail($id);
        $name = $rule->rule_name;
        $rule->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Visibility Rule '{$name}' deleted successfully.",
            ]);
        }

        return redirect()->route('masters.payroll.visibility')
            ->with('success', "Visibility Rule '{$name}' deleted successfully.");
    }

    /**
     * Toggle active/inactive status of a visibility rule.
     */
    public function toggleStatus(Request $request, string $id)
    {
        $rule = PayrollVisibilityControl::findOrFail($id);
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

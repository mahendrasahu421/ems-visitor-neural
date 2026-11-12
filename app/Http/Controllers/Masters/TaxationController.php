<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Taxation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaxationController extends Controller
{
    /**
     * Display a listing of taxation and statutory compliance rules.
     */
    public function index(Request $request)
    {
        $data['title'] = "Taxation & Statutory Compliance";

        // Handle AJAX DataTables query
        if ($request->ajax()) {
            $query = Taxation::query();

            // Filters
            if ($request->filled('type') && $request->type !== 'all') {
                $query->where('type', $request->type);
            }

            if ($request->filled('applicable_on') && $request->applicable_on !== 'all') {
                $query->where('applicable_on', $request->applicable_on);
            }

            if ($request->filled('calculation_basis') && $request->calculation_basis !== 'all') {
                $query->where('calculation_basis', $request->calculation_basis);
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('tax_name', 'like', "%{$search}%")
                        ->orWhere('tax_code', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $taxes = $query->orderBy('type', 'asc')->orderBy('id', 'asc')->get();

            $formatted = $taxes->map(function ($item, $index) {
                $applicableLabel = match ($item->applicable_on) {
                    'basic'          => 'Basic Salary',
                    'gross'          => 'Gross Salary',
                    'net'            => 'Net Salary',
                    'taxable_income' => 'Taxable Income',
                    'ctc'            => 'Annual CTC',
                    default          => ucfirst($item->applicable_on),
                };

                return [
                    'index'             => $index + 1,
                    'id'                => $item->id,
                    'tax_name'          => $item->tax_name,
                    'tax_code'          => $item->tax_code,
                    'type'              => $item->type,
                    'applicable_on'     => $item->applicable_on,
                    'applicable_label'  => $applicableLabel,
                    'calculation_basis' => $item->calculation_basis,
                    'rate'              => (float) $item->rate,
                    'fixed_amount'      => (float) $item->fixed_amount,
                    'formatted_rate'    => $item->calculation_basis === 'percentage'
                        ? number_format($item->rate, 2) . '%'
                        : '₹' . number_format($item->fixed_amount, 2),
                    'threshold_limit'   => (float) $item->threshold_limit,
                    'formatted_limit'   => $item->threshold_limit > 0
                        ? '₹' . number_format($item->threshold_limit, 2)
                        : 'No Limit',
                    'effective_from'    => $item->effective_from ? $item->effective_from->format('d M, Y') : 'Immediate',
                    'raw_effective_from'=> $item->effective_from ? $item->effective_from->format('Y-m-d') : '',
                    'status'            => $item->status,
                    'description'       => $item->description ?: 'No description provided.',
                    'edit_url'          => route('masters.payroll.taxation.edit', $item->id),
                    'update_url'        => route('masters.payroll.taxation.update', $item->id),
                    'delete_url'        => route('masters.payroll.taxation.destroy', $item->id),
                    'toggle_url'        => route('masters.payroll.taxation.toggle-status', $item->id),
                ];
            });

            return response()->json([
                'success' => true,
                'data'    => $formatted,
            ]);
        }

        // Summary KPI Metrics
        $data['totalTaxRules']       = Taxation::count();
        $data['deductionRules']      = Taxation::where('type', 'deduction')->count();
        $data['employerContribRules']= Taxation::where('type', 'employer_contribution')->count();
        $data['activeTaxRules']      = Taxation::where('status', 'Active')->count();
        $data['taxes']               = Taxation::orderBy('type', 'asc')->orderBy('id', 'asc')->get();

        return view('home.payroll.taxation.index', $data);
    }

    /**
     * Show the form for creating a new taxation rule.
     */
    public function create()
    {
        $data['title'] = "Add Taxation Rule";
        return view('home.payroll.taxation.create', $data);
    }

    /**
     * Store a newly created taxation rule in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tax_name'          => 'required|string|max:150',
            'tax_code'          => 'required|string|max:50|unique:taxations,tax_code',
            'type'              => 'required|in:deduction,employer_contribution,statutory_tax',
            'applicable_on'     => 'required|in:basic,gross,net,taxable_income,ctc',
            'calculation_basis' => 'required|in:percentage,fixed_slab',
            'rate'              => 'nullable|numeric|min:0|max:100',
            'fixed_amount'      => 'nullable|numeric|min:0',
            'threshold_limit'   => 'nullable|numeric|min:0',
            'effective_from'    => 'nullable|date',
            'status'            => 'required|in:Active,Inactive',
            'description'       => 'nullable|string|max:500',
        ]);

        $tax = Taxation::create([
            'tax_name'          => trim($validated['tax_name']),
            'tax_code'          => strtoupper(trim($validated['tax_code'])),
            'type'              => $validated['type'],
            'applicable_on'     => $validated['applicable_on'],
            'calculation_basis' => $validated['calculation_basis'],
            'rate'              => $validated['calculation_basis'] === 'percentage' ? ($validated['rate'] ?? 0) : 0,
            'fixed_amount'      => $validated['calculation_basis'] === 'fixed_slab' ? ($validated['fixed_amount'] ?? 0) : 0,
            'threshold_limit'   => $validated['threshold_limit'] ?? 0,
            'effective_from'    => $validated['effective_from'] ?? null,
            'status'            => $validated['status'],
            'description'       => $validated['description'] ?? null,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Taxation Rule "' . $tax->tax_name . '" created successfully!',
                'data'    => $tax,
            ]);
        }

        return redirect()->route('masters.payroll.taxation')
            ->with('success', 'Taxation Rule created successfully!');
    }

    /**
     * Display the specified tax rule.
     */
    public function show(string $id)
    {
        $tax = Taxation::findOrFail($id);
        return response()->json([
            'success' => true,
            'tax'     => $tax,
        ]);
    }

    /**
     * Show the form for editing the specified taxation rule.
     */
    public function edit(string $id)
    {
        $tax = Taxation::findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'tax'     => $tax,
            ]);
        }

        $data['title'] = "Edit Taxation Rule: " . $tax->tax_name;
        $data['tax']   = $tax;

        return view('home.payroll.taxation.create', $data);
    }

    /**
     * Update the specified taxation rule in storage.
     */
    public function update(Request $request, string $id)
    {
        $tax = Taxation::findOrFail($id);

        $validated = $request->validate([
            'tax_name'          => 'required|string|max:150',
            'tax_code'          => ['required', 'string', 'max:50', Rule::unique('taxations', 'tax_code')->ignore($tax->id)],
            'type'              => 'required|in:deduction,employer_contribution,statutory_tax',
            'applicable_on'     => 'required|in:basic,gross,net,taxable_income,ctc',
            'calculation_basis' => 'required|in:percentage,fixed_slab',
            'rate'              => 'nullable|numeric|min:0|max:100',
            'fixed_amount'      => 'nullable|numeric|min:0',
            'threshold_limit'   => 'nullable|numeric|min:0',
            'effective_from'    => 'nullable|date',
            'status'            => 'required|in:Active,Inactive',
            'description'       => 'nullable|string|max:500',
        ]);

        $tax->update([
            'tax_name'          => trim($validated['tax_name']),
            'tax_code'          => strtoupper(trim($validated['tax_code'])),
            'type'              => $validated['type'],
            'applicable_on'     => $validated['applicable_on'],
            'calculation_basis' => $validated['calculation_basis'],
            'rate'              => $validated['calculation_basis'] === 'percentage' ? ($validated['rate'] ?? 0) : 0,
            'fixed_amount'      => $validated['calculation_basis'] === 'fixed_slab' ? ($validated['fixed_amount'] ?? 0) : 0,
            'threshold_limit'   => $validated['threshold_limit'] ?? 0,
            'effective_from'    => $validated['effective_from'] ?? null,
            'status'            => $validated['status'],
            'description'       => $validated['description'] ?? null,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Taxation Rule "' . $tax->tax_name . '" updated successfully!',
                'data'    => $tax,
            ]);
        }

        return redirect()->route('masters.payroll.taxation')
            ->with('success', 'Taxation Rule updated successfully!');
    }

    /**
     * Remove the specified taxation rule.
     */
    public function destroy(Request $request, string $id)
    {
        $tax = Taxation::findOrFail($id);
        $name = $tax->tax_name;
        $tax->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Taxation rule '{$name}' deleted successfully.",
            ]);
        }

        return redirect()->route('masters.payroll.taxation')
            ->with('success', "Taxation rule '{$name}' deleted successfully.");
    }

    /**
     * Toggle active/inactive status of a tax rule.
     */
    public function toggleStatus(Request $request, string $id)
    {
        $tax = Taxation::findOrFail($id);
        $newStatus = $tax->status === 'Active' ? 'Inactive' : 'Active';
        $tax->status = $newStatus;
        $tax->save();

        return response()->json([
            'success'   => true,
            'newStatus' => $newStatus,
            'message'   => "Status of '{$tax->tax_name}' changed to {$newStatus}.",
        ]);
    }
}

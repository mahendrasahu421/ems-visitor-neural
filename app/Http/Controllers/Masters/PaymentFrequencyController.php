<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\PaymentFrequency;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentFrequencyController extends Controller
{
    /**
     * Display a listing of payroll calculation and disbursement frequencies.
     */
    public function index(Request $request)
    {
        $data['title'] = 'Payroll Payment Frequency';

        // Handle AJAX DataTables query
        if ($request->ajax()) {
            $query = PaymentFrequency::query();

            // Filters
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('cycle_type') && $request->cycle_type !== 'all') {
                match ($request->cycle_type) {
                    'monthly'     => $query->where('cycles_per_year', 12),
                    'semi_monthly'=> $query->where('cycles_per_year', 24),
                    'bi_weekly'   => $query->where('cycles_per_year', 26),
                    'weekly'      => $query->where('cycles_per_year', 52),
                    'daily'       => $query->where('cycles_per_year', 365),
                    default       => null,
                };
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('frequency_name', 'like', "%{$search}%")
                        ->orWhere('frequency_code', 'like', "%{$search}%")
                        ->orWhere('pay_day_rule', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $frequencies = $query->orderBy('is_default', 'desc')
                ->orderBy('days_per_cycle', 'desc')
                ->orderBy('id', 'asc')
                ->get();

            $formatted = $frequencies->map(function ($item, $index) {
                return [
                    'index'           => $index + 1,
                    'id'              => $item->id,
                    'frequency_name'  => $item->frequency_name,
                    'frequency_code'  => $item->frequency_code,
                    'cycles_per_year' => $item->cycles_per_year,
                    'days_per_cycle'  => $item->days_per_cycle,
                    'pay_day_rule'    => $item->pay_day_rule ?: 'End of cycle',
                    'is_default'      => (bool) $item->is_default,
                    'status'          => $item->status,
                    'description'     => $item->description ?: 'Standard payroll cadence.',
                    'edit_url'        => route('masters.payroll.payment-frequency.edit', $item->id),
                    'update_url'      => route('masters.payroll.payment-frequency.update', $item->id),
                    'delete_url'      => route('masters.payroll.payment-frequency.destroy', $item->id),
                    'toggle_url'      => route('masters.payroll.payment-frequency.toggle-status', $item->id),
                    'set_default_url' => route('masters.payroll.payment-frequency.set-default', $item->id),
                ];
            });

            return response()->json([
                'success' => true,
                'data'    => $formatted,
            ]);
        }

        // Summary KPI Metrics
        $data['totalFrequencies'] = PaymentFrequency::count();
        $data['activeFrequencies']= PaymentFrequency::where('status', 'Active')->count();
        $data['defaultFrequency'] = PaymentFrequency::where('is_default', true)->first();
        $data['multiCycleCount']  = PaymentFrequency::where('cycles_per_year', '>=', 24)->count();
        $data['frequencies']      = PaymentFrequency::orderBy('is_default', 'desc')->orderBy('days_per_cycle', 'desc')->get();

        return view('home.payroll.payment-frequency.index', $data);
    }

    /**
     * Show the form for creating a new payment frequency.
     */
    public function create()
    {
        $data['title'] = 'Add Payment Frequency';
        return view('home.payroll.payment-frequency.create', $data);
    }

    /**
     * Store a newly created payment frequency in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'frequency_name'  => 'required|string|max:100',
            'frequency_code'  => 'required|string|max:50|unique:payment_frequencies,frequency_code',
            'cycles_per_year' => 'required|integer|min:1|max:365',
            'days_per_cycle'  => 'required|integer|min:1|max:365',
            'pay_day_rule'    => 'nullable|string|max:150',
            'is_default'      => 'nullable|in:0,1',
            'status'          => 'required|in:Active,Inactive',
            'description'     => 'nullable|string|max:500',
        ]);

        $isDefault = !empty($validated['is_default']) && $validated['is_default'] == '1';

        if ($isDefault) {
            PaymentFrequency::where('is_default', true)->update(['is_default' => false]);
        }

        $frequency = PaymentFrequency::create([
            'frequency_name'  => trim($validated['frequency_name']),
            'frequency_code'  => strtoupper(trim($validated['frequency_code'])),
            'cycles_per_year' => $validated['cycles_per_year'],
            'days_per_cycle'  => $validated['days_per_cycle'],
            'pay_day_rule'    => $validated['pay_day_rule'] ?? null,
            'is_default'      => $isDefault,
            'status'          => $validated['status'],
            'description'     => $validated['description'] ?? null,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment Frequency "' . $frequency->frequency_name . '" created successfully!',
                'data'    => $frequency,
            ]);
        }

        return redirect()->route('masters.payroll.payment-frequency')
            ->with('success', 'Payment Frequency created successfully!');
    }

    /**
     * Display the specified payment frequency.
     */
    public function show(string $id)
    {
        $freq = PaymentFrequency::findOrFail($id);
        return response()->json([
            'success'   => true,
            'frequency' => $freq,
        ]);
    }

    /**
     * Show the form for editing the specified payment frequency.
     */
    public function edit(string $id)
    {
        $frequency = PaymentFrequency::findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success'   => true,
                'frequency' => $frequency,
            ]);
        }

        $data['title']     = 'Edit Payment Frequency: ' . $frequency->frequency_name;
        $data['frequency'] = $frequency;

        return view('home.payroll.payment-frequency.create', $data);
    }

    /**
     * Update the specified payment frequency in storage.
     */
    public function update(Request $request, string $id)
    {
        $frequency = PaymentFrequency::findOrFail($id);

        $validated = $request->validate([
            'frequency_name'  => 'required|string|max:100',
            'frequency_code'  => ['required', 'string', 'max:50', Rule::unique('payment_frequencies', 'frequency_code')->ignore($frequency->id)],
            'cycles_per_year' => 'required|integer|min:1|max:365',
            'days_per_cycle'  => 'required|integer|min:1|max:365',
            'pay_day_rule'    => 'nullable|string|max:150',
            'is_default'      => 'nullable|in:0,1',
            'status'          => 'required|in:Active,Inactive',
            'description'     => 'nullable|string|max:500',
        ]);

        $isDefault = !empty($validated['is_default']) && $validated['is_default'] == '1';

        if ($isDefault && !$frequency->is_default) {
            PaymentFrequency::where('is_default', true)->update(['is_default' => false]);
        }

        $frequency->update([
            'frequency_name'  => trim($validated['frequency_name']),
            'frequency_code'  => strtoupper(trim($validated['frequency_code'])),
            'cycles_per_year' => $validated['cycles_per_year'],
            'days_per_cycle'  => $validated['days_per_cycle'],
            'pay_day_rule'    => $validated['pay_day_rule'] ?? null,
            'is_default'      => $isDefault,
            'status'          => $validated['status'],
            'description'     => $validated['description'] ?? null,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment Frequency "' . $frequency->frequency_name . '" updated successfully!',
                'data'    => $frequency,
            ]);
        }

        return redirect()->route('masters.payroll.payment-frequency')
            ->with('success', 'Payment Frequency updated successfully!');
    }

    /**
     * Remove the specified payment frequency.
     */
    public function destroy(Request $request, string $id)
    {
        $frequency = PaymentFrequency::findOrFail($id);

        if ($frequency->is_default) {
            $msg = 'The active default frequency cannot be deleted. Please set another schedule as default first.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $name = $frequency->frequency_name;
        $frequency->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Payment Frequency '{$name}' deleted successfully.",
            ]);
        }

        return redirect()->route('masters.payroll.payment-frequency')
            ->with('success', "Payment Frequency '{$name}' deleted successfully.");
    }

    /**
     * Toggle active/inactive status of a payment frequency.
     */
    public function toggleStatus(Request $request, string $id)
    {
        $frequency = PaymentFrequency::findOrFail($id);

        if ($frequency->is_default && $frequency->status === 'Active') {
            return response()->json([
                'success' => false,
                'message' => 'The active default frequency cannot be deactivated. Select another default first.',
            ], 422);
        }

        $newStatus = $frequency->status === 'Active' ? 'Inactive' : 'Active';
        $frequency->status = $newStatus;
        $frequency->save();

        return response()->json([
            'success'   => true,
            'newStatus' => $newStatus,
            'message'   => "Status of '{$frequency->frequency_name}' changed to {$newStatus}.",
        ]);
    }

    /**
     * Set a frequency as the organization active default.
     */
    public function setDefault(Request $request, string $id)
    {
        $frequency = PaymentFrequency::findOrFail($id);

        PaymentFrequency::where('is_default', true)->update(['is_default' => false]);

        $frequency->update([
            'is_default' => true,
            'status'     => 'Active',
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "'{$frequency->frequency_name}' is now the default payroll frequency.",
            ]);
        }

        return redirect()->route('masters.payroll.payment-frequency')
            ->with('success', "'{$frequency->frequency_name}' is now the default payroll frequency.");
    }
}

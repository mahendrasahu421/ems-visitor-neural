<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\PaymentMode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentModeController extends Controller
{
    /**
     * Display a listing of payment disbursement channels.
     */
    public function index(Request $request)
    {
        $data['title'] = 'Payroll Payment Modes';

        // AJAX DataTables request
        if ($request->ajax()) {
            $query = PaymentMode::query();

            // Filters
            if ($request->filled('channel_type') && $request->channel_type !== 'all') {
                $query->where('channel_type', $request->channel_type);
            }

            if ($request->filled('requires_bank_details') && $request->requires_bank_details !== 'all') {
                $query->where('requires_bank_details', $request->requires_bank_details == '1');
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('mode_name', 'like', "%{$search}%")
                        ->orWhere('mode_code', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $modes = $query->orderBy('is_default', 'desc')
                ->orderBy('channel_type', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $formatted = $modes->map(function ($item, $index) {
                return [
                    'index'                 => $index + 1,
                    'id'                    => $item->id,
                    'mode_name'             => $item->mode_name,
                    'mode_code'             => $item->mode_code,
                    'channel_type'          => $item->channel_type,
                    'requires_bank_details' => (bool) $item->requires_bank_details,
                    'processing_fee'        => (float) $item->processing_fee,
                    'formatted_fee'         => $item->processing_fee > 0
                        ? '₹' . number_format($item->processing_fee, 2)
                        : 'Free (₹0)',
                    'is_default'            => (bool) $item->is_default,
                    'status'                => $item->status,
                    'description'           => $item->description ?: 'No operational description provided.',
                    'edit_url'              => route('masters.payroll.payment-mode.edit', $item->id),
                    'update_url'            => route('masters.payroll.payment-mode.update', $item->id),
                    'delete_url'            => route('masters.payroll.payment-mode.destroy', $item->id),
                    'toggle_url'            => route('masters.payroll.payment-mode.toggle-status', $item->id),
                    'set_default_url'       => route('masters.payroll.payment-mode.set-default', $item->id),
                ];
            });

            return response()->json([
                'success' => true,
                'data'    => $formatted,
            ]);
        }

        // Summary KPI Metrics
        $data['totalModes']       = PaymentMode::count();
        $data['electronicModes']  = PaymentMode::where('channel_type', 'electronic')->count();
        $data['physicalModes']    = PaymentMode::where('channel_type', 'physical')->count();
        $data['activeModes']      = PaymentMode::where('status', 'Active')->count();
        $data['defaultMode']      = PaymentMode::where('is_default', true)->first();
        $data['modes']            = PaymentMode::orderBy('is_default', 'desc')->orderBy('id', 'asc')->get();

        return view('home.payroll.payment-mode.index', $data);
    }

    /**
     * Show the form for creating a new payment mode.
     */
    public function create()
    {
        $data['title'] = 'Add Payment Mode';
        return view('home.payroll.payment-mode.create', $data);
    }

    /**
     * Store a newly created payment mode in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mode_name'             => 'required|string|max:100',
            'mode_code'             => 'required|string|max:50|unique:payment_modes,mode_code',
            'channel_type'          => 'required|in:electronic,physical',
            'requires_bank_details' => 'nullable|in:0,1',
            'processing_fee'        => 'nullable|numeric|min:0',
            'is_default'            => 'nullable|in:0,1',
            'status'                => 'required|in:Active,Inactive',
            'description'           => 'nullable|string|max:500',
        ]);

        $isDefault = !empty($validated['is_default']) && $validated['is_default'] == '1';

        if ($isDefault) {
            PaymentMode::where('is_default', true)->update(['is_default' => false]);
        }

        $mode = PaymentMode::create([
            'mode_name'             => trim($validated['mode_name']),
            'mode_code'             => strtoupper(trim($validated['mode_code'])),
            'channel_type'          => $validated['channel_type'],
            'requires_bank_details' => !empty($validated['requires_bank_details']) && $validated['requires_bank_details'] == '1',
            'processing_fee'        => $validated['processing_fee'] ?? 0,
            'is_default'            => $isDefault,
            'status'                => $validated['status'],
            'description'           => $validated['description'] ?? null,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment Mode "' . $mode->mode_name . '" created successfully!',
                'data'    => $mode,
            ]);
        }

        return redirect()->route('masters.payroll.payment-mode')
            ->with('success', 'Payment Mode created successfully!');
    }

    /**
     * Display the specified payment mode.
     */
    public function show(string $id)
    {
        $mode = PaymentMode::findOrFail($id);
        return response()->json([
            'success' => true,
            'mode'    => $mode,
        ]);
    }

    /**
     * Show the form for editing the specified payment mode.
     */
    public function edit(string $id)
    {
        $mode = PaymentMode::findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'mode'    => $mode,
            ]);
        }

        $data['title'] = 'Edit Payment Mode: ' . $mode->mode_name;
        $data['mode']  = $mode;

        return view('home.payroll.payment-mode.create', $data);
    }

    /**
     * Update the specified payment mode in storage.
     */
    public function update(Request $request, string $id)
    {
        $mode = PaymentMode::findOrFail($id);

        $validated = $request->validate([
            'mode_name'             => 'required|string|max:100',
            'mode_code'             => ['required', 'string', 'max:50', Rule::unique('payment_modes', 'mode_code')->ignore($mode->id)],
            'channel_type'          => 'required|in:electronic,physical',
            'requires_bank_details' => 'nullable|in:0,1',
            'processing_fee'        => 'nullable|numeric|min:0',
            'is_default'            => 'nullable|in:0,1',
            'status'                => 'required|in:Active,Inactive',
            'description'           => 'nullable|string|max:500',
        ]);

        $isDefault = !empty($validated['is_default']) && $validated['is_default'] == '1';

        if ($isDefault && !$mode->is_default) {
            PaymentMode::where('is_default', true)->update(['is_default' => false]);
        }

        $mode->update([
            'mode_name'             => trim($validated['mode_name']),
            'mode_code'             => strtoupper(trim($validated['mode_code'])),
            'channel_type'          => $validated['channel_type'],
            'requires_bank_details' => !empty($validated['requires_bank_details']) && $validated['requires_bank_details'] == '1',
            'processing_fee'        => $validated['processing_fee'] ?? 0,
            'is_default'            => $isDefault,
            'status'                => $validated['status'],
            'description'           => $validated['description'] ?? null,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment Mode "' . $mode->mode_name . '" updated successfully!',
                'data'    => $mode,
            ]);
        }

        return redirect()->route('masters.payroll.payment-mode')
            ->with('success', 'Payment Mode updated successfully!');
    }

    /**
     * Remove the specified payment mode.
     */
    public function destroy(Request $request, string $id)
    {
        $mode = PaymentMode::findOrFail($id);

        if ($mode->is_default) {
            $msg = 'The active default payment mode cannot be deleted. Please set another mode as default first.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $name = $mode->mode_name;
        $mode->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Payment mode '{$name}' deleted successfully.",
            ]);
        }

        return redirect()->route('masters.payroll.payment-mode')
            ->with('success', "Payment mode '{$name}' deleted successfully.");
    }

    /**
     * Toggle active/inactive status of a payment mode.
     */
    public function toggleStatus(Request $request, string $id)
    {
        $mode = PaymentMode::findOrFail($id);

        if ($mode->is_default && $mode->status === 'Active') {
            return response()->json([
                'success' => false,
                'message' => 'The active default payment mode cannot be deactivated. Set another mode as default first.',
            ], 422);
        }

        $newStatus = $mode->status === 'Active' ? 'Inactive' : 'Active';
        $mode->status = $newStatus;
        $mode->save();

        return response()->json([
            'success'   => true,
            'newStatus' => $newStatus,
            'message'   => "Status of '{$mode->mode_name}' changed to {$newStatus}.",
        ]);
    }

    /**
     * Set a payment mode as the organization active default.
     */
    public function setDefault(Request $request, string $id)
    {
        $mode = PaymentMode::findOrFail($id);

        PaymentMode::where('is_default', true)->update(['is_default' => false]);

        $mode->update([
            'is_default' => true,
            'status'     => 'Active',
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "'{$mode->mode_name}' is now the default salary payment mode.",
            ]);
        }

        return redirect()->route('masters.payroll.payment-mode')
            ->with('success', "'{$mode->mode_name}' is now the default salary payment mode.");
    }
}

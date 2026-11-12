<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\SalaryComponent;
use App\Models\DesignationMaster;
use App\Models\CountryState;
use App\Models\StateCity;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SalaryComponentController extends Controller
{
    /**
     * Display a listing of salary components.
     */
    public function index(Request $request)
    {
        $data['title'] = 'Salary Components Master';

        // AJAX DataTable Request
        if ($request->ajax()) {
            $query = SalaryComponent::with(['designation', 'state', 'city']);

            // Filters
            if ($request->filled('type') && $request->type !== 'all') {
                $query->where('type', $request->type);
            }

            if ($request->filled('calculation_type') && $request->calculation_type !== 'all') {
                $query->where('calculation_type', $request->calculation_type);
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('is_taxable') && $request->is_taxable !== 'all') {
                $query->where('is_taxable', $request->is_taxable == '1' ? 1 : 0);
            }

            // Designation Filter
            if ($request->filled('designation_id') && $request->designation_id !== 'all') {
                if ($request->designation_id === 'global') {
                    $query->whereNull('designation_id');
                } else {
                    $query->where('designation_id', (int) $request->designation_id);
                }
            }

            // State Filter
            if ($request->filled('state_id') && $request->state_id !== 'all') {
                if ($request->state_id === 'global') {
                    $query->whereNull('state_id');
                } else {
                    $query->where('state_id', (int) $request->state_id);
                }
            }

            // City Filter
            if ($request->filled('city_id') && $request->city_id !== 'all') {
                if ($request->city_id === 'global') {
                    $query->whereNull('city_id');
                } else {
                    $query->where('city_id', (int) $request->city_id);
                }
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('component_name', 'like', "%{$search}%")
                        ->orWhere('component_code', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('designation', function ($dq) use ($search) {
                            $dq->where('Designation', 'like', "%{$search}%");
                        })
                        ->orWhereHas('state', function ($sq) use ($search) {
                            $sq->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('city', function ($cq) use ($search) {
                            $cq->where('name', 'like', "%{$search}%");
                        });
                });
            }

            $components = $query->orderBy('type', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $formatted = $components->map(function ($item, $index) {
                $desigText = $item->designation ? $item->designation->Designation : 'All Roles';
                $desigBadge = $item->designation
                    ? '<span class="badge bg-purple-subtle text-purple border border-purple-subtle px-2 py-1 rounded-pill fs-11 fw-semibold"><i class="ti ti-briefcase me-1"></i>' . htmlspecialchars($item->designation->Designation) . '</span>'
                    : '<span class="badge bg-light text-muted border px-2 py-1 rounded-pill fs-11"><i class="ti ti-world me-1"></i>All Roles</span>';

                $stateText = $item->state ? $item->state->name : 'All States';
                $cityText = $item->city ? $item->city->name : 'All Cities';

                if ($item->state && $item->city) {
                    $locBadge = '<span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 rounded-pill fs-11 fw-semibold"><i class="ti ti-map-pin me-1"></i>' . htmlspecialchars($item->state->name) . ' &bull; ' . htmlspecialchars($item->city->name) . '</span>';
                } elseif ($item->state) {
                    $locBadge = '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill fs-11 fw-semibold"><i class="ti ti-map me-1"></i>' . htmlspecialchars($item->state->name) . '</span>';
                } elseif ($item->city) {
                    $locBadge = '<span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 rounded-pill fs-11 fw-semibold"><i class="ti ti-building me-1"></i>' . htmlspecialchars($item->city->name) . '</span>';
                } else {
                    $locBadge = '<span class="badge bg-light text-muted border px-2 py-1 rounded-pill fs-11"><i class="ti ti-map-pins me-1"></i>All Regions</span>';
                }

                return [
                    'index'            => $index + 1,
                    'id'               => $item->id,
                    'component_name'   => $item->component_name,
                    'component_code'   => $item->component_code,
                    'designation_id'   => $item->designation_id,
                    'designation_name' => $desigText,
                    'designation_badge'=> $desigBadge,
                    'state_id'         => $item->state_id,
                    'state_name'       => $stateText,
                    'city_id'          => $item->city_id,
                    'city_name'        => $cityText,
                    'location_badge'   => $locBadge,
                    'type'             => $item->type,
                    'calculation_type' => $item->calculation_type,
                    'percentage'       => (float) $item->percentage,
                    'fixed_amount'     => (float) $item->fixed_amount,
                    'formatted_value'  => $item->calculation_type === 'percentage'
                        ? number_format($item->percentage, 2) . '%'
                        : '₹' . number_format($item->fixed_amount, 2),
                    'is_taxable'       => (bool) $item->is_taxable,
                    'is_statutory'     => (bool) $item->is_statutory,
                    'status'           => $item->status,
                    'description'      => $item->description ?: 'No description provided.',
                    'edit_url'         => route('masters.payroll.salary-component.edit', $item->id),
                    'update_url'       => route('masters.payroll.salary-component.update', $item->id),
                    'delete_url'       => route('masters.payroll.salary-component.destroy', $item->id),
                    'toggle_url'       => route('masters.payroll.salary-component.toggle-status', $item->id),
                    'updated_at'       => $item->updated_at ? $item->updated_at->format('d M, Y') : 'N/A',
                ];
            });

            return response()->json([
                'success' => true,
                'data'    => $formatted,
            ]);
        }

        // Summary KPI Metrics
        $data['totalComponents'] = SalaryComponent::count();
        $data['earningComponents'] = SalaryComponent::where('type', 'earning')->count();
        $data['deductionComponents'] = SalaryComponent::where('type', 'deduction')->count();
        $data['activeComponents'] = SalaryComponent::where('status', 'Active')->count();
        $data['components'] = SalaryComponent::with(['designation', 'state', 'city'])->orderBy('type', 'asc')->orderBy('id', 'asc')->get();
        $data['designations'] = DesignationMaster::orderBy('Designation')->get();
        $data['states'] = CountryState::where('country_id', 101)->orWhereNull('country_id')->orderBy('name')->get(['id', 'name']);

        return view('home.payroll.salary-component.index', $data);
    }

    /**
     * Show the form for creating a new salary component.
     */
    public function create()
    {
        $data['title'] = 'Add Salary Component';
        $data['designations'] = DesignationMaster::orderBy('Designation')->get();
        $data['states'] = CountryState::where('country_id', 101)->orWhereNull('country_id')->orderBy('name')->get(['id', 'name']);
        return view('home.payroll.salary-component.create', $data);
    }

    /**
     * Store a newly created salary component in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'component_name'   => 'required|string|max:150',
            'component_code'   => 'required|string|max:50',
            'designation_id'   => 'nullable|exists:designationmaster,DesignationID',
            'state_id'         => 'nullable|exists:country_states,id',
            'city_id'          => 'nullable|exists:state_cities,id',
            'type'             => 'required|in:earning,deduction',
            'calculation_type' => 'required|in:percentage,fixed',
            'percentage'       => 'nullable|numeric|min:0|max:100',
            'fixed_amount'     => 'nullable|numeric|min:0',
            'is_taxable'       => 'required|in:0,1',
            'is_statutory'     => 'nullable|in:0,1',
            'status'           => 'required|in:Active,Inactive',
            'description'      => 'nullable|string|max:500',
        ]);

        $desigId = !empty($validated['designation_id']) ? (int) $validated['designation_id'] : null;
        $stateId = !empty($validated['state_id']) ? (int) $validated['state_id'] : null;
        $cityId  = !empty($validated['city_id']) ? (int) $validated['city_id'] : null;

        $component = SalaryComponent::create([
            'component_name'   => trim($validated['component_name']),
            'component_code'   => strtoupper(trim($validated['component_code'])),
            'designation_id'   => $desigId,
            'state_id'         => $stateId,
            'city_id'          => $cityId,
            'type'             => $validated['type'],
            'calculation_type' => $validated['calculation_type'],
            'percentage'       => $validated['calculation_type'] === 'percentage' ? ($validated['percentage'] ?? 0) : 0,
            'fixed_amount'     => $validated['calculation_type'] === 'fixed' ? ($validated['fixed_amount'] ?? 0) : 0,
            'is_taxable'       => (bool) $validated['is_taxable'],
            'is_statutory'     => !empty($validated['is_statutory']) ? (bool) $validated['is_statutory'] : false,
            'status'           => $validated['status'],
            'description'      => $validated['description'] ?? null,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Salary Component "' . $component->component_name . '" created successfully!',
                'data'    => $component->load(['designation', 'state', 'city']),
            ]);
        }

        return redirect()->route('masters.payroll.salary-component')
            ->with('success', 'Salary Component created successfully!');
    }

    /**
     * Show the specified resource (or return JSON for drawer).
     */
    public function show(string $id)
    {
        $component = SalaryComponent::with(['designation', 'state', 'city'])->findOrFail($id);
        return response()->json([
            'success'   => true,
            'component' => $component,
        ]);
    }

    /**
     * Show the form for editing the specified salary component.
     */
    public function edit(string $id)
    {
        $component = SalaryComponent::with(['designation', 'state', 'city'])->findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success'   => true,
                'component' => $component,
            ]);
        }

        $data['title'] = 'Edit Salary Component: ' . $component->component_name;
        $data['component'] = $component;
        $data['designations'] = DesignationMaster::orderBy('Designation')->get();
        $data['states'] = CountryState::where('country_id', 101)->orWhereNull('country_id')->orderBy('name')->get(['id', 'name']);

        return view('home.payroll.salary-component.create', $data);
    }

    /**
     * Update the specified salary component in storage.
     */
    public function update(Request $request, string $id)
    {
        $component = SalaryComponent::findOrFail($id);

        $validated = $request->validate([
            'component_name'   => 'required|string|max:150',
            'component_code'   => 'required|string|max:50',
            'designation_id'   => 'nullable|exists:designationmaster,DesignationID',
            'state_id'         => 'nullable|exists:country_states,id',
            'city_id'          => 'nullable|exists:state_cities,id',
            'type'             => 'required|in:earning,deduction',
            'calculation_type' => 'required|in:percentage,fixed',
            'percentage'       => 'nullable|numeric|min:0|max:100',
            'fixed_amount'     => 'nullable|numeric|min:0',
            'is_taxable'       => 'required|in:0,1',
            'is_statutory'     => 'nullable|in:0,1',
            'status'           => 'required|in:Active,Inactive',
            'description'      => 'nullable|string|max:500',
        ]);

        $desigId = !empty($validated['designation_id']) ? (int) $validated['designation_id'] : null;
        $stateId = !empty($validated['state_id']) ? (int) $validated['state_id'] : null;
        $cityId  = !empty($validated['city_id']) ? (int) $validated['city_id'] : null;

        $component->update([
            'component_name'   => trim($validated['component_name']),
            'component_code'   => strtoupper(trim($validated['component_code'])),
            'designation_id'   => $desigId,
            'state_id'         => $stateId,
            'city_id'          => $cityId,
            'type'             => $validated['type'],
            'calculation_type' => $validated['calculation_type'],
            'percentage'       => $validated['calculation_type'] === 'percentage' ? ($validated['percentage'] ?? 0) : 0,
            'fixed_amount'     => $validated['calculation_type'] === 'fixed' ? ($validated['fixed_amount'] ?? 0) : 0,
            'is_taxable'       => (bool) $validated['is_taxable'],
            'is_statutory'     => !empty($validated['is_statutory']) ? (bool) $validated['is_statutory'] : false,
            'status'           => $validated['status'],
            'description'      => $validated['description'] ?? null,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Salary Component "' . $component->component_name . '" updated successfully!',
                'data'    => $component->load(['designation', 'state', 'city']),
            ]);
        }

        return redirect()->route('masters.payroll.salary-component')
            ->with('success', 'Salary Component updated successfully!');
    }

    /**
     * Get cities for a state (AJAX).
     */
    public function getCities($state_id)
    {
        $cities = StateCity::where('state_id', (int) $state_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data'    => $cities,
        ]);
    }

    /**
     * Remove the specified salary component.
     */
    public function destroy(Request $request, string $id)
    {
        $component = SalaryComponent::findOrFail($id);
        $name = $component->component_name;
        $component->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Salary Component '{$name}' deleted successfully.",
            ]);
        }

        return redirect()->route('masters.payroll.salary-component')
            ->with('success', "Salary Component '{$name}' deleted successfully.");
    }

    /**
     * Toggle active/inactive status of a salary component.
     */
    public function toggleStatus(Request $request, string $id)
    {
        $component = SalaryComponent::findOrFail($id);
        $newStatus = $component->status === 'Active' ? 'Inactive' : 'Active';
        $component->status = $newStatus;
        $component->save();

        return response()->json([
            'success'   => true,
            'newStatus' => $newStatus,
            'message'   => "Status of '{$component->component_name}' changed to {$newStatus}.",
        ]);
    }
}

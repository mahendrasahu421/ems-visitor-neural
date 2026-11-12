<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AgencyController extends Controller
{
    /**
     * Display a listing of the agencies.
     */
    public function index(Request $request)
    {
        $data['title'] = 'Agency Master';

        $totalAgencies = Agency::count();
        $activeAgencies = Agency::where('status', 'Active')->count();
        $inactiveAgencies = Agency::where('status', 'Inactive')->count();
        $mappedEmployees = Employee::whereNotNull('agency_id')->whereNull('deleted_at')->count();

        $data['totalAgencies'] = $totalAgencies;
        $data['activeAgencies'] = $activeAgencies;
        $data['inactiveAgencies'] = $inactiveAgencies;
        $data['mappedEmployees'] = $mappedEmployees;
        $data['selectedStatus'] = $request->status ?? '';
        $data['searchKeyword'] = $request->search ?? '';

        $query = Agency::withCount(['employees' => function ($q) {
            $q->whereNull('deleted_at');
        }]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('agency_name', 'like', "%{$search}%")
                  ->orWhere('agency_code', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('state', 'like', "%{$search}%");
            });
        }

        if ($request->ajax()) {
            $agencies = $query->latest()->get();

            $formatted = $agencies->map(function ($agency, $idx) {
                return [
                    'index'          => $idx + 1,
                    'id'             => $agency->id,
                    'agency_name'    => $agency->agency_name,
                    'agency_code'    => $agency->agency_code ?: '-',
                    'contact_person' => $agency->contact_person ?: '-',
                    'email'          => $agency->email ?: '-',
                    'phone'          => $agency->phone ?: '-',
                    'city_state'     => trim(($agency->city ? $agency->city . ', ' : '') . ($agency->state ?: ''), ', ') ?: '-',
                    'status'         => $agency->status,
                    'employee_count' => $agency->employees_count,
                    'address'        => $agency->address ?: '',
                    'notes'          => $agency->notes ?: '',
                ];
            });

            return response()->json(['data' => $formatted]);
        }

        $data['agencies'] = $query->latest()->get();

        return view('masters.agency.index', $data);
    }

    /**
     * Store a newly created agency.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agency_name'    => 'required|string|max:255',
            'agency_code'    => 'nullable|string|max:50|unique:agencies,agency_code',
            'contact_person' => 'nullable|string|max:255',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'nullable|string|max:50',
            'address'        => 'nullable|string',
            'city'           => 'nullable|string|max:100',
            'state'          => 'nullable|string|max:100',
            'status'         => 'required|in:Active,Inactive',
            'notes'          => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $agency = Agency::create($validator->validated());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Agency created successfully!',
                'data'    => $agency,
            ]);
        }

        return redirect()->route('masters.organisation.agency')->with('success', 'Agency created successfully!');
    }

    /**
     * Display details of a specific agency (for modals).
     */
    public function show($id)
    {
        $agency = Agency::withCount(['employees' => function ($q) {
            $q->whereNull('deleted_at');
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'agency'  => $agency,
        ]);
    }

    /**
     * Update an agency.
     */
    public function update(Request $request, $id)
    {
        $agency = Agency::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'agency_name'    => 'required|string|max:255',
            'agency_code'    => 'nullable|string|max:50|unique:agencies,agency_code,' . $id,
            'contact_person' => 'nullable|string|max:255',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'nullable|string|max:50',
            'address'        => 'nullable|string',
            'city'           => 'nullable|string|max:100',
            'state'          => 'nullable|string|max:100',
            'status'         => 'required|in:Active,Inactive',
            'notes'          => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $agency->update($validator->validated());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Agency updated successfully!',
                'data'    => $agency,
            ]);
        }

        return redirect()->route('masters.organisation.agency')->with('success', 'Agency updated successfully!');
    }

    /**
     * Toggle status of an agency.
     */
    public function updateStatus(Request $request, $id)
    {
        $agency = Agency::findOrFail($id);
        $agency->status = $request->status == '1' || $request->status === 'Active' ? 'Active' : 'Inactive';
        $agency->save();

        return response()->json([
            'success' => true,
            'message' => 'Agency status updated successfully!'
        ]);
    }

    /**
     * Remove the specified agency.
     */
    public function destroy($id)
    {
        $agency = Agency::findOrFail($id);

        // Reset agency_id for mapped employees so they aren't orphaned
        Employee::where('agency_id', $id)->update(['agency_id' => null]);

        $agency->delete();

        return response()->json([
            'success' => true,
            'message' => 'Agency deleted successfully!'
        ]);
    }

    /**
     * Show Employee-Agency Mapping screen.
     */
    public function mapping(Request $request)
    {
        $data['title'] = 'Employee Mapping with Agency';
        $data['agencies'] = Agency::orderBy('agency_name')->get();

        $query = Employee::with(['designation', 'agency'])
            ->whereNull('deleted_at');

        // Filter by Agency
        if ($request->filled('filter_agency')) {
            if ($request->filter_agency === 'unassigned') {
                $query->whereNull('agency_id');
            } else {
                $query->where('agency_id', $request->filter_agency);
            }
        }

        // Filter by search term
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('employee_name', 'like', "%{$search}%")
                    ->orWhere('employee_email', 'like', "%{$search}%")
                    ->orWhere('employee_mobile', 'like', "%{$search}%");
            });
        }

        $data['employees'] = $query->orderBy('employee_name')->get();
        $data['totalCount'] = Employee::whereNull('deleted_at')->count();
        $data['mappedCount'] = Employee::whereNotNull('agency_id')->whereNull('deleted_at')->count();
        $data['unmappedCount'] = Employee::whereNull('agency_id')->whereNull('deleted_at')->count();

        return view('masters.agency.mapping', $data);
    }

    /**
     * Save bulk Employee-Agency Mapping.
     */
    public function saveMapping(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,employee_id',
            'target_agency_id' => 'nullable', // null means unassign
        ]);

        $agencyId = $request->target_agency_id;
        if ($agencyId !== null && $agencyId !== '' && $agencyId !== 'unassign') {
            $agency = Agency::findOrFail($agencyId);
            $targetId = $agency->id;
            $msg = count($request->employee_ids) . " employee(s) mapped to {$agency->agency_name} successfully!";
        } else {
            $targetId = null;
            $msg = count($request->employee_ids) . " employee(s) unassigned from agencies successfully!";
        }

        Employee::whereIn('employee_id', $request->employee_ids)
            ->update(['agency_id' => $targetId]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }
}

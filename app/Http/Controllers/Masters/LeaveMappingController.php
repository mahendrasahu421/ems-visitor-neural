<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Designation;
use App\Models\LeaveType;
use App\Models\LeaveMapping;

class LeaveMappingController extends Controller
{
    /**
     * Display a listing of leave allocations per designation.
     */
    public function index()
    {
        $data['title'] = 'Leave Allocation by Designation';
        $data['totalAllocations'] = LeaveMapping::count();
        $data['activeAllocations'] = LeaveMapping::where('status', 'Active')->count();
        $data['totalDesignations'] = Designation::where('status', 'Active')->count();
        $data['totalAllowedDays'] = LeaveMapping::where('status', 'Active')->sum('allow_days');

        $data['designations'] = Designation::where('status', 'Active')->orderBy('name')->get();
        $data['leaveTypes'] = LeaveType::where('status', 'Active')->orderBy('leave_name')->get();

        return view('home.leave-allowed.index', $data);
    }

    /**
     * Show the form for creating a new leave allocation mapping.
     */
    public function create()
    {
        $data['title'] = 'Assign Leave Policy to Designation';
        $data['leaves'] = LeaveType::select('id', 'leave_name', 'leave_code', 'total_leaves', 'leave_category')
            ->where('status', 'Active')
            ->orderBy('leave_name')
            ->get();
        $data['designations'] = Designation::where('status', 'Active')->orderBy('name')->get();

        return view('home.leave-allowed.create', $data);
    }

    /**
     * Store a newly created leave allocation mapping.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'designation_id' => 'required|exists:designations,id',
                'leave_type_id' => 'required|exists:leave_types,id',
                'allow_days' => 'required|integer|min:1',
                'status' => 'required|in:Active,Inactive',
            ]);

            // Duplicate Check
            $exists = LeaveMapping::where('designation_id', $request->designation_id)
                ->where('leave_type_id', $request->leave_type_id)
                ->first();

            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'This leave policy is already assigned to the selected designation.');
            }

            LeaveMapping::create([
                'designation_id' => $request->designation_id,
                'leave_type_id' => $request->leave_type_id,
                'allow_days' => $request->allow_days,
                'status' => $request->status,
            ]);

            return redirect()->route('settings.leave-allow')->with('success', 'Leave allocation assigned successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Return allowed days for selected LeaveType (AJAX).
     */
    public function getDays($id)
    {
        $leave = LeaveType::find($id);

        return response()->json([
            'days' => $leave ? $leave->total_leaves : 0,
            'code' => $leave ? $leave->leave_code : '',
            'category' => $leave ? $leave->leave_category : '',
        ]);
    }

    /**
     * Return LeaveMapping list for DataTable (AJAX).
     */
    public function list(Request $request)
    {
        try {
            $search = $request->input('search.value');
            $limit = $request->input('length', 10);
            $start = $request->input('start', 0);
            $designationFilter = $request->designation_filter;
            $leaveTypeFilter = $request->leave_type_filter;
            $statusFilter = $request->status_filter;

            $query = LeaveMapping::with(['leaveType', 'designation']);

            if ($designationFilter) {
                $query->where('designation_id', $designationFilter);
            }

            if ($leaveTypeFilter) {
                $query->where('leave_type_id', $leaveTypeFilter);
            }

            if ($statusFilter) {
                $query->where('status', $statusFilter);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('leaveType', function ($q1) use ($search) {
                        $q1->where('leave_name', 'like', "%{$search}%")
                            ->orWhere('leave_code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('designation', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
                });
            }

            $totalRecord = LeaveMapping::count();
            $filteredRecord = $query->count();
            $leaveMappings = $query->latest('id')->skip($start)->take($limit)->get();

            $rows = [];
            foreach ($leaveMappings as $index => $mapping) {
                $statusBadge = ($mapping->status === 'Active')
                    ? '<span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fs-11"><i class="ti ti-circle-check me-1"></i>Active</span>'
                    : '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 fs-11"><i class="ti ti-circle-x me-1"></i>Inactive</span>';

                $desigName = $mapping->designation->name ?? 'All Staff';
                $leaveName = $mapping->leaveType->leave_name ?? 'General Leave';
                $leaveCode = $mapping->leaveType->leave_code ?? 'GL';
                $totalMax = $mapping->leaveType->total_leaves ?? $mapping->allow_days;

                $rows[] = [
                    'id' => $mapping->id,
                    'DT_RowIndex' => $start + $index + 1,
                    'designation' => '<div class="d-flex align-items-center gap-1.5"><span class="avatar avatar-xs bg-light text-primary rounded d-inline-flex align-items-center justify-content-center"><i class="ti ti-briefcase fs-12"></i></span><span class="fw-bold text-dark fs-13">' . e($desigName) . '</span></div>',
                    'leave_name' => '<div class="fw-semibold text-dark fs-13">' . e($leaveName) . '</div>',
                    'leave_code' => '<span class="badge bg-light text-primary border font-monospace fs-11">' . e($leaveCode) . '</span>',
                    'allow_days' => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace fs-12 fw-bold">' . $mapping->allow_days . ' Days</span> <span class="fs-11 text-muted ms-1">(Max: ' . $totalMax . ')</span>',
                    'status' => $statusBadge,
                    'action' => '
                        <div class="d-flex align-items-center justify-content-end gap-1 pe-2">
                            <button type="button" class="btn btn-sm btn-outline-danger action-btn" onclick="deleteMapping(' . $mapping->id . ', \'' . addslashes(e($desigName)) . '\', \'' . addslashes(e($leaveName)) . '\')" title="Remove Allocation">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    ',
                ];
            }

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecord,
                'recordsFiltered' => $filteredRecord,
                'data' => $rows,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified leave mapping.
     */
    public function destroy(string $id)
    {
        $mapping = LeaveMapping::find($id);

        if (!$mapping) {
            return response()->json(['success' => false, 'message' => 'Allocation record not found.'], 404);
        }

        try {
            $mapping->delete();
            return response()->json(['success' => true, 'message' => 'Leave allocation removed successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }
}

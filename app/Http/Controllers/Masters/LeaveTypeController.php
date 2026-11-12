<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Company;
use App\Models\Leave;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeaveTypeController extends Controller
{
    /**
     * Display leave type listing page.
     */
    public function index()
    {
        $data['title'] = 'Leave Type Policy & Settings';
        $data['totalLeaveTypes'] = LeaveType::count();
        $data['activeLeaveTypes'] = LeaveType::where('status', 'Active')->count();
        $data['paidLeaveTypes'] = LeaveType::where('leave_category', 'Paid')->count();
        $data['totalQuota'] = LeaveType::where('status', 'Active')->sum('total_leaves');

        return view('home.leave-type.index', $data);
    }

    /**
     * Return LeaveType list for DataTable (AJAX).
     */
    public function list(Request $request)
    {
        try {
            $search = $request->input('search')['value'] ?? null;
            $limit = $request->input('length', 10);
            $start = $request->input('start', 0);
            $categoryFilter = $request->category_filter;
            $statusFilter = $request->status_filter;

            $query = LeaveType::with(['agency', 'company']);

            if ($categoryFilter) {
                $query->where('leave_category', $categoryFilter);
            }

            if ($statusFilter) {
                $query->where('status', $statusFilter);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('leave_name', 'like', "%{$search}%")
                        ->orWhere('leave_code', 'like', "%{$search}%")
                        ->orWhere('leave_category', 'like', "%{$search}%")
                        ->orWhere('applicable_for', 'like', "%{$search}%");
                });
            }

            $totalRecord = LeaveType::count();
            $filteredRecord = $query->count();
            $leaveTypes = $query->latest('id')->skip($start)->take($limit)->get();
            $rows = [];

            foreach ($leaveTypes as $index => $leave) {
                $statusBadge = ($leave->status === 'Active')
                    ? '<span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fs-11"><i class="ti ti-circle-check me-1"></i>Active</span>'
                    : '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 fs-11"><i class="ti ti-circle-x me-1"></i>Inactive</span>';

                $catBadge = match ($leave->leave_category) {
                    'Paid' => '<span class="badge bg-success-subtle text-success border border-success-subtle fs-11">Paid Leave</span>',
                    'Unpaid' => '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle fs-11">Unpaid</span>',
                    'Special' => '<span class="badge bg-purple-subtle text-purple border border-purple-subtle fs-11" style="background-color:#f5f3ff; color:#7c3aed; border-color:#ddd6fe;">Special Leave</span>',
                    default => '<span class="badge bg-light text-dark border fs-11">' . ($leave->leave_category ?? 'General') . '</span>',
                };

                $applicableBadge = match ($leave->applicable_for) {
                    'Male' => '<span class="badge bg-light text-primary border fs-11"><i class="ti ti-gender-male me-1"></i>Male Only</span>',
                    'Female' => '<span class="badge bg-light text-danger border fs-11"><i class="ti ti-gender-female me-1"></i>Female Only</span>',
                    default => '<span class="badge bg-light text-dark border fs-11"><i class="ti ti-users me-1"></i>All Staff</span>',
                };

                $cfBadge = $leave->carry_forward
                    ? '<span class="badge bg-success-subtle text-success border border-success-subtle fs-11"><i class="ti ti-check me-0.5"></i>Yes</span>'
                    : '<span class="badge bg-light text-muted border fs-11">No</span>';

                $encashBadge = $leave->encashable
                    ? '<span class="badge bg-info-subtle text-info border border-info-subtle fs-11"><i class="ti ti-check me-0.5"></i>Yes</span>'
                    : '<span class="badge bg-light text-muted border fs-11">No</span>';

                $rows[] = [
                    'id' => $leave->id,
                    'DT_RowIndex' => $start + $index + 1,
                    'leave_name' => '<div><div class="fw-bold text-dark fs-13">' . e($leave->leave_name) . '</div>' . ($leave->description ? '<div class="text-muted fs-11 text-truncate" style="max-width:200px;">' . e($leave->description) . '</div>' : '') . '</div>',
                    'leave_code' => '<span class="badge bg-light text-primary border font-monospace fs-11 fw-bold">' . e($leave->leave_code) . '</span>',
                    'total_leaves' => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-12 font-monospace fw-bold">' . $leave->total_leaves . ' Days / Yr</span>',
                    'carry_forward' => $cfBadge,
                    'encashable' => $encashBadge,
                    'applicable_for' => $applicableBadge,
                    'leave_category' => $catBadge,
                    'status' => $statusBadge,
                    'action' => '
                        <div class="d-flex align-items-center justify-content-end gap-1 pe-2">
                            <a href="' . route('settings.leave-type.edit', $leave->id) . '" class="btn btn-sm btn-outline-secondary action-btn" title="Edit Leave Type">
                                <i class="ti ti-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger action-btn" onclick="deleteLeave(' . $leave->id . ', \'' . addslashes(e($leave->leave_name)) . '\')" title="Delete Leave Type">
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
                'message' => 'Something went wrong while fetching leave types.',
                'exception' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show create leave type form.
     */
    public function create()
    {
        $data['title'] = 'Create New Leave Policy';
        $data['agencies'] = Agency::where('status', 'Active')->orderBy('agency_name')->get();
        $data['companies'] = Company::all();
        return view('home.leave-type.create', $data);
    }

    /**
     * Store a new leave type.
     */
    public function store(Request $request)
    {
        $agencyId = $request->input('agency_id') ?? $request->input('company_id');
        $defaultCompanyId = Company::value('id') ?? $agencyId;

        $request->merge([
            'leave_code' => strtoupper(trim($request->leave_code ?? '')),
            'agency_id' => $agencyId,
            'company_id' => $request->input('company_id') ?? $defaultCompanyId,
        ]);

        $request->validate([
            'agency_id' => 'required|exists:agencies,id',
            'company_id' => 'nullable|integer',
            'leave_name' => 'required|string|max:255',
            'leave_code' => 'required|string|max:50|unique:leave_types,leave_code',
            'total_leaves' => 'required|integer|min:0',
            'carry_forward' => 'required|in:0,1',
            'encashable' => 'required|in:0,1',
            'applicable_for' => 'required|in:Male,Female,All',
            'leave_category' => 'required|in:Paid,Unpaid,Special',
            'status' => 'required|in:Active,Inactive',
            'description' => 'nullable|string|max:500',
        ]);

        LeaveType::create($request->all());

        return redirect()
            ->route('settings.leave-type')
            ->with('success', 'Leave Type created successfully!');
    }

    /**
     * Show edit leave type form.
     */
    public function edit(string $id)
    {
        $data['title'] = 'Edit Leave Policy';
        $data['leaveType'] = LeaveType::findOrFail($id);
        $data['agencies'] = Agency::where('status', 'Active')->orderBy('agency_name')->get();
        $data['companies'] = Company::all();
        return view('home.leave-type.edit', $data);
    }

    /**
     * Update leave type.
     */
    public function update(Request $request, string $id)
    {
        $leaveType = LeaveType::findOrFail($id);

        $agencyId = $request->input('agency_id') ?? $request->input('company_id') ?? $leaveType->agency_id;
        $defaultCompanyId = $leaveType->company_id ?? Company::value('id') ?? $agencyId;

        $request->merge([
            'leave_code' => strtoupper(trim($request->leave_code ?? '')),
            'agency_id' => $agencyId,
            'company_id' => $request->input('company_id') ?? $defaultCompanyId,
        ]);

        $request->validate([
            'agency_id' => 'required|exists:agencies,id',
            'company_id' => 'nullable|integer',
            'leave_name' => 'required|string|max:255',
            'leave_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('leave_types', 'leave_code')->ignore($leaveType->id)
            ],
            'total_leaves' => 'required|integer|min:0',
            'carry_forward' => 'required|in:0,1',
            'encashable' => 'required|in:0,1',
            'applicable_for' => 'required|in:Male,Female,All',
            'leave_category' => 'required|in:Paid,Unpaid,Special',
            'status' => 'required|in:Active,Inactive',
            'description' => 'nullable|string|max:500',
        ]);

        $leaveType->update($request->all());

        return redirect()
            ->route('settings.leave-type')
            ->with('success', 'Leave Type updated successfully!');
    }

    /**
     * Delete leave type safely.
     */
    public function destroy(string $id)
    {
        $leaveType = LeaveType::find($id);

        if (!$leaveType) {
            return response()->json(['success' => false, 'message' => 'Leave Type not found.'], 404);
        }

        // Check if employee leaves exist under this leave type
        $linkedCount = Leave::where('leave_type_id', $id)->count();
        if ($linkedCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete '{$leaveType->leave_name}'. There are {$linkedCount} employee leave applications linked to this leave type. Please mark it Inactive instead."
            ], 422);
        }

        try {
            $leaveType->delete();
            return response()->json(['success' => true, 'message' => 'Leave Type deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }
}


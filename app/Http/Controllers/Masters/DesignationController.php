<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\DesignationMaster;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DesignationController extends Controller
{
    /**
     * Display a listing of the resource with server-side pagination, search, and KPI metrics.
     */
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $filter = $request->input('filter', 'all'); // 'all', 'assigned', 'unassigned'
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 15, 25, 50, 100])) {
            $perPage = 10;
        }

        // Active Employees assigned to any designation
        $assignedStaff = Employee::whereNull('deleted_at')
            ->whereNotNull('designation_id')
            ->where('designation_id', '>', 0)
            ->count();

        // Total count of designations in system
        $totalDesignations = DesignationMaster::count();

        // Calculate mapped vs vacant designations
        $allDesignationsWithStaff = DesignationMaster::withCount(['employees' => function ($q) {
            $q->whereNull('deleted_at');
        }])->get();

        $usedCount = $allDesignationsWithStaff->where('employees_count', '>', 0)->count();
        $unassignedCount = $allDesignationsWithStaff->where('employees_count', 0)->count();

        // Top designation by staff count
        $topDesignation = $allDesignationsWithStaff->sortByDesc('employees_count')->first();

        // Build query for table pagination
        $query = DesignationMaster::query()
            ->withCount(['employees' => function ($q) {
                $q->whereNull('deleted_at');
            }]);

        // Search by Designation name or ID
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('Designation', 'like', "%{$search}%")
                  ->orWhere('DesignationID', 'like', "%{$search}%");
            });
        }

        // Filter by utilization
        if ($filter === 'assigned') {
            $query->has('employees');
        } elseif ($filter === 'unassigned') {
            $query->doesntHave('employees');
        }

        // Sorting
        $sort = $request->input('sort', 'id_asc');
        switch ($sort) {
            case 'id_desc':
                $query->orderBy('DesignationID', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('Designation', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('Designation', 'desc');
                break;
            case 'staff_desc':
                $query->orderBy('employees_count', 'desc');
                break;
            default:
                $query->orderBy('DesignationID', 'asc');
                break;
        }

        $designations = $query->paginate($perPage)->withQueryString();

        $data = [
            'title' => 'Designation Master',
            'designations' => $designations,
            'totalDesignations' => $totalDesignations,
            'assignedStaff' => $assignedStaff,
            'usedCount' => $usedCount,
            'unassignedCount' => $unassignedCount,
            'topDesignation' => $topDesignation,
            'currentSearch' => $search,
            'currentFilter' => $filter,
            'perPage' => $perPage,
            'currentSort' => $sort,
        ];

        return view('home.designation.index', $data);
    }

    /**
     * DataTables AJAX list fetching directly from designationmaster table (for backwards compatibility)
     */
    public function list(Request $request)
    {
        try {
            $search = $request->input('search')['value'] ?? null;
            $limit = (int) $request->input('length', 10);
            $start = (int) $request->input('start', 0);

            $query = DesignationMaster::query()
                ->withCount(['employees' => function ($q) {
                    $q->whereNull('deleted_at');
                }]);

            $totalRecords = DesignationMaster::count();

            // Search filter
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('Designation', 'like', "%{$search}%")
                      ->orWhere('DesignationID', 'like', "%{$search}%");
                });
            }

            $filteredRecords = $query->count();

            $designations = $query->orderBy('DesignationID', 'asc')
                ->skip($start)
                ->take($limit > 0 ? $limit : 100)
                ->get();

            $rows = [];
            foreach ($designations as $index => $desig) {
                $staffCount = $desig->employees_count ?? 0;

                $formattedDate = '-';
                if (!empty($desig->CreationDate) && $desig->CreationDate !== '0000-00-00 00:00:00') {
                    try {
                        $formattedDate = Carbon::parse($desig->CreationDate)->format('d M, Y');
                    } catch (\Exception $e) {
                        $formattedDate = $desig->CreationDate;
                    }
                }

                $codeBadge = '<span class="badge bg-light text-secondary border font-monospace fs-11">#DESIG-' . str_pad($desig->DesignationID, 3, '0', STR_PAD_LEFT) . '</span>';
                $staffBadge = $staffCount > 0
                    ? '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill fs-11 fw-semibold"><i class="ti ti-users me-1"></i>' . $staffCount . ' Staff</span>'
                    : '<span class="badge bg-light text-muted border px-2 py-1 rounded-pill fs-11"><i class="ti ti-user-x me-1"></i>0 Staff</span>';

                $statusBadge = '<span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-semibold fs-11"><span class="pulse-dot" style="width:7px;height:7px;background:#10b981;border-radius:50%;display:inline-block;margin-right:5px;"></span>Active</span>';

                $escapedTitle = htmlspecialchars($desig->Designation, ENT_QUOTES);

                $actions = '
                    <div class="d-flex align-items-center justify-content-end gap-1 pe-2">
                        <button type="button" class="btn btn-sm btn-outline-success action-btn" onclick="openSalarySetupModal(' . $desig->DesignationID . ', \'' . $escapedTitle . '\', ' . $staffCount . ')" title="Set / Bulk Apply Salary Structure">
                            <i class="ti ti-cash"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary action-btn" onclick="openEditModal(' . $desig->DesignationID . ', \'' . $escapedTitle . '\', ' . $staffCount . ')" title="Edit Designation">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger action-btn" onclick="openDeleteModal(' . $desig->DesignationID . ', \'' . $escapedTitle . '\', ' . $staffCount . ')" title="Delete Designation">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                ';

                $rows[] = [
                    'DT_RowIndex' => $start + $index + 1,
                    'designation_id' => $desig->DesignationID,
                    'designation_code' => $codeBadge,
                    'designation_name' => $desig->Designation ?: '--',
                    'staff_count' => $staffBadge,
                    'creation_date' => '<span class="fs-12 text-muted"><i class="ti ti-calendar me-1"></i>' . $formattedDate . '</span>',
                    'status' => $statusBadge,
                    'action' => $actions,
                ];
            }

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $rows,
            ]);

        } catch (\Exception $e) {
            \Log::error('Designation List Error: ' . $e->getMessage());

            return response()->json([
                'error' => true,
                'message' => 'Something went wrong while fetching designation records: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['title'] = 'Create Designation';
        return view('home.designation.create', $data);
    }

    /**
     * Store a newly created designation in designationmaster table.
     */
    public function store(Request $request)
    {
        $request->validate([
            'designation_name' => 'required_without:name|nullable|string|max:255',
            'name' => 'required_without:designation_name|nullable|string|max:255',
        ]);

        $name = trim($request->input('designation_name') ?: $request->input('name'));

        // Check for duplicates
        $existing = DesignationMaster::where('Designation', $name)->first();
        if ($existing) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Designation '{$name}' already exists (Code: #DESIG-" . str_pad($existing->DesignationID, 3, '0', STR_PAD_LEFT) . ")."
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', "Designation '{$name}' already exists.");
        }

        $newId = DB::table('designationmaster')->insertGetId([
            'Designation' => $name,
            'CreationDate' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Designation created successfully!',
                'id' => $newId,
                'name' => $name,
            ]);
        }

        return redirect()->route('masters.organisation.designation')
            ->with('success', 'Designation created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $designation = DesignationMaster::withCount(['employees' => function ($q) {
            $q->whereNull('deleted_at');
        }])->findOrFail($id);

        $data['title'] = 'Edit Designation';
        $data['designation'] = $designation;

        return view('home.designation.edit', $data);
    }

    /**
     * Update the specified designation in designationmaster table.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'designation_name' => 'required_without:name|nullable|string|max:255',
            'name' => 'required_without:designation_name|nullable|string|max:255',
        ]);

        $name = trim($request->input('designation_name') ?: $request->input('name'));

        // Check for duplicate name excluding current record
        $existing = DesignationMaster::where('Designation', $name)
            ->where('DesignationID', '!=', $id)
            ->first();

        if ($existing) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Another designation named '{$name}' already exists."
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', "Another designation named '{$name}' already exists.");
        }

        DB::table('designationmaster')
            ->where('DesignationID', $id)
            ->update([
                'Designation' => $name,
            ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Designation updated successfully!',
            ]);
        }

        return redirect()->route('masters.organisation.designation')
            ->with('success', 'Designation updated successfully!');
    }

    /**
     * Remove the specified designation from designationmaster table.
     */
    public function destroy(string $id)
    {
        try {
            $staffCount = Employee::where('designation_id', $id)
                ->whereNull('deleted_at')
                ->count();

            if ($staffCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete designation: {$staffCount} employee(s) are actively assigned to it. Please reassign them before deleting."
                ], 422);
            }

            DB::table('designationmaster')
                ->where('DesignationID', $id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Designation deleted successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Designation Delete Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while deleting: ' . $e->getMessage()
            ], 500);
        }
    }
}

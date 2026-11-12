<?php

namespace App\Http\Controllers\AdminHR;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Department;
use App\Models\CountryState;
use App\Models\Shift;
use App\Services\ShiftAttendanceService;
use NunoMaduro\Collision\Adapters\Phpunit\State;
class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $roleName = $user->name ?? 'Admin';
        $title = $roleName . ' Dashboard';
        $imageUrl = "https://ui-avatars.com/api/?name=" . urlencode($roleName) . "&background=0d6efd&color=fff&size=128&bold=true";

        // Metrics & Stats - Active Employees Only
        $activeEmployeesQuery = \App\Models\Employee::whereNull('deleted_at')
            ->where(function ($q) {
                $q->where('status', '1')
                    ->orWhere('status', 'Active')
                    ->orWhereNull('status')
                    ->orWhere('status', '');
            });

        $activeEmployees = (clone $activeEmployeesQuery)->count();
        $totalEmployees = $activeEmployees;
        $inactiveEmployees = \App\Models\Employee::whereNull('deleted_at')->where('status', 'Inactive')->count();
        $activePercentage = 100;

        $totalAgencies = \Illuminate\Support\Facades\Schema::hasTable('agencies')
            ? \App\Models\Agency::where('status', 'Active')->count()
            : 0;
        $agencyEmployees = (clone $activeEmployeesQuery)->whereNotNull('agency_id')->count();
        $directEmployees = max(0, $activeEmployees - $agencyEmployees);

        // Today's Attendances & Punctuality Breakdown
        $activeEmpIds = (clone $activeEmployeesQuery)->pluck('employee_id');
        $todayAttendanceRecords = \Illuminate\Support\Facades\Schema::hasTable('employee_attendances')
            ? \App\Models\EmployeeAttendance::whereDate('attendance_date', today())
                ->whereIn('employee_id', $activeEmpIds)
                ->with(['employee.shift'])
                ->get()
            : collect();

        $todayAttendances = $todayAttendanceRecords->count();
        $todayLateCount = 0;
        $todayEarlyCount = 0;
        $todayEarlyComerCount = 0;
        $todayOnTimeCount = 0;
        $todayAttendanceMap = [];

        foreach ($todayAttendanceRecords as $att) {
            $shift = $att->employee?->shift ?: ShiftAttendanceService::getDefaultShift();
            $eval = ShiftAttendanceService::evaluate($att->check_in_at, $att->check_out_at, $shift, $att->attendance_date);
            if ($eval['is_late']) {
                $todayLateCount++;
            } else {
                $todayOnTimeCount++;
            }
            if (!empty($eval['is_early_in'])) {
                $todayEarlyComerCount++;
            }
            if ($eval['is_early_out']) {
                $todayEarlyCount++;
            }
            $todayAttendanceMap[$att->employee_id] = [
                'check_in_at' => $att->check_in_at ? date('h:i A', strtotime($att->check_in_at)) : null,
                'check_out_at' => $att->check_out_at ? date('h:i A', strtotime($att->check_out_at)) : null,
                'eval' => $eval,
            ];
        }

        $totalClients = \Illuminate\Support\Facades\Schema::hasTable('clientmaster')
            ? \Illuminate\Support\Facades\DB::table('clientmaster')->count()
            : 0;
        $activeClients = \Illuminate\Support\Facades\Schema::hasTable('clientmaster')
            ? \Illuminate\Support\Facades\DB::table('clientmaster')->where('DelStatus', 1)->count()
            : 0;

        $totalProjects = \Illuminate\Support\Facades\Schema::hasTable('client_projects')
            ? \Illuminate\Support\Facades\DB::table('client_projects')->count()
            : 0;
        $totalDepartments = \Illuminate\Support\Facades\Schema::hasTable('departments')
            ? \Illuminate\Support\Facades\DB::table('departments')->whereNull('deleted_at')->count()
            : 0;

        $pendingLeaves = \Illuminate\Support\Facades\Schema::hasTable('leaves')
            ? \Illuminate\Support\Facades\DB::table('leaves')->whereIn('status', ['Pending', 'SENT'])->count()
            : 0;
        $totalInvoices = \Illuminate\Support\Facades\Schema::hasTable('invoice')
            ? \Illuminate\Support\Facades\DB::table('invoice')->count()
            : 0;
        $receivedRevenue = \Illuminate\Support\Facades\Schema::hasTable('recived_payment')
            ? (float) \Illuminate\Support\Facades\DB::table('recived_payment')->where('status', 1)->sum('received_net')
            : 0.0;

        // Recent Active Employees Only
        $recentEmployees = (clone $activeEmployeesQuery)
            ->with(['designation', 'agency', 'shift'])
            ->latest('employee_id')
            ->take(8)
            ->get();

        // Recent Projects
        $recentProjects = \Illuminate\Support\Facades\Schema::hasTable('client_projects')
            ? \Illuminate\Support\Facades\DB::table('client_projects as p')
                ->leftJoin('clientmaster as c', 'p.client_id', '=', 'c.ClientID')
                ->select('p.*', 'c.ClientName')
                ->orderByDesc('p.project_id')
                ->take(5)
                ->get()
            : collect();

        // Recent Leaves
        $recentLeaves = \Illuminate\Support\Facades\Schema::hasTable('leaves')
            ? \Illuminate\Support\Facades\DB::table('leaves as l')
                ->leftJoin('employees as e', 'l.employee_id', '=', 'e.employee_id')
                ->leftJoin('leave_types as lt', 'l.leave_type_id', '=', 'lt.id')
                ->select('l.*', 'e.employee_name', 'e.employee_email', 'lt.leave_name')
                ->orderByDesc('l.id')
                ->take(5)
                ->get()
            : collect();

        // Departments with head counts
        $departments = \Illuminate\Support\Facades\Schema::hasTable('departments')
            ? \Illuminate\Support\Facades\DB::table('departments')->whereNull('deleted_at')->take(5)->get()
            : collect();

        return view('home.dashboard.index', compact(
            'roleName',
            'title',
            'imageUrl',
            'totalEmployees',
            'activeEmployees',
            'inactiveEmployees',
            'activePercentage',
            'totalAgencies',
            'agencyEmployees',
            'directEmployees',
            'todayAttendances',
            'todayLateCount',
            'todayEarlyCount',
            'todayEarlyComerCount',
            'todayOnTimeCount',
            'todayAttendanceMap',
            'totalClients',
            'activeClients',
            'totalProjects',
            'totalDepartments',
            'pendingLeaves',
            'totalInvoices',
            'receivedRevenue',
            'recentEmployees',
            'recentProjects',
            'recentLeaves',
            'departments'
        ));
    }

   
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getDepartments($category_id)
    {
        $departments = Department::where('category_id', $category_id)
            ->select('id', 'department_name')
            ->get();

        return response()->json($departments);
    }
    public function getDesignation($department_id)
    {
        $departments = Designation::where('department_id', $department_id)
            ->select('id', 'designation_name')
            ->get();

        return response()->json($departments);
    }
    public function getState($country_id)
    { 
        try {
            $states = \App\Models\CountryState::where('country_id', $country_id)
                ->select('id', 'name')
                ->orderBy('name', 'asc')
                ->get();

            if ($states->isEmpty()) {
                return response()->json([]);
            }

            return response()->json($states);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }
    public function getcity($state_id)
    {
        try {
            $states = \App\Models\StateCity::where('state_id', $state_id)
                ->select('id', 'name')
                ->orderBy('name', 'asc')
                ->get();

            if ($states->isEmpty()) {
                return response()->json([]);
            }

            return response()->json($states);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }


}

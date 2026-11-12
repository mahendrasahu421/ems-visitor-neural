<?php

namespace App\Http\Controllers\Masters;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Employee;
use \App\Models\Gender;
use \App\Models\MaritalStatus;
class AdminEmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] = 'Employee';
        $data['imageUrl'] = "https://picsum.photos/200/200?random=" . rand(1, 1000);
        return view('home.employee.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['title'] = 'Employee';
        $data['imageUrl'] = "https://picsum.photos/200/200?random=" . rand(1, 1000);
        $data['gender'] = Gender::all();
        $data['company'] = Company::all();
        $data['MaritalStatus'] = MaritalStatus::all();
        return view('home.employee.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function list(Request $request)
    {
        try {
            $search = $request->input('search')['value'] ?? null;
            $limit = $request->input('length', 10);
            $start = $request->input('start', 0);

            // ✅ Use query builder (not ->all())
            $query = Employee::query();

            // ✅ Apply search filter
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('employee_name', 'like', "%{$search}%")
                        ->orWhere('employee_email', 'like', "%{$search}%")
                        ->orWhere('patId', 'like', "%{$search}%")
                        ->orWhere('employee_mobile', 'like', "%{$search}%");
                });
            }

            // ✅ Get total count before pagination
            $totalRecord = $query->count();

            // ✅ Apply pagination
            $employees = $query->skip($start)->take($limit)->get();

            // ✅ Prepare data for DataTable
            $rows = [];
            foreach ($employees as $index => $emp) {
                $rows[] = [
                    'DT_RowIndex' => $start + $index + 1,
                    'name' => $emp->employee_name ?? '--',
                    'patId' => $emp->patId ?? '--',
                    'email' => $emp->employee_email ?? '--',
                    'phone' => $emp->employee_mobile ?? '--',
                    'department' => $emp->department ?? '--',
                    'designation' => $emp->designation ?? '--',
                    'status' => $emp->status == 'Active'
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>',
                    'action' => '<a href="' . url('employee.show', $emp->id) . '" class="btn btn-sm btn-primary">View</a>',
                ];
            }

            // ✅ Return response for DataTable
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecord,
                'recordsFiltered' => $totalRecord,
                'data' => $rows,
            ]);

        } catch (\Exception $e) {
            // ⚠️ Handle errors gracefully
            return response()->json([
                'error' => true,
                'message' => 'Something went wrong while fetching employee records.',
                'exception' => $e->getMessage(),
            ], 500);
        }
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
}

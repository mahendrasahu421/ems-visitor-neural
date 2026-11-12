<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] = 'Master / Organisation / Department';
        $data['imageUrl'] = "https://picsum.photos/200/200?random=" . rand(1, 1000);
        return view('home.department.index', $data);
    }

    /**
     * Return Department list (AJAX DataTable)
     */
    public function list(Request $request)
    {
        try {
            $search = $request->input('search')['value'] ?? null;
            $limit = $request->input('length', 10);
            $start = $request->input('start', 0);

            // ✅ Eager load category relationship
            $query = Department::with('category');

            // ✅ Apply search filter
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('department_name', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($c) use ($search) {
                            $c->where('name', 'like', "%{$search}%");
                        });
                });
            }

            // ✅ Get total count before pagination
            $totalRecord = $query->count();

            // ✅ Apply pagination
            $departments = $query->skip($start)->take($limit)->get();

            // ✅ Prepare data for DataTable
            $rows = [];
            foreach ($departments as $index => $dept) {
                $rows[] = [
                    'DT_RowIndex' => $start + $index + 1,
                    'department_name' => $dept->department_name ?? '--',
                    'category_name' => $dept->category->name ?? '--',
                    'department_code' => $dept->department_code ?? '--',
                    'status' => $dept->status === 'Active'
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>',
                    // 'action' => '<a href="' . url('employee.show', $dept->id) . '" class="btn btn-sm btn-primary">View</a>',
                ];
            }

            // ✅ Return DataTable response
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
                'message' => 'Something went wrong while fetching department records.',
                'exception' => $e->getMessage(),
            ], 500);
        }
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['title'] = 'Master / Organisation / Department Create';
        $data['imageUrl'] = "https://picsum.photos/200/200?random=" . rand(1, 1000);
        $data['category'] = Category::all();
        return view('home.department.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate input data
            $request->validate([
                'company_id' => 'required',
                'category_id' => 'required',
                'department_name' => 'required|string|max:255',
                'department_code' => 'required|string|max:50|unique:departments,department_code',
                'status' => 'required|in:Active,Inactive',
            ]);

            // Create department record
            Department::create([
                'company_id' => $request->company_id,
                'category_id' => $request->category_id,
                'department_name' => $request->department_name,
                'department_code' => $request->department_code,
                'department_head' => $request->department_head,
                'status' => $request->status,
            ]);

            return redirect()
                ->route('masters.organisation.department')
                ->with('success', 'Department created successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors separately
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();

        } catch (\Exception $e) {
            // Handle any other exceptions (like DB or unexpected errors)
            return redirect()->back()
                ->with('error', 'Something went wrong: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['title'] = 'Master / Organisation / Department Edit';
        $data['department'] = Department::findOrFail($id);
        $data['imageUrl'] = "https://picsum.photos/200/200?random=" . rand(1, 1000);
        return view('home.department.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $department = Department::findOrFail($id);

        $request->validate([
            'company_id' => 'required',
            'department_name' => 'required|string|max:255',
            'department_code' => 'required|string|max:50|unique:departments,department_code,' . $department->id,
            'status' => 'required|in:active,inactive',
        ]);

        $department->update([
            'company_id' => $request->company_id,
            'department_name' => $request->department_name,
            'department_code' => $request->department_code,
            'department_head' => $request->department_head,
            'status' => $request->status,
        ]);

        return redirect()->route('masters.organisation.department')
            ->with('success', 'Department updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $department = Department::find($id);

        if (!$department) {
            return response()->json(['success' => false, 'message' => 'Department not found.']);
        }

        try {
            $department->delete();
            return response()->json(['success' => true, 'message' => 'Department deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong.']);
        }
    }
}

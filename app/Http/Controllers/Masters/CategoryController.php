<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] = 'Settings / Category';
        $data['imageUrl'] = "https://picsum.photos/200/200?random=" . rand(1, 1000);
        return view('home.category.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['title'] = 'Settings / Category';
        $data['imageUrl'] = "https://picsum.photos/200/200?random=" . rand(1, 1000);
        return view('home.category.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    // Store category
    public function store(Request $request)
    {
        try {
            // ✅ Validation Rules + Custom Messages
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:Categories,name',
                'status' => 'required',
            ], [
                'name.required' => 'Please enter category name.',
                'name.unique' => 'This category name already exists.',
                'status.required' => 'Please select status (Active or Inactive).',
            ]);

            // ✅ Save Data
            Category::create([
                'name' => $validatedData['name'],
                'status' => $validatedData['status'],
            ]);

            // ✅ Success Toast
            return redirect()->route('settings.category')->with('success', 'Category created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // ❌ Validation error — show first message as toast
            $errorMessage = collect($e->errors())->flatten()->first();
            return back()->with('error', $errorMessage)->withInput();
        } catch (\Exception $e) {
            // ❌ Other runtime or DB errors
            return back()->with('error', 'Something went wrong: ' . $e->getMessage())->withInput();
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

    public function list(Request $request)
    {
        try {
            $search = $request->input('search')['value'] ?? null;
            $limit = $request->input('length', 10);
            $start = $request->input('start', 0);

            // ✅ Use query builder (not ->all())
            $query = Category::query();

            // ✅ Apply search filter
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            }

            // ✅ Get total count before pagination
            $totalRecord = $query->count();

            // ✅ Apply pagination
            $categorys = $query->skip($start)->take($limit)->get();

            // ✅ Prepare data for DataTable
            $rows = [];
            foreach ($categorys as $index => $cat) {
                $rows[] = [
                    'DT_RowIndex' => $start + $index + 1,
                    'name' => $cat->name ?? '--',
                    'status' => $cat->status == 'Active'
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>',
                    'action' => '<a href="' . url('employee.show', $cat->id) . '" class="btn btn-sm btn-primary">View</a>',
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
}

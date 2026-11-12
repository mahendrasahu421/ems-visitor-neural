<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobCategory;

class JobCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['categories'] = JobCategory::latest()->get();
        $data['parents'] = JobCategory::whereNull('parent_id')->get();
        $data['imageUrl'] = "https://picsum.photos/200/200?random=" . rand(1, 1000);
        $data['title'] = 'Jobs Categories';
        return view('hr.job-categories.index', $data);
    }
    public function create()
    {
        $parents = JobCategory::whereNull('parent_id')->get();
        return view('job_categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:job_categories,id',
        ]);

        JobCategory::create($request->all());

        return redirect()->back()->with('success', 'Category created successfully!');
    }

    public function edit($id)
    {
        // Fetch the category.
        $jobCategory = JobCategory::findOrFail($id);

        // Fetch parent categories and exclude the current category.
        $parents = JobCategory::whereNull('parent_id')
            ->where('id', '!=', $jobCategory->id)
            ->get();

        return view('hr.job-categories.edit', [
            'jobCategory' => $jobCategory,
            'parents' => $parents,
            'title' => 'Edit Categories',
            'imageUrl' => "https://picsum.photos/200/200?random=" . rand(1, 1000),
        ]);
    }


    public function update(Request $request, $id)
    {
        // Fetch the category.
        $jobCategory = JobCategory::findOrFail($id);

        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:job_categories,id',
            'status' => 'required|in:Active,Inactive',
        ]);

        // Update fields
        $jobCategory->update([
            'name' => $request->name,
            'type' => $request->type,
            'parent_id' => $request->parent_id,
            'status' => $request->status,
        ]);

        return redirect()->route('hr.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    public function destroy($id)
    {
        $jobCategory = JobCategory::findOrFail($id);
        $jobCategory->delete();

        return redirect()->back()->with('success', 'Category deleted successfully!');
    }


}

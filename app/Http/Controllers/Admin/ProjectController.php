<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProjectController extends Controller
{
    public function index()
    {
        return redirect()->route('invoice.projects');
    }

    public function create()
    {
        return redirect()->route('invoice.projects.create');
    }

    public function store(Request $request)
    {
        return redirect()
            ->route('invoice.projects.create')
            ->with('error', 'Please add projects from this page.');
    }

    public function edit($id)
    {
        return redirect()->route('invoice.projects.edit', $id);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'project_category' => 'required|string|max:255',
            'project_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $project = Project::findOrFail($id);
        $project->project_name = $request->project_name;
        $project->work_type = $request->project_category;
        $project->status = $request->status;
        $project->save();

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    public function list(Request $request)
    {
        $search = $request->input('search.value') ?? null;
        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);

        $query = DB::table('client_projects as p')
            ->leftJoin('clientmaster as c', 'p.client_id', '=', 'c.ClientID')
            ->leftJoin('work_type_master as w', 'p.work_type_id', '=', 'w.WorkTypeID')
            ->select(
                'p.project_id',
                'p.project_name',
                'p.work_type',
                'p.engagement_type',
                'c.ClientName',
                'w.WorkTypeName'
            )
            ->orderByDesc('p.project_id');

        if (Schema::hasColumn('client_projects', 'status')) {
            $query->addSelect('p.status');
        } elseif (Schema::hasColumn('client_projects', 'DelStatus')) {
            $query->addSelect(DB::raw('p.DelStatus as status'));
        } else {
            $query->addSelect(DB::raw('1 as status'));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('c.ClientName', 'like', "%{$search}%")
                    ->orWhere('w.WorkTypeName', 'like', "%{$search}%")
                    ->orWhere('p.work_type', 'like', "%{$search}%")
                    ->orWhere('p.engagement_type', 'like', "%{$search}%")
                    ->orWhere('p.project_name', 'like', "%{$search}%");
            });
        }

        $totalRecord = $query->count();
        $projects = $query->skip($start)->take($limit)->get();

        $rows = [];
        foreach ($projects as $index => $project) {
            $rows[] = [
                'id' => $project->project_id,
                'DT_RowIndex' => $start + $index + 1,
                'client_name' => $project->ClientName ?? '-',
                'project_category' => $project->WorkTypeName ?? $project->work_type ?? $project->engagement_type ?? '-',
                'project_name' => $project->project_name,
                'status' => (int) $project->status === 1
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>',
                'status_raw' => (int) $project->status === 1 ? 'active' : 'inactive'
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecord,
            'recordsFiltered' => $totalRecord,
            'data' => $rows
        ]);
    }

    public function destroy($id)
    {
        $project = Project::find($id);
        if (!$project) {
            return response()->json(['success' => false, 'message' => 'Project not found.']);
        }

        try {
            $project->delete();
            return response()->json(['success' => true, 'message' => 'Project deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong.']);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $status = $request->input('status');

        if (!in_array($status, ['active', 'inactive'])) {
            return response()->json(['success' => false, 'message' => 'Invalid status.']);
        }

        $project->status = $status;
        $project->save();

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }
}

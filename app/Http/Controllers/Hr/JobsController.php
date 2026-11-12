<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobHrms;
use App\Models\JobCategory;
use Illuminate\Support\Facades\Auth;
class JobsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] = 'Add New Job';
        $data['imageUrl'] = "https://picsum.photos/200/200?random=" . rand(1, 1000);


        return view('hr.jobs.index', $data);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['title'] = 'Add New Job';

        // Functional Area (Job Categories)
        $data['categories'] = JobCategory::where('type', 'Industry')
            ->where('status', 'Active')
            ->get();

        // Job Types
        $data['jobCategories'] = JobCategory::where('type', 'Job Type')
            ->where('status', 'Active')
            ->get();


        $data['imageUrl'] = "https://picsum.photos/200/200?random=" . rand(1, 1000);
        // Status options
        $data['statuses'] = ['Active', 'Inactive'];

        return view('hr.jobs.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'category_id' => 'nullable|exists:job_categories,id',
                'job_category_id' => 'nullable|exists:job_categories,id',
                'skills' => 'nullable|array',
                'positions' => 'required|integer|min:1',
                'ctc_from' => 'nullable|numeric',
                'ctc_to' => 'nullable|numeric',
                'min_experience' => 'nullable|integer|min:0',
                'max_experience' => 'nullable|integer|min:0',
                'locations' => 'nullable|array',
                'description' => 'required|string',
                'keywords' => 'nullable|string|max:255',
                'status' => 'required|in:Active,Inactive',
                'share_social' => 'nullable|boolean', // ✅ social share ka option
            ]);

            $job = JobHrms::create([
                'title' => $validated['title'],
                'category_id' => $validated['category_id'] ?? null,
                'job_category_id' => $validated['job_category_id'] ?? null,
                'skills' => $validated['skills'] ?? [],
                'positions' => $validated['positions'],
                'ctc_from' => $validated['ctc_from'] ?? null,
                'ctc_to' => $validated['ctc_to'] ?? null,
                'min_experience' => $validated['min_experience'] ?? null,
                'max_experience' => $validated['max_experience'] ?? null,
                'locations' => $validated['locations'] ?? [],
                'description' => $validated['description'],
                'keywords' => $validated['keywords'] ?? null,
                'status' => $validated['status'],
            ]);

            // Generate share links when the checkbox is selected.
            if ($request->has('share_social')) {
                $url = route('jobs.show', $job->id);
                $title = urlencode($job->title);
                $description = urlencode(\Illuminate\Support\Str::limit($job->description, 100));

                $shareLinks = [
                    'whatsapp' => "https://wa.me/?text={$title}%20{$url}",
                    'facebook' => "https://www.facebook.com/sharer/sharer.php?u={$url}",
                    'linkedin' => "https://www.linkedin.com/shareArticle?mini=true&url={$url}&title={$title}&summary={$description}",
                    'twitter' => "https://twitter.com/intent/tweet?url={$url}&text={$title}",
                ];

                // ✅ Flash session me store karo taaki success page me dikha sake
                return redirect()->back()
                    ->with('success', 'Job created successfully and ready to share!')
                    ->with('shareLinks', $shareLinks);
            }

            return redirect()->back()->with('success', 'Job created successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Job creation failed: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong!');
        }
    }



    /**
     * Display the specified resource.
     */
    public function list(Request $request)
    {
        // echo "working";exit;
        $search = $request->input('search.value');
        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $draw = $request->input('draw');

        $query = JobHrms::query();

        // Search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('keywords', 'like', "%{$search}%");
            });
        }

        $totalRecords = JobHrms::count();
        $filteredRecords = $query->count();

        $jobs = $query->skip($start)
            ->take($limit)
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [];
        foreach ($jobs as $index => $job) {
            $data[] = [
                'sn' => $start + $index + 1,
                'name' => $job->title,
                'salary' => ($job->ctc_from ?? 0) . ' - ' . ($job->ctc_to ?? 0),
                'location' => is_array($job->locations) ? implode(', ', $job->locations) : $job->locations,
                'post' => $job->positions,
                'description' => strip_tags($job->description),
                'status' => ucfirst($job->status),
                'created_at' => $job->created_at->format('d-m-Y'),
                'action' => '<a href="' . route('hr.jobs.edit', $job->id) . '" class="btn btn-sm btn-primary">Edit</a>
                               <form action="' . route('hr.jobs.destroy', $job->id) . '" method="POST" style="display:inline;">
                                   ' . csrf_field() . method_field('DELETE') . '
                                   <button type="submit" onclick="return confirm(\'Are you sure?\')" class="btn btn-sm btn-danger">Delete</button>
                               </form>'
            ];
        }

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
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

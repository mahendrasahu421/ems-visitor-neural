<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] = 'Email Templates';
        $data['imageUrl'] = "https://picsum.photos/200/200?random=" . rand(1, 1000);
        return view('home.setting.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['title'] = 'Create Email Templates';
        $data['compneyDetails'] = Company::select('company_name', 'company_logo', 'address')
            ->where('status', 'Active')
            ->get();

        $data['imageUrl'] = "https://picsum.photos/200/200?random=" . rand(1, 1000);
        return view('home.setting.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'template_key' => 'required|string|unique:email_templates,template_key',
            'subject' => 'required|string|max:255',
            'body' => 'required|string'
        ]);

        try {

            DB::beginTransaction();

            EmailTemplate::create([
                'template_key' => $request->template_key,
                'subject' => $request->subject,
                'body' => $request->body
            ]);

            DB::commit();

            // 🔥 Redirect to list page with success message
            return redirect()
                ->route('settings.email-templates')
                ->with('success', 'Email Template Saved Successfully!');
        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }


    public function list(Request $request)
    {
        try {
            $search = $request->input('search')['value'] ?? null;
            $limit = $request->input('length', 10);
            $start = $request->input('start', 0);

            // Query builder for EmailTemplate
            $query = EmailTemplate::query();

            // Apply search filter (template_key, subject)
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('template_key', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%");
                });
            }

            // Get total count before pagination
            $totalRecord = $query->count();

            // Apply pagination
            $templates = $query->skip($start)->take($limit)->get();

            // Prepare data for DataTable
            $rows = [];
            foreach ($templates as $index => $template) {
                $rows[] = [
                    'DT_RowIndex' => $start + $index + 1,
                    'template_key' => $template->template_key ?? '--',
                    'subject' => $template->subject ?? '--',

                    'created_at' => $template->created_at->format('d-M-Y'),
                    'id' => $template->id,
                ];
            }

            // Return response for DataTable
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecord,
                'recordsFiltered' => $totalRecord,
                'data' => $rows,
            ]);
        } catch (\Exception $e) {
            // Handle errors gracefully
            return response()->json([
                'error' => true,
                'message' => 'Something went wrong while fetching email templates.',
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

    public function preview($id)
    {
        $data['compneyDetails'] = Company::select('company_name', 'company_logo', 'address')
            ->where('status', 'Active')
            ->get();
        $data['template'] = EmailTemplate::select('id', 'template_key', 'subject', 'body')
            ->findOrFail($id);
        $data['title'] = 'Email Preview';
        $data['imageUrl'] = "https://picsum.photos/200/200?random=" . rand(1, 1000);
        $data['template'] = EmailTemplate::findOrFail($id);
        return view('home.setting.preview', $data);
    }
}

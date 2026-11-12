<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\Country; // corrected namespace

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['countries'] = Country::all();
        $data['title'] = 'Masters/Organisation/Branch';
        $data['imageUrl'] = "https://picsum.photos/200/200?random=" . rand(1, 1000);
        return view('home.branch.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */


    public function create()
    {
        $data['title'] = 'Masters/Organisation/Branch/Create';
        $data['imageUrl'] = "https://picsum.photos/200/200?random=" . rand(1, 1000);
        $data['countries'] = Country::all();
        $data['companies'] = Company::all();
        return view('home.branch.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'company_id' => 'required|integer|exists:companies,id',
                'branch_name' => 'required|string|max:255',
                'branch_code' => 'required|string|max:50|unique:branches,branch_code',
                'contact_no' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'address' => 'required|string',
                'country_id' => 'required|exists:countries,id',
                'state_id' => 'required|exists:states,id',
                'city_id' => 'required|exists:cities,id',
                'status' => 'required|in:1,0',
            ]);

            \App\Models\Branch::create([
                'company_id' => $request->company_id,
                'branch_name' => $request->branch_name,
                'branch_code' => $request->branch_code,
                'branch_owner_name' => $request->branch_owner_name,
                'contact_number' => $request->contact_number,
                'gst_number' => $request->gst_number,
                'country' => $request->country,
                'state' => $request->state,
                'city' => $request->city,
                'address_1' => $request->address_1,
                'address_2' => $request->address_2,
                'status' => $request->status,
            ]);

            return redirect()->route('masters.organisation.branch')
                ->with('success', 'Branch created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Branch Store Error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Something went wrong: ' . $e->getMessage())
                ->withInput();
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $branch = \App\Models\Branch::findOrFail($id);
        $countries = Country::all();
        return view('admin.branch.edit', compact('branch', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $branch = \App\Models\Branch::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'address' => 'nullable|string',
        ]);

        $branch->update([
            'name' => $request->name,
            'country_id' => $request->country_id,
            'address' => $request->address,
        ]);

        return redirect()->route('admin.branches.index')->with('success', 'Branch updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $branch = \App\Models\Branch::findOrFail($id);
        $branch->delete();

        return redirect()->route('admin.branches.index')->with('success', 'Branch deleted successfully.');
    }
}

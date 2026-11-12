<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Leave;
class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */



    public function dashboardData()
    {
        $user = Auth::user();

        // Total leaves count
        $totalLeaves = Leave::where('employee_id', $user->id)->count();

        // Approved leaves (taken)
        $taken = Leave::where('employee_id', $user->id)
            ->where('status', 'approved')
            ->count();

        // Pending leave requests
        $pending = Leave::where('employee_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // Absent (for example, rejected)
        $absent = Leave::where('employee_id', $user->id)
            ->where('status', 'rejected')
            ->count();

        // Example static values (you can calculate from attendance later)
        $workedDays = 240;
        $lossOfPay = 2;

        return response()->json([
            'total_leaves' => $totalLeaves,
            'taken' => $taken,
            'absent' => $absent,
            'pending' => $pending,
            'worked_days' => $workedDays,
            'loss_of_pay' => $lossOfPay,
        ]);
    }

    public function index()
    {
        $data['role'] = Auth::user()->name;
        $data['title'] = $data['role'] . ' ' . 'Dashboard';
        $data['imageUrl'] = "https://picsum.photos/200/200?random=" . rand(1, 1000);
        return view('employee.index', $data);
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
}

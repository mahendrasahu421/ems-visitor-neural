<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeIncrement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeAppraisalIncrementController extends Controller
{
    /**
     * Display list of employee's own increments and appraisals.
     */
    public function index()
    {
        $employee = Auth::guard('employee')->user();

        $data['title'] = 'My Annual Increments & Appraisals';
        $data['employee'] = $employee;
        $data['increments'] = EmployeeIncrement::with(['previousDesignation', 'newDesignation'])
            ->where('employee_id', $employee->employee_id)
            ->where('status', 'Applied')
            ->orderBy('effective_date', 'desc')
            ->get();

        $data['currentStructure'] = $employee->salaryStructure;

        return view('employee.increments.index', $data);
    }

    /**
     * Download increment letter for the logged-in employee.
     */
    public function downloadLetter($id)
    {
        $employee = Auth::guard('employee')->user();

        $increment = EmployeeIncrement::with([
            'employee.designation',
            'employee.agency',
            'previousDesignation',
            'newDesignation',
            'previousSalaryStructure',
            'newSalaryStructure',
            'approver',
        ])
        ->where('employee_id', $employee->employee_id)
        ->where('id', $id)
        ->firstOrFail();

        $data['title'] = "Annual Increment Letter";
        $data['increment'] = $increment;
        $data['company_name'] = config('app.name', 'EMS Organization');

        $pdf = Pdf::loadView('payroll.increments.letter_pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);

        $fileName = sprintf('Increment_Letter_%s_%s.pdf', $increment->increment_number, preg_replace('/[^A-Za-z0-9_-]/', '', $increment->employee->employee_name));

        return $pdf->download($fileName);
    }
}

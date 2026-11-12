<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmployeeIncrement;
use App\Models\SalaryStructure;
use App\Services\Payroll\EmployeeIncrementService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmployeeIncrementController extends Controller
{
    protected EmployeeIncrementService $incrementService;

    public function __construct(EmployeeIncrementService $incrementService)
    {
        $this->incrementService = $incrementService;
    }

    /**
     * Display a listing of annual employee increments.
     */
    public function index(Request $request)
    {
        $data['title'] = 'Employee Annual Increments & Salary Revisions';

        if ($request->ajax()) {
            $query = EmployeeIncrement::with(['employee.designation', 'employee.agency', 'previousDesignation', 'newDesignation'])
                ->orderBy('effective_date', 'desc')
                ->orderBy('id', 'desc');

            if ($request->filled('cycle') && $request->cycle !== 'all') {
                $query->where('review_cycle', $request->cycle);
            }

            if ($request->filled('year') && $request->year !== 'all') {
                $query->whereYear('effective_date', (int) $request->year);
            }

            if ($request->filled('employee_id')) {
                $query->where('employee_id', $request->employee_id);
            }

            $increments = $query->get();

            $formatted = $increments->map(function ($item) {
                $emp = $item->employee;
                $empName = $emp ? $emp->employee_name : 'Employee';
                $empEmail = $emp ? ($emp->employee_email ?? '') : '';
                $empImage = $emp ? ($emp->profile_image ?? '') : '';
                $agency = $emp && $emp->agency ? $emp->agency->agency_name : 'Direct Payroll';

                $prevDesig = $item->previousDesignation?->name ?? 'Staff';
                $newDesig = $item->newDesignation?->name ?? $prevDesig;
                $isPromotion = ($item->new_designation_id && $item->previous_designation_id && $item->new_designation_id !== $item->previous_designation_id);

                return [
                    'id'                  => $item->id,
                    'increment_number'    => $item->increment_number,
                    'employee_id'         => $item->employee_id,
                    'employee_name'       => $empName,
                    'employee_email'      => $empEmail,
                    'profile_image'       => $empImage,
                    'agency'              => $agency,
                    'review_cycle'        => $item->review_cycle,
                    'effective_date'      => $item->effective_date ? $item->effective_date->format('d M, Y') : '-',
                    'effective_date_raw'  => $item->effective_date ? $item->effective_date->format('Y-m-d') : '',
                    'current_gross'       => (float) $item->current_gross,
                    'new_gross'           => (float) $item->new_gross,
                    'monthly_diff'        => (float) max(0, $item->new_gross - $item->current_gross),
                    'current_ctc'         => (float) $item->current_ctc,
                    'new_ctc'             => (float) $item->new_ctc,
                    'hike_percentage'     => (float) $item->hike_percentage,
                    'hike_amount'         => (float) $item->hike_amount,
                    'previous_designation'=> $prevDesig,
                    'new_designation'     => $newDesig,
                    'is_promotion'        => $isPromotion,
                    'performance_rating'  => $item->performance_rating ?: 'Satisfactory',
                    'status'              => $item->status,
                    'status_badge'        => $item->status_badge_class,
                    'letter_url'          => route('payroll.increments.letter', $item->id),
                    'download_url'        => route('payroll.increments.download-letter', $item->id),
                    'show_url'            => route('payroll.increments.show', $item->id),
                ];
            });

            return response()->json(['data' => $formatted]);
        }

        // Metrics & KPI Counts
        $data['totalProcessed'] = EmployeeIncrement::where('status', 'Applied')->count();
        $data['avgHikePercentage'] = round((float) EmployeeIncrement::where('status', 'Applied')->avg('hike_percentage'), 1);
        $data['totalAnnualHike'] = (float) EmployeeIncrement::where('status', 'Applied')->sum('hike_amount');
        $data['dueForReviewCount'] = $this->incrementService->getEmployeesDueForReview(12)->count();

        $data['reviewCycles'] = EmployeeIncrement::distinct('review_cycle')->pluck('review_cycle')->filter()->values();
        $data['activeEmployees'] = Employee::active()->orderBy('employee_name')->get(['employee_id', 'employee_name', 'employee_email']);
        $data['designations'] = Designation::where('status', 'Active')->orderBy('name')->get();

        return view('payroll.increments.index', $data);
    }

    /**
     * Show the form for creating a new individual employee increment.
     */
    public function create(Request $request)
    {
        $data['title'] = 'Process Annual Increment & Appraisal';

        $data['employees'] = Employee::active()
            ->with(['designation', 'agency', 'salaryStructure', 'latestIncrement'])
            ->orderBy('employee_name')
            ->get();

        $data['designations'] = Designation::where('status', 'Active')->orderBy('name')->get();
        $data['selectedEmployeeId'] = $request->query('employee_id');

        $selectedEmployee = null;
        if ($data['selectedEmployeeId']) {
            $selectedEmployee = Employee::with(['designation', 'agency', 'salaryStructure', 'latestIncrement'])
                ->where('employee_id', $data['selectedEmployeeId'])
                ->first();
        }
        $data['selectedEmployee'] = $selectedEmployee;

        return view('payroll.increments.create', $data);
    }

    /**
     * AJAX endpoint: calculate real-time preview of increment breakdown.
     */
    public function preview(Request $request)
    {
        $request->validate([
            'employee_id'        => 'required|exists:employees,employee_id',
            'increment_type'     => 'required|in:percentage,fixed_amount,direct_ctc',
            'increment_value'    => 'required|numeric|min:0',
            'new_designation_id' => 'nullable|exists:designations,id',
        ]);

        $employee = Employee::active()
            ->with(['salaryStructure', 'designation'])
            ->where('employee_id', $request->employee_id)
            ->first();

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Active employee not found.'], 404);
        }

        $preview = $this->incrementService->previewIncrement(
            $employee,
            (float) $request->increment_value,
            $request->increment_type,
            $request->filled('new_designation_id') ? (int) $request->new_designation_id : null
        );

        return response()->json([
            'success' => true,
            'data'    => $preview,
        ]);
    }

    /**
     * Store and apply a new increment record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id'          => 'required|exists:employees,employee_id',
            'effective_date'       => 'required|date',
            'review_cycle'         => 'required|string|max:100',
            'increment_type'       => 'required|in:percentage,fixed_amount,direct_ctc',
            'increment_value'      => 'required|numeric|min:0',
            'new_designation_id'   => 'nullable|exists:designations,id',
            'performance_rating'   => 'nullable|string|max:100',
            'remarks'              => 'nullable|string|max:500',
            'letter_notes'         => 'nullable|string|max:1000',
            // Optional component adjustments
            'basic_salary'         => 'nullable|numeric|min:0',
            'hra'                  => 'nullable|numeric|min:0',
            'conveyance_allowance' => 'nullable|numeric|min:0',
            'medical_allowance'    => 'nullable|numeric|min:0',
            'special_allowance'    => 'nullable|numeric|min:0',
            'other_allowance'      => 'nullable|numeric|min:0',
            'pf_employee'          => 'nullable|numeric|min:0',
            'pf_employer'          => 'nullable|numeric|min:0',
            'esi_employee'         => 'nullable|numeric|min:0',
            'esi_employer'         => 'nullable|numeric|min:0',
            'professional_tax'     => 'nullable|numeric|min:0',
            'tds_monthly'          => 'nullable|numeric|min:0',
        ]);

        try {
            $increment = $this->incrementService->createAndApplyIncrement($validated, auth()->id());

            return redirect()->route('payroll.increments.index')
                ->with('success', "Annual Increment #{$increment->increment_number} applied successfully! Revised salary structure is now active.");
        } catch (\Exception $e) {
            Log::error("Failed to process increment: " . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', "Failed to process increment: " . $e->getMessage());
        }
    }

    /**
     * Show detailed increment audit view.
     */
    public function show($id)
    {
        $increment = EmployeeIncrement::with([
            'employee.designation',
            'employee.agency',
            'previousDesignation',
            'newDesignation',
            'previousSalaryStructure',
            'newSalaryStructure',
            'approver',
        ])->findOrFail($id);

        $data['title'] = "Increment Details #{$increment->increment_number}";
        $data['increment'] = $increment;

        return view('payroll.increments.show', $data);
    }

    /**
     * Show official Increment / Appraisal Letter web view.
     */
    public function letter($id)
    {
        $increment = EmployeeIncrement::with([
            'employee.designation',
            'employee.agency',
            'previousDesignation',
            'newDesignation',
            'previousSalaryStructure',
            'newSalaryStructure',
            'approver',
        ])->findOrFail($id);

        $data['title'] = "Annual Increment Letter - {$increment->employee->employee_name}";
        $data['increment'] = $increment;
        $data['company_name'] = config('app.name', 'EMS Organization');

        return view('payroll.increments.letter', $data);
    }

    /**
     * Download official Increment / Appraisal Letter as PDF.
     */
    public function downloadLetter($id)
    {
        $increment = EmployeeIncrement::with([
            'employee.designation',
            'employee.agency',
            'previousDesignation',
            'newDesignation',
            'previousSalaryStructure',
            'newSalaryStructure',
            'approver',
        ])->findOrFail($id);

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

    /**
     * Bulk Increment View / Due Review Center.
     */
    public function bulkForm(Request $request)
    {
        $data['title'] = 'Bulk Annual Increment & Revision Center';
        $data['dueEmployees'] = $this->incrementService->getEmployeesDueForReview(12);
        $data['allEmployees'] = Employee::active()
            ->with(['designation', 'agency', 'salaryStructure', 'latestIncrement'])
            ->orderBy('employee_name')
            ->get();
        $data['agencies'] = Agency::where('status', 'Active')->orderBy('agency_name')->get();
        $data['currentYear'] = date('Y');

        return view('payroll.increments.bulk', $data);
    }

    /**
     * Execute bulk annual increments across selected employees.
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'employee_ids'    => 'required|array|min:1',
            'employee_ids.*'  => 'exists:employees,employee_id',
            'hike_percentage' => 'required|numeric|min:0.1|max:100',
            'effective_date'  => 'required|date',
            'review_cycle'    => 'required|string|max:100',
            'remarks'         => 'nullable|string|max:500',
        ]);

        $result = $this->incrementService->bulkApplyIncrement(
            $request->employee_ids,
            (float) $request->hike_percentage,
            $request->effective_date,
            $request->review_cycle,
            auth()->id(),
            $request->remarks
        );

        if ($result['applied_count'] > 0) {
            $msg = "Bulk increment successfully applied for {$result['applied_count']} employee(s)! Total annual budget increase: ₹" . number_format($result['total_hike_budget'], 2);
            if ($result['skipped_count'] > 0) {
                $msg .= " ({$result['skipped_count']} skipped due to missing active salary structure).";
            }
            return redirect()->route('payroll.increments.index')->with('success', $msg);
        }

        return redirect()->back()
            ->withInput()
            ->with('error', "No increments were applied. " . implode(' ', $result['errors']));
    }

    /**
     * Fetch complete increment history for an employee (JSON for modal / tab).
     */
    public function history($employee_id)
    {
        $employee = Employee::with(['designation'])->findOrFail($employee_id);
        $increments = EmployeeIncrement::with(['previousDesignation', 'newDesignation'])
            ->where('employee_id', $employee_id)
            ->orderBy('effective_date', 'desc')
            ->get();

        return response()->json([
            'success'   => true,
            'employee'  => [
                'employee_id'   => $employee->employee_id,
                'employee_name' => $employee->employee_name,
                'designation'   => $employee->designation?->name,
                'joining_date'  => $employee->joining_date,
            ],
            'increments' => $increments,
        ]);
    }
}

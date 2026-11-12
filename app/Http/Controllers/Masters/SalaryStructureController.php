<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\SalaryStructure;
use App\Services\Payroll\EmployeePayrollImportService;
use Illuminate\Http\Request;

class SalaryStructureController extends Controller
{
    /**
     * Display a listing of employee salary structures.
     */
    public function index(Request $request)
    {
        $data['title'] = 'Employee Salary Structures (CTC Setup)';

        if ($request->ajax()) {
            $query = SalaryStructure::with(['employee.designation', 'employee.agency'])
                ->orderBy('status', 'asc')
                ->orderBy('updated_at', 'desc');

            if ($request->filled('status') && $request->status !== '' && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('agency_id') && $request->agency_id !== '' && $request->agency_id !== 'all') {
                if ($request->agency_id === 'unassigned' || $request->agency_id === 'direct') {
                    $query->whereHas('employee', function ($q) {
                        $q->whereNull('agency_id');
                    });
                } else {
                    $agencyId = $request->agency_id;
                    $query->whereHas('employee', function ($q) use ($agencyId) {
                        $q->where('agency_id', $agencyId);
                    });
                }
            }

            $structures = $query->get();

            $formatted = $structures->map(function ($item) {
                $emp = $item->employee;
                $empName = $emp ? $emp->employee_name : 'Employee';
                $empEmail = $emp ? ($emp->employee_email ?? '') : '';
                $empMobile = $emp ? ($emp->employee_mobile ?? '') : '';
                $empImage = $emp ? ($emp->profile_image ?? '') : '';
                $designation = $emp && $emp->designation ? ($emp->designation->name ?? $emp->designation->Designation ?? 'Staff') : 'Staff';
                $agency = $emp && $emp->agency ? $emp->agency->agency_name : 'Direct / In-House';
                $hasAgency = $emp && !empty($emp->agency_id) && $agency !== 'Direct / In-House';

                $deductions = (float)$item->pf_employee + (float)$item->esi_employee + (float)$item->professional_tax + (float)$item->tds_monthly;
                $estNet = max(0, (float)$item->monthly_gross - $deductions);

                return [
                    'id' => $item->id,
                    'employee_id' => $item->employee_id,
                    'employee_name' => $empName,
                    'employee_email' => $empEmail,
                    'employee_mobile' => $empMobile,
                    'profile_image' => $empImage,
                    'designation' => $designation,
                    'agency' => $agency,
                    'has_agency' => $hasAgency,
                    'ctc_annual' => (float)$item->ctc_annual,
                    'monthly_gross' => (float)$item->monthly_gross,
                    'est_net' => $estNet,
                    'basic_salary' => (float)$item->basic_salary,
                    'hra' => (float)$item->hra,
                    'conveyance_allowance' => (float)$item->conveyance_allowance,
                    'medical_allowance' => (float)$item->medical_allowance,
                    'special_allowance' => (float)$item->special_allowance,
                    'other_allowance' => (float)$item->other_allowance,
                    'bike_allowance' => (float)($item->bike_allowance ?? 1500),
                    'gratuity' => (float)($item->gratuity ?? 0),
                    'pf_employee' => (float)$item->pf_employee,
                    'esi_employee' => (float)$item->esi_employee,
                    'professional_tax' => (float)$item->professional_tax,
                    'tds_monthly' => (float)$item->tds_monthly,
                    'pf_employer' => (float)$item->pf_employer,
                    'esi_employer' => (float)$item->esi_employer,
                    'total_deductions' => $deductions,
                    'bank_name' => $item->bank_name ?: '-',
                    'bank_account_no' => $item->bank_account_no ?: '-',
                    'ifsc_code' => $item->ifsc_code ?: '-',
                    'pan_no' => $item->pan_no ?: '-',
                    'uan_no' => $item->uan_no ?: '-',
                    'payment_mode' => $item->payment_mode ?: 'Bank Transfer',
                    'effective_from' => $item->effective_from ? \Carbon\Carbon::parse($item->effective_from)->format('d M, Y') : 'N/A',
                    'status' => $item->status,
                    'edit_url' => route('masters.payroll.salary-structure.edit', $item->id),
                    'delete_url' => route('masters.payroll.salary-structure.destroy', $item->id),
                ];
            });

            return response()->json(['data' => $formatted]);
        }

        $data['totalEmployees'] = Employee::active()->count();
        $data['configuredCount'] = SalaryStructure::where('status', 'Active')
            ->whereHas('employee', fn($q) => $q->active())
            ->distinct('employee_id')->count('employee_id');
        $data['totalMonthlyGross'] = (float) SalaryStructure::where('status', 'Active')
            ->whereHas('employee', fn($q) => $q->active())
            ->sum('monthly_gross');
        $data['totalAnnualCTC'] = (float) SalaryStructure::where('status', 'Active')
            ->whereHas('employee', fn($q) => $q->active())
            ->sum('ctc_annual');
        $data['agencies'] = \App\Models\Agency::where('status', 'Active')->orderBy('agency_name')->get();

        $data['filterSearch'] = $request->search;
        $data['filterStatus'] = $request->status;
        $data['filterAgencyId'] = $request->agency_id;

        return view('home.payroll.salary-structure.index', $data);
    }

    /**
     * Show the form for creating/assigning a new employee salary structure.
     */
    public function create(Request $request)
    {
        $data['title'] = 'Add / Assign Employee Salary';
        $data['employees'] = Employee::with(['designation', 'agency'])
            ->active()
            ->orderBy('employee_name')
            ->get();
        $data['selectedEmployeeId'] = $request->query('employee_id');
        $data['structure'] = null;

        // If employee_id is passed and already has an active structure, prefill it
        if ($data['selectedEmployeeId']) {
            $data['structure'] = SalaryStructure::where('employee_id', $data['selectedEmployeeId'])
                ->where('status', 'Active')
                ->latest('effective_from')
                ->first();
        }

        return view('home.payroll.salary-structure.create', $data);
    }

    /**
     * Store or update an employee's salary structure.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id'          => 'required|exists:employees,employee_id',
            'effective_from'       => 'required|date',
            'monthly_gross'        => 'required|numeric|min:0',
            'ctc_annual'           => 'required|numeric|min:0',
            'basic_salary'         => 'required|numeric|min:0',
            'hra'                  => 'nullable|numeric|min:0',
            'conveyance_allowance' => 'nullable|numeric|min:0',
            'medical_allowance'    => 'nullable|numeric|min:0',
            'special_allowance'    => 'nullable|numeric|min:0',
            'other_allowance'      => 'nullable|numeric|min:0',
            'bike_allowance'       => 'nullable|numeric|min:0',
            'gratuity'             => 'nullable|numeric|min:0',
            'pf_employee'          => 'nullable|numeric|min:0',
            'pf_employer'          => 'nullable|numeric|min:0',
            'esi_employee'         => 'nullable|numeric|min:0',
            'esi_employer'         => 'nullable|numeric|min:0',
            'professional_tax'     => 'nullable|numeric|min:0',
            'tds_monthly'          => 'nullable|numeric|min:0',
            'bank_name'            => 'nullable|string|max:100',
            'bank_account_no'      => 'nullable|string|max:50',
            'ifsc_code'            => 'nullable|string|max:20',
            'pan_no'               => 'nullable|string|max:20',
            'uan_no'               => 'nullable|string|max:30',
            'pf_no'                => 'nullable|string|max:50',
            'esi_no'               => 'nullable|string|max:50',
            'payment_mode'         => 'required|in:Bank Transfer,Cheque,Cash',
            'status'               => 'required|in:Active,Inactive',
            'remarks'              => 'nullable|string|max:500',
        ]);

        // If setting this structure Active, verify employee is active and mark older structures as Inactive
        if ($validated['status'] === 'Active') {
            $targetEmp = Employee::find($validated['employee_id']);
            if (!$targetEmp || !$targetEmp->isActive()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Cannot set active salary structure for an inactive employee. Please activate the employee first.');
            }

            SalaryStructure::where('employee_id', $validated['employee_id'])
                ->where('status', 'Active')
                ->update(['status' => 'Inactive']);
        }

        SalaryStructure::create([
            'employee_id'          => $validated['employee_id'],
            'effective_from'       => $validated['effective_from'],
            'monthly_gross'        => $validated['monthly_gross'] ?? 0,
            'ctc_annual'           => $validated['ctc_annual'] ?? 0,
            'basic_salary'         => $validated['basic_salary'] ?? 0,
            'hra'                  => $validated['hra'] ?? 0,
            'conveyance_allowance' => $validated['conveyance_allowance'] ?? 0,
            'medical_allowance'    => $validated['medical_allowance'] ?? 0,
            'special_allowance'    => $validated['special_allowance'] ?? 0,
            'other_allowance'      => $validated['other_allowance'] ?? 0,
            'bike_allowance'       => $validated['bike_allowance'] ?? 1500,
            'gratuity'             => $validated['gratuity'] ?? 0,
            'pf_employee'          => $validated['pf_employee'] ?? 0,
            'pf_employer'          => $validated['pf_employer'] ?? 0,
            'esi_employee'         => $validated['esi_employee'] ?? 0,
            'esi_employer'         => $validated['esi_employer'] ?? 0,
            'professional_tax'     => $validated['professional_tax'] ?? 0,
            'tds_monthly'          => $validated['tds_monthly'] ?? 0,
            'bank_name'            => $validated['bank_name'] ?? null,
            'bank_account_no'      => $validated['bank_account_no'] ?? null,
            'ifsc_code'            => $validated['ifsc_code'] ?? null,
            'pan_no'               => $validated['pan_no'] ?? null,
            'uan_no'               => $validated['uan_no'] ?? null,
            'pf_no'                => $validated['pf_no'] ?? null,
            'esi_no'               => $validated['esi_no'] ?? null,
            'payment_mode'         => $validated['payment_mode'],
            'status'               => $validated['status'],
            'remarks'              => $validated['remarks'] ?? null,
        ]);

        return redirect()->route('masters.payroll.salary-structure')
            ->with('success', 'Employee salary structure saved successfully!');
    }

    /**
     * Show the form for editing the specified salary structure.
     */
    public function edit($id)
    {
        $structure = SalaryStructure::with('employee')->findOrFail($id);
        $data['title'] = 'Edit Employee Salary: ' . ($structure->employee->employee_name ?? '');
        $data['employees'] = Employee::with(['designation', 'agency'])
            ->active()
            ->orderBy('employee_name')
            ->get();
        $data['selectedEmployeeId'] = $structure->employee_id;
        $data['structure'] = $structure;

        return view('home.payroll.salary-structure.create', $data);
    }

    /**
     * Update the specified salary structure in storage.
     */
    public function update(Request $request, $id)
    {
        $structure = SalaryStructure::findOrFail($id);

        $validated = $request->validate([
            'employee_id'          => 'required|exists:employees,employee_id',
            'effective_from'       => 'required|date',
            'monthly_gross'        => 'required|numeric|min:0',
            'ctc_annual'           => 'required|numeric|min:0',
            'basic_salary'         => 'required|numeric|min:0',
            'hra'                  => 'nullable|numeric|min:0',
            'conveyance_allowance' => 'nullable|numeric|min:0',
            'medical_allowance'    => 'nullable|numeric|min:0',
            'special_allowance'    => 'nullable|numeric|min:0',
            'other_allowance'      => 'nullable|numeric|min:0',
            'bike_allowance'       => 'nullable|numeric|min:0',
            'gratuity'             => 'nullable|numeric|min:0',
            'pf_employee'          => 'nullable|numeric|min:0',
            'pf_employer'          => 'nullable|numeric|min:0',
            'esi_employee'         => 'nullable|numeric|min:0',
            'esi_employer'         => 'nullable|numeric|min:0',
            'professional_tax'     => 'nullable|numeric|min:0',
            'tds_monthly'          => 'nullable|numeric|min:0',
            'bank_name'            => 'nullable|string|max:100',
            'bank_account_no'      => 'nullable|string|max:50',
            'ifsc_code'            => 'nullable|string|max:20',
            'pan_no'               => 'nullable|string|max:20',
            'uan_no'               => 'nullable|string|max:30',
            'pf_no'                => 'nullable|string|max:50',
            'esi_no'               => 'nullable|string|max:50',
            'payment_mode'         => 'required|in:Bank Transfer,Cheque,Cash',
            'status'               => 'required|in:Active,Inactive',
            'remarks'              => 'nullable|string|max:500',
        ]);

        if ($validated['status'] === 'Active') {
            $targetEmp = Employee::find($validated['employee_id']);
            if (!$targetEmp || !$targetEmp->isActive()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Cannot set active salary structure for an inactive employee. Please activate the employee first.');
            }

            SalaryStructure::where('employee_id', $validated['employee_id'])
                ->where('id', '!=', $structure->id)
                ->update(['status' => 'Inactive']);
        }

        $structure->update([
            'employee_id'          => $validated['employee_id'],
            'effective_from'       => $validated['effective_from'],
            'monthly_gross'        => $validated['monthly_gross'] ?? 0,
            'ctc_annual'           => $validated['ctc_annual'] ?? 0,
            'basic_salary'         => $validated['basic_salary'] ?? 0,
            'hra'                  => $validated['hra'] ?? 0,
            'conveyance_allowance' => $validated['conveyance_allowance'] ?? 0,
            'medical_allowance'    => $validated['medical_allowance'] ?? 0,
            'special_allowance'    => $validated['special_allowance'] ?? 0,
            'other_allowance'      => $validated['other_allowance'] ?? 0,
            'bike_allowance'       => $validated['bike_allowance'] ?? 1500,
            'gratuity'             => $validated['gratuity'] ?? 0,
            'pf_employee'          => $validated['pf_employee'] ?? 0,
            'pf_employer'          => $validated['pf_employer'] ?? 0,
            'esi_employee'         => $validated['esi_employee'] ?? 0,
            'esi_employer'         => $validated['esi_employer'] ?? 0,
            'professional_tax'     => $validated['professional_tax'] ?? 0,
            'tds_monthly'          => $validated['tds_monthly'] ?? 0,
            'bank_name'            => $validated['bank_name'] ?? null,
            'bank_account_no'      => $validated['bank_account_no'] ?? null,
            'ifsc_code'            => $validated['ifsc_code'] ?? null,
            'pan_no'               => $validated['pan_no'] ?? null,
            'uan_no'               => $validated['uan_no'] ?? null,
            'pf_no'                => $validated['pf_no'] ?? null,
            'esi_no'               => $validated['esi_no'] ?? null,
            'payment_mode'         => $validated['payment_mode'],
            'status'               => $validated['status'],
            'remarks'              => $validated['remarks'] ?? null,
        ]);

        return redirect()->route('masters.payroll.salary-structure')
            ->with('success', 'Employee salary structure updated successfully!');
    }

    /**
     * Remove the specified salary structure.
     */
    public function destroy(Request $request, $id)
    {
        $structure = SalaryStructure::findOrFail($id);
        $structure->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Salary structure deleted successfully.'
            ]);
        }

        return redirect()->route('masters.payroll.salary-structure')
            ->with('success', 'Salary structure deleted successfully.');
    }

    /**
     * Handle bulk upload of employees along with their salary and PF/statutory structure.
     */
    public function import(Request $request, EmployeePayrollImportService $importService)
    {
        $request->validate([
            'salary_file' => 'required|file|max:10240|mimes:csv,txt,xlsx,xls',
        ], [
            'salary_file.required' => 'Please select a CSV or Excel file to upload.',
            'salary_file.mimes'    => 'The file must be a valid .csv, .xlsx, or .xls spreadsheet.',
            'salary_file.max'      => 'The file size must not exceed 10MB.',
        ]);

        $file = $request->file('salary_file');
        $result = $importService->import($file, ['source' => 'payroll']);

        if (!$result['success']) {
            $errorPreview = implode('<br>', array_slice($result['errors'], 0, 5));
            return redirect()->route('masters.payroll.salary-structure')
                ->with('error', $result['message'] . (!empty($errorPreview) ? '<br>' . $errorPreview : ''));
        }

        $redirect = redirect()->route('masters.payroll.salary-structure')
            ->with('success', $result['message']);

        if ($result['skipped'] > 0 && !empty($result['errors'])) {
            $errorPreview = implode('<br>', array_slice($result['errors'], 0, 5));
            if (count($result['errors']) > 5) {
                $errorPreview .= '<br>...and ' . (count($result['errors']) - 5) . ' more skipped row(s).';
            }
            $redirect->with('warning', "{$result['skipped']} row(s) were skipped due to validation issues:<br>{$errorPreview}");
        }

        return $redirect;
    }

    /**
     * Download comprehensive sample template with employee, salary, PF, and statutory fields.
     */
    public function importSample(EmployeePayrollImportService $importService)
    {
        return $importService->downloadSampleCsv();
    }

    /**
     * Fetch Designation salary profile, existing template and state breakdown.
     */
    public function getDesignationSalaryProfile($id, \App\Services\Payroll\DesignationSalaryService $designationService)
    {
        try {
            $profile = $designationService->getDesignationProfile((int) $id);
            return response()->json([
                'success' => true,
                'data'    => $profile,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Designation not found: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Save designation salary template and bulk update all employees under this designation.
     */
    public function saveDesignationSalary(Request $request, \App\Services\Payroll\DesignationSalaryService $designationService)
    {
        $validated = $request->validate([
            'designation_id'       => 'required|exists:designationmaster,DesignationID',
            'monthly_gross'        => 'required|numeric|min:0',
            'basic_salary_pct'     => 'nullable|numeric|min:10|max:100',
            'hra_pct'              => 'nullable|numeric|min:0|max:100',
            'conveyance_allowance' => 'nullable|numeric|min:0',
            'medical_allowance'    => 'nullable|numeric|min:0',
            'bike_allowance'       => 'nullable|numeric|min:0',
            'other_allowance'      => 'nullable|numeric|min:0',
            'is_pf_enabled'        => 'nullable',
            'is_esi_enabled'       => 'nullable',
            'is_pt_enabled'        => 'nullable',
            'is_gratuity_enabled'  => 'nullable',
            'apply_state_pt_rule'  => 'nullable',
            'apply_metro_hra_rule' => 'nullable',
            'apply_to_employees'   => 'nullable',
            'effective_from'       => 'nullable|date',
            'remarks'              => 'nullable|string|max:500',
        ]);

        $applyToEmployees = $request->has('apply_to_employees') ? (bool) $request->apply_to_employees : true;

        try {
            $result = $designationService->saveAndApply((int) $validated['designation_id'], $validated, $applyToEmployees);

            $empCount = $result['employees_updated'];
            $desigName = $result['designation_name'];
            $stateSummary = $result['state_summary'];

            $stateDetails = [];
            foreach ($stateSummary as $st => $cnt) {
                $stateDetails[] = "{$cnt} in {$st}";
            }
            $stateText = !empty($stateDetails) ? ' (' . implode(', ', $stateDetails) . ')' : '';

            $msg = "Salary structure successfully configured for [{$desigName}] and automatically updated for {$empCount} active employee(s){$stateText} with state-wise statutory rules!";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'data'    => $result,
                ]);
            }

            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to apply designation salary: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to apply designation salary: ' . $e->getMessage());
        }
    }
}


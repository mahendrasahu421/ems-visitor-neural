<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Models\Payslip;
use App\Models\SalaryStructure;
use App\Services\PayrollService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class EmployeePayrollApiController extends Controller
{
    protected PayrollService $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    /**
     * Get the authenticated employee's active salary structure & CTC breakdown.
     */
    public function salaryStructure(Request $request)
    {
        $employee = $request->user();

        $structure = SalaryStructure::where('employee_id', $employee->employee_id)
            ->where('status', 'Active')
            ->latest('effective_from')
            ->first();

        if (!$structure) {
            return response()->json([
                'success' => false,
                'message' => 'No active salary structure found for this employee.',
                'data' => null,
            ], 404);
        }

        // Calculate statutory annual components
        $annualGross = round($structure->monthly_gross * 12, 2);
        $annualBasic = round($structure->basic_salary * 12, 2);
        $annualHra = round($structure->hra * 12, 2);
        $annualAllowances = round(($structure->conveyance_allowance + $structure->medical_allowance + ($structure->bike_allowance ?? 1500) + $structure->special_allowance + $structure->other_allowance) * 12, 2);
        $annualPf = round(($structure->pf_employee + $structure->pf_employer) * 12, 2);
        $annualEsi = round(($structure->esi_employee + $structure->esi_employer) * 12, 2);
        $annualGratuity = round(($structure->gratuity ?? 0) * 12, 2);

        return response()->json([
            'success' => true,
            'message' => 'Salary structure retrieved successfully.',
            'data' => [
                'employee_id' => $employee->employee_id,
                'employee_name' => $employee->employee_name,
                'designation' => $employee->designation->name ?? $employee->designation->Designation ?? 'Staff',
                'effective_from' => $structure->effective_from ? Carbon::parse($structure->effective_from)->format('d M, Y') : null,
                'ctc_annual' => (float) $structure->ctc_annual,
                'monthly_gross' => (float) $structure->monthly_gross,
                'earnings' => [
                    'basic_salary' => (float) $structure->basic_salary,
                    'hra' => (float) $structure->hra,
                    'conveyance_allowance' => (float) $structure->conveyance_allowance,
                    'medical_allowance' => (float) $structure->medical_allowance,
                    'bike_allowance' => (float) ($structure->bike_allowance ?? 1500),
                    'special_allowance' => (float) $structure->special_allowance,
                    'other_allowance' => (float) $structure->other_allowance,
                    'total_monthly_earnings' => (float) ($structure->basic_salary + $structure->hra + $structure->conveyance_allowance + $structure->medical_allowance + ($structure->bike_allowance ?? 1500) + $structure->special_allowance + $structure->other_allowance),
                ],
                'deductions' => [
                    'pf_employee' => (float) $structure->pf_employee,
                    'esi_employee' => (float) $structure->esi_employee,
                    'professional_tax' => (float) $structure->professional_tax,
                    'tds_monthly' => (float) $structure->tds_monthly,
                    'total_monthly_deductions' => (float) ($structure->pf_employee + $structure->esi_employee + $structure->professional_tax + $structure->tds_monthly),
                ],
                'employer_contributions' => [
                    'pf_employer' => (float) $structure->pf_employer,
                    'esi_employer' => (float) $structure->esi_employer,
                    'gratuity' => (float) ($structure->gratuity ?? 0),
                ],
                'estimated_monthly_net_pay' => (float) max(0, $structure->monthly_gross - ($structure->pf_employee + $structure->esi_employee + $structure->professional_tax + $structure->tds_monthly)),
                'bank_details' => [
                    'bank_name' => $structure->bank_name,
                    'bank_account_no' => $structure->bank_account_no ? 'XXXX-XXXX-' . substr($structure->bank_account_no, -4) : null,
                    'ifsc_code' => $structure->ifsc_code,
                    'pan_no' => $structure->pan_no,
                    'uan_no' => $structure->uan_no,
                    'payment_mode' => $structure->payment_mode,
                ],
                'annual_breakdown' => [
                    'gross' => $annualGross,
                    'basic' => $annualBasic,
                    'hra' => $annualHra,
                    'allowances' => $annualAllowances,
                    'pf' => $annualPf,
                    'esi' => $annualEsi,
                    'gratuity' => $annualGratuity,
                ],
            ],
        ]);
    }

    /**
     * Get list of monthly payslips for the authenticated employee.
     */
    public function payslips(Request $request)
    {
        $employee = $request->user();
        $year = $request->input('year');
        $month = $request->input('month');

        $query = Payslip::where('employee_id', $employee->employee_id)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc');

        if ($year) {
            $query->where('year', (int) $year);
        }

        if ($month) {
            $query->where('month', (int) $month);
        }

        $payslips = $query->get()->map(function ($slip) {
            return [
                'id' => $slip->id,
                'payslip_number' => $slip->payslip_number,
                'month' => $slip->month,
                'month_name' => Carbon::createFromDate($slip->year, $slip->month, 1)->format('F'),
                'year' => $slip->year,
                'period' => Carbon::createFromDate($slip->year, $slip->month, 1)->format('F Y'),
                'payable_days' => (float) $slip->payable_days,
                'total_days' => $slip->total_days,
                'total_earnings' => (float) $slip->total_earnings,
                'total_deductions' => (float) $slip->total_deductions,
                'net_salary' => (float) $slip->net_salary,
                'payment_status' => $slip->payment_status,
                'payment_date' => $slip->payment_date ? Carbon::parse($slip->payment_date)->format('d M, Y') : null,
                'pdf_url' => url('/api/v1/employee/payroll/payslips/' . $slip->id . '/download'),
                'created_at' => $slip->created_at ? $slip->created_at->toISOString() : null,
            ];
        });

        // Summary metrics for employee dashboard
        $latestPayslip = $payslips->first();
        $currentYear = (int) ($year ?: Carbon::now()->year);
        $yearPayslips = $payslips->where('year', $currentYear);

        return response()->json([
            'success' => true,
            'message' => 'Payslips fetched successfully.',
            'summary' => [
                'latest_net_pay' => $latestPayslip ? $latestPayslip['net_salary'] : 0,
                'latest_month' => $latestPayslip ? $latestPayslip['period'] : null,
                'current_year_net_earned' => (float) $yearPayslips->sum('net_salary'),
                'current_year_deductions' => (float) $yearPayslips->sum('total_deductions'),
                'total_payslips_available' => $payslips->count(),
            ],
            'data' => $payslips->values(),
        ]);
    }

    /**
     * Get single payslip details by ID.
     */
    public function show(Request $request, $id)
    {
        $employee = $request->user();

        $payslip = Payslip::where('id', $id)
            ->where('employee_id', $employee->employee_id)
            ->first();

        if (!$payslip) {
            return response()->json([
                'success' => false,
                'message' => 'Payslip not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payslip details retrieved successfully.',
            'data' => [
                'id' => $payslip->id,
                'payslip_number' => $payslip->payslip_number,
                'month' => $payslip->month,
                'month_name' => Carbon::createFromDate($payslip->year, $payslip->month, 1)->format('F'),
                'year' => $payslip->year,
                'period' => Carbon::createFromDate($payslip->year, $payslip->month, 1)->format('F Y'),
                
                // Attendance Breakdown
                'attendance' => [
                    'total_days' => $payslip->total_days,
                    'present_days' => (float) $payslip->present_days,
                    'paid_leave_days' => (float) $payslip->paid_leave_days,
                    'holiday_days' => (float) $payslip->holiday_days,
                    'weekly_off_days' => (float) $payslip->weekly_off_days,
                    'lwp_days' => (float) $payslip->lwp_days,
                    'payable_days' => (float) $payslip->payable_days,
                ],

                // Earnings Breakdown
                'earnings' => [
                    'base_gross' => (float) $payslip->base_gross,
                    'earned_basic' => (float) $payslip->earned_basic,
                    'earned_hra' => (float) $payslip->earned_hra,
                    'earned_conveyance' => (float) $payslip->earned_conveyance,
                    'earned_medical' => (float) $payslip->earned_medical,
                    'earned_bike_allowance' => (float) ($payslip->earned_bike_allowance ?? 1500),
                    'earned_special_allowance' => (float) $payslip->earned_special_allowance,
                    'bonus' => (float) $payslip->bonus,
                    'incentive' => (float) $payslip->incentive,
                    'overtime_pay' => (float) $payslip->overtime_pay,
                    'total_earnings' => (float) $payslip->total_earnings,
                ],

                // Deductions Breakdown
                'deductions' => [
                    'pf_deduction' => (float) $payslip->pf_deduction,
                    'esi_deduction' => (float) $payslip->esi_deduction,
                    'pt_deduction' => (float) $payslip->pt_deduction,
                    'tds_deduction' => (float) $payslip->tds_deduction,
                    'lwp_deduction' => (float) $payslip->lwp_deduction,
                    'other_deductions' => (float) $payslip->other_deductions,
                    'total_deductions' => (float) $payslip->total_deductions,
                ],

                // Statutory Accrual (Employer retiral provision / CTC benefit)
                'statutory_accrual' => [
                    'gratuity_accrual' => (float) ($payslip->gratuity_accrual ?? 0),
                ],

                // Net Pay
                'net_salary' => (float) $payslip->net_salary,
                'net_salary_in_words' => $payslip->net_salary_in_words,

                // Bank Details Snapshot
                'bank_details' => [
                    'bank_name' => $payslip->bank_name,
                    'bank_account_no' => $payslip->bank_account_no ? 'XXXX-'.substr($payslip->bank_account_no, -4) : null,
                    'ifsc_code' => $payslip->ifsc_code,
                    'pan_no' => $payslip->pan_no,
                    'uan_no' => $payslip->uan_no,
                    'payment_mode' => $payslip->payment_mode,
                ],

                // Payment Meta
                'payment_status' => $payslip->payment_status,
                'payment_date' => $payslip->payment_date ? Carbon::parse($payslip->payment_date)->format('d M, Y') : null,
                'transaction_reference' => $payslip->transaction_reference,
                'pdf_url' => url('/api/v1/employee/payroll/payslips/' . $payslip->id . '/download'),
            ],
        ]);
    }

    /**
     * Download Payslip PDF.
     */
    public function downloadPdf(Request $request, $id)
    {
        $employee = $request->user();

        $payslip = Payslip::where('id', $id)
            ->where('employee_id', $employee->employee_id)
            ->first();

        if (!$payslip) {
            return response()->json([
                'success' => false,
                'message' => 'Payslip not found.',
            ], 404);
        }

        $filename = sprintf('payslip_%d_%d_%02d.pdf', $payslip->employee_id, $payslip->year, $payslip->month);
        $fullPath = storage_path('app/public/payslips/' . $filename);

        // If PDF doesn't exist on disk yet, generate it on demand
        if (!File::exists($fullPath)) {
            $relPath = $this->payrollService->generatePayslipPdf($payslip);
            $payslip->update(['pdf_path' => $relPath]);
        }

        return Response::download($fullPath, "Payslip_{$payslip->payslip_number}.pdf", [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Annual Financial Summary (Form-16 helper / YTD Overview).
     */
    public function annualSummary(Request $request)
    {
        $employee = $request->user();
        $year = (int) $request->input('year', Carbon::now()->year);

        $payslips = Payslip::where('employee_id', $employee->employee_id)
            ->where('year', $year)
            ->get();

        $structure = SalaryStructure::where('employee_id', $employee->employee_id)
            ->where('status', 'Active')
            ->latest('effective_from')
            ->first();

        $ytdGross = (float) $payslips->sum('total_earnings');
        $ytdNet = (float) $payslips->sum('net_salary');
        $ytdDeductions = (float) $payslips->sum('total_deductions');
        $ytdPf = (float) $payslips->sum('pf_deduction');
        $ytdPt = (float) $payslips->sum('pt_deduction');
        $ytdTds = (float) $payslips->sum('tds_deduction');
        $ytdLwpDays = (float) $payslips->sum('lwp_days');

        return response()->json([
            'success' => true,
            'message' => "Annual payroll summary for year {$year} retrieved successfully.",
            'data' => [
                'year' => $year,
                'employee_id' => $employee->employee_id,
                'ctc_annual' => $structure ? (float) $structure->ctc_annual : 0,
                'months_processed' => $payslips->count(),
                'ytd_gross_earnings' => $ytdGross,
                'ytd_net_salary' => $ytdNet,
                'ytd_total_deductions' => $ytdDeductions,
                'statutory_contributions' => [
                    'pf_total' => $ytdPf,
                    'pt_total' => $ytdPt,
                    'tds_total' => $ytdTds,
                ],
                'ytd_lwp_days' => $ytdLwpDays,
            ],
        ]);
    }
}

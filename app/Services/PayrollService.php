<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\PayrollBatch;
use App\Models\PayrollWorkingDaysSetting;
use App\Models\Payslip;
use App\Models\SalaryStructure;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PayrollService
{
    /**
     * Calculate monthly payroll breakdown for an individual employee.
     */
    public function calculateEmployeePayroll(Employee $employee, int $month, int $year, array $adjustments = []): array
    {
        if (!$employee->isActive()) {
            throw new \Exception("Cannot calculate payroll for inactive employee: {$employee->employee_name} (ID: {$employee->employee_id})");
        }

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->endOfDay();
        $totalDays = $startDate->daysInMonth;

        // Fetch active salary structure
        $structure = SalaryStructure::where('employee_id', $employee->employee_id)
            ->where('status', 'Active')
            ->where('effective_from', '<=', $endDate->toDateString())
            ->orderBy('effective_from', 'desc')
            ->first();

        if (!$structure) {
            throw new \Exception("Active salary structure not found for employee: {$employee->employee_name} (ID: {$employee->employee_id})");
        }

        // Fetch active working days calculation rule master
        $workingDaysRule = PayrollWorkingDaysSetting::getActiveSetting();

        // 1. Calculate Weekly Offs based on configured pattern
        $weeklyOffDays = 0;
        $weeklyOffDates = [];
        $saturdayCount = 0;

        for ($d = 1; $d <= $totalDays; $d++) {
            $curr = Carbon::createFromDate($year, $month, $d);
            $isWeeklyOff = false;

            if ($workingDaysRule->weekly_off_pattern === 'sunday_only') {
                if ($curr->isSunday()) {
                    $isWeeklyOff = true;
                }
            } elseif ($workingDaysRule->weekly_off_pattern === 'saturday_sunday') {
                if ($curr->isSunday() || $curr->isSaturday()) {
                    $isWeeklyOff = true;
                }
            } elseif ($workingDaysRule->weekly_off_pattern === 'alternate_saturday_sunday') {
                if ($curr->isSunday()) {
                    $isWeeklyOff = true;
                } elseif ($curr->isSaturday()) {
                    $saturdayCount++;
                    if ($saturdayCount === 2 || $saturdayCount === 4) {
                        $isWeeklyOff = true;
                    }
                }
            } else {
                if ($curr->isSunday()) {
                    $isWeeklyOff = true;
                }
            }

            if ($isWeeklyOff) {
                $weeklyOffDays++;
                $weeklyOffDates[] = $curr->toDateString();
            }
        }

        // 2. Calculate Holidays (excluding weekly off days)
        $holidayDates = DB::table('holidays')
            ->whereBetween('holiday_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->pluck('holiday_date')
            ->toArray();

        $holidayDays = 0;
        foreach ($holidayDates as $hDate) {
            if (!in_array($hDate, $weeklyOffDates)) {
                $holidayDays++;
            }
        }

        // 3. Calculate Present Days from Attendance
        $presentDays = DB::table('employee_attendances')
            ->where('employee_id', $employee->employee_id)
            ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->where(function ($query) {
                $query->whereNotNull('check_in_at')
                      ->orWhereIn('status', ['checked_in', 'checked_out', 'present', 'PRESENT']);
            })
            ->distinct('attendance_date')
            ->count('attendance_date');

        // 4. Calculate Approved Paid Leaves in the month
        $approvedLeaves = DB::table('leaves')
            ->where('employee_id', $employee->employee_id)
            ->where('status', 'APPROVED')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('from_date', [$startDate->toDateString(), $endDate->toDateString()])
                      ->orWhereBetween('to_date', [$startDate->toDateString(), $endDate->toDateString()])
                      ->orWhere(function ($q2) use ($startDate, $endDate) {
                          $q2->where('from_date', '<=', $startDate->toDateString())
                             ->where('to_date', '>=', $endDate->toDateString());
                      });
            })
            ->get();

        $paidLeaveDays = 0;
        foreach ($approvedLeaves as $leave) {
            $lStart = Carbon::parse($leave->from_date)->max($startDate);
            $lEnd = Carbon::parse($leave->to_date)->min($endDate);
            $days = $lStart->diffInDays($lEnd) + 1;
            $paidLeaveDays += $days;
        }

        // Cap leaves and present days logically
        $workingDays = max(0, $totalDays - $weeklyOffDays - $holidayDays);
        $attendedOrApproved = $presentDays + $paidLeaveDays;

        $lwpDays = 0;
        if ($attendedOrApproved < $workingDays) {
            $lwpDays = $workingDays - $attendedOrApproved;
        }

        // 5. Working Days Basis & Per-Day Salary Calculation
        $baseGross = (float) $structure->monthly_gross;

        $divisorDays = match ($workingDaysRule->calculation_basis) {
            'fixed_26_days' => 26,
            'fixed_30_days' => 30,
            'actual_working_days' => max(1, $workingDays),
            'calendar_days' => max(1, $totalDays),
            default => max(1, $totalDays),
        };

        // Per Day Salary & LWP Deduction Formula:
        $perDaySalary = round($baseGross / $divisorDays, 2);
        $lwpDeduction = round($lwpDays * $perDaySalary, 2);

        // Payable Ratio & Prorated Components
        if ($lwpDays > 0) {
            $payableRatio = max(0, ($divisorDays - $lwpDays) / $divisorDays);
        } else {
            $payableRatio = 1.0;
        }

        $earnedBasic = round((float) $structure->basic_salary * $payableRatio, 2);
        $earnedHra = round((float) $structure->hra * $payableRatio, 2);
        $earnedConveyance = round((float) $structure->conveyance_allowance * $payableRatio, 2);
        $earnedMedical = round((float) $structure->medical_allowance * $payableRatio, 2);
        $bikeAllowance = (float) ($structure->bike_allowance ?? 1500.00);
        $earnedBikeAllowance = round($bikeAllowance * $payableRatio, 2);
        $earnedSpecial = round((float) $structure->special_allowance * $payableRatio, 2);

        // Adjustments / variable earnings
        $bonus = (float) ($adjustments['bonus'] ?? 0);
        $incentive = (float) ($adjustments['incentive'] ?? 0);
        $overtimePay = (float) ($adjustments['overtime_pay'] ?? 0);

        $totalEarnings = round($earnedBasic + $earnedHra + $earnedConveyance + $earnedMedical + $earnedBikeAllowance + $earnedSpecial + $bonus + $incentive + $overtimePay, 2);

        // Deductions
        $pfDeduction = round((float) $structure->pf_employee * $payableRatio, 2);
        $esiDeduction = round((float) $structure->esi_employee * $payableRatio, 2);
        $ptDeduction = (float) $structure->professional_tax;
        $tdsDeduction = (float) $structure->tds_monthly;
        $otherDeductions = (float) ($adjustments['other_deductions'] ?? 0);

        // Gratuity accrual (statutory employer retiral provision)
        $gratuityAccrual = round((float) ($structure->gratuity > 0 ? $structure->gratuity : ($structure->basic_salary * 0.0481)), 2);

        $totalDeductions = round($pfDeduction + $esiDeduction + $ptDeduction + $tdsDeduction + $otherDeductions, 2);
        $netSalary = max(0, round($totalEarnings - $totalDeductions, 2));

        $payableDays = max(0, $totalDays - $lwpDays);

        $designationName = $employee->designation->name ?? $employee->designation->Designation ?? 'Employee';
        $departmentName = $employee->agency->agency_name ?? 'Operations';

        return [
            'structure' => $structure,
            'total_days' => $totalDays,
            'present_days' => (float) $presentDays,
            'paid_leave_days' => (float) $paidLeaveDays,
            'holiday_days' => (float) $holidayDays,
            'weekly_off_days' => (float) $weeklyOffDays,
            'lwp_days' => (float) $lwpDays,
            'payable_days' => (float) $payableDays,
            'base_gross' => $baseGross,
            'earned_basic' => $earnedBasic,
            'earned_hra' => $earnedHra,
            'earned_conveyance' => $earnedConveyance,
            'earned_medical' => $earnedMedical,
            'earned_bike_allowance' => $earnedBikeAllowance,
            'earned_special_allowance' => $earnedSpecial,
            'gratuity_accrual' => $gratuityAccrual,
            'bonus' => $bonus,
            'incentive' => $incentive,
            'overtime_pay' => $overtimePay,
            'total_earnings' => $totalEarnings,
            'pf_deduction' => $pfDeduction,
            'esi_deduction' => $esiDeduction,
            'pt_deduction' => $ptDeduction,
            'tds_deduction' => $tdsDeduction,
            'lwp_deduction' => $lwpDeduction,
            'other_deductions' => $otherDeductions,
            'total_deductions' => $totalDeductions,
            'net_salary' => $netSalary,
            'net_salary_in_words' => $this->numberToWords($netSalary),
            'bank_name' => $structure->bank_name,
            'bank_account_no' => $structure->bank_account_no,
            'ifsc_code' => $structure->ifsc_code,
            'pan_no' => $structure->pan_no,
            'uan_no' => $structure->uan_no,
            'designation_name' => $designationName,
            'department_name' => $departmentName,
            'payment_mode' => $structure->payment_mode ?? 'Bank Transfer',
        ];
    }

    /**
     * Generate or update a Payslip record for an employee for a given month/year.
     */
    public function generatePayslip(Employee $employee, int $month, int $year, ?int $batchId = null, array $adjustments = []): Payslip
    {
        $calc = $this->calculateEmployeePayroll($employee, $month, $year, $adjustments);

        $payslipNumber = sprintf('PAY-%d%02d-%05d', $year, $month, $employee->employee_id);

        $payslip = Payslip::updateOrCreate(
            [
                'employee_id' => $employee->employee_id,
                'month' => $month,
                'year' => $year,
            ],
            [
                'payslip_number' => $payslipNumber,
                'payroll_batch_id' => $batchId,
                'total_days' => $calc['total_days'],
                'present_days' => $calc['present_days'],
                'paid_leave_days' => $calc['paid_leave_days'],
                'holiday_days' => $calc['holiday_days'],
                'weekly_off_days' => $calc['weekly_off_days'],
                'lwp_days' => $calc['lwp_days'],
                'payable_days' => $calc['payable_days'],
                'base_gross' => $calc['base_gross'],
                'earned_basic' => $calc['earned_basic'],
                'earned_hra' => $calc['earned_hra'],
                'earned_conveyance' => $calc['earned_conveyance'],
                'earned_medical' => $calc['earned_medical'],
                'earned_bike_allowance' => $calc['earned_bike_allowance'],
                'earned_special_allowance' => $calc['earned_special_allowance'],
                'gratuity_accrual' => $calc['gratuity_accrual'],
                'bonus' => $calc['bonus'],
                'incentive' => $calc['incentive'],
                'overtime_pay' => $calc['overtime_pay'],
                'total_earnings' => $calc['total_earnings'],
                'pf_deduction' => $calc['pf_deduction'],
                'esi_deduction' => $calc['esi_deduction'],
                'pt_deduction' => $calc['pt_deduction'],
                'tds_deduction' => $calc['tds_deduction'],
                'lwp_deduction' => $calc['lwp_deduction'],
                'other_deductions' => $calc['other_deductions'],
                'total_deductions' => $calc['total_deductions'],
                'net_salary' => $calc['net_salary'],
                'net_salary_in_words' => $calc['net_salary_in_words'],
                'bank_name' => $calc['bank_name'],
                'bank_account_no' => $calc['bank_account_no'],
                'ifsc_code' => $calc['ifsc_code'],
                'pan_no' => $calc['pan_no'],
                'uan_no' => $calc['uan_no'],
                'designation_name' => $calc['designation_name'],
                'department_name' => $calc['department_name'],
                'payment_status' => $adjustments['payment_status'] ?? 'Pending',
                'payment_date' => $adjustments['payment_date'] ?? null,
                'payment_mode' => $calc['payment_mode'],
                'transaction_reference' => $adjustments['transaction_reference'] ?? null,
                'remarks' => $adjustments['remarks'] ?? null,
            ]
        );

        // Generate and save the PDF file
        $pdfPath = $this->generatePayslipPdf($payslip);
        $payslip->update(['pdf_path' => $pdfPath]);

        return $payslip;
    }

    /**
     * Generate Monthly Payroll Batch for all eligible employees.
     */
    public function generateMonthlyBatch(int $month, int $year, ?int $adminId = null, array $notes = []): PayrollBatch
    {
        $batchNumber = sprintf('BATCH-%d%02d-%s', $year, $month, strtoupper(Str::random(4)));

        $batch = PayrollBatch::create([
            'batch_number' => $batchNumber,
            'month' => $month,
            'year' => $year,
            'status' => 'Generated',
            'processed_by' => $adminId,
            'notes' => $notes['notes'] ?? "Payroll batch for " . Carbon::createFromDate($year, $month, 1)->format('F Y'),
        ]);

        $employees = Employee::active()
            ->whereHas('salaryStructures', function ($q) {
                $q->where('status', 'Active');
            })
            ->get();

        $totalEmployees = 0;
        $totalGross = 0;
        $totalDeductions = 0;
        $totalNetPay = 0;

        foreach ($employees as $employee) {
            try {
                $payslip = $this->generatePayslip($employee, $month, $year, $batch->id);
                $totalEmployees++;
                $totalGross += $payslip->total_earnings;
                $totalDeductions += $payslip->total_deductions;
                $totalNetPay += $payslip->net_salary;
            } catch (\Exception $e) {
                \Log::warning("Skipping payroll for employee {$employee->employee_id}: " . $e->getMessage());
            }
        }

        $batch->update([
            'total_employees' => $totalEmployees,
            'total_gross' => $totalGross,
            'total_deductions' => $totalDeductions,
            'total_net_pay' => $totalNetPay,
        ]);

        return $batch;
    }

    /**
     * Render and save Payslip PDF.
     */
    public function generatePayslipPdf(Payslip $payslip): string
    {
        $payslip->loadMissing('employee.designation', 'employee.agency');

        $data = [
            'payslip' => $payslip,
            'employee' => $payslip->employee,
            'period' => Carbon::createFromDate($payslip->year, $payslip->month, 1)->format('F Y'),
            'company_name' => config('app.name', 'EMS Organization'),
            'company_address' => 'Plot 42, Cyber Hub, Sector 18, Gurugram, HR 122002',
        ];

        $pdf = Pdf::loadView('payroll.payslip_pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOption(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        $directory = storage_path('app/public/payslips');
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $filename = sprintf('payslip_%d_%d_%02d.pdf', $payslip->employee_id, $payslip->year, $payslip->month);
        $fullPath = $directory . DIRECTORY_SEPARATOR . $filename;

        $pdf->save($fullPath);

        return 'storage/payslips/' . $filename;
    }

    /**
     * Convert numbers to Indian Currency Words format (Lakhs, Crores, etc.).
     */
    public function numberToWords(float $number): string
    {
        $amount = round($number, 2);
        $rupees = (int) floor($amount);
        $paise = (int) round(($amount - $rupees) * 100);

        if ($rupees === 0 && $paise === 0) {
            return 'Zero Rupees Only';
        }

        $words = $this->convertIntegerToIndianWords($rupees) . ' Rupees';

        if ($paise > 0) {
            $words .= ' and ' . $this->convertIntegerToIndianWords($paise) . ' Paise';
        }

        return trim($words) . ' Only';
    }

    private function convertIntegerToIndianWords(int $n): string
    {
        $ones = [
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
            6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
            11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen'
        ];

        $tens = [
            2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
            6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'
        ];

        if ($n === 0) {
            return '';
        }

        if ($n < 20) {
            return $ones[$n];
        }

        if ($n < 100) {
            return $tens[(int) floor($n / 10)] . ($n % 10 !== 0 ? ' ' . $ones[$n % 10] : '');
        }

        if ($n < 1000) {
            return $ones[(int) floor($n / 100)] . ' Hundred' . ($n % 100 !== 0 ? ' ' . $this->convertIntegerToIndianWords($n % 100) : '');
        }

        if ($n < 100000) { // Thousands (1,000 to 99,999)
            return $this->convertIntegerToIndianWords((int) floor($n / 1000)) . ' Thousand' . ($n % 1000 !== 0 ? ' ' . $this->convertIntegerToIndianWords($n % 1000) : '');
        }

        if ($n < 10000000) { // Lakhs (1,00,000 to 99,99,999)
            return $this->convertIntegerToIndianWords((int) floor($n / 100000)) . ' Lakh' . ($n % 100000 !== 0 ? ' ' . $this->convertIntegerToIndianWords($n % 100000) : '');
        }

        // Crores
        return $this->convertIntegerToIndianWords((int) floor($n / 10000000)) . ' Crore' . ($n % 10000000 !== 0 ? ' ' . $this->convertIntegerToIndianWords($n % 10000000) : '');
    }
}

<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\SalaryStructure;
use App\Services\PayrollService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PayrollSampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::where('status', 'Active')->take(10)->get();

        $sampleBanks = ['HDFC Bank', 'ICICI Bank', 'State Bank of India', 'Axis Bank', 'Kotak Mahindra Bank'];

        foreach ($employees as $idx => $emp) {
            $monthlyGross = 35000 + ($idx * 5000); // 35k to 80k
            $basic = round($monthlyGross * 0.50, 2);
            $hra = round($monthlyGross * 0.25, 2);
            $conveyance = 2000.00;
            $medical = 1500.00;
            $special = round($monthlyGross - ($basic + $hra + $conveyance + $medical), 2);
            $ctcAnnual = round($monthlyGross * 12 + (1800 * 12), 2);

            SalaryStructure::updateOrCreate(
                [
                    'employee_id' => $emp->employee_id,
                    'status' => 'Active',
                ],
                [
                    'effective_from' => '2026-01-01',
                    'ctc_annual' => $ctcAnnual,
                    'monthly_gross' => $monthlyGross,
                    'basic_salary' => $basic,
                    'hra' => $hra,
                    'conveyance_allowance' => $conveyance,
                    'medical_allowance' => $medical,
                    'special_allowance' => $special,
                    'other_allowance' => 0,
                    'pf_employee' => 1800.00,
                    'pf_employer' => 1800.00,
                    'esi_employee' => $monthlyGross <= 21000 ? round($monthlyGross * 0.0075, 2) : 0,
                    'esi_employer' => $monthlyGross <= 21000 ? round($monthlyGross * 0.0325, 2) : 0,
                    'professional_tax' => 200.00,
                    'tds_monthly' => $monthlyGross > 50000 ? 2500.00 : 500.00,
                    'bank_name' => $sampleBanks[$idx % count($sampleBanks)],
                    'bank_account_no' => '50100' . rand(10000000, 99999999),
                    'ifsc_code' => 'HDFC000' . rand(100, 999),
                    'pan_no' => 'ABCDE' . rand(1000, 9999) . 'F',
                    'uan_no' => '101' . rand(100000000, 999999999),
                    'pf_no' => 'MH/BAN/0012345/000/' . rand(1000, 9999),
                    'esi_no' => '31000' . rand(100000, 999999),
                    'payment_mode' => 'Bank Transfer',
                    'status' => 'Active',
                    'remarks' => 'Standard employee salary structure',
                ]
            );
        }

        // Run Payroll generation for August 2026 and September 2026
        $payrollService = app(PayrollService::class);

        // August 2026 Batch
        $payrollService->generateMonthlyBatch(8, 2026);

        // September 2026 Batch
        $payrollService->generateMonthlyBatch(9, 2026);
    }
}

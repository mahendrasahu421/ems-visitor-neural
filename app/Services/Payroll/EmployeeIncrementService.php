<?php

namespace App\Services\Payroll;

use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmployeeIncrement;
use App\Models\SalaryStructure;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeIncrementService
{
    /**
     * Generate next incremental sequence number (e.g. INC-2026-00001).
     */
    public function generateIncrementNumber(): string
    {
        $year = date('Y');
        $last = EmployeeIncrement::where('increment_number', 'like', "INC-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $parts = explode('-', $last->increment_number);
            $seq = isset($parts[2]) ? ((int) $parts[2]) + 1 : 1;
        } else {
            $seq = 1;
        }

        return sprintf('INC-%s-%05d', $year, $seq);
    }

    /**
     * Preview salary revision breakdown without saving to database.
     */
    public function previewIncrement(
        Employee $employee,
        float $hikeValue,
        string $hikeType = 'percentage',
        ?int $newDesignationId = null
    ): array {
        $currentStructure = $employee->salaryStructure;

        $currentGross = $currentStructure ? (float) $currentStructure->monthly_gross : 0.0;
        $currentCtc = $currentStructure ? (float) $currentStructure->ctc_annual : ($currentGross * 12);

        $newGross = $currentGross;
        $newCtc = $currentCtc;
        $hikePercentage = 0.0;
        $hikeAmount = 0.0;

        if ($hikeType === 'percentage') {
            $hikePercentage = round($hikeValue, 2);
            $newGross = round($currentGross * (1 + ($hikePercentage / 100)), 2);
            $newCtc = round($currentCtc * (1 + ($hikePercentage / 100)), 2);
            $hikeAmount = round($newCtc - $currentCtc, 2);
        } elseif ($hikeType === 'fixed_amount') {
            // Hike value is monthly gross increment
            $newGross = round($currentGross + $hikeValue, 2);
            $annualHike = round($hikeValue * 12, 2);
            $newCtc = round($currentCtc + $annualHike, 2);
            $hikeAmount = $annualHike;
            $hikePercentage = $currentGross > 0 ? round(($hikeValue / $currentGross) * 100, 2) : 0.0;
        } elseif ($hikeType === 'direct_ctc') {
            $newCtc = round($hikeValue, 2);
            $newGross = round($newCtc / 12, 2);
            $hikeAmount = round($newCtc - $currentCtc, 2);
            $hikePercentage = $currentCtc > 0 ? round(($hikeAmount / $currentCtc) * 100, 2) : 0.0;
        }

        // Smart Earnings Breakdown for new structure:
        // Basic: 50% of new Gross
        $basic = round($newGross * 0.50, 2);
        // HRA: 40% of Basic
        $hra = round($basic * 0.40, 2);
        // Retain conveyance & medical or statutory defaults
        $conveyance = $currentStructure ? (float) $currentStructure->conveyance_allowance : 1600.0;
        $medical = $currentStructure ? (float) $currentStructure->medical_allowance : 1250.0;
        $other = $currentStructure ? (float) $currentStructure->other_allowance : 0.0;

        // Balance as Special Allowance
        $allocated = $basic + $hra + $conveyance + $medical + $other;
        $special = max(0, round($newGross - $allocated, 2));

        // Deductions & Statutory calculations
        $pfNo = $currentStructure?->pf_no;
        $uanNo = $currentStructure?->uan_no;
        $esiNo = $currentStructure?->esi_no;

        // PF Employee: 12% of basic (capped at 1800 if basic > 15000, or 0 if exempt)
        $pfEmployee = 0.0;
        $pfEmployer = 0.0;
        if (($currentStructure && $currentStructure->pf_employee > 0) || !empty($pfNo) || !empty($uanNo)) {
            $pfEmployee = min(1800.0, round($basic * 0.12, 2));
            $pfEmployer = $pfEmployee;
        }

        // ESI: 0.75% of Gross if gross <= 21,000 and enrolled
        $esiEmployee = 0.0;
        $esiEmployer = 0.0;
        if (($currentStructure && $currentStructure->esi_employee > 0) || !empty($esiNo) || $newGross <= 21000) {
            if ($newGross <= 21000) {
                $esiEmployee = round($newGross * 0.0075, 2);
                $esiEmployer = round($newGross * 0.0325, 2);
            }
        }

        $pt = $currentStructure ? (float) $currentStructure->professional_tax : 200.0;
        $tds = $currentStructure ? (float) $currentStructure->tds_monthly : 0.0;

        $totalDeductions = $pfEmployee + $esiEmployee + $pt + $tds;
        $netSalary = max(0, round($newGross - $totalDeductions, 2));

        $prevDesig = $employee->designation;
        $newDesig = $newDesignationId ? Designation::find($newDesignationId) : $prevDesig;

        return [
            'employee'              => $employee,
            'current_structure'     => $currentStructure,
            'current_gross'         => $currentGross,
            'current_ctc'           => $currentCtc,
            'hike_type'             => $hikeType,
            'hike_value'            => $hikeValue,
            'hike_percentage'       => $hikePercentage,
            'hike_amount'           => $hikeAmount,
            'monthly_hike'          => round($newGross - $currentGross, 2),
            'new_gross'             => $newGross,
            'new_ctc'               => $newCtc,
            'basic_salary'          => $basic,
            'hra'                   => $hra,
            'conveyance_allowance'  => $conveyance,
            'medical_allowance'     => $medical,
            'special_allowance'     => $special,
            'other_allowance'       => $other,
            'pf_employee'           => $pfEmployee,
            'pf_employer'           => $pfEmployer,
            'esi_employee'          => $esiEmployee,
            'esi_employer'          => $esiEmployer,
            'professional_tax'      => $pt,
            'tds_monthly'           => $tds,
            'total_deductions'      => $totalDeductions,
            'net_take_home'         => $netSalary,
            'previous_designation'  => $prevDesig,
            'new_designation'       => $newDesig,
            'is_promotion'          => $newDesig && $prevDesig && ($newDesig->id !== $prevDesig->id),
        ];
    }

    /**
     * Create and apply increment record and activate revised salary structure.
     */
    public function createAndApplyIncrement(array $data, ?int $adminId = null): EmployeeIncrement
    {
        $employee = Employee::with(['salaryStructure', 'designation'])
            ->where('employee_id', $data['employee_id'])
            ->firstOrFail();

        if (!$employee->isActive()) {
            throw new \Exception("Cannot process increment for inactive employee: {$employee->employee_name} (ID: {$employee->employee_id})");
        }

        $effectiveDate = Carbon::parse($data['effective_date'])->format('Y-m-d');
        $currentStructure = $employee->salaryStructure;

        $currentGross = $currentStructure ? (float) $currentStructure->monthly_gross : 0.0;
        $currentCtc = $currentStructure ? (float) $currentStructure->ctc_annual : ($currentGross * 12);

        $hikeType = $data['increment_type'] ?? 'percentage';
        $hikeValue = (float) ($data['increment_value'] ?? 0);

        // Calculate preview figures
        $preview = $this->previewIncrement($employee, $hikeValue, $hikeType, $data['new_designation_id'] ?? null);

        $newGross = (float) ($data['new_gross'] ?? $preview['new_gross']);
        $newCtc = (float) ($data['new_ctc'] ?? $preview['new_ctc']);
        $hikePercentage = (float) ($data['hike_percentage'] ?? $preview['hike_percentage']);
        $hikeAmount = (float) ($data['hike_amount'] ?? $preview['hike_amount']);

        $incrementNumber = $this->generateIncrementNumber();

        return DB::transaction(function () use (
            $employee,
            $currentStructure,
            $effectiveDate,
            $currentGross,
            $currentCtc,
            $newGross,
            $newCtc,
            $hikeType,
            $hikeValue,
            $hikePercentage,
            $hikeAmount,
            $incrementNumber,
            $data,
            $preview,
            $adminId
        ) {
            // 1. Create Increment Record
            $increment = EmployeeIncrement::create([
                'increment_number'             => $incrementNumber,
                'employee_id'                  => $employee->employee_id,
                'review_cycle'                 => $data['review_cycle'] ?? 'Annual Appraisal ' . date('Y'),
                'effective_date'               => $effectiveDate,
                'current_ctc'                  => $currentCtc,
                'current_gross'                => $currentGross,
                'increment_type'               => $hikeType,
                'increment_value'              => $hikeValue,
                'hike_percentage'              => $hikePercentage,
                'hike_amount'                  => $hikeAmount,
                'new_ctc'                      => $newCtc,
                'new_gross'                    => $newGross,
                'previous_designation_id'      => $employee->designation_id,
                'new_designation_id'           => $data['new_designation_id'] ?? $employee->designation_id,
                'previous_salary_structure_id' => $currentStructure?->id,
                'performance_rating'           => $data['performance_rating'] ?? null,
                'status'                       => 'Applied',
                'remarks'                      => $data['remarks'] ?? 'Annual salary increment revision.',
                'letter_notes'                 => $data['letter_notes'] ?? null,
                'approved_by'                  => $adminId,
                'created_by'                   => $adminId,
                'applied_at'                   => now(),
            ]);

            // 2. Mark existing active salary structures as Inactive
            SalaryStructure::where('employee_id', $employee->employee_id)
                ->where('status', 'Active')
                ->update(['status' => 'Inactive']);

            // 3. Create New Active Salary Structure
            $basic = (float) ($data['basic_salary'] ?? $preview['basic_salary']);
            $hra = (float) ($data['hra'] ?? $preview['hra']);
            $conveyance = (float) ($data['conveyance_allowance'] ?? $preview['conveyance_allowance']);
            $medical = (float) ($data['medical_allowance'] ?? $preview['medical_allowance']);
            $special = (float) ($data['special_allowance'] ?? $preview['special_allowance']);
            $other = (float) ($data['other_allowance'] ?? $preview['other_allowance']);

            $pfEmp = (float) ($data['pf_employee'] ?? $preview['pf_employee']);
            $pfEmpr = (float) ($data['pf_employer'] ?? $preview['pf_employer']);
            $esiEmp = (float) ($data['esi_employee'] ?? $preview['esi_employee']);
            $esiEmpr = (float) ($data['esi_employer'] ?? $preview['esi_employer']);
            $pt = (float) ($data['professional_tax'] ?? $preview['professional_tax']);
            $tds = (float) ($data['tds_monthly'] ?? $preview['tds_monthly']);

            $newStructure = SalaryStructure::create([
                'employee_id'          => $employee->employee_id,
                'effective_from'       => $effectiveDate,
                'ctc_annual'           => $newCtc,
                'monthly_gross'        => $newGross,
                'basic_salary'         => $basic,
                'hra'                  => $hra,
                'conveyance_allowance' => $conveyance,
                'medical_allowance'    => $medical,
                'special_allowance'    => $special,
                'other_allowance'      => $other,
                'pf_employee'          => $pfEmp,
                'pf_employer'          => $pfEmpr,
                'esi_employee'         => $esiEmp,
                'esi_employer'         => $esiEmpr,
                'professional_tax'     => $pt,
                'tds_monthly'          => $tds,
                'bank_name'            => $currentStructure?->bank_name,
                'bank_account_no'      => $currentStructure?->bank_account_no,
                'ifsc_code'            => $currentStructure?->ifsc_code,
                'pan_no'               => $currentStructure?->pan_no,
                'uan_no'               => $currentStructure?->uan_no,
                'pf_no'                => $currentStructure?->pf_no,
                'esi_no'               => $currentStructure?->esi_no,
                'payment_mode'         => $currentStructure?->payment_mode ?? 'Bank Transfer',
                'status'               => 'Active',
                'remarks'              => "Revised via Increment #{$increment->increment_number} ({$increment->review_cycle})",
            ]);

            $increment->update(['new_salary_structure_id' => $newStructure->id]);

            // 4. Update Designation if Promoted
            if (!empty($data['new_designation_id']) && $data['new_designation_id'] != $employee->designation_id) {
                $employee->update(['designation_id' => $data['new_designation_id']]);
            }

            return $increment;
        });
    }

    /**
     * Bulk apply uniform increment percentage to multiple employees.
     */
    public function bulkApplyIncrement(
        array $employeeIds,
        float $hikePercentage,
        string $effectiveDate,
        string $cycle,
        ?int $adminId = null,
        ?string $remarks = null
    ): array {
        $appliedCount = 0;
        $skippedCount = 0;
        $totalHikeBudget = 0.0;
        $errors = [];

        $employees = Employee::active()
            ->whereIn('employee_id', $employeeIds)
            ->with(['salaryStructure', 'designation'])
            ->get();

        foreach ($employees as $employee) {
            try {
                if (!$employee->salaryStructure) {
                    $skippedCount++;
                    $errors[] = "Employee {$employee->employee_name} (ID: {$employee->employee_id}) has no active salary structure.";
                    continue;
                }

                $increment = $this->createAndApplyIncrement([
                    'employee_id'        => $employee->employee_id,
                    'review_cycle'       => $cycle,
                    'effective_date'     => $effectiveDate,
                    'increment_type'     => 'percentage',
                    'increment_value'    => $hikePercentage,
                    'remarks'            => $remarks ?: "Bulk annual increment of {$hikePercentage}% applied.",
                ], $adminId);

                $appliedCount++;
                $totalHikeBudget += (float) $increment->hike_amount;
            } catch (\Exception $e) {
                $skippedCount++;
                $errors[] = "Emp #{$employee->employee_id} ({$employee->employee_name}): " . $e->getMessage();
                Log::warning("Bulk increment error for employee {$employee->employee_id}: " . $e->getMessage());
            }
        }

        return [
            'success'            => $appliedCount > 0,
            'applied_count'      => $appliedCount,
            'skipped_count'      => $skippedCount,
            'total_hike_budget'  => $totalHikeBudget,
            'errors'             => $errors,
        ];
    }

    /**
     * Get active employees who are due for annual increment review (tenure >= 1 year since joining or last revision).
     */
    public function getEmployeesDueForReview(int $tenureMonths = 12): Collection
    {
        $thresholdDate = now()->subMonths($tenureMonths)->toDateString();

        return Employee::active()
            ->with(['designation', 'agency', 'salaryStructure', 'latestIncrement'])
            ->where(function ($q) use ($thresholdDate) {
                // Either joining date is older than 12 months
                $q->where('joining_date', '<=', $thresholdDate)
                  ->orWhereNull('joining_date');
            })
            ->get()
            ->filter(function ($emp) use ($thresholdDate) {
                // If has recent increment within 12 months, skip
                if ($emp->latestIncrement && $emp->latestIncrement->effective_date > $thresholdDate) {
                    return false;
                }
                return true;
            })
            ->values();
    }
}

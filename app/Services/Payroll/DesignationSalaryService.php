<?php

namespace App\Services\Payroll;

use App\Models\DesignationMaster;
use App\Models\DesignationSalaryStructure;
use App\Models\Employee;
use App\Models\SalaryStructure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DesignationSalaryService
{
    public function __construct(
        protected SalaryStateRuleService $stateRuleService
    ) {}

    /**
     * Get or initialize designation salary structure profile with employee and state breakdown.
     */
    public function getDesignationProfile(int $designationId): array
    {
        $designation = DesignationMaster::findOrFail($designationId);

        $template = DesignationSalaryStructure::where('designation_id', $designationId)->first();

        // Fetch active employees with this designation
        $employees = Employee::where('designation_id', $designationId)
            ->whereNull('deleted_at')
            ->where('status', 'Active')
            ->get();

        // Build state distribution of employees in this designation
        $stateCounts = [];
        foreach ($employees as $emp) {
            $stateName = $this->stateRuleService->resolveStateName($emp->posting_state) ?: 'Not Assigned / Default';
            $ptSample = $this->stateRuleService->calculateStatePt($emp->posting_state, $template?->monthly_gross ?? 35000);
            $isMetro = $this->stateRuleService->isMetro($emp->posting_state);

            if (!isset($stateCounts[$stateName])) {
                $stateCounts[$stateName] = [
                    'state_name' => $stateName,
                    'count' => 0,
                    'pt_amount' => $ptSample,
                    'is_metro' => $isMetro,
                ];
            }
            $stateCounts[$stateName]['count']++;
        }

        return [
            'designation_id'   => $designation->DesignationID,
            'designation_name' => $designation->Designation,
            'total_employees'  => $employees->count(),
            'state_breakdown'  => array_values($stateCounts),
            'template'         => $template,
        ];
    }

    /**
     * Save Designation Salary Structure and optionally bulk-apply to all mapped employees.
     */
    public function saveAndApply(int $designationId, array $data, bool $applyToEmployees = true): array
    {
        $designation = DesignationMaster::findOrFail($designationId);

        $gross = (float) ($data['monthly_gross'] ?? 35000);
        $basicPct = (float) ($data['basic_salary_pct'] ?? 50.00);
        $hraPct = (float) ($data['hra_pct'] ?? 25.00);
        $conveyance = (float) ($data['conveyance_allowance'] ?? 2000.00);
        $medical = (float) ($data['medical_allowance'] ?? 1500.00);
        $bikeAllowance = (float) ($data['bike_allowance'] ?? 0.00);
        $other = (float) ($data['other_allowance'] ?? 0.00);

        // If Bike Allowance not explicitly given, auto-check if role is field/supervisor
        if (!isset($data['bike_allowance'])) {
            $title = strtolower($designation->Designation);
            if (str_contains($title, 'supervisor') || str_contains($title, 'field') || str_contains($title, 'delivery') || str_contains($title, 'technician')) {
                $bikeAllowance = 1500.00;
            }
        }

        $isPf = isset($data['is_pf_enabled']) ? (bool) $data['is_pf_enabled'] : true;
        $isEsi = isset($data['is_esi_enabled']) ? (bool) $data['is_esi_enabled'] : ($gross <= 21000);
        $isPt = isset($data['is_pt_enabled']) ? (bool) $data['is_pt_enabled'] : true;
        $isGratuity = isset($data['is_gratuity_enabled']) ? (bool) $data['is_gratuity_enabled'] : true;
        $applyStatePt = isset($data['apply_state_pt_rule']) ? (bool) $data['apply_state_pt_rule'] : true;
        $applyMetroHra = isset($data['apply_metro_hra_rule']) ? (bool) $data['apply_metro_hra_rule'] : true;

        // Auto balancing Special Allowance
        $baseBasic = round($gross * ($basicPct / 100), 2);
        $baseHra = round($gross * ($hraPct / 100), 2);
        $allocated = $baseBasic + $baseHra + $conveyance + $medical + $bikeAllowance + $other;
        $special = max(0, round($gross - $allocated, 2));

        // 1. Save or Update Designation Template
        $template = DesignationSalaryStructure::updateOrCreate(
            ['designation_id' => $designationId],
            [
                'monthly_gross'        => $gross,
                'basic_salary_pct'     => $basicPct,
                'hra_pct'              => $hraPct,
                'conveyance_allowance' => $conveyance,
                'medical_allowance'    => $medical,
                'bike_allowance'       => $bikeAllowance,
                'special_allowance'    => $special,
                'other_allowance'      => $other,
                'is_pf_enabled'        => $isPf,
                'is_esi_enabled'       => $isEsi,
                'is_pt_enabled'        => $isPt,
                'is_gratuity_enabled'  => $isGratuity,
                'apply_state_pt_rule'  => $applyStatePt,
                'apply_metro_hra_rule' => $applyMetroHra,
                'status'               => $data['status'] ?? 'Active',
                'remarks'              => $data['remarks'] ?? "Designation template for {$designation->Designation}",
            ]
        );

        $employeesUpdated = 0;
        $updatedSummary = [];

        // 2. Bulk apply to all active employees under this designation
        if ($applyToEmployees) {
            $employees = Employee::where('designation_id', $designationId)
                ->whereNull('deleted_at')
                ->where('status', 'Active')
                ->get();

            DB::beginTransaction();
            try {
                foreach ($employees as $emp) {
                    $empState = $emp->posting_state;
                    $stateName = $this->stateRuleService->resolveStateName($empState) ?: 'Default';

                    // Compute employee-specific Basic
                    $empBasic = round($gross * ($basicPct / 100), 2);

                    // Compute employee-specific HRA (Metro 50% of Basic vs Non-Metro 40% of Basic)
                    if ($applyMetroHra && !empty($empState)) {
                        $hraPctBasic = $this->stateRuleService->getHraPercentageOfBasic($empState);
                        $empHra = round($empBasic * ($hraPctBasic / 100), 2);
                    } else {
                        $empHra = round($gross * ($hraPct / 100), 2);
                    }

                    // Compute employee-specific Special Allowance (Balancing figure to equal Gross)
                    $allocatedComponents = $empBasic + $empHra + $conveyance + $medical + $bikeAllowance + $other;
                    $empSpecial = max(0, round($gross - $allocatedComponents, 2));

                    // Statutory PF (12% of basic, capped at 1,800 if basic > 15,000)
                    $empPf = 0.00;
                    if ($isPf) {
                        $empPf = min(1800.00, round($empBasic * 0.12, 2));
                    }

                    // Statutory ESI (0.75% emp, 3.25% empr if Gross <= 21,000)
                    $empEsi = 0.00;
                    $emprEsi = 0.00;
                    if ($isEsi && $gross <= 21000) {
                        $empEsi = round($gross * 0.0075, 2);
                        $emprEsi = round($gross * 0.0325, 2);
                    }

                    // State-wise Professional Tax (PT)
                    $empPt = 0.00;
                    if ($isPt) {
                        if ($applyStatePt) {
                            $empPt = $this->stateRuleService->calculateStatePt($empState, $gross);
                        } else {
                            $empPt = ($gross >= 15000) ? 200.00 : 0.00;
                        }
                    }

                    // Gratuity (4.81% of basic salary)
                    $empGratuity = 0.00;
                    if ($isGratuity) {
                        $empGratuity = round($empBasic * 0.0481, 2);
                    }

                    // Annual CTC
                    $empAnnualCtc = round(($gross * 12) + ($empPf * 12) + ($emprEsi * 12) + ($empGratuity * 12), 2);

                    // Deactivate existing active structures
                    SalaryStructure::where('employee_id', $emp->employee_id)
                        ->where('status', 'Active')
                        ->update(['status' => 'Inactive']);

                    // Create new active salary structure
                    SalaryStructure::create([
                        'employee_id'          => $emp->employee_id,
                        'effective_from'       => $data['effective_from'] ?? now()->startOfMonth()->toDateString(),
                        'monthly_gross'        => $gross,
                        'basic_salary'         => $empBasic,
                        'hra'                  => $empHra,
                        'conveyance_allowance' => $conveyance,
                        'medical_allowance'    => $medical,
                        'bike_allowance'       => $bikeAllowance,
                        'special_allowance'    => $empSpecial,
                        'other_allowance'      => $other,
                        'pf_employee'          => $empPf,
                        'pf_employer'          => $empPf,
                        'esi_employee'         => $empEsi,
                        'esi_employer'         => $emprEsi,
                        'professional_tax'     => $empPt,
                        'tds_monthly'          => 0.00,
                        'gratuity'             => $empGratuity,
                        'ctc_annual'           => $empAnnualCtc,
                        'payment_mode'         => 'Bank Transfer',
                        'status'               => 'Active',
                        'remarks'              => "Auto-assigned from Designation [{$designation->Designation}] template (State: {$stateName})",
                    ]);

                    $employeesUpdated++;
                    if (!isset($updatedSummary[$stateName])) {
                        $updatedSummary[$stateName] = 0;
                    }
                    $updatedSummary[$stateName]++;
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Failed to bulk apply designation salary for {$designationId}: " . $e->getMessage());
                throw $e;
            }
        }

        return [
            'success'           => true,
            'template'          => $template,
            'designation_name'  => $designation->Designation,
            'employees_updated' => $employeesUpdated,
            'state_summary'     => $updatedSummary,
        ];
    }
}

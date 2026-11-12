<?php

namespace App\Services\Payroll;

use App\Models\Agency;
use App\Models\Country;
use App\Models\CountryState;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\SalaryStructure;
use App\Models\Shift;
use App\Models\StateCity;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeePayrollImportService
{
    /**
     * Map fuzzy or formatted header strings to canonical internal keys.
     */
    public function mapCanonicalHeader(string $header): ?string
    {
        return match ($header) {
            // Employee Identity & Demographics
            'employee_id', 'emp_id', 'id', 'emp_code', 'employee_code', 'code' => 'employee_id',
            'employee_name', 'name', 'full_name', 'fullname', 'emp_name' => 'employee_name',
            'employee_email', 'email', 'email_address', 'mail', 'emp_email' => 'employee_email',
            'employee_mobile', 'mobile', 'phone', 'contact', 'mobile_number', 'phone_number', 'contact_number' => 'employee_mobile',
            'employee_password', 'password', 'pass' => 'employee_password',
            'employee_gender', 'gender', 'sex' => 'employee_gender',
            'blood_group', 'bloodgroup', 'blood', 'blood_type' => 'blood_group',
            'posting_country', 'country', 'country_id', 'country_name' => 'posting_country',
            'posting_state', 'state', 'state_id', 'state_name' => 'posting_state',
            'posting_city', 'city', 'city_id', 'city_name' => 'posting_city',
            'joining_date', 'date_of_joining', 'doj', 'joining' => 'joining_date',
            'date_of_confirmation', 'confirmation_date', 'doc' => 'date_of_confirmation',
            'employment_type', 'type', 'job_type' => 'employment_type',
            'designation_id', 'designation', 'designation_name', 'role', 'position' => 'designation_id',
            'agency_id', 'agency', 'agency_name', 'agency_code', 'contractor', 'assigned_agency' => 'agency_id',
            'shift_id', 'shift', 'shift_name', 'work_shift' => 'shift_id',
            'is_supervisor', 'supervisor', 'mark_as_supervisor', 'is_supervisor_flag' => 'is_supervisor',
            'supervisor_id', 'reporting_supervisor', 'reports_to', 'manager', 'supervisor_name', 'supervisor_email' => 'supervisor_id',
            'status', 'employee_status', 'account_status' => 'status',
            'profile_image', 'image', 'photo', 'avatar' => 'profile_image',

            // Salary Earnings & CTC
            'ctc_annual', 'annual_ctc', 'ctc', 'yearly_ctc' => 'ctc_annual',
            'monthly_gross', 'gross_salary', 'gross', 'monthly_ctc', 'salary_gross' => 'monthly_gross',
            'basic_salary', 'basic', 'basic_pay' => 'basic_salary',
            'hra', 'house_rent_allowance' => 'hra',
            'conveyance_allowance', 'conveyance', 'ta' => 'conveyance_allowance',
            'medical_allowance', 'medical', 'medical_reimbursement' => 'medical_allowance',
            'special_allowance', 'special' => 'special_allowance',
            'other_allowance', 'other_allowances', 'others', 'misc_allowance' => 'other_allowance',
            'bike_allowance', 'bike_maintenance_allowance', 'bike_maint_allowance', 'bma', 'bike' => 'bike_allowance',
            'gratuity', 'gratuity_amount', 'gratuity_monthly', 'gratuity_accrual' => 'gratuity',

            // Deductions & Statutory (PF, ESI, PT, TDS)
            'pf_employee', 'pf_deduction', 'epf_employee', 'employee_pf', 'pf_emp', 'pf_employee_deduction', 'employee_pf_deduction', 'pf_deduction_employee' => 'pf_employee',
            'pf_employer', 'epf_employer', 'employer_pf', 'pf_empr', 'pf_employer_contribution', 'employer_pf_contribution' => 'pf_employer',
            'esi_employee', 'esic_employee', 'employee_esi', 'esi_deduction', 'esi_emp', 'esi_employee_deduction', 'employee_esi_deduction' => 'esi_employee',
            'esi_employer', 'esic_employer', 'employer_esi', 'esi_empr', 'esi_employer_contribution', 'employer_esi_contribution' => 'esi_employer',
            'professional_tax', 'pt', 'prof_tax', 'p_tax', 'pt_deduction' => 'professional_tax',
            'tds_monthly', 'tds', 'tax_deduction', 'monthly_tds', 'income_tax', 'tds_deduction' => 'tds_monthly',

            // Bank Details
            'bank_name', 'bank' => 'bank_name',
            'bank_account_no', 'bank_account', 'account_no', 'account_number', 'acc_no', 'bank_acc_no' => 'bank_account_no',
            'ifsc_code', 'ifsc' => 'ifsc_code',

            // Statutory Registration Numbers (PF, UAN, ESI, PAN)
            'pan_no', 'pan', 'pan_card', 'pan_number' => 'pan_no',
            'uan_no', 'uan', 'uan_number' => 'uan_no',
            'pf_no', 'pf_number', 'epf_no', 'provident_fund_no' => 'pf_no',
            'esi_no', 'esi_number', 'esic_no', 'esic_number' => 'esi_no',

            // Structure Settings & Metadata
            'payment_mode', 'pay_mode', 'salary_payment_mode' => 'payment_mode',
            'effective_from', 'salary_effective_from', 'effective_date', 'salary_effective_date', 'wef' => 'effective_from',
            'remarks', 'salary_remarks', 'comments', 'notes' => 'remarks',

            default => null,
        };
    }

    /**
     * Parse rows from CSV / Excel file.
     */
    public function readImportRows(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        return $sheet->toArray(null, true, true, false);
    }

    /**
     * Clean header string.
     */
    public function normalizeHeader($header): string
    {
        $clean = trim((string) $header);
        $clean = str_replace(['(', ')', '[', ']', '{', '}', '#'], '', $clean);
        $clean = str_replace([' ', '-', '/', '\\', '.'], '_', $clean);
        return strtolower(trim($clean, '_'));
    }

    /**
     * Main bulk import method supporting smart upsert of employees and their salary/PF structures.
     *
     * @param  UploadedFile|string  $file
     * @param  array  $options  ['source' => 'payroll' | 'employee']
     * @return array
     */
    public function import($file, array $options = []): array
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        if (!file_exists($filePath) || !is_readable($filePath)) {
            return [
                'success' => false,
                'message' => 'Unable to access the uploaded file. Please try again.',
                'total_rows' => 0,
                'employees_created' => 0,
                'employees_updated' => 0,
                'salary_structures_synced' => 0,
                'skipped' => 0,
                'errors' => ['File not found or unreadable.'],
            ];
        }

        try {
            $rows = $this->readImportRows($filePath);
        } catch (\Exception $e) {
            Log::error('Bulk import spreadsheet load error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to parse spreadsheet file: ' . $e->getMessage(),
                'total_rows' => 0,
                'employees_created' => 0,
                'employees_updated' => 0,
                'salary_structures_synced' => 0,
                'skipped' => 0,
                'errors' => [$e->getMessage()],
            ];
        }

        if (count($rows) < 2) {
            return [
                'success' => false,
                'message' => 'The import file is empty or does not contain any data rows below the header.',
                'total_rows' => 0,
                'employees_created' => 0,
                'employees_updated' => 0,
                'salary_structures_synced' => 0,
                'skipped' => 0,
                'errors' => ['File contains no data rows.'],
            ];
        }

        // Build header mapping
        $rawHeaders = $rows[0];
        $headerMap = [];
        foreach ($rawHeaders as $idx => $rawHeader) {
            $clean = $this->normalizeHeader($rawHeader);
            if ($clean !== '') {
                $canonical = $this->mapCanonicalHeader($clean);
                if ($canonical) {
                    $headerMap[$idx] = $canonical;
                }
            }
        }

        // Check if we have minimum identifiers
        $hasName = in_array('employee_name', $headerMap, true);
        $hasEmail = in_array('employee_email', $headerMap, true);
        $hasId = in_array('employee_id', $headerMap, true);

        if (!$hasEmail && !$hasId) {
            return [
                'success' => false,
                'message' => 'Missing essential identification column. The spreadsheet must contain at least "Employee Email" or "Employee ID" column to match/register employees.',
                'total_rows' => 0,
                'employees_created' => 0,
                'employees_updated' => 0,
                'salary_structures_synced' => 0,
                'skipped' => 0,
                'errors' => ['Neither Employee Email nor Employee ID header was recognized.'],
            ];
        }

        $employeesCreated = 0;
        $employeesUpdated = 0;
        $salariesSynced = 0;
        $skipped = 0;
        $errors = [];
        $defaultImage = '1770807033_users-default.png';

        $dataRows = array_slice($rows, 1);
        $totalRows = count($dataRows);

        foreach ($dataRows as $index => $row) {
            $line = $index + 2; // spreadsheet 1-based index (header is line 1)
            $rowData = [];

            foreach ($headerMap as $headerIndex => $canonicalKey) {
                $rowData[$canonicalKey] = trim((string) ($row[$headerIndex] ?? ''));
            }

            // Skip entirely empty row
            if (empty(array_filter($rowData, fn($v) => $v !== ''))) {
                continue;
            }

            // 1. Resolve / Match existing employee
            $employee = null;

            // Try matching by explicit Employee ID / Code
            if (!empty($rowData['employee_id'])) {
                $rawId = preg_replace('/[^0-9]/', '', (string) $rowData['employee_id']);
                if ($rawId !== '') {
                    $employee = Employee::where('employee_id', (int) $rawId)->first();
                }
            }

            // Try matching by Email (primary unique employee handle)
            $rawEmail = strtolower(trim((string) ($rowData['employee_email'] ?? '')));
            if (!$employee && !empty($rawEmail)) {
                $employee = Employee::where('employee_email', $rawEmail)->first();
            }

            // Try matching by Mobile Number (fallback only if email is not provided)
            $rawMobile = preg_replace('/[^0-9]/', '', (string) ($rowData['employee_mobile'] ?? ''));
            if (!$employee && empty($rawEmail) && strlen($rawMobile) >= 10 && !in_array($rawMobile, ['0000000000', '1234567890'], true)) {
                $employee = Employee::where('employee_mobile', $rawMobile)->first();
            }

            // 2. Perform Employee Creation or Update (Smart Upsert)
            $isNewEmployee = false;
            try {
                if ($employee) {
                    // Update existing employee's details if provided in row
                    $updatePayload = [];

                    if (!empty($rowData['employee_name'])) {
                        $updatePayload['employee_name'] = $rowData['employee_name'];
                    }
                    if (!empty($rowData['employee_mobile']) && $rowData['employee_mobile'] !== $employee->employee_mobile) {
                        $updatePayload['employee_mobile'] = $rowData['employee_mobile'];
                    }
                    if (!empty($rowData['employee_password'])) {
                        $updatePayload['employee_password'] = Hash::make($rowData['employee_password']);
                    }
                    if (isset($rowData['employee_gender']) && $rowData['employee_gender'] !== '') {
                        $updatePayload['employee_gender'] = $this->normalizeGender($rowData['employee_gender']);
                    }
                    if (isset($rowData['blood_group']) && $rowData['blood_group'] !== '') {
                        $updatePayload['blood_group'] = $this->normalizeBloodGroup($rowData['blood_group']);
                    }
                    if (!empty($rowData['designation_id'])) {
                        $updatePayload['designation_id'] = $this->resolveDesignation($rowData['designation_id']);
                    }
                    if (isset($rowData['agency_id']) && $rowData['agency_id'] !== '') {
                        $updatePayload['agency_id'] = $this->resolveAgency($rowData['agency_id']);
                    }
                    if (!empty($rowData['shift_id'])) {
                        $updatePayload['shift_id'] = $this->resolveShift($rowData['shift_id']);
                    }
                    if (!empty($rowData['employment_type'])) {
                        $updatePayload['employment_type'] = $this->normalizeEmploymentType($rowData['employment_type']);
                    }
                    if (!empty($rowData['joining_date'])) {
                        $updatePayload['joining_date'] = $this->normalizeImportDate($rowData['joining_date']) ?: $employee->joining_date;
                    }
                    if (isset($rowData['date_of_confirmation']) && $rowData['date_of_confirmation'] !== '') {
                        $updatePayload['date_of_confirmation'] = $this->normalizeImportDate($rowData['date_of_confirmation']);
                    }
                    if (isset($rowData['is_supervisor']) && $rowData['is_supervisor'] !== '') {
                        $updatePayload['is_supervisor'] = $this->resolveSupervisorFlag($rowData['is_supervisor'], $employee->designation_id);
                    }
                    if (isset($rowData['supervisor_id']) && $rowData['supervisor_id'] !== '') {
                        $updatePayload['supervisor_id'] = $this->resolveSupervisor($rowData['supervisor_id'], $employee->employee_email);
                    }
                    if (!empty($rowData['posting_country'])) {
                        $countryId = $this->resolveCountry($rowData['posting_country']);
                        $updatePayload['posting_country'] = $countryId;
                        if (!empty($rowData['posting_state'])) {
                            $updatePayload['posting_state'] = $this->resolveState($rowData['posting_state'], $countryId);
                        }
                    }
                    if (!empty($rowData['posting_city'])) {
                        $stateId = $updatePayload['posting_state'] ?? $employee->posting_state;
                        $updatePayload['posting_city'] = $this->resolveCity($rowData['posting_city'], $stateId);
                    }
                    if (!empty($rowData['status'])) {
                        $statusRaw = strtolower(trim($rowData['status']));
                        $updatePayload['status'] = in_array($statusRaw, ['inactive', '0', 'disabled', 'left'], true) ? 'Inactive' : 'Active';
                    }

                    if (!empty($updatePayload)) {
                        $employee->update($updatePayload);
                        if (($updatePayload['status'] ?? null) === 'Inactive') {
                            $employee->deactivate();
                        } elseif (($updatePayload['status'] ?? null) === 'Active') {
                            $employee->activate();
                        }
                    }

                    $employeesUpdated++;
                } else {
                    // Create New Employee
                    $empName = trim((string) ($rowData['employee_name'] ?? ''));
                    if (empty($empName)) {
                        $errors[] = "Row {$line}: Missing employee name. Required to register a new employee.";
                        $skipped++;
                        continue;
                    }

                    if (empty($rawEmail) || !filter_var($rawEmail, FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "Row {$line}: Valid employee email is required for new employee '{$empName}'.";
                        $skipped++;
                        continue;
                    }

                    $countryId = $this->resolveCountry($rowData['posting_country'] ?? null);
                    $stateId = $this->resolveState($rowData['posting_state'] ?? null, $countryId);
                    $cityId = $this->resolveCity($rowData['posting_city'] ?? null, $stateId);
                    $designationId = $this->resolveDesignation($rowData['designation_id'] ?? null);
                    $agencyId = $this->resolveAgency($rowData['agency_id'] ?? null);
                    $shiftId = $this->resolveShift($rowData['shift_id'] ?? null);
                    $isSupervisor = $this->resolveSupervisorFlag($rowData['is_supervisor'] ?? null, $designationId);
                    $supervisorId = $this->resolveSupervisor($rowData['supervisor_id'] ?? null, $rawEmail);
                    $password = !empty($rowData['employee_password']) ? $rowData['employee_password'] : '123456';
                    $gender = $this->normalizeGender($rowData['employee_gender'] ?? null);
                    $bloodGroup = $this->normalizeBloodGroup($rowData['blood_group'] ?? null);
                    $joiningDate = $this->normalizeImportDate($rowData['joining_date'] ?? null) ?: now()->toDateString();
                    $confirmationDate = $this->normalizeImportDate($rowData['date_of_confirmation'] ?? null);
                    $mobile = !empty($rawMobile) ? $rawMobile : '0000000000';
                    $empType = $this->normalizeEmploymentType($rowData['employment_type'] ?? null);

                    $statusRaw = strtolower(trim((string) ($rowData['status'] ?? 'active')));
                    $status = in_array($statusRaw, ['inactive', '0', 'disabled', 'left'], true) ? 'Inactive' : 'Active';

                    $employee = Employee::create([
                        'employee_name' => $empName,
                        'employee_email' => $rawEmail,
                        'employee_mobile' => $mobile,
                        'employee_password' => Hash::make($password),
                        'employee_gender' => $gender,
                        'blood_group' => $bloodGroup,
                        'posting_country' => $countryId,
                        'posting_state' => $stateId,
                        'posting_city' => $cityId,
                        'joining_date' => $joiningDate,
                        'date_of_confirmation' => $confirmationDate,
                        'employment_type' => $empType,
                        'designation_id' => $designationId,
                        'agency_id' => $agencyId,
                        'shift_id' => $shiftId,
                        'is_supervisor' => $isSupervisor,
                        'supervisor_id' => $supervisorId,
                        'status' => $status,
                        'profile_image' => !empty($rowData['profile_image']) ? $rowData['profile_image'] : $defaultImage,
                    ]);

                    $isNewEmployee = true;
                    $employeesCreated++;

                    if ($status === 'Inactive') {
                        $employee->deactivate();
                    }
                }
            } catch (\Exception $e) {
                $errors[] = "Row {$line}: Error saving employee record: " . $e->getMessage();
                $skipped++;
                continue;
            }

            // 3. Process Salary Structure & Statutory (PF / ESI / Bank / PAN / UAN) Upsert
            $hasSalaryData = $this->hasAnySalaryOrStatutoryField($rowData);

            if ($hasSalaryData && $employee) {
                try {
                    $this->upsertSalaryStructure($employee, $rowData);
                    $salariesSynced++;
                } catch (\Exception $e) {
                    $errors[] = "Row {$line} (Emp: {$employee->employee_name}): Error saving salary/PF structure: " . $e->getMessage();
                }
            }
        }

        $totalProcessed = $employeesCreated + $employeesUpdated;
        $success = $totalProcessed > 0;

        $msgParts = [];
        if ($employeesCreated > 0) {
            $msgParts[] = "{$employeesCreated} new employee(s) created";
        }
        if ($employeesUpdated > 0) {
            $msgParts[] = "{$employeesUpdated} existing employee(s) updated";
        }
        if ($salariesSynced > 0) {
            $msgParts[] = "{$salariesSynced} salary & PF structure(s) configured";
        }

        $summaryMessage = !empty($msgParts)
            ? 'Import complete: ' . implode(', ', $msgParts) . '.'
            : 'No employee records were modified.';

        return [
            'success' => $success,
            'message' => $summaryMessage,
            'total_rows' => $totalRows,
            'employees_created' => $employeesCreated,
            'employees_updated' => $employeesUpdated,
            'salary_structures_synced' => $salariesSynced,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Determine if row includes any salary, statutory, or bank details.
     */
    protected function hasAnySalaryOrStatutoryField(array $rowData): bool
    {
        $keys = [
            'monthly_gross', 'ctc_annual', 'basic_salary', 'hra',
            'conveyance_allowance', 'medical_allowance', 'bike_allowance', 'special_allowance', 'other_allowance',
            'pf_employee', 'pf_employer', 'esi_employee', 'esi_employer',
            'professional_tax', 'tds_monthly', 'gratuity', 'bank_name', 'bank_account_no',
            'ifsc_code', 'pan_no', 'uan_no', 'pf_no', 'esi_no', 'payment_mode'
        ];

        foreach ($keys as $key) {
            if (isset($rowData[$key]) && trim((string) $rowData[$key]) !== '') {
                return true;
            }
        }

        return false;
    }

    /**
     * Upsert Salary Structure record for an employee with smart calculations and statutory defaults.
     */
    protected function upsertSalaryStructure(Employee $employee, array $rowData): SalaryStructure
    {
        // 1. Calculate Earnings & Breakdown
        $monthlyGross = $this->normalizeNumeric($rowData['monthly_gross'] ?? null);
        $basic = $this->normalizeNumeric($rowData['basic_salary'] ?? null);
        $hra = $this->normalizeNumeric($rowData['hra'] ?? null);
        $conveyance = $this->normalizeNumeric($rowData['conveyance_allowance'] ?? null);
        $medical = $this->normalizeNumeric($rowData['medical_allowance'] ?? null);
        $other = $this->normalizeNumeric($rowData['other_allowance'] ?? null);
        $special = $this->normalizeNumeric($rowData['special_allowance'] ?? null);
        $bikeAllowance = isset($rowData['bike_allowance']) && $rowData['bike_allowance'] !== ''
            ? $this->normalizeNumeric($rowData['bike_allowance'])
            : 1500.00;
        $gratuity = isset($rowData['gratuity']) && $rowData['gratuity'] !== ''
            ? $this->normalizeNumeric($rowData['gratuity'])
            : 0.00;

        // Smart auto-breakdown fallback if user provided monthly_gross but no explicit breakdown
        if ($monthlyGross > 0) {
            if ($basic <= 0) {
                // 50% of gross as Basic
                $basic = round($monthlyGross * 0.50, 2);
            }
            if ($hra <= 0 && $basic > 0) {
                // 40% of Basic as HRA (Metro standard 50%, non-metro 40%)
                $hra = round($basic * 0.40, 2);
            }
            if ($gratuity <= 0 && $basic > 0) {
                $gratuity = round($basic * 0.0481, 2);
            }
            if ($special <= 0) {
                // Remaining balance as Special Allowance
                $allocated = $basic + $hra + $conveyance + $medical + $other + $bikeAllowance;
                $special = max(0, round($monthlyGross - $allocated, 2));
            }
        } elseif ($basic > 0) {
            // If user entered basic and components without explicit monthly_gross
            if ($gratuity <= 0) {
                $gratuity = round($basic * 0.0481, 2);
            }
            $monthlyGross = round($basic + $hra + $conveyance + $medical + $special + $other + $bikeAllowance, 2);
        }

        // 2. Calculate Deductions & Contributions
        $pfNo = trim((string) ($rowData['pf_no'] ?? ''));
        $uanNo = trim((string) ($rowData['uan_no'] ?? ''));
        $esiNo = trim((string) ($rowData['esi_no'] ?? ''));

        // PF Employee (12% of basic, capped at 1,800 if basic > 15,000)
        $pfEmployee = $this->normalizeNumeric($rowData['pf_employee'] ?? null);
        if ($pfEmployee <= 0 && (!empty($pfNo) || !empty($uanNo) || ($basic > 0 && $monthlyGross <= 25000))) {
            $pfEmployee = min(1800.0, round($basic * 0.12, 2));
        }

        // PF Employer (12% matching)
        $pfEmployer = $this->normalizeNumeric($rowData['pf_employer'] ?? null);
        if ($pfEmployer <= 0 && $pfEmployee > 0) {
            $pfEmployer = $pfEmployee;
        }

        // ESI Employee (0.75% of Gross if gross <= 21,000 or ESI number provided)
        $esiEmployee = $this->normalizeNumeric($rowData['esi_employee'] ?? null);
        if ($esiEmployee <= 0 && (!empty($esiNo) || ($monthlyGross > 0 && $monthlyGross <= 21000))) {
            $esiEmployee = round($monthlyGross * 0.0075, 2);
        }

        // ESI Employer (3.25% of Gross)
        $esiEmployer = $this->normalizeNumeric($rowData['esi_employer'] ?? null);
        if ($esiEmployer <= 0 && $esiEmployee > 0) {
            $esiEmployer = round($monthlyGross * 0.0325, 2);
        }

        // Professional Tax (₹200 if gross >= 15,000)
        $pt = $this->normalizeNumeric($rowData['professional_tax'] ?? null);
        if ($pt <= 0 && $monthlyGross >= 15000) {
            $pt = 200.0;
        }

        // Monthly TDS
        $tds = $this->normalizeNumeric($rowData['tds_monthly'] ?? null);

        // Annual CTC (Gross + Employer PF + Employer ESI + Employer Gratuity)
        $ctcAnnual = $this->normalizeNumeric($rowData['ctc_annual'] ?? null);
        if ($ctcAnnual <= 0 && $monthlyGross > 0) {
            $ctcAnnual = round(($monthlyGross * 12) + ($pfEmployer * 12) + ($esiEmployer * 12) + ($gratuity * 12), 2);
        }

        // 3. Bank & Statutory Registration
        $bankName = trim((string) ($rowData['bank_name'] ?? ''));
        $bankAccount = trim((string) ($rowData['bank_account_no'] ?? ''));
        $ifsc = strtoupper(trim(str_replace(' ', '', (string) ($rowData['ifsc_code'] ?? ''))));
        $pan = strtoupper(trim(str_replace(' ', '', (string) ($rowData['pan_no'] ?? ''))));
        $payMode = trim((string) ($rowData['payment_mode'] ?? 'Bank Transfer'));
        if (empty($payMode)) {
            $payMode = 'Bank Transfer';
        }

        // Effective Date
        $effectiveFrom = $this->normalizeImportDate($rowData['effective_from'] ?? null);
        if (!$effectiveFrom) {
            $effectiveFrom = !empty($employee->joining_date)
                ? $employee->joining_date
                : now()->startOfMonth()->toDateString();
        }

        $remarks = !empty($rowData['remarks'])
            ? trim((string) $rowData['remarks'])
            : 'Imported via Bulk Payroll Import on ' . now()->format('d M Y');

        // 4. Find existing structure or create new
        $existing = SalaryStructure::where('employee_id', $employee->employee_id)
            ->where('status', 'Active')
            ->first();

        if (!$existing) {
            $existing = SalaryStructure::where('employee_id', $employee->employee_id)->first();
        }

        $payload = [
            'employee_id'          => $employee->employee_id,
            'effective_from'       => $effectiveFrom,
            'ctc_annual'           => $ctcAnnual,
            'monthly_gross'        => $monthlyGross,
            'basic_salary'         => $basic,
            'hra'                  => $hra,
            'conveyance_allowance' => $conveyance,
            'medical_allowance'    => $medical,
            'bike_allowance'       => $bikeAllowance,
            'special_allowance'    => $special,
            'other_allowance'      => $other,
            'pf_employee'          => $pfEmployee,
            'pf_employer'          => $pfEmployer,
            'esi_employee'         => $esiEmployee,
            'esi_employer'         => $esiEmployer,
            'professional_tax'     => $pt,
            'tds_monthly'          => $tds,
            'gratuity'             => $gratuity,
            'bank_name'            => !empty($bankName) ? $bankName : ($existing?->bank_name),
            'bank_account_no'      => !empty($bankAccount) ? $bankAccount : ($existing?->bank_account_no),
            'ifsc_code'            => !empty($ifsc) ? $ifsc : ($existing?->ifsc_code),
            'pan_no'               => !empty($pan) ? $pan : ($existing?->pan_no),
            'uan_no'               => !empty($uanNo) ? $uanNo : ($existing?->uan_no),
            'pf_no'                => !empty($pfNo) ? $pfNo : ($existing?->pf_no),
            'esi_no'               => !empty($esiNo) ? $esiNo : ($existing?->esi_no),
            'payment_mode'         => $payMode,
            'status'               => $employee->isActive() ? 'Active' : 'Inactive',
            'remarks'              => $remarks,
        ];

        if ($existing) {
            $existing->update($payload);
            $target = $existing;
        } else {
            $target = SalaryStructure::create($payload);
        }

        // Deactivate any other salary structures for this employee
        SalaryStructure::where('employee_id', $employee->employee_id)
            ->where('id', '!=', $target->id)
            ->update(['status' => 'Inactive']);

        return $target;
    }

    /**
     * Download comprehensive sample CSV with full Employee, PF, UAN, Bank, and Salary columns.
     */
    public function downloadSampleCsv(): StreamedResponse
    {
        $headers = [
            'Employee ID',
            'Employee Name',
            'Employee Email',
            'Mobile Number',
            'Password',
            'Gender',
            'Blood Group',
            'Designation',
            'Agency',
            'Work Shift',
            'Employment Type',
            'Joining Date',
            'Date of Confirmation',
            'Mark as Supervisor',
            'Reporting Supervisor',
            'Country',
            'State',
            'City',
            'Employee Status',
            'Monthly Gross',
            'Basic Salary',
            'HRA',
            'Conveyance Allowance',
            'Medical Allowance',
            'Bike Maintenance Allowance',
            'Special Allowance',
            'Other Allowance',
            'PF Employee Deduction',
            'PF Employer Contribution',
            'ESI Employee Deduction',
            'ESI Employer Contribution',
            'Professional Tax',
            'Monthly TDS',
            'Gratuity',
            'Annual CTC',
            'Bank Name',
            'Bank Account No',
            'IFSC Code',
            'PAN Number',
            'UAN Number',
            'PF Number',
            'ESI Number',
            'Payment Mode',
            'Salary Effective From',
            'Remarks',
        ];

        $sampleAgency = Agency::where('status', 'Active')->value('agency_name') ?? 'Neural';
        $sampleShift = Shift::where('status', 'Active')->value('shift_name') ?? 'General Shift';
        $sampleSupervisor = Employee::where('is_supervisor', 1)->value('employee_name') ?? 'Shashank Shrivastva';

        // Sample 1: Full Payroll & Statutory with explicit values
        $sample1 = [
            'EMP-101',
            'Aakash Sharma',
            'aakash.sharma@example.com',
            '9876543210',
            'password123',
            'Male',
            'O+',
            'Software Engineer',
            $sampleAgency,
            $sampleShift,
            'Full Time',
            '2026-01-15',
            '2026-07-15',
            'No',
            $sampleSupervisor,
            'India',
            'Madhya Pradesh',
            'Bhopal',
            'Active',
            '35000',
            '17500',
            '7000',
            '1600',
            '1250',
            '1500',
            '6150',
            '0',
            '1800',
            '1800',
            '0',
            '0',
            '200',
            '500',
            '841.75',
            '451701',
            'HDFC Bank',
            '50100234567890',
            'HDFC0001234',
            'ABCDE1234F',
            '100987654321',
            'MP/BHP/0045678/000/001',
            '',
            'Bank Transfer',
            '2026-01-15',
            'Confirmed Software Engineer CTC',
        ];

        // Sample 2: Senior Manager / Supervisor (High Gross, No ESI, High PF)
        $sample2 = [
            'EMP-102',
            'Pooja Verma',
            'pooja.verma@example.com',
            '9876543211',
            'password123',
            'Female',
            'B+',
            'Supervisor',
            $sampleAgency,
            $sampleShift,
            'Full Time',
            '2025-06-01',
            '2025-12-01',
            'Yes',
            '',
            'India',
            'Madhya Pradesh',
            'Bhopal',
            'Active',
            '75000',
            '37500',
            '15000',
            '3000',
            '2500',
            '1500',
            '15500',
            '0',
            '1800',
            '1800',
            '0',
            '0',
            '200',
            '2500',
            '1803.75',
            '943245',
            'State Bank of India',
            '30987654321',
            'SBIN0004567',
            'XYZPE5678K',
            '100876543210',
            'MP/BHP/0045678/000/002',
            '',
            'Bank Transfer',
            '2025-06-01',
            'Senior Team Lead Structure',
        ];

        // Sample 3: Contract / Agency Staff with ESI & PF
        $sample3 = [
            '', // Leave blank for auto new employee
            'Ramesh Kumar',
            'ramesh.kumar@example.com',
            '9876543212',
            '123456',
            'Male',
            'A+',
            'Technical Support',
            $sampleAgency,
            $sampleShift,
            'Contract',
            '2026-03-01',
            '',
            'No',
            $sampleSupervisor,
            'India',
            'Delhi',
            'New Delhi',
            'Active',
            '18000',
            '9000',
            '3600',
            '1000',
            '0',
            '1500',
            '2900',
            '0',
            '1080',
            '1080',
            '135',
            '585',
            '200',
            '0',
            '432.90',
            '241174.80',
            'Punjab National Bank',
            '1234000100203040',
            'PUNB0123400',
            'PQRST9876M',
            '100765432109',
            'DL/DEL/0087654/000/003',
            '11009876543210987',
            'Bank Transfer',
            '2026-03-01',
            'Contract Staff with ESI & PF',
        ];

        // Sample 4: Quick Entry using Auto-Calculation (Only Monthly Gross & Statutory IDs provided)
        $sample4 = [
            '', // Leave blank for auto new employee
            'Sneha Patel',
            'sneha.patel@example.com',
            '9876543213',
            '',
            'Female',
            'AB+',
            'Executive',
            '',
            $sampleShift,
            'Full Time',
            '2026-04-01',
            '',
            'No',
            $sampleSupervisor,
            'India',
            'Gujarat',
            'Ahmedabad',
            'Active',
            '28000', // Monthly Gross - system auto calculates Basic, HRA, PF, PT, CTC!
            '', // Basic (Auto calculated 50%)
            '', // HRA (Auto calculated 40% of basic)
            '',
            '',
            '1500', // Fixed Bike Maintenance Allowance
            '', // Special allowance (Auto balanced)
            '',
            '', // PF Employee (Auto calculated 12%)
            '', // PF Employer (Auto calculated)
            '',
            '',
            '', // PT (Auto 200)
            '',
            '', // Gratuity (Auto calculated 4.81%)
            '', // CTC (Auto calculated)
            'ICICI Bank',
            '002301567890',
            'ICIC0000023',
            'CKLPS4321B',
            '100654321098',
            'GJ/AHM/0034567/000/004',
            '',
            'Bank Transfer',
            '2026-04-01',
            'Auto calculated structure sample',
        ];

        $callback = function () use ($headers, $sample1, $sample2, $sample3, $sample4) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for perfect Excel encoding display
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headers);
            fputcsv($handle, $sample1);
            fputcsv($handle, $sample2);
            fputcsv($handle, $sample3);
            fputcsv($handle, $sample4);
            fclose($handle);
        };

        return response()->streamDownload($callback, 'employee_payroll_pf_import_template.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // Helper resolution methods
    public function normalizeGender($gender): int
    {
        $value = strtolower(trim((string) $gender));
        return match ($value) {
            '2', 'female', 'f', 'woman' => 2,
            '3', 'other', 'o' => 3,
            default => 1, // Male
        };
    }

    public function normalizeBloodGroup($value): ?string
    {
        if (empty($value) || $value === '-') {
            return null;
        }
        $clean = strtoupper(trim(str_replace(' ', '', (string) $value)));
        $validGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        if (in_array($clean, $validGroups, true)) {
            return $clean;
        }
        return in_array($clean, ['A', 'B', 'AB', 'O'], true) ? $clean . '+' : null;
    }

    public function normalizeEmploymentType($value): string
    {
        if (empty($value)) {
            return 'Full Time';
        }
        $val = strtolower(trim((string) $value));
        return match (true) {
            str_contains($val, 'part') => 'Part Time',
            str_contains($val, 'contract') => 'Contract',
            str_contains($val, 'intern') => 'Intern',
            default => 'Full Time',
        };
    }

    public function normalizeImportDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function normalizeNumeric($value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }
        $clean = preg_replace('/[^0-9.]/', '', (string) $value);
        return (float) $clean;
    }

    public function resolveCountry($value)
    {
        if (empty($value)) {
            return Country::value('id') ?? 101;
        }
        if (is_numeric($value) && Country::where('id', $value)->exists()) {
            return (int) $value;
        }
        $country = Country::where('name', 'like', "%{$value}%")->orWhere('code', 'like', "%{$value}%")->first();
        return $country ? $country->id : (Country::value('id') ?? 101);
    }

    public function resolveState($value, $countryId = null)
    {
        if (empty($value)) {
            return CountryState::when($countryId, fn($q) => $q->where('country_id', $countryId))->value('id') ?? 1;
        }
        if (is_numeric($value) && CountryState::where('id', $value)->exists()) {
            return (int) $value;
        }
        $state = CountryState::when($countryId, fn($q) => $q->where('country_id', $countryId))
            ->where(function ($q) use ($value) {
                $q->where('name', 'like', "%{$value}%")
                  ->orWhere('code', 'like', "%{$value}%");
            })->first();

        if (!$state) {
            $state = CountryState::where('name', 'like', "%{$value}%")->first();
        }
        return $state ? $state->id : (CountryState::when($countryId, fn($q) => $q->where('country_id', $countryId))->value('id') ?? 1);
    }

    public function resolveCity($value, $stateId = null)
    {
        if (empty($value)) {
            return StateCity::when($stateId, fn($q) => $q->where('state_id', $stateId))->value('id') ?? 1;
        }
        if (is_numeric($value) && StateCity::where('id', $value)->exists()) {
            return (int) $value;
        }
        $city = StateCity::when($stateId, fn($q) => $q->where('state_id', $stateId))
            ->where(function ($q) use ($value) {
                $q->where('name', 'like', "%{$value}%")
                  ->orWhere('code', 'like', "%{$value}%");
            })->first();

        if (!$city) {
            $city = StateCity::where('name', 'like', "%{$value}%")->first();
        }
        return $city ? $city->id : (StateCity::when($stateId, fn($q) => $q->where('state_id', $stateId))->value('id') ?? 1);
    }

    public function resolveDesignation($value)
    {
        if (empty($value)) {
            return Designation::where('status', 'Active')->value('id') ?? Designation::value('id') ?? 1;
        }
        if (is_numeric($value) && Designation::where('id', $value)->exists()) {
            return (int) $value;
        }
        $desig = Designation::where('name', 'like', "%{$value}%")
            ->orWhere('code', 'like', "%{$value}%")
            ->first();
        return $desig ? $desig->id : (Designation::where('status', 'Active')->value('id') ?? 1);
    }

    public function resolveAgency($value): ?int
    {
        if (empty($value)) {
            return null;
        }
        if (is_numeric($value) && Agency::where('id', $value)->exists()) {
            return (int) $value;
        }
        $agency = Agency::where('agency_name', 'like', "%{$value}%")
            ->orWhere('agency_code', 'like', "%{$value}%")
            ->first();
        return $agency ? $agency->id : null;
    }

    public function resolveShift($value): int
    {
        if (empty($value)) {
            return Shift::where('status', 'Active')->value('id') ?? 1;
        }
        if (is_numeric($value) && Shift::where('id', $value)->exists()) {
            return (int) $value;
        }
        $shift = Shift::where('shift_name', 'like', "%{$value}%")->first();
        return $shift ? $shift->id : (Shift::where('status', 'Active')->value('id') ?? 1);
    }

    public function resolveSupervisor($value, ?string $employeeEmail = null): ?int
    {
        if (empty($value) || in_array(strtolower(trim((string)$value)), ['none', 'no', 'null', 'n/a', '-', '0'], true)) {
            return null;
        }
        if (is_numeric($value) && Employee::where('employee_id', $value)->exists()) {
            return (int) $value;
        }
        $cleanVal = trim((string) $value);

        // Search among supervisors first
        $supervisor = Employee::where('is_supervisor', 1)
            ->where(function ($q) use ($cleanVal) {
                $q->where('employee_name', 'like', "%{$cleanVal}%")
                  ->orWhere('employee_email', 'like', "%{$cleanVal}%")
                  ->orWhere('employee_mobile', 'like', "%{$cleanVal}%");
            })
            ->first();

        if ($supervisor) {
            return $supervisor->employee_id;
        }

        // Search any employee
        $emp = Employee::where(function ($q) use ($cleanVal) {
                $q->where('employee_name', 'like', "%{$cleanVal}%")
                  ->orWhere('employee_email', 'like', "%{$cleanVal}%")
                  ->orWhere('employee_mobile', 'like', "%{$cleanVal}%");
            })
            ->when($employeeEmail, fn($q) => $q->where('employee_email', '!=', $employeeEmail))
            ->first();

        return $emp ? $emp->employee_id : null;
    }

    public function resolveSupervisorFlag($value, $designationId = null): int
    {
        if ($designationId) {
            $desig = Designation::find($designationId);
            if ($desig && stripos($desig->name, 'Supervisor') !== false) {
                return 1;
            }
        }
        $val = strtolower(trim((string) $value));
        if (in_array($val, ['1', 'yes', 'y', 'true', 'on', 'supervisor'], true)) {
            return 1;
        }
        return 0;
    }
}

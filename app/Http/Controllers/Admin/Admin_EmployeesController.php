<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Country;
use App\Models\CountryState;
use App\Models\StateCity;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Shift;
use App\Services\Payroll\EmployeePayrollImportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class Admin_EmployeesController extends Controller
{


    public function index(Request $request)
    {
        $data['title'] = 'Employee Directory';

        $totalEmployees = Employee::whereNull('deleted_at')->count();
        $activeEmployees = Employee::whereNull('deleted_at')
            ->where(function ($q) {
                $q->where('status', 'Active')
                    ->orWhere('status', '1')
                    ->orWhereNull('status')
                    ->orWhere('status', '');
            })->count();
        $inactiveEmployees = Employee::whereNull('deleted_at')
            ->where('status', 'Inactive')
            ->count();
        $fulltimeEmployees = Employee::whereNull('deleted_at')
            ->where(function ($q) {
                $q->where('employment_type', 'like', '%Full%');
            })->count();

        $data['totalEmployees'] = $totalEmployees;
        $data['activeEmployees'] = $activeEmployees;
        $data['inactiveEmployees'] = $inactiveEmployees;
        $data['fulltimeEmployees'] = $fulltimeEmployees;
        $data['agencies'] = Agency::where('status', 'Active')->orderBy('agency_name')->get();
        $data['designations'] = Designation::where('status', 'Active')->orderBy('name')->get();
        $data['selectedStatus'] = $request->input('status', 'Active');

        if ($request->ajax()) {
            $query = Employee::with(['designation', 'agency', 'shift', 'supervisorEmployee', 'subordinates'])
                ->whereNull('deleted_at');

            if ($request->filled('agency_id')) {
                if ($request->agency_id === 'direct' || $request->agency_id === 'unassigned') {
                    $query->whereNull('agency_id');
                } else {
                    $query->where('agency_id', $request->agency_id);
                }
            }

            $employees = $query->latest('employee_id')->get();

            $formatted = $employees->map(function ($emp) {
                $status = ($emp->status === 'Inactive') ? 'Inactive' : 'Active';
                $designationName = $emp->designation->name ?? ($emp->designation->designation_name ?? 'Staff');
                $agencyName = $emp->agency ? $emp->agency->agency_name : 'Direct / In-House';
                $agencyCode = $emp->agency ? $emp->agency->agency_code : null;
                $hasAgency = !empty($emp->agency_id) && !empty($emp->agency);
                $joiningFormatted = $emp->joining_date ? \Carbon\Carbon::parse($emp->joining_date)->format('d M, Y') : '-';
                $shiftName = $emp->shift ? $emp->shift->shift_name : 'General Shift';
                $shiftTiming = $emp->shift ? date('h:i A', strtotime($emp->shift->start_time)) . ' - ' . date('h:i A', strtotime($emp->shift->end_time)) : '09:30 AM - 06:30 PM';

                $supervisorName = $emp->supervisorEmployee ? $emp->supervisorEmployee->employee_name : '-';
                $subordinatesCount = $emp->subordinates ? $emp->subordinates->count() : 0;

                return [
                    'employee_id' => $emp->employee_id,
                    'employee_name' => $emp->employee_name,
                    'employee_email' => $emp->employee_email,
                    'employee_mobile' => $emp->employee_mobile,
                    'blood_group' => $emp->blood_group ?: '-',
                    'employment_type' => $emp->employment_type ?: 'Full-time',
                    'designation_name' => $designationName,
                    'agency_id' => $emp->agency_id,
                    'agency_name' => $agencyName,
                    'agency_code' => $agencyCode,
                    'has_agency' => $hasAgency,
                    'shift_name' => $shiftName,
                    'shift_timing' => $shiftTiming,
                    'joining_date' => $joiningFormatted,
                    'is_supervisor' => (bool) $emp->is_supervisor,
                    'supervisor_id' => $emp->supervisor_id,
                    'supervisor_name' => $supervisorName,
                    'subordinates_count' => $subordinatesCount,
                    'status' => $status,
                    'profile_image'  => $emp->profile_image,
                ];
            });

            return response()->json(['data' => $formatted]);
        }

        return view('admin.employee.index', $data);
    }

    public function updateStatus(Request $request, $employee_id)
    {
        $employee = Employee::where('employee_id', $employee_id)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ]);
        }

        if ($request->status == 1 || $request->status === 'Active' || $request->status === '1') {
            $employee->activate();
        } else {
            $employee->deactivate();
        }

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }


    public function create()
    {
        $data['title'] = 'Add Employee';
        $data['countries'] = Country::all();
        $data['designations'] = Designation::where('status', 'Active')->get();
        $data['agencies'] = Agency::where('status', 'Active')->orderBy('agency_name')->get();
        $data['shifts'] = Shift::where('status', 'Active')->orderBy('shift_name')->get();
        $data['supervisors'] = Employee::where(function ($q) {
            $q->where('is_supervisor', 1)
              ->orWhereHas('designation', function ($d) {
                  $d->where('name', 'like', '%Supervisor%');
              });
        })->where('status', 'Active')->orderBy('employee_name')->get();

        return view('admin.employee.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_name' => 'required|string|max:255',
            'employee_email' => 'required|email|unique:employees,employee_email',
            'employee_mobile' => 'required',
            'employee_password' => 'required|string|min:6',
            'employee_gender' => 'required|in:1,2,3',
            'blood_group' => 'nullable|string|max:10',
            'posting_country' => 'required',
            'posting_state' => 'required',
            'posting_city' => 'required',
            'joining_date' => 'required|date',
            'employment_type' => 'required',
            'designation_id' => 'required',
            'agency_id' => 'nullable|exists:agencies,id',
            'shift_id' => 'nullable|exists:shifts,id',
            'is_supervisor' => 'nullable',
            'supervisor_id' => 'nullable|exists:employees,employee_id',
            'status' => 'required',
            'profile_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $image_name = null;
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $image_name = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/employees'), $image_name);
        }

        $isSupervisor = $request->boolean('is_supervisor') || $request->input('is_supervisor') === '1' || $request->input('is_supervisor') === 'on';
        $designation = Designation::find($request->designation_id);
        if ($designation && stripos($designation->name, 'Supervisor') !== false) {
            $isSupervisor = true;
        }

        $supervisorId = $request->filled('supervisor_id') ? $request->supervisor_id : null;

        Employee::create([
            'employee_name' => $request->employee_name,
            'employee_email' => $request->employee_email,
            'employee_mobile' => $request->employee_mobile,
            'employee_password' => bcrypt($request->employee_password),
            'employee_gender' => $request->employee_gender,
            'blood_group' => $request->blood_group ?: null,
            'posting_country' => $request->posting_country,
            'posting_state' => $request->posting_state,
            'posting_city' => $request->posting_city,
            'joining_date' => $request->joining_date,
            'date_of_confirmation' => $request->date_of_confirmation,
            'employment_type' => $request->employment_type,
            'designation_id' => $request->designation_id,
            'agency_id' => $request->agency_id ?: null,
            'shift_id' => $request->shift_id ?: (Shift::where('status', 'Active')->value('id') ?? 1),
            'week_off_days' => $request->filled('week_off_days') ? array_values((array)$request->input('week_off_days')) : null,
            'is_supervisor' => $isSupervisor ? 1 : 0,
            'supervisor_id' => $supervisorId,
            'status' => $request->status,
            'profile_image' => $image_name,
        ]);

        return redirect()->route('employees.index')->with('success', 'Employee Added Successfully');
    }

    public function import(Request $request, EmployeePayrollImportService $importService)
    {
        $request->validate([
            'employee_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ], [
            'employee_file.required' => 'Please select a CSV or Excel file to upload.',
            'employee_file.mimes' => 'The import file must be a CSV or Excel file (.csv, .xlsx, .xls).',
            'employee_file.max' => 'The file size must not exceed 10 MB.',
        ]);

        $file = $request->file('employee_file');
        $result = $importService->import($file, ['source' => 'employee']);

        if (!$result['success']) {
            $errorPreview = implode('<br>', array_slice($result['errors'], 0, 5));
            return redirect()->route('employees.index')
                ->with('error', $result['message'] . (!empty($errorPreview) ? '<br>' . $errorPreview : ''));
        }

        $redirect = redirect()->route('employees.index')
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

    public function importSample(EmployeePayrollImportService $importService)
    {
        return $importService->downloadSampleCsv();
    }

    private function mapCanonicalHeader(string $header): ?string
    {
        return match ($header) {
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
            default => null,
        };
    }

    private function readImportRows(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        return $sheet->toArray(null, true, true, false);
    }

    private function normalizeHeader($header): string
    {
        return strtolower(trim(str_replace([' ', '-', '/'], '_', (string) $header)));
    }

    private function normalizeGender($gender): int
    {
        $value = strtolower(trim((string) $gender));

        return match ($value) {
            '2', 'female', 'f', 'woman' => 2,
            '3', 'other', 'o' => 3,
            default => 1, // Male
        };
    }

    private function normalizeImportDate($value): ?string
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

    private function resolveCountry($value)
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

    private function resolveState($value, $countryId = null)
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

    private function resolveCity($value, $stateId = null)
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

    private function resolveDesignation($value)
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

    private function resolveAgency($value): ?int
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

    private function resolveShift($value): int
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

    private function resolveSupervisor($value, ?string $employeeEmail = null): ?int
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

        // Search any employee by name, email, or mobile (excluding current employee)
        $emp = Employee::where(function ($q) use ($cleanVal) {
                $q->where('employee_name', 'like', "%{$cleanVal}%")
                  ->orWhere('employee_email', 'like', "%{$cleanVal}%")
                  ->orWhere('employee_mobile', 'like', "%{$cleanVal}%");
            })
            ->when($employeeEmail, fn($q) => $q->where('employee_email', '!=', $employeeEmail))
            ->first();

        return $emp ? $emp->employee_id : null;
    }

    private function resolveSupervisorFlag($value, $designationId = null): int
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

    private function normalizeBloodGroup($value): ?string
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

    private function normalizeEmploymentType($value): string
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

    public function edit($employee_id)
    {
        $employee = Employee::where('employee_id', $employee_id)->firstOrFail();

        $data['title'] = 'Edit Employee';
        $data['employee'] = $employee;
        $data['countries'] = Country::all();
        $data['designations'] = Designation::where('status', 'Active')->get();
        $data['agencies'] = Agency::where('status', 'Active')->orderBy('agency_name')->get();
        $data['shifts'] = Shift::where('status', 'Active')->orderBy('shift_name')->get();
        $data['states'] = CountryState::where('country_id', $employee->posting_country)->get();
        $data['cities'] = StateCity::where('state_id', $employee->posting_state)->get();

        $data['supervisors'] = Employee::where(function ($q) {
            $q->where('is_supervisor', 1)
              ->orWhereHas('designation', function ($d) {
                  $d->where('name', 'like', '%Supervisor%');
              });
        })->where('employee_id', '!=', $employee_id)
          ->where('status', 'Active')
          ->orderBy('employee_name')
          ->get();

        // Mapped subordinates under this employee if they are a supervisor
        $data['subordinates'] = Employee::with(['designation', 'agency'])
            ->where('supervisor_id', $employee_id)
            ->whereNull('deleted_at')
            ->get();

        // Active employees available to be mapped to this supervisor
        $data['availableEmployees'] = Employee::with(['designation', 'agency'])
            ->where('employee_id', '!=', $employee_id)
            ->where(function ($q) use ($employee_id) {
                $q->whereNull('supervisor_id')
                  ->orWhere('supervisor_id', '!=', $employee_id);
            })
            ->where('status', 'Active')
            ->whereNull('deleted_at')
            ->orderBy('employee_name')
            ->get();

        return view('admin.employee.edit', $data);
    }

    public function update(Request $request, $employee_id)
    {
        $employee = Employee::where('employee_id', $employee_id)->firstOrFail();

        $request->validate([
            'employee_name'   => 'required|string|max:255',
            'employee_email'  => 'required|email|unique:employees,employee_email,' . $employee_id . ',employee_id',
            'employee_mobile' => 'required',
            'employee_gender' => 'required',
            'blood_group'     => 'nullable|string|max:10',
            'joining_date'    => 'required|date',
            'employment_type' => 'required',
            'designation_id'  => 'required',
            'agency_id'       => 'nullable|exists:agencies,id',
            'shift_id'        => 'nullable|exists:shifts,id',
            'is_supervisor'   => 'nullable',
            'supervisor_id'   => 'nullable|exists:employees,employee_id',
            'status'          => 'required'
        ]);

        $data = $request->all();
        $data['agency_id'] = $request->filled('agency_id') ? $request->agency_id : null;
        $data['shift_id'] = $request->filled('shift_id') ? $request->shift_id : ($employee->shift_id ?: (Shift::where('status', 'Active')->value('id') ?? 1));
        $data['week_off_days'] = $request->filled('week_off_days') ? array_values((array)$request->input('week_off_days')) : null;
        $data['blood_group'] = $request->filled('blood_group') ? $request->blood_group : null;

        $isSupervisor = $request->boolean('is_supervisor') || $request->input('is_supervisor') === '1' || $request->input('is_supervisor') === 'on';
        $designation = Designation::find($request->designation_id);
        if ($designation && stripos($designation->name, 'Supervisor') !== false) {
            $isSupervisor = true;
        }

        $data['is_supervisor'] = $isSupervisor ? 1 : 0;
        $data['supervisor_id'] = $request->filled('supervisor_id') ? $request->supervisor_id : null;

        if ($request->filled('employee_password')) {
            $data['employee_password'] = bcrypt($request->employee_password);
        } else {
            unset($data['employee_password']);
        }
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $image_name = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/employees'), $image_name);
            $data['profile_image'] = $image_name;
        } else {
            unset($data['profile_image']);
        }

        $employee->update($data);

        if (isset($data['status'])) {
            if ($data['status'] === 'Inactive' || $data['status'] === '0') {
                $employee->deactivate();
            } else {
                $employee->activate();
            }
        }

        // If employee is a supervisor, map any newly selected subordinate employees
        if ($isSupervisor && $request->filled('mapped_employee_ids')) {
            $mappedIds = array_filter((array) $request->input('mapped_employee_ids', []));
            if (!empty($mappedIds)) {
                Employee::whereIn('employee_id', $mappedIds)
                    ->where('employee_id', '!=', $employee_id)
                    ->update(['supervisor_id' => $employee_id]);
            }
        }

        return redirect()->route('employees.index')->with('success', 'Employee Updated Successfully');
    }

    public function destroy($employee_id)
    {
        $employee = Employee::where('employee_id', $employee_id)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ]);
        }

        $employee->deactivate();
        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employee deleted successfully'
        ]);
    }

    public function show(Request $request, $employee_id)
    {
        $employee = Employee::with(['country', 'state', 'city', 'designation', 'agency', 'shift', 'supervisorEmployee'])
            ->where('employee_id', $employee_id)
            ->firstOrFail();

        $cityName = $employee->city->name ?? (is_numeric($employee->posting_city) ? '' : $employee->posting_city);
        $stateName = $employee->state->name ?? (is_numeric($employee->posting_state) ? '' : $employee->posting_state);

        $countryName = $employee->country->name ?? '';
        if (empty($countryName) && $employee->state && $employee->state->country_id) {
            $country = Country::find($employee->state->country_id);
            $countryName = $country->name ?? '';
        }
        if (empty($countryName) && !empty($employee->posting_country) && !is_numeric($employee->posting_country)) {
            $countryName = $employee->posting_country;
        }
        if (empty($countryName) && (!empty($stateName) || !empty($cityName))) {
            $countryName = 'India';
        }

        $locationParts = array_filter([$cityName, $stateName, $countryName]);
        $fullLocation = !empty($locationParts) ? implode(', ', $locationParts) : '-';

        $shift = $employee->shift ?? Shift::first();
        $shiftName = $shift ? $shift->shift_name : 'General Shift';
        $shiftTiming = $shift ? date('h:i A', strtotime($shift->start_time)) . ' - ' . date('h:i A', strtotime($shift->end_time)) : '09:30 AM - 06:30 PM';
        $gracePeriod = ($shift->grace_period ?? 15) . ' mins';

        $supervisorEmp = $employee->supervisorEmployee;
        $subordinates = $employee->subordinates()->with(['designation', 'agency'])->whereNull('deleted_at')->get()->map(function ($sub) {
            return [
                'employee_id' => $sub->employee_id,
                'employee_name' => $sub->employee_name,
                'employee_email' => $sub->employee_email,
                'designation_name' => $sub->designation?->name ?? 'Staff',
                'agency_name' => $sub->agency ? $sub->agency->agency_name : 'Direct / In-House',
                'status' => $sub->status,
                'profile_image' => $sub->profile_image,
            ];
        });

        $data = [
            'employee_id' => $employee->employee_id,
            'employee_name' => $employee->employee_name,
            'employee_email' => $employee->employee_email,
            'employee_mobile' => $employee->employee_mobile,
            'employee_gender' => $employee->employee_gender,
            'blood_group' => $employee->blood_group ?: '-',
            'joining_date' => $employee->joining_date,
            'date_of_confirmation' => $employee->date_of_confirmation,
            'employment_type' => $employee->employment_type,
            'designation_name' => $employee->designation->name ?? ($employee->designation->designation_name ?? 'Staff'),
            'agency_id' => $employee->agency_id,
            'agency_name' => $employee->agency ? $employee->agency->agency_name : 'Direct / In-House',
            'agency_code' => $employee->agency ? $employee->agency->agency_code : null,
            'has_agency' => !empty($employee->agency_id) && !empty($employee->agency),
            'shift_id' => $employee->shift_id,
            'shift_name' => $shiftName,
            'shift_timing' => $shiftTiming,
            'shift_display' => "{$shiftName} ({$shiftTiming})",
            'grace_period' => $gracePeriod,
            'is_supervisor' => (bool) $employee->is_supervisor,
            'supervisor_id' => $employee->supervisor_id,
            'supervisor_name' => $supervisorEmp ? $supervisorEmp->employee_name : '-',
            'supervisor_designation' => $supervisorEmp?->designation?->name ?? null,
            'subordinates_count' => $subordinates->count(),
            'subordinates' => $subordinates,
            'status' => $employee->status,
            'profile_image' => $employee->profile_image,
            'country_name' => $countryName ?: '-',
            'state_name' => $stateName ?: '-',
            'city_name' => $cityName ?: '-',
            'full_location' => $fullLocation,
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($data);
        }

        $salaryStructure = \App\Models\SalaryStructure::where('employee_id', $employee->employee_id)
            ->where('status', 'Active')
            ->first();

        $documents = \App\Models\EmployeeDocument::where('employee_id', $employee->employee_id)
            ->latest('id')
            ->get();

        return view('admin.employee.show', [
            'title' => ($employee->employee_name ?? 'Employee') . ' - Profile',
            'emp' => (object)$data,
            'employee' => $employee,
            'subordinates' => $subordinates,
            'salaryStructure' => $salaryStructure,
            'documents' => $documents,
        ]);
    }

    public function mapSubordinates(Request $request, $employee_id)
    {
        $supervisor = Employee::where('employee_id', $employee_id)->firstOrFail();

        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,employee_id',
        ]);

        Employee::whereIn('employee_id', $request->employee_ids)
            ->where('employee_id', '!=', $employee_id)
            ->update(['supervisor_id' => $employee_id]);

        return response()->json([
            'success' => true,
            'message' => count($request->employee_ids) . ' employee(s) mapped to ' . $supervisor->employee_name . ' successfully.'
        ]);
    }

    public function unmapSubordinate(Request $request, $employee_id)
    {
        $request->validate([
            'subordinate_id' => 'required|exists:employees,employee_id',
        ]);

        Employee::where('employee_id', $request->subordinate_id)
            ->where('supervisor_id', $employee_id)
            ->update(['supervisor_id' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Employee unmapped from supervisor successfully.'
        ]);
    }



    public function getStates($country_id)
    {
        return response()->json(
            CountryState::where('country_id', $country_id)->get()
        );
    }

    public function getCities($state_id)
    {
        return response()->json(
            StateCity::where('state_id', $state_id)->get()
        );
    }
}

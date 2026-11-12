<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeOtp;
use App\Models\EmployeeAuthActivityLog;
use App\Models\EmployeeAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class EmployeeAuthController extends Controller
{
    /**
     * Helper: Format employee profile data for API responses.
     */
    protected function formatEmployeeData(Employee $employee): array
    {
        // Image URL resolution
        $profileImageUrl = null;
        if (!empty($employee->profile_image)) {
            if (str_starts_with($employee->profile_image, 'http://') || str_starts_with($employee->profile_image, 'https://')) {
                $profileImageUrl = $employee->profile_image;
            } elseif (file_exists(public_path('uploads/employees/' . $employee->profile_image))) {
                $profileImageUrl = asset('uploads/employees/' . $employee->profile_image);
            } else {
                $profileImageUrl = asset('storage/' . $employee->profile_image);
            }
        }

        $supervisor = $employee->supervisorEmployee;
        $supervisorName = $supervisor ? $supervisor->employee_name : ($employee->supervisor?->name ?? null);
        $isSupervisor = (bool) $employee->isSupervisor();
        $subordinatesCount = $isSupervisor ? $employee->subordinates()->count() : 0;

        $shift = $employee->shift ?? \App\Models\Shift::first();
        $shiftTiming = $shift ? date('h:i A', strtotime($shift->start_time)) . ' - ' . date('h:i A', strtotime($shift->end_time)) : '09:30 AM - 06:30 PM';

        return [
            'employee_id' => $employee->employee_id,
            'name' => $employee->employee_name,
            'employee_name' => $employee->employee_name,
            'email' => $employee->employee_email,
            'employee_email' => $employee->employee_email,
            'mobile' => $employee->employee_mobile,
            'employee_mobile' => $employee->employee_mobile,
            'gender' => $employee->employee_gender,
            'blood_group' => $employee->blood_group ?: '-',
            'designation_id' => $employee->designation_id,
            'designation' => $employee->designation?->name ?? $employee->designation?->designation_name ?? 'Staff Member',
            'agency_id' => $employee->agency_id,
            'agency_name' => $employee->agency ? $employee->agency->agency_name : 'Direct / In-House',
            'agency_code' => $employee->agency?->agency_code,
            'agency' => $employee->agency ? [
                'id' => $employee->agency->id,
                'agency_name' => $employee->agency->agency_name,
                'agency_code' => $employee->agency->agency_code,
                'contact_person' => $employee->agency->contact_person ?? null,
                'email' => $employee->agency->email ?? null,
                'phone' => $employee->agency->phone ?? null,
            ] : null,
            'shift_id' => $employee->shift_id,
            'shift_name' => $shift ? $shift->shift_name : 'General Shift',
            'shift_timing' => $shiftTiming,
            'shift' => $shift ? [
                'id' => $shift->id,
                'shift_name' => $shift->shift_name,
                'start_time' => $shift->start_time,
                'end_time' => $shift->end_time,
                'timing' => $shiftTiming,
                'grace_period' => $shift->grace_period ?? 15,
            ] : null,
            'is_supervisor' => $isSupervisor,
            'supervisor_role' => $isSupervisor ? 'Supervisor' : null,
            'team_size' => $subordinatesCount,
            'subordinates_count' => $subordinatesCount,
            'supervisor_capabilities' => $isSupervisor ? [
                'can_approve_leaves' => true,
                'can_reject_leaves' => true,
                'can_view_team_attendance' => true,
                'can_view_team_dashboard' => true,
                'can_view_team_members' => true,
                'can_add_employees' => true,
                'can_edit_employees' => true,
            ] : null,
            'supervisor_id' => $employee->supervisor_id,
            'supervisor_name' => $supervisorName,
            'supervisor' => $supervisor ? [
                'id' => $supervisor->employee_id,
                'name' => $supervisor->employee_name,
                'email' => $supervisor->employee_email,
                'designation' => $supervisor->designation?->name ?? 'Supervisor',
            ] : ($employee->supervisor ? [
                'id' => $employee->supervisor->id,
                'name' => $employee->supervisor->name,
                'email' => $employee->supervisor->email,
            ] : null),
            'status' => $employee->status,
            'profile_image' => $profileImageUrl,
            'joining_date' => $employee->joining_date,
            'employment_type' => $employee->employment_type ?: 'Full-time',
            'is_pin_set' => !empty($employee->login_pin),
            'is_pin_enabled' => (bool) $employee->is_pin_enabled,
            'pin_set_at' => $employee->pin_set_at?->toIso8601String(),
            'last_login_at' => $employee->last_login_at?->toIso8601String(),
        ];
    }

    /**
     * Helper: Generate consistent authentication response.
     */
    protected function authSuccessResponse(Employee $employee, Request $request, string $actionName, string $message = 'Login successful.')
    {
        $deviceName = $request->input('device_name', $request->userAgent() ?: 'Mobile App');
        $token = $employee->createToken($deviceName, ['role:employee'])->plainTextToken;

        $employee->update(['last_login_at' => now()]);
        $employee->load(['designation', 'supervisorEmployee.designation', 'agency', 'shift', 'subordinates']);

        EmployeeAuthActivityLog::record(
            $employee->employee_id,
            $actionName,
            'success',
            $request->ip(),
            $request->userAgent(),
            $deviceName,
            ['message' => 'Login successful']
        );

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'employee' => $this->formatEmployeeData($employee),
            ],
        ], 200);
    }

    /**
     * 1. Universal Login (Supports Email, Mobile, or EmpID with Password or PIN).
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'nullable|string',
            'email' => 'nullable|string',
            'username' => 'nullable|string',
            'mobile' => 'nullable|string',
            'employee_id' => 'nullable',
            'password' => 'nullable|string',
            'pin' => 'nullable|string|regex:/^[0-9]{4,6}$/',
            'device_name' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $login = trim((string) (
            $request->input('login')
            ?? $request->input('email')
            ?? $request->input('username')
            ?? $request->input('mobile')
            ?? $request->input('employee_id')
        ));

        if (empty($login)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide login identifier (Email, Mobile number, or Employee ID).',
                'errors' => [
                    'login' => ['Login identifier (Email, Mobile, or Employee ID) is required.'],
                ],
            ], 422);
        }

        if (!$request->filled('password') && !$request->filled('pin')) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide either password or PIN for authentication.',
                'errors' => [
                    'password' => ['Password or PIN is required.'],
                ],
            ], 422);
        }

        $employee = Employee::where('employee_email', $login)
            ->orWhere('employee_mobile', $login)
            ->orWhere('employee_id', $login)
            ->first();

        if (!$employee) {
            EmployeeAuthActivityLog::record(null, 'login_attempt', 'failed', $request->ip(), $request->userAgent(), $request->input('device_name'), ['login' => $login, 'reason' => 'Employee not found']);
            return response()->json([
                'success' => false,
                'message' => 'Invalid login credentials.',
            ], 401);
        }

        if (!$employee->isActive()) {
            EmployeeAuthActivityLog::record($employee->employee_id, 'login_attempt', 'failed', $request->ip(), $request->userAgent(), $request->input('device_name'), ['reason' => 'Account inactive']);
            return response()->json([
                'success' => false,
                'message' => 'Your employee account is currently inactive. Please contact HR or Administrator.',
            ], 403);
        }

        // Check PIN or Password
        if ($request->filled('pin')) {
            if (!$employee->is_pin_enabled || empty($employee->login_pin)) {
                return response()->json([
                    'success' => false,
                    'message' => 'PIN login is not enabled for this account. Please use password or set up your PIN.',
                ], 400);
            }

            if (!Hash::check($request->input('pin'), $employee->login_pin)) {
                EmployeeAuthActivityLog::record($employee->employee_id, 'login_pin', 'failed', $request->ip(), $request->userAgent(), $request->input('device_name'), ['reason' => 'Invalid PIN']);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid PIN entered.',
                ], 401);
            }

            return $this->authSuccessResponse($employee, $request, 'login_pin', 'Employee login successful with PIN.');
        }

        $password = (string) $request->input('password');
        $passwordMatches = Hash::check($password, $employee->employee_password);

        if (!$passwordMatches && ($employee->employee_password === $password || $employee->employee_password === md5($password))) {
            $employee->update(['employee_password' => Hash::make($password)]);
            $passwordMatches = true;
        }

        if (!$passwordMatches) {
            EmployeeAuthActivityLog::record($employee->employee_id, 'login_password', 'failed', $request->ip(), $request->userAgent(), $request->input('device_name'), ['reason' => 'Invalid Password']);
            return response()->json([
                'success' => false,
                'message' => 'Invalid login credentials. Please check your password.',
            ], 401);
        }

        return $this->authSuccessResponse($employee, $request, 'login_password', 'Employee login successful.');
    }

    /**
     * 2. Login with Mobile Number (Mobile + Password OR Mobile + PIN).
     */
    public function loginWithMobile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string',
            'password' => 'nullable|string',
            'pin' => 'nullable|string|regex:/^[0-9]{4,6}$/',
            'device_name' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!$request->filled('password') && !$request->filled('pin')) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide either password or PIN with your mobile number.',
            ], 422);
        }

        $mobile = trim($request->input('mobile'));
        $employee = Employee::where('employee_mobile', $mobile)->first();

        if (!$employee) {
            EmployeeAuthActivityLog::record(null, 'login_mobile', 'failed', $request->ip(), $request->userAgent(), $request->input('device_name'), ['mobile' => $mobile, 'reason' => 'Mobile not found']);
            return response()->json([
                'success' => false,
                'message' => 'No employee account found with this mobile number.',
            ], 401);
        }

        if (!$employee->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Your employee account is currently inactive. Please contact HR or Administrator.',
            ], 403);
        }

        if ($request->filled('pin')) {
            if (!$employee->is_pin_enabled || empty($employee->login_pin)) {
                return response()->json([
                    'success' => false,
                    'message' => 'PIN login is not enabled for this account. Please login using your password.',
                ], 400);
            }

            if (!Hash::check($request->input('pin'), $employee->login_pin)) {
                EmployeeAuthActivityLog::record($employee->employee_id, 'login_mobile_pin', 'failed', $request->ip(), $request->userAgent(), $request->input('device_name'), ['reason' => 'Invalid PIN']);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid PIN entered.',
                ], 401);
            }

            return $this->authSuccessResponse($employee, $request, 'login_mobile_pin', 'Login successful via mobile and PIN.');
        }

        $password = (string) $request->input('password');
        $passwordMatches = Hash::check($password, $employee->employee_password);

        if (!$passwordMatches && ($employee->employee_password === $password || $employee->employee_password === md5($password))) {
            $employee->update(['employee_password' => Hash::make($password)]);
            $passwordMatches = true;
        }

        if (!$passwordMatches) {
            EmployeeAuthActivityLog::record($employee->employee_id, 'login_mobile_password', 'failed', $request->ip(), $request->userAgent(), $request->input('device_name'), ['reason' => 'Invalid Password']);
            return response()->json([
                'success' => false,
                'message' => 'Invalid password for this mobile number.',
            ], 401);
        }

        return $this->authSuccessResponse($employee, $request, 'login_mobile_password', 'Login successful via mobile and password.');
    }

    /**
     * 3. Login with Employee ID (EmpID + Password OR EmpID + PIN).
     */
    public function loginWithEmpId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required',
            'password' => 'nullable|string',
            'pin' => 'nullable|string|regex:/^[0-9]{4,6}$/',
            'device_name' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!$request->filled('password') && !$request->filled('pin')) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide either password or PIN with your Employee ID.',
            ], 422);
        }

        $empId = trim($request->input('employee_id'));
        $employee = Employee::where('employee_id', $empId)->first();

        if (!$employee) {
            EmployeeAuthActivityLog::record(null, 'login_empid', 'failed', $request->ip(), $request->userAgent(), $request->input('device_name'), ['employee_id' => $empId, 'reason' => 'Employee ID not found']);
            return response()->json([
                'success' => false,
                'message' => 'No employee account found with this Employee ID.',
            ], 401);
        }

        if (!$employee->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Your employee account is currently inactive. Please contact HR or Administrator.',
            ], 403);
        }

        if ($request->filled('pin')) {
            if (!$employee->is_pin_enabled || empty($employee->login_pin)) {
                return response()->json([
                    'success' => false,
                    'message' => 'PIN login is not enabled for this account. Please login using your password.',
                ], 400);
            }

            if (!Hash::check($request->input('pin'), $employee->login_pin)) {
                EmployeeAuthActivityLog::record($employee->employee_id, 'login_empid_pin', 'failed', $request->ip(), $request->userAgent(), $request->input('device_name'), ['reason' => 'Invalid PIN']);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid PIN entered.',
                ], 401);
            }

            return $this->authSuccessResponse($employee, $request, 'login_empid_pin', 'Login successful via Employee ID and PIN.');
        }

        $password = (string) $request->input('password');
        $passwordMatches = Hash::check($password, $employee->employee_password);

        if (!$passwordMatches && ($employee->employee_password === $password || $employee->employee_password === md5($password))) {
            $employee->update(['employee_password' => Hash::make($password)]);
            $passwordMatches = true;
        }

        if (!$passwordMatches) {
            EmployeeAuthActivityLog::record($employee->employee_id, 'login_empid_password', 'failed', $request->ip(), $request->userAgent(), $request->input('device_name'), ['reason' => 'Invalid Password']);
            return response()->json([
                'success' => false,
                'message' => 'Invalid password for this Employee ID.',
            ], 401);
        }

        return $this->authSuccessResponse($employee, $request, 'login_empid_password', 'Login successful via Employee ID and password.');
    }

    /**
     * 4. Quick PIN Login (Mobile/Email/EmpID + PIN).
     */
    public function loginWithPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string',
            'pin' => 'required|string|regex:/^[0-9]{4,6}$/',
            'device_name' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $identifier = trim($request->input('identifier'));
        $employee = Employee::where('employee_mobile', $identifier)
            ->orWhere('employee_email', $identifier)
            ->orWhere('employee_id', $identifier)
            ->first();

        if (!$employee) {
            EmployeeAuthActivityLog::record(null, 'login_pin_quick', 'failed', $request->ip(), $request->userAgent(), $request->input('device_name'), ['identifier' => $identifier, 'reason' => 'User not found']);
            return response()->json([
                'success' => false,
                'message' => 'No employee account found matching credentials.',
            ], 401);
        }

        if (!$employee->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Your employee account is currently inactive. Please contact HR or Administrator.',
            ], 403);
        }

        if (!$employee->is_pin_enabled || empty($employee->login_pin)) {
            return response()->json([
                'success' => false,
                'message' => 'PIN login is not enabled for this account. Please sign in with your password and enable PIN.',
            ], 400);
        }

        if (!Hash::check($request->input('pin'), $employee->login_pin)) {
            EmployeeAuthActivityLog::record($employee->employee_id, 'login_pin_quick', 'failed', $request->ip(), $request->userAgent(), $request->input('device_name'), ['reason' => 'Invalid PIN']);
            return response()->json([
                'success' => false,
                'message' => 'Invalid PIN entered.',
            ], 401);
        }

        return $this->authSuccessResponse($employee, $request, 'login_pin_quick', 'Quick PIN login successful.');
    }

    /**
     * 5. PIN Setup (First-time or Account Setup).
     * Protected by auth:sanctum, employee.api.
     */
    public function setupPin(Request $request)
    {
        $employee = $request->user();

        $rules = [
            'pin' => 'required|string|regex:/^[0-9]{4,6}$/|confirmed',
            'pin_confirmation' => 'required|string',
        ];

        // If employee already had a PIN set, require password verification for safety
        if (!empty($employee->login_pin)) {
            $rules['password'] = 'required|string';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!empty($employee->login_pin) && !Hash::check($request->input('password'), $employee->employee_password)) {
            EmployeeAuthActivityLog::record($employee->employee_id, 'pin_setup', 'failed', $request->ip(), $request->userAgent(), null, ['reason' => 'Password mismatch during override']);
            return response()->json([
                'success' => false,
                'message' => 'Account password incorrect. Cannot setup PIN.',
            ], 400);
        }

        $pin = $request->input('pin');
        $employee->login_pin = Hash::make($pin);
        $employee->is_pin_enabled = true;
        $employee->pin_set_at = now();
        $employee->save();

        EmployeeAuthActivityLog::record($employee->employee_id, 'pin_setup', 'success', $request->ip(), $request->userAgent(), null, ['message' => 'PIN successfully created']);

        return response()->json([
            'success' => true,
            'message' => 'Security PIN created and enabled successfully.',
            'data' => [
                'is_pin_set' => true,
                'is_pin_enabled' => true,
                'pin_set_at' => $employee->pin_set_at->toIso8601String(),
            ],
        ], 200);
    }

    /**
     * 6. Change PIN (Verify Existing PIN -> Set New PIN).
     * Protected by auth:sanctum, employee.api.
     */
    public function changePin(Request $request)
    {
        $employee = $request->user();

        $validator = Validator::make($request->all(), [
            'current_pin' => 'required|string|regex:/^[0-9]{4,6}$/',
            'new_pin' => 'required|string|regex:/^[0-9]{4,6}$/|different:current_pin|confirmed',
            'new_pin_confirmation' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (empty($employee->login_pin)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have a PIN configured yet. Please use the PIN Setup endpoint.',
            ], 400);
        }

        if (!Hash::check($request->input('current_pin'), $employee->login_pin)) {
            EmployeeAuthActivityLog::record($employee->employee_id, 'pin_change', 'failed', $request->ip(), $request->userAgent(), null, ['reason' => 'Current PIN mismatch']);
            return response()->json([
                'success' => false,
                'message' => 'Your current PIN is incorrect.',
            ], 400);
        }

        $employee->login_pin = Hash::make($request->input('new_pin'));
        $employee->is_pin_enabled = true;
        $employee->pin_set_at = now();
        $employee->save();

        EmployeeAuthActivityLog::record($employee->employee_id, 'pin_change', 'success', $request->ip(), $request->userAgent(), null, ['message' => 'PIN successfully changed']);

        return response()->json([
            'success' => true,
            'message' => 'PIN changed successfully.',
            'data' => [
                'is_pin_set' => true,
                'is_pin_enabled' => true,
                'pin_set_at' => $employee->pin_set_at->toIso8601String(),
            ],
        ], 200);
    }

    /**
     * 7. Forgot PIN: Request OTP (Public).
     */
    public function forgotPinRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $identifier = trim($request->input('identifier'));
        $employee = Employee::where('employee_mobile', $identifier)
            ->orWhere('employee_email', $identifier)
            ->orWhere('employee_id', $identifier)
            ->first();

        if (!$employee) {
            EmployeeAuthActivityLog::record(null, 'pin_forgot_otp_request', 'failed', $request->ip(), $request->userAgent(), null, ['identifier' => $identifier, 'reason' => 'Employee not found']);
            return response()->json([
                'success' => false,
                'message' => 'No employee account found matching the provided mobile/email/Employee ID.',
            ], 404);
        }

        if (!$employee->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Your employee account is inactive. Please contact HR or Administrator.',
            ], 403);
        }

        // Invalidate previous active OTPs for this purpose
        EmployeeOtp::where('employee_id', $employee->employee_id)
            ->where('purpose', 'forgot_pin')
            ->where('is_used', false)
            ->update(['is_used' => true]);

        // Generate 6-digit OTP
        $otpCode = (string) random_int(100000, 999999);
        $expiresAt = Carbon::now()->addMinutes(10);

        EmployeeOtp::create([
            'employee_id' => $employee->employee_id,
            'identifier' => $identifier,
            'otp_code' => $otpCode,
            'purpose' => 'forgot_pin',
            'expires_at' => $expiresAt,
            'is_used' => false,
        ]);

        EmployeeAuthActivityLog::record($employee->employee_id, 'pin_forgot_otp_request', 'success', $request->ip(), $request->userAgent(), null, ['purpose' => 'forgot_pin']);

        // Mask mobile for security display
        $rawMobile = $employee->employee_mobile ?? '';
        $maskedMobile = strlen($rawMobile) > 4
            ? str_repeat('*', strlen($rawMobile) - 4) . substr($rawMobile, -4)
            : $rawMobile;

        return response()->json([
            'success' => true,
            'message' => 'A 6-digit OTP has been generated for PIN recovery. It will expire in 10 minutes.',
            'data' => [
                'identifier' => $identifier,
                'masked_mobile' => $maskedMobile,
                'expires_at' => $expiresAt->toIso8601String(),
                'expires_in_minutes' => 10,
                // In Staging/Testing environments, provide OTP in response for testing convenience:
                'otp_code' => $otpCode,
            ],
        ], 200);
    }

    /**
     * 8. Reset PIN with OTP (Public).
     */
    public function resetPinWithOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string',
            'otp' => 'required|string|regex:/^[0-9]{6}$/',
            'new_pin' => 'required|string|regex:/^[0-9]{4,6}$/|confirmed',
            'new_pin_confirmation' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $identifier = trim($request->input('identifier'));
        $employee = Employee::where('employee_mobile', $identifier)
            ->orWhere('employee_email', $identifier)
            ->orWhere('employee_id', $identifier)
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee account not found.',
            ], 404);
        }

        if (!$employee->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Your employee account is inactive. Please contact HR or Administrator.',
            ], 403);
        }

        $otpRecord = EmployeeOtp::where('employee_id', $employee->employee_id)
            ->where('purpose', 'forgot_pin')
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->orderBy('id', 'desc')
            ->first();

        if (!$otpRecord || $otpRecord->otp_code !== trim($request->input('otp'))) {
            EmployeeAuthActivityLog::record($employee->employee_id, 'pin_reset_otp', 'failed', $request->ip(), $request->userAgent(), null, ['reason' => 'Invalid or expired OTP']);
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP. Please request a new OTP.',
            ], 422);
        }

        // Mark OTP as used
        $otpRecord->update(['is_used' => true]);

        // Update PIN
        $employee->login_pin = Hash::make($request->input('new_pin'));
        $employee->is_pin_enabled = true;
        $employee->pin_set_at = now();
        $employee->save();

        EmployeeAuthActivityLog::record($employee->employee_id, 'pin_reset_otp', 'success', $request->ip(), $request->userAgent(), null, ['message' => 'PIN successfully reset using OTP']);

        return response()->json([
            'success' => true,
            'message' => 'Your security PIN has been reset successfully. You can now login using your new PIN.',
            'data' => [
                'is_pin_set' => true,
                'is_pin_enabled' => true,
                'pin_set_at' => $employee->pin_set_at->toIso8601String(),
            ],
        ], 200);
    }

    /**
     * 9. Enable / Disable PIN Login (Toggle).
     * Protected by auth:sanctum, employee.api.
     */
    public function togglePinLogin(Request $request)
    {
        $employee = $request->user();

        $validator = Validator::make($request->all(), [
            'enable' => 'required|boolean',
            'pin' => 'required|string|regex:/^[0-9]{4,6}$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (empty($employee->login_pin)) {
            return response()->json([
                'success' => false,
                'message' => 'No PIN is set for this account. Please setup your PIN first.',
            ], 400);
        }

        if (!Hash::check($request->input('pin'), $employee->login_pin)) {
            EmployeeAuthActivityLog::record($employee->employee_id, 'pin_toggle', 'failed', $request->ip(), $request->userAgent(), null, ['reason' => 'PIN mismatch']);
            return response()->json([
                'success' => false,
                'message' => 'Verification failed. PIN is incorrect.',
            ], 401);
        }

        $enable = filter_var($request->input('enable'), FILTER_VALIDATE_BOOLEAN);
        $employee->is_pin_enabled = $enable;
        $employee->save();

        $action = $enable ? 'pin_toggle_enabled' : 'pin_toggle_disabled';
        EmployeeAuthActivityLog::record($employee->employee_id, $action, 'success', $request->ip(), $request->userAgent(), null, ['is_pin_enabled' => $enable]);

        return response()->json([
            'success' => true,
            'message' => $enable ? 'PIN login has been enabled successfully.' : 'PIN login has been disabled.',
            'data' => [
                'is_pin_set' => true,
                'is_pin_enabled' => (bool) $employee->is_pin_enabled,
                'pin_set_at' => $employee->pin_set_at?->toIso8601String(),
            ],
        ], 200);
    }

    /**
     * 10. Get Current PIN Status.
     * Protected by auth:sanctum, employee.api.
     */
    public function pinStatus(Request $request)
    {
        $employee = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'employee_id' => $employee->employee_id,
                'is_pin_set' => !empty($employee->login_pin),
                'is_pin_enabled' => (bool) $employee->is_pin_enabled,
                'pin_set_at' => $employee->pin_set_at?->toIso8601String(),
                'last_login_at' => $employee->last_login_at?->toIso8601String(),
            ],
        ], 200);
    }

    /**
     * 11. Get Authentication & Security Activity Logs.
     * Protected by auth:sanctum, employee.api.
     */
    public function authLogs(Request $request)
    {
        $employee = $request->user();

        $logs = EmployeeAuthActivityLog::where('employee_id', $employee->employee_id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get(['id', 'action', 'status', 'ip_address', 'device_name', 'details', 'created_at']);

        return response()->json([
            'success' => true,
            'data' => $logs,
        ], 200);
    }

    /**
     * Employee Logout (Revoke Current Token).
     */
    public function logout(Request $request)
    {
        $employee = $request->user();

        if ($employee && method_exists($employee, 'currentAccessToken') && $employee->currentAccessToken()) {
            $employee->currentAccessToken()->delete();
        }

        if ($employee) {
            EmployeeAuthActivityLog::record($employee->employee_id, 'logout', 'success', $request->ip(), $request->userAgent());
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully. Token revoked.',
        ], 200);
    }

    /**
     * Get Authenticated Employee Profile.
     */
    public function profile(Request $request)
    {
        $employee = $request->user()->load(['designation', 'supervisor', 'country', 'state', 'city', 'agency', 'shift']);

        $today = Carbon::now('Asia/Kolkata')->toDateString();
        $todayAttendance = EmployeeAttendance::where('employee_id', $employee->employee_id)
            ->where('attendance_date', $today)
            ->first();

        $timerPayload = $todayAttendance ? $todayAttendance->getTimerPayload() : EmployeeAttendance::defaultTimerPayload();

        return response()->json([
            'success' => true,
            'data' => array_merge($this->formatEmployeeData($employee), [
                'designation' => [
                    'id' => $employee->designation?->DesignationID ?? $employee->designation?->id,
                    'name' => $employee->designation?->designation_name ?? $employee->designation?->name ?? 'Staff Member',
                ],
                'agency' => $employee->agency ? [
                    'id' => $employee->agency->id,
                    'agency_name' => $employee->agency->agency_name,
                    'agency_code' => $employee->agency->agency_code,
                    'contact_person' => $employee->agency->contact_person,
                    'email' => $employee->agency->email,
                    'phone' => $employee->agency->phone,
                    'status' => $employee->agency->status,
                ] : null,
                'supervisor' => $employee->supervisor ? [
                    'id' => $employee->supervisor->id,
                    'name' => $employee->supervisor->name,
                    'email' => $employee->supervisor->email,
                ] : null,
                'shift' => $employee->shift ? [
                    'id' => $employee->shift->id,
                    'shift_name' => $employee->shift->shift_name,
                    'start_time' => $employee->shift->start_time,
                    'end_time' => $employee->shift->end_time,
                    'grace_period' => $employee->shift->grace_period,
                    'break_duration' => $employee->shift->break_duration,
                ] : [
                    'id' => null,
                    'shift_name' => 'General Shift (Default)',
                    'start_time' => '09:30:00',
                    'end_time' => '18:30:00',
                    'grace_period' => 15,
                    'break_duration' => 60,
                ],
                'location' => [
                    'country' => $employee->country?->name,
                    'state' => $employee->state?->name,
                    'city' => $employee->city?->name,
                ],
                'today_attendance' => [
                    'date' => $today,
                    'is_checked_in' => (bool) ($todayAttendance?->check_in_at),
                    'is_checked_out' => (bool) ($todayAttendance?->check_out_at),
                    'check_in_at' => optional($todayAttendance?->check_in_at)->toDateTimeString(),
                    'check_in_time' => optional($todayAttendance?->check_in_at)?->format('h:i A'),
                    'check_out_at' => optional($todayAttendance?->check_out_at)->toDateTimeString(),
                    'check_out_time' => optional($todayAttendance?->check_out_at)?->format('h:i A'),
                    'status' => $todayAttendance?->status ?? 'not_marked',
                    'timer' => $timerPayload,
                ],
                'date_of_confirmation' => $employee->date_of_confirmation,
                'assigned_projects' => [],
                'supervisor_capabilities' => $employee->isSupervisor() ? [
                    'can_approve_leaves' => true,
                    'can_reject_leaves' => true,
                    'can_view_team_attendance' => true,
                    'can_view_team_dashboard' => true,
                    'can_view_team_members' => true,
                    'can_add_employees' => true,
                    'can_edit_employees' => true,
                ] : null,
                'team_summary' => $employee->isSupervisor() ? [
                    'total_members' => $employee->teamQuery()->count(),
                ] : null,
            ]),
        ], 200);
    }

    /**
     * Get List of Active Agencies.
     */
    public function agencies(Request $request)
    {
        $agencies = \App\Models\Agency::where('status', 'Active')
            ->orderBy('agency_name')
            ->get(['id', 'agency_name', 'agency_code', 'contact_person', 'email', 'phone', 'city', 'state']);

        return response()->json([
            'success' => true,
            'data' => $agencies,
        ], 200);
    }
}

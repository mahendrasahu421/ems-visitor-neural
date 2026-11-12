<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmployeeAttendanceController;
use App\Http\Controllers\Api\Employee\EmployeeAuthController;
use App\Http\Controllers\Api\Employee\EmployeeAttendanceApiController;
use App\Http\Controllers\Api\Employee\EmployeeCalendarApiController;
use App\Http\Controllers\Api\Employee\EmployeeLeaveApiController;
use App\Http\Controllers\Api\Employee\EmployeeSupervisorApiController;
use App\Http\Controllers\Api\Employee\EmployeeNotificationController;
use App\Http\Controllers\Api\Employee\EmployeePayrollApiController;
use App\Http\Controllers\Api\Common\HolidayApiController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| All routes are prefixed with /api and protected by versioning.
|
*/

// Legacy quick toggle route, now protected by the employee-only API contract.
Route::post('employee/attendance/toggle', [EmployeeAttendanceController::class, 'toggle'])
    ->middleware(['auth:sanctum', 'employee.api'])
    ->name('api.employee.attendance.toggle');

Route::prefix('v1')->group(function () {

    // Legal Compliance Endpoints (Privacy Policy & Terms)
    Route::get('privacy-policy', [\App\Http\Controllers\LegalController::class, 'privacyPolicy'])->name('api.v1.privacy.policy');
    Route::get('terms-and-conditions', [\App\Http\Controllers\LegalController::class, 'termsConditions'])->name('api.v1.terms.conditions');

    /*
    |--------------------------------------------------------------------------
    | 1. Employee API Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('employee')->group(function () {
        // Public Auth Routes
        Route::prefix('auth')->group(function () {
            Route::post('login', [EmployeeAuthController::class, 'login'])->name('api.v1.employee.login');
            Route::post('login/mobile', [EmployeeAuthController::class, 'loginWithMobile'])->name('api.v1.employee.login.mobile');
            Route::post('login/empid', [EmployeeAuthController::class, 'loginWithEmpId'])->name('api.v1.employee.login.empid');
            Route::post('login/pin', [EmployeeAuthController::class, 'loginWithPin'])->name('api.v1.employee.login.pin');
            Route::post('pin/forgot-request', [EmployeeAuthController::class, 'forgotPinRequest'])->name('api.v1.employee.pin.forgot-request');
            Route::post('pin/reset', [EmployeeAuthController::class, 'resetPinWithOtp'])->name('api.v1.employee.pin.reset');
        });

        // Protected Employee Routes
        Route::middleware(['auth:sanctum', 'employee.api'])->group(function () {
            // Auth & PIN Management
            Route::prefix('auth')->group(function () {
                Route::post('logout', [EmployeeAuthController::class, 'logout'])->name('api.v1.employee.logout');
                Route::post('pin/setup', [EmployeeAuthController::class, 'setupPin'])->name('api.v1.employee.pin.setup');
                Route::post('pin/change', [EmployeeAuthController::class, 'changePin'])->name('api.v1.employee.pin.change');
                Route::post('pin/toggle', [EmployeeAuthController::class, 'togglePinLogin'])->name('api.v1.employee.pin.toggle');
                Route::get('pin/status', [EmployeeAuthController::class, 'pinStatus'])->name('api.v1.employee.pin.status');
                Route::get('activity-logs', [EmployeeAuthController::class, 'authLogs'])->name('api.v1.employee.auth.logs');
            });
            Route::get('profile', [EmployeeAuthController::class, 'profile'])->name('api.v1.employee.profile');
            Route::get('agencies', [EmployeeAuthController::class, 'agencies'])->name('api.v1.employee.agencies');

            // Attendance
            Route::prefix('attendance')->group(function () {
                Route::post('check-in', [EmployeeAttendanceApiController::class, 'checkIn'])->name('api.v1.employee.attendance.checkin');
                Route::post('check-out', [EmployeeAttendanceApiController::class, 'checkOut'])->name('api.v1.employee.attendance.checkout');
                Route::get('today', [EmployeeAttendanceApiController::class, 'today'])->name('api.v1.employee.attendance.today');
                Route::get('history', [EmployeeAttendanceApiController::class, 'history'])->name('api.v1.employee.attendance.history');
            });

            // Calendar
            Route::prefix('calendar')->group(function () {
                Route::get('/', [EmployeeCalendarApiController::class, 'calendar'])->name('api.v1.employee.calendar');
                Route::get('date-details', [EmployeeCalendarApiController::class, 'dateDetails'])->name('api.v1.employee.calendar.date-details');
            });

            // Leave Management
            Route::prefix('leave')->group(function () {
                Route::get('types', [EmployeeLeaveApiController::class, 'types'])->name('api.v1.employee.leave.types');
                Route::post('apply', [EmployeeLeaveApiController::class, 'apply'])->name('api.v1.employee.leave.apply');
                Route::get('list', [EmployeeLeaveApiController::class, 'list'])->name('api.v1.employee.leave.list');
                Route::get('{id}', [EmployeeLeaveApiController::class, 'details'])->name('api.v1.employee.leave.details');
            });

            // Payroll & Payslips
            Route::prefix('payroll')->group(function () {
                Route::get('salary-structure', [EmployeePayrollApiController::class, 'salaryStructure'])->name('api.v1.employee.payroll.salary-structure');
                Route::get('payslips', [EmployeePayrollApiController::class, 'payslips'])->name('api.v1.employee.payroll.payslips');
                Route::get('payslips/{id}', [EmployeePayrollApiController::class, 'show'])->name('api.v1.employee.payroll.payslip.show');
                Route::get('payslips/{id}/download', [EmployeePayrollApiController::class, 'downloadPdf'])->name('api.v1.employee.payroll.payslip.download');
                Route::get('annual-summary', [EmployeePayrollApiController::class, 'annualSummary'])->name('api.v1.employee.payroll.annual-summary');
            });

            // Supervisor Management Features (Mobile App Admin capabilities)

            Route::prefix('supervisor')->group(function () {
                Route::get('dashboard', [EmployeeSupervisorApiController::class, 'dashboard'])->name('api.v1.employee.supervisor.dashboard');
                Route::get('leaves', [EmployeeSupervisorApiController::class, 'leaves'])->name('api.v1.employee.supervisor.leaves.index');
                Route::get('leaves/{id}', [EmployeeSupervisorApiController::class, 'leaveDetails'])->name('api.v1.employee.supervisor.leaves.details');
                Route::post('leaves/{id}/approve', [EmployeeSupervisorApiController::class, 'approveLeave'])->name('api.v1.employee.supervisor.leaves.approve');
                Route::post('leaves/{id}/reject', [EmployeeSupervisorApiController::class, 'rejectLeave'])->name('api.v1.employee.supervisor.leaves.reject');
                Route::post('leaves/apply-for-member', [EmployeeSupervisorApiController::class, 'applyLeaveForMember'])->name('api.v1.employee.supervisor.leaves.apply-for-member');
                Route::get('team/attendance', [EmployeeSupervisorApiController::class, 'teamAttendance'])->name('api.v1.employee.supervisor.team.attendance');
                Route::get('meta/form-options', [EmployeeSupervisorApiController::class, 'formOptions'])->name('api.v1.employee.supervisor.meta.form-options');
                Route::get('team/members', [EmployeeSupervisorApiController::class, 'teamMembers'])->name('api.v1.employee.supervisor.team.members');
                Route::post('team/members', [EmployeeSupervisorApiController::class, 'createMember'])->name('api.v1.employee.supervisor.team.members.create');
                Route::get('team/members/{employee_id}', [EmployeeSupervisorApiController::class, 'getMemberProfile'])->name('api.v1.employee.supervisor.team.member-profile');
                Route::put('team/members/{employee_id}', [EmployeeSupervisorApiController::class, 'updateMember'])->name('api.v1.employee.supervisor.team.members.update');
                Route::get('team/members/{employee_id}/attendance', [EmployeeSupervisorApiController::class, 'memberAttendanceHistory'])->name('api.v1.employee.supervisor.team.member-attendance');
                Route::get('team/members/{employee_id}/leave-balances', [EmployeeSupervisorApiController::class, 'memberLeaveBalances'])->name('api.v1.employee.supervisor.team.member-leave-balances');
                Route::get('team/monthly-summary', [EmployeeSupervisorApiController::class, 'teamMonthlySummary'])->name('api.v1.employee.supervisor.team.monthly-summary');

                // Supervisor Reports Endpoints
                Route::prefix('reports')->group(function () {
                    Route::get('summary', [EmployeeSupervisorApiController::class, 'reportSummary'])->name('api.v1.employee.supervisor.reports.summary');

                    // Attendance Punch Logs Report & Exports
                    Route::get('attendance', [EmployeeSupervisorApiController::class, 'attendanceReport'])->name('api.v1.employee.supervisor.reports.attendance');
                    Route::get('attendance/export/excel', [EmployeeSupervisorApiController::class, 'exportAttendanceLogExcel'])->name('api.v1.employee.supervisor.reports.attendance.export.excel');
                    Route::get('attendance/export/pdf', [EmployeeSupervisorApiController::class, 'exportAttendanceLogPdf'])->name('api.v1.employee.supervisor.reports.attendance.export.pdf');

                    // Attendance Monthly Register (1..31 Grid) Report & Exports
                    Route::get('attendance-register', [EmployeeSupervisorApiController::class, 'attendanceRegisterReport'])->name('api.v1.employee.supervisor.reports.attendance-register');
                    Route::get('attendance-register/export/excel', [EmployeeSupervisorApiController::class, 'exportAttendanceRegisterExcel'])->name('api.v1.employee.supervisor.reports.attendance-register.export.excel');
                    Route::get('attendance-register/export/pdf', [EmployeeSupervisorApiController::class, 'exportAttendanceRegisterPdf'])->name('api.v1.employee.supervisor.reports.attendance-register.export.pdf');

                    // Daily Work Reports
                    Route::get('daily-work-reports', [EmployeeSupervisorApiController::class, 'dailyWorkReports'])->name('api.v1.employee.supervisor.reports.daily-work-reports');

                    // Leave Report & Exports
                    Route::get('leaves', [EmployeeSupervisorApiController::class, 'leaveReport'])->name('api.v1.employee.supervisor.reports.leaves');
                    Route::get('leaves/export/excel', [EmployeeSupervisorApiController::class, 'exportLeaveReportExcel'])->name('api.v1.employee.supervisor.reports.leaves.export.excel');
                    Route::get('leaves/export/pdf', [EmployeeSupervisorApiController::class, 'exportLeaveReportPdf'])->name('api.v1.employee.supervisor.reports.leaves.export.pdf');
                });

                // Supervisor Team Push Notification
                Route::post('team/notify', [EmployeeNotificationController::class, 'sendTeamNotification'])->name('api.v1.employee.supervisor.team.notify');
            });

            // Push Notification & Device Token APIs
            Route::post('fcm-token', [EmployeeNotificationController::class, 'storeFcmToken'])->name('api.v1.employee.fcm.store');
            Route::post('fcm-token/delete', [EmployeeNotificationController::class, 'deleteFcmToken'])->name('api.v1.employee.fcm.delete');
            Route::get('notifications', [EmployeeNotificationController::class, 'index'])->name('api.v1.employee.notifications.index');
            Route::post('notifications/{id}/read', [EmployeeNotificationController::class, 'markAsRead'])->name('api.v1.employee.notifications.read');
            Route::post('notifications/mark-all-read', [EmployeeNotificationController::class, 'markAllAsRead'])->name('api.v1.employee.notifications.mark-all-read');
        });
    });


    /*
    |--------------------------------------------------------------------------
    | 3. Common Holiday APIs
    |--------------------------------------------------------------------------
    */
    Route::prefix('holidays')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [HolidayApiController::class, 'index'])->name('api.v1.holidays.index');
        Route::get('restricted', [HolidayApiController::class, 'restricted'])->name('api.v1.holidays.restricted');
        Route::get('calendar', [HolidayApiController::class, 'calendar'])->name('api.v1.holidays.calendar');
    });

});

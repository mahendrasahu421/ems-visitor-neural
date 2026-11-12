<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\Clients\ClientsController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\Admin_EmployeesController;
use App\Http\Controllers\Admin\EmployeeReportController;
use App\Http\Controllers\Hr\HumanResourceController;
use App\Http\Controllers\Hr\JobCategoryController;
use App\Http\Controllers\Hr\AttendanceController;
use App\Http\Controllers\Hr\PerformanceController;
use App\Http\Controllers\Hr\PerformanceReviewController;
use App\Http\Controllers\StateCityController;
use App\Http\Controllers\SkillsController;
use App\Http\Controllers\MapRolePermission;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Employee\leavesController;
use App\Http\Controllers\AdminHR\DashboardController;
use App\Http\Controllers\AdminHR\DocumentController;
use App\Http\Controllers\Masters\CompanyController;
use App\Http\Controllers\Masters\AgencyController;
use App\Http\Controllers\Masters\BranchController;
use App\Http\Controllers\Masters\DepartmentController;
use App\Http\Controllers\Masters\DesignationController;
use App\Http\Controllers\Masters\ShiftController;
use App\Http\Controllers\Masters\LeaveTypeController;
use App\Http\Controllers\Masters\HolidayController;
use App\Http\Controllers\Masters\PolicyController;
use App\Http\Controllers\Masters\CategoryController;
use App\Http\Controllers\Masters\SalaryComponentController;
use App\Http\Controllers\Masters\SalaryStructureController;
use App\Http\Controllers\Masters\CalculationRulesController;
use App\Http\Controllers\Masters\TaxationController;
use App\Http\Controllers\Masters\ApplicabilityController;
use App\Http\Controllers\Masters\PaymentFrequencyController;
use App\Http\Controllers\Masters\PaymentModeController;
use App\Http\Controllers\Masters\VisibilityControlController;
use App\Http\Controllers\Masters\JobsController;
use App\Http\Controllers\Attendance\AttendanceDashboardController;
use App\Http\Controllers\Attendance\AttendanceOrgReportController;
use App\Http\Controllers\Attendance\AttendanceRegisterReportController;
use App\Http\Controllers\Attendance\ConsistentAttendeesController;
use App\Http\Controllers\Employee\DailyReportController;
use App\Http\Controllers\Employee\EmployeeTicketController;
use App\Http\Controllers\EmployeeAuthController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Employee\AttendanceCotroller as EmployeeAttendanceController;
use App\Http\Controllers\Masters\JafController;
use App\Http\Controllers\Masters\LeaveMappingController;
use App\Http\Controllers\Masters\OnboardingController;
use App\Http\Controllers\PMT\FeedbackController;
use App\Http\Controllers\PMT\ReviewEmployeeController;
use App\Http\Controllers\PMT\SelfAppraisalController;
use App\Http\Controllers\PMT\ViewFeedbackController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Invoice\InvoiceController;
use App\Http\Controllers\LegacyNeuralInvoiceController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Client\ClientAuthController;
use App\Http\Controllers\Client\ClientDashboardController;
use App\Http\Controllers\Client\ClientTicketController;
use App\Http\Controllers\LegalController;

Route::get('/districts/search', [StateCityController::class, 'search'])->name('districts.search');
Route::get('/skills/search', [SkillsController::class, 'skillsSearch'])->name('skills.search');

Route::put('/employee/update-profile', [EmployeeController::class, 'updateProfile'])
    ->name('employee.updateProfile')
    ->middleware(['auth:employee', 'employee.active.web']);
Route::get('/employee/get-states', [EmployeeController::class, 'getStates'])->name('employee.getStates');
Route::get('/employee/get-cities', [EmployeeController::class, 'getCities'])->name('employee.getCities');

Route::middleware(['auth'])->group(function () {

    Route::get('/leave-type/get-days/{id}', [LeaveMappingController::class, 'getDays']);
    Route::get('/get-departments/{category_id}', [DashboardController::class, 'getDepartments']);
    Route::get('/get-designation/{department_id}', [DashboardController::class, 'getDesignation']);
    Route::get('/get-state/{country_id}', [DashboardController::class, 'getState'])->name('getState');
    Route::get('/get-city/{state_id}', [DashboardController::class, 'getCity'])->name('getCity');

    Route::get('/home', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [HumanResourceController::class, 'index'])->name('dashboard');
    Route::resource('categories', JobCategoryController::class);

    Route::get('masters/organisation/company', [CompanyController::class, 'index'])->name('masters.organisation.company');
    Route::get('masters/organisation/company/create', [CompanyController::class, 'create'])->name('masters.organisation.company.create');
    Route::get('masters/organisation/company/store', [CompanyController::class, 'create'])->name('masters.organisation.company.store');

    Route::get('masters/organisation/branch', [BranchController::class, 'index'])->name('masters.organisation.branch');
    Route::get('masters/organisation/branch/create', [BranchController::class, 'create'])->name('masters.organisation.branch.create');
    Route::post('masters/organisation/branch/store', [BranchController::class, 'store'])->name('masters.organisation.branch.store');

    // Designation Master
    Route::get('masters/organisation/designation', [DesignationController::class, 'index'])->name('masters.organisation.designation');
    Route::get('masters/organisation/designation/list', [DesignationController::class, 'list'])->name('masters.organisation.designation.list');
    Route::get('masters/organisation/designation/create', [DesignationController::class, 'create'])->name('masters.organisation.designation.create');
    Route::post('masters/organisation/designation/store', [DesignationController::class, 'store'])->name('masters.organisation.designation.store');
    Route::get('masters/organisation/designation/edit/{id}', [DesignationController::class, 'edit'])->name('masters.organisation.designation.edit');
    Route::match(['post', 'put'], 'masters/organisation/designation/update/{id}', [DesignationController::class, 'update'])->name('masters.organisation.designation.update');
    Route::match(['delete', 'post'], 'masters/organisation/designation/destroy/{id}', [DesignationController::class, 'destroy'])->name('masters.organisation.designation.destroy');

    // Designation settings aliases
    Route::get('settings/designation', [DesignationController::class, 'index'])->name('settings.designation');
    Route::get('settings/designation/list', [DesignationController::class, 'list'])->name('settings.designation.list');
    Route::get('settings/designation/create', [DesignationController::class, 'create'])->name('settings.designation.create');
    Route::post('settings/designation/store', [DesignationController::class, 'store'])->name('settings.designation.store');
    Route::get('settings/designation/edit/{id}', [DesignationController::class, 'edit'])->name('settings.designation.edit');
    Route::match(['post', 'put'], 'settings/designation/update/{id}', [DesignationController::class, 'update'])->name('settings.designation.update');
    Route::match(['delete', 'post'], 'settings/designation/destroy/{id}', [DesignationController::class, 'destroy'])->name('settings.designation.destroy');

    // Shift Master
    Route::get('masters/organisation/shift', [ShiftController::class, 'index'])->name('masters.organisation.shift');
    Route::get('masters/organisation/shift/list', [ShiftController::class, 'list'])->name('masters.organisation.shift.list');
    Route::get('masters/organisation/shift/create', [ShiftController::class, 'create'])->name('masters.organisation.shift.create');
    Route::post('masters/organisation/shift/store', [ShiftController::class, 'store'])->name('masters.organisation.shift.store');
    Route::get('masters/organisation/shift/edit/{id}', [ShiftController::class, 'edit'])->name('masters.organisation.shift.edit');
    Route::match(['post', 'put'], 'masters/organisation/shift/update/{id}', [ShiftController::class, 'update'])->name('masters.organisation.shift.update');
    Route::delete('masters/organisation/shift/destroy/{id}', [ShiftController::class, 'destroy'])->name('masters.organisation.shift.destroy');
    Route::delete('masters/organisation/shift/delete/{id}', [ShiftController::class, 'destroy'])->name('masters.organisation.shift.delete');

    // Shift settings aliases
    Route::get('settings/shift', [ShiftController::class, 'index'])->name('settings.shift');
    Route::get('settings/shift/list', [ShiftController::class, 'list'])->name('settings.shift.list');
    Route::get('settings/shift/create', [ShiftController::class, 'create'])->name('settings.shift.create');
    Route::post('settings/shift/store', [ShiftController::class, 'store'])->name('settings.shift.store');
    Route::get('settings/shift/edit/{id}', [ShiftController::class, 'edit'])->name('settings.shift.edit');
    Route::match(['post', 'put'], 'settings/shift/update/{id}', [ShiftController::class, 'update'])->name('settings.shift.update');
    Route::delete('settings/shift/destroy/{id}', [ShiftController::class, 'destroy'])->name('settings.shift.destroy');

    Route::prefix('masters/organisation/agency')->name('masters.organisation.agency')->group(function () {
        Route::get('/', [AgencyController::class, 'index']);
        Route::post('/', [AgencyController::class, 'store'])->name('.store');
        Route::get('/mapping', [AgencyController::class, 'mapping'])->name('.mapping');
        Route::post('/mapping', [AgencyController::class, 'saveMapping'])->name('.mapping.save');
        Route::get('/{id}', [AgencyController::class, 'show'])->name('.show');
        Route::put('/{id}', [AgencyController::class, 'update'])->name('.update');
        Route::delete('/{id}', [AgencyController::class, 'destroy'])->name('.destroy');
        Route::post('/status/{id}', [AgencyController::class, 'updateStatus'])->name('.status');
    });

    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        Route::get('create', [ProjectController::class, 'create'])->name('create');
        Route::post('store', [ProjectController::class, 'store'])->name('store');
        Route::get('edit/{id}', [ProjectController::class, 'edit'])->name('edit');
        Route::put('update/{id}', [ProjectController::class, 'update'])->name('update');
        Route::get('list', [ProjectController::class, 'list'])->name('list');
        Route::delete('delete/{id}', [ProjectController::class, 'destroy'])->name('delete');
        Route::post('status/{id}', [ProjectController::class, 'updateStatus'])->name('status');
    });

    Route::prefix('admin/clients')->group(function () {
        Route::get('/', [ClientsController::class, 'index'])->name('admin.clients');
        Route::post('create', [ClientsController::class, 'create'])->name('admin.clients.create');
        Route::get('{id}', [ClientsController::class, 'show'])->name('admin.clients.show');
    });

    Route::prefix('admin/tickets')->name('admin.tickets.')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('index');
        Route::get('{ticket}', [TicketController::class, 'show'])->name('show');
        Route::put('{ticket}', [TicketController::class, 'update'])->name('update');
    });


    Route::prefix('employees')->group(function () {

        Route::get('/', [Admin_EmployeesController::class, 'index'])->name('employees.index');
        Route::get('/create', [Admin_EmployeesController::class, 'create'])->name('employees.create');
        Route::post('/store', [Admin_EmployeesController::class, 'store'])->name('employees.store');
        Route::post('/import', [Admin_EmployeesController::class, 'import'])->name('employees.import');
        Route::get('/import/sample', [Admin_EmployeesController::class, 'importSample'])->name('employees.import.sample');
        Route::get('/edit/{employee_id}', [Admin_EmployeesController::class, 'edit'])->name('employees.edit');
        Route::put('/update/{employee_id}', [Admin_EmployeesController::class, 'update'])->name('employees.update');
        Route::delete('/delete/{employee_id}', [Admin_EmployeesController::class, 'destroy'])->name('employees.delete');
        Route::post('/status/{employee_id}', [Admin_EmployeesController::class, 'updateStatus'])->name('employees.status');

        // Employee Document Vault & Verification System
        Route::prefix('documents')->name('employees.documents.')->group(function () {
            Route::get('/', [DocumentController::class, 'index'])->name('index');
            Route::post('/store', [DocumentController::class, 'store'])->name('store');
            Route::get('/{id}', [DocumentController::class, 'show'])->name('show')->where('id', '[0-9]+');
            Route::post('/{id}/verify', [DocumentController::class, 'verifyStatus'])->name('verify')->where('id', '[0-9]+');
            Route::delete('/{id}', [DocumentController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');
            Route::get('/{id}/download', [DocumentController::class, 'download'])->name('download')->where('id', '[0-9]+');
        });

        Route::get('/{employee_id}', [Admin_EmployeesController::class, 'show'])->where('employee_id', '[0-9]+');
        Route::post('/{employee_id}/map-subordinates', [Admin_EmployeesController::class, 'mapSubordinates'])->name('employees.mapSubordinates')->where('employee_id', '[0-9]+');
        Route::post('/{employee_id}/unmap-subordinate', [Admin_EmployeesController::class, 'unmapSubordinate'])->name('employees.unmapSubordinate')->where('employee_id', '[0-9]+');

        Route::get('/get-states/{country_id}', [Admin_EmployeesController::class, 'getStates'])->name('employees.getStates');
        Route::get('/get-cities/{state_id}', [Admin_EmployeesController::class, 'getCities'])->name('employees.getCities');
    });

    Route::prefix('employee-report')->group(function () {
        Route::get('/', [EmployeeReportController::class, 'index'])->name('employee-report.index');
        Route::get('/list', [EmployeeReportController::class, 'list'])->name('employee-report.list');
        Route::get('/export', [EmployeeReportController::class, 'export'])->name('employee-report.export');
    });

    Route::get('settings/holiday', [HolidayController::class, 'index'])->name('settings.holiday');
    Route::get('settings/holiday/calendar', [HolidayController::class, 'calendar'])->name('settings.holiday.calendar');
    Route::get('settings/holiday/create', [HolidayController::class, 'create'])->name('settings.holiday.create');
    Route::match(['get', 'post'], 'settings/holiday/store', [HolidayController::class, 'store'])->name('settings.holiday.store');
    Route::delete('settings/holiday/{id}', [HolidayController::class, 'destroy'])->name('settings.holiday.destroy');

    Route::get('masters/organisation/policy', [PolicyController::class, 'index'])->name('masters.organisation.policy');
    Route::get('masters/organisation/policy/create', [PolicyController::class, 'create'])->name('masters.organisation.policy.create');
    Route::get('masters/organisation/policy/store', [PolicyController::class, 'store'])->name('masters.organisation.policy.store');

    Route::get('recruitment/jobs', [JobsController::class, 'index'])->name('recruitment.jobs');
    Route::get('recruitment/job/applied-candidate', [JobsController::class, 'appliedCandidate'])->name('recruitment.jobs.applied-candidate');
    Route::get('recruitment/jobs/create', [JobsController::class, 'create'])->name('recruitment.jobs.create');

    Route::get('employee/onboarding', [OnboardingController::class, 'index'])->name('employee.onboarding');

    Route::get('create-job-questionaire', [JafController::class, 'index'])->name('create-job-questionaire');

    Route::get('masters/payroll/salary-component', [SalaryComponentController::class, 'index'])->name('masters.payroll.salary-component');
    Route::get('masters/payroll/salary-component/create', [SalaryComponentController::class, 'create'])->name('masters.payroll.salary-component.create');
    Route::post('masters/payroll/salary-component/store', [SalaryComponentController::class, 'store'])->name('masters.payroll.salary-component.store');
    Route::get('masters/payroll/salary-component/edit/{id}', [SalaryComponentController::class, 'edit'])->name('masters.payroll.salary-component.edit');
    Route::put('masters/payroll/salary-component/update/{id}', [SalaryComponentController::class, 'update'])->name('masters.payroll.salary-component.update');
    Route::delete('masters/payroll/salary-component/delete/{id}', [SalaryComponentController::class, 'destroy'])->name('masters.payroll.salary-component.destroy');
    Route::post('masters/payroll/salary-component/toggle-status/{id}', [SalaryComponentController::class, 'toggleStatus'])->name('masters.payroll.salary-component.toggle-status');
    Route::get('masters/payroll/salary-component/get-cities/{state_id}', [SalaryComponentController::class, 'getCities'])->name('masters.payroll.salary-component.get-cities');

    Route::get('masters/payroll/salary-structure', [SalaryStructureController::class, 'index'])->name('masters.payroll.salary-structure');
    Route::get('masters/payroll/salary-structure/create', [SalaryStructureController::class, 'create'])->name('masters.payroll.salary-structure.create');
    Route::post('masters/payroll/salary-structure/store', [SalaryStructureController::class, 'store'])->name('masters.payroll.salary-structure.store');
    Route::get('masters/payroll/salary-structure/designation-profile/{id}', [SalaryStructureController::class, 'getDesignationSalaryProfile'])->name('masters.payroll.salary-structure.designation-profile');
    Route::post('masters/payroll/salary-structure/save-designation-salary', [SalaryStructureController::class, 'saveDesignationSalary'])->name('masters.payroll.salary-structure.save-designation-salary');
    Route::post('masters/payroll/salary-structure/import', [SalaryStructureController::class, 'import'])->name('masters.payroll.salary-structure.import');
    Route::get('masters/payroll/salary-structure/import/sample', [SalaryStructureController::class, 'importSample'])->name('masters.payroll.salary-structure.import.sample');
    Route::get('masters/payroll/salary-structure/edit/{id}', [SalaryStructureController::class, 'edit'])->name('masters.payroll.salary-structure.edit');
    Route::put('masters/payroll/salary-structure/update/{id}', [SalaryStructureController::class, 'update'])->name('masters.payroll.salary-structure.update');
    Route::delete('masters/payroll/salary-structure/delete/{id}', [SalaryStructureController::class, 'destroy'])->name('masters.payroll.salary-structure.destroy');


    Route::get('masters/payroll/calculation-rules', [CalculationRulesController::class, 'index'])->name('masters.payroll.calculation-rules');
    Route::get('masters/payroll/calculation-rules/create', [CalculationRulesController::class, 'create'])->name('masters.payroll.calculation-rules.create');
    Route::post('masters/payroll/calculation-rules/store', [CalculationRulesController::class, 'store'])->name('masters.payroll.calculation-rules.store');
    Route::get('masters/payroll/calculation-rules/edit/{id}', [CalculationRulesController::class, 'edit'])->name('masters.payroll.calculation-rules.edit');
    Route::put('masters/payroll/calculation-rules/update/{id}', [CalculationRulesController::class, 'update'])->name('masters.payroll.calculation-rules.update');
    Route::delete('masters/payroll/calculation-rules/delete/{id}', [CalculationRulesController::class, 'destroy'])->name('masters.payroll.calculation-rules.destroy');
    Route::post('masters/payroll/calculation-rules/set-default/{id}', [CalculationRulesController::class, 'setDefault'])->name('masters.payroll.calculation-rules.set-default');

    Route::get('masters/payroll/taxation', [TaxationController::class, 'index'])->name('masters.payroll.taxation');
    Route::get('masters/payroll/taxation/create', [TaxationController::class, 'create'])->name('masters.payroll.taxation.create');
    Route::post('masters/payroll/taxation/store', [TaxationController::class, 'store'])->name('masters.payroll.taxation.store');
    Route::get('masters/payroll/taxation/edit/{id}', [TaxationController::class, 'edit'])->name('masters.payroll.taxation.edit');
    Route::put('masters/payroll/taxation/update/{id}', [TaxationController::class, 'update'])->name('masters.payroll.taxation.update');
    Route::delete('masters/payroll/taxation/delete/{id}', [TaxationController::class, 'destroy'])->name('masters.payroll.taxation.destroy');
    Route::post('masters/payroll/taxation/toggle-status/{id}', [TaxationController::class, 'toggleStatus'])->name('masters.payroll.taxation.toggle-status');

    Route::get('masters/payroll/applicability', [ApplicabilityController::class, 'index'])->name('masters.payroll.applicability');
    Route::get('masters/payroll/applicability/create', [ApplicabilityController::class, 'create'])->name('masters.payroll.applicability.create');
    Route::post('masters/payroll/applicability/store', [ApplicabilityController::class, 'store'])->name('masters.payroll.applicability.store');
    Route::get('masters/payroll/applicability/edit/{id}', [ApplicabilityController::class, 'edit'])->name('masters.payroll.applicability.edit');
    Route::put('masters/payroll/applicability/update/{id}', [ApplicabilityController::class, 'update'])->name('masters.payroll.applicability.update');
    Route::delete('masters/payroll/applicability/delete/{id}', [ApplicabilityController::class, 'destroy'])->name('masters.payroll.applicability.destroy');
    Route::post('masters/payroll/applicability/toggle-status/{id}', [ApplicabilityController::class, 'toggleStatus'])->name('masters.payroll.applicability.toggle-status');

    Route::get('masters/payroll/frequency', [PaymentFrequencyController::class, 'index'])->name('masters.payroll.payment-frequency');
    Route::get('masters/payroll/frequency/create', [PaymentFrequencyController::class, 'create'])->name('masters.payroll.payment-frequency.create');
    Route::post('masters/payroll/frequency/store', [PaymentFrequencyController::class, 'store'])->name('masters.payroll.payment-frequency.store');
    Route::get('masters/payroll/frequency/edit/{id}', [PaymentFrequencyController::class, 'edit'])->name('masters.payroll.payment-frequency.edit');
    Route::put('masters/payroll/frequency/update/{id}', [PaymentFrequencyController::class, 'update'])->name('masters.payroll.payment-frequency.update');
    Route::delete('masters/payroll/frequency/delete/{id}', [PaymentFrequencyController::class, 'destroy'])->name('masters.payroll.payment-frequency.destroy');
    Route::post('masters/payroll/frequency/toggle-status/{id}', [PaymentFrequencyController::class, 'toggleStatus'])->name('masters.payroll.payment-frequency.toggle-status');
    Route::post('masters/payroll/frequency/set-default/{id}', [PaymentFrequencyController::class, 'setDefault'])->name('masters.payroll.payment-frequency.set-default');

    Route::get('masters/payroll/payment-mode', [PaymentModeController::class, 'index'])->name('masters.payroll.payment-mode');
    Route::get('masters/payroll/payment-mode/create', [PaymentModeController::class, 'create'])->name('masters.payroll.payment-mode.create');
    Route::post('masters/payroll/payment-mode/store', [PaymentModeController::class, 'store'])->name('masters.payroll.payment-mode.store');
    Route::get('masters/payroll/payment-mode/edit/{id}', [PaymentModeController::class, 'edit'])->name('masters.payroll.payment-mode.edit');
    Route::put('masters/payroll/payment-mode/update/{id}', [PaymentModeController::class, 'update'])->name('masters.payroll.payment-mode.update');
    Route::delete('masters/payroll/payment-mode/delete/{id}', [PaymentModeController::class, 'destroy'])->name('masters.payroll.payment-mode.destroy');
    Route::post('masters/payroll/payment-mode/toggle-status/{id}', [PaymentModeController::class, 'toggleStatus'])->name('masters.payroll.payment-mode.toggle-status');
    Route::post('masters/payroll/payment-mode/set-default/{id}', [PaymentModeController::class, 'setDefault'])->name('masters.payroll.payment-mode.set-default');

    // Admin Payroll Management
    Route::prefix('payroll')->name('payroll.')->group(function () {
        Route::get('process', [\App\Http\Controllers\Admin\AdminPayrollController::class, 'processIndex'])->name('process');
        Route::post('generate', [\App\Http\Controllers\Admin\AdminPayrollController::class, 'generateBatch'])->name('generate');
        Route::get('batches', [\App\Http\Controllers\Admin\AdminPayrollController::class, 'batches'])->name('batches');
        Route::get('batches/{id}', [\App\Http\Controllers\Admin\AdminPayrollController::class, 'batchDetails'])->name('batches.show');
        Route::post('batches/{id}/approve', [\App\Http\Controllers\Admin\AdminPayrollController::class, 'approveBatch'])->name('batches.approve');
        Route::post('batches/{id}/disburse-selected', [\App\Http\Controllers\Admin\AdminPayrollController::class, 'disburseSelected'])->name('batches.disburse-selected');
        Route::post('batches/{id}/mark-pending', [\App\Http\Controllers\Admin\AdminPayrollController::class, 'markPending'])->name('batches.mark-pending');
        Route::get('attendance-details/{employee_id}/{month}/{year}', [\App\Http\Controllers\Admin\AdminPayrollController::class, 'attendanceRegisterDetails'])->name('attendance-details');
        Route::get('payslips', [\App\Http\Controllers\Admin\AdminPayrollController::class, 'payslips'])->name('payslips');
        Route::get('payslips/{id}/download', [\App\Http\Controllers\Admin\AdminPayrollController::class, 'downloadPdf'])->name('payslips.download');

        // Annual Increments & Salary Revisions
        Route::get('increments', [\App\Http\Controllers\Payroll\EmployeeIncrementController::class, 'index'])->name('increments.index');
        Route::get('increments/create', [\App\Http\Controllers\Payroll\EmployeeIncrementController::class, 'create'])->name('increments.create');
        Route::post('increments/preview', [\App\Http\Controllers\Payroll\EmployeeIncrementController::class, 'preview'])->name('increments.preview');
        Route::post('increments/store', [\App\Http\Controllers\Payroll\EmployeeIncrementController::class, 'store'])->name('increments.store');
        Route::get('increments/bulk', [\App\Http\Controllers\Payroll\EmployeeIncrementController::class, 'bulkForm'])->name('increments.bulk');
        Route::post('increments/bulk', [\App\Http\Controllers\Payroll\EmployeeIncrementController::class, 'bulkStore'])->name('increments.bulk.store');
        Route::get('increments/{id}', [\App\Http\Controllers\Payroll\EmployeeIncrementController::class, 'show'])->name('increments.show');
        Route::get('increments/{id}/letter', [\App\Http\Controllers\Payroll\EmployeeIncrementController::class, 'letter'])->name('increments.letter');
        Route::get('increments/{id}/download-letter', [\App\Http\Controllers\Payroll\EmployeeIncrementController::class, 'downloadLetter'])->name('increments.download-letter');
        Route::get('increments/history/{employee_id}', [\App\Http\Controllers\Payroll\EmployeeIncrementController::class, 'history'])->name('increments.history');
    });


    Route::get('settings/leave-type', [LeaveTypeController::class, 'index'])->name('settings.leave-type');

    Route::get('settings/leave-type/create', [LeaveTypeController::class, 'create'])->name('settings.leave-type.create');
    Route::post('settings/leave-type/store', [LeaveTypeController::class, 'store'])->name('settings.leave-type.store');
    Route::get('settings/leave-type/edit/{id}', [LeaveTypeController::class, 'edit'])->name('settings.leave-type.edit');
    Route::put('settings/leave-type/edit/{id}', [LeaveTypeController::class, 'update'])->name('settings.leave-type.update');
    Route::delete('settings/leave-type/delete/{id}', [LeaveTypeController::class, 'destroy'])->name('settings.leave-type.destroy');
    Route::get('settings/leave-type/list', [LeaveTypeController::class, 'list'])->name('settings.leave-type.list');

    Route::get('settings/leave-allow', [LeaveMappingController::class, 'index'])->name('settings.leave-allow');
    Route::get('settings/leave-allow/create', [LeaveMappingController::class, 'create'])->name('settings.leave-allow.create');
    Route::post('settings/leave-mapping/store', [LeaveMappingController::class, 'store'])->name('settings.leave.mapping.store');
    Route::get('settings/leave-mapping/list', [LeaveMappingController::class, 'list'])->name('settings.leave.mapping.list');
    Route::delete('settings/leave-mapping/delete/{id}', [LeaveMappingController::class, 'destroy'])->name('settings.leave.mapping.destroy');

    Route::get('masters/payroll/visibility', [VisibilityControlController::class, 'index'])->name('masters.payroll.visibility');
    Route::get('masters/payroll/visibility/create', [VisibilityControlController::class, 'create'])->name('masters.payroll.visibility.create');
    Route::post('masters/payroll/visibility/store', [VisibilityControlController::class, 'store'])->name('masters.payroll.visibility.store');
    Route::get('masters/payroll/visibility/edit/{id}', [VisibilityControlController::class, 'edit'])->name('masters.payroll.visibility.edit');
    Route::put('masters/payroll/visibility/update/{id}', [VisibilityControlController::class, 'update'])->name('masters.payroll.visibility.update');
    Route::delete('masters/payroll/visibility/delete/{id}', [VisibilityControlController::class, 'destroy'])->name('masters.payroll.visibility.destroy');
    Route::post('masters/payroll/visibility/toggle-status/{id}', [VisibilityControlController::class, 'toggleStatus'])->name('masters.payroll.visibility.toggle-status');

    Route::get('attendance/attendance-dashboard/summary', [AttendanceDashboardController::class, 'index'])->name('attendance.attendance-dashboard.summary');
    Route::get('attendance/attendance-dashboard/summary/create', [AttendanceDashboardController::class, 'create'])->name('attendance.attendance-dashboard.summary.create');
    Route::get('attendance/attendance-dashboard/summary/store', [AttendanceDashboardController::class, 'store'])->name('attendance.attendance-dashboard.summary.store');

    Route::get('attendance/attendance-dashboard/org-reports', [AttendanceOrgReportController::class, 'index'])->name('attendance.attendance-dashboard.org-reports');
    Route::get('attendance/attendance-dashboard/org-reports/list', [AttendanceOrgReportController::class, 'list'])->name('attendance.attendance-dashboard.org-reports.list');
    Route::get('attendance/attendance-dashboard/org-reports/employee/{employeeId}', [AttendanceOrgReportController::class, 'employeeDetails'])->name('attendance.attendance-dashboard.org-reports.employee');
    Route::get('attendance/attendance-dashboard/org-reports/employee-monthly/{employeeId}', [AttendanceOrgReportController::class, 'employeeMonthlyReport'])->name('attendance.attendance-dashboard.org-reports.employee-monthly');
    Route::get('attendance/attendance-dashboard/org-reports/create', [AttendanceOrgReportController::class, 'create'])->name('attendance.attendance-dashboard.org-reports.create');
    Route::get('attendance/attendance-dashboard/org-reports/store', [AttendanceOrgReportController::class, 'store'])->name('attendance.attendance-dashboard.org-reports.store');
    Route::get('attendance/daily-attendance/consistent-attendees', [ConsistentAttendeesController::class, 'index'])->name('attendance.daily-attendance.consistent-attendees');

    Route::prefix('attendance/attendance-register')->name('attendance.register.')->group(function () {
        Route::get('/', [AttendanceRegisterReportController::class, 'index'])->name('index');
        Route::get('/export/excel', [AttendanceRegisterReportController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [AttendanceRegisterReportController::class, 'exportPdf'])->name('export.pdf');
    });

    Route::middleware('admin.access')->group(function () {
        Route::get('permissions', [PermissionController::class, 'index'])->name('permission');
        Route::get('permissions/create', [PermissionController::class, 'create'])->name('permission.create');
        Route::post('permissions/store', [PermissionController::class, 'store'])->name('permission.store');
        Route::get('permissions/list', [PermissionController::class, 'list'])->name('permission.list');
        Route::get('permissions/edit/{id}', [PermissionController::class, 'edit'])->name('permissions.edit');
        Route::put('permissions/update/{id}', [PermissionController::class, 'update'])->name('permissions.update');
        Route::delete('permissions/destroy/{id}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

        Route::get('roles', [RoleController::class, 'index'])->name('role');
        Route::get('roles/create', [RoleController::class, 'create'])->name('role.create');
        Route::post('roles/store', [RoleController::class, 'store'])->name('role.store');
        Route::get('roles/list', [RoleController::class, 'list'])->name('role.list');
        Route::get('roles/edit/{id}', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('roles/update/{id}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('roles/destroy/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');

        Route::prefix('admin/users')->name('admin.users.')->middleware('superadmin.access')->group(function () {
            Route::get('/', [AdminUserController::class, 'index'])->name('index');
            Route::get('create', [AdminUserController::class, 'create'])->name('create');
            Route::post('/', [AdminUserController::class, 'store'])->name('store');
            Route::get('{user}/edit', [AdminUserController::class, 'edit'])->name('edit');
            Route::put('{user}', [AdminUserController::class, 'update'])->name('update');
            Route::delete('{user}', [AdminUserController::class, 'destroy'])->name('destroy');
        });

        Route::get('map-role-permission', fn() => redirect()->route('role'))->name('map-role-permission');
    });

    Route::get('settings/email-template', [SettingController::class, 'index'])->name('settings.email-templates');
    Route::get('settings/email-template/create', [SettingController::class, 'create'])->name('email-templates.create');
    Route::post('settings/email-template/store', [SettingController::class, 'store'])->name('settings.email-template.store');
    Route::get('settings/email-template/list', [SettingController::class, 'list'])->name('settings.email-template.list');
    Route::get('settings/email-template/preview/{id}', [SettingController::class, 'preview'])->name('settings.email-template.preview');

    Route::get('attendance/leave/request', [AttendanceController::class, 'index'])->name('attendance.leave.request');
    Route::get('attendance/leave/list', [AttendanceController::class, 'list'])->name('leaves.list');
    Route::post('attendance/leave/updateStatus', [AttendanceController::class, 'updateStatus'])->name('leave.updateStatus');

    Route::prefix('invoice')->name('invoice.')->group(function () {
        Route::get('/', [InvoiceController::class, 'dashboard'])->name('dashboard');
        Route::get('clients', [InvoiceController::class, 'clients'])->name('clients');
        Route::get('clients/list', [InvoiceController::class, 'clientsList'])->name('clients.list');
        Route::get('clients/create', [InvoiceController::class, 'createClient'])->name('clients.create');
        Route::post('clients/fetch-states', [InvoiceController::class, 'fetchClientStates'])->name('clients.fetch-states');
        Route::post('clients', [InvoiceController::class, 'storeClient'])->name('clients.store');
        Route::get('clients/{id}/edit', [InvoiceController::class, 'editClient'])->name('clients.edit');
        Route::put('clients/{id}', [InvoiceController::class, 'updateClient'])->name('clients.update');
        Route::get('companies', [InvoiceController::class, 'companies'])->name('companies');
        Route::get('companies/list', [InvoiceController::class, 'companiesList'])->name('companies.list');
        Route::get('companies/create', [InvoiceController::class, 'createCompany'])->name('companies.create');
        Route::post('companies', [InvoiceController::class, 'storeCompany'])->name('companies.store');
        Route::get('companies/{id}/edit', [InvoiceController::class, 'editCompany'])->name('companies.edit');
        Route::put('companies/{id}', [InvoiceController::class, 'updateCompany'])->name('companies.update');
        Route::get('projects', [InvoiceController::class, 'projects'])->name('projects');
        Route::get('projects/list', [InvoiceController::class, 'projectsList'])->name('projects.list');
        Route::get('projects/create', [InvoiceController::class, 'createProject'])->name('projects.create');
        Route::get('projects/mapping', [InvoiceController::class, 'projectMapping'])->name('projects.mapping');
        Route::post('projects', [InvoiceController::class, 'storeProject'])->name('projects.store');
        Route::get('projects/{id}/edit', [InvoiceController::class, 'editProject'])->name('projects.edit');
        Route::put('projects/{id}', [InvoiceController::class, 'updateProject'])->name('projects.update');
        Route::get('projects/{id}/employees', [InvoiceController::class, 'mapProjectEmployees'])->name('projects.employees');
        Route::post('projects/{id}/employees', [InvoiceController::class, 'storeProjectEmployees'])->name('projects.employees.store');
        Route::get('projects/{id}', [InvoiceController::class, 'showProject'])->name('projects.show');
        Route::post('projects/{id}/concern-persons', [InvoiceController::class, 'storeProjectConcernPerson'])->name('projects.concern-persons.store');
        Route::post('projects/{id}/concern-persons/{personId}/send-credentials', [InvoiceController::class, 'sendConcernPersonCredentials'])->name('projects.concern-persons.send-credentials');
        Route::delete('projects/{id}/concern-persons/{personId}', [InvoiceController::class, 'deleteProjectConcernPerson'])->name('projects.concern-persons.destroy');
        Route::get('invoices', [InvoiceController::class, 'invoices'])->name('invoices');
        Route::get('invoices/list', [InvoiceController::class, 'invoicesList'])->name('invoices.list');
        Route::get('invoices/create', [InvoiceController::class, 'createInvoice'])->name('invoices.create');
        Route::post('invoices/invoice-type', [InvoiceController::class, 'invoiceType'])->name('invoices.invoice-type');
        Route::post('invoices/check-invoice', [InvoiceController::class, 'checkInvoiceNumber'])->name('invoices.check-invoice');
        Route::post('invoices/client-details', [InvoiceController::class, 'invoiceClientDetails'])->name('invoices.client-details');
        Route::post('invoices', [InvoiceController::class, 'storeInvoice'])->name('invoices.store');
        Route::get('invoices/{id}/edit', [InvoiceController::class, 'editInvoice'])->name('invoices.edit');
        Route::put('invoices/{id}', [InvoiceController::class, 'updateInvoice'])->name('invoices.update');
        Route::post('invoices/{id}/mark-sent', [InvoiceController::class, 'markInvoiceSent'])->name('invoices.mark-sent');
        Route::get('invoices/{id}/send', [InvoiceController::class, 'sendInvoiceForm'])->name('invoices.send');
        Route::post('invoices/{id}/send', [InvoiceController::class, 'sendInvoice'])->name('invoices.send.store');
        Route::get('invoices/{id}', [InvoiceController::class, 'showInvoice'])->name('invoices.show');
        Route::get('payments', [InvoiceController::class, 'payments'])->name('payments');
        Route::get('payments/list', [InvoiceController::class, 'paymentsList'])->name('payments.list');
        Route::get('payments/create', [InvoiceController::class, 'createPayment'])->name('payments.create');
        Route::post('payments', [InvoiceController::class, 'storePayment'])->name('payments.store');
        Route::post('payments/cancel-invoice', [InvoiceController::class, 'cancelInvoice'])->name('payments.cancel-invoice');
        Route::get('payments/{id}/edit', [InvoiceController::class, 'editPayment'])->name('payments.edit');
        Route::put('payments/{id}', [InvoiceController::class, 'updatePayment'])->name('payments.update');
        Route::get('ledger', [InvoiceController::class, 'ledger'])->name('ledger');
        Route::get('expenses', [InvoiceController::class, 'expenses'])->name('expenses');
        Route::get('expenses/list', [InvoiceController::class, 'expensesList'])->name('expenses.list');
        Route::get('expenses/create', [InvoiceController::class, 'createExpense'])->name('expenses.create');
        Route::post('expenses', [InvoiceController::class, 'storeExpense'])->name('expenses.store');
        Route::get('expenses/{id}/edit', [InvoiceController::class, 'editExpense'])->name('expenses.edit');
        Route::put('expenses/{id}', [InvoiceController::class, 'updateExpense'])->name('expenses.update');
    });

    // Push Notifications & FCM Web
    Route::post('/admin/fcm-token', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'storeWebFcmToken'])->name('admin.fcm.token');
    Route::get('/admin/notifications', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('/admin/notifications/send', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'send'])->name('admin.notifications.send');

    // Employee Resignations & Exit Reporting
    Route::prefix('admin/resignation')->name('admin.resignation.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminResignationController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\AdminResignationController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Admin\AdminResignationController::class, 'store'])->name('store');
        Route::get('/export', [\App\Http\Controllers\Admin\AdminResignationController::class, 'export'])->name('export');
        Route::get('/{id}', [\App\Http\Controllers\Admin\AdminResignationController::class, 'show'])->name('show');
        Route::post('/{id}/respond', [\App\Http\Controllers\Admin\AdminResignationController::class, 'respond'])->name('respond');
        Route::post('/{id}/complete-exit', [\App\Http\Controllers\Admin\AdminResignationController::class, 'completeExit'])->name('complete-exit');
    });
});


Route::get('/', [AuthController::class, 'index']);
Route::match(['get', 'post'], '/login', [AuthController::class, 'login'])->name('login');
Route::post('/login/submit', [AuthController::class, 'login'])->name('login.submit');

Route::get('/privacy-policy', [LegalController::class, 'privacyPolicy'])->name('privacy.policy');
Route::get('/terms-and-conditions', [LegalController::class, 'termsConditions'])->name('terms.conditions');

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/client/login', [ClientAuthController::class, 'index'])->name('client.login');
Route::post('/client/login', [ClientAuthController::class, 'login'])->name('client.login.submit');
Route::get('/client/logout', [ClientAuthController::class, 'logout'])->name('client.logout');

Route::prefix('client')->name('client.')->middleware('auth:client')->group(function () {
    Route::get('dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
    Route::get('tickets', [ClientTicketController::class, 'index'])->name('tickets.index');
    Route::get('tickets/create', [ClientTicketController::class, 'create'])->name('tickets.create');
    Route::post('tickets', [ClientTicketController::class, 'store'])->name('tickets.store');
    Route::get('tickets/{ticket}', [ClientTicketController::class, 'show'])->name('tickets.show');
});

Route::any('/neuralinvoice/{path?}', LegacyNeuralInvoiceController::class)
    ->where('path', '.*')
    ->name('legacy.neuralinvoice');

Route::get('/employee/login', [EmployeeAuthController::class, 'index'])->name('employee.login');
Route::post('/employee/login', [EmployeeAuthController::class, 'store'])->name('employee.login.submit');
Route::get('/employee/logout', [EmployeeAuthController::class, 'logout'])->name('employee.logout');

Route::prefix('employee')->middleware(['auth:employee', 'employee.active.web'])->group(function () {
    Route::prefix('self-appraisal')->group(function () {

        Route::get('/', [SelfAppraisalController::class, 'index'])->name('employee.self.appraisal');
        Route::get('/competencies', [SelfAppraisalController::class, 'competencies'])->name('employee.competencies');
        Route::get('/kpi-assessment', [SelfAppraisalController::class, 'kpiAssessment'])->name('employee.kpi.assessment');
        Route::get('/form-c', [SelfAppraisalController::class, 'formC'])->name('employee.form-c');
    });

    Route::prefix('review-employee')->group(function () {
        Route::get('/', [ReviewEmployeeController::class, 'index'])->name('employee.review.employee');
    });


    Route::get('/feedback', [FeedbackController::class, 'index'])->name('employee.feedback');
    Route::get('/view-feedback', [ViewFeedbackController::class, 'index'])->name('employee.view-feedback');

    // Dashboard
    Route::get('dashboard', [EmployeeController::class, 'index'])->name('employee.dashboard');
    Route::get('dashboard/data', [EmployeeController::class, 'dashboardData'])->name('employee.dashboard.data');

    Route::prefix('tickets')->name('employee.tickets.')->group(function () {
        Route::get('/', [EmployeeTicketController::class, 'index'])->name('index');
        Route::get('{ticket}', [EmployeeTicketController::class, 'show'])->name('show');
        Route::put('{ticket}', [EmployeeTicketController::class, 'update'])->name('update');
    });

    Route::get('leaves', [LeavesController::class, 'index'])->name('employee.leaves');
    Route::get('leaves/apply', [LeavesController::class, 'create'])->name('employee.leaves.apply');
    Route::post('leaves/apply/store', [LeavesController::class, 'store'])->name('employee.leaves.store');
    Route::get('leaves/list', [LeavesController::class, 'list'])->name('employee.leaves.list');
    Route::get('leave/balance/{leaveTypeId}', [LeavesController::class, 'getLeaveBalance'])->name('leave.balance');


    Route::get('employee/daily-reports/export', [DailyReportController::class, 'export'])->name('employee.daily.reports.export');

    Route::get('daily-reports', [DailyReportController::class, 'index'])->name('employee.daily.reports');
    Route::post('daily-report', [DailyReportController::class, 'store'])->name('daily-report.store');
    Route::get('daily-reports/view', [DailyReportController::class, 'list'])->name('employee.daily.reports.view');
    Route::get('daily-reports/view/project-wise', [DailyReportController::class, 'project'])->name('employee.daily.reports.project');
    Route::get('employee/daily-reports/project/ajax', [DailyReportController::class, 'projectAjax'])->name('employee.daily.reports.project.ajax');
    Route::get('employee/daily-reports/ajax', [DailyReportController::class, 'ajaxList'])->name('employee.daily.reports.ajax');



    Route::get('attendance', [EmployeeAttendanceController::class, 'index'])->name('employee.attendance');
    Route::post('attendance/punch', [EmployeeAttendanceController::class, 'punch'])->name('employee.attendance.punch');
    Route::get('holidays', [AttendanceController::class, 'holidayList'])->name('employee.holidays');

    Route::get('performance', [PerformanceController::class, 'index'])->name('employee.performance');
    Route::get('performance/review', [PerformanceReviewController::class, 'index'])->name('employee.performance.review');

    // Annual Increments & Appraisals
    Route::get('increments', [\App\Http\Controllers\Employee\EmployeeAppraisalIncrementController::class, 'index'])->name('employee.increments');
    Route::get('increments/{id}/download-letter', [\App\Http\Controllers\Employee\EmployeeAppraisalIncrementController::class, 'downloadLetter'])->name('employee.increments.download-letter');

    // Resignation & Exit Management
    Route::prefix('resignation')->name('employee.resignation.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Employee\EmployeeResignationController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Employee\EmployeeResignationController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Employee\EmployeeResignationController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Employee\EmployeeResignationController::class, 'show'])->name('show');
        Route::post('/{id}/withdraw', [\App\Http\Controllers\Employee\EmployeeResignationController::class, 'withdraw'])->name('withdraw');
    });
});

// Resignation Direct Email 1-Click Action Link
Route::match(['get', 'post'], 'resignation/action/{token}/{decision?}', [\App\Http\Controllers\Admin\AdminResignationController::class, 'emailAction'])->name('resignation.email-action');

// Dedicated API Documentation Routes
use App\Http\Controllers\ApiDocsController;
use App\Http\Controllers\ProjectWorkflowController;

Route::get('api-documentation', [ApiDocsController::class, 'index'])->name('api.docs');
Route::get('developer/api-docs', fn() => redirect()->route('api.docs'));
Route::get('api-docs', fn() => redirect()->route('api.docs'));
Route::get('api-documentation/postman.json', [ApiDocsController::class, 'postman'])->name('api.docs.postman');

// Dedicated Project Operational Flow & Interactive Guide Routes
Route::get('project-workflow', [ProjectWorkflowController::class, 'index'])->name('project.workflow');
Route::get('project-docs', fn() => redirect()->route('project.workflow'));
Route::get('workflow', fn() => redirect()->route('project.workflow'));
Route::get('flow', fn() => redirect()->route('project.workflow'));
Route::get('project-workflow/download-docx', [ProjectWorkflowController::class, 'downloadDocx'])->name('project.workflow.download');




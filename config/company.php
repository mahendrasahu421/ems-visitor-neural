<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Company Profile
    |--------------------------------------------------------------------------
    |
    | Keep "full" for the existing product. Use "limited_company" for a
    | separate deployment where only employee, leave, attendance report, and
    | attendance API features should be available.
    |
    */
    'profile' => env('APP_PROFILE', 'full'),

    'limited_route_patterns' => [
        'home',
        'dashboard',
        'logout',
        'login',
        'getState',
        'getCity',
        'districts.search',
        'skills.search',

        'employees.*',
        'employee-report.*',

        'attendance.leave.request',
        'leaves.list',
        'leave.updateStatus',
        'attendance.attendance-dashboard.org-reports',
        'attendance.attendance-dashboard.org-reports.list',
        'attendance.attendance-dashboard.org-reports.employee',

        'settings.leave-type*',
        'settings.leave-allow',
        'settings.leave.mapping.*',
        'admin.users.*',
        'role',
        'role.*',
        'roles.*',
        'permission',
        'permission.*',
        'permissions.*',
        'map-role-permission',
        'map-role-permission.*',
        'leave.balance',
        'employee.login',
        'employee.login.submit',
        'employee.logout',
        'employee.dashboard',
        'employee.dashboard.data',
        'employee.leaves*',
        'employee.attendance',
        'employee.holidays',
        'employee.updateProfile',
        'employee.getStates',
        'employee.getCities',
    ],

    'limited_path_patterns' => [
        '/',
        'home',
        'dashboard',
        'login',
        'logout',
        'districts/search',
        'skills/search',
        'get-*',
        'leave-type/get-days/*',
        'employees*',
        'employee-report*',
        'attendance/leave*',
        'attendance/attendance-dashboard/org-reports*',
        'settings/leave-type*',
        'settings/leave-allow*',
        'settings/leave-mapping*',
        'admin/users*',
        'roles*',
        'permissions*',
        'map-role-permission*',
        'employee/login',
        'employee/logout',
        'employee/dashboard*',
        'employee/leaves*',
        'employee/leave/balance*',
        'employee/attendance*',
        'employee/holidays*',
        'employee/update-profile',
        'employee/get-states',
        'employee/get-cities',
    ],

    'limited_menu_routes' => [
        'home',
        'dashboard',
        'employees.index',
        'employees.create',
        'employee-report.index',
        'attendance.leave.request',
        'attendance.attendance-dashboard.org-reports',
        'settings.leave-type',
        'settings.leave-allow',
    ],
];

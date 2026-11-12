<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ApiDocsController extends Controller
{
    /**
     * Render the developer-friendly API documentation page.
     */
    public function index()
    {
        $baseUrl = url('/api');
        
        return view('docs.api', [
            'title' => 'API Documentation | EMS Portal',
            'baseUrl' => $baseUrl,
        ]);
    }

    /**
     * Download or view the Postman Collection v2.1 JSON.
     */
    public function postman()
    {
        $baseUrl = url('/api');

        $collection = [
            'info' => [
                '_postman_id' => 'ems-api-' . md5($baseUrl),
                'name' => 'EMS Visitor & Employee Portal API',
                'description' => 'Complete REST API collection for EMS Employee Portal including Authentication, Attendance, Calendar, Leave Management, and Holidays.',
                'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
            ],
            'variable' => [
                [
                    'key' => 'baseUrl',
                    'value' => $baseUrl,
                    'type' => 'string',
                ],
                [
                    'key' => 'token',
                    'value' => '',
                    'type' => 'string',
                ],
            ],
            'auth' => [
                'type' => 'bearer',
                'bearer' => [
                    [
                        'key' => 'token',
                        'value' => '{{token}}',
                        'type' => 'string',
                    ],
                ],
            ],
            'item' => [
                [
                    'name' => '1. Authentication & Security (PIN)',
                    'item' => [
                        [
                            'name' => 'Universal Login (Password or PIN)',
                            'request' => [
                                'auth' => ['type' => 'noauth'],
                                'method' => 'POST',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                    ['key' => 'Content-Type', 'value' => 'application/json'],
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => json_encode([
                                        'login' => '7668036640',
                                        'password' => 'Password@123',
                                        'pin' => '1234',
                                        'device_name' => 'Postman Client',
                                    ], JSON_PRETTY_PRINT),
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/auth/login',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'auth', 'login'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Login with Mobile Number',
                            'request' => [
                                'auth' => ['type' => 'noauth'],
                                'method' => 'POST',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                    ['key' => 'Content-Type', 'value' => 'application/json'],
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => json_encode([
                                        'mobile' => '7668036640',
                                        'password' => 'Password@123',
                                        'pin' => '',
                                        'device_name' => 'Mobile App',
                                    ], JSON_PRETTY_PRINT),
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/auth/login/mobile',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'auth', 'login', 'mobile'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Login with Employee ID',
                            'request' => [
                                'auth' => ['type' => 'noauth'],
                                'method' => 'POST',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                    ['key' => 'Content-Type', 'value' => 'application/json'],
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => json_encode([
                                        'employee_id' => '99',
                                        'password' => 'Password@123',
                                        'pin' => '',
                                        'device_name' => 'Mobile App',
                                    ], JSON_PRETTY_PRINT),
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/auth/login/empid',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'auth', 'login', 'empid'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Quick PIN Login',
                            'request' => [
                                'auth' => ['type' => 'noauth'],
                                'method' => 'POST',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                    ['key' => 'Content-Type', 'value' => 'application/json'],
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => json_encode([
                                        'identifier' => '7668036640',
                                        'pin' => '1234',
                                        'device_name' => 'Mobile App',
                                    ], JSON_PRETTY_PRINT),
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/auth/login/pin',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'auth', 'login', 'pin'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Setup Security PIN',
                            'request' => [
                                'method' => 'POST',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                    ['key' => 'Content-Type', 'value' => 'application/json'],
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => json_encode([
                                        'pin' => '1234',
                                        'pin_confirmation' => '1234',
                                        'password' => 'Password@123',
                                    ], JSON_PRETTY_PRINT),
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/auth/pin/setup',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'auth', 'pin', 'setup'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Change PIN',
                            'request' => [
                                'method' => 'POST',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                    ['key' => 'Content-Type', 'value' => 'application/json'],
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => json_encode([
                                        'current_pin' => '1234',
                                        'new_pin' => '5678',
                                        'new_pin_confirmation' => '5678',
                                    ], JSON_PRETTY_PRINT),
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/auth/pin/change',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'auth', 'pin', 'change'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Forgot PIN - Request OTP',
                            'request' => [
                                'auth' => ['type' => 'noauth'],
                                'method' => 'POST',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                    ['key' => 'Content-Type', 'value' => 'application/json'],
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => json_encode([
                                        'identifier' => '7668036640',
                                    ], JSON_PRETTY_PRINT),
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/auth/pin/forgot-request',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'auth', 'pin', 'forgot-request'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Reset PIN with OTP',
                            'request' => [
                                'auth' => ['type' => 'noauth'],
                                'method' => 'POST',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                    ['key' => 'Content-Type', 'value' => 'application/json'],
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => json_encode([
                                        'identifier' => '7668036640',
                                        'otp' => '123456',
                                        'new_pin' => '9999',
                                        'new_pin_confirmation' => '9999',
                                    ], JSON_PRETTY_PRINT),
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/auth/pin/reset',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'auth', 'pin', 'reset'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Enable or Disable PIN Login',
                            'request' => [
                                'method' => 'POST',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                    ['key' => 'Content-Type', 'value' => 'application/json'],
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => json_encode([
                                        'enable' => true,
                                        'pin' => '9999',
                                    ], JSON_PRETTY_PRINT),
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/auth/pin/toggle',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'auth', 'pin', 'toggle'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Get PIN Status',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/auth/pin/status',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'auth', 'pin', 'status'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Get Auth & Security Activity Logs',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/auth/activity-logs',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'auth', 'activity-logs'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Employee Logout',
                            'request' => [
                                'method' => 'POST',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/auth/logout',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'auth', 'logout'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Get Profile',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/profile',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'profile'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Get Agencies List',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/agencies',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'agencies'],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => '2. Attendance Management',
                    'item' => [
                        [
                            'name' => 'Mark Check-In',
                            'request' => [
                                'method' => 'POST',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                    ['key' => 'Content-Type', 'value' => 'application/json'],
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => json_encode([
                                        'latitude' => 23.259933,
                                        'longitude' => 77.412613,
                                        'note' => 'Office check-in',
                                        'device_info' => 'Android Phone',
                                    ], JSON_PRETTY_PRINT),
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/attendance/check-in',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'attendance', 'check-in'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Mark Check-Out',
                            'request' => [
                                'method' => 'POST',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                    ['key' => 'Content-Type', 'value' => 'application/json'],
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => json_encode([
                                        'latitude' => 23.259933,
                                        'longitude' => 77.412613,
                                        'note' => 'Office check-out',
                                        'device_info' => 'Android Phone',
                                    ], JSON_PRETTY_PRINT),
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/attendance/check-out',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'attendance', 'check-out'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Today Attendance Status',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/attendance/today',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'attendance', 'today'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Attendance History',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/attendance/history?start_date=2026-09-01&end_date=2026-09-21&per_page=20',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'attendance', 'history'],
                                    'query' => [
                                        ['key' => 'start_date', 'value' => '2026-09-01'],
                                        ['key' => 'end_date', 'value' => '2026-09-21'],
                                        ['key' => 'per_page', 'value' => '20'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => '3. Calendar & Schedule',
                    'item' => [
                        [
                            'name' => 'Monthly Calendar Grid',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/calendar?month=9&year=2026',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'calendar'],
                                    'query' => [
                                        ['key' => 'month', 'value' => '9'],
                                        ['key' => 'year', 'value' => '2026'],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Date Details Breakdown',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/calendar/date-details?date=2026-09-21',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'calendar', 'date-details'],
                                    'query' => [
                                        ['key' => 'date', 'value' => '2026-09-21'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => '4. Leave Management',
                    'item' => [
                        [
                            'name' => 'Get Leave Types',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/leave/types?year=2026&applicable_only=false',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'leave', 'types'],
                                    'query' => [
                                        ['key' => 'year', 'value' => '2026', 'description' => 'Optional calendar year for balance calculation'],
                                        ['key' => 'applicable_only', 'value' => 'false', 'description' => 'Optional filter: true/1 to return only gender-applicable types'],
                                    ],
                                ],
                                'description' => 'Returns active leave types with individual employee quotas, approved used days, pending days, remaining balances, gender applicability check, and annual summary.',
                            ],
                        ],
                        [
                            'name' => 'Apply for Leave',
                            'request' => [
                                'method' => 'POST',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'body' => [
                                    'mode' => 'formdata',
                                    'formdata' => [
                                        ['key' => 'leave_type_id', 'value' => '1', 'type' => 'text'],
                                        ['key' => 'from_date', 'value' => '2026-09-24', 'type' => 'text'],
                                        ['key' => 'to_date', 'value' => '2026-09-25', 'type' => 'text'],
                                        ['key' => 'reason', 'value' => 'Personal work out of station', 'type' => 'text'],
                                    ],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/leave/apply',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'leave', 'apply'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'List Leave Applications',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/leave/list?status=ALL&year=2026',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'leave', 'list'],
                                    'query' => [
                                        ['key' => 'status', 'value' => 'ALL'],
                                        ['key' => 'year', 'value' => '2026'],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Get Leave Details',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/leave/1',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'leave', '1'],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => '5. Holidays',
                    'item' => [
                        [
                            'name' => 'Get Holiday List',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/holidays?year=2026',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'holidays'],
                                    'query' => [
                                        ['key' => 'year', 'value' => '2026'],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Get Holiday Calendar (Grouped)',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/holidays/calendar?year=2026',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'holidays', 'calendar'],
                                    'query' => [
                                        ['key' => 'year', 'value' => '2026'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => '6. Supervisor Management',
                    'item' => [
                        [
                            'name' => 'Supervisor Dashboard',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/dashboard',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'dashboard'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'List Team Leaves',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/leaves?status=PENDING',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'leaves'],
                                    'query' => [
                                        ['key' => 'status', 'value' => 'PENDING'],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Get Team Leave Details',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/leaves/1',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'leaves', '1'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Approve Subordinate Leave',
                            'request' => [
                                'method' => 'POST',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                    ['key' => 'Content-Type', 'value' => 'application/json'],
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => json_encode([
                                        'remarks' => 'Approved, have a great time.',
                                    ], JSON_PRETTY_PRINT),
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/leaves/1/approve',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'leaves', '1', 'approve'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Reject Subordinate Leave',
                            'request' => [
                                'method' => 'POST',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                    ['key' => 'Content-Type', 'value' => 'application/json'],
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => json_encode([
                                        'reason' => 'Critical production release scheduled during these dates.',
                                    ], JSON_PRETTY_PRINT),
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/leaves/1/reject',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'leaves', '1', 'reject'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Team Live Attendance Roster',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/team/attendance?date=2026-09-24',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'team', 'attendance'],
                                    'query' => [
                                        ['key' => 'date', 'value' => '2026-09-24'],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Team Members Directory',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/team/members',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'team', 'members'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Subordinate Monthly Attendance Sheet',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/team/members/99/attendance?month=9&year=2026',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'team', 'members', '99', 'attendance'],
                                    'query' => [
                                        ['key' => 'month', 'value' => '9'],
                                        ['key' => 'year', 'value' => '2026'],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Subordinate Full Profile View',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/team/members/99',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'team', 'members', '99'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Dropdown Metadata for Adding Employees',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/meta/form-options',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'meta', 'form-options'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Add / Register Subordinate Employee',
                            'request' => [
                                'method' => 'POST',
                                'header' => [
                                    ['key' => 'Content-Type', 'value' => 'application/json'],
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => json_encode([
                                        'first_name' => 'Rohan',
                                        'last_name' => 'Verma',
                                        'email' => 'rohan.verma@neuralinfo.org',
                                        'mobile' => '9811223344',
                                        'password' => 'Password@123',
                                        'designation_id' => 1,
                                        'agency_id' => 1,
                                        'shift_id' => 1,
                                        'joining_date' => '2026-10-01',
                                        'employment_type' => 'Full Time',
                                        'salary' => 35000,
                                    ], JSON_PRETTY_PRINT),
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/team/members',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'team', 'members'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Update Subordinate Details',
                            'request' => [
                                'method' => 'PUT',
                                'header' => [
                                    ['key' => 'Content-Type', 'value' => 'application/json'],
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => json_encode([
                                        'first_name' => 'Rohan',
                                        'last_name' => 'Verma',
                                        'mobile' => '9811223344',
                                        'designation_id' => 1,
                                        'salary' => 38000,
                                    ], JSON_PRETTY_PRINT),
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/team/members/99',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'team', 'members', '99'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Subordinate Leave Balances Quota',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/team/members/99/leave-balances?year=2026',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'team', 'members', '99', 'leave-balances'],
                                    'query' => [
                                        ['key' => 'year', 'value' => '2026'],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Apply Leave on Behalf of Subordinate',
                            'request' => [
                                'method' => 'POST',
                                'header' => [
                                    ['key' => 'Content-Type', 'value' => 'application/json'],
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => json_encode([
                                        'employee_id' => 99,
                                        'leave_type_id' => 1,
                                        'from_date' => '2026-10-05',
                                        'to_date' => '2026-10-06',
                                        'reason' => 'Applied by supervisor per phone call',
                                        'auto_approve' => true,
                                    ], JSON_PRETTY_PRINT),
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/leaves/apply-for-member',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'leaves', 'apply-for-member'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Team Monthly Turnout & Analytics Summary',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/team/monthly-summary?month=9&year=2026',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'team', 'monthly-summary'],
                                    'query' => [
                                        ['key' => 'month', 'value' => '9'],
                                        ['key' => 'year', 'value' => '2026'],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Supervisor Reports Hub Summary',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/reports/summary',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'reports', 'summary'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Team Attendance Punch Logs Report',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/reports/attendance?start_date=2026-09-01&end_date=2026-09-30',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'reports', 'attendance'],
                                    'query' => [
                                        ['key' => 'start_date', 'value' => '2026-09-01'],
                                        ['key' => 'end_date', 'value' => '2026-09-30'],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Export Attendance Punch Logs (Excel)',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/reports/attendance/export/excel?start_date=2026-09-01&end_date=2026-09-30',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'reports', 'attendance', 'export', 'excel'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Export Attendance Punch Logs (PDF)',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/pdf'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/reports/attendance/export/pdf?start_date=2026-09-01&end_date=2026-09-30',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'reports', 'attendance', 'export', 'pdf'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Monthly Attendance Register Matrix (1..31 Grid)',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/reports/attendance-register?month=9&year=2026',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'reports', 'attendance-register'],
                                    'query' => [
                                        ['key' => 'month', 'value' => '9'],
                                        ['key' => 'year', 'value' => '2026'],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Export Attendance Register (Excel)',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/reports/attendance-register/export/excel?month=9&year=2026',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'reports', 'attendance-register', 'export', 'excel'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Export Attendance Register (PDF)',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/pdf'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/reports/attendance-register/export/pdf?month=9&year=2026',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'reports', 'attendance-register', 'export', 'pdf'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Subordinates Daily Work Progress Reports',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/reports/daily-work-reports?start_date=2026-09-01',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'reports', 'daily-work-reports'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Team Leave Utilization Report',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/reports/leaves?year=2026',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'reports', 'leaves'],
                                    'query' => [
                                        ['key' => 'year', 'value' => '2026'],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Export Team Leave Report (Excel)',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/reports/leaves/export/excel?year=2026',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'reports', 'leaves', 'export', 'excel'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Export Team Leave Report (PDF)',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/pdf'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/reports/leaves/export/pdf?year=2026',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'reports', 'leaves', 'export', 'pdf'],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => '7. Push Notifications (FCM)',
                    'item' => [
                        [
                            'name' => 'Register / Sync Device FCM Token',
                            'request' => [
                                'method' => 'POST',
                                'header' => [
                                    ['key' => 'Content-Type', 'value' => 'application/json'],
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => json_encode([
                                        'fcm_token' => 'YOUR_FCM_DEVICE_REGISTRATION_TOKEN_HERE',
                                        'device_type' => 'android',
                                        'device_name' => 'Android Phone',
                                        'app_version' => '1.0.0',
                                    ], JSON_PRETTY_PRINT),
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/fcm-token',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'fcm-token'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Unregister Device Token (Logout)',
                            'request' => [
                                'method' => 'POST',
                                'header' => [
                                    ['key' => 'Content-Type', 'value' => 'application/json'],
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => json_encode([
                                        'fcm_token' => 'YOUR_FCM_DEVICE_REGISTRATION_TOKEN_HERE',
                                    ], JSON_PRETTY_PRINT),
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/fcm-token/delete',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'fcm-token', 'delete'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Get Notification Inbox',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/notifications?per_page=20',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'notifications'],
                                    'query' => [
                                        ['key' => 'per_page', 'value' => '20'],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Mark Notification Read',
                            'request' => [
                                'method' => 'POST',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/notifications/1/read',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'notifications', '1', 'read'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Mark All Notifications as Read',
                            'request' => [
                                'method' => 'POST',
                                'header' => [
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/notifications/mark-all-read',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'notifications', 'mark-all-read'],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Supervisor: Team Broadcast Notification',
                            'request' => [
                                'method' => 'POST',
                                'header' => [
                                    ['key' => 'Content-Type', 'value' => 'application/json'],
                                    ['key' => 'Accept', 'value' => 'application/json'],
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => json_encode([
                                        'title' => 'Urgent Team Meeting',
                                        'body' => 'All team members please attend briefing at 4:30 PM.',
                                    ], JSON_PRETTY_PRINT),
                                ],
                                'url' => [
                                    'raw' => '{{baseUrl}}/v1/employee/supervisor/team/notify',
                                    'host' => ['{{baseUrl}}'],
                                    'path' => ['v1', 'employee', 'supervisor', 'team', 'notify'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return response()->json($collection, 200, [
            'Content-Disposition' => 'attachment; filename="EMS_API_Collection.json"',
        ]);
    }
}

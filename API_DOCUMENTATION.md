# EMS Visitor & Employee Portal - API Documentation

Comprehensive REST API documentation for EMS Visitor & Employee Mobile/Web Client applications.

- **Base URL:** `http://127.0.0.1:8000/api` (Local) / `https://your-domain.com/api` (Production)
- **API Version:** `v1`
- **Default Headers for all API Requests:**
  ```http
  Accept: application/json
  Content-Type: application/json
  ```
- **Protected Endpoints Header:**
  ```http
  Authorization: Bearer <access_token>
  ```

---

## Standard Response Structure

### Success Response Format
```json
{
  "success": true,
  "message": "Optional descriptive status message",
  "data": { ... }
}
```

### Error Response Format
```json
{
  "success": false,
  "message": "Error description or reason for failure",
  "errors": {
    "field_name": [
      "Validation error message"
    ]
  }
}
```

### Common HTTP Status Codes
| Code | Status | Description |
|---|---|---|
| **200** | `OK` | Request succeeded. Returns data payload. |
| **201** | `Created` | Resource successfully created (e.g. Leave applied). |
| **401** | `Unauthorized` | Invalid credentials or missing/expired Bearer token. |
| **403** | `Forbidden` | Access denied (e.g. account inactive or non-employee token). |
| **404** | `Not Found` | Resource with specified identifier does not exist. |
| **409** | `Conflict` | Action already performed (e.g. check-in already marked for today). |
| **422** | `Unprocessable Content` | Validation failed (missing required parameters or invalid types). |
| **500** | `Internal Error` | Unexpected server exception. |

---

# Table of Contents
1. [Authentication & Profile APIs](#1-authentication--profile-apis)
   - [1.1 Employee Login](#11-employee-login)
   - [1.2 Employee Logout](#12-employee-logout)
   - [1.3 Get Employee Profile](#13-get-employee-profile)
   - [1.4 Get Agencies List](#14-get-agencies-list)
2. [Attendance APIs](#2-attendance-apis)
   - [2.1 Mark Check-In](#21-mark-check-in)
   - [2.2 Mark Check-Out](#22-mark-check-out)
   - [2.3 Get Today's Attendance](#23-get-todays-attendance)
   - [2.4 Attendance History (Filters & Pagination)](#24-attendance-history)
   - [2.5 Quick Attendance Toggle (Legacy)](#25-quick-attendance-toggle-legacy)
3. [Calendar APIs](#3-calendar-apis)
   - [3.1 Monthly Aggregated Calendar](#31-monthly-aggregated-calendar)
   - [3.2 Date Details Breakdown](#32-date-details-breakdown)
4. [Leave Management APIs](#4-leave-management-apis)
   - [4.1 Get Leave Types](#41-get-leave-types)
   - [4.2 Apply for Leave](#42-apply-for-leave)
   - [4.3 List Leave Applications](#43-list-leave-applications)
   - [4.4 Get Leave Application Details](#44-get-leave-application-details)
5. [Common Holiday APIs](#5-common-holiday-apis)
   - [5.1 Get Holiday List](#51-get-holiday-list)
   - [5.2 Get Holiday Calendar (Grouped by Month)](#52-get-holiday-calendar-grouped-by-month)
   - [5.3 Get Restricted Holidays List with Application Status](#53-get-restricted-holidays-list-with-application-status)
6. [Supervisor Management APIs (App-Only Admin Capabilities)](#6-supervisor-management-apis-app-only-admin-capabilities)
   - [6.1 Supervisor Dashboard](#61-supervisor-dashboard)
   - [6.2 Team Real-Time Attendance Roster](#62-team-real-time-attendance-roster)
   - [6.3 Team Members Directory](#63-team-members-directory)
   - [6.4 Subordinate Full Profile View](#64-subordinate-full-profile-view)
   - [6.5 Subordinate Leave Balances](#65-subordinate-leave-balances)
   - [6.6 Subordinate Attendance History](#66-subordinate-attendance-history)
   - [6.7 Attendance Regularization / Manual Punch](#67-attendance-regularization--manual-punch)
   - [6.8 Team Leaves List](#68-team-leaves-list)
   - [6.9 Team Leave Details](#69-team-leave-details)
   - [6.10 Approve Subordinate Leave](#610-approve-subordinate-leave)
   - [6.11 Reject Subordinate Leave](#611-reject-subordinate-leave)
   - [6.12 Apply Leave on Behalf of Subordinate](#612-apply-leave-on-behalf-of-subordinate)
   - [6.13 Team Turnout & Monthly Analytics Summary](#613-team-turnout--monthly-analytics-summary)
7. [Testing with Postman / cURL Quick Guide](#7-testing-with-postman--curl-quick-guide)

---

# 1. Authentication & Profile APIs

### 1.1 Employee Login
Authenticate an employee using Email, Mobile number, or Employee ID along with their password. Returns a Laravel Sanctum Bearer Token.

- **Endpoint:** `POST /api/v1/employee/auth/login`
- **Auth Required:** No (Public)
- **Headers:** `Content-Type: application/json`, `Accept: application/json`

#### Request Payload (Send Data)
| Parameter | Type | Required | Description / Constraints |
|---|---|---|---|
| `login` | `string` | **Yes\*** | Employee Email, Mobile number, or unique Employee ID (e.g. `emp@example.com`, `9876543210`, `101`) |
| `email` | `string` | Optional | Can be provided alternatively instead of `login` |
| `mobile` | `string` | Optional | Can be provided alternatively instead of `login` |
| `employee_id` | `integer\|string` | Optional | Can be provided alternatively instead of `login` |
| `password` | `string` | **Yes\*\*** | Account password (required if `pin` is not sent) |
| `pin` | `string` | **Yes\*\*** | 4-6 digit quick PIN (required if `password` is not sent) |
| `device_name` | `string` | No | Identifier for client device / token name (Default: `Mobile App`) |

*\*You can pass `login`, `email`, `mobile`, or `employee_id`.*  
*\*\*Provide either `password` or `pin`.*

**Sample Request Body (Password Login):**
```json
{
  "login": "emp@example.com",
  "password": "Password@123",
  "device_name": "Samsung Galaxy S24"
}
```

**Sample Request Body (PIN Login):**
```json
{
  "login": "9876543210",
  "pin": "1234",
  "device_name": "iPhone 15 Pro"
}
```

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "message": "Employee login successful.",
  "data": {
    "token": "1|nK0R2bL8uP3qXyZ...",
    "token_type": "Bearer",
    "employee": {
      "employee_id": 101,
      "name": "Rohit Sharma",
      "employee_name": "Rohit Sharma",
      "email": "rohit@example.com",
      "employee_email": "rohit@example.com",
      "mobile": "9876543210",
      "employee_mobile": "9876543210",
      "gender": 1,
      "blood_group": "B+",
      "designation_id": 13,
      "designation": "Supervisor",
      "agency_id": 2,
      "agency_name": "Apex Global Staffing",
      "agency_code": "AGS-01",
      "agency": {
        "id": 2,
        "agency_name": "Apex Global Staffing",
        "agency_code": "AGS-01"
      },
      "shift_id": 1,
      "shift_name": "General Shift",
      "shift_timing": "09:30 AM - 06:30 PM",
      "shift": {
        "id": 1,
        "shift_name": "General Shift",
        "start_time": "09:30:00",
        "end_time": "18:30:00",
        "timing": "09:30 AM - 06:30 PM",
        "grace_period": 15
      },
      "is_supervisor": true,
      "supervisor_role": "Supervisor",
      "team_size": 5,
      "subordinates_count": 5,
      "supervisor_capabilities": {
        "can_approve_leaves": true,
        "can_reject_leaves": true,
        "can_view_team_attendance": true,
        "can_view_team_dashboard": true,
        "can_view_team_members": true
      },
      "supervisor_id": null,
      "supervisor_name": null,
      "supervisor": null,
      "status": "Active",
      "profile_image": "http://127.0.0.1:8000/uploads/employees/1715000000_avatar.jpg",
      "joining_date": "2024-01-15",
      "employment_type": "Full-time",
      "is_pin_set": true,
      "is_pin_enabled": true,
      "pin_set_at": "2024-02-01T10:00:00Z",
      "last_login_at": "2026-09-30T12:15:00Z"
    }
  }
}
```

#### Error Responses
- **`401 Unauthorized` (Invalid Credentials):**
  ```json
  {
    "success": false,
    "message": "Invalid login credentials. Please check your Email/Mobile and Password."
  }
  ```
- **`403 Forbidden` (Inactive Account):**
  ```json
  {
    "success": false,
    "message": "Your employee account is currently inactive. Please contact HR or Supervisor."
  }
  ```
- **`422 Unprocessable Content` (Validation Error):**
  ```json
  {
    "message": "The login field is required. (and 1 more error)",
    "errors": {
      "login": ["The login field is required."],
      "password": ["The password field is required."]
    }
  }
  ```

---

### 1.2 Employee Logout
Revokes the current access token used for this session.

- **Endpoint:** `POST /api/v1/employee/auth/logout`
- **Auth Required:** Yes (`Bearer <token>`)
- **Headers:** `Authorization: Bearer <token>`, `Accept: application/json`

#### Request Payload
*None.*

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "message": "Logged out successfully. Token revoked."
}
```

---

### 1.3 Get Employee Profile
Fetches complete authenticated employee details including department, designation, supervisor, location, and assigned projects.

- **Endpoint:** `GET /api/v1/employee/profile`
- **Auth Required:** Yes (`Bearer <token>`)
- **Headers:** `Authorization: Bearer <token>`, `Accept: application/json`

#### Request Payload
*None.*

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "data": {
    "employee_id": 101,
    "name": "John Doe",
    "email": "emp@example.com",
    "mobile": "9876543210",
    "gender": "Male",
    "designation": {
      "id": 5,
      "name": "Senior Software Engineer"
    },
    "agency_id": 2,
    "agency_name": "Neural Info Overseas Private Limited",
    "agency_code": "NIOPL-001",
    "agency": {
      "id": 2,
      "agency_name": "Neural Info Overseas Private Limited",
      "agency_code": "NIOPL-001",
      "contact_person": "Sheetanshu Shrivastava",
      "email": "contact@neuralinfo.org",
      "phone": "+91 9876543210",
      "status": "Active"
    },
    "supervisor": {
      "id": 1,
      "name": "Sheetanshu Shrivastava",
      "email": "mrsrivastava@neuralinfo.org"
    },
    "location": {
      "country": "India",
      "state": "Madhya Pradesh",
      "city": "Bhopal"
    },
    "joining_date": "2024-01-15",
    "date_of_confirmation": "2024-07-15",
    "employment_type": "Full Time",
    "status": "Active",
    "profile_image": "http://127.0.0.1:8000/storage/employees/profile_101.jpg",
    "assigned_projects": [
      {
        "project_id": 12,
        "project_name": "EMS Visitor Portal",
        "role": "Lead Developer",
        "allocation_percent": 100
      }
    ]
  }
}
```

---

### 1.4 Get Agencies List
Returns list of all active staffing agencies available in the system.

- **Endpoint:** `GET /api/v1/employee/agencies`
- **Auth Required:** Yes (`Bearer <token>`)
- **Headers:** `Authorization: Bearer <token>`, `Accept: application/json`

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "agency_name": "Apex Global Staffing",
      "agency_code": "APX-01",
      "contact_person": "Rajesh Sharma",
      "email": "contact@apexstaffing.com",
      "phone": "+91 9876543210",
      "city": "Bhopal",
      "state": "Madhya Pradesh"
    },
    {
      "id": 2,
      "agency_name": "Neural Info Overseas Private Limited",
      "agency_code": "NIOPL-001",
      "contact_person": "Sheetanshu Shrivastava",
      "email": "contact@neuralinfo.org",
      "phone": "+91 7800789705",
      "city": "Kanpur",
      "state": "Uttar Pradesh"
    }
  ]
}
```

---

# 2. Attendance APIs

### 2.1 Mark Check-In
Records daily attendance check-in for the current date with optional GPS coordinates, IP address, and notes.

- **Endpoint:** `POST /api/v1/employee/attendance/check-in`
- **Auth Required:** Yes (`Bearer <token>`)
- **Headers:** `Authorization: Bearer <token>`, `Content-Type: application/json`, `Accept: application/json`

#### Request Payload (Send Data)
| Parameter | Type | Required | Description / Constraints |
|---|---|---|---|
| `latitude` | `numeric` | No | GPS Latitude (between `-90` and `90`) |
| `longitude` | `numeric` | No | GPS Longitude (between `-180` and `180`) |
| `note` | `string` | No | Optional remarks or location description (Max: 1000 chars) |
| `device_info` | `string` | No | Client device model or OS (Max: 255 chars) |

**Sample Request Body:**
```json
{
  "latitude": 23.259933,
  "longitude": 77.412613,
  "note": "Office check-in from Main Reception",
  "device_info": "OnePlus 11 5G"
}
```

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "message": "Checked in successfully at 09:30 AM.",
  "data": {
    "id": 482,
    "employee_id": 101,
    "date": "2026-09-30",
    "check_in_at": "2026-09-30 09:30:15",
    "check_in_time": "09:30 AM",
    "check_out_at": null,
    "check_out_time": null,
    "is_checked_in": true,
    "is_checked_out": false,
    "timer_active": true,
    "status": "checked_in",
    "working_seconds": 14415,
    "working_minutes": 240,
    "working_hours": 4.0,
    "working_hours_formatted": "4h 0m 15s",
    "working_time_clock": "04:00:15",
    "timer": {
      "is_running": true,
      "status": "running",
      "check_in_time": "09:30:15 AM",
      "check_in_time_short": "09:30 AM",
      "check_in_timestamp": 1790740215,
      "check_in_timestamp_ms": 1790740215000,
      "check_out_time": null,
      "check_out_time_short": null,
      "check_out_timestamp": null,
      "check_out_timestamp_ms": null,
      "server_time": "2026-09-30T13:30:30+05:30",
      "server_timestamp_ms": 1790754630000,
      "elapsed_seconds": 14415,
      "elapsed_minutes": 240,
      "elapsed_hours": 4.0,
      "clock": "04:00:15",
      "formatted": "4h 0m 15s"
    },
    "check_in_location": {
      "latitude": 23.259933,
      "longitude": 77.412613,
      "ip": "192.168.1.45"
    },
    "check_out_location": {
      "latitude": null,
      "longitude": null,
      "ip": null
    },
    "note": "Office check-in from Main Reception"
  }
}
```

#### Error Responses
- **`409 Conflict` (Already Checked In):**
  ```json
  {
    "success": false,
    "message": "Check-in is already marked for today at 09:30 AM.",
    "data": { ... }
  }
  ```

---

### 2.2 Mark Check-Out
Records daily attendance check-out. Stops the live timer and calculates final working duration.

- **Endpoint:** `POST /api/v1/employee/attendance/check-out`
- **Auth Required:** Yes (`Bearer <token>`)
- **Headers:** `Authorization: Bearer <token>`, `Content-Type: application/json`, `Accept: application/json`

#### Request Payload (Send Data)
| Parameter | Type | Required | Description / Constraints |
|---|---|---|---|
| `latitude` | `numeric` | No | GPS Latitude (between `-90` and `90`) |
| `longitude` | `numeric` | No | GPS Longitude (between `-180` and `180`) |
| `note` | `string` | No | Optional check-out remarks (Max: 1000 chars) |
| `device_info` | `string` | No | Client device model or OS |

**Sample Request Body:**
```json
{
  "latitude": 23.259933,
  "longitude": 77.412613,
  "note": "Shift completed",
  "device_info": "OnePlus 11 5G"
}
```

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "message": "Checked out successfully at 06:35 PM. Shift completed.",
  "data": {
    "id": 482,
    "employee_id": 101,
    "date": "2026-09-30",
    "check_in_at": "2026-09-30 09:30:15",
    "check_in_time": "09:30 AM",
    "check_out_at": "2026-09-30 18:35:42",
    "check_out_time": "06:35 PM",
    "is_checked_in": true,
    "is_checked_out": true,
    "timer_active": false,
    "status": "checked_out",
    "working_seconds": 32727,
    "working_minutes": 545,
    "working_hours": 9.09,
    "working_hours_formatted": "9h 5m 27s",
    "working_time_clock": "09:05:27",
    "timer": {
      "is_running": false,
      "status": "completed",
      "check_in_time": "09:30:15 AM",
      "check_in_time_short": "09:30 AM",
      "check_in_timestamp": 1790740215,
      "check_in_timestamp_ms": 1790740215000,
      "check_out_time": "06:35:42 PM",
      "check_out_time_short": "06:35 PM",
      "check_out_timestamp": 1790772942,
      "check_out_timestamp_ms": 1790772942000,
      "server_time": "2026-09-30T18:35:42+05:30",
      "server_timestamp_ms": 1790772942000,
      "elapsed_seconds": 32727,
      "elapsed_minutes": 545,
      "elapsed_hours": 9.09,
      "clock": "09:05:27",
      "formatted": "9h 5m 27s"
    },
    "check_in_location": {
      "latitude": 23.259933,
      "longitude": 77.412613,
      "ip": "192.168.1.45"
    },
    "check_out_location": {
      "latitude": 23.259933,
      "longitude": 77.412613,
      "ip": "192.168.1.45"
    },
    "note": "Shift completed"
  }
}
```

#### Error Responses
- **`422 Unprocessable Content` (No Prior Check-In):**
  ```json
  {
    "success": false,
    "message": "No check-in record found for today. Please check in first before checking out."
  }
  ```
- **`409 Conflict` (Already Checked Out):**
  ```json
  {
    "success": false,
    "message": "Check-out is already completed for today at 06:35 PM.",
    "data": { ... }
  }
  ```

---

### 2.3 Get Today's Attendance & Live Work Timer
Retrieves current day attendance status and live stopwatch timer metrics for the employee.
The mobile app can call this on launch to immediately know whether the employee has checked in, and start the digital timer widget.

- **Endpoint:** `GET /api/v1/employee/attendance/today`
- **Auth Required:** Yes (`Bearer <token>`)
- **Headers:** `Authorization: Bearer <token>`, `Accept: application/json`

#### Request Payload
*None.*

#### Success Response (`200 OK` - When not marked yet)
```json
{
  "success": true,
  "data": {
    "date": "2026-09-30",
    "is_checked_in": false,
    "is_checked_out": false,
    "timer_active": false,
    "status": "not_marked",
    "check_in_at": null,
    "check_in_time": null,
    "check_out_at": null,
    "check_out_time": null,
    "working_seconds": 0,
    "working_minutes": 0,
    "working_hours": 0,
    "working_hours_formatted": "0h 0m 0s",
    "working_time_clock": "00:00:00",
    "timer": {
      "is_running": false,
      "status": "not_started",
      "check_in_time": null,
      "check_in_time_short": null,
      "check_in_timestamp": null,
      "check_in_timestamp_ms": null,
      "check_out_time": null,
      "check_out_time_short": null,
      "check_out_timestamp": null,
      "check_out_timestamp_ms": null,
      "server_time": "2026-09-30T09:15:00+05:30",
      "server_timestamp_ms": 1790739300000,
      "elapsed_seconds": 0,
      "elapsed_minutes": 0,
      "elapsed_hours": 0,
      "clock": "00:00:00",
      "formatted": "0h 0m 0s"
    }
  }
}
```

#### Success Response (`200 OK` - When checked in, timer running live)
```json
{
  "success": true,
  "data": {
    "id": 482,
    "employee_id": 101,
    "date": "2026-09-30",
    "check_in_at": "2026-09-30 09:30:15",
    "check_in_time": "09:30 AM",
    "check_out_at": null,
    "check_out_time": null,
    "is_checked_in": true,
    "is_checked_out": false,
    "timer_active": true,
    "status": "checked_in",
    "working_seconds": 7245,
    "working_minutes": 120,
    "working_hours": 2.01,
    "working_hours_formatted": "2h 0m 45s",
    "working_time_clock": "02:00:45",
    "timer": {
      "is_running": true,
      "status": "running",
      "check_in_time": "09:30:15 AM",
      "check_in_time_short": "09:30 AM",
      "check_in_timestamp": 1790740215,
      "check_in_timestamp_ms": 1790740215000,
      "check_out_time": null,
      "check_out_time_short": null,
      "check_out_timestamp": null,
      "check_out_timestamp_ms": null,
      "server_time": "2026-09-30T11:31:00+05:30",
      "server_timestamp_ms": 1790747460000,
      "elapsed_seconds": 7245,
      "elapsed_minutes": 120,
      "elapsed_hours": 2.01,
      "clock": "02:00:45",
      "formatted": "2h 0m 45s"
    }
  }
}
```

#### Mobile App Implementation Note (Client-Side Stopwatch)
To run a 100% accurate, drift-free live stopwatch in Flutter / React Native / Native:
1. When API response arrives, read `data.timer.check_in_timestamp_ms` and `data.timer.server_timestamp_ms`.
2. Compute clock offset: `offset = DateTime.now().millisecondsSinceEpoch - data.timer.server_timestamp_ms`.
3. In a 1-second `Timer.periodic` or `setInterval`:
   ```javascript
   const currentEpochMs = Date.now() - offset;
   const elapsedSeconds = Math.max(0, Math.floor((currentEpochMs - check_in_timestamp_ms) / 1000));
   const hours = Math.floor(elapsedSeconds / 3600);
   const mins = Math.floor((elapsedSeconds % 3600) / 60);
   const secs = elapsedSeconds % 60;
   const clockStr = `${String(hours).padStart(2, '0')}:${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
   // Update UI display: clockStr (e.g. 02:00:46)
   ```
4. When `data.timer.is_running === false`, stop the interval and show the frozen `data.timer.clock`.

---

### 2.4 Attendance History
Fetch historical attendance records with date range filters, status filter, pagination, and total working hours summary.

- **Endpoint:** `GET /api/v1/employee/attendance/history`
- **Auth Required:** Yes (`Bearer <token>`)
- **Headers:** `Authorization: Bearer <token>`, `Accept: application/json`

#### Query Parameters (Send Data)
| Parameter | Type | Required | Description |
|---|---|---|---|
| `start_date` | `date` (`YYYY-MM-DD`) | No | Starting date for filter (e.g. `2026-09-01`) |
| `end_date` | `date` (`YYYY-MM-DD`) | No | Ending date for filter (must be `>= start_date`) |
| `status` | `string` | No | One of: `checked_in`, `checked_out`, `present`, `absent`, `half_day`, `late` |
| `per_page` | `integer` | No | Number of records per page (`1` to `100`, default: `20`) |
| `page` | `integer` | No | Page number for pagination |

**Example Request:**
`GET /api/v1/employee/attendance/history?start_date=2026-09-01&end_date=2026-09-21&per_page=15`

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "data": {
    "records": [
      {
        "id": 482,
        "employee_id": 101,
        "date": "2026-09-21",
        "check_in_at": "2026-09-21 09:30:15",
        "check_in_time": "09:30 AM",
        "check_out_at": "2026-09-21 18:35:42",
        "check_out_time": "06:35 PM",
        "is_checked_in": true,
        "is_checked_out": true,
        "status": "checked_out",
        "working_minutes": 545,
        "working_hours_formatted": "9h 5m",
        "note": "Office check-in"
      }
    ],
    "pagination": {
      "current_page": 1,
      "last_page": 2,
      "per_page": 15,
      "total_records": 18
    },
    "summary": {
      "total_working_minutes": 8940,
      "total_working_hours": 149
    }
  }
}
```

---

### 2.5 Quick Attendance Toggle (Legacy)
Legacy single endpoint for check-in / check-out toggle.

- **Endpoint:** `POST /api/employee/attendance/toggle`
- **Auth Required:** Yes (`Bearer <token>`)
- **Headers:** `Authorization: Bearer <token>`, `Content-Type: application/json`

#### Request Payload (Send Data)
| Parameter | Type | Required | Description |
|---|---|---|---|
| `login` | `string` | No | Email or Employee ID (required if `employee_id` omitted) |
| `employee_id` | `integer` | No | Employee ID (required if `login` omitted) |
| `password` | `string` | **Yes** | Employee password |
| `action` | `string` | No | `checkin`, `checkout`, or `auto` (default: `auto`) |
| `latitude` | `numeric` | No | GPS latitude |
| `longitude` | `numeric` | No | GPS longitude |
| `note` | `string` | No | Optional note |

---

# 3. Calendar APIs

### 3.1 Monthly Aggregated Calendar
Generates a complete day-by-day month view combining Attendance, Weekly Offs (Sundays), Public Holidays, and Approved Leaves with color coding and summary statistics.

- **Endpoint:** `GET /api/v1/employee/calendar`
- **Auth Required:** Yes (`Bearer <token>`)
- **Headers:** `Authorization: Bearer <token>`, `Accept: application/json`

#### Query Parameters (Send Data)
| Parameter | Type | Required | Description |
|---|---|---|---|
| `month` | `integer` | No | Month number `1` to `12` (Default: current month) |
| `year` | `integer` | No | Year between `2020` and `2035` (Default: current year) |

**Example Request:**
`GET /api/v1/employee/calendar?month=9&year=2026`

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "data": {
    "month": 9,
    "month_name": "September",
    "year": 2026,
    "summary": {
      "total_days": 30,
      "present_days": 18,
      "absent_days": 2,
      "leave_days": 1,
      "holiday_days": 1,
      "weekly_offs": 4
    },
    "days": [
      {
        "date": "2026-09-01",
        "day": 1,
        "day_name": "Tuesday",
        "day_type": "present",
        "status": "present",
        "status_label": "Present",
        "color": "#198754",
        "is_today": false,
        "is_future": false,
        "attendance": {
          "check_in": "09:30 AM",
          "check_out": "06:30 PM",
          "working_minutes": 540
        },
        "holiday": null,
        "leave": null
      },
      {
        "date": "2026-09-06",
        "day": 6,
        "day_name": "Sunday",
        "day_type": "weekly_off",
        "status": "weekly_off",
        "status_label": "Weekly Off (Sunday)",
        "color": "#ffc107",
        "is_today": false,
        "is_future": false,
        "attendance": null,
        "holiday": null,
        "leave": null
      },
      {
        "date": "2026-09-15",
        "day": 15,
        "day_name": "Tuesday",
        "day_type": "holiday",
        "status": "holiday",
        "status_label": "Holiday: Eid-e-Milad",
        "color": "#0dcaf0",
        "is_today": false,
        "is_future": false,
        "attendance": null,
        "holiday": {
          "name": "Eid-e-Milad",
          "remark": "Public Holiday"
        },
        "leave": null
      }
    ]
  }
}
```

---

### 3.2 Date Details Breakdown
Detailed breakdown for a specific date (attendance times, location, holiday information, leave duration & reason).

- **Endpoint:** `GET /api/v1/employee/calendar/date-details`
- **Auth Required:** Yes (`Bearer <token>`)
- **Headers:** `Authorization: Bearer <token>`, `Accept: application/json`

#### Query Parameters (Send Data)
| Parameter | Type | Required | Description |
|---|---|---|---|
| `date` | `date` (`YYYY-MM-DD`) | **Yes** | Target date (e.g. `2026-09-21`) |

**Example Request:**
`GET /api/v1/employee/calendar/date-details?date=2026-09-21`

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "data": {
    "date": "2026-09-21",
    "day": "Monday",
    "is_sunday": false,
    "is_holiday": false,
    "holiday_details": null,
    "is_leave": false,
    "leave_details": null,
    "attendance_details": {
      "check_in_at": "2026-09-21 09:30:15",
      "check_out_at": "2026-09-21 18:35:42",
      "status": "checked_out",
      "working_minutes": 545,
      "check_in_ip": "192.168.1.45",
      "check_in_location": {
        "latitude": 23.259933,
        "longitude": 77.412613
      },
      "note": "Office check-in"
    }
  }
}
```

---

# 4. Leave Management APIs

### 4.1 Get Leave Types
Fetch list of active leave types configured in the system with their allowed quotas.

- **Endpoint:** `GET /api/v1/employee/leave/types`
- **Auth Required:** Yes (`Bearer <token>`)
- **Headers:** `Authorization: Bearer <token>`, `Accept: application/json`

#### Request Payload
*None.*

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Casual Leave (CL)",
      "type": "paid",
      "days": 12
    },
    {
      "id": 2,
      "name": "Sick Leave (SL)",
      "type": "paid",
      "days": 10
    },
    {
      "id": 3,
      "name": "Earned Leave (EL)",
      "type": "paid",
      "days": 15
    }
  ]
}
```

---

### 4.2 Apply for Leave
Submits a new leave application with date range, reason, and optional supporting document file upload. Automatically prevents duplicate / overlapping leaves.

- **Endpoint:** `POST /api/v1/employee/leave/apply`
- **Auth Required:** Yes (`Bearer <token>`)
- **Headers:** `Authorization: Bearer <token>`, `Content-Type: multipart/form-data`, `Accept: application/json`

#### Request Payload (Multipart / Form Data)
| Parameter | Type | Required | Description / Constraints |
|---|---|---|---|
| `leave_type_id` | `integer` | **Yes** | Valid ID from `/api/v1/employee/leave/types` |
| `from_date` | `date` (`YYYY-MM-DD`) | **Yes** | Leave start date |
| `to_date` | `date` (`YYYY-MM-DD`) | **Yes** | Leave end date (must be `>= from_date`) |
| `reason` | `string` | **Yes** | Reason for leave (Max: 2000 chars) |
| `document` | `file` | No | Supporting document (pdf, jpg, jpeg, png, doc, docx, Max: 5MB) |

#### Success Response (`201 Created`)
```json
{
  "success": true,
  "message": "Leave application submitted successfully.",
  "data": {
    "id": 15,
    "leave_type": {
      "id": 1,
      "name": "Casual Leave (CL)"
    },
    "from_date": "2026-09-24",
    "to_date": "2026-09-25",
    "duration_days": 2,
    "reason": "Family function attending out of town.",
    "document_url": "http://127.0.0.1:8000/storage/leave_documents/document_sample.pdf",
    "status": "SENT",
    "status_label": "Pending Approval",
    "supervisor_remarks": null,
    "action_at": null,
    "action_by": null,
    "created_at": "2026-09-21 12:45:00",
    "timeline_activity": [
      {
        "id": 34,
        "actor_name": "John Doe",
        "actor_type": "Employee",
        "action": "applied",
        "remarks": "Leave application submitted by employee.",
        "created_at": "2026-09-21 12:45:00"
      }
    ]
  }
}
```

#### Error Responses
- **`422 Unprocessable Content` (Overlapping Leave Exists):**
  ```json
  {
    "success": false,
    "message": "You already have an active or pending leave request overlapping with these dates."
  }
  ```

---

### 4.3 List Leave Applications
Retrieves authenticated employee's past and pending leave applications with status filter and year filter.

- **Endpoint:** `GET /api/v1/employee/leave/list`
- **Auth Required:** Yes (`Bearer <token>`)
- **Headers:** `Authorization: Bearer <token>`, `Accept: application/json`

#### Query Parameters (Send Data)
| Parameter | Type | Required | Description |
|---|---|---|---|
| `status` | `string` | No | Filter by status: `ALL`, `DRAFT`, `SENT`, `APPROVED`, `REJECTED` (Default: `ALL`) |
| `year` | `integer` | No | Filter by year: `2020` to `2035` |
| `per_page` | `integer` | No | Results per page (`1` to `100`, default: `15`) |
| `page` | `integer` | No | Page number |

**Example Request:**
`GET /api/v1/employee/leave/list?status=APPROVED&year=2026`

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "data": {
    "records": [
      {
        "id": 12,
        "leave_type": {
          "id": 2,
          "name": "Sick Leave (SL)"
        },
        "from_date": "2026-08-10",
        "to_date": "2026-08-11",
        "duration_days": 2,
        "reason": "Viral fever recovery",
        "document_url": "http://127.0.0.1:8000/storage/leave_documents/doc.pdf",
        "status": "APPROVED",
        "status_label": "Approved",
        "supervisor_remarks": "Approved. Get well soon.",
        "action_at": "2026-08-10 10:15:00",
        "action_by": "Sheetanshu Shrivastava",
        "created_at": "2026-08-10 08:30:00"
      }
    ],
    "pagination": {
      "current_page": 1,
      "last_page": 1,
      "per_page": 15,
      "total_records": 1
    }
  }
}
```

---

### 4.4 Get Leave Application Details
Fetches single leave application details with complete timeline activities, approvals, and remarks.

- **Endpoint:** `GET /api/v1/employee/leave/{id}`
- **Auth Required:** Yes (`Bearer <token>`)
- **Headers:** `Authorization: Bearer <token>`, `Accept: application/json`

#### URL Parameters
| Parameter | Type | Required | Description |
|---|---|---|---|
| `id` | `integer` | **Yes** | Leave record ID |

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "data": {
    "id": 15,
    "leave_type": {
      "id": 1,
      "name": "Casual Leave (CL)"
    },
    "from_date": "2026-09-24",
    "to_date": "2026-09-25",
    "duration_days": 2,
    "reason": "Family function attending out of town.",
    "document_url": null,
    "status": "SENT",
    "status_label": "Pending Approval",
    "supervisor_remarks": null,
    "action_at": null,
    "action_by": null,
    "created_at": "2026-09-21 12:45:00",
    "timeline_activity": [
      {
        "id": 34,
        "actor_name": "John Doe",
        "actor_type": "Employee",
        "action": "applied",
        "remarks": "Leave application submitted by employee.",
        "created_at": "2026-09-21 12:45:00"
      }
    ]
  }
}
```

#### Error Response (`404 Not Found`)
```json
{
  "success": false,
  "message": "Leave application not found."
}
```

---

# 5. Common Holiday APIs

### 5.1 Get Holiday List
Retrieves calendar holidays for a given year.

- **Endpoint:** `GET /api/v1/holidays`
- **Auth Required:** Yes (`Bearer <token>`)
- **Headers:** `Authorization: Bearer <token>`, `Accept: application/json`

#### Query Parameters (Send Data)
| Parameter | Type | Required | Description |
|---|---|---|---|
| `year` | `integer` | No | Year (Default: current year) |

**Example Request:**
`GET /api/v1/holidays?year=2026`

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "data": {
    "year": 2026,
    "total_holidays": 14,
    "holidays": [
      {
        "id": 1,
        "name": "Republic Day",
        "date": "2026-01-26",
        "day": "Monday",
        "month": "January",
        "remark": "National Holiday",
        "is_past": true
      },
      {
        "id": 8,
        "name": "Independence Day",
        "date": "2026-08-15",
        "day": "Saturday",
        "month": "August",
        "remark": "National Holiday",
        "is_past": true
      },
      {
        "id": 11,
        "name": "Diwali",
        "date": "2026-11-08",
        "day": "Sunday",
        "month": "November",
        "remark": "Festival Holiday",
        "is_past": false
      }
    ]
  }
}
```

---

### 5.2 Get Holiday Calendar (Grouped by Month)
Retrieves holidays grouped month-by-month for easy UI rendering.

- **Endpoint:** `GET /api/v1/holidays/calendar`
- **Auth Required:** Yes (`Bearer <token>`)
- **Headers:** `Authorization: Bearer <token>`, `Accept: application/json`

#### Query Parameters (Send Data)
| Parameter | Type | Required | Description |
|---|---|---|---|
| `year` | `integer` | No | Target year (Default: current year) |

**Example Request:**
`GET /api/v1/holidays/calendar?year=2026`

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "data": {
    "year": 2026,
    "total_holidays": 14,
    "months": {
      "January": {
        "month": "January",
        "count": 1,
        "holidays": [
          {
            "id": 1,
            "name": "Republic Day",
            "date": "2026-01-26",
            "day": "Monday",
            "remark": "National Holiday"
          }
        ]
      },
      "August": {
        "month": "August",
        "count": 1,
        "holidays": [
          {
            "id": 8,
            "name": "Independence Day",
            "date": "2026-08-15",
            "day": "Saturday",
            "remark": "National Holiday"
          }
        ]
      }
    }
  }
}
```

---

### 5.3 Get Restricted Holidays List with Application Status
Fetches all optional / Restricted Holidays (RH) for the target year. When invoked by an authenticated employee, each holiday includes application status (`applied`, `leave_id`, `status`, `can_apply`), along with the employee's remaining RH quota (e.g. 5 days/year).

- **Endpoint:** `GET /api/v1/holidays/restricted`
- **Auth Required:** Yes (`Bearer <token>`)
- **Headers:** `Authorization: Bearer <token>`, `Accept: application/json`

#### Query Parameters
| Parameter | Type | Required | Description |
|---|---|---|---|
| `year` | `integer` | No | Target year (Default: current year) |

**Example Request:**
`GET /api/v1/holidays/restricted?year=2026`

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "data": {
    "year": 2026,
    "quota": {
      "total_allowed": 5,
      "used": 1,
      "pending": 0,
      "remaining": 4
    },
    "total_restricted_holidays": 26,
    "restricted_holidays": [
      {
        "id": 41,
        "name": "Lohri",
        "date": "2026-01-13",
        "day": "Tuesday",
        "month": "January",
        "remark": "Restricted Holiday - Optional (Any 5)",
        "is_past": true,
        "application_status": {
          "applied": true,
          "leave_id": 18,
          "status": "APPROVED",
          "can_apply": false
        }
      },
      {
        "id": 42,
        "name": "Makar Sankranti/Magh Bihu/ Pongal",
        "date": "2026-01-14",
        "day": "Wednesday",
        "month": "January",
        "remark": "Restricted Holiday - Optional (Any 5)",
        "is_past": false,
        "application_status": {
          "applied": false,
          "leave_id": null,
          "status": null,
          "can_apply": true
        }
      }
    ]
  }
}
```

---

# 6. Supervisor Management APIs (App-Only Admin Capabilities)

> [!NOTE]
> All endpoints under `/api/v1/employee/supervisor/*` require a Bearer token from an employee who has been designated as a **Supervisor** (`is_supervisor: true`).
> Supervisors have **Admin-level authority exclusively over their mapped team members** (`where supervisor_id = supervisor.employee_id`). They cannot view or modify employees reporting to other supervisors.

---

### 6.1 Supervisor Dashboard
High-level overview of team attendance today, turnout percentage, absent members, late punches, and pending leaves requiring action.

- **Endpoint:** `GET /api/v1/employee/supervisor/dashboard`
- **Auth Required:** Yes (`Bearer <token>`)

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "data": {
    "supervisor": {
      "id": 122,
      "name": "Shashank Shrivastva",
      "designation": "Supervisor"
    },
    "team_metrics": {
      "total_members": 9,
      "present_today": 7,
      "absent_today": 1,
      "late_today": 2,
      "early_in_today": 1,
      "on_leave_today": 1,
      "pending_leaves_count": 2,
      "turnout_percentage": 77.8
    },
    "recent_leave_requests": [ ... ]
  }
}
```

---

### 6.2 Team Real-Time Attendance Roster
View real-time daily punch status of all supervised team members with late/early metrics and shift comparison.

- **Endpoint:** `GET /api/v1/employee/supervisor/team/attendance`
- **Auth Required:** Yes (`Bearer <token>`)

#### Query Parameters
| Parameter | Type | Required | Description |
|---|---|---|---|
| `date` | `string` | No | Date format `YYYY-MM-DD` (Default: today) |
| `status` | `string` | No | Filter by: `ALL`, `PRESENT`, `ABSENT`, `LATE`, `EARLY_IN`, `ON_LEAVE` |

---

### 6.3 Team Members Directory
List all subordinates reporting to this supervisor with agency name, shift timings, and today's punch state.

- **Endpoint:** `GET /api/v1/employee/supervisor/team/members`
- **Auth Required:** Yes (`Bearer <token>`)

#### Query Parameters
| Parameter | Type | Required | Description |
|---|---|---|---|
| `search` | `string` | No | Search by member name, email, or mobile |

---

### 6.3.1 Form Options / Dropdown Metadata for Adding Employees
Retrieves all necessary lookup choices (Designations, Agencies, Shifts, Countries, States, Cities, Blood Groups, Employment Types) for the mobile app add/edit employee forms.

- **Endpoint:** `GET /api/v1/employee/supervisor/meta/form-options`
- **Auth Required:** Yes (`Bearer <token>`, Supervisor role)

#### Query Parameters (Cascading Dropdowns)
| Parameter | Type | Required | Description |
|---|---|---|---|
| `country_id` | `integer` | No | Filter states by country ID |
| `state_id` | `integer` | No | Filter cities by state ID |

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "data": {
    "designations": [
      { "id": 1, "name": "Software Engineer", "code": "SE" },
      { "id": 2, "name": "UI/UX Designer", "code": "UID" }
    ],
    "agencies": [
      { "id": 1, "agency_name": "Neural", "agency_code": "NIOPL-001" }
    ],
    "shifts": [
      { "id": 1, "shift_name": "General Shift", "start_time": "09:30:00", "end_time": "18:30:00", "grace_period": 15 }
    ],
    "countries": [
      { "id": 101, "name": "India" }
    ],
    "states": [],
    "cities": [],
    "blood_groups": ["A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-", "-"],
    "employment_types": ["Full-time", "Part-time", "Contract", "Intern", "Probation"],
    "genders": [
      { "id": 1, "name": "Male" },
      { "id": 2, "name": "Female" },
      { "id": 3, "name": "Other" }
    ]
  }
}
```

---

### 6.3.2 Add / Register New Employee Under Supervisor
Allows a supervisor to create and register a new employee directly from the mobile app. The employee is **automatically mapped under this supervisor** (`supervisor_id = $supervisor->employee_id`) and immediately added to their team.

- **Endpoint:** `POST /api/v1/employee/supervisor/team/members`
- **Auth Required:** Yes (`Bearer <token>`, Supervisor role)
- **Headers:** `Authorization: Bearer <token>`, `Content-Type: application/json` or `multipart/form-data`

#### Request Payload
| Parameter | Type | Required | Description / Constraints |
|---|---|---|---|
| `name` / `employee_name` | `string` | **Yes** | Full name of the employee (Max: 150 chars) |
| `email` / `employee_email` | `string` | **Yes** | Valid unique corporate or personal email |
| `mobile` / `employee_mobile` | `string` | **Yes** | Unique 10-digit mobile number |
| `password` / `employee_password` | `string` | **Yes** | Account password (Min: 6 characters) |
| `gender` / `employee_gender` | `integer` | No | `1` = Male, `2` = Female, `3` = Other (Default: 1) |
| `designation_id` | `integer` | **Yes** | Valid designation ID from metadata |
| `agency_id` | `integer` | No | Staffing agency ID (Defaults to supervisor's agency) |
| `shift_id` | `integer` | No | Work shift ID (Defaults to General Shift) |
| `employment_type` | `string` | No | `Full-time`, `Contract`, `Part-time`, `Intern` |
| `blood_group` | `string` | No | e.g. `O+`, `B+`, `A+`, `AB+` |
| `joining_date` | `string` | No | Date format `YYYY-MM-DD` (Defaults to today) |
| `date_of_confirmation` | `string` | No | Date format `YYYY-MM-DD` |
| `country_id` / `posting_country` | `integer` | No | Country ID |
| `state_id` / `posting_state` | `integer` | No | State ID |
| `city_id` / `posting_city` | `integer` | No | City ID |
| `emergency_contact` | `string` | No | Emergency phone number |
| `current_address` | `string` | No | Residential address |
| `profile_image` | `file` | No | Photo avatar (`jpeg`, `png`, `jpg`, Max 2MB) |

#### Success Response (`201 Created`)
```json
{
  "success": true,
  "message": "Employee 'Rahul Verma' created successfully and added to your team.",
  "data": {
    "employee_id": 125,
    "employee_name": "Rahul Verma",
    "employee_email": "rahul.verma@example.com",
    "employee_mobile": "9876543210",
    "employee_gender": "Male",
    "blood_group": "O+",
    "designation": {
      "id": 1,
      "name": "Software Engineer"
    },
    "agency": {
      "id": 1,
      "name": "Neural"
    },
    "shift": {
      "id": 1,
      "name": "General Shift",
      "timing": "09:30 - 18:30"
    },
    "supervisor": {
      "id": 122,
      "name": "Shashank Shrivastva"
    },
    "status": "Active",
    "employment_type": "Full-time",
    "joining_date": "2026-10-01",
    "profile_image": "http://127.0.0.1:8000/uploads/employees/1770807033_users-default.png"
  }
}
```

---

### 6.3.3 Update Subordinate Employee Details
Allows a supervisor to update details of an employee belonging to their supervised team.

- **Endpoint:** `PUT /api/v1/employee/supervisor/team/members/{employee_id}`
- **Auth Required:** Yes (`Bearer <token>`, Supervisor role)

#### Request Payload (All Optional)
`name`, `mobile`, `gender`, `designation_id`, `agency_id`, `shift_id`, `employment_type`, `blood_group`, `status` (`Active`/`Inactive`), `password`, `profile_image`.

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "message": "Subordinate 'Rahul Verma' updated successfully.",
  "data": {
    "employee_id": 125,
    "employee_name": "Rahul Verma",
    "employee_email": "rahul.verma@example.com",
    "employee_mobile": "9876543210",
    "designation": "Senior Software Engineer",
    "agency": "Neural",
    "shift": "General Shift",
    "status": "Active",
    "employment_type": "Full-time"
  }
}
```

---

### 6.4 Subordinate Full Profile View
Deep employee profile view (equivalent to Web Admin employee view) for a team member, including personal info, agency contractor details, shift parameters, leave balance summary, and recent attendance logs.

- **Endpoint:** `GET /api/v1/employee/supervisor/team/members/{employee_id}`
- **Auth Required:** Yes (`Bearer <token>`)

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "data": {
    "employee": {
      "id": 101,
      "name": "Mahendra Sahu",
      "email": "mahendra.s@neuralinfo.org",
      "mobile": "9876500001",
      "gender": 1,
      "blood_group": "O+",
      "profile_image": "http://127.0.0.1:8000/uploads/employees/avatar.png",
      "dob": "15 Aug, 1995",
      "joining_date": "01 Jan, 2024",
      "emergency_contact": "9876543210",
      "address": "Plot 12, Sector 5, Bhopal, MP",
      "status": "Active",
      "employment_type": "Full-time"
    },
    "agency": {
      "id": 1,
      "name": "Neural",
      "code": "NIOPL-001",
      "contact_person": "Sheetanshu Shrivastava",
      "phone": "+917800789705",
      "email": "contact@neuralinfo.org"
    },
    "designation": {
      "id": 1,
      "name": "Software Engineer",
      "code": "SE"
    },
    "shift": {
      "id": 1,
      "name": "General Shift",
      "timing": "09:30 - 18:30",
      "grace_period": 15
    },
    "supervisor": {
      "id": 122,
      "name": "Shashank Shrivastva"
    },
    "current_month_summary": {
      "month_name": "September 2026",
      "present_days": 21,
      "late_days": 2,
      "approved_leaves": 1
    },
    "leave_balances": [ ... ],
    "recent_attendances": [ ... ],
    "recent_leaves": [ ... ]
  }
}
```

---

### 6.5 Subordinate Leave Balances
View remaining quotas for all leave types (CL, SL, EL, RH, etc.) for a specific subordinate.

- **Endpoint:** `GET /api/v1/employee/supervisor/team/members/{employee_id}/leave-balances`
- **Auth Required:** Yes (`Bearer <token>`)

#### Query Parameters
| Parameter | Type | Required | Description |
|---|---|---|---|
| `year` | `integer` | No | Target calendar year (Default: current year) |

---

### 6.6 Subordinate Attendance History
Monthly calendar grid and daily records of check-ins, check-outs, lateness, absences, weekly offs, and approved leaves for a team member.

- **Endpoint:** `GET /api/v1/employee/supervisor/team/members/{employee_id}/attendance`
- **Auth Required:** Yes (`Bearer <token>`)

#### Query Parameters
| Parameter | Type | Required | Description |
|---|---|---|---|
| `month` | `integer` | No | 1 - 12 (Default: current month) |
| `year` | `integer` | No | 2020 - 2035 (Default: current year) |

---

### 6.7 Attendance Regularization (Disabled)
> [!WARNING]
> **Attendance Regularization by Supervisors has been DISABLED by company policy.**
> To maintain attendance integrity and fraud prevention, supervisors cannot manually insert or modify punch-in/out timestamps. Employees must punch their attendance live via the mobile app with GPS geolocation.
>
> Calling this endpoint returns HTTP `403 Forbidden`:
> ```json
> {
>   "success": false,
>   "message": "Attendance regularization feature has been disabled by company policy. Only live mobile punches with geolocation are permitted."
> }
> ```

---

### 6.8 Team Leaves List
List leave applications submitted by all mapped subordinates with status filtering and pagination.

- **Endpoint:** `GET /api/v1/employee/supervisor/leaves`
- **Auth Required:** Yes (`Bearer <token>`)

#### Query Parameters
| Parameter | Type | Required | Description |
|---|---|---|---|
| `status` | `string` | No | `ALL`, `PENDING`, `APPROVED`, `REJECTED` (Default: `ALL`) |
| `employee_id` | `integer` | No | Filter by specific subordinate |
| `year` | `integer` | No | Filter by year |
| `per_page` | `integer` | No | Pagination size (Default: 15) |

---

### 6.9 Team Leave Details
Detailed view of a team member's leave request with team conflict warning (alerts supervisor if other members are already on approved leave during the same dates).

- **Endpoint:** `GET /api/v1/employee/supervisor/leaves/{id}`
- **Auth Required:** Yes (`Bearer <token>`)

---

### 6.10 Approve Subordinate Leave
Approve a pending leave application.

- **Endpoint:** `POST /api/v1/employee/supervisor/leaves/{id}/approve`
- **Auth Required:** Yes (`Bearer <token>`)

#### Request Payload
| Parameter | Type | Required | Description |
|---|---|---|---|
| `remarks` | `string` | No | Optional approval notes |

---

### 6.11 Reject Subordinate Leave
Reject a leave application with mandatory reason.

- **Endpoint:** `POST /api/v1/employee/supervisor/leaves/{id}/reject`
- **Auth Required:** Yes (`Bearer <token>`)

#### Request Payload
| Parameter | Type | Required | Description |
|---|---|---|---|
| `reason` | `string` | **Yes** | Reason for rejecting the leave |

---

### 6.12 Apply Leave on Behalf of Subordinate
Allows the supervisor to apply for leave on behalf of a team member (e.g. employee is hospitalized or called the supervisor), with auto-approval option.

- **Endpoint:** `POST /api/v1/employee/supervisor/leaves/apply-for-member`
- **Auth Required:** Yes (`Bearer <token>`)

#### Request Payload
| Parameter | Type | Required | Description |
|---|---|---|---|
| `employee_id` | `integer` | **Yes** | Must belong to supervisor's team |
| `leave_type_id` | `integer` | **Yes** | Valid leave type ID |
| `from_date` | `string` | **Yes** | Date format `YYYY-MM-DD` |
| `to_date` | `string` | **Yes** | Date format `YYYY-MM-DD` |
| `reason` | `string` | **Yes** | Detailed reason |
| `auto_approve` | `boolean` | No | `true` (Default, auto-marks APPROVED) or `false` |
| `remarks` | `string` | No | Supervisor remarks |

---

### 6.13 Team Turnout & Monthly Analytics Summary
Aggregated monthly analytics on team attendance performance.

- **Endpoint:** `GET /api/v1/employee/supervisor/team/monthly-summary`
- **Auth Required:** Yes (`Bearer <token>`)

#### Query Parameters
| Parameter | Type | Required | Description |
|---|---|---|---|
| `month` | `integer` | No | Target month (1 - 12) |
| `year` | `integer` | No | Target year |

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "data": {
    "month": 9,
    "month_name": "September 2026",
    "year": 2026,
    "team_overview": {
      "total_team_members": 9,
      "days_in_month": 30,
      "total_present_mandays": 182,
      "total_late_incidents": 14,
      "total_leave_mandays": 8,
      "average_team_turnout_percentage": 82.5
    },
    "member_breakdown": [
      {
        "employee_id": 101,
        "employee_name": "Mahendra Sahu",
        "designation": "Software Engineer",
        "agency_name": "Neural",
        "present_days": 21,
        "late_days": 2,
        "leave_days": 1,
        "attendance_percentage": 70.0
      }
    ]
  }
}
```

---

### 6.13 Supervisor Reports Hub / Overview Dashboard
Provides a single consolidated dashboard of team attendance, task progress, and leave statistics for the supervisor's team.

- **Endpoint:** `GET /api/v1/employee/supervisor/reports/summary`
- **Auth Required:** Yes (`Bearer <token>`, must have supervisor role)
- **Headers:** `Authorization: Bearer <token>`, `Accept: application/json`

#### Query Parameters
| Parameter | Type | Required | Description |
|---|---|---|---|
| `start_date` | `string` | No | Date format `YYYY-MM-DD` (Defaults to start of current month) |
| `end_date` | `string` | No | Date format `YYYY-MM-DD` (Defaults to today) |

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "data": {
    "period": {
      "start_date": "2026-09-01",
      "end_date": "2026-09-30",
      "days_in_range": 30
    },
    "team_overview": {
      "total_members": 9,
      "total_expected_mandays": 270,
      "present_mandays": 210,
      "turnout_percentage": 77.8,
      "late_arrivals": 12,
      "early_arrivals": 4,
      "early_departures": 2,
      "total_hours_worked": 1820.5,
      "average_daily_hours_per_member": 8.67
    },
    "daily_work_reports": {
      "total_submissions": 195,
      "total_hours_logged": 1640.25
    },
    "leaves_summary": {
      "total_applications": 14,
      "approved_applications": 10,
      "approved_leave_days": 18,
      "pending_applications": 3,
      "rejected_applications": 1
    },
    "available_reports": [
      {
        "name": "Attendance Punch Logs Report",
        "endpoint": "/api/v1/employee/supervisor/reports/attendance",
        "method": "GET",
        "description": "Detailed punch-in/out logs with shift evaluation, punctuality, and working hours."
      },
      {
        "name": "Monthly Attendance Register Matrix",
        "endpoint": "/api/v1/employee/supervisor/reports/attendance-register",
        "method": "GET",
        "description": "Calendar grid (1-31) of daily attendance statuses for all subordinates."
      },
      {
        "name": "Daily Work & Project Task Reports",
        "endpoint": "/api/v1/employee/supervisor/reports/daily-work-reports",
        "method": "GET",
        "description": "Task descriptions, logged project hours, and daily submissions."
      },
      {
        "name": "Team Leave Utilization Report",
        "endpoint": "/api/v1/employee/supervisor/reports/leaves",
        "method": "GET",
        "description": "Subordinate leave balances, approved history, and application trends."
      }
    ]
  }
}
```

---

### 6.14 Team Attendance Detailed Logs Report
Detailed daily punch logs for all subordinates or a specific subordinate with shift timings, late/early flags, duration, and live timer.

- **Endpoint:** `GET /api/v1/employee/supervisor/reports/attendance`
- **Auth Required:** Yes (`Bearer <token>`)

#### Query Parameters
| Parameter | Type | Required | Description |
|---|---|---|---|
| `start_date` | `string` | No | Date format `YYYY-MM-DD` |
| `end_date` | `string` | No | Date format `YYYY-MM-DD` (must be `>= start_date`) |
| `employee_id` | `integer` | No | Filter by specific subordinate |
| `status` | `string` | No | `ALL`, `PRESENT`, `ABSENT`, `LATE`, `EARLY_IN`, `CHECKED_IN`, `CHECKED_OUT` |
| `per_page` | `integer` | No | Items per page (1 - 100, default 25) |
| `export` | `string` | No | `excel` or `pdf` (Downloads exported file directly) |

#### Export Endpoints (PDF & Excel)
Supervisors can download the detailed punch logs report in Excel (`.xlsx`) or PDF format using either query param or direct endpoints:
- **Excel Export:**
  - `GET /api/v1/employee/supervisor/reports/attendance?export=excel`
  - `GET /api/v1/employee/supervisor/reports/attendance/export/excel`
- **PDF Export:**
  - `GET /api/v1/employee/supervisor/reports/attendance?export=pdf`
  - `GET /api/v1/employee/supervisor/reports/attendance/export/pdf`

*Note: All filters (`start_date`, `end_date`, `employee_id`, `status`) apply to the exported files.*

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "data": {
    "records": [
      {
        "attendance_id": 482,
        "employee": {
          "id": 101,
          "name": "Mahendra Sahu",
          "designation": "Software Engineer",
          "agency": "Neural",
          "profile_image": null
        },
        "date": "2026-09-30",
        "day": "Wednesday",
        "check_in_at": "2026-09-30 09:30:15",
        "check_in_time": "09:30 AM",
        "check_out_at": "2026-09-30 18:35:42",
        "check_out_time": "06:35 PM",
        "status": "PRESENT",
        "shift": {
          "name": "General Shift",
          "start_time": "09:30:00",
          "end_time": "18:30:00"
        },
        "punctuality": {
          "is_late": false,
          "late_minutes": 0,
          "late_formatted": null,
          "is_early_in": false,
          "early_in_minutes": 0,
          "is_early_out": false,
          "early_out_minutes": 0
        },
        "working_seconds": 32727,
        "working_clock": "09:05:27",
        "working_formatted": "9h 5m 27s",
        "timer": {
          "is_running": false,
          "status": "completed",
          "clock": "09:05:27",
          "formatted": "9h 5m 27s"
        },
        "notes": "Regular shift"
      }
    ],
    "pagination": {
      "current_page": 1,
      "last_page": 1,
      "per_page": 25,
      "total_records": 1
    },
    "summary": {
      "total_records": 1,
      "total_hours_worked": 9.09,
      "late_arrivals_in_page": 0,
      "early_arrivals_in_page": 0
    }
  }
}
```

---

### 6.15 Monthly Attendance Register Matrix
Provides a complete 1..31 day-by-day attendance grid for all supervised subordinates (similar to the admin Attendance Register on web).

- **Endpoint:** `GET /api/v1/employee/supervisor/reports/attendance-register`
- **Auth Required:** Yes (`Bearer <token>`)

#### Query Parameters
| Parameter | Type | Required | Description |
|---|---|---|---|
| `month` | `integer` | No | 1 - 12 (Default: current month) |
| `year` | `integer` | No | e.g. 2026 (Default: current year) |
| `employee_id` | `integer` | No | Filter by specific subordinate |
| `export` | `string` | No | `excel` or `pdf` (Direct download of 1..31 monthly grid) |

#### Export Endpoints (PDF & Excel)
Identical to the Admin Attendance Register export layout (1..31 monthly grid matrix with color coding, daily punch times, and summary counts):
- **Excel Export:**
  - `GET /api/v1/employee/supervisor/reports/attendance-register?export=excel`
  - `GET /api/v1/employee/supervisor/reports/attendance-register/export/excel`
- **PDF Export:**
  - `GET /api/v1/employee/supervisor/reports/attendance-register?export=pdf`
  - `GET /api/v1/employee/supervisor/reports/attendance-register/export/pdf`

#### Response Highlights
- `days_headers`: Array of days 1..31 with weekday name, `is_sunday`, `is_holiday`, `holiday_name`.
- `status_legend`:
  - `P`: Present (On Time)
  - `L`: Late Arrival
  - `A`: Absent
  - `LV`: Approved Leave
  - `WO`: Weekly Off (Sunday)
  - `H`: Public Holiday
  - `-`: Upcoming Date / Not reached
- `register`: Array of each subordinate with their full 1..31 map including punch timings and total present, absent, leave, and late days.

---

### 6.16 Subordinates' Daily Work Progress Reports
Lists task descriptions and logged project hours submitted by team members (from the `dailyreport` system).

- **Endpoint:** `GET /api/v1/employee/supervisor/reports/daily-work-reports`
- **Auth Required:** Yes (`Bearer <token>`)

#### Query Parameters
| Parameter | Type | Required | Description |
|---|---|---|---|
| `start_date` | `string` | No | Date format `YYYY-MM-DD` |
| `end_date` | `string` | No | Date format `YYYY-MM-DD` |
| `employee_id` | `integer` | No | Filter by subordinate |
| `project_id` | `integer` | No | Filter by project |
| `per_page` | `integer` | No | Pagination size (Default: 20) |

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "data": {
    "records": [
      {
        "report_id": 142,
        "employee": {
          "id": 101,
          "name": "Mahendra Sahu",
          "designation": "Software Engineer"
        },
        "project": {
          "id": 12,
          "name": "EMS Visitor Management"
        },
        "entry_date": "2026-09-30",
        "starting_time": "09:30",
        "ending_time": "18:30",
        "total_hours": 9.0,
        "total_hours_raw": "9:0",
        "work_description": "Implemented attendance live stopwatch timer and supervisor reports API.",
        "submitted_at": "2026-09-30 18:35:00"
      }
    ],
    "pagination": { ... },
    "summary": {
      "total_reports_submitted": 1,
      "total_project_hours_in_page": 9.0,
      "active_contributors": 1,
      "unique_projects_count": 1
    }
  }
}
```

---

### 6.17 Team Leave Utilization Report
Comprehensive leave balances and application history for the supervised team.

- **Endpoint:** `GET /api/v1/employee/supervisor/reports/leaves`
- **Auth Required:** Yes (`Bearer <token>`)

#### Query Parameters
| Parameter | Type | Required | Description |
|---|---|---|---|
| `year` | `integer` | No | Filter by year (Default: current year) |
| `start_date` | `string` | No | Date format `YYYY-MM-DD` |
| `end_date` | `string` | No | Date format `YYYY-MM-DD` |
| `employee_id` | `integer` | No | Filter by subordinate |
| `status` | `string` | No | `ALL`, `APPROVED`, `REJECTED`, `PENDING` |
| `leave_type_id` | `integer` | No | Filter by leave type |
| `export` | `string` | No | `excel` or `pdf` (Direct download of team leave report) |

#### Export Endpoints (PDF & Excel)
Supervisors can export complete team leave applications and leave balance quotas in either Excel or formatted PDF:
- **Excel Export:**
  - `GET /api/v1/employee/supervisor/reports/leaves?export=excel`
  - `GET /api/v1/employee/supervisor/reports/leaves/export/excel`
- **PDF Export:**
  - `GET /api/v1/employee/supervisor/reports/leaves?export=pdf`
  - `GET /api/v1/employee/supervisor/reports/leaves/export/pdf`

*Note: The generated file includes application records (employee name, designation, dates, duration, reason, status) and each subordinate's leave balance breakdown (allotted, used, pending, remaining).*

---

---

# 7. Firebase Cloud Messaging (FCM) & Push Notifications

### Overview
The system provides complete Push Notification infrastructure powered by Firebase Cloud Messaging (FCM Project `vprotect-3e20f`):
- **Android / Mobile App**: Package `neuralinfo.in`, Project Number `996502919692`.
- **Web Admin Portal**: Service worker registered at `/firebase-messaging-sw.js` with desktop push notifications and real-time in-app alerts.
- **Automated Alerts**: Push notifications are automatically dispatched when:
  - An employee checks in or checks out (notifies their supervisor).
  - An employee applies for leave (notifies supervisor and admins).
  - A supervisor approves or rejects a leave request (notifies the employee).
  - A supervisor adds a new subordinate employee.
  - Supervisors can broadcast alerts to their entire team.
  - Admins can broadcast announcements or send targeted alerts from the Admin Web Dashboard (`/admin/notifications`).

---

### 7.1 Register / Sync Device FCM Token
Call this endpoint on app startup, user login, or whenever Firebase refreshes the FCM device token.

- **Endpoint:** `POST /api/v1/employee/fcm-token`
- **Auth Required:** Yes (`Bearer <token>`)
- **Headers:** `Authorization: Bearer <token>`, `Content-Type: application/json`

#### Request Payload
| Parameter | Type | Required | Description |
|---|---|---|---|
| `fcm_token` | `string` | **Yes** | FCM registration token from Firebase SDK |
| `device_type` | `string` | No | `android` (Default), `ios`, `web` |
| `device_name` | `string` | No | e.g. `Samsung Galaxy S23`, `Redmi Note 12` |
| `app_version` | `string` | No | e.g. `1.0.0` |

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "message": "FCM Device token registered successfully.",
  "data": {
    "device_id": 1,
    "device_type": "android",
    "device_name": "Samsung Galaxy S23",
    "is_active": true,
    "registered_at": "2026-10-01T15:30:00+05:30"
  }
}
```

---

### 7.2 Unregister FCM Token (On Logout)
Call this endpoint when the employee logs out to prevent notifications from being delivered to this device.

- **Endpoint:** `POST /api/v1/employee/fcm-token/delete`
- **Auth Required:** Yes (`Bearer <token>`)

#### Request Payload
| Parameter | Type | Required | Description |
|---|---|---|---|
| `fcm_token` | `string` | **Yes** | The device token to deactivate |

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "message": "Device token unregistered successfully."
}
```

---

### 7.3 Get Employee Notification History / Inbox
Fetches paginated notifications delivered to this employee, along with the unread count.

- **Endpoint:** `GET /api/v1/employee/notifications`
- **Auth Required:** Yes (`Bearer <token>`)

#### Query Parameters
| Parameter | Type | Required | Description |
|---|---|---|---|
| `per_page` | `integer` | No | Number of items per page (5 - 100, default: 20) |
| `page` | `integer` | No | Page number |

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "message": "Notifications retrieved successfully.",
  "unread_count": 1,
  "data": {
    "current_page": 1,
    "last_page": 1,
    "total": 1,
    "notifications": [
      {
        "id": 1,
        "title": "Leave Request Approved",
        "body": "Your Casual Leave request (2 days) has been approved.",
        "image_url": null,
        "action_url": null,
        "data": {
          "type": "leave_status",
          "leave_id": 45,
          "status": "APPROVED"
        },
        "sender_type": "system",
        "is_read": false,
        "read_at": null,
        "created_at": "2026-10-01T15:20:00+05:30",
        "time_ago": "10 minutes ago"
      }
    ]
  }
}
```

---

### 7.4 Mark Notification as Read
- **Endpoint:** `POST /api/v1/employee/notifications/{id}/read`
- **Auth Required:** Yes (`Bearer <token>`)

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "message": "Notification marked as read."
}
```

---

### 7.5 Mark All Notifications as Read
- **Endpoint:** `POST /api/v1/employee/notifications/mark-all-read`
- **Auth Required:** Yes (`Bearer <token>`)

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "message": "All notifications marked as read."
}
```

---

### 7.6 Supervisor: Broadcast Notification to Supervised Team
Allows a supervisor to send a quick push notification to all subordinates in their team.

- **Endpoint:** `POST /api/v1/employee/supervisor/team/notify`
- **Auth Required:** Yes (`Bearer <token>`, Supervisor role)

#### Request Payload
| Parameter | Type | Required | Description |
|---|---|---|---|
| `title` | `string` | **Yes** | Push notification title (Max 150 chars) |
| `body` | `string` | **Yes** | Notification message body (Max 1000 chars) |
| `image_url` | `string` | No | Optional banner image URL |

#### Success Response (`200 OK`)
```json
{
  "success": true,
  "message": "Team notification sent.",
  "result": {
    "success": true,
    "message": "Successfully delivered 8 notifications (0 failed).",
    "sent_count": 8,
    "failed_count": 0
  }
}
```

---

## 8. Testing with Postman / cURL Quick Guide

### Step 1: Login to get Bearer Token
```bash
curl -X POST "http://127.0.0.1:8000/api/v1/employee/auth/login" \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{
       "login": "emp@example.com",
       "password": "your_password",
       "device_name": "Postman Test"
     }'
```
Copy the `"token"` from response.

### Step 2: Use Token in Subsequent Requests
```bash
curl -X GET "http://127.0.0.1:8000/api/v1/employee/profile" \
     -H "Authorization: Bearer YOUR_COPIED_TOKEN" \
     -H "Accept: application/json"
```

### Step 3: Check-in with Geolocation
```bash
curl -X POST "http://127.0.0.1:8000/api/v1/employee/attendance/check-in" \
     -H "Authorization: Bearer YOUR_COPIED_TOKEN" \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{
       "latitude": 23.259933,
       "longitude": 77.412613,
       "note": "Office check-in"
     }'
```

        <!-- ========================================== -->
        <!-- 6. SUPERVISOR MANAGEMENT APIS -->
        <!-- ========================================== -->
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="ti ti-user-star text-primary"></i> 6. Supervisor Management APIs
                </h4>
                <p class="text-muted fs-14 mb-0">
                    Comprehensive suite for team supervisors to review subordinates, approve or reject leave requests, add new team members, monitor real-time attendance, export monthly registers & punch logs (Excel & PDF), and broadcast team alerts directly from the mobile app.
                </p>
            </div>
            <span class="badge bg-purple-subtle text-purple border border-purple-subtle px-3 py-1.5 fs-12 fw-semibold" style="background:#f5f3ff; color:#7c3aed; border-color:#ddd6fe;">
                <i class="ti ti-shield-check me-1"></i>Supervisor Role Required
            </span>
        </div>

        <!-- 6.1 Supervisor Dashboard -->
        <div class="endpoint-card" id="ep-sup-dashboard">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Supervisor Team Dashboard & Turnout Metrics</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">Supervisor Only</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/supervisor/dashboard</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/supervisor/dashboard', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Fetches high-level executive metrics for the supervisor's team including total active reportees, today's turnout (present, absent, late, early in, on leave), pending leaves count, and the 5 most recent leave applications.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X GET "{{ $baseUrl }}/v1/employee/supervisor/dashboard" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Accept: application/json"</code></pre>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "data": {
    "supervisor": {
      "id": 122,
      "name": "Team Supervisor",
      "designation": "Project Manager"
    },
    "team_metrics": {
      "total_members": 7,
      "present_today": 5,
      "absent_today": 1,
      "late_today": 1,
      "early_in_today": 2,
      "on_leave_today": 1,
      "pending_leaves_count": 2,
      "turnout_percentage": 71.4
    },
    "recent_leave_requests": [
      {
        "id": 5,
        "employee": {
          "id": 99,
          "name": "Pransi Gupta",
          "email": "pransi.g@neuralinfo.org",
          "designation": "Senior Developer"
        },
        "leave_type": { "id": 1, "name": "Paid Casual Leave" },
        "from_date": "2026-11-10",
        "to_date": "2026-11-12",
        "duration_days": 3,
        "status": "SENT",
        "status_label": "Pending Approval",
        "is_pending": true
      }
    ]
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6.2 Team Live Attendance Roster -->
        <div class="endpoint-card" id="ep-sup-attendance">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Team Live Attendance Roster</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">Supervisor Only</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/supervisor/team/attendance?date=2026-10-01&status=ALL</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/supervisor/team/attendance?date=2026-10-01&status=ALL', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Returns real-time today's check-in / check-out status of all supervised team members. Displays punch times, shift grace evaluations (e.g. <code>Late by 22m</code>, <code>Early In by 15m</code>), check-in notes, and current location.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Query Parameters</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead><tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">date</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>Format: <code>YYYY-MM-DD</code> (Default: Today)</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">status</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td><code>ALL</code>, <code>PRESENT</code>, <code>ABSENT</code>, <code>LATE</code>, <code>EARLY_IN</code>, <code>ON_LEAVE</code></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X GET "{{ $baseUrl }}/v1/employee/supervisor/team/attendance?date=2026-10-01" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Accept: application/json"</code></pre>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "data": {
    "date": "2026-10-01",
    "turnout": { "total": 7, "present": 5, "late": 1, "absent": 1 },
    "team_members": [
      {
        "employee_id": 99,
        "name": "Pransi Gupta",
        "designation": "Senior Developer",
        "punched_in": true,
        "punched_out": false,
        "check_in_time": "09:20 AM",
        "check_out_time": null,
        "work_clock": "04:12:30",
        "status": "PRESENT"
      }
    ]
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6.3 Team Members Directory -->
        <div class="endpoint-card" id="ep-sup-members">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Team Members Directory</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">Supervisor Only</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/supervisor/team/members?search=Pransi</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/supervisor/team/members', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Lists all employees reporting to the authenticated supervisor with contact info, designation, partner agency, shift timings, posting location, and instant today's punch indicator.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Query Parameters</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead><tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">search</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>Search by name, email, or employee ID</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">status</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td><code>Active</code> (default), <code>Inactive</code></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "data": {
    "total_count": 7,
    "members": [
      {
        "employee_id": 99,
        "name": "Pransi Gupta",
        "email": "pransi.g@neuralinfo.org",
        "mobile": "9876543210",
        "designation": "Senior Developer",
        "agency": "Neural Info Overseas",
        "shift": "General Shift (09:30 AM - 06:30 PM)",
        "status": "Active",
        "today_punch": {
          "punched_in": true,
          "check_in_time": "09:20 AM",
          "work_duration": "4h 12m"
        }
      }
    ]
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6.4 Dropdown Metadata for Forms -->
        <div class="endpoint-card" id="ep-sup-meta-options">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Dropdown Metadata Options for Adding Employees</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">Supervisor Only</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/supervisor/meta/form-options</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/supervisor/meta/form-options', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Retrieves active dropdown list items required to populate the mobile form when adding or updating subordinate employees (Designations, Staffing Agencies, Work Shifts, Countries, and standard Blood Groups).
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X GET "{{ $baseUrl }}/v1/employee/supervisor/meta/form-options" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Accept: application/json"</code></pre>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "data": {
    "designations": [
      { "id": 1, "name": "Software Engineer", "code": "SE-01" },
      { "id": 2, "name": "Senior Developer", "code": "SR-DEV" }
    ],
    "agencies": [
      { "id": 1, "name": "Neural Info Overseas", "code": "NIOPL-001" }
    ],
    "shifts": [
      { "id": 1, "name": "General Shift", "start_time": "09:30:00", "end_time": "18:30:00" }
    ],
    "countries": [
      { "id": 101, "name": "India" }
    ],
    "employment_types": ["Full Time", "Part Time", "Contract", "Internship"],
    "blood_groups": ["A+", "A-", "B+", "B-", "O+", "O-", "AB+", "AB-"]
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6.5 Add / Register New Subordinate Employee -->
        <div class="endpoint-card" id="ep-sup-member-create">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method POST fs-12">POST</span>
                    <span class="fw-bold text-dark fs-15">Register / Add New Subordinate Employee</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">Supervisor Only</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-primary font-mono fw-semibold">/api/v1/employee/supervisor/team/members</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/supervisor/team/members', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Enables authorized supervisors to register new subordinates directly from the mobile app. The new employee is automatically assigned to report directly to the creating supervisor.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Request Body (JSON)</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead><tr><th>Field</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">first_name</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>First name of employee</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">last_name</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>Last name of employee</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">email</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>Unique email address</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">mobile</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>Unique 10-digit mobile number</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">password</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>Login password (min: 6 chars)</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">designation_id</td>
                                        <td class="font-mono text-muted">integer</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>ID from <code>/meta/form-options</code></td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">agency_id</td>
                                        <td class="font-mono text-muted">integer</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>Staffing agency ID</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">shift_id</td>
                                        <td class="font-mono text-muted">integer</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>Shift timing ID</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">joining_date</td>
                                        <td class="font-mono text-muted">date</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>Format: <code>YYYY-MM-DD</code></td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">salary</td>
                                        <td class="font-mono text-muted">numeric</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>Monthly base salary</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 mb-3">
                            <span class="fs-12 fw-bold text-dark text-uppercase tracking-wider d-block mb-1">
                                <i class="ti ti-code me-1 text-primary"></i> Request Body (Raw JSON Payload):
                            </span>
                            <div class="code-console">
                                <div class="code-console-header">
                                    <span class="fs-11 text-muted font-mono">application/json</span>
                                    <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy JSON</button>
                                </div>
                                <pre class="language-json"><code>{
  "first_name": "Rohan",
  "last_name": "Verma",
  "email": "rohan.verma@neuralinfo.org",
  "mobile": "9811223344",
  "password": "Password@123",
  "designation_id": 1,
  "agency_id": 1,
  "shift_id": 1,
  "joining_date": "2026-10-01",
  "employment_type": "Full Time",
  "gender": "Male",
  "salary": 35000,
  "city": "Bhopal",
  "state": "Madhya Pradesh",
  "country_id": 101,
  "pin_code": "462001"
}</code></pre>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X POST "{{ $baseUrl }}/v1/employee/supervisor/team/members" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "first_name": "Rohan",
    "last_name": "Verma",
    "email": "rohan.verma@neuralinfo.org",
    "mobile": "9811223344",
    "password": "Password@123",
    "designation_id": 1,
    "agency_id": 1,
    "shift_id": 1
  }'</code></pre>
                        </div>
                        <div class="mt-3">
                            <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (201 Created):</span>
                            <div class="code-console mt-1">
                                <pre class="language-json"><code>{
  "success": true,
  "message": "Subordinate employee Rohan Verma registered successfully.",
  "data": {
    "employee_id": 145,
    "name": "Rohan Verma",
    "email": "rohan.verma@neuralinfo.org",
    "mobile": "9811223344",
    "designation": "Software Engineer",
    "agency": "Neural Info Overseas",
    "supervisor_id": 122,
    "status": "Active",
    "joining_date": "2026-10-01"
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6.6 Subordinate Full Profile View -->
        <div class="endpoint-card" id="ep-sup-member-profile">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Subordinate Full Profile View</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">Supervisor Only</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/supervisor/team/members/{employee_id}</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/supervisor/team/members/99', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Retrieves detailed profile information for a supervised subordinate employee, including job details, address, emergency contact, shift timing, and current attendance status.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">URL Path Parameters</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead><tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">employee_id</td>
                                        <td class="font-mono text-muted">integer</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>Subordinate's Employee ID (e.g. <code>99</code>)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X GET "{{ $baseUrl }}/v1/employee/supervisor/team/members/99" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Accept: application/json"</code></pre>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "data": {
    "employee_id": 99,
    "first_name": "Pransi",
    "last_name": "Gupta",
    "name": "Pransi Gupta",
    "email": "pransi.g@neuralinfo.org",
    "mobile": "9876543210",
    "designation": { "id": 2, "name": "Senior Developer" },
    "agency": { "id": 1, "name": "Neural Info Overseas" },
    "shift": { "id": 1, "name": "General Shift", "timing": "09:30 AM - 06:30 PM" },
    "employment_type": "Full Time",
    "status": "Active",
    "joining_date": "2024-03-15",
    "city": "Bhopal",
    "state": "Madhya Pradesh"
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6.7 Update Subordinate Employee Details -->
        <div class="endpoint-card" id="ep-sup-member-update">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method PUT fs-12">PUT</span>
                    <span class="fw-bold text-dark fs-15">Update Subordinate Employee Details</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">Supervisor Only</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-warning font-mono fw-semibold">/api/v1/employee/supervisor/team/members/{employee_id}</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/supervisor/team/members/99', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Updates details of a subordinate employee reporting to the supervisor (designation, shift, agency, address, contact, or status).
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-dark text-uppercase tracking-wider d-block mb-1">
                            <i class="ti ti-code me-1 text-primary"></i> Request Body (Raw JSON Payload):
                        </span>
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-11 text-muted font-mono">application/json</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy JSON</button>
                            </div>
                            <pre class="language-json"><code>{
  "first_name": "Pransi",
  "last_name": "Gupta",
  "mobile": "9876543210",
  "designation_id": 2,
  "shift_id": 1,
  "salary": 45000,
  "city": "Bhopal",
  "state": "Madhya Pradesh"
}</code></pre>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "message": "Employee details updated successfully.",
  "data": {
    "employee_id": 99,
    "name": "Pransi Gupta",
    "designation": "Senior Developer",
    "updated_at": "2026-10-01 16:45:00"
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6.8 Subordinate Monthly Attendance Sheet -->
        <div class="endpoint-card" id="ep-sup-member-attendance">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Subordinate Monthly Attendance Sheet</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">Supervisor Only</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/supervisor/team/members/{employee_id}/attendance?month=9&year=2026</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/supervisor/team/members/99/attendance', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Retrieves full monthly 30/31-day attendance register for a specific subordinate, complete with monthly summary (present days, late days, absent days, approved leaves) and daily breakdown.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Query Parameters</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead><tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">month</td>
                                        <td class="font-mono text-muted">integer</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>Month 1-12 (default: current month)</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">year</td>
                                        <td class="font-mono text-muted">integer</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>Year (default: current year)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "data": {
    "employee": { "id": 99, "name": "Pransi Gupta" },
    "period": "September 2026",
    "summary": { "present_days": 22, "absent_days": 1, "late_days": 2, "leave_days": 1 },
    "attendance_records": [
      {
        "date": "2026-09-30",
        "day": "Wednesday",
        "status": "PRESENT",
        "check_in": "09:30 AM",
        "check_out": "06:35 PM",
        "working_hours": "9h 5m"
      }
    ]
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6.9 Subordinate Leave Balances Quota -->
        <div class="endpoint-card" id="ep-sup-leave-balances">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Subordinate Leave Balances Quota</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">Supervisor Only</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/supervisor/team/members/{employee_id}/leave-balances?year=2026</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/supervisor/team/members/99/leave-balances', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Retrieves exact quota balances for each leave type for a subordinate employee (Allotted, Used, Pending, Remaining).
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X GET "{{ $baseUrl }}/v1/employee/supervisor/team/members/99/leave-balances?year=2026" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Accept: application/json"</code></pre>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "data": {
    "employee": { "id": 99, "name": "Pransi Gupta" },
    "year": 2026,
    "balances": [
      {
        "leave_type_id": 1,
        "leave_name": "Casual Leave",
        "allotted_days": 12,
        "used_days": 2,
        "pending_days": 1,
        "remaining_days": 9
      },
      {
        "leave_type_id": 2,
        "leave_name": "Sick Leave",
        "allotted_days": 10,
        "used_days": 1,
        "pending_days": 0,
        "remaining_days": 9
      }
    ]
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6.10 Team Leaves Queue -->
        <div class="endpoint-card" id="ep-sup-leaves">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">List Team Leaves Queue</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">Supervisor Only</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/supervisor/leaves?status=PENDING&per_page=15</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/supervisor/leaves?status=PENDING', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Retrieves paginated list of leave applications submitted by subordinates. Filterable by status (<code>ALL</code>, <code>PENDING</code>, <code>APPROVED</code>, <code>REJECTED</code>), specific subordinate ID, and year.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Query Parameters</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead><tr><th>Parameter</th><th>Type</th><th>Default</th><th>Description</th></tr></thead>
                                <tbody>
                                    <tr><td class="font-mono text-primary">status</td><td>string</td><td>ALL</td><td><code>PENDING</code>, <code>APPROVED</code>, <code>REJECTED</code>, or <code>ALL</code></td></tr>
                                    <tr><td class="font-mono text-primary">employee_id</td><td>integer</td><td>null</td><td>Filter by specific subordinate ID</td></tr>
                                    <tr><td class="font-mono text-primary">year</td><td>integer</td><td>current</td><td>Filter by year (e.g. <code>2026</code>)</td></tr>
                                    <tr><td class="font-mono text-primary">per_page</td><td>integer</td><td>15</td><td>Page size (max 100)</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "data": {
    "total_pending": 2,
    "leaves": [
      {
        "id": 5,
        "employee": { "id": 99, "name": "Pransi Gupta" },
        "leave_type": "Paid Casual Leave",
        "from_date": "2026-11-10",
        "to_date": "2026-11-12",
        "duration_days": 3,
        "status": "SENT",
        "status_label": "Pending Approval",
        "reason": "Family function"
      }
    ]
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6.11 Team Leave Details & Conflicts -->
        <div class="endpoint-card" id="ep-sup-leave-details">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Get Team Leave Details & Roster Conflicts</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">Supervisor Only</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/supervisor/leaves/{id}</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/supervisor/leaves/5', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Inspects a subordinate's leave application in detail. Automatically checks if other team members are already scheduled on approved leave during the same dates (<code>team_conflicts</code>), allowing the supervisor to avoid staffing shortages.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X GET "{{ $baseUrl }}/v1/employee/supervisor/leaves/5" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Accept: application/json"</code></pre>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "data": {
    "id": 5,
    "employee": { "id": 99, "name": "Pransi Gupta" },
    "from_date": "2026-11-10",
    "to_date": "2026-11-12",
    "duration_days": 3,
    "status": "SENT",
    "team_conflicts": [
      {
        "employee_name": "Rohan Verma",
        "leave_type": "Sick Leave",
        "dates": "2026-11-10 to 2026-11-11"
      }
    ]
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6.12 Approve Subordinate Leave -->
        <div class="endpoint-card" id="ep-sup-leave-approve">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method POST fs-12">POST</span>
                    <span class="fw-bold text-dark fs-15">Approve Subordinate Leave</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">Supervisor Only</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-primary font-mono fw-semibold">/api/v1/employee/supervisor/leaves/{id}/approve</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/supervisor/leaves/5/approve', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Approves a subordinate's pending leave application. Updates status to <code>APPROVED</code>, records supervisor remarks, logs an audit entry, and automatically sends approval email and push notifications.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-dark text-uppercase tracking-wider d-block mb-1">
                            <i class="ti ti-code me-1 text-primary"></i> Request Body (Raw JSON Payload):
                        </span>
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-11 text-muted font-mono">application/json</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy JSON</button>
                            </div>
                            <pre class="language-json"><code>{
  "remarks": "Approved, enjoy your vacation."
}</code></pre>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "message": "Leave application for Pransi Gupta has been approved successfully.",
  "data": {
    "id": 5,
    "status": "APPROVED",
    "supervisor_remarks": "Approved, enjoy your vacation.",
    "action_at": "2026-10-01 11:00:30",
    "action_by": "Team Supervisor"
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6.13 Reject Subordinate Leave -->
        <div class="endpoint-card" id="ep-sup-leave-reject">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method POST fs-12">POST</span>
                    <span class="fw-bold text-dark fs-15">Reject Subordinate Leave</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">Supervisor Only</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-primary font-mono fw-semibold">/api/v1/employee/supervisor/leaves/{id}/reject</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/supervisor/leaves/5/reject', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Rejects a subordinate's pending leave application. Requires an explanation reason, updates status to <code>REJECTED</code>, and dispatches instant push & email alert.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-dark text-uppercase tracking-wider d-block mb-1">
                            <i class="ti ti-code me-1 text-primary"></i> Request Body (Raw JSON Payload):
                        </span>
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-11 text-muted font-mono">application/json</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy JSON</button>
                            </div>
                            <pre class="language-json"><code>{
  "reason": "Critical project deliverable during this week; leave cannot be granted."
}</code></pre>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "message": "Leave application for Pransi Gupta has been rejected.",
  "data": {
    "id": 5,
    "status": "REJECTED",
    "supervisor_remarks": "Critical project deliverable during this week; leave cannot be granted.",
    "action_at": "2026-10-01 11:00:32"
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6.14 Apply Leave on Behalf of Subordinate -->
        <div class="endpoint-card" id="ep-sup-leave-apply-member">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method POST fs-12">POST</span>
                    <span class="fw-bold text-dark fs-15">Apply Leave on Behalf of Subordinate</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">Supervisor Only</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-primary font-mono fw-semibold">/api/v1/employee/supervisor/leaves/apply-for-member</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/supervisor/leaves/apply-for-member', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Enables a supervisor to submit and auto-approve a leave application for a subordinate (e.g. subordinate called in sick or has emergency without app access).
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Request Body (JSON)</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead><tr><th>Field</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>
                                <tbody>
                                    <tr><td class="font-mono text-primary fw-semibold">employee_id</td><td>integer</td><td><span class="param-required">Required</span></td><td>Subordinate employee ID</td></tr>
                                    <tr><td class="font-mono text-primary fw-semibold">leave_type_id</td><td>integer</td><td><span class="param-required">Required</span></td><td>Leave type ID</td></tr>
                                    <tr><td class="font-mono text-primary fw-semibold">from_date</td><td>date</td><td><span class="param-required">Required</span></td><td>YYYY-MM-DD</td></tr>
                                    <tr><td class="font-mono text-primary fw-semibold">to_date</td><td>date</td><td><span class="param-required">Required</span></td><td>YYYY-MM-DD</td></tr>
                                    <tr><td class="font-mono text-primary fw-semibold">reason</td><td>string</td><td><span class="param-required">Required</span></td><td>Explanation reason</td></tr>
                                    <tr><td class="font-mono text-primary fw-semibold">auto_approve</td><td>boolean</td><td><span class="param-optional">Optional</span></td><td>Default: true</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 mb-3">
                            <span class="fs-12 fw-bold text-dark text-uppercase tracking-wider d-block mb-1">
                                <i class="ti ti-code me-1 text-primary"></i> Request Body (Raw JSON Payload):
                            </span>
                            <div class="code-console">
                                <div class="code-console-header">
                                    <span class="fs-11 text-muted font-mono">application/json</span>
                                    <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy JSON</button>
                                </div>
                                <pre class="language-json"><code>{
  "employee_id": 99,
  "leave_type_id": 1,
  "from_date": "2026-10-05",
  "to_date": "2026-10-06",
  "reason": "Subordinate requested leave via phone call for family event.",
  "auto_approve": true
}</code></pre>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (201 Created):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "message": "Leave application recorded and approved successfully for Pransi Gupta.",
  "data": {
    "leave_id": 48,
    "employee": { "id": 99, "name": "Pransi Gupta" },
    "from_date": "2026-10-05",
    "to_date": "2026-10-06",
    "duration_days": 2,
    "status": "APPROVED",
    "applied_by_supervisor": true
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6.15 Team Monthly Turnout & Analytics Summary -->
        <div class="endpoint-card" id="ep-sup-monthly-summary">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Team Monthly Turnout & Analytics Summary</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">Supervisor Only</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/supervisor/team/monthly-summary?month=9&year=2026</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/supervisor/team/monthly-summary?month=9&year=2026', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Aggregates monthly team attendance percentages, total shifts scheduled, on-time percentage, punctuality rate, total hours worked, and member breakdown.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X GET "{{ $baseUrl }}/v1/employee/supervisor/team/monthly-summary?month=9&year=2026" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Accept: application/json"</code></pre>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "data": {
    "period": "September 2026",
    "turnout_percentage": 92.4,
    "punctuality_rate": 88.5,
    "total_hours_worked": 1248.5,
    "total_members": 7,
    "members_summary": [
      {
        "employee_id": 99,
        "name": "Pransi Gupta",
        "present_days": 22,
        "late_days": 1,
        "absent_days": 0,
        "turnout_percent": 100.0
      }
    ]
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6.16 Supervisor Reports Hub Overview -->
        <div class="endpoint-card" id="ep-sup-report-summary">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Supervisor Reports Hub / Overview Dashboard</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">Supervisor Only</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/supervisor/reports/summary</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/supervisor/reports/summary', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Aggregated statistical hub providing high-level metrics for all 4 reporting areas: Attendance Punch Logs, 1..31 Register Matrix, Daily Work Reports, and Team Leave Quotas.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X GET "{{ $baseUrl }}/v1/employee/supervisor/reports/summary" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Accept: application/json"</code></pre>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "data": {
    "team_count": 7,
    "attendance_report": { "total_punches": 154, "total_hours": 1342.2 },
    "leaves_report": { "total_applications": 8, "approved": 6, "pending": 2 },
    "work_reports": { "total_submitted": 42, "total_hours_logged": 336.0 }
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6.17 Team Attendance Detailed Punch Logs Report & Exports -->
        <div class="endpoint-card" id="ep-sup-report-attendance">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Team Attendance Detailed Punch Logs Report & Exports</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle fs-11">Excel & PDF Export</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">Supervisor Only</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/supervisor/reports/attendance?start_date=2026-09-01&end_date=2026-09-30</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/supervisor/reports/attendance', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Detailed daily punch logs for all subordinates or a specific subordinate with shift timings, late/early flags, duration, and live timer. Supervisors can also export this report directly to <strong>Excel (.xlsx)</strong> or <strong>PDF</strong>.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Query Parameters & Exports</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead><tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>
                                <tbody>
                                    <tr><td class="font-mono text-primary">start_date</td><td>date</td><td>Optional</td><td>YYYY-MM-DD</td></tr>
                                    <tr><td class="font-mono text-primary">end_date</td><td>date</td><td>Optional</td><td>YYYY-MM-DD</td></tr>
                                    <tr><td class="font-mono text-primary">employee_id</td><td>integer</td><td>Optional</td><td>Filter subordinate</td></tr>
                                    <tr><td class="font-mono text-primary">status</td><td>string</td><td>Optional</td><td>ALL, PRESENT, ABSENT, LATE</td></tr>
                                    <tr><td class="font-mono text-primary">export</td><td>string</td><td>Optional</td><td><code>excel</code> or <code>pdf</code> (Direct download)</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="alert alert-info border-0 rounded-3 p-3 fs-13 mb-0">
                            <strong><i class="ti ti-download me-1"></i> Direct Export URLs:</strong><br>
                            &bull; <strong>Excel:</strong> <code>GET /api/v1/employee/supervisor/reports/attendance/export/excel</code><br>
                            &bull; <strong>PDF:</strong> <code>GET /api/v1/employee/supervisor/reports/attendance/export/pdf</code>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample JSON Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "data": {
    "records": [
      {
        "attendance_id": 482,
        "employee": { "id": 99, "name": "Pransi Gupta", "designation": "Senior Developer" },
        "date": "2026-09-30",
        "check_in_time": "09:30 AM",
        "check_out_time": "06:35 PM",
        "working_formatted": "9h 5m 27s",
        "status": "PRESENT"
      }
    ],
    "summary": { "total_records": 1, "total_hours_worked": 9.09 }
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6.18 Monthly Attendance Register Matrix (1..31 Grid) & Exports -->
        <div class="endpoint-card" id="ep-sup-report-attendance-register">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Monthly Attendance Register Matrix (1..31 Grid) & Exports</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle fs-11">Excel & PDF Export</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">Supervisor Only</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/supervisor/reports/attendance-register?month=9&year=2026</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/supervisor/reports/attendance-register', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Provides a complete 1..31 day-by-day attendance grid for all supervised subordinates, identical to the Admin Web Attendance Register. Includes color coding (P, A, L, LV, WO, H) and direct <strong>Excel (.xlsx)</strong> and <strong>PDF</strong> downloads!
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Query Parameters & Exports</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead><tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>
                                <tbody>
                                    <tr><td class="font-mono text-primary">month</td><td>integer</td><td>Optional</td><td>1-12 (default: current month)</td></tr>
                                    <tr><td class="font-mono text-primary">year</td><td>integer</td><td>Optional</td><td>e.g. 2026</td></tr>
                                    <tr><td class="font-mono text-primary">employee_id</td><td>integer</td><td>Optional</td><td>Filter subordinate</td></tr>
                                    <tr><td class="font-mono text-primary">export</td><td>string</td><td>Optional</td><td><code>excel</code> or <code>pdf</code> (Direct download)</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="alert alert-info border-0 rounded-3 p-3 fs-13 mb-0">
                            <strong><i class="ti ti-download me-1"></i> Direct 1..31 Register Export URLs:</strong><br>
                            &bull; <strong>Excel:</strong> <code>GET /api/v1/employee/supervisor/reports/attendance-register/export/excel</code><br>
                            &bull; <strong>PDF:</strong> <code>GET /api/v1/employee/supervisor/reports/attendance-register/export/pdf</code>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample JSON Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "data": {
    "month_name": "September 2026",
    "days_in_month": 30,
    "status_legend": { "P": "Present", "L": "Late", "A": "Absent", "LV": "Leave", "WO": "Off", "H": "Holiday" },
    "register": [
      {
        "employee_id": 99,
        "name": "Pransi Gupta",
        "designation": "Senior Developer",
        "days": {
          "1": { "status": "P", "check_in": "09:30 AM", "check_out": "06:30 PM" },
          "2": { "status": "L", "check_in": "09:55 AM", "check_out": "06:30 PM" },
          "6": { "status": "WO" }
        },
        "totals": { "present": 22, "late": 1, "absent": 0, "leave": 1 }
      }
    ]
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6.19 Subordinates' Daily Work Progress Reports -->
        <div class="endpoint-card" id="ep-sup-report-daily-work">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Subordinates' Daily Work Progress Reports</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">Supervisor Only</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/supervisor/reports/daily-work-reports?start_date=2026-09-01</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/supervisor/reports/daily-work-reports', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Lists task descriptions and logged project hours submitted by team members (from the daily work report system).
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X GET "{{ $baseUrl }}/v1/employee/supervisor/reports/daily-work-reports" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Accept: application/json"</code></pre>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "data": {
    "reports": [
      {
        "id": 12,
        "employee_name": "Pransi Gupta",
        "date": "2026-09-30",
        "project": "EMS Mobile App Development",
        "work_description": "Integrated Push Notifications and Supervisor APIs.",
        "hours": 8.5
      }
    ]
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6.20 Team Leave Utilization Report & Exports -->
        <div class="endpoint-card" id="ep-sup-report-leaves">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Team Leave Utilization Report & Exports</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle fs-11">Excel & PDF Export</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">Supervisor Only</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/supervisor/reports/leaves?year=2026&status=ALL</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/supervisor/reports/leaves', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Comprehensive leave balance quotas and application history for the supervised team. Includes each subordinate's allotted, used, pending, and remaining quota breakdown, plus direct <strong>Excel (.xlsx)</strong> and <strong>PDF</strong> downloads!
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Query Parameters & Exports</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead><tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>
                                <tbody>
                                    <tr><td class="font-mono text-primary">year</td><td>integer</td><td>Optional</td><td>e.g. 2026</td></tr>
                                    <tr><td class="font-mono text-primary">employee_id</td><td>integer</td><td>Optional</td><td>Filter subordinate</td></tr>
                                    <tr><td class="font-mono text-primary">status</td><td>string</td><td>Optional</td><td>ALL, APPROVED, PENDING, REJECTED</td></tr>
                                    <tr><td class="font-mono text-primary">export</td><td>string</td><td>Optional</td><td><code>excel</code> or <code>pdf</code> (Direct download)</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="alert alert-info border-0 rounded-3 p-3 fs-13 mb-0">
                            <strong><i class="ti ti-download me-1"></i> Direct Leave Report Export URLs:</strong><br>
                            &bull; <strong>Excel:</strong> <code>GET /api/v1/employee/supervisor/reports/leaves/export/excel</code><br>
                            &bull; <strong>PDF:</strong> <code>GET /api/v1/employee/supervisor/reports/leaves/export/pdf</code>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample JSON Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "data": {
    "year": 2026,
    "summary": { "total_applications": 8, "approved": 6, "pending": 2 },
    "leaves": [
      {
        "leave_id": 5,
        "employee_name": "Pransi Gupta",
        "designation": "Senior Developer",
        "leave_type": "Paid Casual Leave",
        "from_date": "10 Nov, 2026",
        "to_date": "12 Nov, 2026",
        "duration": "3 Days",
        "status": "APPROVED",
        "reason": "Personal function"
      }
    ],
    "member_balances": [
      {
        "employee_id": 99,
        "employee_name": "Pransi Gupta",
        "designation": "Senior Developer",
        "balances": [
          { "leave_name": "Casual Leave", "allotted_days": 12, "used_days": 2, "remaining_days": 9 }
        ]
      }
    ]
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6.21 Broadcast Team Push Notification -->
        <div class="endpoint-card" id="ep-sup-team-notify">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method POST fs-12">POST</span>
                    <span class="fw-bold text-dark fs-15">Broadcast Team Push Notification</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">Supervisor Only</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-primary font-mono fw-semibold">/api/v1/employee/supervisor/team/notify</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/supervisor/team/notify', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Broadcasts an urgent push notification and in-app message to all active mobile devices belonging to the supervisor's team.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-dark text-uppercase tracking-wider d-block mb-1">
                            <i class="ti ti-code me-1 text-primary"></i> Request Body (Raw JSON Payload):
                        </span>
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-11 text-muted font-mono">application/json</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy JSON</button>
                            </div>
                            <pre class="language-json"><code>{
  "title": "Team Standup Reminder",
  "body": "Daily briefing will begin in Conference Room A at 10:15 AM."
}</code></pre>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "message": "Team notification broadcast successfully to 7 active devices.",
  "data": {
    "recipients_count": 7,
    "dispatched_at": "2026-10-01 10:00:00"
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-5 border-slate-200">

        <!-- ========================================== -->
        <!-- 7. PUSH NOTIFICATIONS & FCM APIS -->
        <!-- ========================================== -->
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="ti ti-bell-ringing text-danger"></i> 7. Firebase Cloud Messaging (FCM) & Push Notifications
                </h4>
                <p class="text-muted fs-14 mb-0">
                    Infrastructure for registering mobile device tokens with Firebase Cloud Messaging (Project: <code>vprotect-3e20f</code>) and receiving real-time push alerts on punch actions, leave status updates, broadcasts, and in-app notifications.
                </p>
            </div>
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5 fs-12 fw-semibold">
                <i class="ti ti-brand-firebase me-1"></i>Firebase Cloud Messaging
            </span>
        </div>

        <!-- 7.1 Register / Sync Device FCM Token -->
        <div class="endpoint-card" id="ep-fcm-register">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method POST fs-12">POST</span>
                    <span class="fw-bold text-dark fs-15">Register / Sync Device FCM Token</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-primary font-mono fw-semibold">/api/v1/employee/fcm-token</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/fcm-token', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Call this endpoint on app launch, user login, or whenever Firebase SDK generates/refreshes the device registration token. Associates the device with the authenticated employee.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Request Body (JSON)</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead><tr><th>Field</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>
                                <tbody>
                                    <tr><td class="font-mono text-primary fw-semibold">fcm_token</td><td>string</td><td><span class="param-required">Required</span></td><td>Device registration token from Firebase SDK</td></tr>
                                    <tr><td class="font-mono text-primary fw-semibold">device_type</td><td>string</td><td><span class="param-optional">Optional</span></td><td><code>android</code> (default), <code>ios</code>, <code>web</code></td></tr>
                                    <tr><td class="font-mono text-primary fw-semibold">device_name</td><td>string</td><td><span class="param-optional">Optional</span></td><td>e.g. <code>Samsung Galaxy S24</code></td></tr>
                                    <tr><td class="font-mono text-primary fw-semibold">app_version</td><td>string</td><td><span class="param-optional">Optional</span></td><td>e.g. <code>1.0.0</code></td></tr>
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
  "fcm_token": "fN8d9s7fA9s8d7fA9sd8f7as98df7as9d8f7:APA91bF...",
  "device_type": "android",
  "device_name": "Samsung Galaxy S24",
  "app_version": "1.0.0"
}</code></pre>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "message": "FCM Device token registered successfully.",
  "data": {
    "device_id": 1,
    "device_type": "android",
    "device_name": "Samsung Galaxy S24",
    "is_active": true,
    "registered_at": "2026-10-01 15:30:00"
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7.2 Unregister FCM Token on Logout -->
        <div class="endpoint-card" id="ep-fcm-delete">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method POST fs-12">POST</span>
                    <span class="fw-bold text-dark fs-15">Unregister FCM Token on Logout</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-primary font-mono fw-semibold">/api/v1/employee/fcm-token/delete</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/fcm-token/delete', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Call this when the employee logs out of the mobile app to disable notifications from being sent to this device.
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
  "fcm_token": "fN8d9s7fA9s8d7fA9sd8f7as98df7as9d8f7:APA91bF..."
}</code></pre>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "message": "FCM Device token unregistered successfully."
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7.3 In-App Notifications Inbox & Unread Counter -->
        <div class="endpoint-card" id="ep-notifications-list">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">In-App Notifications Inbox & Unread Counter</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/notifications?per_page=20</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/notifications', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Retrieves paginated notifications inbox for the employee bell icon, including overall unread count badge number.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X GET "{{ $baseUrl }}/v1/employee/notifications" \
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
    "unread_count": 3,
    "notifications": [
      {
        "id": 105,
        "title": "Leave Approved",
        "body": "Your Casual Leave for 01 Oct to 03 Oct has been approved.",
        "type": "leave_approval",
        "is_read": false,
        "created_at": "2026-10-01 11:00:30",
        "time_ago": "2 hours ago"
      }
    ]
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7.4 Mark Single Notification as Read -->
        <div class="endpoint-card" id="ep-notification-read">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method POST fs-12">POST</span>
                    <span class="fw-bold text-dark fs-15">Mark Single Notification as Read</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-primary font-mono fw-semibold">/api/v1/employee/notifications/{id}/read</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/notifications/105/read', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Marks a single notification as read when tapped by the user in the mobile app.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X POST "{{ $baseUrl }}/v1/employee/notifications/105/read" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Accept: application/json"</code></pre>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "message": "Notification marked as read.",
  "data": {
    "id": 105,
    "is_read": true,
    "remaining_unread": 2
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7.5 Mark All Notifications as Read -->
        <div class="endpoint-card" id="ep-notifications-read-all">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method POST fs-12">POST</span>
                    <span class="fw-bold text-dark fs-15">Mark All Notifications as Read</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-primary font-mono fw-semibold">/api/v1/employee/notifications/mark-all-read</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/notifications/mark-all-read', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Marks all unread notifications for the employee as read in a single click.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X POST "{{ $baseUrl }}/v1/employee/notifications/mark-all-read" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Accept: application/json"</code></pre>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "message": "All notifications marked as read.",
  "data": {
    "updated_count": 3,
    "remaining_unread": 0
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

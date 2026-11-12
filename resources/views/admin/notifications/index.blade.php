@extends('admin.layout.layout')

@section('main-section')
<div class="page-wrapper">
    <div class="content">

        <!-- Page Header -->
        <div class="d-md-flex d-block align-items-center justify-content-between mb-4">
            <div class="my-auto">
                <h3 class="page-title mb-1">Push Notifications</h3>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Push Notifications</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                <button type="button" class="btn btn-outline-primary me-2" onclick="window.requestHrmsNotificationPermission && window.requestHrmsNotificationPermission()">
                    <i class="ti ti-bell-ringing me-1"></i> Enable Web Alerts
                </button>
            </div>
        </div>
        <!-- /Page Header -->

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Stats Row -->
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card flex-fill border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-13 text-muted">Total Active Devices</span>
                            <h3 class="mb-0 mt-1">{{ number_format($deviceStats['total']) }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-primary-transparent text-primary rounded-circle">
                            <i class="ti ti-devices fs-20"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card flex-fill border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-13 text-muted">Android Mobile Devices</span>
                            <h3 class="mb-0 mt-1 text-success">{{ number_format($deviceStats['android']) }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-success-transparent text-success rounded-circle">
                            <i class="ti ti-brand-android fs-20"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card flex-fill border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-13 text-muted">Web Browser Clients</span>
                            <h3 class="mb-0 mt-1 text-info">{{ number_format($deviceStats['web']) }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-info-transparent text-info rounded-circle">
                            <i class="ti ti-browser fs-20"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card flex-fill border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-13 text-muted">iOS Devices</span>
                            <h3 class="mb-0 mt-1 text-warning">{{ number_format($deviceStats['ios']) }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-warning-transparent text-warning rounded-circle">
                            <i class="ti ti-brand-apple fs-20"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Send Notification Card -->
            <div class="col-xl-4 col-lg-5">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-send me-1 text-primary"></i> Send Push Notification
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.notifications.send') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Target Audience <span class="text-danger">*</span></label>
                                <select name="target_type" id="target_type" class="form-select" required>
                                    <option value="all_employees">All Mobile Employees</option>
                                    <option value="single_employee">Specific Employee</option>
                                    <option value="all_web">All Web Admin Users</option>
                                </select>
                            </div>

                            <div class="mb-3" id="employee_select_box" style="display: none;">
                                <label class="form-label fw-semibold">Select Employee <span class="text-danger">*</span></label>
                                <select name="employee_id" id="employee_id" class="form-select select2">
                                    <option value="">-- Choose Employee --</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->employee_id }}">{{ $emp->employee_name }} ({{ $emp->employee_email }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Notification Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" placeholder="e.g. Office Notice / Meeting Alert" required maxlength="150">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Message Body <span class="text-danger">*</span></label>
                                <textarea name="body" class="form-control" rows="4" placeholder="Enter notification message here..." required maxlength="1000"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Action / Link URL (Optional)</label>
                                <input type="url" name="action_url" class="form-control" placeholder="https://example.com/notices">
                                <small class="text-muted">Opens this URL when user taps the notification.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Image URL (Optional)</label>
                                <input type="url" name="image_url" class="form-control" placeholder="https://example.com/banner.png">
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-send me-1"></i> Send Notification
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Firebase Config Summary Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="card-title mb-0"><i class="ti ti-brand-firebase text-warning me-1"></i> Firebase Project Setup</h6>
                    </div>
                    <div class="card-body p-3 fs-13">
                        <p class="mb-2"><strong>Project ID:</strong> <code>{{ $firebaseConfig['projectId'] }}</code></p>
                        <p class="mb-2"><strong>Sender ID:</strong> <code>{{ $firebaseConfig['messagingSenderId'] }}</code></p>
                        <p class="mb-2"><strong>Android Package:</strong> <code>neuralinfo.in</code></p>
                        <p class="mb-0 text-muted">Web Push Service Worker registered at <code>/firebase-messaging-sw.js</code>.</p>
                    </div>
                </div>
            </div>

            <!-- Notifications History Table -->
            <div class="col-xl-8 col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-history me-1 text-secondary"></i> Notification History & Logs
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Title & Message</th>
                                        <th>Target</th>
                                        <th>Status</th>
                                        <th>Sent Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($notifications as $item)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold text-dark">{{ $item->title }}</div>
                                                <div class="text-muted fs-12 text-truncate" style="max-width: 280px;">{{ $item->body }}</div>
                                                @if($item->action_url)
                                                    <a href="{{ $item->action_url }}" target="_blank" class="fs-11 text-primary"><i class="ti ti-external-link"></i> Link</a>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->employee)
                                                    <span class="badge bg-light text-dark">{{ $item->employee->employee_name }}</span>
                                                @elseif($item->user)
                                                    <span class="badge bg-light text-info">{{ $item->user->name }} (Admin)</span>
                                                @elseif($item->target_type === 'all_employees')
                                                    <span class="badge bg-primary-transparent text-primary">All Employees</span>
                                                @elseif($item->target_type === 'all_web_users')
                                                    <span class="badge bg-info-transparent text-info">All Web Users</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($item->target_type) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->status === 'sent')
                                                    <span class="badge bg-success-transparent text-success"><i class="ti ti-check me-1"></i> Sent</span>
                                                @elseif($item->status === 'pending')
                                                    <span class="badge bg-warning-transparent text-warning">Pending</span>
                                                @else
                                                    <span class="badge bg-danger-transparent text-danger">Failed</span>
                                                @endif
                                            </td>
                                            <td class="fs-12 text-muted">
                                                {{ $item->created_at ? $item->created_at->format('d M, h:i A') : '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                No push notifications sent yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($notifications->hasPages())
                        <div class="card-footer bg-white border-top">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const targetSelect = document.getElementById('target_type');
    const employeeBox = document.getElementById('employee_select_box');
    const employeeInput = document.getElementById('employee_id');

    if (targetSelect && employeeBox) {
        targetSelect.addEventListener('change', function() {
            if (this.value === 'single_employee') {
                employeeBox.style.display = 'block';
                employeeInput.setAttribute('required', 'required');
            } else {
                employeeBox.style.display = 'none';
                employeeInput.removeAttribute('required');
            }
        });
    }
});
</script>
@endsection

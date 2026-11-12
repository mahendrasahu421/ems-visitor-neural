@extends('layout.master')
@section('title', $title ?? 'Create / Edit Permission')

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <style>
        .perm-form-card {
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .action-chip-label {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.15s ease;
            user-select: none;
            font-size: 13px;
            font-weight: 500;
        }

        .action-chip-label:hover {
            background: #eff6ff;
            border-color: #bfdbfe;
        }

        .action-chip-label.selected {
            background: #eff6ff;
            border-color: #3b82f6;
            color: #1d4ed8;
            box-shadow: 0 1px 4px rgba(59, 130, 246, 0.15);
        }

        .nav-pills-custom .nav-link {
            border-radius: 10px;
            font-weight: 600;
            font-size: 13px;
            padding: 8px 18px;
            color: #64748b;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .nav-pills-custom .nav-link.active {
            background: #0d6efd;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(13, 110, 253, 0.25);
        }
    </style>

    <div class="page-wrapper">
        <div class="content">

            @php
                $isEdit = isset($permission) && $permission->exists;
            @endphp

            {{-- Breadcrumbs & Header --}}
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2 mb-md-0">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('permission') }}" class="btn btn-sm btn-white border rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                            <i class="ti ti-arrow-left fs-16 text-dark"></i>
                        </a>
                        <div>
                            <h3 class="mb-0 text-dark fw-bold">{{ $isEdit ? 'Edit Permission: ' . $permission->name : 'Create Permissions' }}</h3>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 fs-12 mt-0.5">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted"><i class="ti ti-home-2 me-1"></i>Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('permission') }}" class="text-muted">Permissions</a></li>
                                    <li class="breadcrumb-item active text-primary" aria-current="page">{{ $isEdit ? 'Edit' : 'Create' }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('permission') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 shadow-sm">
                        <i class="ti ti-arrow-left fs-14"></i> Back to Permissions
                    </a>
                </div>
            </div>

            @if ($isEdit)
                {{-- Single Edit Form --}}
                <div class="row justify-content-center">
                    <div class="col-xl-6 col-lg-8">
                        <div class="perm-form-card">
                            <div class="card-header bg-white py-3 px-4 border-bottom">
                                <h5 class="card-title mb-0 fs-15 fw-bold text-dark d-flex align-items-center gap-2">
                                    <i class="ti ti-edit text-primary"></i> Edit Permission Details
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <form method="POST" action="{{ route('permissions.update', $permission->id) }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-3">
                                        <label class="form-label fs-12 fw-bold text-dark mb-1">Permission Name <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="ti ti-key text-muted"></i></span>
                                            <input type="text" name="name" class="form-control border-start-0 font-monospace @error('name') is-invalid @enderror" 
                                                   value="{{ old('name', $permission->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <small class="text-muted fs-11">Recommended format: <code>module.action</code> (e.g. <code>invoice.view</code>, <code>leave.approve</code>).</small>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fs-12 fw-bold text-dark mb-1">Guard Name</label>
                                        <select name="guard_name" class="form-select">
                                            <option value="web" {{ old('guard_name', $permission->guard_name) === 'web' ? 'selected' : '' }}>Web</option>
                                            <option value="client" {{ old('guard_name', $permission->guard_name) === 'client' ? 'selected' : '' }}>Client</option>
                                            <option value="api" {{ old('guard_name', $permission->guard_name) === 'api' ? 'selected' : '' }}>API</option>
                                        </select>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        <a href="{{ route('permission') }}" class="btn btn-light border btn-sm px-3">Cancel</a>
                                        <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                                            <i class="ti ti-check me-1"></i> Update Permission
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- Create Form with Dual Mode (Single & Bulk Generator) --}}
                <div class="row justify-content-center">
                    <div class="col-xl-8 col-lg-10">
                        {{-- Mode Switcher Tabs --}}
                        <ul class="nav nav-pills nav-pills-custom gap-2 mb-3 justify-content-center" id="permCreateTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active d-flex align-items-center gap-1.5" id="bulk-tab" data-bs-toggle="pill" data-bs-target="#bulk-tab-pane" type="button" role="tab">
                                    <i class="ti ti-wand fs-16"></i> Bulk Module Generator (Recommended)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center gap-1.5" id="single-tab" data-bs-toggle="pill" data-bs-target="#single-tab-pane" type="button" role="tab">
                                    <i class="ti ti-plus fs-16"></i> Single Permission
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="permTabContent">
                            {{-- Tab 1: Bulk Module Generator --}}
                            <div class="tab-pane fade show active" id="bulk-tab-pane" role="tabpanel">
                                <div class="perm-form-card">
                                    <div class="card-header bg-white py-3 px-4 border-bottom">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <h5 class="card-title mb-0 fs-15 fw-bold text-dark d-flex align-items-center gap-2">
                                                    <i class="ti ti-wand text-primary"></i> Generate Module Permissions in Bulk
                                                </h5>
                                                <small class="text-muted">Quickly create standard CRUD permissions (view, create, edit, delete, export) for any module.</small>
                                            </div>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill fs-11">
                                                Fast Generator
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body p-4">
                                        <form method="POST" action="{{ route('permission.store') }}">
                                            @csrf
                                            <input type="hidden" name="creation_mode" value="bulk">

                                            <div class="row g-3 mb-3">
                                                <div class="col-md-7">
                                                    <label class="form-label fs-12 fw-bold text-dark mb-1">Module Prefix / Slug <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-end-0"><i class="ti ti-folder text-muted"></i></span>
                                                        <input type="text" name="module_prefix" id="bulkModulePrefix" class="form-control border-start-0 font-monospace" 
                                                               placeholder="e.g. invoice, payroll, project, leave" 
                                                               value="{{ old('module_prefix') }}" required>
                                                    </div>
                                                    <small class="text-muted fs-11">Letters, numbers, underscores only (e.g. <code>invoice</code> generates <code>invoice.view</code>, etc.).</small>
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label fs-12 fw-bold text-dark mb-1">Guard Name</label>
                                                    <select name="guard_name" class="form-select form-select-sm" id="bulkGuardSelect">
                                                        <option value="web" selected>Web (Admin/Staff Portal)</option>
                                                        <option value="client">Client (Client Portal)</option>
                                                        <option value="api">API</option>
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Action Checkbox Chips --}}
                                            <div class="mb-4">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <label class="form-label fs-12 fw-bold text-dark mb-0">Select Standard Actions to Generate <span class="text-danger">*</span></label>
                                                    <button type="button" class="btn btn-link btn-sm p-0 text-primary fs-12 fw-semibold text-decoration-none" id="toggleAllBulkActions">
                                                        Select All
                                                    </button>
                                                </div>
                                                <div class="row g-2">
                                                    @php
                                                        $standardActions = [
                                                            ['key' => 'view', 'label' => 'View / Read', 'icon' => 'ti-eye', 'checked' => true],
                                                            ['key' => 'create', 'label' => 'Create / Add', 'icon' => 'ti-plus', 'checked' => true],
                                                            ['key' => 'edit', 'label' => 'Edit / Update', 'icon' => 'ti-edit', 'checked' => true],
                                                            ['key' => 'delete', 'label' => 'Delete', 'icon' => 'ti-trash', 'checked' => true],
                                                            ['key' => 'export', 'label' => 'Export / Download', 'icon' => 'ti-download', 'checked' => true],
                                                            ['key' => 'approve', 'label' => 'Approve / Reject', 'icon' => 'ti-check', 'checked' => false],
                                                            ['key' => 'manage', 'label' => 'Manage / Full Control', 'icon' => 'ti-settings', 'checked' => false],
                                                        ];
                                                    @endphp
                                                    @foreach ($standardActions as $act)
                                                        <div class="col-sm-4 col-6">
                                                            <label class="action-chip-label {{ $act['checked'] ? 'selected' : '' }}" for="act_{{ $act['key'] }}">
                                                                <input class="form-check-input bulk-act-checkbox m-0" type="checkbox" name="actions[]" 
                                                                       value="{{ $act['key'] }}" id="act_{{ $act['key'] }}" 
                                                                       {{ $act['checked'] ? 'checked' : '' }}>
                                                                <i class="ti {{ $act['icon'] }} text-primary"></i>
                                                                <span>{{ $act['label'] }}</span>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                            {{-- Live Generated Preview --}}
                                            <div class="bg-light p-3 rounded-3 border mb-4">
                                                <span class="text-muted fs-11 fw-bold text-uppercase d-block mb-1.5">Live Generation Preview:</span>
                                                <div id="bulkPreviewBadges" class="d-flex flex-wrap gap-1.5">
                                                    <span class="text-muted fs-12 fst-italic">Type a module prefix above to preview generated permissions...</span>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-end gap-2">
                                                <a href="{{ route('permission') }}" class="btn btn-light border btn-sm px-3">Cancel</a>
                                                <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                                                    <i class="ti ti-wand me-1"></i> Generate & Save Permissions
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Tab 2: Single Permission --}}
                            <div class="tab-pane fade" id="single-tab-pane" role="tabpanel">
                                <div class="perm-form-card">
                                    <div class="card-header bg-white py-3 px-4 border-bottom">
                                        <h5 class="card-title mb-0 fs-15 fw-bold text-dark d-flex align-items-center gap-2">
                                            <i class="ti ti-plus text-primary"></i> Create Single Custom Permission
                                        </h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <form method="POST" action="{{ route('permission.store') }}">
                                            @csrf
                                            <input type="hidden" name="creation_mode" value="single">

                                            <div class="mb-3">
                                                <label class="form-label fs-12 fw-bold text-dark mb-1">Permission Identifier <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-key text-muted"></i></span>
                                                    <input type="text" name="name" class="form-control border-start-0 font-monospace @error('name') is-invalid @enderror" 
                                                           placeholder="e.g. invoice.send_email, report.custom_export" 
                                                           value="{{ old('name') }}" required>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <small class="text-muted fs-11">Recommended: lowercase with dots (e.g. <code>domain.action</code>).</small>
                                            </div>

                                            <div class="mb-4">
                                                <label class="form-label fs-12 fw-bold text-dark mb-1">Guard Name</label>
                                                <select name="guard_name" class="form-select form-select-sm">
                                                    <option value="web" selected>Web</option>
                                                    <option value="client">Client</option>
                                                    <option value="api">API</option>
                                                </select>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-end gap-2">
                                                <a href="{{ route('permission') }}" class="btn btn-light border btn-sm px-3">Cancel</a>
                                                <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                                                    <i class="ti ti-check me-1"></i> Save Permission
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <x-footer />
    </div>
@endsection

@push('after_scripts')
    <script>
        $(document).ready(function() {
            function updateBulkPreview() {
                const prefix = ($('#bulkModulePrefix').val() || '').toLowerCase().trim().replace(/[^a-z0-9_\-]/g, '');
                const selectedActions = [];
                $('.bulk-act-checkbox:checked').each(function() {
                    selectedActions.push($(this).val());
                });

                if (!prefix) {
                    $('#bulkPreviewBadges').html('<span class="text-muted fs-12 fst-italic">Type a module prefix above to preview generated permissions...</span>');
                    return;
                }

                if (selectedActions.length === 0) {
                    $('#bulkPreviewBadges').html('<span class="text-warning fs-12">Please select at least one action above.</span>');
                    return;
                }

                let badgesHtml = '';
                selectedActions.forEach(function(act) {
                    badgesHtml += `<span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11 font-monospace px-2 py-1"><i class="ti ti-key me-1"></i>${prefix}.${act}</span>`;
                });
                $('#bulkPreviewBadges').html(badgesHtml);
            }

            $('#bulkModulePrefix').on('keyup input', updateBulkPreview);

            $('.bulk-act-checkbox').on('change', function() {
                if ($(this).is(':checked')) {
                    $(this).closest('.action-chip-label').addClass('selected');
                } else {
                    $(this).closest('.action-chip-label').removeClass('selected');
                }
                updateBulkPreview();
            });

            let allBulkSelected = true;
            $('#toggleAllBulkActions').on('click', function() {
                allBulkSelected = !allBulkSelected;
                $('.bulk-act-checkbox').prop('checked', allBulkSelected);
                $('.action-chip-label').toggleClass('selected', allBulkSelected);
                $(this).text(allBulkSelected ? 'Deselect All' : 'Select All');
                updateBulkPreview();
            });

            updateBulkPreview();
        });
    </script>
@endpush


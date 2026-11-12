@extends('client.layout.layout')
@section('title', $title)

@push('styles')
<style>
    .ticket-create-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        transition: all 0.2s ease;
    }
    .ticket-section-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: #1e293b;
        letter-spacing: 0.02em;
        margin-bottom: 0.85rem;
        display: flex;
        align-items: center;
    }
    .ticket-section-title i {
        font-size: 1.15rem;
        margin-right: 0.5rem;
    }
    .section-divider {
        border-top: 1px dashed #e2e8f0;
        margin: 1.5rem 0;
    }
    /* Priority Radio Cards */
    .priority-options-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.75rem;
    }
    @media (max-width: 768px) {
        .priority-options-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 480px) {
        .priority-options-grid {
            grid-template-columns: 1fr;
        }
    }
    .priority-card-label {
        margin-bottom: 0;
        cursor: pointer;
        display: block;
        height: 100%;
    }
    .priority-card-label input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .priority-box {
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.85rem 0.75rem;
        text-align: center;
        transition: all 0.2s ease-in-out;
        background: #f8fafc;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .priority-box:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .priority-box .priority-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.35rem;
        font-size: 1rem;
        transition: all 0.2s ease;
    }
    .priority-box .priority-name {
        font-weight: 600;
        font-size: 0.85rem;
        margin-bottom: 0.15rem;
        color: #334155;
    }
    .priority-box .priority-desc {
        font-size: 0.7rem;
        color: #64748b;
        line-height: 1.2;
    }
    
    /* Low Priority */
    .priority-box.priority-low .priority-icon { background: #dcfce7; color: #16a34a; }
    .priority-card-label input[type="radio"]:checked + .priority-box.priority-low {
        border-color: #16a34a;
        background: #f0fdf4;
        box-shadow: 0 0 0 1px #16a34a, 0 4px 10px rgba(22, 163, 74, 0.12);
    }
    .priority-card-label input[type="radio"]:checked + .priority-box.priority-low .priority-name {
        color: #15803d;
    }

    /* Medium Priority */
    .priority-box.priority-medium .priority-icon { background: #e0f2fe; color: #0284c7; }
    .priority-card-label input[type="radio"]:checked + .priority-box.priority-medium {
        border-color: #0284c7;
        background: #f0f9ff;
        box-shadow: 0 0 0 1px #0284c7, 0 4px 10px rgba(2, 132, 199, 0.12);
    }
    .priority-card-label input[type="radio"]:checked + .priority-box.priority-medium .priority-name {
        color: #0369a1;
    }

    /* High Priority */
    .priority-box.priority-high .priority-icon { background: #fef3c7; color: #d97706; }
    .priority-card-label input[type="radio"]:checked + .priority-box.priority-high {
        border-color: #d97706;
        background: #fffbeb;
        box-shadow: 0 0 0 1px #d97706, 0 4px 10px rgba(217, 119, 6, 0.12);
    }
    .priority-card-label input[type="radio"]:checked + .priority-box.priority-high .priority-name {
        color: #b45309;
    }

    /* Critical Priority */
    .priority-box.priority-critical .priority-icon { background: #fee2e2; color: #dc2626; }
    .priority-card-label input[type="radio"]:checked + .priority-box.priority-critical {
        border-color: #dc2626;
        background: #fef2f2;
        box-shadow: 0 0 0 1px #dc2626, 0 4px 10px rgba(220, 38, 38, 0.12);
    }
    .priority-card-label input[type="radio"]:checked + .priority-box.priority-critical .priority-name {
        color: #b91c1c;
    }

    /* Dropzone Upload Box */
    .attachment-dropzone {
        border: 2px dashed #cbd5e1;
        background-color: #f8fafc;
        border-radius: 12px;
        padding: 1.5rem 1.25rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }
    .attachment-dropzone:hover,
    .attachment-dropzone.dragover {
        background-color: #f1f5f9;
        border-color: #3b82f6;
    }
    .attachment-dropzone .upload-icon {
        font-size: 2rem;
        color: #64748b;
        margin-bottom: 0.5rem;
        transition: transform 0.2s ease;
    }
    .attachment-dropzone:hover .upload-icon {
        transform: translateY(-3px);
        color: #3b82f6;
    }
    .file-preview-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        margin-top: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Form Floating Labels & Controls */
    .form-control-custom {
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.65rem 0.9rem;
        font-size: 0.92rem;
        transition: all 0.2s ease;
        background-color: #fff;
    }
    .form-control-custom:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        outline: none;
    }
    .input-group-custom .input-group-text {
        border: 1.5px solid #e2e8f0;
        border-right: none;
        background-color: #f8fafc;
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
        color: #64748b;
    }
    .input-group-custom .form-control-custom {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
    
    /* Quick templates chips */
    .template-chip {
        display: inline-block;
        font-size: 0.75rem;
        padding: 0.25rem 0.6rem;
        border-radius: 20px;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        margin-right: 0.35rem;
        margin-bottom: 0.35rem;
        transition: all 0.15s ease;
    }
    .template-chip:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    /* Sidebar info cards */
    .sidebar-info-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
    }
    .sla-pill {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
    }
</style>
@endpush

@section('main-section')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <!-- Header & Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
                <div class="my-auto mb-2 mb-md-0">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-md rounded-circle bg-primary-transparent text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-ticket fs-20"></i>
                        </span>
                        <div>
                            <h3 class="mb-1 text-dark fw-bold">{{ $title }}</h3>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 fs-12">
                                    <li class="breadcrumb-item"><a href="{{ route('client.dashboard') }}" class="text-muted"><i class="ti ti-home-2 me-1"></i>Client Portal</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('client.tickets.index') }}" class="text-muted">Tickets</a></li>
                                    <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Create</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('client.tickets.index') }}" class="btn btn-outline-secondary d-flex align-items-center px-3 py-2">
                        <i class="ti ti-arrow-left me-1"></i>Back to Tickets
                    </a>
                </div>
            </div>

            <!-- Error Alerts -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="ti ti-alert-circle fs-20 me-2 mt-1"></i>
                        <div>
                            <h6 class="alert-heading fw-bold mb-1">Please correct the errors below:</h6>
                            <ul class="mb-0 ps-3 fs-13">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row g-4">
                <!-- Main Form Column -->
                <div class="col-lg-8">
                    <div class="card ticket-create-card mb-4">
                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="card-title mb-0 fw-bold text-dark">Ticket Submission Form</h5>
                                <p class="text-muted fs-12 mb-0 mt-0">Fill in the details below to notify our technical & project support team.</p>
                            </div>
                            <span class="badge bg-light text-primary border px-2 py-1 fs-12"><i class="ti ti-shield-lock me-1"></i>Secure Request</span>
                        </div>

                        <form action="{{ route('client.tickets.store') }}" method="POST" enctype="multipart/form-data" id="ticketCreateForm">
                            @csrf
                            <div class="card-body p-4">

                                <!-- SECTION 1: PROJECT SELECTION -->
                                <div class="ticket-section-title">
                                    <i class="ti ti-folder text-primary"></i> 1. Project Information
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-13 text-dark mb-1">
                                            Assigned Project <span class="text-muted fw-normal">(Select if listed)</span>
                                        </label>
                                        <div class="input-group input-group-custom">
                                            <span class="input-group-text"><i class="ti ti-layout-grid"></i></span>
                                            <select name="project_id" id="projectIdSelect" class="form-select form-control-custom">
                                                <option value="">-- Choose from your active projects --</option>
                                                @foreach ($projects as $project)
                                                    <option value="{{ $project->id }}" @selected(old('project_id') == $project->id)>
                                                        {{ $project->project_name }} @if(!empty($project->project_category))({{ $project->project_category }})@endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if ($projects->isEmpty())
                                            <div class="text-warning fs-12 mt-1">
                                                <i class="ti ti-info-circle me-1"></i>No linked active projects found. Please enter project name below.
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-13 text-dark mb-1">
                                            Custom Project Name <span class="text-muted fw-normal">(If unlisted or general)</span>
                                        </label>
                                        <div class="input-group input-group-custom">
                                            <span class="input-group-text"><i class="ti ti-edit"></i></span>
                                            <input type="text" 
                                                   name="project_name" 
                                                   id="projectNameInput"
                                                   value="{{ old('project_name') }}" 
                                                   class="form-control form-control-custom" 
                                                   placeholder="e.g. Mobile App Redesign / Website Maintenance">
                                        </div>
                                    </div>
                                </div>

                                <div class="section-divider"></div>

                                <!-- SECTION 2: ISSUE DETAILS & PRIORITY -->
                                <div class="ticket-section-title">
                                    <i class="ti ti-file-alert text-primary"></i> 2. Issue Summary & Urgency
                                </div>

                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <label class="form-label fw-semibold fs-13 text-dark mb-0">
                                                Subject <span class="text-danger">*</span>
                                            </label>
                                            <span class="fs-12 text-muted" id="subjectCharCount">0 / 255</span>
                                        </div>
                                        <div class="input-group input-group-custom">
                                            <span class="input-group-text"><i class="ti ti-message-2"></i></span>
                                            <input type="text" 
                                                   name="subject" 
                                                   id="subjectInput"
                                                   value="{{ old('subject') }}" 
                                                   class="form-control form-control-custom" 
                                                   placeholder="Brief headline of the problem or task (e.g., API returning 500 error on checkout)" 
                                                   maxlength="255"
                                                   required>
                                        </div>
                                    </div>

                                    <!-- Priority Selector Radio Cards -->
                                    <div class="col-12 mt-3">
                                        <label class="form-label fw-semibold fs-13 text-dark mb-2">
                                            Select Priority Level <span class="text-danger">*</span>
                                        </label>
                                        
                                        <div class="priority-options-grid">
                                            <!-- Low Priority -->
                                            <label class="priority-card-label">
                                                <input type="radio" name="priority" value="Low" @checked(old('priority') === 'Low') required>
                                                <div class="priority-box priority-low">
                                                    <div class="priority-icon">
                                                        <i class="ti ti-clock"></i>
                                                    </div>
                                                    <div class="priority-name">Low</div>
                                                    <div class="priority-desc">Minor tweak / inquiry</div>
                                                </div>
                                            </label>

                                            <!-- Medium Priority (Default) -->
                                            <label class="priority-card-label">
                                                <input type="radio" name="priority" value="Medium" @checked(old('priority', 'Medium') === 'Medium') required>
                                                <div class="priority-box priority-medium">
                                                    <div class="priority-icon">
                                                        <i class="ti ti-info-circle"></i>
                                                    </div>
                                                    <div class="priority-name">Medium</div>
                                                    <div class="priority-desc">Normal bug or task</div>
                                                </div>
                                            </label>

                                            <!-- High Priority -->
                                            <label class="priority-card-label">
                                                <input type="radio" name="priority" value="High" @checked(old('priority') === 'High') required>
                                                <div class="priority-box priority-high">
                                                    <div class="priority-icon">
                                                        <i class="ti ti-alert-triangle"></i>
                                                    </div>
                                                    <div class="priority-name">High</div>
                                                    <div class="priority-desc">Major feature broken</div>
                                                </div>
                                            </label>

                                            <!-- Critical Priority -->
                                            <label class="priority-card-label">
                                                <input type="radio" name="priority" value="Critical" @checked(old('priority') === 'Critical') required>
                                                <div class="priority-box priority-critical">
                                                    <div class="priority-icon">
                                                        <i class="ti ti-flame"></i>
                                                    </div>
                                                    <div class="priority-name">Critical</div>
                                                    <div class="priority-desc">System / blocker down</div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="section-divider"></div>

                                <!-- SECTION 3: DESCRIPTION & TEMPLATES -->
                                <div class="ticket-section-title">
                                    <i class="ti ti-file-text text-primary"></i> 3. Detailed Description
                                </div>

                                <div class="mb-2 d-flex flex-wrap align-items-center justify-content-between gap-2">
                                    <label class="form-label fw-semibold fs-13 text-dark mb-0">
                                        Describe the issue in detail <span class="text-danger">*</span>
                                    </label>
                                    <div class="d-flex align-items-center">
                                        <span class="fs-12 text-muted me-2">Quick helpers:</span>
                                        <button type="button" class="template-chip" onclick="insertTemplate('bug')">
                                            <i class="ti ti-bug me-1"></i>Bug Report
                                        </button>
                                        <button type="button" class="template-chip" onclick="insertTemplate('feature')">
                                            <i class="ti ti-sparkles me-1"></i>Change Request
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <textarea name="description" 
                                              id="descriptionTextarea" 
                                              rows="7" 
                                              class="form-control form-control-custom" 
                                              placeholder="Please provide comprehensive information about what happened, steps to reproduce, affected URLs or users, and the expected outcome..." 
                                              maxlength="5000"
                                              required>{{ old('description') }}</textarea>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <span class="fs-12 text-muted">Markdown or plain text supported.</span>
                                        <span class="fs-12 text-muted" id="descCharCount">0 / 5000 characters</span>
                                    </div>
                                </div>

                                <div class="section-divider"></div>

                                <!-- SECTION 4: FILE ATTACHMENT -->
                                <div class="ticket-section-title">
                                    <i class="ti ti-paperclip text-primary"></i> 4. Supporting Attachment
                                </div>

                                <div class="attachment-dropzone" id="dropzoneArea" onclick="document.getElementById('attachmentInput').click()">
                                    <input type="file" 
                                           name="attachment" 
                                           id="attachmentInput" 
                                           class="d-none" 
                                           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.txt"
                                           onchange="handleFileSelect(this)">
                                    <div id="dropzonePrompt">
                                        <div class="upload-icon">
                                            <i class="ti ti-cloud-upload"></i>
                                        </div>
                                        <h6 class="fs-14 fw-bold text-dark mb-1">Click to browse or drag & drop file here</h6>
                                        <p class="text-muted fs-12 mb-2">Attach screenshots, error logs, or relevant documents to expedite diagnosis.</p>
                                        <div class="d-flex flex-wrap justify-content-center gap-1">
                                            <span class="badge bg-white text-secondary border">PNG, JPG</span>
                                            <span class="badge bg-white text-secondary border">PDF</span>
                                            <span class="badge bg-white text-secondary border">DOC, DOCX</span>
                                            <span class="badge bg-white text-secondary border">XLS, XLSX</span>
                                            <span class="badge bg-white text-secondary border">TXT</span>
                                            <span class="badge bg-light text-muted border">Max 4 MB</span>
                                        </div>
                                    </div>

                                    <!-- Selected File Preview -->
                                    <div id="filePreview" class="file-preview-card d-none" onclick="event.stopPropagation()">
                                        <div class="d-flex align-items-center gap-2 text-start">
                                            <span class="avatar avatar-sm rounded bg-primary-transparent text-primary d-inline-flex align-items-center justify-content-center">
                                                <i class="ti ti-file fs-16" id="previewIcon"></i>
                                            </span>
                                            <div>
                                                <p class="fs-13 fw-semibold text-dark mb-0 text-truncate" id="fileName" style="max-width: 280px;"></p>
                                                <span class="fs-12 text-muted" id="fileSize"></span>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeAttachment(event)">
                                            <i class="ti ti-trash me-1"></i>Remove
                                        </button>
                                    </div>
                                </div>

                            </div>

                            <!-- Card Footer Action Bar -->
                            <div class="card-footer bg-light border-top px-4 py-3 d-flex align-items-center justify-content-between">
                                <a href="{{ route('client.tickets.index') }}" class="btn btn-outline-secondary px-4 py-2">
                                    <i class="ti ti-x me-1"></i>Cancel
                                </a>
                                <button type="submit" id="submitBtn" class="btn btn-primary d-flex align-items-center px-4 py-2 fw-semibold">
                                    <i class="ti ti-send me-2"></i>Submit Ticket
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right Sidebar: Guidance & Support Info -->
                <div class="col-lg-4">
                    <!-- Guidelines Card -->
                    <div class="card sidebar-info-card shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="card-title mb-0 fw-bold d-flex align-items-center text-dark">
                                <i class="ti ti-bulb text-warning me-2 fs-18"></i>Tips for Faster Resolution
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <ul class="list-unstyled mb-0 fs-13 text-muted d-flex flex-column gap-3">
                                <li class="d-flex align-items-start gap-2">
                                    <i class="ti ti-check text-success fs-16 mt-1 flex-shrink-0"></i>
                                    <div>
                                        <strong class="text-dark">Be specific:</strong> Include step-by-step actions that lead to the problem.
                                    </div>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <i class="ti ti-check text-success fs-16 mt-1 flex-shrink-0"></i>
                                    <div>
                                        <strong class="text-dark">Attach evidence:</strong> Screenshots or screen recordings save hours of back-and-forth.
                                    </div>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <i class="ti ti-check text-success fs-16 mt-1 flex-shrink-0"></i>
                                    <div>
                                        <strong class="text-dark">Set accurate priority:</strong> Mark 'Critical' only for full system or production outages.
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- SLA / Priority Response Time Card -->
                    <div class="card sidebar-info-card shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="card-title mb-0 fw-bold d-flex align-items-center text-dark">
                                <i class="ti ti-hourglass-high text-info me-2 fs-18"></i>Estimated Response Times
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-danger-transparent text-danger sla-pill">Critical</span>
                                        <span class="fs-12 text-dark">Outages / Blockers</span>
                                    </div>
                                    <span class="fs-12 fw-bold text-danger">&lt; 2 - 4 Hrs</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-warning-transparent text-warning sla-pill">High</span>
                                        <span class="fs-12 text-dark">Major Issues</span>
                                    </div>
                                    <span class="fs-12 fw-bold text-warning">&lt; 8 Hrs</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-info-transparent text-info sla-pill">Medium</span>
                                        <span class="fs-12 text-dark">Standard Requests</span>
                                    </div>
                                    <span class="fs-12 fw-bold text-info">&lt; 24 Hrs</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-success-transparent text-success sla-pill">Low</span>
                                        <span class="fs-12 text-dark">Minor Queries</span>
                                    </div>
                                    <span class="fs-12 fw-bold text-success">&lt; 48 Hrs</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Direct Help Box -->
                    <div class="card border-0 rounded-3 text-white" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="avatar avatar-sm rounded-circle bg-white text-dark d-inline-flex align-items-center justify-content-center">
                                    <i class="ti ti-headset fs-16"></i>
                                </span>
                                <h6 class="text-white mb-0 fw-bold">Need Immediate Help?</h6>
                            </div>
                            <p class="fs-12 text-white-50 mb-3">If this is a critical production emergency, you can also reach out to your dedicated account manager directly.</p>
                            <a href="{{ route('client.dashboard') }}" class="btn btn-sm btn-light w-100 fw-semibold text-dark">
                                <i class="ti ti-dashboard me-1"></i>Go to Client Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after_scripts')
<script>
    // Live Character Counters
    const subjectInput = document.getElementById('subjectInput');
    const subjectCount = document.getElementById('subjectCharCount');
    if (subjectInput && subjectCount) {
        const updateSubjectCount = () => {
            subjectCount.textContent = `${subjectInput.value.length} / 255`;
        };
        subjectInput.addEventListener('input', updateSubjectCount);
        updateSubjectCount();
    }

    const descTextarea = document.getElementById('descriptionTextarea');
    const descCount = document.getElementById('descCharCount');
    if (descTextarea && descCount) {
        const updateDescCount = () => {
            descCount.textContent = `${descTextarea.value.length} / 5000 characters`;
        };
        descTextarea.addEventListener('input', updateDescCount);
        updateDescCount();
    }

    // Quick Templates
    function insertTemplate(type) {
        if (!descTextarea) return;
        let template = '';
        if (type === 'bug') {
            template = `1. Steps to Reproduce:\n- Step 1:\n- Step 2:\n- Step 3:\n\n2. Expected Behavior:\n\n3. Actual Behavior:\n\n4. Relevant URL or Page:\n`;
        } else if (type === 'feature') {
            template = `1. Purpose / Goal:\n\n2. Specific Changes Requested:\n\n3. Target Audience / Module:\n`;
        }

        if (descTextarea.value.trim() !== '') {
            if (confirm('Replace current description with this template format?')) {
                descTextarea.value = template;
            }
        } else {
            descTextarea.value = template;
        }
        descTextarea.focus();
        if (descCount) descCount.textContent = `${descTextarea.value.length} / 5000 characters`;
    }

    // Drag & Drop / File Upload Handler
    const dropzoneArea = document.getElementById('dropzoneArea');
    const attachmentInput = document.getElementById('attachmentInput');
    const dropzonePrompt = document.getElementById('dropzonePrompt');
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const previewIcon = document.getElementById('previewIcon');

    if (dropzoneArea) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzoneArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzoneArea.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzoneArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzoneArea.classList.remove('dragover');
            }, false);
        });

        dropzoneArea.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files.length > 0) {
                attachmentInput.files = files;
                handleFileSelect(attachmentInput);
            }
        });
    }

    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    function handleFileSelect(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const maxBytes = 4 * 1024 * 1024; // 4MB

            if (file.size > maxBytes) {
                alert('File size exceeds the 4 MB limit. Please select a smaller file.');
                input.value = '';
                return;
            }

            fileName.textContent = file.name;
            fileSize.textContent = formatBytes(file.size);

            // Icon update based on extension
            const ext = file.name.split('.').pop().toLowerCase();
            if (['png', 'jpg', 'jpeg'].includes(ext)) {
                previewIcon.className = 'ti ti-photo fs-16';
            } else if (ext === 'pdf') {
                previewIcon.className = 'ti ti-file-text fs-16';
            } else if (['doc', 'docx'].includes(ext)) {
                previewIcon.className = 'ti ti-file-word fs-16';
            } else if (['xls', 'xlsx'].includes(ext)) {
                previewIcon.className = 'ti ti-file-spreadsheet fs-16';
            } else {
                previewIcon.className = 'ti ti-file fs-16';
            }

            dropzonePrompt.classList.add('d-none');
            filePreview.classList.remove('d-none');
        }
    }

    function removeAttachment(e) {
        if (e) e.stopPropagation();
        if (attachmentInput) attachmentInput.value = '';
        if (filePreview) filePreview.classList.add('d-none');
        if (dropzonePrompt) dropzonePrompt.classList.remove('d-none');
    }

    // Submit Button Loading state
    const form = document.getElementById('ticketCreateForm');
    const submitBtn = document.getElementById('submitBtn');
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            if (form.checkValidity()) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Submitting...';
            }
        });
    }
</script>
@endpush

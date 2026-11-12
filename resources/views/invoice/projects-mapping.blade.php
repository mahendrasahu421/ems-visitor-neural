@extends('layout.master')
@section('title', $title)

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <div class="page-wrapper">
        <div class="content">
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div>
                    <h2 class="mb-1">{{ $title }}</h2>
                    <div class="text-muted small">Select a project and map the employees who will work on it.</div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('invoice.projects') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-list me-1"></i>Project List
                    </a>
                    <a href="{{ route('invoice.projects.create') }}" class="btn btn-primary">
                        <i class="ti ti-circle-plus me-1"></i>Add Project
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Project</th>
                                    <th>Client</th>
                                    <th>Work Type</th>
                                    <th>Duration</th>
                                    <th>Mapped Employees</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($projects as $project)
                                    <tr>
                                        <td>{{ $project->project_id }}</td>
                                        <td><strong>{{ $project->project_name }}</strong></td>
                                        <td>{{ $project->ClientName ?? '-' }}</td>
                                        <td>{{ $project->work_type_display ?? '-' }}</td>
                                        <td>
                                            {{ $project->project_start_date ? date('d-m-Y', strtotime($project->project_start_date)) : '-' }}
                                            -
                                            {{ $project->project_end_date ? date('d-m-Y', strtotime($project->project_end_date)) : '-' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ (int) $project->team_count > 0 ? 'success' : 'warning' }}">
                                                {{ (int) $project->team_count }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('invoice.projects.employees', $project->project_id) }}" class="btn btn-sm btn-outline-success" title="Map Employees">
                                                    <i class="ti ti-users"></i>
                                                </a>
                                                <a href="{{ route('invoice.projects.show', $project->project_id) }}" class="btn btn-sm btn-outline-primary" title="View Project">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No projects found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <x-footer />
    </div>
@endsection

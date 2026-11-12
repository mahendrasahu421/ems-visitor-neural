@extends('layout.master')
@section('title', $title)

@section('main-section')

    <style>
        .dash-widget {
            background: #fff;
            padding: 18px 20px;
            border-radius: 8px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.06);
            border: 1px solid #eef2f7;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }

        .dash-widget:hover {
            transform: translateY(-1px);
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.12);
        }

        .dash-widget-info span {
            font-size: 14px;
            color: #6c757d;
        }

        .dash-widget-info h3 {
            margin-top: 5px;
            color: #1b2559;
            font-weight: 600;
        }

        .dash-widget-icon {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 20px;
        }

        .bg-orange {
            background: #ff6b00 !important;
        }

        .total_jobs {
            color: #ff6b00 !important;
        }
    </style>


    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <!-- Confirmation Modal -->
    <div class="modal fade" id="copyConfirmModal" tabindex="-1" aria-labelledby="copyConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="copyConfirmModalLabel">Confirm Copy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to duplicate this job? A copy of this job will be created.
                    <div id="rowPreview" class="mt-2 text-muted" style="font-size: 0.9em;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-outline-primary btn-sm me-2" id="reviewRow">Review</button>
                    <button type="button" class="btn btn-outline-primary btn-sm me-2" id="confirmCopy">Yes</button>
                </div>

            </div>
        </div>
    </div>


    <div class="page-wrapper">
        <div class="content">

            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">{{ $title }}</h2>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                    <div class="mb-2">
                        <a href="{{ route('recruitment.jobs.create') }}" class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-circle-plus me-2"></i>Add Jobs
                        </a>
                    </div>
                </div>
            </div>
            <!-- /Breadcrumb -->

            <!-- Job Cards -->
            <div class="row">
                <!-- Total Jobs -->
                <div class="col-md-4">
                    <div class="dash-widget">
                        <div class="dash-widget-info">
                            <span>Total Jobs</span>
                            <h3 class="total_jobs">120</h3>
                        </div>
                        <div class="dash-widget-icon bg-orange">
                            <i class="fa-solid fa-briefcase"></i>
                        </div>
                    </div>
                </div>

                <!-- Published Jobs -->
                <div class="col-md-4">
                    <div class="dash-widget">
                        <div class="dash-widget-info">
                            <span>Published Jobs</span>
                            <h3 class="text-success">80</h3>
                        </div>
                        <div class="dash-widget-icon bg-success">
                            <i class="fa-solid fa-check"></i>
                        </div>
                    </div>
                </div>

                <!-- Unpublished Jobs -->
                <div class="col-md-4">
                    <div class="dash-widget">
                        <div class="dash-widget-info">
                            <span>Unpublished Jobs</span>
                            <h3 class="text-danger">40</h3>
                        </div>
                        <div class="dash-widget-icon bg-danger">
                            <i class="fa-solid fa-ban"></i>
                        </div>
                    </div>
                </div>


            </div>

            <!-- /Job Cards -->


            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h4 class="card-title">Jobs List</h4>

                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table datatable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Date of Publish</th>
                                            <th>Job Title</th>
                                            <th>Functional Area</th>
                                            <th>State</th>
                                            <th>City</th>
                                            <th>Experience</th>
                                            <th>Status</th>
                                            <th>Share</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr>
                                            <td>12 Nov 2025</td>
                                            <td>Senior Developer</td>
                                            <td>IT / Software</td>
                                            <td>Maharashtra</td>
                                            <td>Mumbai</td>
                                            <td>3-5 Years</td>
                                            <td>
                                                <span class="badge badge-success d-inline-flex align-items-center badge-sm">
                                                    <i class="ti ti-point-filled me-1"></i>Active
                                                </span>
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-primary"><i
                                                        class="ti ti-share-2"></i></a>
                                                <button class="btn btn-sm btn-primary copy-btn"><i class="ti ti-copy"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>10 Nov 2025</td>
                                            <td>UI/UX Designer</td>
                                            <td>Design</td>
                                            <td>Maharashtra</td>
                                            <td>Pune</td>
                                            <td>2-4 Years</td>
                                            <td>
                                                <span class="badge badge-danger d-inline-flex align-items-center badge-sm">
                                                    <i class="ti ti-point-filled me-1"></i>Inactive
                                                </span>
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-primary"><i
                                                        class="ti ti-share-2"></i></a>
                                                <button class="btn btn-sm btn-warning copy-btn"><i class="ti ti-copy"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>



                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <x-footer />
    </div>


@endsection

@push('after_scripts')
    <script>
        let rowToCopy = null;

        document.querySelectorAll('.copy-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                rowToCopy = this.closest('tr'); // Store the row reference
                const copyModal = new bootstrap.Modal(document.getElementById('copyConfirmModal'));
                copyModal.show();
            });
        });

        document.getElementById('confirmCopy').addEventListener('click', function() {
            if (rowToCopy) {
                let rowData = Array.from(rowToCopy.querySelectorAll('td'))
                    .slice(0, -1) // exclude actions column
                    .map(td => td.innerText.trim())
                    .join('\t'); // tab-separated

                navigator.clipboard.writeText(rowData)
                    .then(() => {
                        alert('Row data copied to clipboard!');
                    })
                    .catch(err => {
                        alert('Failed to copy: ' + err);
                    });

                rowToCopy = null;
                const copyModalEl = document.getElementById('copyConfirmModal');
                const copyModal = bootstrap.Modal.getInstance(copyModalEl);
                copyModal.hide();
            }
        });
    </script>
@endpush

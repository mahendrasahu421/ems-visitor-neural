@extends('layout.master')
@section('title', $title)

@section('main-section')
    <style>
        .filter-row {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: nowrap; 
        }

        #jobFilter {
            max-width: 250px;
        }
    </style>

    <div class="page-wrapper">
        <div class="content">

            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">{{ $title }}</h2>
                </div>
            </div> 
            <!-- /Breadcrumb -->

            <div class="card mb-3">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="filter-row">

                                <label class="form-label mb-0 fw-bold">Filter by Job Title</label>

                                <select id="jobFilter" class="form-select">
                                    <option value="">All Jobs</option>
                                    <option value="Senior Developer">Senior Developer</option>
                                    <option value="UI/UX Designer">UI/UX Designer</option>
                                </select>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- /Filter -->

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h4 class="card-title">Applied Candidate List</h4>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table datatable" id="candidateTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Job Title</th>
                                            <th>City</th>
                                            <th>Profile</th>
                                            <th>Candidate Name</th>
                                            <th>Date</th>
                                            <th>Onboarding</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr>
                                            <td>Senior Developer</td>
                                            <td>Mumbai</td>
                                            <td>IT / Software</td>
                                            <td>Rohit Sharma</td>
                                            <td>21 Nov 2025</td>
                                            <td><a href="{{ route('employee.onboarding') }}" class="btn btn-sm btn-primary">Onboarding</a></td>
                                        </tr>

                                        <tr>
                                            <td>UI/UX Designer</td>
                                            <td>Pune</td>
                                            <td>Design</td>
                                            <td>Neha Verma</td>
                                            <td>20 Nov 2025</td>
                                            <td><a href="{{ route('employee.onboarding') }}" class="btn btn-sm btn-primary">Onboarding</a></td>
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
        $('#jobFilter').on('change', function() {
            var selectedJob = $(this).val().toLowerCase();

            $('#candidateTable tbody tr').filter(function() {
                var jobTitle = $(this).find('td:nth-child(1)').text().toLowerCase();

                if (selectedJob === "" || jobTitle === selectedJob) {
                    $(this).show();
                } else { 
                    $(this).hide(); 
                }
            });
        });
    </script>
@endpush

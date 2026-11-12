@extends('employee.layout.layout')
@section('title', $title)
@section('main-section')
    <style>
        .table td {
            padding: 8px 8px !important;
        }
        .table th {
            padding: 8px 8px !important;
        }
    </style>
    <x-alert-modal />

    <div class="page-wrapper" style="min-height: 428px;">
        <div class="content">

            <h4 class="mb-2">You are controlling officer for 1 Employees.</h4>
            <h5 class="mb-4 text-muted">आप 1 कर्मचारियों के नियंत्रण अधिकारी हैं|</h5>
            <div class="card mt-3">
                <div class="card-body p-0">
                    <div class="custom-datatable-filter table-responsive">
                        <table class="table datatable">
                            <thead class="thead-light">
                                <tr>
                                    <th>S.No.</th>
                                    <th>Profile Image</th>
                                    <th>Emp.ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile Number</th>
                                    <th>Designation</th>
                                    <th>Review Assessment</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td><img src="" alt="User Profile Picture"></td>
                                    <td><p>PAC001340</p></td>
                                    <td>Pravish Pandey</td>
                                    <td>pravishkumar774@gmail.com</td>
                                    <td>9631555911</td>
                                    <td>Senior Branch Manager</td>
                                    <td>Completed</td>
                                </tr>

                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

        </div>

        <x-footer />
    </div>

@endsection
@push('after_scripts')
    <script></script>
@endpush

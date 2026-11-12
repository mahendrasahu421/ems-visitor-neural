@extends('employee.layout.layout')
@section('title', $title)
@section('main-section')
    <style>
        .custom-card {
            border-radius: 15px;
            min-height: 150px;
            background: #ffffff;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            padding: 20px 15px;
        }

        .custom-card:hover {
            box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;
        }

        .card-icon {
            width: 70px;
            height: 70px;
            margin-bottom: 12px;
            transition: transform 0.3s ease;
        }

        .custom-card:hover .card-icon {
            transform: scale(1.1);
        }

        .badge-custom {
            background: linear-gradient(135deg, #f37438, #ff7f50);
            color: #fff;
            font-weight: 500;
            padding: 6px 16px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .badge-custom:hover {
            background: linear-gradient(135deg, #ff7f50, #f37438);
            transform: scale(1.05);
            color: #fff;
        }

        h4 span {
            font-weight: 400;
        }

        @media(max-width: 767px){
            .custom-card {
                min-height: 140px;
            }
            .card-icon {
                width: 60px;
                height: 60px;
            }
        }
    </style>

    <x-alert-modal />

    <div class="page-wrapper" style="min-height: 428px;">
        <div class="content">

            <h4 class="mb-2">Welcome Rana Pratap to ACFL Performance Management Process / 
                <span class="mb-4 text-muted">एसीएफएल प्रदर्शन प्रबंधन प्रक्रिया में आप का स्वागत है।</span>
            </h4>

            <div class="row mt-4 justify-content-start">
                <!-- Card 1 -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="custom-card text-center">
                        <img src="{{ asset('frontent/assets/img/SelfAssessment.png') }}" class="card-icon" alt="">
                        <a href="{{ route('employee.competencies') }}" class="badge-custom">Start Self Appraisal</a>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="custom-card text-center">
                        <img src="{{ asset('frontent/assets/img/ControlingReview.png') }}" class="card-icon" alt="">
                        <a href="{{ route('employee.review.employee') }}" class="badge-custom">Review your Employee</a>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="custom-card text-center">
                        <img src="{{ asset('frontent/assets/img/GiveFeedback.png') }}" class="card-icon" alt="">
                        <a href="{{ route('employee.view-feedback') }}" class="badge-custom">View your CO Feedback</a>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="custom-card text-center">
                        <img src="{{ asset('frontent/assets/img/SubmitFeedback.png') }}" class="card-icon" alt="">
                        <a href="{{ route('employee.feedback') }}" class="badge-custom">Submit your Feedback</a>
                    </div>
                </div>
            </div>

            <div>
                <h5 class="mb-2">Previous years details</h5>
                <h6 class="text-muted">पिछले वर्षों का विवरण</h6>
            </div>

            <div class="card mt-3">
                <div class="card-body p-0">
                    <div class="custom-datatable-filter table-responsive">
                        <table class="table datatable">
                            <thead class="thead-light">
                                <tr>
                                    <th>S.No.</th>
                                    <th>FY</th>
                                    <th>CO Name</th>
                                    <th>Monthly CTC</th>
                                    <th>Training Suggested</th>
                                    <th>Rating</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>2023–24</td>
                                    <td>
                                        <h6 class="fw-medium"><a href="#">Rohit Sharma</a></h6>
                                    </td>
                                    <td>₹45,000</td>
                                    <td>Leadership Training</td>
                                    <td>
                                        <span class="badge badge-success d-inline-flex align-items-center badge-sm">
                                            <i class="ti ti-point-filled me-1"></i>4.5
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>2022–23</td>
                                    <td>
                                        <h6 class="fw-medium"><a href="#">Anil Kumar</a></h6>
                                    </td>
                                    <td>₹40,000</td>
                                    <td>Technical Upskilling</td>
                                    <td>
                                        <span class="badge badge-danger d-inline-flex align-items-center badge-sm">
                                            <i class="ti ti-point-filled me-1"></i>3.8
                                        </span>
                                    </td>
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

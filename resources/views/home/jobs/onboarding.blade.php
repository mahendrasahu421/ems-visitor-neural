@extends('layout.master')
@section('title', $title)

@section('main-section')
    <style>
        .candidate-card {
            padding: 30px;
            border-radius: 30px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
            margin-bottom: 50px;
            display: flex;
            gap: 40px;
            overflow: hidden;
            transition: transform 0.4s, box-shadow 0.4s;
            flex-wrap: wrap;
        }

        .dataTables_paginate {
            margin-bottom: 15px;
        }

        .candidate-left {
            flex: 0 0 150px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .candidate-profile {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            border: 5px solid #ff6b00;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            margin-bottom: 15px;
        }

        .candidate-profile img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .candidate-right {
            flex: 1;
        }

        .candidate-right h3 {
            font-size: 28px;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .candidate-right a {
            display: inline-block;
            font-size: 14px;
            color: #ff9b00;
            margin-bottom: 20px;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .candidate-right a:hover {
            color: #ff6b00;
        }

        .candidate-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px 30px;
            font-size: 15px;
            color: #555;
            margin-top: 10px;
        }

        .candidate-info div strong {
            display: inline-block;
            width: 140px;
            font-weight: 600;
            color: #34495e;
        }

        .skill-cell {
            grid-column: span 2;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }

        .skill-badge {
            display: inline-block;
            background: linear-gradient(135deg, #ff6b00, #ff9b00);
            color: #fff;
            font-size: 13px;
            font-weight: 500;
            padding: 5px 12px;
            border-radius: 20px;
            margin-right: 6px;
            margin-top: 4px;
            animation: badgePulse 2s infinite;
        }

        @keyframes badgePulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .workflow {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .workflow::before {
            content: '';
            position: absolute;
            top: 40%;
            left: 115px;
            right: 115px;
            height: 4px;
            background: #e0e0e0;
            z-index: 0;
            transform: translateY(-50%);
            border-radius: 2px;
        }

        .workflow::after {
            content: '';
            position: absolute;
            top: 40%;
            left: 93px;
            height: 4px;
            background: #1abc9c;
            z-index: 1;
            transform: translateY(-50%);
            border-radius: 2px;
            width: var(--workflow-green, 0%);
            transition: width 0.5s ease;
        }

        .workflow-step {
            text-align: center;
            position: relative;
            flex: 1;
            z-index: 2;
            transition: transform 0.3s;
        }

        .workflow-step .step-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #bdc3c7;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px auto;
            font-size: 24px;
            transition: background 0.3s, transform 0.3s;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .workflow-step.completed .step-icon {
            background: #3498db;
        }

        .workflow-step.active .step-icon {
            background: #1abc9c;
            margin-top: 25px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .workflow-step p {
            font-weight: 600;
            margin: 5px 0 0;
            font-size: 15px;
            color: #1a1a1a;
        }

        @media (max-width: 768px) {
            .workflow {
                flex-direction: column;
                align-items: center;
            }

            .workflow::before {
                top: 0;
                left: 50%;
                width: 4px;
                height: 100%;
                transform: translateX(-50%);
            }

            .workflow::after {
                top: 0;
                left: 50%;
                width: 4px !important;
                height: var(--workflow-green, 0%);
                transform: translateX(-50%);
            }
        }

        tbody>tr td {
            padding: 5px !important;
        }
    </style>

    <div class="page-wrapper">
        <div class="content">

            <!-- Candidate Details -->
            <div class="card mb-4 p-4">
                <div class="row">
                    <div class="col-md-2 text-center mb-3">

                        <div>
                            <img src="https://randomuser.me/api/portraits/men/41.jpg"
                                style="width:110px;height:110px;border-radius:50%;border:4px solid #ff6b00;object-fit:cover;box-shadow:0 4px 10px rgba(0,0,0,0.15);">
                        </div>

                        <div class="mt-3">
                            <a href="{{ asset('uploads/Rohit.pdf') }}" target="_blank"
                                style="display:inline-block;background: linear-gradient(135deg, #ff6b00, #ff9b00);color:#fff;padding:6px 18px;font-size:13px;border-radius:20px;font-weight:600;text-decoration:none;box-shadow:0 4px 10px rgba(0,0,0,0.12);">
                                View CV
                            </a>
                        </div>

                    </div>

                    <div class="col-md-9">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <tbody>

                                    <tr>
                                        <th width="180">Name</th>
                                        <td>Rohit Mishra</td>
                                        <th width="180">DOB</th>
                                        <td>04-03-2005</td>
                                    </tr>

                                    <tr>
                                        <th>Email</th>
                                        <td>rohitmishra41@gmail.com</td>
                                        <th>State</th>
                                        <td>Karnataka</td>
                                    </tr>

                                    <tr>
                                        <th>City</th>
                                        <td>Bangalore</td>
                                        <th>Working With</th>
                                        <td>Communities and Children</td>
                                    </tr>

                                    <tr>
                                        <th>Skills</th>
                                        <td colspan="3">
                                            <span class="badge bg-primary text-white me-1 mb-1">Research</span>
                                            <span class="badge bg-primary text-white me-1 mb-1">Documentation</span>
                                            <span class="badge bg-primary text-white me-1 mb-1">Communication</span>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Workflow -->
            <div class="workflow">
                <div class="workflow-step completed">
                    <div class="step-icon" style="margin-top: 20px"><i class="fa-solid fa-file-alt"></i></div>
                    <p>Submission</p>
                    <small>Date: 11-12-2025</small>
                </div>

                <div class="workflow-step mt-3">
                    <div class="step-icon"><i class="fa-solid fa-check"></i></div>
                    <p>Shortlist</p>
                    <div style="display: flex; gap: 20px; justify-content: center; margin-top: 5px;">
                        <div>
                            <input type="radio" name="shortlist" id="shortlisted">
                            <label for="shortlisted">Shortlisted</label>
                        </div>
                        <div>
                            <input type="radio" name="shortlist" id="not-shortlisted">
                            <label for="not-shortlisted">Not Shortlisted</label>
                        </div>
                    </div>
                </div>

                <div class="workflow-step">
                    <div class="step-icon"><i class="fa-solid fa-calendar-alt"></i></div>
                    <p>Interview</p>
                </div>

                <div class="workflow-step">
                    <div class="step-icon"><i class="fa-solid fa-file"></i></div>
                    <p>Confirmation Letter</p>
                </div>

                <div class="workflow-step">
                    <div class="step-icon"><i class="fa-solid fa-industry"></i></div>
                    <p>Placed</p>
                </div>
            </div>

            <!-- Interview Table -->
            <div class="row mt-5">
                <div class="col-sm-12">
                    <div class="card">

                        <div class="card-header">
                            <h4 class="card-title">Interview Schedule</h4>
                        </div>

                        <div class="card-body">

                            <div class="table-responsive">
                                <table class="table datatable w-100">

                                    <thead class="thead-light">
                                        <tr>

                                            <th>Round</th>
                                            <th>Mode</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Venue</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>Comments</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <tr>
                                            <td>R1</td>

                                            <td>
                                                <select class="form-control">
                                                    <option>On Call</option>
                                                    <option>Online</option>
                                                    <option>Offline</option>
                                                </select>
                                            </td>

                                            <td><input type="date" class="form-control" value="2025-11-29"></td>
                                            <td><input type="time" class="form-control" value="12:00"></td>

                                            <td>
                                                <textarea class="form-control" rows="2">City: Kanpur State: Uttar Pradesh</textarea>
                                            </td>
                                            <td>
                                                <textarea class="form-control" rows="2">Hello</textarea>
                                            </td>

                                            <td>
                                                <select class="form-control">
                                                    <option>Select Status</option>
                                                    <option>Cleared</option>
                                                    <option>Rejected</option>
                                                    <option>Postponed</option>
                                                </select>
                                            </td>

                                            <td>
                                                <textarea class="form-control" rows="2"></textarea>
                                            </td>

                                            <td>
                                                <button class="btn btn-info btn-sm"><i
                                                        class="fa fa-paper-plane"></i></button>
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


        <!-- Shortlist Modal -->
        <div class="modal fade" id="shortlist_modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <span class="avatar avatar-xl bg-transparent-success text-success mb-3">
                            <i class="ti ti-check fs-36"></i>
                        </span>
                        <h4 class="mb-1">Confirm Action</h4>
                        <p class="mb-3" id="shortlistMessage">Are you sure?</p>
                        <input type="hidden" id="shortlistStatus">
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" id="confirmShortlistBtn" class="btn btn-success">Yes, Proceed</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('after_scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const shortlistModal = new bootstrap.Modal(document.getElementById('shortlist_modal'));
            const shortlistMessage = document.getElementById('shortlistMessage');
            const shortlistStatus = document.getElementById('shortlistStatus');

            const shortlistRadio = document.getElementById('shortlisted');
            const notShortlistRadio = document.getElementById('not-shortlisted');
            let selectedRadio = null;

            shortlistRadio.addEventListener('click', function(e) {
                e.preventDefault();
                selectedRadio = shortlistRadio;
                shortlistMessage.textContent = "Are you sure you want to shortlist this candidate?";
                shortlistStatus.value = "shortlisted";
                shortlistModal.show();
            });

            notShortlistRadio.addEventListener('click', function(e) {
                e.preventDefault();
                selectedRadio = notShortlistRadio;
                shortlistMessage.textContent = "Are you sure you want to mark as not shortlisted?";
                shortlistStatus.value = "not_shortlisted";
                shortlistModal.show();
            });

            document.getElementById('confirmShortlistBtn').addEventListener('click', function() {
                if (selectedRadio) selectedRadio.checked = true;

                const icon = document.querySelector(".workflow-step.active .step-icon");
                icon.classList.add("completed");

                shortlistModal.hide();
                setTimeout(updateWorkflowLine, 300);
            });

            function updateWorkflowLine() {
                const steps = document.querySelectorAll(".workflow-step");
                let lastCompletedIndex = -1;

                steps.forEach((step, index) => {
                    if (step.classList.contains("completed") || step.classList.contains("active")) {
                        lastCompletedIndex = index;
                    }
                });

                let percent = (lastCompletedIndex / (steps.length - 1)) * 100;

                document.documentElement.style.setProperty("--workflow-green", percent + "%");
            }

            updateWorkflowLine();
        });
    </script>
@endpush

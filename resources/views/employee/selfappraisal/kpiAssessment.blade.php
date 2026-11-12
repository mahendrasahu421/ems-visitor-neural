@extends('employee.layout.layout')
@section('title', $title)
@section('main-section')

    <x-alert-modal />

    <style>
        .custom-datatable-filter table th,
        .custom-datatable-filter table td {
            vertical-align: middle;
        }

        .rating-dropdown .dropdown-toggle {
            width: 100%;
            text-align: left;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 60px;
        }

        .progress-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-bottom: 10px;
            position: relative;
            padding: 0 40px;
        }

        .progress-container::before {
            content: "";
            position: absolute;
            top: 20px;
            left: 100px;
            right: 50px;
            height: 3px;
            background: #f16529;
            z-index: 1;
        }

        .step {
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .circle {
            width: 35px;
            height: 35px;
            background: #f16529;
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 8px auto;
            font-size: 16px;
        }

        .step p {
            font-size: 14px;
            color: #f16529;
            font-weight: 600;
        }
    </style>

    <div class="page-wrapper" style="min-height: 428px;">
        <div class="content">

            <div class="progress-container">
                <div class="step active">
                    <div class="circle"><i class="fas fa-check"></i></div>
                    <p>Self Appraisal</p>
                </div>

                <div class="step active">
                    <div class="circle"><i class="fas fa-check"></i></div>
                    <p>Competencies</p>
                </div>

                <div class="step active">
                    <div class="circle"><i class="fas fa-check"></i></div>
                    <p>KPI</p>
                </div>

                <div class="step active">
                    <div class="circle"><i class="fas fa-check"></i></div>
                    <p>Form-C</p>
                </div>
            </div>

            <h4 class="mb-3">KPI Assessment /
                <span class="text-muted">केपीआई मूल्यांकन</span>
            </h4>

            <div class="card mt-3">
                <div class="card-body p-0">
                    <div class="custom-datatable-filter table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr style="text-align:center; background:#f8f9fa;">
                                    <th rowspan="2">Sr.No.<br><small>क्रमांक</small></th>
                                    <th rowspan="2">Objective<br><small>उद्देश्य</small></th>
                                    <th rowspan="2">Key Performance Indicator<br><small>मुख्य निष्पादन संकेतक</small>
                                    </th>
                                    <th rowspan="2">Weightage<br><small>महत्व</small></th>

                                    <th colspan="3">Performance<br><small>प्रदर्शन</small></th>

                                    <th colspan="3">
                                        Self KPI Score & Remarks <br>
                                        <small>स्वयं केपीआई स्कोर</small>
                                    </th>
                                </tr>

                                <tr style="text-align:center; background:#f8f9fa;">
                                    <th>Target<br><small>लक्ष्य</small></th>
                                    <th>Actual<br><small>वास्तविक</small></th>
                                    <th>Achievement%<br><small>उपलब्धि%</small></th>

                                    <th>Rating<br><small>रेटिंग</small></th>
                                    <th>Score<br><small>अंक</small></th>
                                    <th>Self Assessment Remarks <br>
                                        <small>स्व-मूल्यांकन टिप्पणियाँ</small>
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Improve Customer Satisfaction</td>
                                    <td>Response Time, Resolution Rate</td>
                                    <td>20%</td>

                                    <td>30%</td>
                                    <td>28%</td>
                                    <td>93%</td>

                                    <td style="width:180px;">
                                        <div class="dropdown rating-dropdown">
                                            <button class="btn bg-white text-dark dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown">
                                                Select Rating
                                            </button>

                                            <ul class="dropdown-menu w-100 bg-white text-dark">
                                                <li><a class="dropdown-item text-dark" href="#">5</a></li>
                                                <li><a class="dropdown-item text-dark" href="#">4</a></li>
                                                <li><a class="dropdown-item text-dark" href="#">3</a></li>
                                                <li><a class="dropdown-item text-dark" href="#">2</a></li>
                                                <li><a class="dropdown-item text-dark" href="#">1</a></li>
                                            </ul>
                                        </div>
                                    </td>


                                    <td>0.4</td>
                                    <td>
                                        <textarea class="form-control self-remarks" placeholder="Enter your remarks..." maxlength="500">Very Good</textarea>
                                        <small class="text-muted char-count">Characters Left: 500</small>
                                    </td>
                                </tr>

                                <tr>
                                    <td>2</td>
                                    <td>Team Coordination</td>
                                    <td>Meetings, Support, Planning</td>
                                    <td>15%</td>

                                    <td>25%</td>
                                    <td>20%</td>
                                    <td>80%</td>

                                    <td style="width:180px;">
                                        <div class="dropdown rating-dropdown">
                                            <button class="btn bg-white text-dark dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown">
                                                Select Rating
                                            </button>

                                            <ul class="dropdown-menu w-100 bg-white text-dark">
                                                <li><a class="dropdown-item text-dark" href="#">5</a></li>
                                                <li><a class="dropdown-item text-dark" href="#">4</a></li>
                                                <li><a class="dropdown-item text-dark" href="#">3</a></li>
                                                <li><a class="dropdown-item text-dark" href="#">2</a></li>
                                                <li><a class="dropdown-item text-dark" href="#">1</a></li>
                                            </ul>
                                        </div>
                                    </td>


                                    <td>0.4</td>

                                    <td>
                                        <textarea class="form-control self-remarks" placeholder="Enter your remarks..." maxlength="500">Very Good</textarea>
                                        <small class="text-muted char-count">Characters Left: 500</small>
                                    </td>
                                </tr>

                                <tr>
                                    <td>3</td>
                                    <td>Team Coordination</td>
                                    <td>Meetings, Support, Planning</td>
                                    <td>15%</td>

                                    <td>25%</td>
                                    <td>20%</td>
                                    <td>80%</td>

                                    <td style="width:180px;">
                                        <div class="dropdown rating-dropdown">
                                            <button class="btn bg-white text-dark dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown">
                                                Select Rating
                                            </button>

                                            <ul class="dropdown-menu w-100 bg-white text-dark">
                                                <li><a class="dropdown-item text-dark" href="#">5</a></li>
                                                <li><a class="dropdown-item text-dark" href="#">4</a></li>
                                                <li><a class="dropdown-item text-dark" href="#">3</a></li>
                                                <li><a class="dropdown-item text-dark" href="#">2</a></li>
                                                <li><a class="dropdown-item text-dark" href="#">1</a></li>
                                            </ul>
                                        </div>
                                    </td>


                                    <td>0.4</td>

                                    <td>
                                        <textarea class="form-control self-remarks" placeholder="Enter your remarks..." maxlength="500">Very Good</textarea>
                                        <small class="text-muted char-count">Characters Left: 500</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>

                <div class="card-footer text-end" style="background:#f8f9fa;">
                    <h5 class="mb-3">
                        Total Score: <strong>12.5</strong>
                    </h5>

                    <a href="#" class="btn btn-primary">
                        Save
                    </a>

                    <a href="#" class="btn btn-primary">
                        Save as Draft
                    </a>

                    <a href="{{ route('employee.form-c') }}" class="btn btn-primary ms-2">
                        Go to Form C
                    </a>
                </div>
            </div>

        </div>

        <x-footer />
    </div>

@endsection

@push('after_scripts')
    <script>
        document.querySelectorAll(".rating-dropdown .dropdown-item").forEach(function(item) {
            item.addEventListener("click", function() {
                this.closest(".rating-dropdown")
                    .querySelector(".dropdown-toggle").textContent = this.textContent;
            });
        });


        document.querySelectorAll(".self-remarks").forEach(function(textarea) {
            const charCount = textarea.nextElementSibling;
            const maxLength = 500;
            charCount.textContent = `Characters Left: ${maxLength - textarea.value.length}`;
            textarea.addEventListener("input", function() {
                const remaining = maxLength - this.value.length;
                charCount.textContent = `Characters Left: ${remaining >= 0 ? remaining : 0}`;
                if (this.value.length > maxLength) {
                    this.value = this.value.slice(0, maxLength);
                }
            });
        });
    </script>
@endpush

@extends('employee.layout.layout')
@section('title', $title)
@section('main-section')

    <style>
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
    <x-alert-modal />

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

            <!-- Page Header -->
                <h4 class="my-4">Form C / <span class="text-muted">फॉर्म सी</span></h4>
                

            <!-- Achievements Section -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <div class="btn-primary p-2">
                        <h5 class=" text-white">1. Top 3 Achievements during performance assessment year 
                        <span>/ प्रदर्शन मूल्यांकन वर्ष के दौरान शीर्ष 3 उपलब्धियाँ</span></h5>
                    </div>

                    <!-- Achievement 1 -->
                    <div class="achievement-group mt-3">
                        <p>Achievement 1 / उपलब्धि 1</p>
                        <textarea class="form-control achievement" rows="2" maxlength="500">In the Jandaha Branch, there was no Loan Disbursement for the Past 8 Months, however, after i joined, over 30 loans have been Disbursed every month, I have worked collaboratively with my team to achieve the Target in my area. this includes completing the loan dis. target on time and continuously improving departmental performance.</textarea>
                        <small class="text-muted charCount">500 characters left</small>
                    </div>

                    <!-- Achievement 2 -->
                    <div class="achievement-group mt-3">
                        <p>Achievement 2 / उपलब्धि 2</p>
                        <textarea class="form-control achievement" rows="2" maxlength="500">we have complete control over the BM and Staff in our area to ensure that no one breaks the Company rule & that no theft occurs. this has maintained trust and transparency within the organization.</textarea>
                        <small class="text-muted charCount">500 characters left</small>
                    </div>

                    <!-- Achievement 3 -->
                    <div class="achievement-group mt-3">
                        <p>Achievement 3 / उपलब्धि 3</p>
                        <textarea class="form-control achievement" rows="2" maxlength="500"> i have implement new strategies to improve workflows and operations across various branches ,which has positively impacted and achievement of our target .</textarea>
                        <small class="text-muted charCount">500 characters left</small>
                    </div>

                </div>

                <div class="card-body">
                    <div class="btn-primary p-2">
                        <h5 class=" text-white">2.What I could have done better during performance assessment year / <span> प्रदर्शन मूल्यांकन वर्ष के दौरान मैं क्या बेहतर कर सकता था</span></h5>
                        
                    </div>

                    <!-- Activity 1 -->
                    <div class="achievement-group mt-3">
                        <p>Activity 1 / गतिविधि 1</p>
                        <textarea class="form-control achievement" rows="2" maxlength="500">i could have managed my responsibilities more efficiently and prioritized my tasks better .</textarea>
                        <small class="text-muted charCount">500 characters left</small>
                    </div>

                    <!-- Activity 2 -->
                    <div class="achievement-group mt-3">
                        <p>Activity 2 / गतिविधि 2</p>
                        <textarea class="form-control achievement" rows="2" maxlength="500">i could have worked on enhancing clarity and effectiveness in communicating with my team and other department .</textarea>
                        <small class="text-muted charCount">500 characters left</small>
                    </div>

                    <!-- Activity 3 -->
                    <div class="achievement-group mt-3">
                        <p>Activity 3 / गतिविधि 3</p>
                        <textarea class="form-control achievement" rows="2" maxlength="500"> i could have been more proactive in acquiring new skills and technologies, which would have further improved my work efficiency.</textarea>
                        <small class="text-muted charCount">500 characters left</small>
                    </div>

                </div>

                <div class="card-body">
                    <div class="btn-primary p-2">
                        <h5 class=" text-white">3.My improvement goals for next year / <span>अगले वर्ष के लिए मेरे सुधार लक्ष्य</span></h5>
                        
                    </div>

                    <!-- Goal 1 -->
                    <div class="achievement-group mt-3">
                        <p>Goal 1 / लक्ष्य 1</p>
                        <textarea class="form-control achievement" rows="2" maxlength="500">At the Branch in our Area ,we will work together with the team to reduce the Par % .</textarea>
                        <small class="text-muted charCount">500 characters left</small>
                    </div>

                    <!-- Goal 2 -->
                    <div class="achievement-group mt-3">
                        <p>Goal 2 / लक्ष्य 2</p>
                        <textarea class="form-control achievement" rows="2" maxlength="500">I will work with my team in my area to meet the loan Dis. target by the Company .</textarea>
                        <small class="text-muted charCount">500 characters left</small>
                    </div>

                    <!-- Goal 3 -->
                    <div class="achievement-group mt-3">
                        <p>Goal 3 / लक्ष्य 3</p>
                        <textarea class="form-control achievement" rows="2" maxlength="500">In the Gaunaha Branch ,there was only 1 GM & No BM ,but have added three more GMs & 1 BM there ,Additionally ,we have had 1 GM Join in sasamusa & 1 GM join in the Tamkuhi Branch ,Moving forward ,we will have over five GMs in all our Braanches .</textarea>
                        <small class="text-muted charCount">500 characters left</small>
                    </div>

                </div>

                <div class="card-body">
                    <div class="btn-primary p-2">
                        <h5 class="text-white">Training</h5>
                    </div>

                    <div class="dropdown w-100 position-relative">
                        <button class="btn btn-light w-100 text-start no-caret text-black bg-white mt-3" type="button"
                            id="trainingMenu" data-bs-toggle="dropdown" data-bs-display="static">
                            Select
                        </button>

                        <ul class="dropdown-menu w-100 dropdown-menu-bottom p-2" aria-labelledby="trainingMenu">
                            <input type="text" id="searchTraining" class="form-control mb-2" placeholder="Search...">

                            <li><a class="dropdown-item training-item" href="#">Add Training</a></li>
                            <li><a class="dropdown-item training-item" href="#">View All</a></li>
                            <li><a class="dropdown-item training-item" href="#">Export</a></li>
                        </ul>
                    </div>
                    <div class="card-footer text-end" style="background:#f8f9fa;">

                        <a href="{{ route('employee.dashboard') }}" class="btn btn-primary ms-2">
                            Go to Dashboard
                        </a>

                        <a href="#" class="btn btn-primary">
                            Save as Draft
                        </a>
                        
                        <a href="#" class="btn btn-primary">
                            Save
                        </a>

                    </div>
                </div>
            </div>

        </div>
        <x-footer />
    </div>

@endsection

@push('after_scripts')
    <script>
        document.getElementById('searchTraining').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let items = document.querySelectorAll('.training-item');

            items.forEach(item => {
                let text = item.textContent.toLowerCase();
                item.style.display = text.includes(filter) ? "block" : "none";
            });
        });
    </script>

    <script>
        document.querySelectorAll('.achievement-group').forEach(group => {
            const textarea = group.querySelector('.achievement');
            const counter = group.querySelector('.charCount');

            function updateCount() {
                const remaining = 500 - textarea.value.length;
                counter.textContent = remaining + ' characters left';
            }

            updateCount();
            textarea.addEventListener('input', updateCount);
        });
    </script>
@endpush

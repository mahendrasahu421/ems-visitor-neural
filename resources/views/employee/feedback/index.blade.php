@extends('employee.layout.layout')
@section('title', $title)
@section('main-section')

    <x-alert-modal />

    <div class="page-wrapper" style="min-height: 428px;">
        <div class="content">

            <h4 class="mb-2">Feedback</h4>
            <h5 class="mb-4 text-muted">प्रतिक्रिया|</h5>

            <div class="card p-3 shadow">

                <!-- Question 1 -->
                <div class="card border-primary mb-3 shadow-sm">
                    <div class="card-body">
                        <p class="fw-bold mb-3">
                            Have you filled your appraisal form by yourself? <br>
                            क्या आपने अपना मूल्यांकन फॉर्म स्वयं भरा है?
                        </p>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="question_1" id="q1_yes"
                                    value="yes">
                                <label class="form-check-label fw-semibold" for="q1_yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="question_1" id="q1_no"
                                    value="no">
                                <label class="form-check-label fw-semibold" for="q1_no">No</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Question 2 -->
                <div class="card border-primary mb-3 shadow-sm">
                    <div class="card-body">
                        <p class="fw-bold mb-3">
                            Did you face any difficulty while filling your appraisal form? <br>
                            क्या आपको अपना मूल्यांकन फॉर्म भरते समय किसी कठिनाई का सामना करना पड़ा?
                        </p>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="question_2" id="q2_yes"
                                    value="yes">
                                <label class="form-check-label fw-semibold" for="q2_yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="question_2" id="q2_no"
                                    value="no">
                                <label class="form-check-label fw-semibold" for="q2_no">No</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Question 3 -->
                <div class="card border-primary mb-3 shadow-sm">
                    <div class="card-body">
                        <p class="fw-bold mb-3">
                            Did your controlling officer inform you regarding the time and venue of the discussion in
                            advance? <br>
                            क्या आपके नियंत्रण अधिकारी ने आपको चर्चा के समय और स्थान के बारे में पहले से सूचित किया था?
                        </p>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="question_3" id="q3_yes"
                                    value="yes">
                                <label class="form-check-label fw-semibold" for="q3_yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="question_3" id="q3_no"
                                    value="no">
                                <label class="form-check-label fw-semibold" for="q3_no">No</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Question 4 -->
                <div class="card border-primary mb-3 shadow-sm">
                    <div class="card-body">
                        <p class="fw-bold mb-3">
                            Did your controlling officer discuss while reviewing your performance? <br>
                            क्या आपके नियंत्रक अधिकारी ने चर्चा करते समय आपके प्रदर्शन की समीक्षा की?
                        </p>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="question_4" id="q4_yes"
                                    value="yes">
                                <label class="form-check-label fw-semibold" for="q4_yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="question_4" id="q4_no"
                                    value="no">
                                <label class="form-check-label fw-semibold" for="q4_no">No</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Question 5 -->
                <div class="card border-primary mb-3 shadow-sm">
                    <div class="card-body">
                        <p class="fw-bold mb-3">
                            What was the mode of discussion? <br>
                            चर्चा का तरीका क्या था?
                        </p>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="question_5" id="q5_yes"
                                    value="yes">
                                <label class="form-check-label fw-semibold" for="q5_yes">Face to Face</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="question_5" id="q5_no"
                                    value="no">
                                <label class="form-check-label fw-semibold" for="q5_no">Telephone</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="question_5" id="q5_yes"
                                    value="yes">
                                <label class="form-check-label fw-semibold" for="q5_yes">Video Conference</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="question_5" id="q5_no"
                                    value="no">
                                <label class="form-check-label fw-semibold" for="q5_no">None</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Question 6 -->
                <div class="card border-primary mb-3 shadow-sm">
                    <div class="card-body">
                        <p class="fw-bold mb-3">
                            How much time did your controlling officer devote to your performance review?? <br>
                            आपके नियंत्रण अधिकारी ने आपके प्रदर्शन की समीक्षा के लिए कितना समय दिया?
                        </p>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="question_6" id="q6_yes"
                                    value="yes">
                                <label class="form-check-label fw-semibold" for="q6_yes">Less then 10 min</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="question_6" id="q6_no"
                                    value="no">
                                <label class="form-check-label fw-semibold" for="q6_no">10 to 30 min</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="question_6" id="q6_yes"
                                    value="yes">
                                <label class="form-check-label fw-semibold" for="q6_yes">More then 30 min</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Question 7 -->
                <div class="card border-primary mb-3 shadow-sm">
                    <div class="card-body">
                        <p class="fw-bold mb-3">
                            What are two achievements highlighted by your controlling officer? <br>
                            आपके नियंत्रण अधिकारी द्वारा किन दो उपलब्धियों पर प्रकाश डाला गया है?
                        </p>
                        <div class="mb-2">
                            <textarea class="form-control char-count-textarea" name="question_7_text" rows="2" maxlength="500"
                                data-max="500" placeholder="Type your achievements here...">Keeping the team united and achieving the target through quality-controlled work.</textarea>
                            <small class="text-muted counter-text">500 characters left</small>
                        </div>
                    </div>
                </div>

                <!-- Question 8 -->
                <div class="card border-primary mb-3 shadow-sm">
                    <div class="card-body">
                        <p class="fw-bold mb-3">
                            What are two area of improvement suggestedby your controlling officer? <br>
                            आपके नियंत्रण अधिकारी द्वारा सुधार के कौन से दो क्षेत्र सुझाए गए हैं?
                        </p>
                        <div class="mb-2">
                            <textarea class="form-control char-count-textarea" name="question_7_text" rows="2" maxlength="500"
                                data-max="500" placeholder="Type your achievements here...">Ensuring that 100% of the target for collection and loan disbursement are met .</textarea>
                            <small class="text-muted counter-text">500 characters left</small>
                        </div>
                    </div>
                </div>

                <!-- Question 9 -->
                <div class="card border-primary mb-3 shadow-sm">
                    <div class="card-body">
                        <p class="fw-bold mb-3">
                           How would you like to express yourself after performance review? <br>
                            प्रदर्शन समीक्षा के बाद आप स्वयं को किस प्रकार अभिव्यक्त करना चाहेंगे?
                        </p>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="question_6" id="q6_yes"
                                    value="yes">
                                <label class="form-check-label fw-semibold" for="q6_yes">Motivated</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="question_6" id="q6_no"
                                    value="no">
                                <label class="form-check-label fw-semibold" for="q6_no">Did not make difference</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="question_6" id="q6_yes"
                                    value="yes">
                                <label class="form-check-label fw-semibold" for="q6_yes">Demotivated</label>
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
        document.querySelectorAll('.char-count-textarea').forEach(textarea => {
            const counter = textarea.nextElementSibling;
            const max = parseInt(textarea.dataset.max);
            counter.textContent = `${max - textarea.value.length} characters left`;

            textarea.addEventListener('input', () => {
                const remaining = max - textarea.value.length;
                counter.textContent = `${remaining} characters left`;
            });
        });
    </script>
@endpush

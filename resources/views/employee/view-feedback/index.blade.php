@extends('employee.layout.layout')
@section('title', $title)
@section('main-section')

    <style>
        table.table td {
            vertical-align: top;
            white-space: normal !important;
            overflow: visible !important;
        }

        .custom-datatable-filter {
            overflow-x: hidden !important;
        }

        .feedback-box {
            width: 100%;
            min-height: 120px;
            height: auto;
            overflow: hidden !important;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
            resize: vertical;
        }

        textarea.remarks-box {
            min-height: 90px;
            resize: vertical;
            overflow-x: hidden !important;
        }

        /* NEW SCROLL FIX FOR KPI TABLE */
        .kpi-table-wrapper {
            width: 100%;
            overflow-x: auto !important;
            overflow-y: hidden;
        }

        .kpi-table-wrapper table {
            min-width: 1400px;
        }
    </style>

    <x-alert-modal />

    <div class="page-wrapper" style="min-height: 428px;">
        <div class="content">

            <h4 class="mb-2">Competency Review</h4>

            <div class="card mt-3">
                <div class="card-body p-0">
                    <div class="custom-datatable-filter table-responsive">
                        <table class="table table-bordered bg-primary">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">S.No.</th>
                                    <th style="width: 70px;">Objective</th>
                                    <th style="width: 250px;">Self Rating</th>
                                    <th style="width: 250px;">CO Rating</th>
                                    <th style="width: 330px;">CO Feedback</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Communication</td>
                                    <td>
                                        Level3: Communicating clearly and effectively, both written & verbal. Actively
                                        listening and adapting communication style to the audience and situation. <br> लिखित
                                        और मौखिक दोनों तरह से स्पष्ट और प्रभावी ढंग से संचार करना। दर्शकों और स्थिति के
                                        अनुसार सक्रिय रूप से सुनना और संचार शैली को अपनाना।
                                    </td>

                                    <td>
                                        Level3: Communicating clearly and effectively, both written & verbal. Actively
                                        listening and adapting communication style to the audience and situation. <br> लिखित
                                        और मौखिक दोनों तरह से स्पष्ट और प्रभावी ढंग से संचार करना। दर्शकों और स्थिति के
                                        अनुसार सक्रिय रूप से सुनना और संचार शैली को अपनाना।
                                    </td>

                                    <td>
                                        <textarea class="feedback-box" readonly>Rana possesses all these qualities.</textarea>
                                    </td>
                                </tr>

                                <tr>
                                    <td>2</td>
                                    <td>Leadership</td>
                                    <td>
                                        Level5: Exceptional leadership skills, able to inspire and guide teams to accomplish
                                        ambitious goals. Recognized as a strategic thinker and skilled at developing and
                                        empowering others. <br> असाधारण नेतृत्व कौशल, महत्वाकांक्षी लक्ष्यों को पूरा करने के
                                        लिए टीमों को प्रेरित करने और मार्गदर्शन करने में सक्षम। एक रणनीतिक विचारक के रूप में
                                        पहचाने जाने वाले और दूसरों को विकसित करने और सशक्त बनाने में कुशल।
                                    </td>

                                    <td>
                                        Level5: Exceptional leadership skills, able to inspire and guide teams to accomplish
                                        ambitious goals. Recognized as a strategic thinker and skilled at developing and
                                        empowering others. <br> असाधारण नेतृत्व कौशल, महत्वाकांक्षी लक्ष्यों को पूरा करने के
                                        लिए टीमों को प्रेरित करने और मार्गदर्शन करने में सक्षम। एक रणनीतिक विचारक के रूप में
                                        पहचाने जाने वाले और दूसरों को विकसित करने और सशक्त बनाने में कुशल।
                                    </td>

                                    <td>
                                        <textarea class="feedback-box" readonly>Rana possesses exceptional leadership skills and is capable of inspiring and guiding team to achieve ambitious goals, recognized as a strategic thinker, he is adept at developing and empowering others.</textarea>
                                    </td>
                                </tr>

                                <tr>
                                    <td>3</td>
                                    <td>Development Skill & Approach</td>
                                    <td>
                                        Level4: Proactively identifying and addressing development needs; Implementing
                                        strategic plan. <br>विकास संबंधी आवश्यकताओं की सक्रिय रूप से पहचान करना और उनका
                                        समाधान करना; रणनीतिक योजना का कार्यान्वयन.
                                    </td>

                                    <td>
                                        Level4: Proactively identifying and addressing development needs; Implementing
                                        strategic plan. <br>विकास संबंधी आवश्यकताओं की सक्रिय रूप से पहचान करना और उनका
                                        समाधान करना; रणनीतिक योजना का कार्यान्वयन.
                                    </td>

                                    <td>
                                        <textarea class="feedback-box" readonly>Actively identifying developmental needs and addressing them, implementing strategic plan -all these qualities are present in Rana .</textarea>
                                    </td>
                                </tr>

                                <tr>
                                    <td>4</td>
                                    <td>Relationship Building</td>
                                    <td>
                                        Level5: Exceptional relationship-building skills, able to connect with a diverse
                                        range of stakeholders and foster productive and collaborative partnerships. <br>
                                        असाधारण संबंध-निर्माण कौशल, विभिन्न प्रकार के हितधारकों के साथ जुड़ने और उत्पादक और
                                        सहयोगात्मक साझेदारी को बढ़ावा देने में सक्षम।
                                    </td>

                                    <td>
                                        Level5: Exceptional relationship-building skills, able to connect with a diverse
                                        range of stakeholders and foster productive and collaborative partnerships. <br>
                                        असाधारण संबंध-निर्माण कौशल, विभिन्न प्रकार के हितधारकों के साथ जुड़ने और उत्पादक और
                                        सहयोगात्मक साझेदारी को बढ़ावा देने में सक्षम।
                                    </td>

                                    <td>
                                        <textarea class="feedback-box" readonly>Exceptional relationship building skills, capable of connecting with diverse stakeholders and fostering productive and collaborative partnerships  .</textarea>
                                    </td>
                                </tr>

                                <tr>
                                    <td>5</td>
                                    <td>Relationship Building</td>
                                    <td>
                                        Level4: Proactively ensure compliance and diligently observe and report enforcement.
                                        <br>सक्रिय रूप से अनुपालन सुनिश्चित करें और प्रवर्तन का परिश्रमपूर्वक निरीक्षण करें
                                        और रिपोर्ट करें।
                                    </td>

                                    <td>
                                        Level4: Proactively ensure compliance and diligently observe and report enforcement.
                                        <br>सक्रिय रूप से अनुपालन सुनिश्चित करें और प्रवर्तन का परिश्रमपूर्वक निरीक्षण करें
                                        और रिपोर्ट करें।
                                    </td>

                                    <td>
                                        <textarea class="feedback-box" readonly>Rana diligently conducts compliance inspections and submits report on time .</textarea>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <h4 class="mb-2">KPI Review</h4>

            <div class="card mt-3">
                <div class="card-body p-0">
                    <div class="kpi-table-wrapper table-responsive">

                        <table class="table table-bordered">
                            <thead>
                                <tr style="text-align:center; background:#f8f9fa;">
                                    <th rowspan="2" style="vertical-align: middle;">Key Performance Indicator</th>
                                    <th colspan="3" style="vertical-align: middle;">Performance</th>
                                    <th colspan="3" style="vertical-align: middle;">Self KPI Score & Remarks</th>
                                    <th colspan="3" style="vertical-align: middle;">Controller's Rating on KPI & Remarks
                                    </th>
                                </tr>

                                <tr>
                                    <th>Target</th>
                                    <th>Actual</th>
                                    <th>Achievement%</th>
                                    <th>Rating</th>
                                    <th>Score</th>
                                    <th>Self Assessment Remarks</th>
                                    <th>Rating</th>
                                    <th>Score</th>
                                    <th>Controller's Remarks</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>Achievement of Target for Business Growth</td>

                                    <td><input type="text" class="form-control" value="3122"></td>
                                    <td><input type="text" class="form-control" value="867"></td>
                                    <td><input type="text" class="form-control" value="28"></td>

                                    <td><input type="number" class="form-control" value="1"></td>
                                    <td><input type="number" class="form-control" value="0.4"></td>

                                    <td>
                                        <textarea class="form-control remarks-box">Sir ,After we joined ,The motipur area started reporting more loan than the previous month .</textarea>
                                    </td>

                                    <td><input type="number" class="form-control" value="1"></td>
                                    <td><input type="number" class="form-control" value="0.4"></td>

                                    <td>
                                        <textarea class="form-control remarks-box">As far as I have observed, Rana is a talented guy...</textarea>
                                    </td>
                                </tr>
                            </tbody>

                        </table>

                    </div>

                </div>
            </div>


            <h4 class="mb-3">Form-C</h4>
            <div class="card">

                <div class=" p-3 ">
                    <h4 class="mb-2 bg-primary p-2">Top 3 Achievements during Performance Assessment Year</h4>

                    <div class="card mt-3">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-6">

                                    <div class="mb-3">
                                        <label class="fw-bold mb-3">Achievements 1</label>
                                        <textarea class="form-control" style="min-height: 120px; resize: vertical;">In the Jolanda Branch, there was no Loan Disbursement for the last 6 months. After I joined, over 50 loans were disbursed every month. I collaborated with my team to achieve the target, completed loan disbursement on time, and improved overall departmental performance.
                                </textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="fw-bold mb-3">Achievements 2</label>
                                        <textarea class="form-control" style="min-height: 120px; resize: vertical;">We have complete control over the BM and staff in our area to ensure no violation of company rules. This has increased trust and transparency in the organization.
                                </textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="fw-bold mb-3">Achievements 3</label>
                                        <textarea class="form-control" style="min-height: 120px; resize: vertical;">I implemented new strategies to improve workflows across branches. This positively impacted target achievement and team efficiency.
                                </textarea>
                                    </div>

                                </div>

                                <div class="col-md-6">
                                    <label class="fw-bold mb-3">Controlling Officer Comment</label>
                                    <textarea class="form-control" style="min-height: 400px; resize: vertical;">In Rana's Area, operations center on loan disbursement. However, during periods of decline, the team puts extra emphasis on improving disbursements and on staff hiring.
                            </textarea>
                                </div>

                            </div>

                        </div>
                    </div>


                </div>
                <div class="p-3 ">
                    <h4 class="mb-2 bg-primary p-2">Top 3 Achievements during Performance Assessment Year</h4>

                    <div class="card mt-3">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-6">

                                    <div class="mb-3">
                                        <label class="fw-bold mb-3">Achievements 1</label>
                                        <textarea class="form-control" style="min-height: 120px; resize: vertical;">In the Jolanda Branch, there was no Loan Disbursement for the last 6 months. After I joined, over 50 loans were disbursed every month. I collaborated with my team to achieve the target, completed loan disbursement on time, and improved overall departmental performance.
                                </textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="fw-bold mb-3">Achievements 2</label>
                                        <textarea class="form-control" style="min-height: 120px; resize: vertical;">We have complete control over the BM and staff in our area to ensure no violation of company rules. This has increased trust and transparency in the organization.
                                </textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="fw-bold mb-3">Achievements 3</label>
                                        <textarea class="form-control" style="min-height: 120px; resize: vertical;">I implemented new strategies to improve workflows across branches. This positively impacted target achievement and team efficiency.
                                </textarea>
                                    </div>

                                </div>

                                <div class="col-md-6">
                                    <label class="fw-bold mb-3">Controlling Officer Comment</label>
                                    <textarea class="form-control" style="min-height: 400px; resize: vertical;">In Rana's Area, operations center on loan disbursement. However, during periods of decline, the team puts extra emphasis on improving disbursements and on staff hiring.
                            </textarea>
                                </div>

                            </div>

                        </div>
                    </div>


                </div>
                <div class=" p-3 ">
                    <h4 class="mb-2 bg-primary p-2">Top 3 Achievements during Performance Assessment Year</h4>

                    <div class="card mt-3">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-6">

                                    <div class="mb-3">
                                        <label class="fw-bold mb-3">Achievements 1</label>
                                        <textarea class="form-control" style="min-height: 120px; resize: vertical;">In the Jolanda Branch, there was no Loan Disbursement for the last 6 months. After I joined, over 50 loans were disbursed every month. I collaborated with my team to achieve the target, completed loan disbursement on time, and improved overall departmental performance.
                                </textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="fw-bold mb-3">Achievements 2</label>
                                        <textarea class="form-control" style="min-height: 120px; resize: vertical;">We have complete control over the BM and staff in our area to ensure no violation of company rules. This has increased trust and transparency in the organization.
                                </textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="fw-bold mb-3">Achievements 3</label>
                                        <textarea class="form-control" style="min-height: 120px; resize: vertical;">I implemented new strategies to improve workflows across branches. This positively impacted target achievement and team efficiency.
                                </textarea>
                                    </div>

                                </div>

                                <div class="col-md-6">
                                    <label class="fw-bold mb-3">Controlling Officer Comment</label>
                                    <textarea class="form-control" style="min-height: 400px; resize: vertical;">In Rana's Area, operations center on loan disbursement. However, during periods of decline, the team puts extra emphasis on improving disbursements and on staff hiring.
                            </textarea>
                                </div>

                            </div>

                        </div>
                    </div>


                </div>
            </div>


            <div class="card mt-3">
                <div class="card-body p-0">

                    <table class="table table-bordered">
                        <tbody>

                            <tr style="background:#f8f9fa;">
                                <th style="width: 20%; font-weight:600;">Self PMP Score</th>
                                <td style="width: 80%;">
                                    <input type="text" class="form-control" value="2.20" readonly>
                                </td>
                            </tr>

                            <tr style="background:#f8f9fa;">
                                <th style="font-weight:600;">CO Final Score</th>
                                <td>
                                    <input type="text" class="form-control" value="2.23" readonly>
                                </td>
                            </tr>

                            <tr style="background:#f8f9fa;">
                                <th style="font-weight:600;">CO Remarks</th>
                                <td>
                                    <textarea class="form-control" style="min-height:100px; resize:vertical;" readonly>In the Audit performance was good and transferred in operations. Got to perform in last quarter in operations and facing manpower shortfall. Keeping these things in consideration performance is good and much better performance in FY 24-25.
                                    </textarea>
                                </td>
                            </tr>

                        </tbody>
                    </table>

                    <div class="card-footer text-center">

                        <a href="{{ route('employee.feedback') }}" class="btn btn-primary ms-2">
                            Go to Feedback
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
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".feedback-box").forEach(function(textarea) {
                textarea.style.height = "auto";
                textarea.style.height = textarea.scrollHeight + "px";

                textarea.addEventListener("input", function() {
                    this.style.height = "auto";
                    this.style.height = this.scrollHeight + "px";
                });
            });
        });
    </script>
@endpush

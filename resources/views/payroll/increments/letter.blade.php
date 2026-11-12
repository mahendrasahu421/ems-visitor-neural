<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annual Increment Letter - {{ $increment->employee->employee_name }}</title>
    <link rel="stylesheet" href="{{ asset('frontent/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontent/assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1e293b;
        }
        .letter-container {
            max-width: 820px;
            margin: 40px auto;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            padding: 50px 60px;
            position: relative;
        }
        .letter-header {
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 24px;
            margin-bottom: 30px;
        }
        .company-logo {
            font-size: 26px;
            font-weight: 800;
            color: #1e3a8a;
            letter-spacing: -0.5px;
        }
        .letter-meta {
            font-size: 13px;
            color: #64748b;
        }
        .letter-title {
            font-size: 20px;
            font-weight: 700;
            text-align: center;
            color: #0f172a;
            margin: 25px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 12px;
        }
        .letter-body {
            font-size: 14.5px;
            line-height: 1.75;
            color: #334155;
        }
        .compensation-table {
            width: 100%;
            margin: 24px 0;
            border-collapse: collapse;
        }
        .compensation-table th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 700;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
        }
        .compensation-table td {
            font-size: 13.5px;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
        }
        .highlight-box {
            background-color: #f0fdf4;
            border-left: 4px solid #16a34a;
            padding: 14px 18px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
        }
        .signature-section {
            margin-top: 50px;
            padding-top: 20px;
        }
        .no-print-bar {
            max-width: 820px;
            margin: 20px auto 0;
        }
        @media print {
            body {
                background: #ffffff;
            }
            .no-print-bar {
                display: none !important;
            }
            .letter-container {
                box-shadow: none;
                margin: 0;
                padding: 20px 30px;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <!-- Top Action Bar (hidden on print) -->
    <div class="no-print-bar d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('payroll.increments.index') }}" class="btn btn-outline-secondary btn-sm">
            &larr; Back to Increments
        </a>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary btn-sm">
                <i class="ti ti-printer me-1"></i> Print Letter
            </button>
            <a href="{{ route('payroll.increments.download-letter', $increment->id) }}" class="btn btn-success text-white btn-sm">
                <i class="ti ti-download me-1"></i> Download PDF
            </a>
        </div>
    </div>

    <!-- Letter Container -->
    <div class="letter-container">
        <!-- Header -->
        <div class="letter-header d-flex justify-content-between align-items-start">
            <div>
                <div class="company-logo">{{ $company_name }}</div>
                <div class="letter-meta mt-1">Human Resources &amp; People Operations</div>
                <div class="letter-meta">Official Performance &amp; Compensation Division</div>
            </div>
            <div class="text-end">
                <div class="badge bg-primary-subtle text-primary border border-primary-subtle fs-12 px-2 py-1">
                    Ref: {{ $increment->increment_number }}
                </div>
                <div class="letter-meta mt-2">
                    Date: <strong>{{ now()->format('F d, Y') }}</strong>
                </div>
            </div>
        </div>

        <!-- Addressee -->
        <div class="mb-4">
            <div class="fw-bold fs-15 text-dark">To,</div>
            <div class="fw-bold fs-16 text-primary mt-1">{{ $increment->employee->employee_name }}</div>
            <div class="text-muted fs-13">Employee ID: <strong>{{ $increment->employee->employee_id }}</strong></div>
            <div class="text-muted fs-13">Current Designation: {{ $increment->previousDesignation->name ?? 'Staff' }}</div>
            <div class="text-muted fs-13">Agency / Department: {{ $increment->employee->agency->agency_name ?? 'In-House' }}</div>
        </div>

        <div class="letter-title">
            Annual Appraisal &amp; Compensation Revision Letter
        </div>

        <!-- Body -->
        <div class="letter-body">
            <p>Dear <strong>{{ $increment->employee->employee_name }}</strong>,</p>

            <p>
                In recognition of your sincere dedication, valued contribution, and performance during the
                <strong>{{ $increment->review_cycle }}</strong>, the Management is pleased to revise your annual compensation package.
            </p>

            @if($increment->isPromotion())
                <div class="highlight-box">
                    <strong>Heartiest Congratulations on your Promotion!</strong><br>
                    In addition to your salary revision, you have been promoted to the role of
                    <strong>{{ $increment->newDesignation->name }}</strong> with effect from
                    <strong>{{ $increment->effective_date->format('F d, Y') }}</strong>.
                </div>
            @endif

            <p>
                Your revised compensation will be effective from
                <strong>{{ $increment->effective_date->format('F d, Y') }}</strong>.
                Below are the details of your revised salary structure:
            </p>

            <!-- Comparison Table -->
            <table class="compensation-table">
                <thead>
                    <tr>
                        <th>Particulars</th>
                        <th class="text-end">Pre-Revision (&#8377;)</th>
                        <th class="text-end">Revised (&#8377;)</th>
                        <th class="text-end">Increase</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Monthly Gross Salary</strong></td>
                        <td class="text-end">&#8377; {{ number_format($increment->current_gross, 2) }}</td>
                        <td class="text-end fw-bold text-dark">&#8377; {{ number_format($increment->new_gross, 2) }}</td>
                        <td class="text-end text-success fw-bold">+&#8377; {{ number_format(max(0, $increment->new_gross - $increment->current_gross), 2) }} / mo</td>
                    </tr>
                    <tr>
                        <td><strong>Annual Cost to Company (CTC)</strong></td>
                        <td class="text-end">&#8377; {{ number_format($increment->current_ctc, 2) }}</td>
                        <td class="text-end fw-bold text-primary">&#8377; {{ number_format($increment->new_ctc, 2) }}</td>
                        <td class="text-end text-success fw-bold">+&#8377; {{ number_format($increment->hike_amount, 2) }} / yr</td>
                    </tr>
                    <tr>
                        <td><strong>Percentage Hike</strong></td>
                        <td class="text-end text-muted">-</td>
                        <td class="text-end fw-bold text-success">{{ number_format($increment->hike_percentage, 1) }}%</td>
                        <td class="text-end text-success fw-bold">{{ number_format($increment->hike_percentage, 1) }}% Hike</td>
                    </tr>
                    @if($increment->isPromotion())
                        <tr>
                            <td><strong>Designation / Position</strong></td>
                            <td class="text-end text-muted">{{ $increment->previousDesignation->name ?? 'Staff' }}</td>
                            <td class="text-end fw-bold text-dark">{{ $increment->newDesignation->name }}</td>
                            <td class="text-end text-warning fw-bold">Promoted</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            @if($increment->letter_notes)
                <div class="p-3 bg-light rounded border border-light-subtle my-3">
                    <div class="text-muted fs-11 text-uppercase font-weight-bold mb-1">Message from Management:</div>
                    <div class="fst-italic text-dark">
                        "{{ $increment->letter_notes }}"
                    </div>
                </div>
            @endif

            <p class="mt-3">
                All other terms and conditions of your employment contract remain unaltered.
                Kindly note that your salary details are strictly confidential between you and the organization.
            </p>

            <p>
                We appreciate your continued commitment and look forward to your valuable contributions to our collective growth in the upcoming year.
            </p>

            <p class="mt-4">
                Warm regards and best wishes,
            </p>

            <!-- Signature -->
            <div class="signature-section d-flex justify-content-between align-items-end">
                <div>
                    <div style="height: 45px;"></div>
                    <div class="fw-bold text-dark fs-14">Authorized Signatory</div>
                    <div class="text-muted fs-12">Human Resources Division</div>
                    <div class="text-muted fs-12">{{ $company_name }}</div>
                </div>
                <div class="text-end">
                    <div style="height: 45px;"></div>
                    <div class="fw-bold text-dark fs-14">{{ $increment->employee->employee_name }}</div>
                    <div class="text-muted fs-12">Employee Signature &amp; Date</div>
                    <div class="text-muted fs-11">(I accept the revised terms)</div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>

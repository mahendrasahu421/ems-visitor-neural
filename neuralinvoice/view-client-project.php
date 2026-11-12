<?php
// Start output buffering at the very beginning
ob_start();

include 'EvaraFroentend/layout/head.php';
include 'includes/auth.php';
include 'includes/helpers.php';

$id = (int) ($_GET['id'] ?? 0);

// First check if DelStatus column exists
$checkColumn = $con->query("SHOW COLUMNS FROM client_projects LIKE 'DelStatus'");
$hasDelStatus = ($checkColumn && $checkColumn->num_rows > 0);

// Build query based on whether DelStatus exists
if ($hasDelStatus) {
    $projectQuery = "SELECT p.*, c.ClientName, COALESCE(wtm.WorkTypeName, p.work_type) AS work_type_display
        FROM client_projects p
        JOIN clientmaster c ON p.client_id = c.ClientID
        LEFT JOIN work_type_master wtm ON p.work_type_id = wtm.WorkTypeID
        WHERE p.project_id = $id AND p.DelStatus = 1";
} else {
    $projectQuery = "SELECT p.*, c.ClientName, COALESCE(wtm.WorkTypeName, p.work_type) AS work_type_display
        FROM client_projects p
        JOIN clientmaster c ON p.client_id = c.ClientID
        LEFT JOIN work_type_master wtm ON p.work_type_id = wtm.WorkTypeID
        WHERE p.project_id = $id";
}

$p = $con->query($projectQuery)->fetch_assoc();

if (!$p) {
    // Try without work_type join
    if ($hasDelStatus) {
        $projectQuery = "SELECT p.*, c.ClientName, p.work_type AS work_type_display
            FROM client_projects p 
            JOIN clientmaster c ON p.client_id = c.ClientID 
            WHERE p.project_id = $id AND p.DelStatus = 1";
    } else {
        $projectQuery = "SELECT p.*, c.ClientName, p.work_type AS work_type_display
            FROM client_projects p 
            JOIN clientmaster c ON p.client_id = c.ClientID 
            WHERE p.project_id = $id";
    }
    $p = $con->query($projectQuery)->fetch_assoc();
}

if (!$p) {
    die('<div class="alert alert-danger">Project not found. <a href="client-project-list.php">Go back to list</a></div>');
}

$fyLabel = !empty($p['financial_year']) ? $p['financial_year'] : '';
$workingDays = isset($p['amc_working_days']) ? (int) $p['amc_working_days'] : 0;
if ($workingDays <= 0 && !empty($p['amc_start']) && !empty($p['amc_end'])) {
    $workingDays = ni_count_working_days($p['amc_start'], $p['amc_end']);
}

// Handle payment marking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_paid'])) {
    $mid = (int) $_POST['milestone_id'];
    $invoiceId = (int) ($_POST['invoice_id'] ?? 0);
    $receivedAmount = round((float) ($_POST['paid_amount'] ?? 0), 2);
    $tdsAmount = round((float) ($_POST['tds_amount'] ?? 0), 2);
    $paymentNote = $con->real_escape_string($_POST['payment_note'] ?? '');
    $receivedDate = $con->real_escape_string($_POST['received_date'] ?? date('Y-m-d'));
    $paymentCredit = round($receivedAmount + $tdsAmount, 2);

    $con->begin_transaction();
    try {
        // 1. Update Milestone Table
        $checkPartialCol = $con->query("SHOW COLUMNS FROM project_payment_milestones LIKE 'partial_paid'");
        $hasPartialCol = ($checkPartialCol && $checkPartialCol->num_rows > 0);

        // Determine if this payment completes the milestone
        $mReq = $con->query("SELECT amount, amount_with_tax, paid_amount FROM project_payment_milestones WHERE milestone_id = $mid");
        $mRow = $mReq->fetch_assoc();
        $mTarget = (float)($mRow['amount_with_tax'] > 0 ? $mRow['amount_with_tax'] : $mRow['amount']);
        $newTotalPaid = (float)$mRow['paid_amount'] + $paymentCredit;
        $isMilestoneDone = ($newTotalPaid + 0.01 >= $mTarget) ? 1 : 0;

        $msUpdate = "UPDATE project_payment_milestones SET 
            is_paid = $isMilestoneDone,
            paid_amount = paid_amount + $paymentCredit,
            last_paid_date = '$receivedDate'
            WHERE milestone_id = $mid";
        $con->query($msUpdate);

        // 2. Insert or Update recived_payment (for Ledger)
        $existingPaymentId = 0;
        if ($invoiceId > 0) {
            $checkPay = $con->query("SELECT id FROM recived_payment WHERE workAmount_invoice_id = $invoiceId LIMIT 1");
            if ($checkPay && $row = $checkPay->fetch_assoc()) {
                $existingPaymentId = (int)$row['id'];
            }
        }

        if ($existingPaymentId > 0) {
            // Update existing record
            $noteEsc = $con->real_escape_string($paymentNote);
            $checkPay = $con->query("SELECT recived_amount, received_net FROM recived_payment WHERE id = $existingPaymentId")->fetch_assoc();
            $currentNet = round((float)($checkPay['received_net'] > 0 ? $checkPay['received_net'] : $checkPay['recived_amount']), 2);
            
            $updateRec = "UPDATE recived_payment SET 
                recived_amount = recived_amount + $receivedAmount,
                tds_amount = tds_amount + $tdsAmount,
                received_net = $currentNet + $paymentCredit,
                payment_note = CASE 
                    WHEN payment_note IS NULL OR payment_note = '' THEN '$noteEsc' 
                    ELSE CONCAT(payment_note, ' | ', '$noteEsc') 
                END,
                received_date = '$receivedDate',
                status = 1
                WHERE id = $existingPaymentId";
            $con->query($updateRec);
        } else {
            // Create new record
            // Fetch the existing Proforma Invoice number to reuse it
            $invNumRes = $con->query("SELECT invoiceNumber FROM workamount WHERE invoice_id = $invoiceId LIMIT 1");
            $invNumRow = $invNumRes ? $invNumRes->fetch_assoc() : null;
            $taxInvoiceNumber = $invNumRow ? $invNumRow['invoiceNumber'] : '';
            
            $invIdVal = $invoiceId > 0 ? $invoiceId : "NULL";
            $sqlRec = "INSERT INTO recived_payment 
                (ClientID, workAmount_invoice_id, taxinvoiceNumber, recived_amount, tds_amount, received_net, payment_note, received_date, status, created_at)
                VALUES 
                ({$p['client_id']}, $invIdVal, '$taxInvoiceNumber', $receivedAmount, $tdsAmount, $paymentCredit, '$paymentNote', '$receivedDate', 1, NOW())";
            $con->query($sqlRec);
        }

        // 3. Update Invoice status if linked
        if ($invoiceId > 0) {
            $invTotalRes = $con->query("SELECT totalAmount FROM invoice WHERE id = $invoiceId");
            $invTotal = (float)$invTotalRes->fetch_assoc()['totalAmount'];
            
            $paidSumRes = $con->query("SELECT SUM(received_net) as total_paid FROM recived_payment WHERE workAmount_invoice_id = $invoiceId");
            $currentPaidSum = (float)$paidSumRes->fetch_assoc()['total_paid'];
            
            $newInvStatus = ($currentPaidSum + 0.01 >= $invTotal) ? 2 : 1;
            $con->query("UPDATE invoice SET status = $newInvStatus WHERE id = $invoiceId");
        }

        $con->commit();
        ob_end_clean();
        header("Location: view-client-project.php?id=$id&updated=1");
        exit;
    } catch (Exception $e) {
        $con->rollback();
        die("Payment failed: " . $e->getMessage());
    }
}

// Handle adding additional work/milestone
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_extra_work'])) {
    $label = $con->real_escape_string($_POST['extra_label']);
    $amount = (float)$_POST['extra_amount'];
    $dueDate = $con->real_escape_string($_POST['extra_due_date']);
    
    if ($amount > 0 && !empty($label)) {
        $con->begin_transaction();
        try {
            // 1. Get current project totals
            $currentValue = (float)$p['total_value'];
            $newBaseValue = $currentValue + $amount;
            
            // 2. Calculate new tax details using helper
            $taxData = ni_compute_gst_for_client_amount($con, (int)$p['client_id'], $newBaseValue);
            
            // 3. Update Project Table
            $updateProject = "UPDATE client_projects SET 
                total_value = $newBaseValue,
                tax_amount = {$taxData['total_tax_amount']},
                cgst_amount = {$taxData['cgst_amount']},
                sgst_amount = {$taxData['sgst_amount']},
                igst_amount = {$taxData['igst_amount']},
                total_value_with_tax = {$taxData['total_amount']},
                total_with_tax = {$taxData['total_amount']}
                WHERE project_id = $id";
            $con->query($updateProject);
            
            // 4. Insert new Milestone
            // Calculate tax for this specific milestone too
            $mTax = ni_compute_gst_for_client_amount($con, (int)$p['client_id'], $amount);
            $msSql = "INSERT INTO project_payment_milestones 
                (project_id, milestone_label, amount, tax_amount, amount_with_tax, due_date, sort_order)
                VALUES ($id, '$label', $amount, {$mTax['total_tax_amount']}, {$mTax['total_amount']}, '$dueDate', 99)";
            $con->query($msSql);
            
            $con->commit();
            header("Location: view-client-project.php?id=$id&updated=1");
            exit;
        } catch (Exception $e) {
            $con->rollback();
            die("Error adding extra work: " . $e->getMessage());
        }
    }
}

// Ensure invoice table has milestone_id for linking
$checkMilestoneCol = $con->query("SHOW COLUMNS FROM invoice LIKE 'milestone_id'");
if ($checkMilestoneCol && $checkMilestoneCol->num_rows === 0) {
    $con->query("ALTER TABLE invoice ADD COLUMN milestone_id INT NULL AFTER ClientID");
}

// Ensure project_payment_milestones table has invoiced_amount for partial tracking
$checkInvoicedCol = $con->query("SHOW COLUMNS FROM project_payment_milestones LIKE 'invoiced_amount'");
if ($checkInvoicedCol && $checkInvoicedCol->num_rows === 0) {
    $con->query("ALTER TABLE project_payment_milestones ADD COLUMN invoiced_amount DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER amount_with_tax");
}

// Fetch milestones with linked invoice sent status
$milestones = $con->query("SELECT m.*, i.id AS linked_inv_id, i.sent_at AS inv_sent_at, p.tax_percent 
                           FROM project_payment_milestones m 
                           LEFT JOIN invoice i ON m.milestone_id = i.milestone_id 
                           JOIN client_projects p ON m.project_id = p.project_id
                           WHERE m.project_id = $id 
                           ORDER BY m.sort_order, m.milestone_id");

// Calculate totals
$totalAmount = 0;
$totalPaid = 0;
$totalPending = 0;
$milestonesData = [];
if ($milestones && $milestones->num_rows > 0) {
    while ($row = $milestones->fetch_assoc()) {
        $milestoneTotalBase = (float)$row['amount']; // Base amount of the milestone
        $milestoneTotalWithTax = (float)$row['amount_with_tax']; // Total with tax of the milestone
        $milestoneInvoicedBase = (float)($row['invoiced_amount'] ?? 0); // Base amount already invoiced

        $baseAmount = $milestoneTotalWithTax > 0 ? $milestoneTotalWithTax : $milestoneTotalBase; // Use total with tax for display
        $totalAmount += $baseAmount; // Total project value (sum of all milestones)

        $paidAmount = (float) $row['paid_amount']; // Actual cash received
        $totalPaid += $paidAmount;
        $totalPending += max(0, $baseAmount - $paidAmount);

        $milestonesData[] = $row;
    }
    // Reset pointer
    $milestones->data_seek(0);
}

// Now include header after all processing
include 'EvaraFroentend/layout/header.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .project-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 15px;
        margin-bottom: 25px;
    }

    .project-header h2 {
        margin: 0 0 10px 0;
        font-weight: 600;
    }

    .info-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        border-left: 4px solid #3bb77e;
        transition: transform 0.2s;
    }

    .info-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .info-label {
        font-size: 12px;
        text-transform: uppercase;
        color: #6c757d;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
        margin-top: 5px;
    }

    .badge-status {
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-active {
        background: #d4edda;
        color: #155724;
    }

    .badge-pending {
        background: #fff3cd;
        color: #856404;
    }

    .badge-paid {
        background: #cce5ff;
        color: #004085;
    }

    .payment-summary {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 25px;
    }

    .summary-box {
        text-align: center;
        padding: 10px;
    }

    .summary-box .amount {
        font-size: 28px;
        font-weight: bold;
    }

    .summary-box .label {
        font-size: 12px;
        opacity: 0.9;
        margin-top: 5px;
    }

    .milestone-table {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .milestone-table thead {
        background: #3bb77e;
        color: white;
    }

    .milestone-table th {
        padding: 15px;
        font-weight: 500;
    }

    .milestone-table td {
        padding: 12px 15px;
        vertical-align: middle;
    }

    .status-paid {
        background: #d4edda;
        color: #155724;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 500;
        display: inline-block;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 500;
        display: inline-block;
    }

    .status-partial {
        background: #cce5ff;
        color: #004085;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 500;
        display: inline-block;
    }

    .btn-sm-custom {
        padding: 5px 12px;
        font-size: 12px;
        border-radius: 20px;
    }

    .progress-bar-custom {
        height: 8px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.3);
        overflow: hidden;
        margin: 10px 0;
    }

    .progress-fill {
        height: 100%;
        background: white;
        border-radius: 10px;
        transition: width 0.3s ease;
    }

    .amount-paid {
        color: #28a745;
        font-weight: 600;
    }

    .amount-pending {
        color: #dc3545;
        font-weight: 600;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .partial-payment-form {
        display: none;
        margin-top: 10px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .btn-print {
        background: #6c757d;
        color: white;
    }

    .btn-print:hover {
        background: #5a6268;
        color: white;
    }

    @media print {

        .btn,
        .action-buttons,
        .btn-print,
        .alert,
        .btn-close {
            display: none !important;
        }

        .info-card {
            break-inside: avoid;
        }

        .project-header {
            background: #667eea !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .payment-summary {
            background: #11998e !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<div class="container mt-15 ni-page">
    <!-- Header Section -->
    <div class="project-header">
        <div class="d-flex justify-content-between align-items-start flex-wrap">
            <div>
                <h2><i class="fas fa-project-diagram"></i> <?php echo htmlspecialchars($p['project_name']); ?></h2>
                <p class="mb-0"><i class="fas fa-user"></i> Client: <?php echo htmlspecialchars($p['ClientName']); ?>
                </p>
                <small><i class="fas fa-hashtag"></i> Project ID: #<?php echo $id; ?></small>
            </div>
            <div class="mt-2 mt-md-0">
                <button onclick="window.print()" class="btn btn-light btn-sm-custom">
                    <i class="fas fa-print"></i> Print
                </button>
                <button type="button" class="btn btn-success btn-sm-custom" data-bs-toggle="modal" data-bs-target="#extraWorkModal">
                    <i class="fas fa-plus"></i> Add Extra Work
                </button>
                <a href="client-project-edit.php?id=<?php echo $id; ?>" class="btn btn-warning btn-sm-custom">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="client-project-list.php" class="btn btn-light btn-sm-custom">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <?php if (!empty($_GET['updated'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> Payment updated successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php $eng = $p['engagement_type'] ?? 'project'; ?>
    <?php if ($eng === 'amc_only'): ?>
        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle"></i> <strong>AMC Only</strong> — Project development hamare paas nahi; sirf AMC
            manage ho rahi hai.
        </div>
    <?php endif; ?>

    <!-- Payment Summary Cards -->
    <div class="payment-summary">
        <div class="row">
            <div class="col-md-4">
                <div class="summary-box">
                    <div class="amount">₹<?php echo number_format($totalAmount, 2); ?></div>
                    <div class="label">Total Project Value (incl. tax)</div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="summary-box">
                    <div class="amount amount-paid">₹<?php echo number_format($totalPaid, 2); ?></div>
                    <div class="label">Total Paid</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-box">
                    <div class="amount amount-pending">₹<?php echo number_format($totalPending, 2); ?></div>
                    <div class="label">Pending Amount</div>
                </div>
            </div>
        </div>
        <div class="progress-bar-custom">
            <div class="progress-fill"
                style="width: <?php echo $totalAmount > 0 ? ($totalPaid / $totalAmount * 100) : 0; ?>%"></div>
        </div>
        <div class="text-center mt-2">
            <small><?php echo $totalAmount > 0 ? round($totalPaid / $totalAmount * 100, 1) : 0; ?>% Completed</small>
        </div>
    </div>

    <div class="row">
        <!-- Basic Information -->
        <div class="col-md-6">
            <div class="info-card">
                <h6><i class="fas fa-info-circle"></i> Basic Information</h6>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <div class="info-label">Work Type</div>
                        <div class="info-value">
                            <?php echo htmlspecialchars($p['work_type_display'] ?? $p['work_type'] ?? '—'); ?></div>
                    </div>
                    <div class="col-6">
                        <div class="info-label">Engagement Type</div>
                        <div class="info-value">
                            <span
                                class="badge-status <?php echo $eng === 'amc_only' ? 'badge-active' : 'badge-pending'; ?>">
                                <?php echo ucfirst($eng); ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-label">Financial Year</div>
                        <div class="info-value"><?php echo $fyLabel ? 'FY ' . htmlspecialchars($fyLabel) : '—'; ?></div>
                    </div>
                    <div class="col-6">
                        <div class="info-label">Working Days</div>
                        <div class="info-value"><?php echo (int) $workingDays; ?> <small
                                class="text-muted">(Mon–Fri)</small></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Information -->
        <div class="col-md-6">
            <div class="info-card">
                <h6><i class="fas fa-chart-line"></i> Financial Details</h6>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <div class="info-label">Total Value (excl. tax)</div>
                        <div class="info-value">₹<?php echo number_format($p['total_value'], 2); ?></div>
                    </div>
                    <div class="col-6">
                        <div class="info-label">Total Value (incl. tax)</div>
                        <div class="info-value">
                            ₹<?php echo number_format($p['total_value_with_tax'] ?? $p['total_value'], 2); ?></div>
                    </div>
                    <div class="col-6">
                        <div class="info-label">Tax Amount</div>
                        <div class="info-value text-success">
                            ₹<?php echo number_format(($p['total_value_with_tax'] ?? $p['total_value']) - $p['total_value'], 2); ?>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-label">Payment Mode</div>
                        <div class="info-value"><?php echo ucfirst($p['payment_mode'] ?? 'Percentage'); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Duration -->
        <div class="col-md-6">
            <div class="info-card">
                <h6><i class="fas fa-calendar-alt"></i> Project Duration</h6>
                <hr>
                <div class="row">
                    <div class="col-4">
                        <div class="info-label">Start Date</div>
                        <div class="info-value"><?php echo $p['project_start_date'] ? date('d-m-Y', strtotime($p['project_start_date'])) : '—'; ?></div>
                    </div>
                    <div class="col-4">
                        <div class="info-label">End Date</div>
                        <div class="info-value"><?php echo $p['project_end_date'] ? date('d-m-Y', strtotime($p['project_end_date'])) : '—'; ?></div>
                    </div>
                    <div class="col-4">
                        <div class="info-label">Duration</div>
                        <div class="info-value">
                            <?php echo $p['project_duration_days'] ? $p['project_duration_days'] . ' days' : '—'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- AMC Period -->
        <div class="col-md-6">
            <div class="info-card">
                <h6><i class="fas fa-clock"></i> AMC Period</h6>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <div class="info-label">AMC Start Date</div>
                        <div class="info-value"><?php echo $p['amc_start'] ? date('d-m-Y', strtotime($p['amc_start'])) : '—'; ?></div>
                    </div>
                    <div class="col-6">
                        <div class="info-label">AMC End Date</div>
                        <div class="info-value"><?php echo $p['amc_end'] ? date('d-m-Y', strtotime($p['amc_end'])) : '—'; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes Section -->
    <?php if ($p['notes']): ?>
        <div class="info-card">
            <h6><i class="fas fa-sticky-note"></i> Notes</h6>
            <hr>
            <p class="mb-0"><?php echo nl2br(htmlspecialchars($p['notes'])); ?></p>
        </div>
    <?php endif; ?>

    <!-- Payment Milestones Table -->
    <div class="info-card">
        <h6><i class="fas fa-tasks"></i> Payment Milestones</h6>
        <hr>
        <?php if ($milestones && $milestones->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table milestone-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Milestone</th>
                            <th>Module</th>
                            <th>%</th>
                            <th>Amount (₹)</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Payment Details</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $counter = 1;
                        $milestones->data_seek(0);
                        while ($m = $milestones->fetch_assoc()): 
                            $milestoneTotalBase = (float)$m['amount'];
                            $milestoneTotalWithTax = (float)$m['amount_with_tax'];
                            $milestoneInvoicedBase = (float)($m['invoiced_amount'] ?? 0);
                            $milestonePaidAmount = (float)$m['paid_amount'];
                            $projectTaxPercent = (float)$m['tax_percent'];

                            // Amount to display for the milestone (total with tax)
                            $displayAmount = $milestoneTotalWithTax > 0 ? $milestoneTotalWithTax : $milestoneTotalBase;

                            // Calculate remaining base amount to be invoiced
                            $remainingBaseToInvoice = max(0, $milestoneTotalBase - $milestoneInvoicedBase);
                            // Calculate remaining amount with tax to be invoiced
                            $remainingWithTaxToInvoice = max(0, $milestoneTotalBase * (1 + $projectTaxPercent / 100) - ($milestoneInvoicedBase * (1 + $projectTaxPercent / 100)));

                            // Amount to prefill in the invoice form (remaining base amount)
                            $prefillInvoiceAmount = $remainingBaseToInvoice;
                            $prefillInvoicePct = $p['total_value'] > 0 ? ($remainingBaseToInvoice / $p['total_value']) * 100 : 0;

                            $pendingPaymentAmount = max(0, $displayAmount - $milestonePaidAmount);
                            $isFullyPaid = $milestonePaidAmount >= $displayAmount;
                            $isFullyInvoiced = $milestoneInvoicedBase >= $milestoneTotalBase - 0.01; // Allow for floating point inaccuracies
                            
                            if ($isFullyPaid) {
                                $statusClass = 'status-paid';
                                $statusText = '<i class="fas fa-check-circle"></i> Paid';
                            } elseif ($paidAmount > 0) {
                                $statusClass = 'status-partial';
                                $statusText = '<i class="fas fa-clock"></i> Partial (' . round($paidAmount / $m['amount'] * 100) . '%)';
                            } else {
                                $statusClass = 'status-pending';
                                $statusText = '<i class="fas fa-hourglass-half"></i> Pending';
                            }
                            ?>
                            <tr class="<?php echo $isFullyPaid ? 'table-success' : ($paidAmount > 0 ? 'table-info' : ''); ?>">
                                <td><?php echo $counter++; ?></td>
                                <td><strong><?php echo htmlspecialchars($m['milestone_label']); ?></strong></td>
                                <td><?php echo htmlspecialchars($m['module_name'] ?? '-'); ?></td>
                                <td><?php echo $m['percentage'] !== null ? $m['percentage'] . '%' : '-'; ?></td>
                                <td>₹<?php echo number_format(isset($m['amount_with_tax']) && (float) $m['amount_with_tax'] > 0 ? $m['amount_with_tax'] : $m['amount'], 2); ?>
                                </td>

                                <td><?php echo $m['due_date'] ? date('d-m-Y', strtotime($m['due_date'])) : '-'; ?></td>
                                <td><span class="<?php echo $statusClass; ?>"><?php echo $statusText; ?></span></td>
                                <td>
                                    <?php if ($paidAmount > 0): ?>
                                        <small>
                                            <strong>Paid:</strong> ₹<?php echo number_format($paidAmount, 2); ?>
                                            <?php if (!$isFullyPaid): ?>
                                                <br><strong>Pending:</strong> ₹<?php echo number_format($pendingAmount, 2); ?>
                                            <?php endif; ?>
                                            <?php if ($m['paid_date'] || ($m['last_paid_date'] ?? false)): ?>
                                                <br><span class="text-muted">on
                                                    <?php echo date('d-m-Y', strtotime($m['paid_date'] ?: ($m['last_paid_date'] ?? date('Y-m-d')))); ?></span>
                                            <?php endif; ?>
                                        </small>
                                    <?php else: ?>
                                        <small class="text-muted">Not paid yet</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!$isFullyPaid): ?>
                                        <div class="action-buttons mb-2">
                                            <?php if (!empty($m['linked_inv_id'])): ?>
                                                <button class="btn btn-sm btn-success btn-sm-custom"
                                                    onclick="showPaymentForm(<?php echo $m['milestone_id']; ?>, <?php echo $pendingPaymentAmount; ?>, <?php echo $m['linked_inv_id']; ?>)">
                                                    <i class="fas fa-money-bill-wave"></i> Pay
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-secondary btn-sm-custom" disabled title="Create Invoice first to record payment">
                                                    <i class="fas fa-money-bill-wave"></i> Pay
                                                </button>
                                            <?php endif; ?>

                                            <?php if (!empty($m['linked_inv_id'])): ?>
                                                <!-- Invoice already exists, show Preview instead of Create -->
                                                <a href="view-invoice.php?invoice_id=<?php echo $m['linked_inv_id']; ?>"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Preview Invoice
                                                </a>
                                                <?php if (!empty($m['inv_sent_at'])): ?>
                                                    <div class="mt-1 small text-success"><i class="fas fa-check-double"></i> Issued: <?php echo date('d-m-Y', strtotime($m['inv_sent_at'])); ?></div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <a href="create_invoice.php?milestone_id=<?php echo $m['milestone_id']; ?>"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-file-invoice"></i> Create Invoice
                                                </a>
                                            <?php endif; ?>
                                        </div>

                                        <div id="paymentForm_<?php echo $m['milestone_id']; ?>" class="partial-payment-form">
                                            <form method="post">
                                                <input type="hidden" name="milestone_id" value="<?php echo $m['milestone_id']; ?>">
                                                <input type="hidden" name="invoice_id" value="<?php echo $m['linked_inv_id']; ?>">
                                                <input type="hidden" name="invoice_id" id="payment_invoice_id_<?php echo $m['milestone_id']; ?>" value="">

                                                <div class="mb-2">
                                                    <label class="small">Received Amount (₹)</label>
                                                    <input type="number" name="paid_amount" class="form-control form-control-sm"
                                                        value="<?php echo $pendingPaymentAmount; ?>" required>
                                                </div>

                                                <div class="mb-2">
                                                    <label class="small">TDS Deducted (₹)</label>
                                                    <input type="number" step="0.01" name="tds_amount" class="form-control form-control-sm"
                                                        value="0.00">
                                                </div>

                                                <div class="mb-2">
                                                    <label class="small">Received Date</label>
                                                    <input type="date" name="received_date" class="form-control form-control-sm"
                                                        value="<?php echo date('Y-m-d'); ?>" required>
                                                </div>

                                                <div class="mb-2">
                                                    <label class="small">Payment Note</label>
                                                    <input type="text" name="payment_note" class="form-control form-control-sm" 
                                                        placeholder="Ref No / Bank Name">
                                                </div>

                                                <button type="submit" name="mark_paid" class="btn btn-sm btn-success w-100">
                                                    Submit Payment
                                                </button>

                                                <button type="button" class="btn btn-sm btn-secondary w-100 mt-1"
                                                    onclick="hidePaymentForm(<?php echo $m['milestone_id']; ?>)">
                                                    Cancel
                                                </button>
                                            </form>
                                        </div>

                                    <?php else: ?>
                                        <span class="text-success d-block mb-2">
                                            <i class="fas fa-check"></i> Completed
                                        </span>

                                        <!-- View Invoice -->
                                        <a href="view-invoice.php?invoice_id=<?php echo $m['linked_inv_id']; ?>"
                                            class="btn btn-sm btn-info mb-1">
                                            <i class="fas fa-eye"></i> Preview Invoice
                                        </a>

                                        <!-- Download Invoice -->
                                        <a href="view-invoice.php?invoice_id=<?php echo $m['linked_inv_id']; ?>"
                                            class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-download"></i> Download Invoice
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot style="background: #f8f9fa; font-weight: bold;">
                        <tr>
                            <td colspan="4" class="text-end">Total:</td>
                            <td>₹<?php echo number_format($totalAmount, 2); ?></td>
                            <td></td>
                            <td colspan="3">Paid: ₹<?php echo number_format($totalPaid, 2); ?> | Pending:
                                ₹<?php echo number_format($totalPending, 2); ?> (incl. tax)</td>

                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> No payment milestones found for this project.
            </div>
        <?php endif; ?>
    </div>

    <!-- Proposal Download -->
    <?php if (!empty($p['proposal_file'])): ?>
        <div class="info-card">
            <h6><i class="fas fa-file-pdf"></i> Proposal Document</h6>
            <hr>
            <a href="<?php echo htmlspecialchars($p['proposal_file']); ?>"
                class="btn btn-primary btn-sm-custom">
                <i class="fas fa-download"></i> Download Proposal
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Modal for Extra Work -->
<div class="modal fade" id="extraWorkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Add Additional Work / Change Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Work Description</label>
                        <input type="text" name="extra_label" class="form-control" placeholder="e.g. Additional API Integration" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount (Base Value ₹)</label>
                        <input type="number" step="0.01" name="extra_amount" class="form-control" required>
                        <small class="text-muted">Tax (GST) will be calculated automatically.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="extra_due_date" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add_extra_work" class="btn btn-primary">Add to Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function showPaymentForm(milestoneId, pendingAmount, invoiceId) {
        // Hide all other forms first
        $('.partial-payment-form').hide();
        // Show the selected form
        $('#paymentForm_' + milestoneId).show();
        $('#payment_invoice_id_' + milestoneId).val(invoiceId); // Set the invoice_id in the form
        // Set the amount field to pending amount
        $('#paymentForm_' + milestoneId + ' input[name="paid_amount"]').val(pendingAmount);
    }

    function hidePaymentForm(milestoneId) {
        $('#paymentForm_' + milestoneId).hide();
    }

    // Auto-hide alerts after 3 seconds
    $(document).ready(function () {
        setTimeout(function () {
            $('.alert').fadeOut('slow');
        }, 3000);
    });
</script>

<?php include 'EvaraFroentend/layout/footer.php'; ?>
<?php ob_end_flush(); ?>

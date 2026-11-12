<?php
include 'includes/auth.php';
include 'connection.php';
include 'includes/helpers.php';

$invoiceId = (int) ($_GET['invoice_id'] ?? $_POST['invoice_id'] ?? 0);
if ($invoiceId <= 0) {
    header('Location: client-invoice-list.php?error=invalid_invoice');
    exit;
}

$invoiceRes = $con->query("SELECT i.*, cm.ClientName FROM invoice i
    LEFT JOIN clientmaster cm ON i.ClientID = cm.ClientID
    WHERE i.id = $invoiceId
    LIMIT 1");
$invoice = $invoiceRes ? $invoiceRes->fetch_assoc() : null;
if (!$invoice) {
    header('Location: client-invoice-list.php?error=invoice_not_found');
    exit;
}

if ((int) ($invoice['status'] ?? 0) === 3) {
    header('Location: client-invoice-list.php?error=cancelled_invoice_edit_blocked');
    exit;
}

$lineRes = $con->query("SELECT * FROM workamount WHERE invoice_id = $invoiceId ORDER BY id");
$lines = [];
while ($lineRes && $row = $lineRes->fetch_assoc()) {
    $lines[] = $row;
}
$invoiceNumber = $lines[0]['invoiceNumber'] ?? '';

$clients = [];
$clientRes = $con->query("SELECT ClientID, ClientName FROM clientmaster WHERE DelStatus = 1 ORDER BY ClientName");
while ($clientRes && $row = $clientRes->fetch_assoc()) {
    $clients[] = $row;
}

$projectMilestones = [];
$projectName = '';
$projectTotal = 0.0;
$milestoneId = (int) ($invoice['milestone_id'] ?? 0);
if ($milestoneId > 0) {
    $msql = "SELECT m.*, p.project_name, p.total_value
        FROM project_payment_milestones m
        JOIN client_projects p ON m.project_id = p.project_id
        WHERE m.project_id = (SELECT project_id FROM project_payment_milestones WHERE milestone_id = $milestoneId)
        ORDER BY m.sort_order, m.milestone_id";
    $mres = $con->query($msql);
    while ($mres && $mrow = $mres->fetch_assoc()) {
        $projectMilestones[] = $mrow;
        $projectName = $mrow['project_name'] ?? '';
        $projectTotal = (float) ($mrow['total_value'] ?? 0);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invoiceType = (int) ($_POST['invoice_type'] ?? ($invoice['invoice_type'] ?? 1));
    $clientId = (int) ($_POST['client_id'] ?? ($invoice['ClientID'] ?? 0));
    $postedInvoiceNumber = $invoiceType === 2
        ? trim((string) ($_POST['oldInvoiceNumber'] ?? $invoiceNumber))
        : trim((string) ($_POST['newInvoiceNumber'] ?? $invoiceNumber));
    if ($postedInvoiceNumber === '') {
        $postedInvoiceNumber = $invoiceNumber ?: ('INV-' . $invoiceId);
    }
    $invoiceDate = trim($_POST['invoiceDate'] ?? '');
    $concern = trim($_POST['concern_person'] ?? '');
    $mobile = preg_replace('/\D+/', '', (string) ($_POST['mobile'] ?? ''));
    $terms = trim($_POST['condition'] ?? '');
    $deliveryDate = trim($_POST['deliveryDate'] ?? '');
    $dueDate = trim($_POST['deudate'] ?? '');
    $discount = max(0, (float) ($_POST['discount'] ?? 0));
    $descriptions = $_POST['discription'] ?? [];
    $durations = $_POST['duration_days'] ?? [];
    $amounts = $_POST['Amount'] ?? [];
    $err = '';

    if ($clientId <= 0) {
        $err = 'Client required.';
    } elseif ($invoiceDate === '' || $concern === '' || $mobile === '' || $deliveryDate === '' || $dueDate === '') {
        $err = 'Required fields missing.';
    } elseif (!is_array($descriptions) || !is_array($durations) || !is_array($amounts)) {
        $err = 'Invalid line items.';
    }

    $cleanLines = [];
    $subtotal = 0.0;
    if ($err === '') {
        foreach ($amounts as $idx => $amountRaw) {
            $description = trim((string) ($descriptions[$idx] ?? ''));
            $duration = trim((string) ($durations[$idx] ?? ''));
            $amount = round((float) $amountRaw, 2);
            if ($description === '' && $duration === '' && $amount <= 0) {
                continue;
            }
            if ($description === '' || $duration === '' || $amount <= 0) {
                $err = 'Each line needs description, duration and amount.';
                break;
            }
            $cleanLines[] = [
                'description' => $description,
                'duration' => $duration,
                'amount' => $amount,
            ];
            $subtotal += $amount;
        }
        if ($err === '' && empty($cleanLines)) {
            $err = 'At least one invoice line is required.';
        }
    }

    if ($err !== '') {
        header('Location: edit-invoice.php?invoice_id=' . $invoiceId . '&error=' . urlencode($err));
        exit;
    }

    $finalAmount = round($subtotal - $discount, 2);
    $tax = ni_compute_gst_for_client_amount($con, $clientId, $finalAmount);
    $sgstAmount = (float) $tax['sgst_amount'];
    $cgstAmount = (float) $tax['cgst_amount'];
    $igstAmount = (float) $tax['igst_amount'];
    $bankChargePercent = (float) ($tax['bank_charge_percent'] ?? 0);
    $bankChargeAmount = (float) ($tax['bank_charge_amount'] ?? 0);
    $totalAmount = round($finalAmount + (float) $tax['total_tax_amount'] + $bankChargeAmount, 2);
    $currency = strtoupper(trim((string) ($_POST['currency'] ?? ($invoice['currency'] ?? 'INR'))));
    $currency = $currency === 'USD' ? 'USD' : 'INR';
    $exchangeRate = max(0, (float) ($_POST['exchange_rate'] ?? ($invoice['exchange_rate'] ?? 1)));
    $totalAmountForeign = ($currency === 'USD' && $exchangeRate > 0) ? round($totalAmount / $exchangeRate, 2) : 0.0;
    $durationDays = implode(', ', array_filter(array_map(fn($v) => trim((string) $v), $durations)));

    $con->begin_transaction();
    try {
        $stmt = $con->prepare("UPDATE invoice SET
            invoice_type = ?,
            ClientID = ?,
            CompanyID = ?,
            milestone_id = ?,
            invoiceDate = ?,
            concern = ?,
            mobile = ?,
            terms = ?,
            deliverydate = ?,
            duedate = ?,
            duration_days = ?,
            subtotal = ?,
            discount = ?,
            finalAmount = ?,
            sgstAmount = ?,
            cgstAmount = ?,
            igstAmount = ?,
            bankChargePercent = ?,
            bankChargeAmount = ?,
            totalAmount = ?,
            currency = ?,
            exchange_rate = ?,
            total_amount_foreign = ?
            WHERE id = ?");
        $companyId = ni_company_id_for_client($con, $clientId);
        $postedMilestoneId = (int) ($_POST['milestone_id'] ?? ($invoice['milestone_id'] ?? 0));
        $stmt->bind_param(
            'iiiisssssssdddddddddsddi',
            $invoiceType,
            $clientId,
            $companyId,
            $postedMilestoneId,
            $invoiceDate,
            $concern,
            $mobile,
            $terms,
            $deliveryDate,
            $dueDate,
            $durationDays,
            $subtotal,
            $discount,
            $finalAmount,
            $sgstAmount,
            $cgstAmount,
            $igstAmount,
            $bankChargePercent,
            $bankChargeAmount,
            $totalAmount,
            $currency,
            $exchangeRate,
            $totalAmountForeign,
            $invoiceId
        );
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        $stmt->close();

        $con->query("DELETE FROM workamount WHERE invoice_id = $invoiceId");
        $stmtLine = $con->prepare("INSERT INTO workamount (invoice_id, invoiceNumber, description, amount, duration) VALUES (?, ?, ?, ?, ?)");
        foreach ($cleanLines as $line) {
            $stmtLine->bind_param('issds', $invoiceId, $postedInvoiceNumber, $line['description'], $line['amount'], $line['duration']);
            if (!$stmtLine->execute()) {
                throw new Exception($stmtLine->error);
            }
        }
        $stmtLine->close();

        if ($postedMilestoneId > 0) {
            $oldFinalAmount = (float) ($invoice['finalAmount'] ?? 0);
            $delta = $finalAmount - $oldFinalAmount;
            if (abs($delta) > 0.00001) {
                $stmtMs = $con->prepare("UPDATE project_payment_milestones SET invoiced_amount = GREATEST(0, invoiced_amount + ?) WHERE milestone_id = ?");
                $stmtMs->bind_param('di', $delta, $postedMilestoneId);
                $stmtMs->execute();
                $stmtMs->close();
            }
        }

        $con->commit();
        header('Location: client-invoice-list.php?success=invoice_updated');
        exit;
    } catch (Throwable $e) {
        $con->rollback();
        header('Location: edit-invoice.php?invoice_id=' . $invoiceId . '&error=' . urlencode('Invoice update failed.'));
        exit;
    }
}

if (empty($lines)) {
    $lines[] = ['description' => '', 'duration' => '', 'amount' => ''];
}

include 'EvaraFroentend/layout/head.php';
include 'EvaraFroentend/layout/header.php';
?>
<style>
    label {
        font-weight: 700;
    }
</style>
<div class="container mt-15">
    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Edit Prefarma Invoice</h3>
                    <a href="client-invoice-list.php" class="btn btn-sm btn-outline-secondary">Back To List</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($_GET['error'])): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                    <div class="alert alert-info">
                        Invoice: <strong><?php echo htmlspecialchars($invoiceNumber ?: ('#' . $invoiceId), ENT_QUOTES, 'UTF-8'); ?></strong>
                        | Client: <strong><?php echo htmlspecialchars($invoice['ClientName'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>
                    <form method="post" class="row g-3" id="editInvoiceForm">
                <input type="hidden" name="invoice_id" value="<?php echo (int) $invoiceId; ?>">
                <input type="hidden" name="milestone_id" value="<?php echo (int) ($invoice['milestone_id'] ?? 0); ?>">
                <input type="hidden" id="linked_project_total" value="<?php echo htmlspecialchars((string) $projectTotal, ENT_QUOTES, 'UTF-8'); ?>">

                <div class="col-md-3">
                    <label>Invoice Type</label>
                    <div class="form-check">
                        <input class="form-check-input invoiceType" type="radio" name="invoice_type" id="newInvoice" value="1" <?php echo (int) ($invoice['invoice_type'] ?? 1) === 2 ? '' : 'checked'; ?>>
                        <label class="form-check-label" for="newInvoice">New Invoice</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input invoiceType" type="radio" name="invoice_type" id="oldInvoice" value="2" <?php echo (int) ($invoice['invoice_type'] ?? 1) === 2 ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="oldInvoice">Old Invoice</label>
                    </div>
                </div>
                <div class="col-md-3" id="oldInvoiceTextbox">
                    <label>Old Invoice Number</label>
                    <input type="text" name="oldInvoiceNumber" class="form-control" id="oldInvoiceNumber" value="<?php echo htmlspecialchars($invoiceNumber, ENT_QUOTES, 'UTF-8'); ?>">
                    <span id="invoiceNumberMessage" style="color:red;"></span>
                </div>
                <div class="col-md-3" id="newInvoiceTextbox">
                    <label>New Invoice Number</label>
                    <input type="text" readonly name="newInvoiceNumber" class="form-control" id="newInvoiceNumber" value="<?php echo htmlspecialchars($invoiceNumber, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="col-md-3">
                    <label>To Client</label>
                    <select required class="form-control" name="client_id" id="clientDropdown">
                        <option value="0">---Select Client---</option>
                        <?php foreach ($clients as $client): ?>
                            <?php $cid = (int) ($client['ClientID'] ?? 0); ?>
                            <option value="<?php echo $cid; ?>" <?php echo $cid === (int) ($invoice['ClientID'] ?? 0) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars((string) ($client['ClientName'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <div id="clientDetails"></div>
                </div>

                <div class="card-header">
                    <h6 class="card-title">Self Details</h6>
                </div>
                <div class="col-md-4">
                    <label>Invoice Date</label>
                    <input type="date" name="invoiceDate" class="form-control" required value="<?php echo htmlspecialchars($invoice['invoiceDate'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="col-md-4">
                    <label>Concern Person</label>
                    <input type="text" name="concern_person" class="form-control" required value="<?php echo htmlspecialchars($invoice['concern'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="col-md-4">
                    <label>Mobile</label>
                    <input type="text" name="mobile" class="form-control" required value="<?php echo htmlspecialchars($invoice['mobile'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="col-md-4">
                    <label>Term & Condition</label>
                    <textarea name="condition" class="form-control" rows="6" required><?php echo htmlspecialchars($invoice['terms'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>
                <div class="col-md-4">
                    <label>Delivery Date</label>
                    <input type="date" name="deliveryDate" class="form-control" required value="<?php echo htmlspecialchars($invoice['deliverydate'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="col-md-4">
                    <label>Due Date</label>
                    <input type="date" name="deudate" class="form-control" required value="<?php echo htmlspecialchars($invoice['duedate'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <?php if ($milestoneId > 0): ?>
                    <div class="card-header">
                        <h6 class="card-title">Milestone Detail</h6>
                    </div>

                    <div class="col-md-12">
                        <?php if (!empty($projectMilestones)): ?>
                        <div class="alert alert-info py-2 mb-3">
                            <i class="fas fa-project-diagram"></i> Project:
                            <strong><?php echo htmlspecialchars($projectName, ENT_QUOTES, 'UTF-8'); ?></strong>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Milestone Label</th>
                                        <th>Amount (₹)</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($projectMilestones as $pm): ?>
                                        <tr class="<?php echo (int) $pm['milestone_id'] === $milestoneId ? 'table-primary border-primary' : ''; ?>">
                                            <td><strong><?php echo htmlspecialchars($pm['milestone_label'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong></td>
                                            <td>₹<?php echo number_format((float) ($pm['amount'] ?? 0), 2); ?></td>
                                            <td><?php echo !empty($pm['due_date']) ? date('d M Y', strtotime($pm['due_date'])) : '-'; ?></td>
                                            <td><?php echo !empty($pm['is_paid']) ? '<span class="badge bg-success">Paid</span>' : '<span class="badge bg-warning text-dark">Pending</span>'; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="row g-3" id="milestoneSection">
                            <div class="col-md-4">
                                <label>Milestone 1 (Readonly)</label>
                                <input type="text" class="form-control" name="milestone_1" id="milestone_1" readonly value="Milestone 1">
                            </div>
                            <div class="col-md-4">
                                <label>Milestone 1 Amount (Readonly)</label>
                                <input type="text" class="form-control" name="milestone_1_amount" id="milestone_1_amount" readonly value="0.00">
                            </div>
                            <div class="col-md-4">
                                <label>Milestone 1 Date (Readonly)</label>
                                <input type="date" class="form-control" name="milestone_1_date" id="milestone_1_date" readonly value="">
                            </div>
                            <div class="col-md-4">
                                <label>Milestone 2 (Readonly)</label>
                                <input type="text" class="form-control" name="milestone_2" id="milestone_2" readonly value="Phase 2">
                            </div>
                            <div class="col-md-4">
                                <label>Milestone 2 Amount (Readonly)</label>
                                <input type="text" class="form-control" name="milestone_2_amount" id="milestone_2_amount" readonly value="0.00">
                            </div>
                            <div class="col-md-4">
                                <label>Milestone 2 Date (Readonly)</label>
                                <input type="date" class="form-control" name="milestone_2_date" id="milestone_2_date" readonly value="">
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="col-12" id="discriptionSection">
                    <?php foreach ($lines as $idx => $line): ?>
                        <div class="row line-row">
                            <div class="col-md-3">
                                <label>Description</label>
                                <input type="text" name="discription[]" class="form-control" required value="<?php echo htmlspecialchars($line['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="col-md-2">
                                <label>Duration</label>
                                <input type="text" name="duration_days[]" class="form-control" required placeholder="e.g. 6 Days, 3 Months" value="<?php echo htmlspecialchars($line['duration'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="col-md-2">
                                <label>Bill %</label>
                                <?php $billPct = $projectTotal > 0 ? ((float) ($line['amount'] ?? 0) / $projectTotal) * 100 : 0; ?>
                                <input type="number" step="0.01" class="form-control pct-calc" name="bill_pct[]" placeholder="%" value="<?php echo $billPct > 0 ? htmlspecialchars(number_format($billPct, 2, '.', ''), ENT_QUOTES, 'UTF-8') : ''; ?>">
                            </div>
                            <div class="col-md-2">
                                <label>Amount</label>
                                <input type="number" step="0.01" name="Amount[]" class="form-control line-amount milestone-amt-field" required value="<?php echo htmlspecialchars((string) ($line['amount'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="col-md-3 mt-4">
                                <div class="btn btn-success addMore" <?php echo $idx === 0 ? '' : 'style="display: none;"'; ?>>Add More</div>
                                <div class="btn btn-danger remove" <?php echo $idx === 0 || count($lines) === 1 ? 'style="display: none;"' : ''; ?>>Remove</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="card-header">
                    <h6 class="card-title">Amount Detail</h6>
                </div>
                <div class="col-md-12">
                    <div class="row mt-2">
                        <div class="col-3"><label>Sub Total</label></div>
                        <div class="col-3"><input required type="text" name="subtotal" id="subtotal" class="" readonly value="<?php echo htmlspecialchars((string) ($invoice['subtotal'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>"></div>
                        <div class="col-3"><span class="ms-2 text-muted usd-equivalent-field" id="subtotal_usd"></span></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-3"><label>Discount</label></div>
                        <div class="col-3"><input type="number" step="0.01" name="discount" id="discount" class="" value="<?php echo htmlspecialchars((string) ($invoice['discount'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>"></div>
                        <div class="col-3"><span class="ms-2 text-muted usd-equivalent-field" id="discount_usd"></span></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-3"><label>Final Amount</label></div>
                        <div class="col-3"><input required type="text" name="finalAmount" id="finalAmount" class="" readonly value="<?php echo htmlspecialchars((string) ($invoice['finalAmount'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>"></div>
                        <div class="col-3"><span class="ms-2 text-muted usd-equivalent-field" id="finalAmount_usd"></span></div>
                    </div>
                    <div id="gst" style="display:none;">
                        <div class="row mt-2">
                            <div class="col-3"><label>SGST</label></div>
                            <div class="col-3"><input required type="text" name="sgstAmount" id="sgstAmount" class="" readonly value="<?php echo htmlspecialchars((string) ($invoice['sgstAmount'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>"></div>
                            <div class="col-3"><span class="ms-2 text-muted usd-equivalent-field" id="sgstAmount_usd"></span></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-3"><label>CGST</label></div>
                            <div class="col-3"><input required type="text" name="cgstAmount" id="cgstAmount" class="" readonly value="<?php echo htmlspecialchars((string) ($invoice['cgstAmount'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>"></div>
                            <div class="col-3"><span class="ms-2 text-muted usd-equivalent-field" id="cgstAmount_usd"></span></div>
                        </div>
                    </div>
                    <div class="row mt-2" id="igst" style="display:none;">
                        <div class="col-3"><label>IGST</label></div>
                        <div class="col-3"><input type="text" name="igstAmount" id="igstAmount" class="" readonly value="<?php echo htmlspecialchars((string) ($invoice['igstAmount'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>"></div>
                        <div class="col-3"><span class="ms-2 text-muted usd-equivalent-field" id="igstAmount_usd"></span></div>
                    </div>
                    <div class="row mt-2" id="bankChargeRow" style="display:none;">
                        <div class="col-3"><label>International Bank Charge @3.5%</label></div>
                        <div class="col-3">
                            <input type="text" name="bankChargeAmount" id="bankChargeAmount" class="" readonly value="<?php echo htmlspecialchars((string) ($invoice['bankChargeAmount'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="bankChargePercent" id="bankChargePercent" value="<?php echo htmlspecialchars((string) ($invoice['bankChargePercent'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-3"><span class="ms-2 text-muted usd-equivalent-field" id="bankChargeAmount_usd"></span></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-3"><label>Total Amount</label></div>
                        <div class="col-3"><input required type="text" name="totalAmount" id="totalAmount" class="" readonly value="<?php echo htmlspecialchars((string) ($invoice['totalAmount'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>"></div>
                        <div class="col-3"><span class="ms-2 text-muted usd-equivalent-field" id="totalAmount_usd"></span></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-3"><label>Convert INR to USD</label></div>
                        <div class="col-3">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" id="convertToUsd" <?php echo ($invoice['currency'] ?? 'INR') === 'USD' ? 'checked' : ''; ?>>
                                Show USD amount on invoice
                            </label>
                        </div>
                    </div>
                    <div class="row mt-2" id="currencyConversionRow" style="display:none;">
                        <div class="col-3"><label>Exchange Rate (1 USD = ? INR)</label></div>
                        <div class="col-3">
                            <input type="number" step="0.0001" name="exchange_rate" id="exchange_rate" class="form-control" value="<?php echo htmlspecialchars((string) ($invoice['exchange_rate'] ?? '83.5000'), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-3"><label>Total Amount (USD)</label></div>
                        <div class="col-3">
                            <input type="text" name="total_amount_foreign" id="total_amount_foreign" class="form-control" readonly value="<?php echo htmlspecialchars((string) ($invoice['total_amount_foreign'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>
                    <input type="hidden" name="currency" id="currency" value="<?php echo htmlspecialchars((string) ($invoice['currency'] ?? 'INR'), ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Update Invoice</button>
                    <a href="view-invoice.php?invoice_id=<?php echo (int) $invoiceId; ?>" class="btn btn-outline-secondary">Preview</a>
                </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function () {
        var selectedCountry = '';
        var selectedState = '';

        function handleInvoiceType() {
            if ($('#newInvoice').is(':checked')) {
                $('#oldInvoiceTextbox').hide();
                $('#newInvoiceTextbox').show();
            } else {
                $('#oldInvoiceTextbox').show();
                $('#newInvoiceTextbox').hide();
            }
        }

        function loadClientDetails(clientId) {
            if (!clientId || clientId === '0') {
                selectedCountry = '';
                selectedState = '';
                $('#clientDetails').html('');
                updateTaxVisibility();
                updateSubtotal();
                return;
            }

            $.ajax({
                type: 'POST',
                url: 'get_client_details.php',
                dataType: 'json',
                data: { client_id: clientId },
                success: function (response) {
                    if (!response || !response.ok) {
                        selectedCountry = '';
                        selectedState = '';
                        $('#clientDetails').html('');
                        updateTaxVisibility();
                        updateSubtotal();
                        return;
                    }

                    selectedCountry = (response.country_name || '').toLowerCase();
                    selectedState = (response.state_name || '').toLowerCase();
                    $('#clientDetails').html(
                        '<small><strong>Country:</strong> ' + (response.country_name || '-') +
                        '<br><strong>State:</strong> ' + (response.state_name || '-') +
                        '</small>'
                    );

                    updateTaxVisibility();
                    updateSubtotal();
                },
                error: function () {
                    selectedCountry = '';
                    selectedState = '';
                    updateTaxVisibility();
                    updateSubtotal();
                }
            });
        }

        function updateTaxVisibility() {
            var isInternational = !((selectedCountry === '' || selectedCountry === 'india') && selectedState !== 'internation' && selectedState !== 'international');
            var isUttarPradesh = selectedState === 'uttar pradesh';

            if (isInternational) {
                $('#gst').hide();
                $('#igst').hide();
                $('#bankChargeRow').show();
                $('#convertToUsd').prop('checked', true);
            } else if (isUttarPradesh) {
                $('#gst').show();
                $('#igst').hide();
                $('#bankChargeRow').hide();
            } else {
                $('#gst').hide();
                $('#igst').show();
                $('#bankChargeRow').hide();
            }
            updateCurrencyConversion();
        }

        function updateSubtotal() {
            var subtotal = 0;
            $('#discriptionSection .line-row').each(function () {
                subtotal += parseFloat($(this).find('input[name="Amount[]"]').val()) || 0;
            });
            $('#subtotal').val(subtotal.toFixed(2));
            updateFinalAmount();
        }

        function updateFinalAmount() {
            var subtotal = parseFloat($('#subtotal').val()) || 0;
            var discount = parseFloat($('#discount').val()) || 0;
            var finalAmount = Math.max(0, subtotal - discount);
            $('#finalAmount').val(finalAmount.toFixed(2));

            if ($('#gst').is(':visible')) {
                var sgst = finalAmount * 0.09;
                var cgst = finalAmount * 0.09;
                $('#sgstAmount').val(sgst.toFixed(2));
                $('#cgstAmount').val(cgst.toFixed(2));
                $('#igstAmount').val('0.00');
                $('#bankChargeAmount').val('0.00');
                $('#bankChargePercent').val('0');
                $('#totalAmount').val((finalAmount + sgst + cgst).toFixed(2));
            } else if ($('#igst').is(':visible')) {
                var igst = finalAmount * 0.18;
                $('#sgstAmount').val('0.00');
                $('#cgstAmount').val('0.00');
                $('#igstAmount').val(igst.toFixed(2));
                $('#bankChargeAmount').val('0.00');
                $('#bankChargePercent').val('0');
                $('#totalAmount').val((finalAmount + igst).toFixed(2));
            } else if ($('#bankChargeRow').is(':visible')) {
                var bankCharge = finalAmount * 0.035;
                $('#sgstAmount').val('0.00');
                $('#cgstAmount').val('0.00');
                $('#igstAmount').val('0.00');
                $('#bankChargeAmount').val(bankCharge.toFixed(2));
                $('#bankChargePercent').val('3.5');
                $('#totalAmount').val((finalAmount + bankCharge).toFixed(2));
            } else {
                $('#sgstAmount').val('0.00');
                $('#cgstAmount').val('0.00');
                $('#igstAmount').val('0.00');
                $('#bankChargeAmount').val('0.00');
                $('#bankChargePercent').val('0');
                $('#totalAmount').val(finalAmount.toFixed(2));
            }

            updateCurrencyConversion();
        }

        function updateCurrencyConversion() {
            var shouldConvert = $('#convertToUsd').is(':checked');
            var totalAmount = parseFloat($('#totalAmount').val()) || 0;
            var exchangeRate = parseFloat($('#exchange_rate').val()) || 0;

            $('#currencyConversionRow').toggle(shouldConvert);
            $('#currency').val(shouldConvert ? 'USD' : 'INR');
            updateUsdEquivalentFields(shouldConvert, exchangeRate);

            if (shouldConvert && exchangeRate > 0) {
                $('#total_amount_foreign').val((totalAmount / exchangeRate).toFixed(2));
            } else {
                $('#total_amount_foreign').val('0.00');
            }
        }

        function updateUsdEquivalentFields(shouldConvert, exchangeRate) {
            var fields = ['subtotal', 'discount', 'finalAmount', 'sgstAmount', 'cgstAmount', 'igstAmount', 'bankChargeAmount', 'totalAmount'];

            fields.forEach(function (fieldId) {
                var amount = parseFloat($('#' + fieldId).val()) || 0;
                var usdText = '';
                if (shouldConvert && exchangeRate > 0) {
                    usdText = '($' + (amount / exchangeRate).toFixed(2) + ')';
                }
                $('#' + fieldId + '_usd').text(usdText);
            });
        }

        $('.invoiceType').on('change', handleInvoiceType);
        $('#clientDropdown').on('change', function () {
            loadClientDetails($(this).val());
        });
        $('#discount').on('input', updateFinalAmount);
        $('#exchange_rate').on('input', updateFinalAmount);
        $('#convertToUsd').on('change', updateCurrencyConversion);

        $('#discriptionSection').on('click', '.addMore', function () {
            var currentRow = $(this).closest('.line-row');
            var newRow = currentRow.clone(true);
            newRow.find('input').val('');
            newRow.find('.addMore').hide();
            newRow.find('.remove').show();
            currentRow.parent().append(newRow);
            updateSubtotal();
        });

        $('#discriptionSection').on('click', '.remove', function () {
            $(this).closest('.line-row').remove();
            updateSubtotal();
        });

        $('#discriptionSection').on('input change', '.pct-calc', function () {
            var pct = parseFloat($(this).val()) || 0;
            var projectTotal = parseFloat($('#linked_project_total').val()) || 0;
            if (projectTotal > 0) {
                var calculatedAmt = (projectTotal * pct) / 100;
                $(this).closest('.line-row').find('.milestone-amt-field').val(calculatedAmt.toFixed(2));
                updateSubtotal();
            }
        });

        $('#discriptionSection').on('input change', '.milestone-amt-field', function () {
            var amt = parseFloat($(this).val()) || 0;
            var projectTotal = parseFloat($('#linked_project_total').val()) || 0;
            if (projectTotal > 0 && amt > 0) {
                var calculatedPct = (amt / projectTotal) * 100;
                $(this).closest('.line-row').find('.pct-calc').val(calculatedPct.toFixed(2));
            }
            updateSubtotal();
        });

        $('#oldInvoiceNumber').on('change', function () {
            $.ajax({
                type: 'POST',
                url: 'check-invoice.php',
                data: { oldInvoiceNumber: $('#oldInvoiceNumber').val() },
                success: function (response) {
                    $('#invoiceNumberMessage').html(response);
                }
            });
        });

        handleInvoiceType();
        updateTaxVisibility();
        updateSubtotal();
        loadClientDetails($('#clientDropdown').val());
    });
</script>
<?php include 'EvaraFroentend/layout/footer.php'; ?>

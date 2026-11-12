<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: update-payment.php');
    exit;
}

$clientId = (int) ($_POST['ClientID'] ?? 0);
$invoiceId = (int) ($_POST['invoiceId'] ?? 0);
$receivedAmount = round((float) ($_POST['getAmount'] ?? 0), 2);
$tdsAmount = round((float) ($_POST['tdsAmount'] ?? 0), 2);
$paymentNote = trim((string) ($_POST['payment_note'] ?? ''));
$paymentCredit = round($receivedAmount + $tdsAmount, 2);

$receivedDate = $_POST['received_date'] ?? '';
$receivedDate = is_string($receivedDate) ? trim($receivedDate) : '';

// MySQL DATE column cannot accept '0000-00-00' under strict modes.
// Normalize empty/invalid values to today's date.
if ($receivedDate === '' || $receivedDate === '0000-00-00' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $receivedDate)) {
    $receivedDate = date('Y-m-d');
} else {
    // Basic validity check for impossible dates (e.g., 2024-02-31)
    $dt = DateTime::createFromFormat('Y-m-d', $receivedDate);
    $errors = DateTime::getLastErrors();
    if (!$dt || ($errors['warning_count'] ?? 0) > 0 || ($errors['error_count'] ?? 0) > 0) {
        $receivedDate = date('Y-m-d');
    }
}


if ($clientId <= 0 || $invoiceId <= 0 || $paymentCredit <= 0) {
    header('Location: update-payment.php?error=invalid_payment');
    exit;
}

function ni_payment_col_exists(mysqli $con, string $column): bool
{
    $column = $con->real_escape_string($column);
    $r = $con->query("SHOW COLUMNS FROM recived_payment LIKE '$column'");
    return $r && $r->num_rows > 0;
}

if (!ni_payment_col_exists($con, 'tds_amount')) {
    $con->query("ALTER TABLE recived_payment ADD COLUMN tds_amount DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER recived_amount");
}
if (!ni_payment_col_exists($con, 'received_net')) {
    $con->query("ALTER TABLE recived_payment ADD COLUMN received_net DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER tds_amount");
}
if (!ni_payment_col_exists($con, 'payment_note')) {
    $con->query("ALTER TABLE recived_payment ADD COLUMN payment_note VARCHAR(255) NULL AFTER received_net");
}
if (!ni_payment_col_exists($con, 'received_date')) {
    $con->query("ALTER TABLE recived_payment ADD COLUMN received_date DATE NULL AFTER payment_note");
}
if (!ni_payment_col_exists($con, 'created_at')) {
    $con->query("ALTER TABLE recived_payment ADD COLUMN created_at DATE NULL AFTER status");
}


$con->begin_transaction();
try {
    $stmt = $con->prepare('SELECT totalAmount, milestone_id, status FROM invoice WHERE id = ? AND ClientID = ? LIMIT 1');
    $stmt->bind_param('ii', $invoiceId, $clientId);
    $stmt->execute();
    $invoice = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$invoice) {
        throw new Exception('invoice_not_found');
    }
    if ((int) ($invoice['status'] ?? 0) === 3) {
        throw new Exception('cancelled_invoice_payment');
    }

    $invoiceTotal = round((float) $invoice['totalAmount'], 2);
    $milestoneId = (int) ($invoice['milestone_id'] ?? 0);

    // Calculate already-paid amount for this invoice.
    // Some older records may have recived_amount but received_net=0; fallback to recived_amount when needed.
    $stmt = $con->prepare('SELECT 
            COALESCE(SUM(CASE WHEN received_net > 0 THEN received_net ELSE recived_amount END), 0) AS paid_before
        FROM recived_payment 
        WHERE workAmount_invoice_id = ? AND status = 1');
    $stmt->bind_param('i', $invoiceId);
    $stmt->execute();
    $paidRow = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $paidBefore = round((float) ($paidRow['paid_before'] ?? 0), 2);
    $pendingBefore = max(0, round($invoiceTotal - $paidBefore, 2));

    if ($paymentCredit > $pendingBefore + 0.01) {
        $msg = sprintf(
            'Payment exceeds pending: invoiceId=%d clientId=%d invoiceTotal=%.2f paidBefore=%.2f pendingBefore=%.2f paymentCredit=%.2f receivedAmount=%.2f tdsAmount=%.2f',
            $invoiceId,
            $clientId,
            $invoiceTotal,
            $paidBefore,
            $pendingBefore,
            $paymentCredit,
            $receivedAmount,
            $tdsAmount
        );
        error_log($msg);
        throw new Exception('payment_exceeds_pending');
    }

    // Check if a payment entry already exists for this invoice
    $stmt = $con->prepare('SELECT id, recived_amount, tds_amount, received_net, payment_note FROM recived_payment WHERE workAmount_invoice_id = ? LIMIT 1');
    $stmt->bind_param('i', $invoiceId);
    $stmt->execute();
    $existingPayment = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($existingPayment) {
        // UPDATE existing record with new amounts
        $existingId = (int) $existingPayment['id'];
        $currentRec = (float) $existingPayment['recived_amount'];
        $currentTds = (float) $existingPayment['tds_amount'];
        $currentNet = (float) $existingPayment['received_net'];

        // Migration fallback: if existing record has amount but 0 net, treat net as the current amount
        if ($currentNet <= 0 && $currentRec > 0) {
            $currentNet = $currentRec;
        }

        $newRec = round($currentRec + $receivedAmount, 10);
        $newTds = round($currentTds + $tdsAmount, 10);
        $newNet = round($currentNet + $paymentCredit, 10);
        $newNote = trim(($existingPayment['payment_note'] ?? '') . " | " . $paymentNote, " | ");

        $stmtUpdate = $con->prepare(
            'UPDATE recived_payment SET 
                recived_amount = ?, 
                tds_amount = ?, 
                received_net = ?, 
                payment_note = ?, 
                received_date = ?, 
                status = 1 
             WHERE id = ?'
        );
        $stmtUpdate->bind_param('dddssi', $newRec, $newTds, $newNet, $newNote, $receivedDate, $existingId);
        $stmtUpdate->execute();
        $stmtUpdate->close();
    } else {
        // Standard INSERT for first payment
        // Fetch the existing Proforma Invoice number to reuse it as Tax Invoice reference
        $stmtInv = $con->prepare('SELECT invoiceNumber FROM workamount WHERE invoice_id = ? LIMIT 1');
        $stmtInv->bind_param('i', $invoiceId);
        $stmtInv->execute();
        $invRes = $stmtInv->get_result()->fetch_assoc();
        $taxInvoiceNumber = $invRes['invoiceNumber'] ?? '';
        $stmtInv->close();

        $status = 1;
        $createdAt = date('Y-m-d');
        $stmtInsert = $con->prepare(
            'INSERT INTO recived_payment
                (ClientID, workAmount_invoice_id, taxinvoiceNumber, recived_amount, tds_amount, received_net, payment_note, received_date, status, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $createdAt = date('Y-m-d');
        $stmtInsert->bind_param(
            'iisdddssis',
            $clientId,
            $invoiceId,
            $taxInvoiceNumber,
            $receivedAmount,
            $tdsAmount,
            $paymentCredit,
            $paymentNote,
            $receivedDate,
            $status,
            $createdAt
        );
        $stmtInsert->execute();
        $stmtInsert->close();
    }

    // Re-calculate total paid from DB to update status correctly
    $stmtCheck = $con->prepare('SELECT 
            COALESCE(SUM(CASE WHEN received_net > 0 THEN received_net ELSE recived_amount END), 0) AS total_paid
        FROM recived_payment 
        WHERE workAmount_invoice_id = ? AND status = 1');
    $stmtCheck->bind_param('i', $invoiceId);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result()->fetch_assoc();
    $totalPaidNow = round((float) ($resCheck['total_paid'] ?? 0), 2);
    $stmtCheck->close();

    // Keep invoice status logic consistent with pending calculation.
    $invoiceStatus = ($totalPaidNow + 0.01 >= $invoiceTotal) ? 2 : 1;

    $stmt = $con->prepare('UPDATE invoice SET status = ? WHERE id = ?');
    $stmt->bind_param('ii', $invoiceStatus, $invoiceId);
    $stmt->execute();
    $stmt->close();

    // Harmonize with view-client-project: Update milestone if linked
    if ($milestoneId > 0) {
        $stmtM = $con->prepare("SELECT amount, amount_with_tax FROM project_payment_milestones WHERE milestone_id = ?");
        $stmtM->bind_param('i', $milestoneId);
        $stmtM->execute();
        $mRow = $stmtM->get_result()->fetch_assoc();
        $stmtM->close();

        if ($mRow) {
            $mTarget = (float)($mRow['amount_with_tax'] > 0 ? $mRow['amount_with_tax'] : $mRow['amount']);
            $isMilestoneDone = ($totalPaidNow + 0.01 >= $mTarget) ? 1 : 0;

            $stmtMU = $con->prepare("UPDATE project_payment_milestones SET 
                is_paid = ?,
                paid_amount = ?,
                last_paid_date = ?
                WHERE milestone_id = ?");
            $stmtMU->bind_param('idsi', $isMilestoneDone, $totalPaidNow, $receivedDate, $milestoneId);
            $stmtMU->execute();
            $stmtMU->close();
        }
    }

    $con->commit();
    header('Location: update-payment.php?success=1');
} catch (Throwable $e) {
    $con->rollback();
    // Helpful debug during integration (will show in logs / error page depending on server settings)
    error_log('Payment update failed: ' . $e->getMessage());
    $knownErrors = [
        'invoice_not_found',
        'cancelled_invoice_payment',
        'payment_exceeds_pending',
        'invalid_payment',
    ];
    $errorCode = in_array($e->getMessage(), $knownErrors, true) ? $e->getMessage() : 'payment_update_failed';
    header('Location: update-payment.php?error=' . urlencode($errorCode));
}

$con->close();
exit;

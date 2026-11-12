<?php
session_start();
include 'connection.php';
include_once 'includes/schema-bootstrap.php';

ni_ensure_database_schema($con);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: client-invoice-list.php');
    exit;
}

$invoiceId = (int) ($_POST['invoice_id'] ?? 0);
$returnTo = trim((string) ($_POST['return_to'] ?? 'client-invoice-list.php'));
$reason = trim((string) ($_POST['cancel_reason'] ?? ''));

if ($returnTo === '' || preg_match('/^https?:\/\//i', $returnTo) || strpos($returnTo, "\n") !== false || strpos($returnTo, "\r") !== false) {
    $returnTo = 'client-invoice-list.php';
}

function ni_cancel_invoice_return_url(string $returnTo, string $key, string $value): string
{
    $parts = parse_url($returnTo);
    $path = $parts['path'] ?? 'client-invoice-list.php';
    if ($path === '') {
        $path = 'client-invoice-list.php';
    }

    $query = [];
    if (!empty($parts['query'])) {
        parse_str($parts['query'], $query);
    }
    unset($query['error'], $query['success']);
    $query[$key] = $value;

    $url = $path . '?' . http_build_query($query);
    if (!empty($parts['fragment'])) {
        $url .= '#' . $parts['fragment'];
    }
    return $url;
}

function ni_cancel_invoice_col_exists(mysqli $con, string $column): bool
{
    $column = $con->real_escape_string($column);
    $r = $con->query("SHOW COLUMNS FROM invoice LIKE '$column'");
    return $r && $r->num_rows > 0;
}

if (!ni_cancel_invoice_col_exists($con, 'sent_at')) {
    $con->query("ALTER TABLE invoice ADD COLUMN sent_at DATETIME NULL DEFAULT NULL AFTER status");
}
if (!ni_cancel_invoice_col_exists($con, 'cancelled_at')) {
    $con->query("ALTER TABLE invoice ADD COLUMN cancelled_at DATETIME NULL DEFAULT NULL AFTER sent_at");
}
if (!ni_cancel_invoice_col_exists($con, 'cancel_reason')) {
    $con->query("ALTER TABLE invoice ADD COLUMN cancel_reason VARCHAR(255) NULL AFTER cancelled_at");
}

if ($invoiceId <= 0) {
    header('Location: ' . ni_cancel_invoice_return_url($returnTo, 'error', 'invalid_invoice'));
    exit;
}

if ($reason === '') {
    header('Location: ' . ni_cancel_invoice_return_url($returnTo, 'error', 'cancel_reason_required'));
    exit;
}

$con->begin_transaction();
try {
    $stmt = $con->prepare('SELECT id, status, finalAmount, milestone_id FROM invoice WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $invoiceId);
    $stmt->execute();
    $invoice = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$invoice) {
        throw new Exception('Invoice not found.');
    }

    if ((int) $invoice['status'] === 3) {
        $con->commit();
        header('Location: ' . ni_cancel_invoice_return_url($returnTo, 'success', 'invoice_already_cancelled'));
        exit;
    }

    $stmt = $con->prepare("SELECT COALESCE(SUM(CASE WHEN received_net > 0 THEN received_net ELSE recived_amount END), 0) AS paid_total FROM recived_payment WHERE workAmount_invoice_id = ? AND status = 1");
    $stmt->bind_param('i', $invoiceId);
    $stmt->execute();
    $paid = (float) (($stmt->get_result()->fetch_assoc()['paid_total'] ?? 0));
    $stmt->close();

    if ($paid > 0.01) {
        throw new Exception('Paid invoice cannot be cancelled.');
    }

    $reason = substr($reason, 0, 255);
    $stmt = $con->prepare('UPDATE invoice SET status = 3, cancelled_at = NOW(), cancel_reason = ? WHERE id = ?');
    $stmt->bind_param('si', $reason, $invoiceId);
    $stmt->execute();
    $stmt->close();

    $milestoneId = (int) ($invoice['milestone_id'] ?? 0);
    if ($milestoneId > 0) {
        $finalAmount = (float) ($invoice['finalAmount'] ?? 0);
        $stmt = $con->prepare('UPDATE project_payment_milestones SET invoiced_amount = GREATEST(0, invoiced_amount - ?) WHERE milestone_id = ?');
        $stmt->bind_param('di', $finalAmount, $milestoneId);
        $stmt->execute();
        $stmt->close();
    }

    $con->commit();
    header('Location: ' . ni_cancel_invoice_return_url($returnTo, 'success', 'invoice_cancelled'));
} catch (Throwable $e) {
    $con->rollback();
    error_log('Invoice cancel failed: ' . $e->getMessage());
    header('Location: ' . ni_cancel_invoice_return_url($returnTo, 'error', 'invoice_cancel_failed'));
}

$con->close();
exit;

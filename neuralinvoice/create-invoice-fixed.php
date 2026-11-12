<?php
session_start();
include 'connection.php';
include 'includes/helpers.php';

function ni_invoice_column_exists(mysqli $con, string $column): bool
{
    $column = $con->real_escape_string($column);
    $r = $con->query("SHOW COLUMNS FROM invoice LIKE '$column'");
    return $r && $r->num_rows > 0;
}

function ni_invoice_ensure_schema(mysqli $con): void
{
    if (!ni_invoice_column_exists($con, 'duration_days')) {
        $con->query("ALTER TABLE invoice ADD COLUMN duration_days VARCHAR(100) NULL DEFAULT NULL AFTER duedate");
    } else {
        $col = $con->query("SHOW COLUMNS FROM invoice LIKE 'duration_days'");
        $row = $col ? $col->fetch_assoc() : null;
        if ($row && stripos((string)$row['Type'], 'int') !== false) {
            $con->query("ALTER TABLE invoice MODIFY COLUMN duration_days VARCHAR(100) NULL DEFAULT NULL");
        }
    }

    $columns = [
        'bankChargePercent' => "DECIMAL(5,2) NOT NULL DEFAULT 0 AFTER igstAmount",
        'bankChargeAmount' => "DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER bankChargePercent",
        'currency' => "VARCHAR(10) NOT NULL DEFAULT 'INR' AFTER totalAmount",
        'exchange_rate' => "DECIMAL(10, 4) NOT NULL DEFAULT 1.0000 AFTER currency",
        'total_amount_foreign' => "DECIMAL(15, 2) NOT NULL DEFAULT 0 AFTER exchange_rate",
        'sent_at' => "DATETIME NULL DEFAULT NULL AFTER status",
        'cancelled_at' => "DATETIME NULL DEFAULT NULL AFTER sent_at",
        'cancel_reason' => "VARCHAR(255) NULL AFTER cancelled_at",
    ];
    foreach ($columns as $column => $definition) {
        if (!ni_invoice_column_exists($con, $column)) {
            $con->query("ALTER TABLE invoice ADD COLUMN $column $definition");
        }
    }
}

function ni_workamount_column_exists(mysqli $con, string $column): bool
{
    $column = $con->real_escape_string($column);
    $r = $con->query("SHOW COLUMNS FROM workamount LIKE '$column'");
    return $r && $r->num_rows > 0;
}

function ni_workamount_ensure_schema(mysqli $con): void
{
    // Requirement: workamount table me duration store karna hai (hr/description ka duration)
    if (!ni_workamount_column_exists($con, 'duration')) {
        $con->query("ALTER TABLE workamount ADD COLUMN duration VARCHAR(100) NULL DEFAULT NULL AFTER amount");
    }
}

function ni_milestone_column_exists(mysqli $con, string $column): bool
{
    $column = $con->real_escape_string($column);
    $r = $con->query("SHOW COLUMNS FROM project_payment_milestones LIKE '$column'");
    return $r && $r->num_rows > 0;
}
function ni_milestone_ensure_schema(mysqli $con): void
{
    if (!ni_milestone_column_exists($con, 'invoiced_amount')) {
        $con->query("ALTER TABLE project_payment_milestones ADD COLUMN invoiced_amount DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER amount_with_tax");
    }
}
ni_invoice_ensure_schema($con);
ni_workamount_ensure_schema($con);


function ni_insert_invoice_and_lines(mysqli $con, array $post, bool $isNew): void
{
    $invoice_type = (int)($post['invoice_type'] ?? 1);
    ni_milestone_ensure_schema($con); // Ensure schema before using invoiced_amount
    $clientId = (int)($post['client_id'] ?? 0);
    $companyId = ni_company_id_for_client($con, $clientId);
    $milestoneId = (int)($post['milestone_id'] ?? 0);
    $invoiceDate = (string)($post['invoiceDate'] ?? '');
    $concernPerson = (string)($post['concern_person'] ?? '');
    $mobile = (string)($post['mobile'] ?? '');
    $condition = (string)($post['condition'] ?? '');
    $deliveryDate = (string)($post['deliveryDate'] ?? '');
    $deuDate = (string)($post['deudate'] ?? '');

    $durationDaysArray = $post['duration_days'] ?? [];
    $durationDays = is_array($durationDaysArray)
        ? implode(', ', array_map(fn($v) => trim((string)$v), $durationDaysArray))
        : trim((string)$durationDaysArray);
    $durationDays = $con->real_escape_string($durationDays);

    $sacCode = '998313';
    $discount = (float)($post['discount'] ?? 0);
    $creation_date = date("Y-m-d");

    // line items (UI uses description[] / duration_days[] / Amount[])
    $descriptions = $post['discription'] ?? [];
    $durations = $post['duration_days'] ?? [];
    $amounts = $post['Amount'] ?? [];

    if (!is_array($descriptions) || !is_array($durations) || !is_array($amounts)) {
        die("Invalid line items payload. Ensure discription[], duration_days[] and Amount[] are submitted correctly.");
    }

    $subtotal = 0.0;
    $validLineCount = 0;
    foreach ($amounts as $idx => $amt) {
        $description = trim((string)($descriptions[$idx] ?? ''));
        $duration = trim((string)($durations[$idx] ?? ''));
        $amount = (float)$amt;

        if ($description === '' && $duration === '' && abs($amount) < 0.00001) {
            continue;
        }

        if ($description === '' || $duration === '' || $amount <= 0) {
            die("Each line needs description, duration and amount.");
        }

        $subtotal += $amount;
        $validLineCount++;
    }
    if ($validLineCount === 0) {
        die("At least one invoice line is required.");
    }
    $subtotal = round($subtotal, 2);

    $finalAmount = round($subtotal - max(0.0, $discount), 2);

    $tax = ni_compute_gst_for_client_amount($con, (int)$clientId, (float)$finalAmount);
    $sgstAmount = (float)$tax['sgst_amount'];
    $cgstAmount = (float)$tax['cgst_amount'];
    $igstAmount = (float)$tax['igst_amount'];
    $bankChargePercent = (float)($tax['bank_charge_percent'] ?? 0);
    $bankChargeAmount = (float)($tax['bank_charge_amount'] ?? 0);
    $totalAmount = round($finalAmount + (float)$tax['total_tax_amount'] + $bankChargeAmount, 2);

    $currency = strtoupper(trim((string)($post['currency'] ?? 'INR')));
    $currency = $currency === 'USD' ? 'USD' : 'INR';
    $exchangeRate = max(0, (float)($post['exchange_rate'] ?? 1.0));
    $totalAmountForeign = ($currency === 'USD' && $exchangeRate > 0) ? round($totalAmount / $exchangeRate, 2) : 0.0;
    $currencyEsc = $con->real_escape_string($currency);

    $milestoneVal = $milestoneId > 0 ? "'$milestoneId'" : "NULL";

    $sqlInvoice = "INSERT INTO invoice (invoice_type, ClientID, CompanyID, milestone_id, invoiceDate, concern, mobile, terms, deliverydate, duedate, duration_days, sacCode, subtotal, discount, finalAmount, sgstAmount, cgstAmount, igstAmount, bankChargePercent, bankChargeAmount, totalAmount, currency, exchange_rate, total_amount_foreign, creation_date)
        VALUES (
            '$invoice_type', '$clientId', '$companyId', $milestoneVal, '$invoiceDate', '$concernPerson', '$mobile', '$condition', '$deliveryDate', '$deuDate', '$durationDays', '$sacCode', '$subtotal', '$discount', '$finalAmount', '$sgstAmount', '$cgstAmount', '$igstAmount', '$bankChargePercent', '$bankChargeAmount', '$totalAmount', 
            '$currencyEsc', '$exchangeRate', '$totalAmountForeign',
            '$creation_date'
        )";

    if ($con->query($sqlInvoice) === false) {
        die("Error inserting data into invoice table: " . $con->error);
    }

    $invoiceId = $con->insert_id;

    // Update milestone's invoiced_amount if linked
    if ($milestoneId > 0) {
        $stmt = $con->prepare("UPDATE project_payment_milestones SET invoiced_amount = invoiced_amount + ? WHERE milestone_id = ?");
        $stmt->bind_param("di", $finalAmount, $milestoneId); // Use finalAmount (base amount) for milestone tracking
        $stmt->execute();
        $stmt->close();
    }
    if ($isNew) {
        $year = date('y', strtotime($invoiceDate));
        $lastYear = date('y', strtotime('1 year', strtotime($invoiceDate)));
        $prefix = ni_invoice_prefix_for_client($con, $clientId);
        $invoiceNumber = $prefix . '/' . $year . '-' . $lastYear . '/00' . $invoiceId;
    } else {
        $invoiceNumber = (string)($post['oldInvoiceNumber'] ?? '');
    }

    // Insert exactly one workamount row per UI row index (0..count(Amount)-1)
    $lineCount = count($amounts);
    for ($key = 0; $key < $lineCount; $key++) {
        if (!array_key_exists($key, $descriptions)) {
            continue;
        }

        $description = trim((string)$descriptions[$key]);
        $amount = (float)($amounts[$key] ?? 0);

        if ($description === '' && abs($amount) < 0.00001) {
            continue;
        }

        $durationLine = $durationDaysArray[$key] ?? '';
        $durationLine = is_array($durationLine)
            ? implode(', ', array_map(fn($v) => trim((string)$v), $durationLine))
            : trim((string)$durationLine);
        $durationLine = $con->real_escape_string($durationLine);

        $safeDescription = $con->real_escape_string($description);
        $safeAmount = $con->real_escape_string((string)$amount);

        $sqlworkamount = "INSERT INTO workamount (invoice_id,invoiceNumber, description, amount, duration)
            VALUES ('$invoiceId', '$invoiceNumber', '$safeDescription', '$safeAmount', '$durationLine')";


        if ($con->query($sqlworkamount) === false) {
            die("Error inserting data into workamount table: " . $con->error);
        }
    }

    $con->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isNew = ((int)($_POST['invoice_type'] ?? 1) === 1);
    ni_insert_invoice_and_lines($con, $_POST, $isNew);
    header("Location: client-invoice-list.php");
    exit();
}

$con->close();
header("Location: client-invoice-list.php");
exit();

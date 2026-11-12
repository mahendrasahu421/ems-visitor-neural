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
        if ($row && stripos((string) $row['Type'], 'int') !== false) {
            $con->query("ALTER TABLE invoice MODIFY COLUMN duration_days VARCHAR(100) NULL DEFAULT NULL");
        }
    }

    $columns = [
        'bankChargePercent' => "DECIMAL(5,2) NOT NULL DEFAULT 0 AFTER igstAmount",
        'bankChargeAmount' => "DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER bankChargePercent",
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

ni_invoice_ensure_schema($con);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data

    if ($_POST['invoice_type'] == 1) {
        $invoice_type = $_POST['invoice_type'];
        $clientId = $_POST['client_id'];
        $companyId = ni_company_id_for_client($con, (int) $clientId);
        $invoiceDate = $_POST['invoiceDate'];
        $year = date('y', strtotime($invoiceDate));
        $lastYear = date('y', strtotime('-1 year', strtotime($invoiceDate)));

        $concernPerson = $_POST['concern_person'];
        $mobile = $_POST['mobile'];
        // $date = $_POST['date'];
        $condition = $_POST['condition'];
        $deliveryDate = $_POST['deliveryDate'];
        $deuDate = $_POST['deudate'];
        $durationDaysArray = $_POST['duration_days'] ?? [];
        $durationDays = is_array($durationDaysArray)
            ? implode(', ', array_map(fn($v) => trim((string)$v), $durationDaysArray))
            : trim((string)$durationDaysArray);
        $durationDays = $con->real_escape_string($durationDays);
        $sacCode = '998313';


        $discount = (float) ($_POST['discount'] ?? 0);
        $creation_date = date("Y-m-d");

        // Calculate subtotal from line items (amounts)
        $subtotal = 0.0;
        if (!empty($_POST['Amount']) && is_array($_POST['Amount'])) {
            foreach ($_POST['Amount'] as $amt) {
                $subtotal += (float) $amt;
            }
        }
        $subtotal = round($subtotal, 2);

        $finalAmount = round($subtotal - max(0.0, (float) $discount), 2);

        // Tax base = finalAmount (discount already reduced)
        $tax = ni_compute_gst_for_client_amount($con, (int) $clientId, (float) $finalAmount);
        $sgstAmount = $tax['sgst_amount'];
        $cgstAmount = $tax['cgst_amount'];
        $igstAmount = $tax['igst_amount'];
        $bankChargePercent = (float) ($tax['bank_charge_percent'] ?? 0);
        $bankChargeAmount = (float) ($tax['bank_charge_amount'] ?? 0);
        $totalAmount = round($finalAmount + $tax['total_tax_amount'] + $bankChargeAmount, 2);

        $sqlInvoice = "INSERT INTO invoice (invoice_type, ClientID, CompanyID, invoiceDate, concern, mobile, terms, deliverydate, duedate, duration_days, sacCode, subtotal, discount, finalAmount, sgstAmount, cgstAmount, igstAmount, bankChargePercent, bankChargeAmount, totalAmount, creation_date)
        VALUES ('$invoice_type', '$clientId', '$companyId', '$invoiceDate', '$concernPerson', '$mobile', '$condition', '$deliveryDate', '$deuDate', '$durationDays', '$sacCode', '$subtotal', '$discount', '$finalAmount', '$sgstAmount', '$cgstAmount', '$igstAmount', '$bankChargePercent', '$bankChargeAmount', '$totalAmount','$creation_date')";



        if ($con->query($sqlInvoice) === FALSE) {
            die("Error inserting data into invoice table: " . $con->error);
        }
        $invoiceId = $con->insert_id;
        $prefix = ni_invoice_prefix_for_client($con, (int) $clientId);
        $invoiceNumber = $prefix . '-' . $lastYear . '-' . $year . '/PI00' . $invoiceId;

        $descriptions = $_POST['discription'] ?? [];
        $amounts = $_POST['Amount'] ?? [];

        if (!is_array($descriptions) || !is_array($amounts)) {
            die("Invalid line items payload. Please ensure Description[] and Amount[] are submitted correctly.");
        }

        foreach ($descriptions as $key => $description) {
            if (!array_key_exists($key, $amounts)) {
                continue; // avoid mixing/misaligned entries
            }

            $description = trim((string)$description);
            $amount = (float) $amounts[$key];

            if ($description === '' && abs($amount) < 0.00001) {
                continue;
            }

            $safeDescription = $con->real_escape_string($description);
            $safeAmount = $con->real_escape_string((string)$amount);

            $sqlworkamount = "INSERT INTO workamount (invoice_id,invoiceNumber, description, amount)
                              VALUES ('$invoiceId','$invoiceNumber', '$safeDescription', '$safeAmount')";

            if ($con->query($sqlworkamount) === FALSE) {
                die("Error inserting data into workamount table: " . $con->error);
            }
        }
        $sqlFetchData = "SELECT * FROM invoice WHERE id = '$invoiceId'";
        $resultFetchData = $con->query($sqlFetchData);

        if ($resultFetchData === FALSE) {
            die("Error fetching data from invoice table: " . $con->error);
        }
        $invoiceData = $resultFetchData->fetch_assoc();
        $resultFetchData->close();
        $con->close();
        header("Location: client-invoice-list.php");
        exit();
    } else {
        $invoice_type = $_POST['invoice_type'];
        $oldInvoiceNumber = $_POST['oldInvoiceNumber'];
        $clientId = $_POST['client_id'];
        $companyId = ni_company_id_for_client($con, (int) $clientId);
        $invoiceDate = $_POST['invoiceDate'];
        $year = date('y', strtotime($invoiceDate));
        $lastYear = date('y', strtotime('-1 year', strtotime($invoiceDate)));
        $concernPerson = $_POST['concern_person'];
        $mobile = $_POST['mobile'];
        // $date = $_POST['date'];
        $condition = $_POST['condition'];
        $deliveryDate = $_POST['deliveryDate'];
        $deuDate = $_POST['deudate'];
        $durationDaysArray = $_POST['duration_days'] ?? [];
        $durationDays = is_array($durationDaysArray)
            ? implode(', ', array_map(fn($v) => trim((string)$v), $durationDaysArray))
            : trim((string)$durationDaysArray);
        $durationDays = $con->real_escape_string($durationDays);
        $sacCode = '998313';

        $discount = (float) ($_POST['discount'] ?? 0);
        $creation_date = date("Y-m-d");

        // Calculate subtotal from line items (amounts)
        $subtotal = 0.0;
        if (!empty($_POST['Amount']) && is_array($_POST['Amount'])) {
            foreach ($_POST['Amount'] as $amt) {
                $subtotal += (float) $amt;
            }
        }
        $subtotal = round($subtotal, 2);

        $finalAmount = round($subtotal - max(0.0, (float) $discount), 2);

        // Tax base = finalAmount (discount already reduced)
        $tax = ni_compute_gst_for_client_amount($con, (int) $clientId, (float) $finalAmount);
        $sgstAmount = $tax['sgst_amount'];
        $cgstAmount = $tax['cgst_amount'];
        $igstAmount = $tax['igst_amount'];
        $bankChargePercent = (float) ($tax['bank_charge_percent'] ?? 0);
        $bankChargeAmount = (float) ($tax['bank_charge_amount'] ?? 0);
        $totalAmount = round($finalAmount + $tax['total_tax_amount'] + $bankChargeAmount, 2);

        $sqlInvoice = "INSERT INTO invoice (invoice_type, ClientID, CompanyID, invoiceDate, concern, mobile, terms, deliverydate, duedate, duration_days, sacCode, subtotal, discount, finalAmount, sgstAmount, cgstAmount, igstAmount, bankChargePercent, bankChargeAmount, totalAmount, creation_date)
        VALUES ('$invoice_type', '$clientId', '$companyId', '$invoiceDate', '$concernPerson', '$mobile', '$condition', '$deliveryDate', '$deuDate', '$durationDays', '$sacCode', '$subtotal', '$discount', '$finalAmount', '$sgstAmount', '$cgstAmount', '$igstAmount', '$bankChargePercent', '$bankChargeAmount', '$totalAmount','$creation_date')";




        if ($con->query($sqlInvoice) === FALSE) {
            die("Error inserting data into invoice table: " . $con->error);
        }
        $invoiceId = $con->insert_id;
        $invoiceNumber = $oldInvoiceNumber;

        $descriptions = $_POST['discription'] ?? [];
        $amounts = $_POST['Amount'] ?? [];

        if (!is_array($descriptions) || !is_array($amounts)) {
            die("Invalid line items payload. Please ensure Description[] and Amount[] are submitted correctly.");
        }

        foreach ($descriptions as $key => $description) {
            if (!array_key_exists($key, $amounts)) {
                continue; // avoid mixing/misaligned entries
            }

            $description = trim((string)$description);
            $amount = (float) $amounts[$key];

            if ($description === '' && abs($amount) < 0.00001) {
                continue;
            }

            $safeDescription = $con->real_escape_string($description);
            $safeAmount = $con->real_escape_string((string)$amount);

            $sqlworkamount = "INSERT INTO workamount (invoice_id,invoiceNumber, description, amount)
                              VALUES ('$invoiceId','$invoiceNumber', '$safeDescription', '$safeAmount')";

            if ($con->query($sqlworkamount) === FALSE) {
                die("Error inserting data into workamount table: " . $con->error);
            }
        }
        $sqlFetchData = "SELECT * FROM invoice WHERE id = '$invoiceId'";
        $resultFetchData = $con->query($sqlFetchData);

        if ($resultFetchData === FALSE) {
            die("Error fetching data from invoice table: " . $con->error);
        }
        $invoiceData = $resultFetchData->fetch_assoc();
        $resultFetchData->close();
        $con->close();
        header("Location: client-invoice-list.php");
        exit();
    }
}

// Fetch client records from the database
$sqlClients = "SELECT * FROM clientmaster";
$resultClients = $con->query($sqlClients);

// Check if the query was successful
if ($resultClients === FALSE) {
    die("Error: " . $con->error);
}

// Fetch client data and store it in an associative array
$clients = array();
while ($rowClient = $resultClients->fetch_assoc()) {
    $clients[] = $rowClient;
}

// Close the result set
$resultClients->close();

// Close the database conection
$con->close();

<?php
session_start();
include 'connection.php';
include 'includes/helpers.php';
error_reporting(0);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $newInvoice = $_POST['newInvoice'];
    $clientId = (int) ($_POST['client_id'] ?? 0);

    if ($newInvoice != "") {
        // Get the current year and one year back
        $currentYear = date('y');
        $lastYear = date('y', strtotime('1 year'));

        // Query to check if the new invoice number exists for the given invoice type
        $checkQuery = "SELECT id FROM invoice WHERE invoice_type = '$newInvoice'";
        $result = $con->query($checkQuery);
        $totalRecord = $result->num_rows;

        // Generate the invoice number with current year and last year
        $prefix = ni_invoice_prefix_for_client($con, $clientId);
        echo $prefix . '/' . $currentYear . '-' . $lastYear . '/00' . ($totalRecord + 1);
    } else {
        echo "Invalid invoice number.";
    }

}

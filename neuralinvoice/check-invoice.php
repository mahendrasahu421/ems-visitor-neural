<?php
session_start();
include 'connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $oldInvoiceNumber = $_POST['oldInvoiceNumber'];
    // echo $oldInvoiceNumber;exit;
    if ($oldInvoiceNumber != "") {
        $checkQuery = "SELECT * FROM workamount WHERE invoiceNumber = '$oldInvoiceNumber'";
        $result = $con->query($checkQuery);

        if ($result->num_rows > 0) {
            // Invoice number already exists
            echo "Invoice already exists.";
        } else {
            "";
        }
    } else {
        echo "Invalid invoice number.";
    }
}

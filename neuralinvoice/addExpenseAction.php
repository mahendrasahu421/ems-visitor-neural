<?php
session_start();
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize form inputs
    $expense_category_id = filter_input(INPUT_POST, 'expense_category_id', FILTER_VALIDATE_INT);
    $expense_name = filter_input(INPUT_POST, 'expense_name', FILTER_SANITIZE_STRING);
    $expense_cost = filter_input(INPUT_POST, 'expense_cost', FILTER_VALIDATE_FLOAT);
    $expense_date = filter_input(INPUT_POST, 'expense_date', FILTER_SANITIZE_STRING);
    $expense_mode = filter_input(INPUT_POST, 'expense_mode', FILTER_VALIDATE_INT);
    $expense_status = filter_input(INPUT_POST, 'expense_status', FILTER_VALIDATE_INT);

    // Validate file upload
    if (!empty($_FILES["expense_invoice"]["name"])) {
        $targetDir = "uploads/";
        $expenseInvoiceFileName = basename($_FILES["expense_invoice"]["name"]);
        $targetFilePath = $targetDir . $expenseInvoiceFileName;

        if (move_uploaded_file($_FILES["expense_invoice"]["tmp_name"], $targetFilePath)) {
            // File uploaded successfully
        } else {
            echo "Error uploading file.";
            exit();
        }
    } else {
        // Handle the case where expense_invoice is not uploaded (optional)
    }

    // Check if any required fields are empty
    if (empty($expense_category_id) || empty($expense_name) || empty($expense_cost) || empty($expense_date) || empty($expense_mode) || empty($expense_status)) {
        echo "Please fill in all required fields.";
        exit();
    }

    // Insert data into expenses table
    $sql = "INSERT INTO expenses (expense_category_id, expense_name, expense_cost, expense_date, expense_mode, expense_invoice, expense_status) 
            VALUES ('$expense_category_id', '$expense_name', '$expense_cost', '$expense_date', '$expense_mode', '$expenseInvoiceFileName', '$expense_status')";

    if ($con->query($sql) === TRUE) {
        header("Location: add-expencese-list.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $con->error;
    }

    $con->close();
} else {
    header("Location: error.php");
    exit();
}

<?php
// update_status.php
session_start();
include 'connection.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize the input if needed
    $invoice_id = isset($_POST['invoice_id']) ? $_POST['invoice_id'] : '';

    // Ensure sent_at column exists (Safety check)
    $con->query("ALTER TABLE invoice ADD COLUMN IF NOT EXISTS sent_at DATETIME NULL DEFAULT NULL AFTER status");

    // Update both status (Generated/Sent) and the timestamp
    $updateStatusQuery = "UPDATE invoice SET status = 1, sent_at = NOW() WHERE id = ? AND status <> 3";
    
    $stmt = $con->prepare($updateStatusQuery);
    $stmt->bind_param('s', $invoice_id);
    $success = $stmt->execute();
    if ($success && $stmt->affected_rows > 0) {
        $response = ['success' => true, 'message' => 'Status updated successfully.'];
    } else {
        $response = ['success' => false, 'message' => 'Failed to update status. Invoice may be cancelled.'];
    }
    $stmt->close();
    $con->close();
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Handle invalid requests
    header('HTTP/1.1 400 Bad Request');
    echo 'Invalid request';
}
?>

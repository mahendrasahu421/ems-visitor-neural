<?php
include 'includes/auth.php';
include 'connection.php';
include 'includes/helpers.php';

header('Content-Type: application/json');

$clientId = (int)($_GET['client_id'] ?? 0);
$baseAmount = (float)($_GET['base_amount'] ?? 0);

if ($clientId <= 0) {
    echo json_encode(['ok' => false, 'message' => 'Invalid client_id']);
    exit;
}

$tax = ni_compute_gst_for_client_amount($con, $clientId, $baseAmount);

echo json_encode(['ok' => true] + $tax);


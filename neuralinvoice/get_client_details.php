<?php
session_start();
include 'connection.php';

header('Content-Type: application/json');

if (!isset($_POST['client_id']) && !isset($_GET['client_id'])) {
    echo json_encode(['ok' => false, 'message' => 'client_id not provided']);
    exit;
}

$clientId = (int)($_POST['client_id'] ?? $_GET['client_id']);
if ($clientId <= 0) {
    echo json_encode(['ok' => false, 'message' => 'invalid client_id']);
    exit;
}

$sql = "
    SELECT cm.ClientID,
           cm.ClientName,
           cm.Country as CountryID,
           cm.StateID,
           cm_del.CountryName,
           sm_del.StateName
    FROM clientmaster cm
    LEFT JOIN countrymaster cm_del ON cm.Country = cm_del.CountryID
    LEFT JOIN statemaster sm_del ON cm.StateID = sm_del.StateID
    WHERE cm.ClientID = $clientId
    LIMIT 1
";

$res = $con->query($sql);
if ($res === false) {
    echo json_encode(['ok' => false, 'message' => 'query failed']);
    exit;
}

$row = $res->fetch_assoc();
if (!$row) {
    echo json_encode(['ok' => false, 'message' => 'client not found']);
    exit;
}

$stateName = $row['StateName'] ?? null;
if ($stateName === null) {
    $stateName = '';
}

$countryName = $row['CountryName'] ?? '';
if ($countryName === null) {
    $countryName = '';
}

if (strtolower(trim((string) $countryName)) !== '' && strtolower(trim((string) $countryName)) !== 'india') {
    $stateName = 'International';
}

echo json_encode([
    'ok' => true,
    'country_name' => $countryName,
    'state_name' => $stateName,
]);


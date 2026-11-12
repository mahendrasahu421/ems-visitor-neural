<?php
// Database connection
$con = new mysqli("localhost", "username", "password", "database");

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Get country ID from the request
$countryId = $_POST['country_id'];

// Fetch states for the selected country
$sqlStates = "SELECT * FROM states WHERE country_id = ?";
$stmt = $con->prepare($sqlStates);
$stmt->bind_param("i", $countryId);
$stmt->execute();
$resultStates = $stmt->get_result();

$states = array();
while ($row = $resultStates->fetch_assoc()) {
    $states[] = $row;
}

// Return states as JSON
echo json_encode($states);

$stmt->close();
$con->close();
?>

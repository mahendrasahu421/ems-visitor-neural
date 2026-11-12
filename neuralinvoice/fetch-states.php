<?php
include 'connection.php'; // Database connection
include_once 'includes/helpers.php';

header('Content-Type: application/json');

// Check if country_id is sent
if (isset($_POST['country_id'])) {
    $countryId = intval($_POST['country_id']);

    if (!ni_country_is_india($con, $countryId)) {
        $stateId = ni_get_international_state_id($con, $countryId);
        echo json_encode($stateId > 0 ? [[
            'StateID' => $stateId,
            'StateName' => 'International',
            'CountryID' => $countryId,
        ]] : []);
        $con->close();
        exit;
    }

    // Fetch states for the selected country
    $sqlStates = "SELECT * FROM statemaster WHERE CountryID = ?";
    $stmt = $con->prepare($sqlStates);
    $stmt->bind_param("i", $countryId);
    $stmt->execute();
    $result = $stmt->get_result();

    $states = [];
    while ($row = $result->fetch_assoc()) {
        $states[] = $row;
    }

    echo json_encode($states);
    $stmt->close();
    $con->close();
    exit;
}

// If no country_id is sent, return an error
echo json_encode(['error' => 'Country ID not provided.']);
exit;

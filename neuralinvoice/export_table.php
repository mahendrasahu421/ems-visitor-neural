<?php
session_start();
include 'connection.php';
$sql = "SELECT * FROM your_table";
$result = $conn->query($sql);

// Create a CSV file
$filename = "export.csv";
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// Output column headers
fputcsv($output, array('Column 1', 'Column 2', 'Column 3'));

// Output data rows
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
$conn->close();
?>

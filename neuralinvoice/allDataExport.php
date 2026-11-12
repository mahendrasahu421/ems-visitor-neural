<?php
session_start();
include 'connection.php';
require 'vendor/autoload.php'; // Include PhpSpreadsheet library

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Fetch all data from the 'invoice' table
    $fetchQuery = "SELECT * FROM invoice";
    $result = $con->query($fetchQuery);

    if ($result->num_rows > 0) {
        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add header row
        $header = ['Column1', 'Column2', 'Column3']; // Replace with your actual column names
        $sheet->fromArray($header, null, 'A1');

        // Add data rows
        $row = 2;
        while ($row_data = $result->fetch_assoc()) {
            $sheet->fromArray($row_data, null, 'A' . $row);
            $row++;
        }

        // Save the spreadsheet as Excel file
        $writer = new Xlsx($spreadsheet);
        $excelFileName = 'exported_data.xlsx';
        $writer->save($excelFileName);

        // Provide the download link to the user
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $excelFileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    } else {
        echo "No data found to export.";
    }
}
?>

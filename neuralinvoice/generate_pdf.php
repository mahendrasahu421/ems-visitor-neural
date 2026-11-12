<?php
session_start();

use Mpdf\Mpdf;
require_once __DIR__ . '/vendor/autoload.php'; // Adjust the path as needed

// Buffer the output of the included file
ob_start();
include 'view-invoice.php';
$html = ob_get_clean();

$mpdf = new Mpdf();

// Add HTML content
$mpdf->WriteHTML($html);

// Output PDF to a file or download it
$mpdf->Output('output.pdf', 'D');
?>

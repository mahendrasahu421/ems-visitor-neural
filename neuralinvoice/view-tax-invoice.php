<?php
session_start();
include 'connection.php';
require_once 'includes/invoice-company.php';

if (isset($_GET['invoice_id'])) {
    $invoiceId = (int) $_GET['invoice_id'];
    
    $sql = "SELECT statemaster.StateName, countrymaster.CountryName, invoice.*, recived_payment.*, 
            workamount.description AS descriptions,
            workamount.amount AS amounts,
            recived_payment.taxinvoiceNumber,
            clientmaster.Address,
            clientmaster.Country,
            clientmaster.StateID,
            clientmaster.pinNo,
            clientmaster.ClientName,
            " . ni_invoice_company_select_sql() . " 
    FROM recived_payment
    LEFT JOIN workamount ON recived_payment.workAmount_invoice_id = workamount.invoice_id
    LEFT JOIN clientmaster ON recived_payment.ClientID = clientmaster.ClientID
    LEFT JOIN statemaster ON clientmaster.StateID = statemaster.StateID
    LEFT JOIN countrymaster ON clientmaster.Country = countrymaster.CountryID
    LEFT JOIN invoice ON recived_payment.workAmount_invoice_id = invoice.id
    LEFT JOIN companymaster co ON COALESCE(invoice.CompanyID, clientmaster.CompanyID) = co.CompanyID
    WHERE invoice.id = $invoiceId OR recived_payment.id = $invoiceId
    ORDER BY recived_payment.id DESC";
    $result = $con->query($sql);
    
    if ($result === FALSE) {
        die("Error fetching data: " . $con->error);
    }
    
    $clintData = $result->fetch_assoc();
    if (!$clintData) {
        die('<div class="alert alert-danger">Tax invoice not found. <a href="recived-payment-form-clinet.php">Go back</a></div>');
    }
    $company = ni_invoice_company_data($clintData);
} else {
    header("Location: error.php");
    exit();
}
?>


<html>

<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=400, initial-scale=0.5" />
    <meta content="telephone=no" name="format-detection" />
    <!-- Add this in the head section of your HTML file -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <title>Invoice Neural</title>
    <style>
        .text-right {
            text-align: end;
        }
    </style>


<body>

    <div>
        <table id="contentToConvert" style="box-shadow: 0px 0px 10px 12px rgba(0, 0, 0, 0.1); margin: 0 auto; background: #fafafa; -webkit-print-color-adjust: exact;" border="1px solid" width="760" cellspacing="0" cellpadding="0">
            <tbody>
                <tr>
                    <td align="center" valign="top">
                        <table style="background: #fafafa; -webkit-print-color-adjust: exact; padding: 10px;" border="0" width="760" cellspacing="0" cellpadding="0"><!-- Hero -->
                            <tbody>
                                <tr>
                                    <td>
                                        <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                            <tbody>
                                                <tr>
                                                    <td style="vertical-align: top;" colspan="2">
                                                        <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="vertical-align: top; ">
                                                                        <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                                                            <tbody>
                                                                                <tr style="background:linear-gradient(#00acec,#f2f2f2)">
                                                                                    <td align="left" valign="top" style="width: 10%; border-bottom: 1px solid;">
                                                                                        <?php if (!empty($company['logo'])): ?>
                                                                                            <img width="120px" src="<?php echo ni_invoice_h($company['logo']); ?>" alt="Logo" />
                                                                                        <?php else: ?>
                                                                                            <b><?php echo ni_invoice_h($company['name']); ?></b>
                                                                                        <?php endif; ?>
                                                                                    </td>
                                                                                    <td align="right" style="font-size: 15pt; font-weight: bold; text-transform: uppercase; padding-right: 10px; border-bottom: 1px solid;">
                                                                                        Tax INVOICE
                                                                                    </td>
                                                                                </tr>


                                                                            </tbody>
                                                                        </table><br>
                                                                    </td>

                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr style="margin-top: 10px;">
                                                    <td style="padding:7px;" valign="top" width="42%"><b style="color: #c23136;">Bill To :</b>

                                                        <b style="display: block;">
                                                            <?php echo htmlspecialchars($clintData['ClientName']); ?></b>
                                                        <?php echo htmlspecialchars($clintData['Address']); ?><br>
                                                        <?php if (strtolower((string) $clintData['CountryName']) !== 'india') {
                                                            echo htmlspecialchars($clintData['CountryName']);
                                                        } else {
                                                            echo htmlspecialchars($clintData['StateName']);
                                                        } ?></br>GSTIN :
                                                        <?php echo htmlspecialchars($clintData['pinNo']); ?>
                                                    </td>
                                                    <td style="vertical-align: top; padding:7px;" width="58%">
                                                        <table style="text-align: right;" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="border-bottom: 0; padding:5px;" width="48%"><b style="color: #c23136;">Bill From :</b><br><b><?php echo ni_invoice_h($company['name']); ?></b><br>
                                                                        <?php echo nl2br(ni_invoice_h($company['address'])); ?><br>
                                                                        <?php if (!empty($company['email'])): ?>Email: <?php echo ni_invoice_h($company['email']); ?><br><?php endif; ?>
                                                                        <?php if (!empty($company['mobile'])): ?>Mobile: <?php echo ni_invoice_h($company['mobile']); ?><br><?php endif; ?>
                                                                        <?php if (!empty($company['phone'])): ?>Phone: <?php echo ni_invoice_h($company['phone']); ?><br><?php endif; ?>
                                                                        <?php if (!empty($company['website'])): ?>Website: <?php echo ni_invoice_h($company['website']); ?><br><?php endif; ?>
                                                                        <?php if (!empty($company['cin'])): ?>CIN: <?php echo ni_invoice_h($company['cin']); ?><br><?php endif; ?>
                                                                        <?php if (!empty($company['pan'])): ?>PAN: <?php echo ni_invoice_h($company['pan']); ?><br><?php endif; ?>
                                                                        <?php if (!empty($company['gst'])): ?>GSTIN: <?php echo ni_invoice_h($company['gst']); ?><br><?php endif; ?>
                                                                        <?php if (!empty($company['iec'])): ?>IEC No: <?php echo ni_invoice_h($company['iec']); ?><br><?php endif; ?>
                                                                        <?php if (!empty($company['lut'])): ?>LUT No: <?php echo ni_invoice_h($company['lut']); ?><br><?php endif; ?>
                                                                        <?php if (!empty($company['tan'])): ?>TAN: <?php echo ni_invoice_h($company['tan']); ?><?php endif; ?>
                                                                    </td>
                                                                    <!-- <td style="font-size: 11pt;" width="48%">AEC No: AACCN4385L</td> -->
                                                                </tr>

                                                            </tbody>
                                                        </table>
                                                <tr>
                                                    <td style="padding-top: 10px;" colspan="2">
                                                        <table style="border: 1px solid #818181; border-bottom: 0;" border="0" width="100%" cellspacing="0" cellpadding="4">
                                                            <thead style="background:linear-gradient(#00acec,#f2f2f2); color: #18181b;">
                                                                <tr>
                                                                    <th style="border-right: 1px solid #818181; border-bottom: 1px solid #818181; background:linear-gradient(#00acec,#f2f2f2); text-align: center;">
                                                                        #Invoice</th>
                                                                    <th style="border-right: 1px solid #818181; border-bottom: 1px solid #818181; background:linear-gradient(#00acec,#f2f2f2); text-align: center;">
                                                                        Date</th>
                                                                    <th style="border-right: 1px solid #818181; border-bottom: 1px solid #818181; background:linear-gradient(#00acec,#f2f2f2); text-align: center;">
                                                                        Due Date</th>
                                                                    <th style="border-right: 1px solid #818181; border-bottom: 1px solid #818181; background:linear-gradient(#00acec,#f2f2f2); text-align: center;">
                                                                        Duration</th>

                                                                    <th style="border-right: 1px solid #818181; border-bottom: 1px solid #818181; background:linear-gradient(#00acec,#f2f2f2); text-align: center;">
                                                                        Place Of Supply</th>
                                                                    <th style="border-right: 1px solid #818181; border-bottom: 1px solid #818181; background:linear-gradient(#00acec,#f2f2f2); text-align: center;">
                                                                        Concern Person</th>
                                                                    <th style="border-right: 1px solid #818181; border-bottom: 1px solid #818181; background:linear-gradient(#00acec,#f2f2f2); text-align: center;">
                                                                        Mobile</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>

                                                                <tr>
                                                                    <td style="border-right: 1px solid #818181; text-align: center; border-bottom: 1px solid #818181;">
                                                                        <?php echo $clintData['taxinvoiceNumber']; ?></td>
                                                                    <td style="border-right: 1px solid #818181; text-align: center; border-bottom: 1px solid #818181;">
                                                                        <?php echo date('d-m-Y', strtotime($clintData['invoiceDate'])); ?></td>
                                                                    <td style="border-right: 1px solid #818181; border-bottom: 1px solid #818181; text-align: center;">
                                                                        <?php echo date('d-m-Y', strtotime($clintData['duedate'])); ?></td>
                                                                    <td style="border-right: 1px solid #818181; border-bottom: 1px solid #818181; text-align: center;">
                                                                        <?php echo !empty($clintData['duration_days']) ? htmlspecialchars($clintData['duration_days']) : '-'; ?></td>

                                                                    <td style="border-right: 1px solid #818181; text-align: center; border-bottom: 1px solid #818181;">
                                                                        <?php echo $clintData['StateName']; ?></td>
                                                                    <td style="border-right: 1px solid #818181; text-align: center; border-bottom: 1px solid #818181;">
                                                                        <?php echo $clintData['concern']; ?></td>
                                                                    <td style="border-right: 1px solid #818181; text-align: center; border-bottom: 1px solid #818181;">
                                                                        <?php echo $clintData['mobile']; ?></td>
                                                                </tr>

                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding-top: 10px;" colspan="2">
                                                        <table style="border: 1px solid #818181; border-bottom: 0;" border="0" width="100%" cellspacing="0" cellpadding="4">
                                                            <thead style="background:linear-gradient(#00acec,#f2f2f2); color: #18181b;">
                                                                <tr>
                                                                    <th style=" border-right: 1px solid #818181; border-bottom: 1px solid #818181; background:linear-gradient(#00acec,#f2f2f2); text-align: center;">
                                                                        Sn no</th>
                                                                    <th style="border-right: 1px solid #818181; border-bottom: 1px solid #818181; background:linear-gradient(#00acec,#f2f2f2); text-align: center;">
                                                                        SAC Code</th>
                                                                    <th style="border-right: 1px solid #818181; border-bottom: 1px solid #818181; background:linear-gradient(#00acec,#f2f2f2); text-align: center;">
                                                                        Description</th>

                                                                    <th style="border-right: 1px solid #818181; border-bottom: 1px solid #818181; background:linear-gradient(#00acec,#f2f2f2);text-align: center;">
                                                                        Total In INR</th>

                                                            </thead>
                                                            <tbody>

                                                            </tbody>
                                                            <tbody>
                                                                <?php $result->data_seek(0); // Reset result set pointer to the beginning 
                                                                ?>
                                                                <?php $i = 1; ?>
                                                                <?php while ($row = $result->fetch_assoc()) : ?>

                                                                    <tr>
                                                                        <td style="border-right: 1px solid #818181; text-align: center; border-bottom: 1px solid #818181;">
                                                                            <?php

                                                                            echo $i ?></td>
                                                                        <td style="border-right: 1px solid #818181; text-align: center; border-bottom: 1px solid #818181;">
                                                                            <?php echo $row['sacCode']; ?></td>
                                                                        <td style="border-right: 1px solid #818181; border-bottom: 1px solid #818181; text-align: center;">
                                                                            <?php echo $row['descriptions']; ?> </td>

                                                                        <td style="border-right: 1px solid #818181; text-align: right; border-bottom: 1px solid #818181;">
                                                                            <?php echo $row['amounts']; ?></td>

                                                                    </tr>
                                                                    <?php $i++; ?>

                                                                <?php endwhile; ?>
                                                                <tr>
                                                                    <td colspan="3" style="border-right: 1px solid #818181; border-bottom: 1px solid #818181;" class="text-right"><?php echo strtolower((string) $clintData['CountryName']) !== 'india' ? 'Sub total in USD' : 'Sub total in INR'; ?></td>
                                                                    <td style="border-right: 1px solid #818181 ; border-bottom: 1px solid #818181; text-align: right;"> <?php echo $clintData['subtotal']; ?></td>
                                                                </tr>

                                                                <?php if ($clintData['discount'] != 0) : ?>
                                                                    <tr>
                                                                        <td colspan="3" style="border-right: 1px solid #818181; border-bottom: 1px solid #818181;" class="text-right">Discount</td>
                                                                        <td style="border-right: 1px solid #818181; border-bottom: 1px solid #818181; text-align: right;"><?php echo $clintData['discount']; ?></td>
                                                                    </tr>
                                                                <?php endif; ?>
                                                                <?php if ($clintData['sgstAmount'] != 0) : ?>
                                                                    <tr>
                                                                        <td colspan="3" style="border-right: 1px solid #818181; border-bottom: 1px solid #818181;" class="text-right">SGST @9%</td>
                                                                        <td style="border-right: 1px solid #818181; border-bottom: 1px solid #818181; text-align: right;"><?php echo $clintData['sgstAmount']; ?></td>
                                                                    </tr>
                                                                <?php endif; ?>
                                                                <?php if ($clintData['cgstAmount'] != 0) : ?>
                                                                    <tr>
                                                                        <td colspan="3" style="border-right: 1px solid #818181; border-bottom: 1px solid #818181;" class="text-right">CGST @9%</td>
                                                                        <td style="border-right: 1px solid #818181; border-bottom: 1px solid #818181; text-align: right;"><?php echo $clintData['cgstAmount']; ?></td>
                                                                    </tr>
                                                                <?php endif; ?>
                                                                <?php if ($clintData['igstAmount'] != 0) : ?>
                                                                    <tr>
                                                                        <td colspan="3" style="border-right: 1px solid #818181; border-bottom: 1px solid #818181;" class="text-right">IGST @18%</td>
                                                                        <td style="border-right: 1px solid #818181; border-bottom: 1px solid #818181; text-align: right;"><?php echo $clintData['igstAmount']; ?></td>
                                                                    </tr>
                                                                <?php endif; ?>
                                                                <?php if (!empty($clintData['bankChargeAmount'])) : ?>
                                                                    <tr>
                                                                        <td colspan="3" style="border-right: 1px solid #818181; border-bottom: 1px solid #818181;" class="text-right">International Bank Charge @<?php echo number_format((float) ($clintData['bankChargePercent'] ?? 3), 2); ?>%</td>
                                                                        <td style="border-right: 1px solid #818181; border-bottom: 1px solid #818181; text-align: right;"><?php echo $clintData['bankChargeAmount']; ?></td>
                                                                    </tr>
                                                                <?php endif; ?>


                                                                <tr>
                                                                    <td colspan="3" style="border-right: 1px solid #818181; border-bottom: 1px solid #818181;" class="text-right"> Total</td>
                                                                    <td style="border-right: 1px solid #818181 ; border-bottom: 1px solid #818181; text-align: right;"> <?php echo $clintData['totalAmount']; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="3" style="border-right: 1px solid #818181; border-bottom: 1px solid #818181;" class="text-right">Grand Total (Round)</td>
                                                                    <td style="border-right: 1px solid #818181 ; border-bottom: 1px solid #818181; text-align: right;"><?php echo intval($clintData['totalAmount']); ?> </td>
                                                                </tr>
                                                                <?php
                                                                function convertToWords($number)
                                                                {
                                                                    $words = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine"];
                                                                    $teens = ["", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];
                                                                    $tens = ["", "Ten", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];

                                                                    if ($number == 0) {
                                                                        return 'Zero';
                                                                    } elseif ($number < 10) {
                                                                        return $words[$number];
                                                                    } elseif ($number < 20) {
                                                                        return $teens[$number - 10];
                                                                    } elseif ($number < 100) {
                                                                        return $tens[($number / 10)] . " " . $words[$number % 10];
                                                                    } elseif ($number < 1000) {
                                                                        return $words[($number / 100)] . " Hundred " . convertToWords($number % 100);
                                                                    } elseif ($number < 1000000) {
                                                                        return convertToWords($number / 1000) . " Thousand " . convertToWords($number % 1000);
                                                                    } elseif ($number < 1000000000) {
                                                                        return convertToWords($number / 1000000) . " Million " . convertToWords($number % 1000000);
                                                                    } else {
                                                                        return convertToWords($number / 1000000000) . " Billion " . convertToWords($number % 1000000000);
                                                                    }
                                                                }

                                                                // Example usage
                                                                $grandTotalInWords = convertToWords(intval($clintData['totalAmount']));
                                                                ?>

                                                                <tr>
                                                                    <td colspan="4" style="border-right: 1px solid #818181; border-bottom: 1px solid #818181;" class="text-right">Rupees <?php echo $grandTotalInWords; ?> Only</td>

                                                                </tr>



                                                            </tbody>
                                                        </table>
                                                        <table>
                                                            <tr>
                                                                <th colspan="2" style="padding:5px; border-bottom: 1px solid #818181; background:linear-gradient(#00acec,#f2f2f2)" class="">Term & condistions</th>
                                                                <th colspan="3" style="padding:5px; border-bottom: 1px solid #818181; text-align: center; background:linear-gradient(#00acec,#f2f2f2)"> Bank Details</th>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="1" style="padding:5px; text-align: start; border-right: 1px solid #818181; border-bottom: 1px solid #818181;" class=""><?php echo $clintData['terms']; ?></td>
                                                                <td colspan="2" style="padding:5px; text-align: start;  border-bottom: 1px solid #818181;" class=""><?php echo ni_invoice_bank_html($company['bank1']); ?></td>
                                                                <td colspan="2" style="padding:5px; text-align: start; border-right: 1px solid #818181; border-bottom: 1px solid #818181;" class=""><?php echo ni_invoice_bank_html($company['bank2']); ?></td>

                                                            </tr>

                                                            <tr>
                                                                <td colspan="1" style="padding:5px; border-right: 1px solid #818181; border-bottom: 1px solid #818181; padding: 10%;" class="">Resiver Signature</td>
                                                                <td colspan="4" style="padding:5px; border-right: 1px solid #818181; border-bottom: 1px solid #818181; padding: 10%;" class="">For <?php echo ni_invoice_h($company['name']); ?></td>

                                                            </tr>
                                                            <tr style="">
                                                                <th colspan="5" style="padding: 8%; text-align:center;  border-right: 1px solid #818181; border-bottom: 1px solid #818181; background:linear-gradient(#f2f2f2,#00acec)">Thanku You For Your Bussiness </th>


                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>


                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

            </tbody>
        </table>

        </td>
        </tr>






    </div>
    <!-- Your HTML content here -->


    <!-- Add this script after including jsPDF -->
    <!-- <form id="downloadForm" action="update_status.php" method="post"> -->


    <div class="card-header mb-5" style="background:linear-gradient(#66b3ff,#f2f2f2)">
        <input type="text" hidden value="<?php echo $clintData['id']; ?>" id="invoice_id" name="invoice_id">
        <!-- <button id="convertToPDF" class="btn btn-success mt-2">Download In Pdf</button> -->
        <button class="btn btn-danger mt-2"> <a style="text-decoration: none; color:aliceblue;" href="recived-payment-form-clinet.php">Back To list</a></button>
    </div>


    <!-- </form> -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#convertToPDF').click(function() {
                // Perform an AJAX request to update the status
                $.ajax({
                    url: 'update_status.php',
                    method: 'POST',
                    data: {
                        invoice_id: $('#invoice_id').val()
                    },
                    success: function(response) {
                        // Check the response if needed
                        console.log(response);

                        // If status is updated successfully, proceed to download PDF
                        if (response.success) {
                            // Use html2pdf to convert the HTML element to PDF
                            var pdfElement = document.getElementById('contentToConvert');

                            html2pdf(pdfElement)
                                .from(pdfElement, 30, 30)
                                .outputPdf();
                        } else {
                            console.error('Failed to update status.');
                        }
                    },
                    error: function(error) {
                        console.error('Error updating status:', error);
                    }
                });
            });
        });
    </script>





</body>

</html>

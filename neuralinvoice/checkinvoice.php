<?php
session_start();
include 'connection.php';
require_once 'includes/invoice-company.php';

if (isset($_GET['invoice_id'])) {
    $invoiceId = (int) $_GET['invoice_id'];
    $sql = "SELECT i.*, 
    CASE 
        WHEN i.milestone_id > 0 THEN m.milestone_label
        ELSE w.description
    END AS description,
    w.invoiceNumber, 
    CASE 
        WHEN i.milestone_id > 0 THEN m.amount
        ELSE w.amount
    END AS amount,
    w.duration, cm.*, sm.*, conm.CountryName,
    " . ni_invoice_company_select_sql() . "
    FROM invoice i
    LEFT JOIN workamount w ON i.id = w.invoice_id
    LEFT JOIN project_payment_milestones m ON i.milestone_id = m.milestone_id
    LEFT JOIN clientmaster cm ON i.ClientID = cm.ClientID
    LEFT JOIN companymaster co ON COALESCE(i.CompanyID, cm.CompanyID) = co.CompanyID
    LEFT JOIN statemaster sm ON cm.StateID = sm.StateID
    LEFT JOIN countrymaster conm ON cm.Country = conm.CountryID
    WHERE i.id = ?";

    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $invoiceId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false) {
        die("Error fetching data: " . $con->error);
    }
    $clintData = $result->fetch_assoc();
    if (!$clintData) {
        die("Invoice not found.");
    }
    $company = ni_invoice_company_data($clintData);

    $isUSD = ($clintData['currency'] ?? 'INR') === 'USD';
    $currencySymbol = $isUSD ? '$' : '₹';
    $currencyName = $isUSD ? 'USD' : 'INR';
    $exchangeRate = (float) ($clintData['exchange_rate'] ?? 0);
    $hasUsdRate = $isUSD && $exchangeRate > 0;
    $inrSymbol = '&#8377;';
    $usdSymbol = '$';
    $toUsd = function ($amount) use ($exchangeRate) {
        return $exchangeRate > 0 ? ((float) $amount / $exchangeRate) : 0;
    };
    $usdTotalAmount = (float) ($clintData['total_amount_foreign'] ?? 0);
    if ($hasUsdRate && $usdTotalAmount <= 0) {
        $usdTotalAmount = $toUsd($clintData['totalAmount'] ?? 0);
    }
    $formatInrWithUsd = function ($amount) use ($hasUsdRate, $toUsd, $inrSymbol, $usdSymbol) {
        $html = $inrSymbol . ni_h(number_format((float) $amount, 2));
        if ($hasUsdRate) {
            $html .= ' <span class="usd-equivalent">(' . $usdSymbol . ni_h(number_format($toUsd($amount), 2)) . ')</span>';
        }
        return $html;
    };
    $displayCurrencyName = $hasUsdRate ? 'USD' : 'INR';
    $formatInvoiceMoney = function ($amount) use ($hasUsdRate, $toUsd, $inrSymbol, $usdSymbol) {
        $displayAmount = $hasUsdRate ? $toUsd($amount) : (float) $amount;
        return ($hasUsdRate ? $usdSymbol : $inrSymbol) . ni_h(number_format($displayAmount, 2));
    };
} else {
    header("Location: error.php");
    exit();
}

function ni_h($val)
{
    return htmlspecialchars((string) ($val ?? ''), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta content="telephone=no" name="format-detection" />
    <title>Invoice - Neural</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --brand: #0a94e857;
            --brand-dark: #0aa2e8;
            --ink: #1f2430;
            --muted: #6c757d;
            --border: #d7dce2;
        }

        body {
            background: #eef1f5;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: var(--ink);
            padding: 30px 0;
        }

        .text-right {
            text-align: right;
        }

        .usd-equivalent {
            color: var(--muted);
            font-size: 11px;
            margin-left: 6px;
            white-space: nowrap;
        }

        .invoice-wrap {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        .invoice-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 24px;
            background: linear-gradient(344deg, var(--brand), #0a94e857);
            border-bottom: 3px solid var(--brand-dark);
        }

        .invoice-header img {
            max-width: 130px;
            max-height: 60px;
        }

        .invoice-header .title {
            font-size: 20px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #332C6D;
        }

        .party-block {
            padding: 18px 24px;
        }

        .party-block h6 {
            color: #c23136;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .party-block .name {
            font-weight: 700;
            font-size: 15px;
            margin-bottom: 2px;
        }

        .party-block .meta-line {
            font-size: 13px;
            color: var(--muted);
            line-height: 1.6;
        }

        .party-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            table-layout: fixed;
        }

        .party-table th {
            background: linear-gradient(180deg, #f8fbff, #eaf7ff);
            border: 1px solid var(--border);
            color: #c23136;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.4px;
            padding: 9px 12px;
            text-align: left;
            text-transform: uppercase;
            width: 50%;
        }

        .party-table td {
            border: 1px solid var(--border);
            padding: 12px;
            text-align: left;
            vertical-align: top;
            width: 50%;
        }

        .party-table th:last-child,
        .party-table .bill-from-cell {
            text-align: right;
        }

        .party-table .name {
            font-weight: 700;
            font-size: 15px;
            margin-bottom: 4px;
            color: var(--ink);
        }

        .party-table .meta-line {
            color: var(--muted);
            line-height: 1.6;
        }

        .meta-table,
        .items-table,
        .totals-table,
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .meta-table th,
        .items-table th,
        .footer-table th {
            background: linear-gradient(180deg, var(--brand), #eaf7ff);
            color: #16324a;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.4px;
            padding: 8px 6px;
            text-align: center;
            border: 1px solid var(--border);
        }

        .meta-table td,
        .items-table td,
        .totals-table td,
        .footer-table td {
            padding: 8px 6px;
            border: 1px solid var(--border);
        }

        .meta-table td {
            text-align: center;
        }

        .items-table td {
            text-align: center;
        }

        .totals-table td {
            border-left: none;
            border-right: none;
        }

        .totals-table tr:last-child td,
        .grand-total td {
            font-weight: 700;
            background: #f7fafc;
        }

        .section-gap {
            padding: 0 24px 18px 24px;
        }

        .amount-words {
            font-weight: 700;
            float: right;
            padding: 10px 24px;
            font-size: 13px;
            font-style: italic;
            color: var(--ink);
        }

        .footer-table th {
            background: linear-gradient(180deg, var(--brand), #eaf7ff);
        }

        .footer-table td {
            font-size: 11px;
            padding: 6px 8px;
        }

        .thank-you {
            text-align: center;
            padding: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            background: linear-gradient(0deg, var(--brand), #eaf7ff);
        }

        .action-bar {
            max-width: 800px;
            margin: 18px auto 0 auto;
            padding: 16px 24px;
            border-radius: 10px;
            background: #ffffff;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
            display: flex;
            gap: 10px;
        }

        .pdf-render-stage {
            position: fixed;
            top: 0;
            left: 0;
            width: 800px;
            background: #ffffff;
            pointer-events: none;
            z-index: -1;
        }

        .invoice-wrap.pdf-render-mode {
            width: 800px;
            max-width: 800px;
            margin: 0;
            border-radius: 0;
            box-shadow: none;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
                -webkit-print-color-adjust: exact;
                /* For Chrome/Safari */
                print-color-adjust: exact;
                /* Standard */
            }

            .invoice-wrap {
                box-shadow: none;
                margin: 0;
                border-radius: 0;
                max-width: 100%;
            }

            .action-bar {
                display: none;
            }

            .party-block,
            .section-gap {
                padding: 12px 18px !important;
            }

            .footer-table td {
                padding: 15px 10px !important;
            }
        }
    </style>
</head>

<body>

    <div id="contentToConvert" class="invoice-wrap">

        <div class="invoice-header">
            <?php if (!empty($company['logo'])): ?>
                <img src="<?php echo ni_h($company['logo']); ?>" alt="Logo" />
            <?php else: ?>
                <div class="title"><?php echo ni_h($company['name']); ?></div>
            <?php endif; ?>
            <div class="title">Proforma Invoice</div>
        </div>

        <div class="section-gap pt-3">
            <table class="party-table">
                <thead>
                    <tr>
                        <th>Bill To</th>
                        <th>Bill From</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="bill-to-cell">
                            <div class="name"><?php echo ni_h($clintData['ClientName']); ?></div>
                            <div class="meta-line">
                                <?php echo nl2br(ni_h($clintData['Address'])); ?><br>
                                <?php echo ni_h(strtolower((string) $clintData['CountryName']) !== 'india' ? $clintData['CountryName'] : $clintData['StateName']); ?><br>
                                GSTIN: <?php echo ni_h($clintData['pinNo']); ?>
                            </div>
                        </td>
                        <td class="bill-from-cell">
                            <div class="name"><?php echo ni_h($company['name']); ?></div>
                            <div class="meta-line">
                                <?php echo nl2br(ni_h($company['address'])); ?><br>
                                <?php if (!empty($company['cin'])): ?>CIN: <?php echo ni_h($company['cin']); ?><br><?php endif; ?>
                                <?php if (!empty($company['pan'])): ?>PAN: <?php echo ni_h($company['pan']); ?><br><?php endif; ?>
                                <?php if (!empty($company['gst'])): ?>GSTIN: <?php echo ni_h($company['gst']); ?><br><?php endif; ?>
                                <?php if (!empty($company['iec'])): ?>IEC No: <?php echo ni_h($company['iec']); ?><br><?php endif; ?>
                                <?php if (!empty($company['lut'])): ?>LUT No: <?php echo ni_h($company['lut']); ?><br><?php endif; ?>
                                <?php if (!empty($company['tan'])): ?>TAN: <?php echo ni_h($company['tan']); ?><?php endif; ?>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section-gap">
            <table class="meta-table">
                <thead>
                    <tr>
                        <th>#Invoice</th>
                        <th>Date</th>
                        <th>Due Date</th>
                        <th>Place Of Supply</th>
                        <th>Concern Person</th>
                        <th>Mobile</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo ni_h($clintData['invoiceNumber']); ?></td>
                        <td><?php echo ni_h(date('d-m-Y', strtotime($clintData['invoiceDate']))); ?></td>
                        <td><?php echo ni_h(date('d-m-Y', strtotime($clintData['duedate']))); ?></td>
                        <td><?php echo ni_h($clintData['StateName']); ?></td>
                        <td><?php echo ni_h($clintData['concern']); ?></td>
                        <td><?php echo ni_h($clintData['mobile']); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section-gap">
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Sr No</th>
                        <th>SAC Code</th>
                        <th>Description</th>
                        <th>Period</th>
                        <th class="text-right">Total (<?php echo ni_h($displayCurrencyName); ?>)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $result->data_seek(0); ?>
                    <?php $i = 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo ni_h($row['sacCode']); ?></td>
                            <td><?php echo ni_h($row['description']); ?></td>
                            <td><?php echo ni_h($row['duration']); ?></td>
                            <td class="text-right"><?php echo $formatInvoiceMoney($row['amount']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <table class="totals-table">
                <tbody>
                    <tr>
                        <td colspan="4" class="text-right">Sub total (<?php echo ni_h($displayCurrencyName); ?>)</td>
                        <td class="text-right"><?php echo $formatInvoiceMoney($clintData['subtotal']); ?></td>
                    </tr>

                    <?php if (!empty($clintData['discount']) && $clintData['discount'] != 0): ?>
                        <tr>
                            <td colspan="4" class="text-right">Discount</td>
                            <td class="text-right"><?php echo $formatInvoiceMoney($clintData['discount']); ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if (!empty($clintData['sgstAmount']) && $clintData['sgstAmount'] != 0): ?>
                        <tr>
                            <td colspan="4" class="text-right">SGST @9%</td>
                            <td class="text-right"><?php echo $formatInvoiceMoney($clintData['sgstAmount']); ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if (!empty($clintData['cgstAmount']) && $clintData['cgstAmount'] != 0): ?>
                        <tr>
                            <td colspan="4" class="text-right">CGST @9%</td>
                            <td class="text-right"><?php echo $formatInvoiceMoney($clintData['cgstAmount']); ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if (!empty($clintData['igstAmount']) && $clintData['igstAmount'] != 0): ?>
                        <tr>
                            <td colspan="4" class="text-right">IGST @18%</td>
                            <td class="text-right"><?php echo $formatInvoiceMoney($clintData['igstAmount']); ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if (!empty($clintData['bankChargeAmount'])): ?>
                        <tr>
                            <td colspan="4" class="text-right">
                                International Bank Charge
                                @<?php echo ni_h(number_format((float) ($clintData['bankChargePercent'] ?? 3.5), 2)); ?>%
                            </td>
                            <td class="text-right"><?php echo $formatInvoiceMoney($clintData['bankChargeAmount']); ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if ($hasUsdRate): ?>
                        <tr>
                            <td colspan="4" class="text-right">Total (USD)</td>
                            <td class="text-right"><?php echo $usdSymbol; ?><?php echo ni_h(number_format($usdTotalAmount, 2)); ?></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-right">Total</td>
                            <td class="text-right"><?php echo $formatInvoiceMoney($clintData['totalAmount']); ?></td>
                        </tr>
                    <?php endif; ?>

                    <tr class="grand-total">
                        <td colspan="4" class="text-right">Grand Total (Round)</td>
                        <td class="text-right">
                            <?php
                                $roundAmount = $hasUsdRate 
                                    ? $usdTotalAmount 
                                    : (float)$clintData['totalAmount'];
                                echo ($hasUsdRate ? $usdSymbol : $inrSymbol) . ni_h(number_format(round($roundAmount), 2));
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <?php
            function convertToWords($number)
            {
                $words = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine"];
                $teens = ["", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];
                $tens = ["", "Ten", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];

                if ($number == 0) {
                    return '';
                } elseif ($number < 10) {
                    return $words[$number];
                } elseif ($number < 20) {
                    return $teens[$number - 10];
                } elseif ($number < 100) {
                    return trim($tens[intdiv($number, 10)] . " " . $words[$number % 10]);
                } elseif ($number < 1000) {
                    return trim($words[intdiv($number, 100)] . " Hundred " . convertToWords($number % 100));
                } elseif ($number < 1000000) {
                    return trim(convertToWords(intdiv($number, 1000)) . " Thousand " . convertToWords($number % 1000));
                } elseif ($number < 1000000000) {
                    return trim(convertToWords(intdiv($number, 1000000)) . " Million " . convertToWords($number % 1000000));
                } else {
                    return trim(convertToWords(intdiv($number, 1000000000)) . " Billion " . convertToWords($number % 1000000000));
                }
            }
            $amountToConvert = $hasUsdRate 
                ? (int)round($usdTotalAmount)
                : (int)round((float)$clintData['totalAmount']);
            $grandTotalInWords = convertToWords($amountToConvert);
            $currencyWord = $hasUsdRate ? 'Dollars' : 'Rupees';
            ?>
            <div class="amount-words"><?php echo $currencyWord; ?> <?php echo ni_h($grandTotalInWords); ?> Only</div>

            <table class="footer-table">
                <tbody>
                    <tr>
                        <th style="width:40%;">Terms &amp; Conditions</th>
                        <th style="width:60%;" colspan="2">Bank Details</th>
                    </tr>
                    <tr>
                        <td><?php echo nl2br(ni_h($clintData['terms'])); ?></td>
                        <td><?php echo ni_invoice_bank_html($company['bank1']); ?></td>
                        <td><?php echo ni_invoice_bank_html($company['bank2']); ?></td>
                    </tr>
                    <tr>
                        <td>Receiver Signature</td>
                        <td colspan="5" style="text-align: right;">For <?php echo ni_h($company['name']); ?></td>
                    </tr>
                </tbody>
            </table>
            <div class="thank-you">Thank You For Your Business</div>
        </div>
    </div>

    <div class="action-bar">
        <input type="hidden" value="<?php echo ni_h($clintData['id']); ?>" id="invoice_id" name="invoice_id">
        <button id="convertToPDF" class="btn btn-success">Download PDF</button>
        <a href="client-invoice-list.php" class="btn btn-outline-secondary">Back To List</a>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#convertToPDF').click(function () {
                var $button = $(this);
                $button.prop('disabled', true).text('Downloading...');

                $.ajax({
                    url: 'update_status.php',
                    method: 'POST',
                    dataType: 'json',
                    data: { invoice_id: $('#invoice_id').val() },
                    success: function (response) {
                        if (response.success) {
                            var pdfElement = document.getElementById('contentToConvert');
                            var invoiceNumber = <?php echo json_encode((string) $clintData['invoiceNumber']); ?>;
                            var fileName = (invoiceNumber || 'invoice').replace(/[\\/:*?"<>|]+/g, '-') + '.pdf';
                            var renderStage = document.createElement('div');
                            var pdfClone = pdfElement.cloneNode(true);

                            renderStage.className = 'pdf-render-stage';
                            pdfClone.classList.add('pdf-render-mode');
                            renderStage.appendChild(pdfClone);
                            document.body.appendChild(renderStage);

                            var opt = {
                                margin: [0, 0, 0, 0],
                                filename: fileName,
                                image: { type: 'jpeg', quality: 1.0 },
                                html2canvas: {
                                    scale: 3,
                                    useCORS: true,
                                    allowTaint: true,
                                    backgroundColor: '#ffffff',
                                    scrollX: 350,
                                    scrollY: 0,
                                    windowWidth: 800
                                },
                                jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
                            };

                            waitForPdfImages(pdfClone).then(function () {
                                return html2pdf().set(opt).from(pdfClone).save();
                            }).catch(function (error) {
                                console.error('PDF download failed:', error);
                                alert('PDF download nahi ho paya. Please dobara try karein.');
                            }).finally(function () {
                                document.body.removeChild(renderStage);
                                $button.prop('disabled', false).text('Download PDF');
                            });
                        } else {
                            console.error('Failed to update status.');
                            alert(response.message || 'Invoice status update nahi ho paya.');
                            $button.prop('disabled', false).text('Download PDF');
                        }
                    },
                    error: function (error) {
                        console.error('Error updating status:', error);
                        alert('Unable to start the PDF download. Please try again.');
                        $button.prop('disabled', false).text('Download PDF');
                    }
                });
            });

            function waitForPdfImages(element) {
                var images = Array.prototype.slice.call(element.querySelectorAll('img'));

                return Promise.all(images.map(function (image) {
                    if (image.complete) {
                        return Promise.resolve();
                    }

                    return new Promise(function (resolve) {
                        image.onload = resolve;
                        image.onerror = resolve;
                    });
                }));
            }
        });
    </script>

</body>

</html>

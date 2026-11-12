<?php
include("EvaraFroentend/layout/head.php");
include("EvaraFroentend/layout/header.php");
include_once 'includes/schema-bootstrap.php';

ni_ensure_database_schema($con);

$searchQuery = isset($_GET['search']) ? $con->real_escape_string($_GET['search']) : '';
$fromDate = $_GET['fromDate'] ?? '';
$toDate = $_GET['toDate'] ?? '';

if (empty($fromDate) && empty($toDate)) {
    $fromDate = date('Y-m-d', strtotime('-1 month'));
    $toDate = date('Y-m-d');
}

// Pending/Due Payments report:
// - Start from invoice
// - Join workamount (invoiceNumber)
// - Join clientmaster/statemaster/countrymaster for party info
// - Aggregate paid totals from recived_payment
// - Keep only invoices with pending > 0
$sql = "SELECT
            i.id,
            i.invoiceDate,
            i.duedate,
            i.duration_days,
            i.finalAmount,
            i.sgstAmount,
            i.cgstAmount,
            i.igstAmount,
            i.bankChargePercent,
            i.bankChargeAmount,
            i.totalAmount,
            i.status AS invoice_status,
            w.invoiceNumber,
            cm.ClientCode,
            cm.ClientName,
            cm.pinNo,
            sm.StateName,
            countrymaster.CountryName,
            paid_totals.paid_total,
            paid_totals.received_total,
            paid_totals.tds_total,
            (i.totalAmount - paid_totals.paid_total) AS pending_total
        FROM invoice i
        LEFT JOIN workamount w ON i.id = w.invoice_id
        LEFT JOIN clientmaster cm ON i.ClientID = cm.ClientID
        LEFT JOIN statemaster sm ON cm.StateID = sm.StateID
        LEFT JOIN countrymaster ON cm.Country = countrymaster.CountryID
        LEFT JOIN (
            SELECT workAmount_invoice_id,
                   SUM(CASE WHEN received_net > 0 THEN received_net ELSE recived_amount END) AS paid_total,
                   SUM(recived_amount) AS received_total,
                   SUM(tds_amount) AS tds_total
            FROM recived_payment
            GROUP BY workAmount_invoice_id
        ) paid_totals ON paid_totals.workAmount_invoice_id = i.id
        WHERE i.creation_date BETWEEN '$fromDate' AND '$toDate'
          AND i.status IN (1, 2)
          AND (
                w.invoiceNumber LIKE '%$searchQuery%'
                OR cm.ClientName LIKE '%$searchQuery%'
                OR cm.ClientCode LIKE '%$searchQuery%'
                OR i.invoiceDate LIKE '%$searchQuery%'
                OR i.duedate LIKE '%$searchQuery%'
          )
        HAVING pending_total > 0
        ORDER BY pending_total DESC, i.id DESC";

$result = $con->query($sql);
if ($result === false) {
    die("Error fetching pending/due payment report: " . $con->error);
}

$rows = [];
$totalInvoice = 0; // due base
$totalPending = 0;
$totalReceived = 0;
$totalTds = 0;

while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
    $totalInvoice += (float) $row['totalAmount'];
    $totalPending += (float) $row['pending_total'];
    $totalReceived += (float) ($row['received_total'] ?? 0);
    $totalTds += (float) ($row['tds_total'] ?? 0);
}
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<style>
    label { font-weight: 700; }
    .summary-box { background: #f8f9fa; border: 1px solid #dee2e6; padding: 12px; border-radius: 6px; }
    .amount-pending { color: #dc3545; font-weight: 700; }
    .amount-paid { color: #198754; font-weight: 700; }
</style>

<div class="container mt-15">
    <div class="row">
        <div class="col-sm-12">
            <div class="card-header">
                <h3 style="text-align: center;">Pending / Due Payments Report</h3>
            </div>
        </div>

        <div class="col-md-12">
            <form method="get" class="form-inline mb-4 mt-1 p-3 card-header">
                <div class="row">
                    <div class="col-sm-3">
                        <label for="search" class="mr-2">Search:</label>
                        <input type="text" id="search" class="form-control mr-2" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="Invoice no, client, dates">
                    </div>
                    <div class="col-sm-3">
                        <label for="fromDate" class="mr-2">From Date:</label>
                        <input type="date" id="fromDate" class="form-control mr-2" value="<?php echo htmlspecialchars($fromDate); ?>" name="fromDate">
                    </div>
                    <div class="col-sm-3">
                        <label for="toDate" class="mr-2">To Date:</label>
                        <input type="date" id="toDate" class="form-control mr-2" name="toDate" value="<?php echo htmlspecialchars($toDate); ?>">
                    </div>
                    <div class="col-sm-3 mt-4">
                        <button type="submit" class="btn btn-success">Search</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-md-3 mb-3"><div class="summary-box"><strong>Due (Invoice Total)</strong><br><?php echo number_format($totalInvoice, 2); ?></div></div>
        <div class="col-md-3 mb-3"><div class="summary-box"><strong>Total Pending</strong><br><span class="amount-pending"><?php echo number_format($totalPending, 2); ?></span></div></div>
        <div class="col-md-3 mb-3"><div class="summary-box"><strong>Received (shown)</strong><br><span class="amount-paid"><?php echo number_format($totalReceived, 2); ?></span></div></div>
        <div class="col-md-3 mb-3"><div class="summary-box"><strong>TDS (shown)</strong><br><?php echo number_format($totalTds, 2); ?></div></div>

        <table id="example" class="display mt-3" style="width:100%">
            <thead>
                <tr>
                    <th>Sr.No.</th>
                    <th>Invoice Date</th>
                    <th>Party Name</th>
                    <th>Country, State</th>
                    <th>Invoice Number</th>
                    <th>Due Date</th>
                    <th>Duration</th>
                    <th>Grand Amount</th>
                    <th>Tax</th>
                    <th>Received</th>
                    <th>TDS</th>
                    <th>Payment Credit (paid_total)</th>
                    <th>Pending</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($rows as $inv): ?>
                    <?php
                        $pending = (float) ($inv['pending_total'] ?? 0);
                        $received = (float) ($inv['received_total'] ?? 0);
                        $tds = (float) ($inv['tds_total'] ?? 0);
                        $paidTotal = (float) ($inv['paid_total'] ?? 0);
                        $statusText = $pending <= 0.01 ? 'Paid' : 'Pending';
                    ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo date('d-m-Y', strtotime($inv['invoiceDate'])); ?></td>
                        <td><?php echo htmlspecialchars($inv['ClientName']); ?></td>
                        <td><?php echo htmlspecialchars(($inv['CountryName'] ?? '') . ', ' . ($inv['StateName'] ?? '')); ?></td>
                        <td>
                            <?php echo htmlspecialchars($inv['invoiceNumber']); ?>
                            <br>
                            <span class="badge badge-warning text-white">
                                <a href="checkinvoice.php?invoice_id=<?php echo (int)$inv['id']; ?>">Preview Invoice</a>
                            </span>
                        </td>
                        <td><?php echo date('d-m-Y', strtotime($inv['duedate'])); ?></td>
                        <td><?php echo !empty($inv['duration_days']) ? htmlspecialchars($inv['duration_days']) : '-'; ?></td>
                        <td><?php echo number_format((float) $inv['finalAmount'], 2); ?></td>
                        <td>
                            SGST: <?php echo number_format((float) $inv['sgstAmount'], 2); ?><br>
                            CGST: <?php echo number_format((float) $inv['cgstAmount'], 2); ?><br>
                            IGST: <?php echo number_format((float) $inv['igstAmount'], 2); ?><br>
                            Bank Charge: <?php echo number_format((float) ($inv['bankChargeAmount'] ?? 0), 2); ?>
                        </td>
                        <td><?php echo number_format($received, 2); ?></td>
                        <td><?php echo number_format($tds, 2); ?></td>
                        <td><?php echo number_format($paidTotal, 2); ?></td>
                        <td class="amount-pending"><?php echo number_format($pending, 2); ?></td>
                        <td><?php echo htmlspecialchars($statusText); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#example').DataTable({
            dom: 'Bfrtip',
            buttons: ['excelHtml5', 'csvHtml5']
        });
    });
</script>

<?php include("EvaraFroentend/layout/footer.php"); ?>

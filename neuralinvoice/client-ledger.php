<?php
include 'EvaraFroentend/layout/head.php';
include 'includes/auth.php';
include 'EvaraFroentend/layout/header.php';

$clientId = isset($_GET['client_id']) ? (int) $_GET['client_id'] : 0;
$fromDate = $_GET['fromDate'] ?? date('Y-m-d', strtotime('-1 year'));
$toDate = $_GET['toDate'] ?? date('Y-m-d');

$clients = $con->query('SELECT ClientID, ClientName FROM clientmaster WHERE DelStatus = 1 ORDER BY ClientName');

$ledger = [];
$summary = ['invoiced' => 0, 'received' => 0, 'tds' => 0, 'pending' => 0];

if ($clientId > 0) {
    // Proforma invoices
    $invSql = "SELECT i.id, w.invoiceNumber, i.invoiceDate, i.totalAmount, i.status,
               'Proforma' AS doc_type
               FROM invoice i
               LEFT JOIN workamount w ON i.id = w.invoice_id
               WHERE i.ClientID = $clientId
               AND i.status <> 3
               AND i.creation_date BETWEEN '$fromDate' AND '$toDate'
               GROUP BY i.id
               ORDER BY i.invoiceDate";
    $inv = $con->query($invSql);
    while ($row = $inv->fetch_assoc()) {
        $ledger[] = [
            'date' => $row['invoiceDate'],
            'ref' => $row['invoiceNumber'],
            'type' => $row['doc_type'],
            'debit' => (float) $row['totalAmount'],
            'credit' => 0,
            'tds' => 0,
            'status' => $row['status'],
        ];
        $summary['invoiced'] += (float) $row['totalAmount'];
    }

    // Tax / received payments
    $paySql = "SELECT rp.*, w.invoiceNumber, i.invoiceDate
               FROM recived_payment rp
               LEFT JOIN invoice i ON rp.workAmount_invoice_id = i.id
               LEFT JOIN workamount w ON rp.workAmount_invoice_id = w.invoice_id
               WHERE rp.ClientID = $clientId
               AND (i.status IS NULL OR i.status <> 3)
               AND DATE(rp.created_at) BETWEEN '$fromDate' AND '$toDate'
               ORDER BY rp.created_at";
    $pay = $con->query($paySql);
    while ($row = $pay->fetch_assoc()) {
        $tds = isset($row['tds_amount']) ? (float) $row['tds_amount'] : 0;
        $net = isset($row['received_net']) && $row['received_net'] > 0
            ? (float) $row['received_net']
            : (float) $row['recived_amount'];
        $ledger[] = [
            'date' => substr($row['created_at'], 0, 10),
            'ref' => $row['taxinvoiceNumber'] ?: $row['invoiceNumber'],
            'type' => 'Payment Received',
            'debit' => 0,
            'credit' => $net,
            'tds' => $tds,
            'status' => $row['status'],
        ];
        $summary['received'] += $net;
        $summary['tds'] += $tds;
    }

    usort($ledger, function ($a, $b) {
        return strcmp($a['date'], $b['date']);
    });
    $summary['pending'] = $summary['invoiced'] - $summary['received'];
}
?>
<div class="container mt-15">
    <div class="card">
        <div class="card-header"><h3>Client Ledger (Laser Report)</h3></div>
        <div class="card-body">
            <form method="get" class="row g-3 mb-4">
                <div class="col-md-4">
                    <label>Client</label>
                    <select name="client_id" class="form-control" required>
                        <option value="">-- Select Client --</option>
                        <?php while ($c = $clients->fetch_assoc()): ?>
                        <option value="<?php echo (int) $c['ClientID']; ?>" <?php echo $clientId === (int) $c['ClientID'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['ClientName']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>From</label>
                    <input type="date" name="fromDate" class="form-control" value="<?php echo htmlspecialchars($fromDate); ?>">
                </div>
                <div class="col-md-3">
                    <label>To</label>
                    <input type="date" name="toDate" class="form-control" value="<?php echo htmlspecialchars($toDate); ?>">
                </div>
                <div class="col-md-2 align-self-end">
                    <button class="btn btn-success">Show Ledger</button>
                </div>
            </form>

            <?php if ($clientId > 0): ?>
            <div class="row mb-4">
                <div class="col-md-3"><div class="p-3 border bg-light"><strong>Total Invoiced</strong><br>₹<?php echo number_format($summary['invoiced'], 2); ?></div></div>
                <div class="col-md-3"><div class="p-3 border bg-light"><strong>Received</strong><br>₹<?php echo number_format($summary['received'], 2); ?></div></div>
                <div class="col-md-3"><div class="p-3 border bg-light"><strong>TDS Deducted</strong><br>₹<?php echo number_format($summary['tds'], 2); ?></div></div>
                <div class="col-md-3"><div class="p-3 border bg-danger text-white"><strong>Outstanding</strong><br>₹<?php echo number_format($summary['pending'], 2); ?></div></div>
            </div>
            <table class="table table-bordered" id="ledgerTable">
                <thead>
                    <tr><th>Date</th><th>Reference</th><th>Type</th><th>Debit (Bill)</th><th>Credit (Paid)</th><th>TDS</th></tr>
                </thead>
                <tbody>
                <?php foreach ($ledger as $line): ?>
                    <tr>
                        <td><?php echo !empty($line['date']) ? date('d-m-Y', strtotime($line['date'])) : '-'; ?></td>
                        <td><?php echo htmlspecialchars($line['ref']); ?></td>
                        <td><?php echo htmlspecialchars($line['type']); ?></td>
                        <td><?php echo $line['debit'] > 0 ? '₹' . number_format($line['debit'], 2) : '-'; ?></td>
                        <td><?php echo $line['credit'] > 0 ? '₹' . number_format($line['credit'], 2) : '-'; ?></td>
                        <td><?php echo $line['tds'] > 0 ? '₹' . number_format($line['tds'], 2) : '-'; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>$(function(){ $('#ledgerTable').DataTable({ dom: 'Bfrtip', pageLength: 50 }); });</script>
<?php include 'EvaraFroentend/layout/footer.php'; ?>

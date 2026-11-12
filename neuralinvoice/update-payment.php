<?php
include("EvaraFroentend/layout/head.php");
include("EvaraFroentend/layout/header.php");
include_once 'includes/schema-bootstrap.php';

ni_ensure_database_schema($con);

$clientId = isset($_GET['clientId']) ? (int) $_GET['clientId'] : 0;
$clients = $con->query("SELECT ClientID, ClientName FROM clientmaster WHERE DelStatus = 1 ORDER BY ClientName");

function ni_payment_clean_return_url(string $url): string
{
    $parts = parse_url($url);
    $path = $parts['path'] ?? 'update-payment.php';
    if ($path === '') {
        $path = 'update-payment.php';
    }

    $query = [];
    if (!empty($parts['query'])) {
        parse_str($parts['query'], $query);
    }
    unset($query['error'], $query['success']);

    $cleanUrl = $path;
    if (!empty($query)) {
        $cleanUrl .= '?' . http_build_query($query);
    }
    if (!empty($parts['fragment'])) {
        $cleanUrl .= '#' . $parts['fragment'];
    }
    return $cleanUrl;
}

$cancelReturnTo = ni_payment_clean_return_url($_SERVER['REQUEST_URI'] ?? 'update-payment.php');

$whereClient = $clientId > 0 ? "AND i.ClientID = $clientId" : '';
$sql = "SELECT w.invoice_id,
               cm.pinNo,
               cm.ClientName,
               conm.CountryName,
               sm.StateName,
               i.id,
               i.invoiceDate,
               i.concern,
               i.mobile,
               i.deliverydate,
               i.duedate,
               i.duration_days,
               i.ClientID,
               i.sacCode,
               i.subtotal,
               i.discount,
               i.finalAmount,
               i.sgstAmount,
               i.cgstAmount,
               i.igstAmount,
               i.bankChargePercent,
               i.bankChargeAmount,
               i.totalAmount,
               i.status,
               COALESCE(pay.received_amount_total, 0) AS received_amount_total,
               COALESCE(pay.tds_amount_total, 0) AS tds_amount_total,
               COALESCE(pay.paid_total, 0) AS paid_total,
               w.invoiceNumber
        FROM invoice i
        LEFT JOIN workamount w ON i.id = w.invoice_id
        LEFT JOIN clientmaster cm ON i.ClientID = cm.ClientID
        LEFT JOIN countrymaster conm ON cm.Country = conm.CountryID
        LEFT JOIN statemaster sm ON cm.StateID = sm.StateID
        LEFT JOIN (
            SELECT workAmount_invoice_id,
                   SUM(recived_amount) AS received_amount_total,
                   SUM(tds_amount) AS tds_amount_total,
                   SUM(CASE WHEN received_net > 0 THEN received_net ELSE recived_amount END) AS paid_total
            FROM recived_payment
            WHERE status = 1
            GROUP BY workAmount_invoice_id
        ) pay ON pay.workAmount_invoice_id = i.id
        WHERE i.status IN (1, 2)
              $whereClient
        GROUP BY i.id
        ORDER BY i.id DESC";
$result = $con->query($sql);
if ($result === false) {
    die("Error fetching invoices: " . $con->error);
}
?>

<style>
    label { font-weight: 700; }
    .amount-pending { color: #dc3545; font-weight: 700; }
    .amount-paid { color: #198754; font-weight: 700; }
</style>

<div class="container mt-15">
    <div class="row">
        <div class="col-sm-12">
            <div class="card-header">
                <h3 style="text-align: center;">Received Payment</h3>
            </div>
        </div>

        <div class="col-md-12">
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?php
                    $successMessages = [
                        'invoice_cancelled' => 'Invoice cancelled successfully.',
                        'invoice_already_cancelled' => 'Invoice already cancelled hai.',
                        'payment_recorded' => 'Payment recorded successfully.',
                    ];
                    echo $successMessages[$_GET['success']] ?? 'Payment recorded successfully.';
                    ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['error']) && !isset($_GET['success'])): ?>
                <div class="alert alert-danger">
                    <?php
                    $paymentErrors = [
                        'cancel_reason_required' => 'Invoice cancel karne ke liye reason required hai.',
                        'invoice_cancel_failed' => 'Invoice cancel nahi ho paya. Paid invoice cancel nahi ho sakti.',
                        'invalid_payment' => 'Payment amount, client, ya invoice missing hai. Please form dobara open karke try karein.',
                        'invoice_not_found' => 'Invoice nahi mila. Please invoice list refresh karke dobara try karein.',
                        'cancelled_invoice_payment' => 'Cancelled invoice par payment receive nahi ho sakti.',
                        'payment_exceeds_pending' => 'Payment pending amount se zyada hai. Please amount check karein.',
                        'payment_update_failed' => 'Payment save nahi ho paya. Please database/log check karein.',
                    ];
                    echo $paymentErrors[$_GET['error']] ?? 'Request process nahi ho payi. Please page refresh karke dobara try karein.';
                    ?>
                </div>
            <?php endif; ?>

            <form method="GET" class="form-inline mb-5 mt-1 p-3 card-header" action="update-payment.php">
                <div class="row">
                    <div class="col-sm-3">
                        <label for="clientId" class="mr-2">Client Name:</label>
                        <select class="form-control" id="clientId" name="clientId">
                            <option value="0">All Clients</option>
                            <?php if ($clients): while ($client = $clients->fetch_assoc()): ?>
                                <option value="<?php echo (int) $client['ClientID']; ?>" <?php echo $clientId === (int) $client['ClientID'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($client['ClientName']); ?>
                                </option>
                            <?php endwhile; endif; ?>
                        </select>
                    </div>
                    <div class="col-sm-3 mt-4">
                        <button type="submit" class="btn btn-success"><i class="fas fa-search"></i> Search</button>
                    </div>
                </div>
            </form>
        </div>

        <table class="table table-bordered mt-3" id="example" style="width:100%">
            <thead>
                <tr>
                    <th>Invoice Date</th>
                    <th>Party Name</th>
                    <th>Country City</th>
                    <th>Invoice Number</th>
                    <th>Duration</th>
                    <th>Due Date</th>
                    <th>Gross Total</th>
                    <th>Tax</th>
                    <th>Grand Amount</th>
                    <th>Received</th>
                    <th>TDS</th>
                    <th>Pending</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                        // paid_total comes from received_net primarily (and received_amount in some cases).
                        // Keep UI consistent with backend checks.
                        $paidTotal = (float) ($row['paid_total'] ?? 0);
                        $pending = max(0, (float) $row['totalAmount'] - $paidTotal);
                        $isPaid = $pending <= 0.01;
                    ?>
                    <tr>
                        <td><?php echo date('d-m-Y', strtotime($row['invoiceDate'])); ?></td>
                        <td><?php echo htmlspecialchars($row['ClientName']); ?></td>
                        <td><?php echo htmlspecialchars(($row['CountryName'] ?? '') . ', ' . ($row['StateName'] ?? '')); ?></td>
                        <td>
                            <?php echo htmlspecialchars($row['invoiceNumber']); ?><br>
                            <span class="badge badge-warning text-white"><a target="_blanck" href="checkinvoice.php?invoice_id=<?php echo (int) $row['id']; ?>">Preview Invoice</a></span>
                        </td>
                        <td><?php echo !empty($row['duration_days']) ? htmlspecialchars($row['duration_days']) : '-'; ?></td>
                        <td><?php echo date('d-m-Y', strtotime($row['duedate'])); ?></td>
                        <td><?php echo number_format((float) $row['finalAmount'], 2); ?></td>
                        <td>
                            SGST: <?php echo number_format((float) $row['sgstAmount'], 2); ?><br>
                            CGST: <?php echo number_format((float) $row['cgstAmount'], 2); ?><br>
                            IGST: <?php echo number_format((float) $row['igstAmount'], 2); ?><br>
                            Bank Charge: <?php echo number_format((float) ($row['bankChargeAmount'] ?? 0), 2); ?>
                        </td>
                        <td><?php echo number_format((float) $row['totalAmount'], 2); ?></td>
                        <td class="amount-paid"><?php echo number_format((float) $row['received_amount_total'], 2); ?></td>
                        <td><?php echo number_format((float) $row['tds_amount_total'], 2); ?></td>
                        <td class="amount-pending"><?php echo number_format($pending, 2); ?></td>
                        <td><?php echo $isPaid ? '<span class="badge bg-success">Paid</span>' : '<span class="badge bg-warning text-dark">Partial / Pending</span>'; ?></td>
                        <td>
                            <?php if (!$isPaid): ?>
                                <button type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="Receive Payment"
                                    onclick="openModal(<?php echo (int) $row['id']; ?>, <?php echo (int) $row['invoice_id']; ?>, <?php echo (float) $row['totalAmount']; ?>, <?php echo (int) $row['ClientID']; ?>, <?php echo $pending; ?>)"
                                    class="btn btn-primary btn-sm"><i class="fas fa-hand-holding-usd"></i>
                                </button>
                            <?php else: ?>
                                <span class="text-success">Completed</span>
                            <?php endif; ?>
                            <?php if ($paidTotal <= 0.01): ?>
                                <button type="button" class="btn btn-danger btn-sm mt-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#cancelInvoiceModal"
                                    data-invoice-id="<?php echo (int) $row['id']; ?>"
                                    title="Cancel Invoice">
                                    Cancel Invoice
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Receive Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="update-client-invoice.php" class="basic-form" method="post" id="updateForm">
                            <input type="hidden" name="invoiceId" id="invoiceId" value="">
                            <input type="hidden" name="winvoice_id" id="winvoice_id" value="">
                            <input type="hidden" name="ClientID" id="ClientID" value="">

                            <div class="form-group">
                                <label for="totalAmount">Final Amount In Invoice</label>
                                <input type="text" readonly name="totalAmount" id="totalAmount" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label for="pendingAmount">Pending Amount</label>
                                <input type="text" readonly id="pendingAmount" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label for="getAmount">Received Amount</label>
                                <input type="number" step="0.01" min="0" name="getAmount" id="getAmount" class="form-control" required />
                            </div>
                            <div class="form-group">
                                <label for="tdsAmount">TDS Amount</label>
                                <input type="number" step="0.01" min="0" name="tdsAmount" id="tdsAmount" class="form-control" value="0" />
                            </div>
                            <div class="form-group">
                                <label for="finalAmount">Payment Credit</label>
                                <input type="text" readonly name="finalAmount" id="finalAmount" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label for="received_date">Received Date</label>
                                <input type="date" name="received_date" id="received_date" class="form-control" required 
                                    value="<?php echo date('Y-m-d'); ?>" />
                            </div>
                            <div class="form-group">
                                <label for="payment_note">Note</label>
                                <input type="text" name="payment_note" id="payment_note" class="form-control" placeholder="Cheque/NEFT/reference note">
                            </div>
                            <button type="submit" name="submit" id="submitButton" class="btn btn-success mt-3">Save Payment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Invoice Modal -->
<div class="modal fade" id="cancelInvoiceModal" tabindex="-1" aria-labelledby="cancelInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelInvoiceModalLabel">Cancel Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="cancel-invoice.php" method="post" id="cancelInvoiceForm">
                <div class="modal-body">
                    <p>Do you want to cancel this invoice? Please provide a reason.</p>
                    <input type="hidden" name="invoice_id" id="modal_invoice_id">
                    <input type="hidden" name="return_to" value="<?php echo htmlspecialchars($cancelReturnTo, ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="mb-3">
                        <label for="cancel_reason" class="form-label">Reason for Cancellation</label>
                        <textarea class="form-control" id="cancel_reason" name="cancel_reason" rows="3" required maxlength="255" placeholder="e.g., Incorrect amount, client request"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Confirm Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script>
    var currentPending = 0;

    function openModal(id, invoice_id, totalAmount, clientID, pendingAmount) {
        currentPending = parseFloat(pendingAmount) || 0;
        $('#invoiceId').val(id);
        $('#totalAmount').val((parseFloat(totalAmount) || 0).toFixed(2));
        $('#pendingAmount').val(currentPending.toFixed(2));
        $('#winvoice_id').val(invoice_id);
        $('#ClientID').val(clientID);
        $('#getAmount').val(currentPending.toFixed(2));
        $('#tdsAmount').val('0.00');
        $('#payment_note').val('');
        $('#received_date').val(new Date().toISOString().slice(0, 10));
        updateFinalAmount();
        var paymentModal = document.getElementById('myModal');
        if (paymentModal && window.bootstrap) {
            bootstrap.Modal.getOrCreateInstance(paymentModal).show();
        }
    }

    function updateFinalAmount() {
        var received = parseFloat($('#getAmount').val()) || 0;
        var tds = parseFloat($('#tdsAmount').val()) || 0;
        $('#finalAmount').val((received + tds).toFixed(2));
    }

    $('#getAmount, #tdsAmount').on('input', updateFinalAmount);
    $('#updateForm').on('submit', function (e) {
        var credit = parseFloat($('#finalAmount').val()) || 0;
        if (credit <= 0) {
            e.preventDefault();
            alert('Enter received amount or TDS amount.');
            return false;
        }
        if (credit > currentPending + 0.01) {
            e.preventDefault();
            alert('Payment cannot be more than pending amount.');
            return false;
        }
    });

    $(document).ready(function() {
        $('#example').DataTable({
            dom: 'Bfrtip',
            buttons: ['excelHtml5']
        });

        var cancelInvoiceModal = document.getElementById('cancelInvoiceModal');
        if (cancelInvoiceModal) {
            cancelInvoiceModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var invoiceId = button ? button.getAttribute('data-invoice-id') : '';
                cancelInvoiceModal.querySelector('#modal_invoice_id').value = invoiceId || '';
                cancelInvoiceModal.querySelector('#cancel_reason').value = '';
            });
        }
    });
</script>

<?php $GLOBALS['NI_SKIP_FOOTER_JQUERY'] = true; ?>
<?php include("EvaraFroentend/layout/footer.php"); ?>

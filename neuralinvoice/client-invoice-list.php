<?php
include("EvaraFroentend/layout/head.php");
include("EvaraFroentend/layout/header.php");

$searchQuery = isset($_GET['search']) ? $con->real_escape_string($_GET['search']) : '';
$clientFilter = isset($_GET['client_id']) ? (int) $_GET['client_id'] : 0;
$statusFilter = $_GET['status_filter'] ?? 'active';
$statusFilter = $statusFilter === 'cancelled' ? 'cancelled' : 'active';
$invoiceStatusSql = $statusFilter === 'cancelled' ? '3' : '0';

$fromDate = isset($_GET['fromDate']) ? $_GET['fromDate'] : '';
$toDate = isset($_GET['toDate']) ? $_GET['toDate'] : '';

$clientsForFilter = $con->query('SELECT ClientID, ClientName FROM clientmaster WHERE DelStatus = 1 ORDER BY ClientName');

// Ensure that both from and to dates are provided for a valid date range
if (empty($fromDate) && empty($toDate)) {
    $oneMonthAgo = date('Y-m-d', strtotime('-1 month'));
    $fromDate = $oneMonthAgo;
    $toDate = date('Y-m-d'); // Current date
}
?>

<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<style>
    label {
        font-weight: 700;
    }

    .invoice-list-page {
        max-width: 1440px;
        margin-top: 28px;
    }

    .invoice-list-card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .invoice-list-header {
        background: #fff;
        border-bottom: 1px solid #e5e7eb;
        padding: 18px 20px;
        gap: 12px;
    }

    .invoice-list-header h3 {
        margin: 0;
        font-size: 22px;
        font-weight: 700;
        color: #1f2937;
    }

    .invoice-list-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .invoice-filter-panel {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 18px;
    }

    .invoice-filter-panel label {
        display: block;
        margin-bottom: 6px;
        color: #374151;
        font-size: 13px;
    }

    .invoice-filter-panel .form-control {
        min-height: 38px;
        border-color: #d1d5db;
    }

    .invoice-table-wrap {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: auto;
    }

    #example {
        margin: 0 !important;
        width: 100% !important;
    }

    #example thead th {
        background: #f3f4f6;
        color: #111827;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .02em;
        border-bottom: 1px solid #d1d5db;
        white-space: nowrap;
        vertical-align: middle;
    }

    #example tbody td {
        vertical-align: middle;
        color: #1f2937;
        font-size: 13px;
    }

    #example tbody td:nth-child(7),
    #example tbody td:nth-child(8) {
        min-width: 180px;
        max-width: 320px;
        white-space: normal;
    }

    #example tbody td:nth-child(10),
    #example tbody td:nth-child(11) {
        white-space: nowrap;
    }

    .invoice-list-page a:not(.btn) {
        color: #0d6efd;
    }

    .invoice-list-page .badge a {
        color: #fff;
    }

    .dataTables_wrapper {
        padding: 0;
    }

    .dataTables_wrapper .dt-buttons {
        padding: 0 0 12px;
    }

    .dataTables_wrapper .dt-button {
        border: 1px solid #d1d5db !important;
        border-radius: 6px !important;
        background: #fff !important;
        color: #111827 !important;
        padding: 6px 12px !important;
        margin-right: 6px !important;
        box-shadow: none !important;
    }

    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        padding: 12px 0 0 !important;
        font-size: 13px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 6px !important;
        padding: 4px 10px !important;
    }

    .dataTables_processing {
        border-radius: 8px !important;
        border: 1px solid #e5e7eb !important;
        box-shadow: 0 12px 30px rgba(15, 23, 42, .12);
    }

    @media (max-width: 767px) {
        .invoice-list-header {
            align-items: flex-start !important;
        }

        .invoice-list-actions .btn {
            width: 100%;
        }
    }
</style>
<div class="container-fluid invoice-list-page">
    <div class="card invoice-list-card">
        <div class="card-header invoice-list-header d-flex flex-wrap justify-content-between align-items-center">
            <h3>
                <?php echo $statusFilter === 'cancelled' ? 'Cancelled Prefarma Invoice List' : 'Prefarma Invoice List'; ?>
            </h3>
            <div class="invoice-list-actions">
                <a href="create-newInvoice.php" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Create Invoice</a>
                <a href="update-payment.php" class="btn btn-sm btn-success"><i class="fas fa-hand-holding-usd"></i> Receive Payment</a>
                <a href="recived-payment-form-clinet-report.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-chart-pie"></i> Payment Report</a>
            </div>
        </div>

        <div class="card-body">
            <?php if (isset($_GET['success']) && $_GET['success'] === 'invoice_cancelled'): ?>
                <div class="alert alert-success">Invoice cancelled successfully.</div>
            <?php elseif (isset($_GET['error']) && $_GET['error'] === 'cancel_reason_required'): ?>
                <div class="alert alert-danger">Invoice cancel karne ke liye reason required hai.</div>
            <?php elseif (isset($_GET['error']) && $_GET['error'] === 'invoice_cancel_failed'): ?>
                <div class="alert alert-danger">Invoice cancel nahi ho paya. Agar payment receive ho chuki hai to cancel block rahega.</div>
            <?php endif; ?>

            <form method="get" id="invoiceFilterForm" class="invoice-filter-panel">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3 col-sm-6">
                        <label for="client_id">Client:</label>
                        <select name="client_id" id="client_id" class="form-control">
                            <option value="0">All Clients</option>
                            <?php if ($clientsForFilter):
                                while ($cf = $clientsForFilter->fetch_assoc()): ?>
                                    <option value="<?php echo (int) $cf['ClientID']; ?>" <?php echo $clientFilter === (int) $cf['ClientID'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cf['ClientName']); ?>
                                    </option>
                                <?php endwhile; endif; ?>
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label for="search">Search:</label>
                        <input type="text" id="search" class="form-control" name="search"
                            value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                            placeholder="Invoice ID, Client Name, Date">
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <label for="status_filter">Status:</label>
                        <select name="status_filter" id="status_filter" class="form-control">
                            <option value="active" <?php echo $statusFilter === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <label for="fromDate">From Date:</label>
                        <input type="date" id="fromDate" class="form-control" value="<?php echo $fromDate; ?>" name="fromDate" placeholder="YYYY-MM-DD">
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <label for="toDate">To Date:</label>
                        <input type="date" id="toDate" class="form-control" name="toDate" value="<?php echo $toDate; ?>" placeholder="YYYY-MM-DD">
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <button type="submit" class="btn btn-success w-100"><i class="fas fa-search"></i> Search</button>
                    </div>
                </div>
            </form>

            <div class="invoice-table-wrap">
                <table id="example" class="table table-hover table-bordered align-middle mb-0" style="width:100%">
            <thead>
                <tr>
                    <th>Sr.no</th>
                    <th>Invoice ID</th>
                    <!-- <th>Client Code</th> -->
                    <th>Client Name</th>
                    <th>Invoice Date</th>
                    <th>Duration</th>
                    <th>Concern</th>
                    <th>Descriptions</th>
                    <th>Amounts</th>
                    <th>Status</th>
                  
                    <th>Send</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
                </table>
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
            <form action="cancel-invoice.php" method="post">
                <div class="modal-body">
                    <p>Is invoice ko cancel karna hai? Please provide a reason.</p>
                    <input type="hidden" name="invoice_id" id="modal_invoice_id">
                    <input type="hidden" name="return_to" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'client-invoice-list.php'); ?>">
                    <div class="mb-3">
                        <label for="cancel_reason" class="form-label">Reason for Cancellation</label>
                        <textarea class="form-control" id="cancel_reason" name="cancel_reason" rows="3" required maxlength="255" placeholder="e.g., Duplicate invoice, incorrect details"></textarea>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script>
    $(document).ready(function () {
        var invoiceTable = $('#example').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            autoWidth: false,
            scrollX: true,
            pageLength: 10,
            dom: 'Bfrtip',
            buttons: [
                'excelHtml5',
                'csvHtml5'
            ],
            ajax: {
                url: 'ajax-client-invoices.php',
                type: 'GET',
                data: function (d) {
                    d.client_id = $('#client_id').val();
                    d.search_text = $('#search').val();
                    d.status_filter = $('#status_filter').val();
                    d.fromDate = $('#fromDate').val();
                    d.toDate = $('#toDate').val();
                }
            },
            order: [[1, 'desc']],
            columns: [
                { data: 'sr_no', orderable: false },
                { data: 'invoice' },
                { data: 'client_name' },
                { data: 'invoice_date' },
                { data: 'duration' },
                { data: 'concern' },
                { data: 'descriptions', orderable: false },
                { data: 'amounts', orderable: false },
                { data: 'status' },
                { data: 'send', orderable: false },
                { data: 'action', orderable: false }
            ],
            columnDefs: [
                { targets: [6, 7], className: 'text-wrap' },
                { targets: [0, 8, 9, 10], className: 'text-center' }
            ],
            drawCallback: function () {
                if (window.bootstrap) {
                    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
                        new bootstrap.Tooltip(el);
                    });
                }
            }
        });

        $('#invoiceFilterForm').on('submit', function (event) {
            event.preventDefault();
            invoiceTable.ajax.reload();
        });

        $('#client_id, #status_filter, #fromDate, #toDate').on('change', function () {
            invoiceTable.ajax.reload();
        });

        $('#search').on('keyup', function (event) {
            if (event.key === 'Enter' || this.value.length === 0 || this.value.length >= 3) {
                invoiceTable.ajax.reload();
            }
        });
    });

    var cancelInvoiceModal = document.getElementById('cancelInvoiceModal');
    if (cancelInvoiceModal) {
        cancelInvoiceModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var invoiceId = button.getAttribute('data-invoice-id');
            var modalInvoiceIdInput = cancelInvoiceModal.querySelector('#modal_invoice_id');
            modalInvoiceIdInput.value = invoiceId;
            cancelInvoiceModal.querySelector('#cancel_reason').value = '';
        });
    }
</script>
<?php $GLOBALS['NI_SKIP_FOOTER_JQUERY'] = true; ?>
<?php include("EvaraFroentend/layout/footer.php"); ?>

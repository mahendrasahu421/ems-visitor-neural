<?php
include 'connection.php';

header('Content-Type: application/json');

function ni_json_h($value)
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function ni_invoice_date_or_default($value, $default)
{
    $value = (string) $value;
    return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : $default;
}

function ni_invoice_count(mysqli $con, string $whereSql): int
{
    $sql = "
        SELECT COUNT(DISTINCT invoice.id) AS total
        FROM invoice
        LEFT JOIN workamount ON invoice.id = workamount.invoice_id
        LEFT JOIN clientmaster ON invoice.ClientID = clientmaster.ClientID
        WHERE $whereSql
    ";
    $result = $con->query($sql);
    if (!$result) {
        return 0;
    }
    $row = $result->fetch_assoc();
    return (int) ($row['total'] ?? 0);
}

$draw = max(0, (int) ($_GET['draw'] ?? 0));
$start = max(0, (int) ($_GET['start'] ?? 0));
$length = (int) ($_GET['length'] ?? 10);
if ($length < 1 || $length > 100) {
    $length = 10;
}

$defaultFrom = date('Y-m-d', strtotime('-1 month'));
$defaultTo = date('Y-m-d');
$fromDate = ni_invoice_date_or_default($_GET['fromDate'] ?? '', $defaultFrom);
$toDate = ni_invoice_date_or_default($_GET['toDate'] ?? '', $defaultTo);

$clientFilter = max(0, (int) ($_GET['client_id'] ?? 0));
$statusFilter = ($_GET['status_filter'] ?? 'active') === 'cancelled' ? 'cancelled' : 'active';
$invoiceStatusSql = $statusFilter === 'cancelled' ? 3 : 0;

$customSearch = trim((string) ($_GET['search_text'] ?? ''));
$dataTableSearch = trim((string) ($_GET['search']['value'] ?? ''));
$searchQuery = $customSearch !== '' ? $customSearch : $dataTableSearch;

$baseWhere = [
    "invoice.creation_date BETWEEN '" . $con->real_escape_string($fromDate) . "' AND '" . $con->real_escape_string($toDate) . "'",
    'invoice.status = ' . $invoiceStatusSql,
];

if ($clientFilter > 0) {
    $baseWhere[] = 'invoice.ClientID = ' . $clientFilter;
}

$filteredWhere = $baseWhere;
if ($searchQuery !== '') {
    $searchLike = "'%" . $con->real_escape_string($searchQuery) . "%'";
    $filteredWhere[] = "(workamount.invoiceNumber LIKE $searchLike
        OR clientmaster.ClientName LIKE $searchLike
        OR clientmaster.ClientCode LIKE $searchLike
        OR invoice.invoiceDate LIKE $searchLike
        OR invoice.concern LIKE $searchLike)";
}

$baseWhereSql = implode(' AND ', $baseWhere);
$filteredWhereSql = implode(' AND ', $filteredWhere);

$orderableColumns = [
    1 => 'invoice_number',
    2 => 'clientmaster.ClientName',
    3 => 'invoice.invoiceDate',
    4 => 'invoice.duration_days',
    5 => 'invoice.concern',
    8 => 'invoice.status',
];
$orderColumnIndex = (int) ($_GET['order'][0]['column'] ?? 0);
$orderColumn = $orderableColumns[$orderColumnIndex] ?? 'invoice.id';
$orderDirection = strtolower((string) ($_GET['order'][0]['dir'] ?? 'desc')) === 'asc' ? 'ASC' : 'DESC';

$recordsTotal = ni_invoice_count($con, $baseWhereSql);
$recordsFiltered = ni_invoice_count($con, $filteredWhereSql);

$sql = "
    SELECT invoice.*,
           GROUP_CONCAT(workamount.description ORDER BY workamount.id SEPARATOR ', ') AS descriptions,
           GROUP_CONCAT(workamount.amount ORDER BY workamount.id SEPARATOR ', ') AS amounts,
           MIN(workamount.invoiceNumber) AS invoice_number,
           clientmaster.ClientCode,
           clientmaster.ClientName
    FROM invoice
    LEFT JOIN workamount ON invoice.id = workamount.invoice_id
    LEFT JOIN clientmaster ON invoice.ClientID = clientmaster.ClientID
    WHERE $filteredWhereSql
    GROUP BY invoice.id
    ORDER BY $orderColumn $orderDirection
    LIMIT $start, $length
";

$result = $con->query($sql);
if (!$result) {
    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => 'Error fetching invoice data.',
    ]);
    exit;
}

$data = [];
$srNo = $start + 1;
while ($invoice = $result->fetch_assoc()) {
    $invoiceId = (int) $invoice['id'];
    $invoiceNumber = ni_json_h($invoice['invoice_number']);
    $previewUrl = 'view-invoice.php?invoice_id=' . $invoiceId;
    $invoiceCell = $invoiceNumber
        . ' <span class="badge badge-primary"><a href="' . $previewUrl . '">Preview / Download</a></span>';

    $statusCell = '<span class="badge badge-success">Active</span>';
    if ((int) $invoice['status'] === 3) {
        $statusCell = '<span class="badge badge-danger">Cancelled</span>';
        if (!empty($invoice['cancelled_at'])) {
            $statusCell .= '<div class="small">' . ni_json_h(date('d-m-Y H:i', strtotime($invoice['cancelled_at']))) . '</div>';
        }
    }

    $sendCell = '<a href="send-invoice.php?invoice_id=' . $invoiceId . '" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Email"><i class="fas fa-paper-plane"></i></a>';
    if (!empty($invoice['sent_at'])) {
        $sendCell .= '<div class="mt-1 small text-success"><i class="fi-rs-check"></i> Sent: '
            . ni_json_h(date('d M, H:i', strtotime($invoice['sent_at']))) . '</div>';
    }

    if ((int) $invoice['status'] !== 3) {
        $actionCell = '<a href="edit-invoice.php?invoice_id=' . $invoiceId . '" class="btn btn-sm btn-warning mb-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="fas fa-edit"></i></a> '
            . '<button type="button" class="btn btn-sm btn-danger cancel-invoice-btn" data-bs-toggle="modal" data-bs-target="#cancelInvoiceModal" data-invoice-id="' . $invoiceId . '" title="Cancel"><i class="fas fa-times-circle"></i></button>';
    } else {
        $actionCell = !empty($invoice['cancel_reason']) ? ni_json_h($invoice['cancel_reason']) : '-';
    }

    $data[] = [
        'sr_no' => $srNo++,
        'invoice' => $invoiceCell,
        'client_name' => ni_json_h($invoice['ClientName']),
        'invoice_date' => !empty($invoice['invoiceDate']) ? ni_json_h(date('d-m-Y', strtotime($invoice['invoiceDate']))) : '-',
        'duration' => !empty($invoice['duration_days']) ? ni_json_h($invoice['duration_days']) : '-',
        'concern' => ni_json_h($invoice['concern']),
        'descriptions' => ni_json_h($invoice['descriptions']),
        'amounts' => ni_json_h($invoice['amounts']),
        'status' => $statusCell,
        'send' => $sendCell,
        'action' => $actionCell,
    ];
}

echo json_encode([
    'draw' => $draw,
    'recordsTotal' => $recordsTotal,
    'recordsFiltered' => $recordsFiltered,
    'data' => $data,
]);

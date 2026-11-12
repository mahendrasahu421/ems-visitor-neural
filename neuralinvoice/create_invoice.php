<?php
session_start();
include 'connection.php';
include 'includes/helpers.php';

$milestoneId = isset($_GET['milestone_id']) ? (int)$_GET['milestone_id'] : 0;
if ($milestoneId <= 0) {
    die('Invalid milestone_id');
}

// Fetch milestone + project + client
$sql = "SELECT 
            m.milestone_id,
            m.project_id,
            m.milestone_label,
            m.invoiced_amount,
            m.percentage,
            m.amount,
            m.amount_with_tax,
            m.due_date,
            p.client_id,
            p.total_value,
            p.tax_percent, -- Fetch project's tax percent
            c.ClientName
        FROM project_payment_milestones m
        JOIN client_projects p ON p.project_id = m.project_id
        JOIN clientmaster c ON c.ClientID = p.client_id
        WHERE m.milestone_id = '$milestoneId'";

$res = $con->query($sql);
if (!$res || $res->num_rows === 0) {
    die('Milestone not found');
}
$row = $res->fetch_assoc();

$clientId = (int)($row['client_id'] ?? 0);
$clientName = (string)($row['ClientName'] ?? '');
$projectTotal = (float)($row['total_value'] ?? 0);

$amountWithTax = (float)($row['amount_with_tax'] ?? 0);
$baseAmount = (float)($row['amount'] ?? 0);
$invoicedAmount = (float)($row['invoiced_amount'] ?? 0);
$projectTaxPercent = (float)($row['tax_percent'] ?? 0);

// Calculate remaining amount to be invoiced
$remainingBaseAmount = max(0, $baseAmount - $invoicedAmount);
$milestonePctToPrefill = $projectTotal > 0 ? ($remainingBaseAmount / $projectTotal) * 100 : 0;

$dueDate = $row['due_date'] ?? '';
$dueDateFmt = '';
if (!empty($dueDate)) {
    $dueDateFmt = date('Y-m-d', strtotime($dueDate));
}

// redirect to create-newInvoice.php with prefill params
$q = http_build_query([
    'client_id' => $clientId,
    'milestone_1_label' => ($remainingBaseAmount < $baseAmount - 0.01) ? ($row['milestone_label'] ?? '') . ' (Remaining)' : ($row['milestone_label'] ?? ''),
    'milestone_1_amount' => number_format($remainingBaseAmount, 2, '.', ''), // Prefill with remaining BASE amount
    'milestone_1_due_date' => $dueDateFmt,
    'milestone_pct' => $milestonePctToPrefill,
    'project_total' => $projectTotal,
    'milestone_id' => $milestoneId,
]);

header("Location: create-newInvoice.php?$q");
exit();

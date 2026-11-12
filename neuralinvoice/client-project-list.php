<?php
include 'EvaraFroentend/layout/head.php';
include 'includes/auth.php';
include 'includes/helpers.php';
include 'EvaraFroentend/layout/header.php';

$clientFilter = isset($_GET['client_id']) ? (int) $_GET['client_id'] : 0;
$where = $clientFilter > 0 ? "WHERE p.client_id = $clientFilter" : '';

$sql = "SELECT p.*, c.ClientName,
        COALESCE(wtm.WorkTypeName, p.work_type) AS work_type_display,
        (SELECT SUM(amount) FROM project_payment_milestones WHERE project_id = p.project_id) AS milestone_total,
        (SELECT SUM(paid_amount) FROM project_payment_milestones WHERE project_id = p.project_id) AS paid_total
        FROM client_projects p
        JOIN clientmaster c ON p.client_id = c.ClientID
        LEFT JOIN work_type_master wtm ON p.work_type_id = wtm.WorkTypeID
        $where
        ORDER BY p.project_id DESC";
$rows = $con->query($sql);
if ($rows === false && strpos($con->error, 'work_type_master') !== false) {
    $sql = "SELECT p.*, c.ClientName, p.work_type AS work_type_display,
        (SELECT SUM(amount) FROM project_payment_milestones WHERE project_id = p.project_id) AS milestone_total,
        (SELECT SUM(paid_amount) FROM project_payment_milestones WHERE project_id = p.project_id) AS paid_total
        FROM client_projects p
        JOIN clientmaster c ON p.client_id = c.ClientID
        $where
        ORDER BY p.project_id DESC";
    $rows = $con->query($sql);
}
$clients = $con->query('SELECT ClientID, ClientName FROM clientmaster WHERE DelStatus = 1 ORDER BY ClientName');
?>
<style>
    .table th, .table td {
        white-space: nowrap;
        vertical-align: middle;
    }
    .tax-column {
        font-size: 0.85rem;
    }
    .badge-tax {
        font-size: 0.7rem;
        padding: 2px 5px;
    }
    .table-responsive {
        overflow-x: auto;
    }
</style>
<div class="container mt-15">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h3>Client Projects / AMC</h3>
            <a href="create-client-project.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Project</a>
        </div>
        <div class="card-body">
            <?php if (!empty($_GET['saved'])): ?><div class="alert alert-success">Project saved.</div><?php endif; ?>
            <form method="get" class="row g-2 mb-3">
                <div class="col-md-4">
                    <select name="client_id" class="form-control">
                        <option value="0">All Clients</option>
                        <?php 
                        $clients->data_seek(0);
                        while ($cl = $clients->fetch_assoc()): ?>
                        <option value="<?php echo (int) $cl['ClientID']; ?>" <?php echo $clientFilter === (int) $cl['ClientID'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cl['ClientName']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2"><button class="btn btn-success"><i class="fas fa-filter"></i> Filter</button></div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle">
                    <thead>
                        <tr>
                            <th rowspan="2">#</th>
                            <th rowspan="2">Type</th>
                            <th rowspan="2">Client</th>
                            <th rowspan="2">Title</th>
                            <th rowspan="2">Work Type</th>
                            <th rowspan="2">FY</th>
                            <th rowspan="2">AMC Days</th>
                            <th rowspan="2">Base Amount</th>
                            <th colspan="8" class="text-center">Tax Details</th>
                            <th rowspan="2">Total With Tax</th>
                            <th rowspan="2">Paid</th>
                            <th rowspan="2">Details</th>
                        </tr>
                        <tr>
                            <th>Tax Type</th>
                            <th>CGST %</th>
                            <th>CGST Amt</th>
                            <th>SGST %</th>
                            <th>SGST Amt</th>
                            <th>IGST %</th>
                            <th>IGST Amt</th>
                            <th>Total Tax</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i = 1;
                    if ($rows && $rows->num_rows > 0):
                        while ($r = $rows->fetch_assoc()):
                            $paidTotal = (float) ($r['paid_total'] ?? 0);
                            $milestoneTotal = (float) ($r['milestone_total'] ?? 0);
                            $pending = $milestoneTotal - $paidTotal;
                            $baseAmount = (float) ($r['total_value'] ?? 0);
                            
                            // Tax Percentages
                            $taxPercent = (float) ($r['tax_percent'] ?? 0);
                            $cgstPercent = (float) ($r['cgst_percent'] ?? 0);
                            $sgstPercent = (float) ($r['sgst_percent'] ?? 0);
                            $igstPercent = (float) ($r['igst_percent'] ?? 0);
                            
                            // Tax Amounts
                            $taxAmount = (float) ($r['tax_amount'] ?? 0);
                            $cgstAmount = (float) ($r['cgst_amount'] ?? 0);
                            $sgstAmount = (float) ($r['sgst_amount'] ?? 0);
                            $igstAmount = (float) ($r['igst_amount'] ?? 0);
                            
                            $totalWithTax = (float) ($r['total_value_with_tax'] ?? 0);
                            if ($totalWithTax <= 0) {
                                $totalWithTax = (float) ($r['total_with_tax'] ?? 0);
                            }
                            if ($totalWithTax <= 0) {
                                $totalWithTax = $baseAmount + $taxAmount;
                            }
                            $taxType = $r['tax_type'] ?? '';
                            
                            // Display tax type badge
                            $taxTypeBadge = '';
                            if ($taxType == 'intra_state') {
                                $taxTypeBadge = '<span class="badge bg-info badge-tax">Intra-State</span>';
                            } elseif ($taxType == 'inter_state') {
                                $taxTypeBadge = '<span class="badge bg-warning badge-tax">Inter-State</span>';
                            } elseif ($taxType == 'international') {
                                $taxTypeBadge = '<span class="badge bg-secondary badge-tax">International</span>';
                            } else {
                                $taxTypeBadge = '<span class="badge bg-light text-dark badge-tax">' . htmlspecialchars($taxType) . '</span>';
                            }

                            $wd = isset($r['amc_working_days']) ? (int) $r['amc_working_days'] : 0;
                            if ($wd <= 0 && !empty($r['amc_start']) && !empty($r['amc_end'])) {
                                $wd = ni_count_working_days($r['amc_start'], $r['amc_end']);
                            }
                            $fy = $r['financial_year'] ?? '';
                            if (!$fy && !empty($r['amc_start'])) {
                                $fy = date('Y', strtotime($r['amc_start'])) . '-' . substr((string) ((int) date('Y', strtotime($r['amc_start'])) + 1), -2);
                            }
                            $eng = $r['engagement_type'] ?? 'project';
                            $typeBadge = $eng === 'amc_only'
                                ? '<span class="badge bg-info">AMC Only</span>'
                                : '<span class="badge bg-secondary">Project</span>';
                    ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo $typeBadge; ?></td>
                            <td><?php echo htmlspecialchars($r['ClientName']); ?></td>
                            <td><?php echo htmlspecialchars($r['project_name']); ?></td>
                            <td><?php echo htmlspecialchars($r['work_type_display'] ?? $r['work_type'] ?? '-'); ?></td>
                            <td><?php echo $fy ? 'FY ' . htmlspecialchars($fy) : '-'; ?></td>
                            <td><?php echo $wd ? (int) $wd : '-'; ?></td>
                            <td class="text-end">
                                <strong>₹<?php echo number_format($baseAmount, 2); ?></strong>
                                <?php if ($pending > 0): ?>
                                    <br><small class="text-danger">Due: ₹<?php echo number_format($pending, 2); ?></small>
                                <?php endif; ?>
                            </td>
                            <!-- Tax Details Columns -->
                            <td class="text-center tax-column"><?php echo $taxTypeBadge; ?></td>
                            <td class="text-end tax-column">
                                <?php echo $cgstPercent > 0 ? number_format($cgstPercent, 2) . '%' : '-'; ?>
                            </td>
                            <td class="text-end tax-column">
                                <?php echo $cgstAmount > 0 ? '₹' . number_format($cgstAmount, 2) : '-'; ?>
                            </td>
                            <td class="text-end tax-column">
                                <?php echo $sgstPercent > 0 ? number_format($sgstPercent, 2) . '%' : '-'; ?>
                            </td>
                            <td class="text-end tax-column">
                                <?php echo $sgstAmount > 0 ? '₹' . number_format($sgstAmount, 2) : '-'; ?>
                            </td>
                            <td class="text-end tax-column">
                                <?php echo $igstPercent > 0 ? number_format($igstPercent, 2) . '%' : '-'; ?>
                            </td>
                            <td class="text-end tax-column">
                                <?php echo $igstAmount > 0 ? '₹' . number_format($igstAmount, 2) : '-'; ?>
                            </td>
                            <td class="text-end tax-column">
                                <strong>₹<?php echo number_format($taxAmount, 2); ?></strong>
                                <small class="text-muted d-block">(<?php echo number_format($taxPercent, 2); ?>%)</small>
                            </td>
                            <td class="text-end">
                                <strong>₹<?php echo number_format($totalWithTax, 2); ?></strong>
                            </td>
                            <td class="text-end">
                                ₹<?php echo number_format($paidTotal, 2); ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="view-client-project.php?id=<?php echo (int) $r['project_id']; ?>" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="client-project-edit.php?id=<?php echo (int) $r['project_id']; ?>" class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="fas fa-edit"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="19" class="text-center">No records yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include 'EvaraFroentend/layout/footer.php'; ?>
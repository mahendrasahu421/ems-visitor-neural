<?php
// Start output buffering at the VERY beginning to prevent header issues
ob_start();

include 'EvaraFroentend/layout/head.php';
include 'includes/auth.php';
include 'includes/helpers.php';

$err = '';
$ok = '';

// Fetch clients with Country and State
$clientsList = [];
$cRes = $con->query('SELECT ClientID, ClientName, Country FROM clientmaster WHERE DelStatus = 1 ORDER BY ClientName');
if ($cRes) {
    while ($row = $cRes->fetch_assoc()) {
        $clientsList[] = $row;
    }
}

$workTypes = ni_get_work_types($con);
$fyOptions = ni_fy_options(3, 2);
$defaultFy = ni_current_fy_start_year();
$defaultPeriod = ni_fy_to_period($defaultFy);
$defaultWorkingDays = ni_count_working_days($defaultPeriod['start'], $defaultPeriod['end']);

/** @var array<string, mixed> */
$old = [
    'engagement_type' => 'project',
    'client_id' => '',
    'project_name' => '',
    'work_type_id' => '',
    'total_value' => '',
    'total_value_with_tax' => '',
    'amc_split' => 'single',
    'amc_fee_due_date' => '',
    'payment_mode' => 'percentage',
    'fy_start_year' => $defaultFy,
    'amc_start' => '',
    'amc_end' => '',
    'amc_working_days' => 0,
    'notes' => '',
    'project_start_date' => '',
    'project_end_date' => '',
    'project_duration_days' => '',
    'total_with_tax' => '',
    'final_amount' => '',
    'has_amc' => 'no', // New field to track if AMC is included
];

/** @var array<int, array<string, string>> */
$milestoneRows = [
    ['milestone_label' => '', 'module_name' => '', 'percentage' => '', 'milestone_amount' => '', 'due_date' => ''],
];

// Function to calculate tax details based on client location
function calculateTaxDetails($con, $clientId, $totalValue) {
    $clientQuery = $con->query("
        SELECT cm.Country,
               cm.StateID,
               COALESCE(c.CountryName, cm.Country) AS CountryName,
               COALESCE(s.StateName, cm.StateID) AS StateName
        FROM clientmaster cm
        LEFT JOIN countrymaster c ON cm.Country = c.CountryID
        LEFT JOIN statemaster s ON cm.StateID = s.StateID
        WHERE cm.ClientID = $clientId
        LIMIT 1
    ");
    $clientData = $clientQuery ? $clientQuery->fetch_assoc() : [];

    $countryName = strtolower(trim((string) ($clientData['CountryName'] ?? $clientData['Country'] ?? '')));
    $stateName = strtolower(trim((string) ($clientData['StateName'] ?? $clientData['StateID'] ?? '')));
    
    $isInternational = $countryName && $countryName !== 'india';
    $taxPercent = 0;
    $cgstPercent = 0;
    $sgstPercent = 0;
    $igstPercent = 0;
    $taxType = '';
    
    if ($isInternational) {
        $taxPercent = 0;
        $taxType = 'international';
    } else if ($stateName === 'uttar pradesh') {
        $taxPercent = 18;
        $cgstPercent = 9;
        $sgstPercent = 9;
        $taxType = 'intra_state';
    } else {
        $taxPercent = 18;
        $igstPercent = 18;
        $taxType = 'inter_state';
    }
    
    $taxAmount = $totalValue * $taxPercent / 100;
    $cgstAmount = $totalValue * $cgstPercent / 100;
    $sgstAmount = $totalValue * $sgstPercent / 100;
    $igstAmount = $totalValue * $igstPercent / 100;
    
    return [
        'tax_percent' => $taxPercent,
        'tax_amount' => $taxAmount,
        'cgst_percent' => $cgstPercent,
        'cgst_amount' => $cgstAmount,
        'sgst_percent' => $sgstPercent,
        'sgst_amount' => $sgstAmount,
        'igst_percent' => $igstPercent,
        'igst_amount' => $igstAmount,
        'tax_type' => $taxType,
        'total_with_tax' => $totalValue + $taxAmount
    ];
}

function ni_cp_column_exists(mysqli $con, string $table, string $column): bool
{
    $tableEsc = $con->real_escape_string($table);
    $columnEsc = $con->real_escape_string($column);
    $r = $con->query("SHOW COLUMNS FROM `$tableEsc` LIKE '$columnEsc'");
    return $r && $r->num_rows > 0;
}

function ni_cp_add_column_if_missing(mysqli $con, string $table, string $column, string $definition): void
{
    if (!ni_cp_column_exists($con, $table, $column)) {
        $con->query("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
    }
}

function ni_cp_sql_date(mysqli $con, string $date): string
{
    $date = trim($date);
    return $date === '' ? 'NULL' : "'" . $con->real_escape_string($date) . "'";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 
    $old['client_id'] = (int) ($_POST['client_id'] ?? 0);
    $old['project_name'] = trim($_POST['project_name'] ?? '');
    $old['amc_split'] = in_array($_POST['amc_split'] ?? '', ['single', 'half_yearly', 'quarterly', 'custom'], true)
        ? $_POST['amc_split'] : 'single';
    $old['amc_fee_due_date'] = trim($_POST['amc_fee_due_date'] ?? '');
    $old['work_type_id'] = (int) ($_POST['work_type_id'] ?? 0);
    $old['total_value'] = trim($_POST['total_value'] ?? '0');
    $old['total_value_with_tax'] = trim($_POST['total_value_with_tax'] ?? '0');
    $old['payment_mode'] = ($_POST['payment_mode'] ?? '') === 'module' ? 'module' : 'percentage';
    $old['fy_start_year'] = (int) ($_POST['fy_start_year'] ?? 0);
    $old['amc_start'] = trim($_POST['amc_start'] ?? '');
    $old['amc_end'] = trim($_POST['amc_end'] ?? '');
    $old['amc_working_days'] = (int) ($_POST['amc_working_days'] ?? 0);
    $old['notes'] = trim($_POST['notes'] ?? '');
    $old['project_start_date'] = trim($_POST['project_start_date'] ?? '');
    $old['project_end_date'] = trim($_POST['project_end_date'] ?? '');
    $old['project_duration_days'] = (int) ($_POST['project_duration_days'] ?? 0);
    $old['total_with_tax'] = trim($_POST['total_with_tax'] ?? '0');
    $old['final_amount'] = trim($_POST['final_amount'] ?? '0');
    $old['has_amc'] = $_POST['has_amc'] ?? 'no';

    $labels = $_POST['milestone_label'] ?? [];
    $modules = $_POST['module_name'] ?? [];
    $pcts = $_POST['percentage'] ?? [];
    $amounts = $_POST['milestone_amount'] ?? [];
    $dueDates = $_POST['due_date'] ?? [];
    $milestoneRows = [];
    foreach ($labels as $i => $label) {
        $milestoneRows[] = [
            'milestone_label' => trim($label),
            'module_name' => trim($modules[$i] ?? ''),
            'percentage' => trim($pcts[$i] ?? ''),
            'milestone_amount' => trim($amounts[$i] ?? ''),
            'due_date' => trim($dueDates[$i] ?? ''),
        ];
    }
    if (empty($milestoneRows)) {
        $milestoneRows[] = ['milestone_label' => '', 'module_name' => '', 'percentage' => '', 'milestone_amount' => '', 'due_date' => ''];
    }

    $clientId = (int) $old['client_id'];
    $engagementType = ni_engagement_from_work_type_id($con, (int) $old['work_type_id']);
    $old['engagement_type'] = $engagementType;
    $projectName = $old['project_name'];
    $paymentMode = $engagementType === 'amc_only' ? 'percentage' : $old['payment_mode'];
    $totalValue = (float) $old['total_value'];
    $totalValueWithTax = (float) $old['total_value_with_tax'];
    $totalWithTax = (float) $old['total_with_tax'];
    $finalAmount = (float) $old['final_amount'];
    $fyStartYear = (int) $old['fy_start_year'];
    $amcStart = $old['amc_start'];
    $amcEnd = $old['amc_end'];
    $period = $fyStartYear > 0 ? ni_fy_to_period($fyStartYear) : null;
    $hasAmc = $old['has_amc'] === 'yes';

    if ($clientId <= 0) {
        $err = 'Please select a client.';
    } elseif ($projectName === '') {
        $err = $engagementType === 'amc_only' ? 'AMC title / description is required.' : 'Project name is required.';
    } elseif ($engagementType === 'amc_only' && $totalValue <= 0) {
        $err = 'Enter AMC annual fee amount.';
    } elseif ($old['work_type_id'] <= 0) {
        $err = 'Please select work type.';
    } elseif (!$period) {
        $err = 'Please select financial year.';
    } elseif ($hasAmc && ($amcStart === '' || $amcEnd === '')) {
        $err = 'Please select AMC start and end dates or uncheck "Include AMC".';
    } elseif ($hasAmc && !ni_validate_amc_dates_in_fy($amcStart, $amcEnd, $fyStartYear)) {
        $err = 'AMC dates must be within FY ' . $period['label'] . ' (' . $period['start'] . ' to ' . $period['end'] . ').';
    }

    $workTypeId = (int) $old['work_type_id'];
    $workTypeName = '';
    if (empty($err) && $workTypeId > 0) {
        $wtR = $con->query("SELECT WorkTypeName FROM work_type_master WHERE WorkTypeID = $workTypeId AND DelStatus = 1 LIMIT 1");
        if ($wtR && $wtR->num_rows) {
            $workTypeName = $wtR->fetch_assoc()['WorkTypeName'];
        } else {
            $err = 'Invalid work type selected.';
        }
    }

    // Validate milestone amounts don't exceed total value
    if (empty($err) && $engagementType === 'project') {
        $totalMilestoneAmount = 0;
        foreach ($milestoneRows as $row) {
            $totalMilestoneAmount += (float) $row['milestone_amount'];
        }
        if ($totalMilestoneAmount > $totalValue) {
            $err = 'Total milestone amount (₹' . number_format($totalMilestoneAmount, 2) . ') cannot exceed Total Project Value (₹' . number_format($totalValue, 2) . ').';
        }
    }

    if ($engagementType === 'amc_only' && $old['amc_split'] !== 'custom') {
        $validMilestones = ni_amc_only_milestones($totalValue, $old['amc_split'], $old['amc_fee_due_date']);
    } else {
        $validMilestones = [];
        foreach ($milestoneRows as $row) {
            if ($row['milestone_label'] !== '') {
                $validMilestones[] = $row;
            }
        }
    }
    if (empty($err) && $engagementType === 'project' && empty($validMilestones)) {
        $err = 'Add at least one payment milestone.';
    }
    if (empty($err) && $engagementType === 'amc_only' && $old['amc_split'] === 'custom' && empty($validMilestones)) {
        $err = 'Add at least one AMC payment row, or choose Single / Half-yearly / Quarterly split.';
    }

    $pctTotal = 0;
    if (empty($err) && $engagementType === 'project' && $paymentMode === 'percentage') {
        foreach ($validMilestones as $row) {
            $pctTotal += (float) $row['percentage'];
        }
        if (abs($pctTotal - 100) > 0.01 && $pctTotal > 0) {
            $err = 'Percentage milestones must total 100% (current: ' . round($pctTotal, 2) . '%).';
        }
    }

    $proposalPath = null;
    if (empty($err) && !empty($_FILES['proposal_file']['name'])) {
        $proposalPath = ni_upload_file($_FILES['proposal_file'], 'proposals');
        if ($proposalPath === false) {
            $err = 'Invalid proposal file. Allowed: PDF, DOC, DOCX.';
        }
    }

    if (empty($err)) {
        // Calculate tax details
        $taxDetails = calculateTaxDetails($con, $clientId, $totalValue);
        $totalWithTax = $taxDetails['total_with_tax'];

        $amcWorkingDays = (int) $old['amc_working_days'];
        if ($hasAmc && $amcWorkingDays <= 0 && $amcStart && $amcEnd) {
            $amcWorkingDays = ni_count_working_days($amcStart, $amcEnd);
        }
        $financialYear = $period['label'];

        ni_cp_add_column_if_missing($con, 'client_projects', 'engagement_type', "ENUM('project','amc_only') NOT NULL DEFAULT 'project' AFTER `project_name`");
        ni_cp_add_column_if_missing($con, 'client_projects', 'tax_percent', "DECIMAL(5,2) DEFAULT 0 AFTER `total_value`");
        ni_cp_add_column_if_missing($con, 'client_projects', 'tax_amount', "DECIMAL(12,2) DEFAULT 0 AFTER `tax_percent`");
        ni_cp_add_column_if_missing($con, 'client_projects', 'cgst_percent', "DECIMAL(5,2) DEFAULT 0 AFTER `tax_amount`");
        ni_cp_add_column_if_missing($con, 'client_projects', 'cgst_amount', "DECIMAL(12,2) DEFAULT 0 AFTER `cgst_percent`");
        ni_cp_add_column_if_missing($con, 'client_projects', 'sgst_percent', "DECIMAL(5,2) DEFAULT 0 AFTER `cgst_amount`");
        ni_cp_add_column_if_missing($con, 'client_projects', 'sgst_amount', "DECIMAL(12,2) DEFAULT 0 AFTER `sgst_percent`");
        ni_cp_add_column_if_missing($con, 'client_projects', 'igst_percent', "DECIMAL(5,2) DEFAULT 0 AFTER `sgst_amount`");
        ni_cp_add_column_if_missing($con, 'client_projects', 'igst_amount', "DECIMAL(12,2) DEFAULT 0 AFTER `igst_percent`");
        ni_cp_add_column_if_missing($con, 'client_projects', 'tax_type', "VARCHAR(50) DEFAULT NULL AFTER `igst_amount`");
        ni_cp_add_column_if_missing($con, 'client_projects', 'total_value_with_tax', "DECIMAL(12,2) DEFAULT 0 AFTER `tax_type`");
        ni_cp_add_column_if_missing($con, 'client_projects', 'total_with_tax', "DECIMAL(12,2) DEFAULT 0 AFTER `total_value_with_tax`");
        ni_cp_add_column_if_missing($con, 'client_projects', 'final_amount', "DECIMAL(12,2) DEFAULT 0 AFTER `total_with_tax`");
        ni_cp_add_column_if_missing($con, 'client_projects', 'project_start_date', "DATE DEFAULT NULL AFTER `amc_end`");
        ni_cp_add_column_if_missing($con, 'client_projects', 'project_end_date', "DATE DEFAULT NULL AFTER `project_start_date`");
        ni_cp_add_column_if_missing($con, 'client_projects', 'project_duration_days', "INT DEFAULT 0 AFTER `amc_working_days`");
        ni_cp_add_column_if_missing($con, 'client_projects', 'work_type_id', "INT NULL AFTER `work_type`");
        ni_cp_add_column_if_missing($con, 'client_projects', 'has_amc', "ENUM('yes','no') DEFAULT 'no' AFTER `engagement_type`");

        ni_cp_add_column_if_missing($con, 'project_payment_milestones', 'tax_amount', "DECIMAL(12,2) DEFAULT 0 AFTER `amount`");
        ni_cp_add_column_if_missing($con, 'project_payment_milestones', 'amount_with_tax', "DECIMAL(12,2) DEFAULT 0 AFTER `tax_amount`");

        $hasWtId = ni_cp_column_exists($con, 'client_projects', 'work_type_id');

        $propSql = $proposalPath ? "'" . $con->real_escape_string($proposalPath) . "'" : 'NULL';
        $notesEsc = $con->real_escape_string($old['notes']);
        $pNameEsc = $con->real_escape_string($projectName);
        $engEsc = $con->real_escape_string($engagementType);
        $pModeEsc = $con->real_escape_string($paymentMode);
        $wtEsc = $con->real_escape_string($workTypeName);
        $fyEsc = $con->real_escape_string($financialYear);
        $hasAmcEsc = $con->real_escape_string($old['has_amc']);
        
        // Set AMC dates to NULL if AMC is not included
        $amcStartSql = ($hasAmc && $amcStart) ? ni_cp_sql_date($con, $amcStart) : 'NULL';
        $amcEndSql = ($hasAmc && $amcEnd) ? ni_cp_sql_date($con, $amcEnd) : 'NULL';
        $amcWorkingDaysFinal = $hasAmc ? $amcWorkingDays : 0;
        
        $projectStartSql = ni_cp_sql_date($con, $old['project_start_date']);
        $projectEndSql = ni_cp_sql_date($con, $old['project_end_date']);
        $projectDurationDays = (int) $old['project_duration_days'];

        $engCol = 'engagement_type, has_amc, ';
        $engVal = "'$engEsc', '$hasAmcEsc', ";

        // Use total_value_with_tax from form if provided, otherwise calculate
        $finalTotalWithTax = ($totalValueWithTax > 0) ? $totalValueWithTax : $totalWithTax;

        // Build INSERT query with all tax fields
        if ($hasWtId) {
            $sql = "INSERT INTO client_projects (
                client_id, project_name, {$engCol}payment_mode, 
                total_value, 
                tax_percent, tax_amount, 
                cgst_percent, cgst_amount, 
                sgst_percent, sgst_amount,
                igst_percent, igst_amount, 
                tax_type,
                total_value_with_tax, total_with_tax, final_amount,
                project_start_date, project_end_date, project_duration_days, 
                amc_start, amc_end, financial_year, amc_working_days, 
                work_type, work_type_id, proposal_file, notes
            ) VALUES (
                $clientId, '$pNameEsc', {$engVal}'$pModeEsc', 
                $totalValue, 
                {$taxDetails['tax_percent']}, {$taxDetails['tax_amount']},
                {$taxDetails['cgst_percent']}, {$taxDetails['cgst_amount']}, 
                {$taxDetails['sgst_percent']}, {$taxDetails['sgst_amount']},
                {$taxDetails['igst_percent']}, {$taxDetails['igst_amount']}, 
                '{$taxDetails['tax_type']}',
                $finalTotalWithTax, $finalTotalWithTax, $finalAmount,
                $projectStartSql, $projectEndSql, $projectDurationDays, 
                $amcStartSql, $amcEndSql, '$fyEsc', $amcWorkingDaysFinal, 
                '$wtEsc', $workTypeId, $propSql, '$notesEsc'
            )";
        } else {
            $sql = "INSERT INTO client_projects (
                client_id, project_name, {$engCol}payment_mode, 
                total_value,
                tax_percent, tax_amount,
                cgst_percent, cgst_amount, 
                sgst_percent, sgst_amount,
                igst_percent, igst_amount, 
                tax_type,
                total_value_with_tax, total_with_tax, final_amount,
                project_start_date, project_end_date, project_duration_days, 
                amc_start, amc_end, financial_year, amc_working_days, 
                work_type, proposal_file, notes
            ) VALUES (
                $clientId, '$pNameEsc', {$engVal}'$pModeEsc', 
                $totalValue,
                {$taxDetails['tax_percent']}, {$taxDetails['tax_amount']},
                {$taxDetails['cgst_percent']}, {$taxDetails['cgst_amount']}, 
                {$taxDetails['sgst_percent']}, {$taxDetails['sgst_amount']},
                {$taxDetails['igst_percent']}, {$taxDetails['igst_amount']}, 
                '{$taxDetails['tax_type']}',
                $finalTotalWithTax, $finalTotalWithTax, $finalAmount,
                $projectStartSql, $projectEndSql, $projectDurationDays, 
                $amcStartSql, $amcEndSql, '$fyEsc', $amcWorkingDaysFinal, 
                '$wtEsc', $propSql, '$notesEsc'
            )";
        }

        // DEBUG-FIRST: transaction + show SQL error
        $con->begin_transaction();
        try {
            if (!$con->query($sql)) {
                throw new Exception('Could not save project: ' . $con->error . ' | SQL=' . $sql);
            }

            $projectId = (int) $con->insert_id;
            if ($projectId <= 0) {
                throw new Exception('Insert succeeded but project_id is invalid: ' . $con->insert_id);
            }

            $sort = 0;
            foreach ($validMilestones as $row) {
                $label = $con->real_escape_string($row['milestone_label']);
                $mod = $con->real_escape_string($row['module_name']);
                $modSql = $mod !== '' ? "'$mod'" : 'NULL';

                $pct = $paymentMode === 'percentage' ? (float) $row['percentage'] : null;
                $amt = (float) $row['milestone_amount'];

                // Calculate tax for milestone
                $milestoneTax = $amt * $taxDetails['tax_percent'] / 100;
                $milestoneWithTax = $amt + $milestoneTax;

                if ($paymentMode === 'percentage' && $totalValue > 0 && $pct > 0 && $amt <= 0) {
                    $amt = round($totalValue * $pct / 100, 2);
                    $milestoneTax = $amt * $taxDetails['tax_percent'] / 100;
                    $milestoneWithTax = $amt + $milestoneTax;
                }

                $pctSql = $pct !== null ? $pct : 'NULL';
                $due = $con->real_escape_string($row['due_date']);
                $dueSql = $due !== '' ? "'$due'" : 'NULL';

                $msSql = "INSERT INTO project_payment_milestones
                    (project_id, milestone_label, module_name, percentage, amount, tax_amount, amount_with_tax, due_date, sort_order)
                    VALUES ($projectId, '$label', $modSql, $pctSql, $amt, $milestoneTax, $milestoneWithTax, $dueSql, $sort)";

                if (!$con->query($msSql)) {
                    throw new Exception('Could not save milestone: ' . $con->error . ' | SQL=' . $msSql);
                }

                $sort++;
            }

            $con->commit();

            // Clear output buffer and redirect
            ob_end_clean();
            header('Location: client-project-list.php?saved=1');
            exit;
        } catch (Throwable $e) {
            $con->rollback();
            $err = 'DEBUG: Save failed. ' . $e->getMessage();
        }
    }
}

$selFy = (int) $old['fy_start_year'];
$fyPeriod = $selFy > 0 ? ni_fy_to_period($selFy) : $defaultPeriod;

if ((int) $old['work_type_id'] > 0) {
    $old['engagement_type'] = ni_engagement_from_work_type_id($con, (int) $old['work_type_id']);
}

// Now include header after all processing is done
include 'EvaraFroentend/layout/header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
<style>
    .ni-page label { font-weight: 600; margin-bottom: 4px; }
    .ni-section-title { font-size: 1rem; font-weight: 700; color: #333; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 2px solid #3bb77e; }
    .ni-milestone-head { font-size: 0.8rem; font-weight: 600; color: #666; }
    .milestone-row { background: #f8f9fa; border-radius: 8px; padding: 10px; margin-bottom: 8px; }
    #totalPercentage.over { color: #dc3545; }
    #totalPercentage.ok { color: #198754; }
    .tax-input-box { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 10px; margin-top: 10px; }
    .tax-input-box label { font-size: 12px; color: #6c757d; }
    .tax-input-box .form-control { background-color: #fff; font-weight: bold; }
    .duration-help { font-size: 11px; color: #6c757d; }
    .value-with-tax-field { background-color: #e8f5e9; font-weight: bold; }
    .milestone-exceed { border-color: #dc3545 !important; background-color: #fff3f3 !important; }
    .amc-optional-section { background: #f0f7ff; border: 1px solid #cce5ff; border-radius: 8px; padding: 15px; margin-bottom: 20px; }
    .amc-fields { transition: all 0.3s ease; }
</style>

<div class="container mt-15 ni-page">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
            <div>
                <h3 class="mb-0">Client Project / AMC</h3>
                <small class="text-muted">Create project with FY-based AMC and payment milestones</small>
            </div>
            <div>
                <a href="client-project-list.php" class="btn btn-outline-secondary btn-sm">Project List</a>
                <a href="work-type-master.php" class="btn btn-outline-primary btn-sm">Work Types</a>
            </div>
        </div>

        <div class="card-body">
            <?php if ($err): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div>
            <?php endif; ?>

            <?php if (empty($workTypes)): ?>
                <div class="alert alert-warning mb-3">
                    No work types in database.
                    <a href="work-type-master.php">Add work types</a> first.
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" id="projectForm" novalidate>
                <!-- Section 1: Basic -->
                <p class="ni-section-title"><span id="section1Title">1. Project Details</span></p>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label>Client <span class="text-danger">*</span></label>
                        <select name="client_id" id="client_id" class="form-control" required>
                            <option value="">-- Select Client --</option>
                            <?php foreach ($clientsList as $c): ?>
                            <option value="<?php echo (int) $c['ClientID']; ?>"
                                <?php echo (int) $old['client_id'] === (int) $c['ClientID'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['ClientName']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Country</label>
                        <input type="text" id="client_country" class="form-control" value="" readonly>
                    </div>
                    <div class="col-md-4">
                        <label>State</label>
                        <input type="text" id="client_state" class="form-control" value="" readonly>
                    </div>
                    <div class="col-md-4">
                        <label id="projectNameLabel">Project Name <span class="text-danger">*</span></label>
                        <input type="text" name="project_name" id="project_name" 
                            class="form-control uppercase-input" required
                            value="<?php echo htmlspecialchars($old['project_name'] ?? ''); ?>"
                            placeholder="e.g. WEBSITE DEVELOPMENT"
                            oninput="this.value = this.value.toUpperCase();">
                    </div>
                    <div class="col-md-4">
                        <label>Work Type <span class="text-danger">*</span></label>
                        <select name="work_type_id" id="work_type_id" class="form-control" required
                            <?php echo empty($workTypes) ? 'disabled' : ''; ?>>
                            <option value="" data-amc-type="project">-- Select Work Type --</option>
                            <?php foreach ($workTypes as $wt):
                                $amcType = ($wt['amc_type'] ?? 'project') === 'amc_only' ? 'amc_only' : 'project';
                            ?>
                            <option value="<?php echo (int) $wt['WorkTypeID']; ?>"
                                data-amc-type="<?php echo $amcType; ?>"
                                <?php echo (int) $old['work_type_id'] === (int) $wt['WorkTypeID'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($wt['WorkTypeName']); ?>
                                <?php echo $amcType === 'amc_only' ? ' (AMC Only)' : ''; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted" id="workTypeHint">Work Type Master mein AMC Type set hoti hai.</small>
                    </div>
                    <div class="col-md-4" id="totalValueCol">
                        <label id="totalValueLabel">Total Project Value (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="total_value" id="total_value"
                            class="form-control" required value="<?php echo htmlspecialchars($old['total_value']); ?>">
                        <small class="text-muted" id="totalValueHint">Enter base amount without tax</small>
                    </div>
                </div>

                <!-- Tax Input Fields Section - This data will be saved to database -->
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <div class="tax-input-box">
                            <h6 class="mb-3">💰 Tax Details (Auto-calculated based on Client Location)</h6>
                            <div class="row">
                                <div class="col-md-3" id="cgstBox" style="display: none;">
                                    <label>CGST</label>
                                    <input type="text" id="cgst_display" class="form-control" readonly>
                                </div>
                                <div class="col-md-3" id="sgstBox" style="display: none;">
                                    <label>SGST</label>
                                    <input type="text" id="sgst_display" class="form-control" readonly>
                                </div>
                                <div class="col-md-3" id="igstBox" style="display: none;">
                                    <label>IGST</label>
                                    <input type="text" id="igst_display" class="form-control" readonly>
                                </div>
                                <div class="col-md-3" id="totalTaxBox">
                                    <label>Total Tax</label>
                                    <input type="text" id="total_tax_display" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Project Value with Tax Field -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label>Project Value with Tax (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="total_value_with_tax" id="total_value_with_tax" readonly
                            class="form-control value-with-tax-field" required 
                            value="<?php echo htmlspecialchars($old['total_value_with_tax']); ?>"
                            style="background-color: #e8f5e9; font-weight: bold;">
                        <small class="text-muted">Base Amount + Tax (will be saved to database)</small>
                    </div>
                </div>

                <!-- Project Duration Fields -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label>Project Duration (Working Days) <span class="text-danger">*</span></label>
                        <input type="number" name="project_duration_days" id="project_duration_days" class="form-control" 
                            min="1" max="365" placeholder="Enter number of working days" required
                            value="<?php echo htmlspecialchars($old['project_duration_days']); ?>">
                        <small class="duration-help">Monday to Friday only (Weekends excluded)</small>
                    </div>
                    <div class="col-md-4">
                        <label>Project Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="project_start_date" id="project_start_date" class="form-control" required
                            value="<?php echo htmlspecialchars($old['project_start_date']); ?>">
                        <small class="text-muted">Any date can be selected (past or future)</small>
                    </div>
                    <div class="col-md-4">
                        <label>Project End Date <span class="text-danger">*</span></label>
                        <input type="date" name="project_end_date" id="project_end_date" class="form-control" required
                             style="background-color: #e9ecef;"
                            value="<?php echo htmlspecialchars($old['project_end_date']); ?>">
                        <small class="text-muted">Auto-calculated based on working days</small>
                    </div>
                </div>

                <!-- Section 2: AMC (Optional) -->
                <p class="ni-section-title">2. AMC (Optional) <span class="text-muted">- Annual Maintenance Contract</span></p>
                <div class="amc-optional-section">
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="has_amc" name="has_amc" value="yes" 
                                    <?php echo $old['has_amc'] === 'yes' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="has_amc">
                                    <strong>Include AMC for this project</strong>
                                </label>
                                <small class="text-muted d-block">Check this if you want to add Annual Maintenance Contract details</small>
                            </div>
                        </div>
                    </div>
                    
                    <div id="amcFields" class="amc-fields" style="<?php echo $old['has_amc'] === 'yes' ? '' : 'display: none;'; ?>">
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label>Financial Year <span class="text-danger">*</span></label>
                                <select name="fy_start_year" id="fy_start_year" class="form-control">
                                    <?php foreach ($fyOptions as $fy): ?>
                                    <option value="<?php echo (int) $fy['start_year']; ?>"
                                        data-start="<?php echo $fy['start']; ?>"
                                        data-end="<?php echo $fy['end']; ?>"
                                        <?php echo (int) $old['fy_start_year'] === (int) $fy['start_year'] ? 'selected' : ''; ?>>
                                        FY <?php echo htmlspecialchars($fy['label']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Allowed: <span id="fy_range_hint"><?php echo $fyPeriod['start']; ?> – <?php echo $fyPeriod['end']; ?></span></small>
                            </div>
                            <div class="col-md-3">
                                <label>AMC Start Date</label>
                                <input type="date" name="amc_start" id="amc_start" class="form-control"
                                    min="<?php echo $fyPeriod['start']; ?>"
                                    max="<?php echo $fyPeriod['end']; ?>"
                                    value="<?php echo htmlspecialchars($old['amc_start']); ?>">
                            </div>
                            <div class="col-md-3">
                                <label>AMC End Date</label>
                                <input type="date" name="amc_end" id="amc_end" class="form-control"
                                    min="<?php echo $fyPeriod['start']; ?>"
                                    max="<?php echo $fyPeriod['end']; ?>"
                                    value="<?php echo htmlspecialchars($old['amc_end']); ?>">
                            </div>
                            <div class="col-md-3">
                                <label>Working Days (Mon–Fri)</label>
                                <input type="number" name="amc_working_days" id="amc_working_days" class="form-control" readonly
                                    value="<?php echo (int) $old['amc_working_days']; ?>">
                                <small class="text-muted" id="days_hint">5-day working week</small>
                            </div>
                        </div>
                        
                        <div id="amcOnlyPaymentBox" class="row g-3 mb-3" style="<?php echo $old['engagement_type'] === 'amc_only' ? '' : 'display:none;'; ?>">
                            <div class="col-md-4">
                                <label>AMC Payment Split</label>
                                <select name="amc_split" id="amc_split" class="form-control">
                                    <option value="single" <?php echo $old['amc_split'] === 'single' ? 'selected' : ''; ?>>Single (100% ek baar)</option>
                                    <option value="half_yearly" <?php echo $old['amc_split'] === 'half_yearly' ? 'selected' : ''; ?>>Half-yearly (50% + 50%)</option>
                                    <option value="quarterly" <?php echo $old['amc_split'] === 'quarterly' ? 'selected' : ''; ?>>Quarterly (4 × 25%)</option>
                                    <option value="custom" <?php echo $old['amc_split'] === 'custom' ? 'selected' : ''; ?>>Custom rows</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>First Payment Due Date</label>
                                <input type="date" name="amc_fee_due_date" id="amc_fee_due_date" class="form-control"
                                    value="<?php echo htmlspecialchars($old['amc_fee_due_date']); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden fields to store values for database -->
                <input type="hidden" name="total_with_tax" id="total_with_tax_hidden" value="0">
                <input type="hidden" name="final_amount" id="final_amount_hidden" value="0">

                <div id="amcOnlyMilestoneNote" class="alert alert-info py-2" style="<?php echo $old['engagement_type'] === 'amc_only' ? '' : 'display:none;'; ?>">
                    AMC Only: payment rows auto-banenge (Single / Half-yearly / Quarterly). Custom chuno to khud rows add karo.
                </div>
                <div id="projectMilestonesToolbar" class="row g-3 mb-3 align-items-end" style="<?php echo $old['engagement_type'] === 'amc_only' ? 'display:none;' : ''; ?>">
                    <div class="col-md-4">
                        <label>Payment Breakup Type</label>
                        <p class="text-muted small mb-1" id="breakupHint">
                            <strong>Percentage:</strong> enter % per row — amount fills from total value.
                            <strong>Module:</strong> enter module name and amount per row.
                        </p>
                        <select name="payment_mode" id="payment_mode" class="form-control">
                            <option value="percentage" <?php echo $old['payment_mode'] === 'percentage' ? 'selected' : ''; ?>>
                                Percentage wise (e.g. 30-40-30)
                            </option>
                            <option value="module" <?php echo $old['payment_mode'] === 'module' ? 'selected' : ''; ?>>
                                Module wise
                            </option>
                        </select>
                    </div>
                </div>
                <div class="row ni-milestone-head d-none d-md-flex mb-2 px-1">
                    <div class="col-md-3">Milestone Label *</div>
                    <div class="col-md-2 module-col">Module</div>
                    <div class="col-md-2 pct-col">Percentage</div>
                    <div class="col-md-2">Amount (₹)</div>
                    <div class="col-md-2">Amount with Tax (₹)</div>
                    <div class="col-md-1">Due Date</div>
                </div>

                <div id="milestones">
                    <?php foreach ($milestoneRows as $idx => $m): ?>
                    <div class="row g-2 milestone-row align-items-center">
                        <div class="col-md-3">
                            <label class="d-md-none small">Milestone</label>
                            <input type="text" name="milestone_label[]" class="form-control"
                                placeholder="e.g. Advance" <?php echo $idx === 0 ? 'required' : ''; ?>
                                value="<?php echo htmlspecialchars($m['milestone_label']); ?>">
                        </div>
                        <div class="col-md-2 module-col">
                            <label class="d-md-none small">Module</label>
                            <input type="text" name="module_name[]" class="form-control"
                                placeholder="Module name"
                                value="<?php echo htmlspecialchars($m['module_name']); ?>">
                        </div>
                        <div class="col-md-2 pct-col">
                            <label class="d-md-none small">%</label>
                            <input type="number" step="0.01" min="0" max="100" name="percentage[]"
                                class="form-control pct-input" placeholder="%"
                                value="<?php echo htmlspecialchars($m['percentage']); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="d-md-none small">Amount</label>
                            <input type="number" step="0.01" min="0" name="milestone_amount[]"
                                class="form-control amt-input milestone-amount" placeholder="Amount"
                                value="<?php echo htmlspecialchars($m['milestone_amount']); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="d-md-none small">With Tax</label>
                            <div class="form-control bg-white" style="font-weight:600; background-color: #f0f0f0 !important;">
                                <span class="tax-after-tax" data-base="<?php echo htmlspecialchars($m['milestone_amount']); ?>">—</span>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <label class="d-md-none small">Due</label>
                            <input type="date" name="due_date[]" class="form-control"
                                value="<?php echo htmlspecialchars($m['due_date']); ?>">
                        </div>
                        <div class="col-md-1 text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-row mt-md-4"
                                <?php echo $idx === 0 ? 'style="display:none"' : ''; ?>>×</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div id="milestoneActions" class="d-flex flex-wrap justify-content-between align-items-center mt-3 mb-4 gap-2"
                    style="<?php echo ($old['engagement_type'] === 'amc_only' && $old['amc_split'] !== 'custom') ? 'display:none;' : ''; ?>">
                    <button type="button" class="btn btn-success btn-sm" id="addMilestone">+ Add Milestone</button>
                    <div class="pct-col" id="pctSummary">
                        <span class="text-muted">Total percentage:</span>
                        <strong id="totalPercentage" class="ok">0%</strong>
                        <span class="text-muted"> / 100%</span>
                    </div>
                </div>
                
                <!-- Milestone validation message -->
                <div id="milestoneValidationMsg" class="alert alert-danger py-2 mt-2" style="display: none;">
                    ⚠️ Total milestone amount cannot exceed Total Project Value!
                </div>

                <p class="ni-section-title">3. Other</p>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label>Upload Proposal (PDF / DOC)</label>
                        <input type="file" name="proposal_file" class="form-control" accept=".pdf,.doc,.docx">
                    </div>
                    <div class="col-md-6">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="2"
                            placeholder="Project scope, deliverables, terms…"><?php echo htmlspecialchars($old['notes']); ?></textarea>
                    </div>
                </div>

                <div class="border-top pt-3">
                    <button type="submit" class="btn btn-primary px-4" id="saveBtn">Save Project</button>
                    <a href="client-project-list.php" class="btn btn-link">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
(function () {
    function isAmcOnly() {
        return $('#work_type_id option:selected').data('amc-type') === 'amc_only';
    }

    function toggleEngagementType() {
        var amc = isAmcOnly();
        var wtText = $('#work_type_id option:selected').text() || '';
        if (!$('#work_type_id').val()) {
            $('#workTypeHint').text('Work Type Master mein AMC Type set hoti hai.');
        } else if (amc) {
            $('#workTypeHint').html('<span class="text-info">AMC Only:</span> sirf maintenance / renewal — payment split neeche dikhega.');
        } else {
            $('#workTypeHint').html('<span class="text-secondary">Project Work:</span> milestones + payment breakup neeche.');
        }
        $('#amcOnlyPaymentBox').toggle(amc);
        $('#amcOnlyMilestoneNote').toggle(amc);
        $('#section1Title').text(amc ? '1. AMC Details' : '1. Project Details');
        $('#projectNameLabel').html(amc ? 'AMC Title <span class="text-danger">*</span>' : 'Project Name <span class="text-danger">*</span>');
        $('#project_name').attr('placeholder', amc ? 'e.g. Website AMC FY 2025-26' : 'e.g. Website Development');
        $('#totalValueLabel').text(amc ? 'AMC Annual Fee (₹)' : 'Total Project Value (₹)');
        $('#totalValueHint').text(amc ? 'Yearly AMC amount for this client' : 'Enter base amount without tax');
        
        var split = $('#amc_split').val();
        var hideMilestones = amc && split !== 'custom';
        $('#projectMilestonesToolbar').toggle(!amc);
        $('#milestones').toggle(!hideMilestones);
        $('.ni-milestone-head').toggle(!hideMilestones);
        $('#milestoneActions').toggle(!hideMilestones);
        $('#milestoneTotalDisplay').toggle(!hideMilestones);
        toggleMode();
    }

    // Toggle AMC fields visibility
    $('#has_amc').on('change', function() {
        if ($(this).is(':checked')) {
            $('#amcFields').slideDown();
        } else {
            $('#amcFields').slideUp();
            // Clear AMC fields when unchecked
            $('#amc_start, #amc_end, #amc_fee_due_date').val('');
            $('#amc_working_days').val('0');
        }
    });

    $('#work_type_id').on('change', toggleEngagementType);
    $('#amc_split').on('change', toggleEngagementType);

    function loadClientCountryState() {
        var clientId = parseInt($('#client_id').val() || '0', 10);
        if (!clientId) {
            $('#client_country').val('');
            $('#client_state').val('');
            recalcTaxDetails();
            return;
        }

        $.ajax({
            url: 'get_client_details.php',
            method: 'POST',
            dataType: 'json',
            data: { client_id: clientId }
        }).done(function (r) {
            if (!r || !r.ok) {
                $('#client_country').val('');
                $('#client_state').val('');
                recalcTaxDetails();
                return;
            }
            $('#client_country').val(r.country_name || '');
            $('#client_state').val(r.state_name || '');
            recalcTaxDetails();
        }).fail(function () {
            $('#client_country').val('');
            $('#client_state').val('');
            recalcTaxDetails();
        });
    }

    $('#client_id').on('change', function () {
        loadClientCountryState();
    });

    function toggleMode() {
        if (isAmcOnly()) {
            return;
        }
        var m = $('#payment_mode').val();
        if (m === 'module') {
            $('.pct-col').hide();
            $('#pctSummary').hide();
            $('.module-col').show();
            $('#breakupHint').html('<strong>Module wise:</strong> add module name and fixed amount for each milestone row.');
        } else {
            $('.pct-col').show();
            $('#pctSummary').show();
            $('.module-col').hide();
            $('#breakupHint').html('<strong>Percentage wise:</strong> enter % per row — amount auto-calculates from total project value. Total should be 100%.');
            updatePctTotal();
        }
    }

    function applyFyBounds() {
        var opt = $('#fy_start_year option:selected');
        var minD = opt.data('start');
        var maxD = opt.data('end');
        $('#amc_start, #amc_end').attr('min', minD).attr('max', maxD);
        $('#fy_range_hint').text(minD + ' – ' + maxD);
        if ($('#amc_start').val() < minD || $('#amc_start').val() > maxD) {
            $('#amc_start').val(minD);
        }
        if ($('#amc_end').val() < minD || $('#amc_end').val() > maxD) {
            $('#amc_end').val(maxD);
        }
        if ($('#amc_end').val() < $('#amc_start').val()) {
            $('#amc_end').val($('#amc_start').val());
        }
    }

    function recalcWorkingDays() {
        var y = $('#fy_start_year').val();
        var s = $('#amc_start').val();
        var e = $('#amc_end').val();
        if (!y || !s || !e) return;
        $.getJSON('ajax-amc-fy-days.php', { fy_start_year: y, amc_start: s, amc_end: e })
            .done(function (r) {
                if (!r.ok) {
                    $('#days_hint').html('<span class="text-danger">' + (r.message || 'Invalid dates') + '</span>');
                    return;
                }
                $('#amc_working_days').val(r.amc_working_days);
                $('#days_hint').text('Calendar: ' + r.calendar_days + ' days | Working (Mon–Fri): ' + r.amc_working_days);
            })
            .fail(function () {
                $('#days_hint').text('Could not calculate days.');
            });
    }

    function updatePctTotal() {
        var sum = 0;
        $('.pct-input').each(function () {
            sum += parseFloat($(this).val()) || 0;
        });
        var el = $('#totalPercentage');
        el.text(sum.toFixed(2) + '%');
        el.removeClass('ok over');
        if (Math.abs(sum - 100) < 0.01) {
            el.addClass('ok');
        } else if (sum > 0) {
            el.addClass('over');
        }
    }

    function recalcRowAmount($row) {
        var total = parseFloat($('#total_value').val()) || 0;
        var pct = parseFloat($row.find('.pct-input').val()) || 0;
        if ($('#payment_mode').val() === 'percentage' && total > 0 && pct > 0) {
            $row.find('.amt-input').val((total * pct / 100).toFixed(2));
        }
    }

    function validateMilestoneTotal() {
        var totalValue = parseFloat($('#total_value').val()) || 0;
        var totalMilestone = 0;
        
        $('.milestone-amount').each(function() {
            var val = parseFloat($(this).val()) || 0;
            totalMilestone += val;
        });
        
        $('#totalMilestoneAmount').text(totalMilestone.toFixed(2));
        
        if (totalValue > 0) {
            $('#milestoneTotalDisplay').show();
            
            if (totalMilestone > totalValue) {
                $('#milestoneStatus').html('<span class="text-danger">⚠️ Exceeds project value by ₹' + (totalMilestone - totalValue).toFixed(2) + '</span>');
                $('#milestoneValidationMsg').show();
                $('#saveBtn').prop('disabled', true);
                
                var remaining = totalValue;
                $('.milestone-amount').each(function() {
                    var val = parseFloat($(this).val()) || 0;
                    if (remaining > 0) {
                        $(this).removeClass('milestone-exceed');
                        remaining -= val;
                    } else {
                        $(this).addClass('milestone-exceed');
                    }
                });
            } else {
                $('#milestoneStatus').html('<span class="text-success">✓ Within project limit (Remaining: ₹' + (totalValue - totalMilestone).toFixed(2) + ')</span>');
                $('#milestoneValidationMsg').hide();
                $('#saveBtn').prop('disabled', false);
                $('.milestone-amount').removeClass('milestone-exceed');
            }
        } else {
            $('#milestoneTotalDisplay').hide();
            $('#milestoneValidationMsg').hide();
            $('#saveBtn').prop('disabled', false);
        }
        
        return totalMilestone <= totalValue;
    }

    $('#payment_mode').on('change', toggleMode);
    $('#fy_start_year').on('change', function () {
        applyFyBounds();
        recalcWorkingDays();
    });
    $('#amc_start, #amc_end').on('change', recalcWorkingDays);

    $('#milestones').on('input', '.amt-input', function () {
        recalcTaxDetails();
        validateMilestoneTotal();
    });

    $('#addMilestone').on('click', function () {
        var $row = $('.milestone-row:first').clone();
        $row.find('input').val('');
        $row.find('.remove-row').show();
        $row.find('[required]').removeAttr('required');
        $('#milestones').append($row);
        toggleMode();
        recalcTaxDetails();
        validateMilestoneTotal();
    });

    $('#milestones').on('click', '.remove-row', function () {
        if ($('.milestone-row').length > 1) {
            $(this).closest('.milestone-row').remove();
            updatePctTotal();
            recalcTaxDetails();
            validateMilestoneTotal();
        }
    });

    $('#milestones').on('input', '.pct-input', function () {
        recalcRowAmount($(this).closest('.milestone-row'));
        updatePctTotal();
        recalcTaxDetails();
        validateMilestoneTotal();
    });

    $('#total_value').on('input', function () {
        $('.milestone-row').each(function () {
            recalcRowAmount($(this));
        });
        recalcTaxDetails();
        validateMilestoneTotal();
    });

    function addWorkingDays(startDate, days) {
        var currentDate = new Date(startDate);
        var remainingDays = days;
        
        while (remainingDays > 0) {
            currentDate.setDate(currentDate.getDate() + 1);
            var dayOfWeek = currentDate.getDay();
            if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                remainingDays--;
            }
        }
        return currentDate;
    }

    $('#project_duration_days, #project_start_date').on('change', function() {
        var startDate = $('#project_start_date').val();
        var durationDays = parseInt($('#project_duration_days').val());
        
        if (startDate && durationDays && durationDays > 0) {
            var endDate = addWorkingDays(startDate, durationDays);
            var endDateStr = endDate.toISOString().split('T')[0];
            $('#project_end_date').val(endDateStr);
        } else {
            $('#project_end_date').val('');
        }
    });

    function recalcTaxDetails() {
        var clientId = parseInt($('#projectForm select[name="client_id"]').val() || '0', 10);
        var totalValue = parseFloat($('#total_value').val() || '0');
        
        if (!clientId || totalValue <= 0) {
            $('#total_with_tax_hidden').val('0');
            $('#final_amount_hidden').val('0');
            $('#total_value_with_tax').val('0');
            $('.tax-after-tax').text('—');
            $('#cgstBox, #sgstBox, #igstBox').hide();
            $('#total_tax_display').val('0% = ₹0.00');
            return;
        }
        
        var countryName = ($('#client_country').val() || '').toLowerCase();
        var stateName = ($('#client_state').val() || '').toLowerCase();
        var isInternational = countryName && (countryName !== 'india');
        
        var cgstPercent = 0, sgstPercent = 0, igstPercent = 0, totalPercent = 0;
        var cgstAmount = 0, sgstAmount = 0, igstAmount = 0, totalTax = 0;
        
        $('#cgstBox, #sgstBox, #igstBox').hide();
        
        if (isInternational) {
            totalPercent = 0;
            totalTax = 0;
            $('#totalTaxBox').show();
        } else if (stateName === 'uttar pradesh') {
            cgstPercent = 9;
            sgstPercent = 9;
            totalPercent = 18;
            cgstAmount = totalValue * 9 / 100;
            sgstAmount = totalValue * 9 / 100;
            totalTax = totalValue * 18 / 100;
            $('#cgstBox').show();
            $('#sgstBox').show();
            $('#totalTaxBox').show();
        } else {
            igstPercent = 18;
            totalPercent = 18;
            igstAmount = totalValue * 18 / 100;
            totalTax = totalValue * 18 / 100;
            $('#igstBox').show();
            $('#totalTaxBox').show();
        }
        
        // Update display
        $('#cgst_display').val(cgstPercent + '% = ₹' + cgstAmount.toFixed(2));
        $('#sgst_display').val(sgstPercent + '% = ₹' + sgstAmount.toFixed(2));
        $('#igst_display').val(igstPercent + '% = ₹' + igstAmount.toFixed(2));
        $('#total_tax_display').val(totalPercent + '% = ₹' + totalTax.toFixed(2));
        
        var totalIncl = totalValue + totalTax;
        $('#total_value_with_tax').val(totalIncl.toFixed(2));
        $('#total_with_tax_hidden').val(totalIncl.toFixed(2));
        $('#final_amount_hidden').val(totalIncl.toFixed(2));
        
        // Update milestone tax display
        $('.milestone-row').each(function() {
            var $row = $(this);
            var milestoneAmount = parseFloat($row.find('.amt-input').val()) || 0;
            var milestoneTax = milestoneAmount * totalPercent / 100;
            var milestoneIncl = milestoneAmount + milestoneTax;
            
            if (milestoneAmount > 0) {
                if (cgstPercent > 0) {
                    var milestoneCgst = milestoneAmount * cgstPercent / 100;
                    var milestoneSgst = milestoneAmount * sgstPercent / 100;
                    $row.find('.tax-after-tax').html(`${milestoneIncl.toFixed(2)}<br><small>CGST: ${milestoneCgst.toFixed(2)} | SGST: ${milestoneSgst.toFixed(2)}</small>`);
                } else if (igstPercent > 0) {
                    var milestoneIgst = milestoneAmount * igstPercent / 100;
                    $row.find('.tax-after-tax').html(`${milestoneIncl.toFixed(2)}<br><small>IGST: ${milestoneIgst.toFixed(2)}</small>`);
                } else {
                    $row.find('.tax-after-tax').html(`${milestoneIncl.toFixed(2)}<br><small>Tax: 0%</small>`);
                }
            } else {
                $row.find('.tax-after-tax').text('—');
            }
        });
    }

    $('#total_value_with_tax').on('input', function() {
        var totalWithTax = parseFloat($(this).val()) || 0;
        var totalValue = totalWithTax / 1.18;
        $('#total_value').val(totalValue.toFixed(2));
        recalcTaxDetails();
        validateMilestoneTotal();
    });

    $('#projectForm').on('submit', function (e) {
        if (isAmcOnly()) {
            return true;
        }
        
        var totalValue = parseFloat($('#total_value').val()) || 0;
        var totalMilestone = 0;
        $('.milestone-amount').each(function() {
            totalMilestone += parseFloat($(this).val()) || 0;
        });
        
        if (totalMilestone > totalValue) {
            e.preventDefault();
            $('#milestoneValidationMsg').show();
            alert('Total milestone amount (₹' + totalMilestone.toFixed(2) + ') cannot exceed Total Project Value (₹' + totalValue.toFixed(2) + ').');
            return false;
        }
        
        if ($('#payment_mode').val() === 'percentage') {
            var sum = 0;
            $('.pct-input').each(function () { sum += parseFloat($(this).val()) || 0; });
            if (sum > 0 && Math.abs(sum - 100) > 0.01) {
                if (!confirm('Total percentage is ' + sum.toFixed(2) + '% (not 100%). Save anyway?')) {
                    e.preventDefault();
                }
            }
        }
    });

    toggleEngagementType();
    applyFyBounds();
    recalcWorkingDays();
    loadClientCountryState();
    validateMilestoneTotal();
})();
</script>
<?php include 'EvaraFroentend/layout/footer.php'; ?>
<?php ob_end_flush(); ?>
<?php
ob_start();

include 'EvaraFroentend/layout/head.php';
include 'includes/auth.php';
include 'includes/helpers.php';
include_once 'includes/schema-bootstrap.php';

ni_ensure_database_schema($con);

$err = '';
$ok = '';
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    die('Invalid Project ID');
}

// Fetch existing project data
$projectQuery = "SELECT * FROM client_projects WHERE project_id = $id";
$p = $con->query($projectQuery)->fetch_assoc();

if (!$p) {
    die('Project not found.');
}

// Fetch clients and work types
$clientsList = [];
$cRes = $con->query('SELECT ClientID, ClientName, Country FROM clientmaster WHERE DelStatus = 1 ORDER BY ClientName');
if ($cRes) {
    while ($row = $cRes->fetch_assoc()) {
        $clientsList[] = $row;
    }
}

$workTypes = ni_get_work_types($con);
$fyOptions = ni_fy_options(3, 2);

// Populate $old with DB values
$old = [
    'engagement_type' => $p['engagement_type'] ?? 'project',
    'client_id' => $p['client_id'],
    'project_name' => $p['project_name'],
    'work_type_id' => $p['work_type_id'],
    'total_value' => $p['total_value'],
    'total_value_with_tax' => $p['total_value_with_tax'] ?? $p['total_with_tax'],
    'amc_split' => 'single', // Note: split logic is usually for auto-generation, not stored directly
    'amc_fee_due_date' => '',
    'payment_mode' => $p['payment_mode'] ?? 'percentage',
    'fy_start_year' => 0, 
    'amc_start' => $p['amc_start'],
    'amc_end' => $p['amc_end'],
    'amc_working_days' => $p['amc_working_days'],
    'notes' => $p['notes'],
    'project_start_date' => $p['project_start_date'],
    'project_end_date' => $p['project_end_date'],
    'project_duration_days' => $p['project_duration_days'],
    'has_amc' => $p['has_amc'] ?? 'no',
];

// Try to resolve fy_start_year from financial_year label (e.g., "2024-25" -> 2024)
if (!empty($p['financial_year'])) {
    $old['fy_start_year'] = (int)explode('-', $p['financial_year'])[0];
}

function ni_project_edit_tax_details(mysqli $con, int $clientId, float $totalValue): array
{
    $clientId = (int) $clientId;
    $res = $con->query("
        SELECT COALESCE(c.CountryName, cm.Country) AS country_name,
               COALESCE(s.StateName, cm.StateID) AS state_name
        FROM clientmaster cm
        LEFT JOIN countrymaster c ON cm.Country = c.CountryID
        LEFT JOIN statemaster s ON cm.StateID = s.StateID
        WHERE cm.ClientID = $clientId
        LIMIT 1
    ");
    $row = $res ? $res->fetch_assoc() : [];

    $country = strtolower(trim((string) ($row['country_name'] ?? '')));
    $state = strtolower(trim((string) ($row['state_name'] ?? '')));

    $cgstPercent = 0.0;
    $sgstPercent = 0.0;
    $igstPercent = 0.0;
    $taxType = 'international';

    if ($country === '' || $country === 'india') {
        if ($state === 'uttar pradesh') {
            $cgstPercent = 9.0;
            $sgstPercent = 9.0;
            $taxType = 'intra_state';
        } else {
            $igstPercent = 18.0;
            $taxType = 'inter_state';
        }
    }

    $cgstAmount = round($totalValue * $cgstPercent / 100, 2);
    $sgstAmount = round($totalValue * $sgstPercent / 100, 2);
    $igstAmount = round($totalValue * $igstPercent / 100, 2);
    $taxAmount = round($cgstAmount + $sgstAmount + $igstAmount, 2);

    return [
        'tax_percent' => $cgstPercent + $sgstPercent + $igstPercent,
        'tax_amount' => $taxAmount,
        'cgst_percent' => $cgstPercent,
        'cgst_amount' => $cgstAmount,
        'sgst_percent' => $sgstPercent,
        'sgst_amount' => $sgstAmount,
        'igst_percent' => $igstPercent,
        'igst_amount' => $igstAmount,
        'tax_type' => $taxType,
        'total_with_tax' => round($totalValue + $taxAmount, 2),
    ];
}

// Fetch existing milestones
$milestoneRows = [];
$mRes = $con->query("SELECT * FROM project_payment_milestones WHERE project_id = $id ORDER BY sort_order, milestone_id");
if ($mRes && $mRes->num_rows > 0) {
    while ($m = $mRes->fetch_assoc()) {
        $milestoneRows[] = [
            'milestone_id' => $m['milestone_id'],
            'milestone_label' => $m['milestone_label'],
            'module_name' => $m['module_name'],
            'percentage' => $m['percentage'],
            'milestone_amount' => $m['amount'],
            'due_date' => $m['due_date'],
        ];
    }
} else {
    $milestoneRows[] = ['milestone_id' => 0, 'milestone_label' => '', 'module_name' => '', 'percentage' => '', 'milestone_amount' => '', 'due_date' => ''];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update $old from POST
    $old['client_id'] = (int)($_POST['client_id'] ?? 0);
    $old['project_name'] = trim($_POST['project_name'] ?? '');
    $old['work_type_id'] = (int)($_POST['work_type_id'] ?? 0);
    $old['total_value'] = (float)($_POST['total_value'] ?? 0);
    $old['total_value_with_tax'] = (float)($_POST['total_value_with_tax'] ?? 0);
    $old['payment_mode'] = ($_POST['payment_mode'] ?? '') === 'module' ? 'module' : 'percentage';
    $old['fy_start_year'] = (int)($_POST['fy_start_year'] ?? 0);
    $old['amc_start'] = trim($_POST['amc_start'] ?? '');
    $old['amc_end'] = trim($_POST['amc_end'] ?? '');
    $old['amc_working_days'] = (int)($_POST['amc_working_days'] ?? 0);
    $old['notes'] = trim($_POST['notes'] ?? '');
    $old['project_start_date'] = trim($_POST['project_start_date'] ?? '');
    $old['project_end_date'] = trim($_POST['project_end_date'] ?? '');
    $old['project_duration_days'] = (int)($_POST['project_duration_days'] ?? 0);
    $old['has_amc'] = $_POST['has_amc'] ?? 'no';

    // Milestones from POST
    $mIds = $_POST['milestone_id'] ?? [];
    $labels = $_POST['milestone_label'] ?? [];
    $modules = $_POST['module_name'] ?? [];
    $pcts = $_POST['percentage'] ?? [];
    $amounts = $_POST['milestone_amount'] ?? [];
    $dueDates = $_POST['due_date'] ?? [];
    
    $validMilestones = [];
    foreach ($labels as $i => $label) {
        if (trim($label) !== '') {
            $validMilestones[] = [
                'milestone_id' => (int)($mIds[$i] ?? 0),
                'milestone_label' => trim($label),
                'module_name' => trim($modules[$i] ?? ''),
                'percentage' => trim($pcts[$i] ?? ''),
                'milestone_amount' => trim($amounts[$i] ?? ''),
                'due_date' => trim($dueDates[$i] ?? ''),
            ];
        }
    }

    // Basic Validation (reused logic)
    $period = $old['fy_start_year'] > 0 ? ni_fy_to_period($old['fy_start_year']) : null;
    $engagementType = ni_engagement_from_work_type_id($con, (int) $old['work_type_id']);

    if ($old['client_id'] <= 0) $err = 'Please select a client.';
    elseif ($old['project_name'] === '') $err = 'Project name is required.';
    elseif ($old['work_type_id'] <= 0) $err = 'Please select work type.';
    elseif ($old['has_amc'] === 'yes' && !$period) $err = 'Please select financial year for AMC.';

    if (empty($err)) {
        $taxDetails = ni_project_edit_tax_details($con, (int) $old['client_id'], (float) $old['total_value']);
        $old['total_value_with_tax'] = (float) $taxDetails['total_with_tax'];

        $projectNameEsc = $con->real_escape_string($old['project_name']);
        $notesEsc = $con->real_escape_string($old['notes']);
        $fyLabel = $period ? $period['label'] : '';
        $taxTypeEsc = $con->real_escape_string((string) $taxDetails['tax_type']);
        
        $con->begin_transaction();
        try {
            $sql = "UPDATE client_projects SET 
                client_id = {$old['client_id']},
                project_name = '$projectNameEsc',
                engagement_type = '$engagementType',
                has_amc = '{$old['has_amc']}',
                payment_mode = '{$old['payment_mode']}',
                total_value = {$old['total_value']},
                tax_percent = {$taxDetails['tax_percent']},
                tax_amount = {$taxDetails['tax_amount']},
                cgst_percent = {$taxDetails['cgst_percent']},
                cgst_amount = {$taxDetails['cgst_amount']},
                sgst_percent = {$taxDetails['sgst_percent']},
                sgst_amount = {$taxDetails['sgst_amount']},
                igst_percent = {$taxDetails['igst_percent']},
                igst_amount = {$taxDetails['igst_amount']},
                tax_type = '$taxTypeEsc',
                total_value_with_tax = {$old['total_value_with_tax']},
                total_with_tax = {$taxDetails['total_with_tax']},
                final_amount = {$taxDetails['total_with_tax']},
                project_start_date = " . ($old['project_start_date'] ? "'{$old['project_start_date']}'" : "NULL") . ",
                project_end_date = " . ($old['project_end_date'] ? "'{$old['project_end_date']}'" : "NULL") . ",
                project_duration_days = {$old['project_duration_days']},
                amc_start = " . ($old['amc_start'] ? "'{$old['amc_start']}'" : "NULL") . ",
                amc_end = " . ($old['amc_end'] ? "'{$old['amc_end']}'" : "NULL") . ",
                financial_year = '$fyLabel',
                amc_working_days = {$old['amc_working_days']},
                work_type_id = {$old['work_type_id']},
                notes = '$notesEsc'
                WHERE project_id = $id";

            $con->query($sql);

            // Handle Milestones
            $keepIds = [];
            $sort = 0;
            foreach ($validMilestones as $ms) {
                $label = $con->real_escape_string($ms['milestone_label']);
                $mod = $con->real_escape_string($ms['module_name']);
                $postedPct = (float) $ms['percentage'];
                $amt = $old['payment_mode'] === 'percentage' && $postedPct > 0
                    ? round((float) $old['total_value'] * $postedPct / 100, 2)
                    : (float) $ms['milestone_amount'];
                $milestoneTax = round($amt * (float) $taxDetails['tax_percent'] / 100, 2);
                $milestoneWithTax = round($amt + $milestoneTax, 2);
                $pct = $old['payment_mode'] === 'percentage' ? $postedPct : "NULL";
                $due = $ms['due_date'] ? "'".$con->real_escape_string($ms['due_date'])."'" : "NULL";
                
                if ($ms['milestone_id'] > 0) {
                    $msSql = "UPDATE project_payment_milestones SET 
                        milestone_label = '$label', module_name = '$mod', percentage = $pct, 
                        amount = $amt, tax_amount = $milestoneTax, amount_with_tax = $milestoneWithTax,
                        due_date = $due, sort_order = $sort
                        WHERE milestone_id = {$ms['milestone_id']} AND project_id = $id";
                    $con->query($msSql);
                    $keepIds[] = $ms['milestone_id'];
                } else {
                    $msSql = "INSERT INTO project_payment_milestones (project_id, milestone_label, module_name, percentage, amount, tax_amount, amount_with_tax, due_date, sort_order)
                              VALUES ($id, '$label', '$mod', $pct, $amt, $milestoneTax, $milestoneWithTax, $due, $sort)";
                    $con->query($msSql);
                    $keepIds[] = $con->insert_id;
                }
                $sort++;
            }

            // Remove deleted rows
            if (!empty($keepIds)) {
                $keepStr = implode(',', $keepIds);
                $con->query("DELETE FROM project_payment_milestones WHERE project_id = $id AND milestone_id NOT IN ($keepStr)");
            }

            $con->commit();
            ob_end_clean();
            header("Location: view-client-project.php?id=$id&updated=1");
            exit;
        } catch (Exception $e) {
            $con->rollback();
            $err = 'Update failed: ' . $e->getMessage();
        }
    }
}

include 'EvaraFroentend/layout/header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
<style>
    .ni-page label { font-weight: 600; margin-bottom: 4px; }
    .ni-section-title { font-size: 1rem; font-weight: 700; color: #333; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 2px solid #3bb77e; }
    .milestone-row { background: #f8f9fa; border-radius: 8px; padding: 10px; margin-bottom: 8px; }
    .tax-input-box { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 10px; margin-top: 10px; }
    .amc-optional-section { background: #f0f7ff; border: 1px solid #cce5ff; border-radius: 8px; padding: 15px; margin-bottom: 20px; }
</style>

<div class="container mt-15 ni-page">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <div>
                <h3 class="mb-0">Edit Client Project</h3>
                <small class="text-muted">Update details for: <strong><?php echo htmlspecialchars($p['project_name']); ?></strong></small>
            </div>
            <a href="view-client-project.php?id=<?php echo $id; ?>" class="btn btn-outline-secondary btn-sm">Back to Project</a>
        </div>

        <div class="card-body">
            <?php if ($err): ?><div class="alert alert-danger"><?php echo $err; ?></div><?php endif; ?>

            <form method="post" enctype="multipart/form-data" id="projectForm">
                <p class="ni-section-title">1. Project Details</p>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label>Client <span class="text-danger">*</span></label>
                        <select name="client_id" id="client_id" class="form-control" required>
                            <?php foreach ($clientsList as $c): ?>
                            <option value="<?php echo $c['ClientID']; ?>" <?php echo $old['client_id'] == $c['ClientID'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['ClientName']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Country</label>
                        <input type="text" id="client_country" class="form-control" readonly>
                    </div>
                    <div class="col-md-4">
                        <label>State</label>
                        <input type="text" id="client_state" class="form-control" readonly>
                    </div>
                    <div class="col-md-4">
                        <label>Project Name <span class="text-danger">*</span></label>
                        <input type="text" name="project_name" class="form-control" required value="<?php echo htmlspecialchars($old['project_name']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label>Work Type <span class="text-danger">*</span></label>
                        <select name="work_type_id" id="work_type_id" class="form-control" required>
                            <?php foreach ($workTypes as $wt): ?>
                            <option value="<?php echo $wt['WorkTypeID']; ?>" 
                                data-amc-type="<?php echo ($wt['amc_type'] ?? 'project'); ?>"
                                <?php echo $old['work_type_id'] == $wt['WorkTypeID'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($wt['WorkTypeName']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Base Value (₹)</label>
                        <input type="number" step="0.01" name="total_value" id="total_value" class="form-control" value="<?php echo $old['total_value']; ?>">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <div class="tax-input-box">
                            <h6>💰 Tax Details</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Total Tax (18% approx)</label>
                                    <input type="text" id="total_tax_display" class="form-control" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label>Project Value with Tax (₹)</label>
                                    <input type="number" step="0.01" name="total_value_with_tax" id="total_value_with_tax" class="form-control bg-light" value="<?php echo $old['total_value_with_tax']; ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label>Duration (Working Days)</label>
                        <input type="number" name="project_duration_days" id="project_duration_days" class="form-control" value="<?php echo $old['project_duration_days']; ?>">
                    </div>
                    <div class="col-md-4">
                        <label>Start Date</label>
                        <input type="date" name="project_start_date" id="project_start_date" class="form-control" value="<?php echo $old['project_start_date']; ?>">
                    </div>
                    <div class="col-md-4">
                        <label>End Date</label>
                        <input type="date" name="project_end_date" id="project_end_date" class="form-control" value="<?php echo $old['project_end_date']; ?>">
                    </div>
                </div>

                <p class="ni-section-title">2. AMC (Optional)</p>
                <div class="amc-optional-section">
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="has_amc" name="has_amc" value="yes" <?php echo $old['has_amc'] === 'yes' ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="has_amc"><strong>Include AMC</strong></label>
                    </div>
                    <div id="amcFields" style="<?php echo $old['has_amc'] === 'yes' ? '' : 'display:none;'; ?>">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label>FY</label>
                                <select name="fy_start_year" id="fy_start_year" class="form-control">
                                    <?php foreach ($fyOptions as $fy): ?>
                                    <option value="<?php echo $fy['start_year']; ?>" 
                                        data-start="<?php echo $fy['start']; ?>" data-end="<?php echo $fy['end']; ?>"
                                        <?php echo $old['fy_start_year'] == $fy['start_year'] ? 'selected' : ''; ?>>
                                        FY <?php echo $fy['label']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>AMC Start</label>
                                <input type="date" name="amc_start" id="amc_start" class="form-control" value="<?php echo $old['amc_start']; ?>">
                            </div>
                            <div class="col-md-3">
                                <label>AMC End</label>
                                <input type="date" name="amc_end" id="amc_end" class="form-control" value="<?php echo $old['amc_end']; ?>">
                            </div>
                            <div class="col-md-3">
                                <label>Working Days</label>
                                <input type="number" name="amc_working_days" id="amc_working_days" class="form-control" value="<?php echo $old['amc_working_days']; ?>" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="ni-section-title">3. Milestones</p>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Breakup Type</label>
                        <select name="payment_mode" id="payment_mode" class="form-control">
                            <option value="percentage" <?php echo $old['payment_mode'] === 'percentage' ? 'selected' : ''; ?>>Percentage (%)</option>
                            <option value="module" <?php echo $old['payment_mode'] === 'module' ? 'selected' : ''; ?>>Module Fixed Amount</option>
                        </select>
                    </div>
                </div>

                <div id="milestones">
                    <?php foreach ($milestoneRows as $idx => $m): ?>
                    <div class="row g-2 milestone-row align-items-center">
                        <input type="hidden" name="milestone_id[]" value="<?php echo $m['milestone_id']; ?>">
                        <div class="col-md-3">
                            <label class="small d-md-none">Label</label>
                            <input type="text" name="milestone_label[]" class="form-control" placeholder="Label" value="<?php echo htmlspecialchars($m['milestone_label']); ?>">
                        </div>
                        <div class="col-md-2 module-col">
                            <label class="small d-md-none">Module</label>
                            <input type="text" name="module_name[]" class="form-control" placeholder="Module" value="<?php echo htmlspecialchars($m['module_name']); ?>">
                        </div>
                        <div class="col-md-2 pct-col">
                            <label class="small d-md-none">%</label>
                            <input type="number" step="0.01" name="percentage[]" class="form-control pct-input" placeholder="%" value="<?php echo $m['percentage']; ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="small d-md-none">Amount</label>
                            <input type="number" step="0.01" name="milestone_amount[]" class="form-control amt-input" placeholder="Amount" value="<?php echo $m['milestone_amount']; ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="small d-md-none">Due Date</label>
                            <input type="date" name="due_date[]" class="form-control" value="<?php echo $m['due_date']; ?>">
                        </div>
                        <div class="col-md-1 text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-row <?php echo $idx === 0 ? 'd-none' : ''; ?>">×</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn btn-success btn-sm mt-2" id="addMilestone">+ Add Milestone</button>

                <div class="mt-4 border-top pt-3">
                    <label>Notes</label>
                    <textarea name="notes" class="form-control" rows="2"><?php echo htmlspecialchars($old['notes']); ?></textarea>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary px-5">Update Project</button>
                    <a href="view-client-project.php?id=<?php echo $id; ?>" class="btn btn-link">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
$(document).ready(function() {
    function loadClientDetails() {
        var cid = $('#client_id').val();
        if(!cid) return;
        $.getJSON('get_client_details.php', {client_id: cid}, function(r) {
            if(r.ok) {
                $('#client_country').val(r.country_name);
                $('#client_state').val(r.state_name);
                recalcTax();
            }
        });
    }

    function recalcTax() {
        var base = parseFloat($('#total_value').val()) || 0;
        var tax = base * 0.18;
        $('#total_tax_display').val('18% = ₹' + tax.toFixed(2));
        // Note: we don't force overwrite total_value_with_tax here to allow manual adjustments
    }

    function recalcTax() {
        var base = parseFloat($('#total_value').val()) || 0;
        var countryName = ($('#client_country').val() || '').toLowerCase();
        var isInternational = countryName && countryName !== 'india';
        var taxPercent = isInternational ? 0 : 18;
        var tax = base * taxPercent / 100;
        var totalWithTax = base + tax;

        $('#total_tax_display').val(taxPercent + '% = ₹' + tax.toFixed(2));
        $('#total_value_with_tax').val(totalWithTax.toFixed(2));
    }

    function recalcPercentageMilestones() {
        if ($('#payment_mode').val() !== 'percentage') {
            return;
        }

        var total = parseFloat($('#total_value').val()) || 0;
        $('#milestones .milestone-row').each(function() {
            var pct = parseFloat($(this).find('.pct-input').val()) || 0;
            if (pct > 0) {
                $(this).find('.amt-input').val((total * pct / 100).toFixed(2));
            }
        });
    }

    function syncMissingMilestonePercentages() {
        if ($('#payment_mode').val() !== 'percentage') {
            return;
        }

        var total = parseFloat($('#total_value').val()) || 0;
        if (total <= 0) {
            return;
        }

        $('#milestones .milestone-row').each(function() {
            var $row = $(this);
            var $pct = $row.find('.pct-input');
            var pct = parseFloat($pct.val()) || 0;
            var amount = parseFloat($row.find('.amt-input').val()) || 0;

            if (pct <= 0 && amount > 0) {
                $pct.val((amount / total * 100).toFixed(2));
            }
        });
    }

    function toggleMode() {
        var m = $('#payment_mode').val();
        $('.pct-col').toggle(m === 'percentage');
        $('.module-col').toggle(m === 'module');
    }

    $('#has_amc').change(function() { $('#amcFields').toggle(this.checked); });
    $('#payment_mode').change(function() {
        toggleMode();
        syncMissingMilestonePercentages();
        recalcPercentageMilestones();
    });
    $('#total_value').on('input change keyup', function() {
        syncMissingMilestonePercentages();
        recalcTax();
        recalcPercentageMilestones();
    });
    $('#client_id').change(loadClientDetails);

    $('#addMilestone').click(function() {
        var $row = $('.milestone-row:first').clone();
        $row.find('input').val('');
        $row.find('input[name="milestone_id[]"]').val('0');
        $row.find('.remove-row').removeClass('d-none');
        $('#milestones').append($row);
    });

    $('#milestones').on('click', '.remove-row', function() {
        $(this).closest('.row').remove();
    });

    // Auto-calculate row amounts if percentage mode
    $('#milestones').on('input', '.pct-input', function() {
        if($('#payment_mode').val() === 'percentage') {
            var total = parseFloat($('#total_value').val()) || 0;
            var pct = parseFloat($(this).val()) || 0;
            $(this).closest('.row').find('.amt-input').val((total * pct / 100).toFixed(2));
        }
    });

    $('#milestones').on('input change keyup', '.amt-input', function() {
        if ($('#payment_mode').val() === 'percentage') {
            var total = parseFloat($('#total_value').val()) || 0;
            var amount = parseFloat($(this).val()) || 0;
            if (total > 0) {
                $(this).closest('.row').find('.pct-input').val((amount / total * 100).toFixed(2));
            }
        }
    });

    // Date logic: Add working days
    function addWorkingDays(startDate, days) {
        var d = new Date(startDate);
        while (days > 0) {
            d.setDate(d.getDate() + 1);
            if (d.getDay() !== 0 && d.getDay() !== 6) days--;
        }
        return d.toISOString().split('T')[0];
    }

    $('#project_duration_days, #project_start_date').on('change', function() {
        var s = $('#project_start_date').val();
        var dur = parseInt($('#project_duration_days').val());
        if(s && dur > 0) $('#project_end_date').val(addWorkingDays(s, dur));
    });

    // Init
    loadClientDetails();
    toggleMode();
    syncMissingMilestonePercentages();
    recalcTax();
    recalcPercentageMilestones();
});
</script>

<?php include 'EvaraFroentend/layout/footer.php'; ob_end_flush(); ?>

<?php
include("EvaraFroentend/layout/head.php");
include("EvaraFroentend/layout/header.php");

// Fetch client records from the database
$sqlClients = "SELECT * FROM clientmaster WHERE DelStatus = 1";
$resultClients = $con->query($sqlClients);

// Check if the query was successful
if ($resultClients === FALSE) {
    die("Error: " . $con->error);
}

// Fetch client data and store it in an associative array
$clients = array();
while ($rowClient = $resultClients->fetch_assoc()) {
    $clients[] = $rowClient;
}

// Close the result set
$resultClients->close();

?>
<style>
    label {
        font-weight: 700;
    }
</style>
<div class="container mt-15">
    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create Prefarma Invoice</h3>
                </div>
                <div class="card-body">
                    <form action="create-invoice-fixed.php" method="post" id="basic-form" novalidate="novalidate"
                        class="row g-3" enctype="multipart/form-data">
                        <div class="col-md-3">
                            <label>Invoice Type</label>
                            <div class="form-check">
                                <input class="form-check-input invoiceType" type="radio" name="invoice_type"
                                    id="newInvoice" value="1" checked>
                                <label class="form-check-label" for="newInvoice">New Invoice</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input invoiceType" type="radio" name="invoice_type"
                                    id="oldInvoice" value="2">
                                <label class="form-check-label" for="oldInvoice">Old Invoice</label>
                            </div>
                        </div>
                        <!-- Textbox for Old Invoice (Initially hidden) -->
                        <div class="col-md-3" id="oldInvoiceTextbox" style="display: none;">
                            <label>Old Invoice Number</label>
                            <input type="text" name="oldInvoiceNumber" class="form-control" id="oldInvoiceNumber" />
                            <span id="invoiceNumberMessage" style="color:red;"></span>
                        </div>
                        <div class="col-md-3" id="newInvoiceTextbox">
                            <label>New Invoice Number</label>
                            <input type="text" readonly name="newInvoiceNumber" class="form-control"
                                id="newInvoiceNumber" />
                            <!-- <span id="invoiceNumberMessage" style="color:red;"></span> -->
                        </div>
                        <?php
                        $prefillClientId = isset($_GET['client_id']) ? (int) $_GET['client_id'] : 0;
                        $prefillMilestone1Label = isset($_GET['milestone_1_label']) ? (string) $_GET['milestone_1_label'] : '';
                        $prefillMilestone1Amount = isset($_GET['milestone_1_amount']) ? (string) $_GET['milestone_1_amount'] : '0.00';
                        $prefillMilestone1DueDate = isset($_GET['milestone_1_due_date']) ? (string) $_GET['milestone_1_due_date'] : '';
                        $prefillMilestonePct = isset($_GET['milestone_pct']) ? (float) $_GET['milestone_pct'] : 0;
                        $projectTotal = isset($_GET['project_total']) ? (float) $_GET['project_total'] : 0;
                        $prefillMilestoneId = isset($_GET['milestone_id']) ? (int) $_GET['milestone_id'] : 0;

                        $projectMilestones = [];
                        $projectName = '';
                        if ($prefillMilestoneId > 0) {
                            $selectedMilestoneSql = "SELECT m.*, p.project_name, p.client_id, p.total_value
                                     FROM project_payment_milestones m
                                     JOIN client_projects p ON m.project_id = p.project_id
                                     WHERE m.milestone_id = $prefillMilestoneId
                                     LIMIT 1";
                            $selectedMilestoneRes = $con->query($selectedMilestoneSql);
                            $selectedMilestone = $selectedMilestoneRes ? $selectedMilestoneRes->fetch_assoc() : null;
                            if ($selectedMilestone) {
                                $prefillClientId = $prefillClientId > 0 ? $prefillClientId : (int) ($selectedMilestone['client_id'] ?? 0);
                                $projectTotal = $projectTotal > 0 ? $projectTotal : (float) ($selectedMilestone['total_value'] ?? 0);
                                $projectName = (string) ($selectedMilestone['project_name'] ?? '');

                                $baseAmount = (float) ($selectedMilestone['amount'] ?? 0);
                                $invoicedAmount = (float) ($selectedMilestone['invoiced_amount'] ?? 0);
                                $remainingBaseAmount = max(0, $baseAmount - $invoicedAmount);

                                if ($prefillMilestone1Label === '') {
                                    $prefillMilestone1Label = ($remainingBaseAmount < $baseAmount - 0.01)
                                        ? (string) ($selectedMilestone['milestone_label'] ?? '') . ' (Remaining)'
                                        : (string) ($selectedMilestone['milestone_label'] ?? '');
                                }
                                if ($prefillMilestone1Amount === '0.00' || $prefillMilestone1Amount === '') {
                                    $prefillMilestone1Amount = number_format($remainingBaseAmount, 2, '.', '');
                                }
                                if ($prefillMilestone1DueDate === '' && !empty($selectedMilestone['due_date'])) {
                                    $prefillMilestone1DueDate = date('Y-m-d', strtotime($selectedMilestone['due_date']));
                                }
                                if ($prefillMilestonePct <= 0 && $projectTotal > 0) {
                                    $prefillMilestonePct = ($remainingBaseAmount / $projectTotal) * 100;
                                }
                            }

                            // Fetch all milestones for the project this milestone belongs to
                            $msql = "SELECT m.*, p.project_name, p.client_id, p.total_value 
                                     FROM project_payment_milestones m
                                     JOIN client_projects p ON m.project_id = p.project_id
                                     WHERE m.project_id = (SELECT project_id FROM project_payment_milestones WHERE milestone_id = $prefillMilestoneId)
                                     ORDER BY m.sort_order, m.milestone_id";
                            $mres = $con->query($msql);
                            if ($mres) {
                                while ($mrow = $mres->fetch_assoc()) {
                                    $projectMilestones[] = $mrow;
                                    $projectName = $mrow['project_name'];
                                    if ((int) $mrow['milestone_id'] === $prefillMilestoneId) {
                                        $prefillClientId = $prefillClientId > 0 ? $prefillClientId : (int) ($mrow['client_id'] ?? 0);
                                        $projectTotal = $projectTotal > 0 ? $projectTotal : (float) ($mrow['total_value'] ?? 0);

                                        $baseAmount = (float) ($mrow['amount'] ?? 0);
                                        $invoicedAmount = (float) ($mrow['invoiced_amount'] ?? 0);
                                        $remainingBaseAmount = max(0, $baseAmount - $invoicedAmount);

                                        if ($prefillMilestone1Label === '') {
                                            $prefillMilestone1Label = ($remainingBaseAmount < $baseAmount - 0.01)
                                                ? (string) ($mrow['milestone_label'] ?? '') . ' (Remaining)'
                                                : (string) ($mrow['milestone_label'] ?? '');
                                        }
                                        if ($prefillMilestone1Amount === '0.00' || $prefillMilestone1Amount === '') {
                                            $prefillMilestone1Amount = number_format($remainingBaseAmount, 2, '.', '');
                                        }
                                        if ($prefillMilestone1DueDate === '' && !empty($mrow['due_date'])) {
                                            $prefillMilestone1DueDate = date('Y-m-d', strtotime($mrow['due_date']));
                                        }
                                        if ($prefillMilestonePct <= 0 && $projectTotal > 0) {
                                            $prefillMilestonePct = ($remainingBaseAmount / $projectTotal) * 100;
                                        }
                                    }
                                }
                            }
                            if (empty($projectMilestones) && !empty($selectedMilestone)) {
                                $projectMilestones[] = $selectedMilestone;
                            }
                        }
                        ?>
                        <div class="col-md-3">
                            <label>To Client</label>
                            <select required class="form-control" name="client_id" id="clientDropdown">
                                <option value="0">---Select Client---</option>
                                <?php $clientOptionFound = false; ?>
                                <?php foreach ($clients as $client): ?>
                                    <?php $cid = (int) ($client['ClientID'] ?? 0); ?>
                                    <?php if ($prefillClientId > 0 && $cid === $prefillClientId) {
                                        $clientOptionFound = true;
                                    } ?>
                                    <option value="<?php echo $cid; ?>" <?php echo ($prefillClientId > 0 && $cid === $prefillClientId) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars((string) ($client['ClientName'] ?? '')); ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php if ($prefillClientId > 0 && !$clientOptionFound): ?>
                                    <?php
                                    $missingClientRes = $con->query("SELECT ClientID, ClientName FROM clientmaster WHERE ClientID = $prefillClientId LIMIT 1");
                                    $missingClient = $missingClientRes ? $missingClientRes->fetch_assoc() : null;
                                    ?>
                                    <?php if ($missingClient): ?>
                                        <option value="<?php echo (int) $missingClient['ClientID']; ?>" selected>
                                            <?php echo htmlspecialchars((string) $missingClient['ClientName']); ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3">

                            <div id="clientDetails">
                                <!-- Clint Details -->
                            </div>
                        </div>

                        <?php if ($projectTotal > 0): ?>
                            <div class="col-md-12">
                                <div class="badge bg-light text-dark border p-2 mb-2">
                                    <i class="fas fa-calculator"></i> Calculations based on Project Total:
                                    <strong>₹<?php echo number_format($projectTotal, 2); ?></strong>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Hidden field to store project total for JS calculations -->
                        <input type="hidden" id="linked_project_total" value="<?php echo $projectTotal; ?>">
                        <!-- Hidden field to link invoice back to a specific milestone -->
                        <input type="hidden" name="milestone_id" value="<?php echo $prefillMilestoneId; ?>">

                        <div class="card-header">
                            <h6 class="card-title">Self Details</h6>

                        </div>
                        <div class="col-md-4">
                            <label>Invoice Date</label>
                            <input required type="date" name="invoiceDate" id="invoiceDate"
                                class="form-control currentdate" label="date" />
                        </div>
                        <div class="col-md-4">
                            <label>Concern Person</label>
                            <input required type="text" name="concern_person" class="form-control"
                                label="concern_person" value="Shashank Shrivastava" />
                        </div>
                        <div class="col-md-4">
                            <label>Mobile</label>
                            <input required type="num" name="mobile" class="form-control" label="mobile"
                                value="8318721943" />
                        </div>

                        <div class="col-md-4">
                            <label>Term & Condition</label>
                            <textarea required type="text" name="condition" class="form-control" label="Contact Number"
                                value="">E.& O.E.
                                1. Payment to be made by 
                                Cheque/NEFT/RTGS
                                2. Interest @18% p.a. will be 
                                charged if the payment is not 
                                made within stipulated time.
                                    
                                </textarea>
                        </div>
                        <div class="col-md-4">
                            <label>Delivery Date</label>
                            <input required type="date" class="form-control currentdate" name="deliveryDate"
                                label="Salary Per Month" value="" />
                        </div>
                        <div class="col-md-4">
                            <label>Due Date</label>
                            <input required type="date" class="form-control currentdate" name="deudate"
                                label="Full Address As Aadhar"
                                value="<?php echo htmlspecialchars($prefillMilestone1DueDate); ?>" />
                        </div>

                        <?php if ($prefillMilestoneId > 0): ?>
                            <div class="card-header">
                                <h6 class="card-title">Milestone Detail</h6>
                            </div>

                            <div class="col-md-12">
                                <?php if (!empty($projectMilestones)): ?>
                                    <div class="alert alert-info py-2 mb-3">
                                        <i class="fas fa-project-diagram"></i> Project:
                                        <strong><?php echo htmlspecialchars($projectName); ?></strong>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Milestone Label</th>
                                                    <th>Amount (₹)</th>
                                                    <th>Due Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($projectMilestones as $pm): ?>
                                                    <tr
                                                        class="<?php echo ($pm['milestone_id'] == $prefillMilestoneId) ? 'table-primary border-primary' : ''; ?>">
                                                        <td><strong><?php echo htmlspecialchars($pm['milestone_label']); ?></strong>
                                                        </td>
                                                        <td>₹<?php echo number_format($pm['amount'], 2); ?></td>
                                                        <td><?php echo $pm['due_date'] ? date('d M Y', strtotime($pm['due_date'])) : '-'; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($pm['is_paid']): ?>
                                                                <span class="badge bg-success">Paid</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-warning text-dark">Pending</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="row g-3" id="milestoneSection">
                                        <div class="col-md-4">
                                            <label>Milestone 1 (Readonly)</label>
                                            <input type="text" class="form-control" name="milestone_1" id="milestone_1" readonly
                                                value="Milestone 1" />
                                        </div>
                                        <div class="col-md-4">
                                            <label>Milestone 1 Amount (Readonly)</label>
                                            <input type="text" class="form-control" name="milestone_1_amount"
                                                id="milestone_1_amount" readonly value="0.00" />
                                        </div>
                                        <div class="col-md-4">
                                            <label>Milestone 1 Date (Readonly)</label>
                                            <input type="date" class="form-control" name="milestone_1_date"
                                                id="milestone_1_date" readonly
                                                value="<?php echo htmlspecialchars($prefillMilestone1DueDate); ?>" />
                                        </div>
                                        <div class="col-md-4">
                                            <label>Milestone 2 (Readonly)</label>
                                            <input type="text" class="form-control" name="milestone_2" id="milestone_2" readonly
                                                value="Phase 2" />
                                        </div>
                                        <div class="col-md-4">
                                            <label>Milestone 2 Amount (Readonly)</label>
                                            <input type="text" class="form-control" name="milestone_2_amount"
                                                id="milestone_2_amount" readonly value="0.00" />
                                        </div>
                                        <div class="col-md-4">
                                            <label>Milestone 2 Date (Readonly)</label>
                                            <input type="date" class="form-control" name="milestone_2_date"
                                                id="milestone_2_date" readonly value="" />
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div id="discriptionSection">
                            <!-- Initial row -->
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Description</label>
                                    <input required type="text" class="form-control" name="discription[]"
                                        label="Add Legal Id"
                                        value="<?php echo ($prefillMilestoneId > 0) ? htmlspecialchars($prefillMilestone1Label) : ''; ?>" />
                                </div>
                                <div class="col-md-2">
                                    <label>Duration</label>
                                    <input required type="text" class="form-control" name="duration_days[]"
                                        placeholder="e.g. 6 Days, 3 Months" value="" />
                                </div>
                                <div class="col-md-2">
                                    <label>Bill %</label>
                                    <input type="number" step="0.01" class="form-control pct-calc" name="bill_pct[]"
                                        placeholder="%"
                                        value="<?php echo $prefillMilestonePct > 0 ? $prefillMilestonePct : ''; ?>" />
                                </div>
                                <div class="col-md-2">
                                    <label>Amount</label>
                                    <input required type="text" class="form-control milestone-amt-field" name="Amount[]"
                                        label="Add Legal Id"
                                        value="<?php echo ($prefillMilestoneId > 0) ? htmlspecialchars($prefillMilestone1Amount) : ''; ?>" />
                                </div>
                                <div class="col-md-3 mt-4">
                                    <div class="btn btn-success addMore">Add More</div>
                                    <div class="btn btn-danger remove" style="display: none;">Remove</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-header">
                            <h6 class="card-title">Amount Detail</h6>

                        </div>

                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-3"><label>Sub Total </label></div>
                                <div class="col-3"><input required type="text" class="" name="subtotal" id="subtotal" />
                                    
                                </div>
                                <div class="col-3"><span class="ms-2 text-muted usd-equivalent-field" id="subtotal_usd"></span>
                                    
                                </div>

                            </div>
                            <div class="row mt-2">
                                <div class="col-3"><label>Discount</label> </div>
                                <div class="col-3"><input type="text" class="" name="discount" id="discount" />
                                   
                                </div>
                                <div class="col-3"> <span class="ms-2 text-muted usd-equivalent-field" id="discount_usd"></span>
                                   
                                </div>


                            </div>
                            <div class="row mt-2">
                                <div class="col-3"><label>Final Amount </label></div>
                                <div class="col-3"><input required type="text" class="" name="finalAmount"
                                        id="finalAmount" />
                                   
                                </div>
                                <div class="col-3"> <span class="ms-2 text-muted usd-equivalent-field" id="finalAmount_usd"></span>
                                   
                                </div>
                            </div>
                            <div id="gst" style="display: none;">
                                <div class="row mt-2">
                                    <div class="col-3"><label>SGST</label> </div>
                                    <div class="col-3"><input required type="text" name="sgstAmount" class=""
                                            id="sgstAmount" />
                                      
                                    </div>
                                    <div class="col-3">  <span class="ms-2 text-muted usd-equivalent-field" id="sgstAmount_usd"></span>
                                      
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-3"><label>CGST</label> </div>
                                    <div class="col-3"><input required type="text" class="" name="cgstAmount"
                                            id="cgstAmount" />
                                        
                                    </div>
                                    <div class="col-3"><span class="ms-2 text-muted usd-equivalent-field" id="cgstAmount_usd"></span>
                                        
                                    </div>
                                </div>

                            </div>
                            <div class="row mt-2" id="igst" style="display: none;">
                                <div class="col-3"><label>IGST</label> </div>
                                <div class="col-3"><input type="text" name="igstAmount" class="" id="igstAmount" />
                                    
                                </div>
                                <div class="col-3"><span class="ms-2 text-muted usd-equivalent-field" id="igstAmount_usd"></span>
                                    
                                </div>

                            </div>
                            <div class="row mt-2" id="bankChargeRow" style="display: none;">
                                <div class="col-3"><label>International Bank Charge @3.5%</label></div>
                                <div class="col-3">
                                    <input type="text" name="bankChargeAmount" class="" id="bankChargeAmount" />
                                    <input type="hidden" name="bankChargePercent" id="bankChargePercent" value="0" />
                                    
                                </div>
                                <div class="col-3">
                                    <span class="ms-2 text-muted usd-equivalent-field" id="bankChargeAmount_usd"></span>
                                    
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-3"><label>Total Amount </label></div>
                                <div class="col-3"><input required type="text" name="totalAmount" class=""
                                        id="totalAmount" />
                                    
                                </div>
                                <div class="col-3"><span class="ms-2 text-muted usd-equivalent-field" id="totalAmount_usd"></span>
                                    
                                </div>

                            </div>
                            <div class="row mt-2">
                                <div class="col-3"><label>Convert INR to USD</label></div>
                                <div class="col-3">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" id="convertToUsd">
                                        Show USD amount on invoice
                                    </label>
                                </div>
                            </div>
                            <div class="row mt-2" id="currencyConversionRow" style="display: none;">
                                <div class="col-3"><label>Exchange Rate (1 USD = ? INR)</label></div>
                                <div class="col-3">
                                    <input type="number" step="" name="exchange_rate" class="form-control"
                                        id="exchange_rate" value="83.5000" />
                                </div>
                                <div class="col-3"><label>Total Amount (USD)</label></div>
                                <div class="col-3"><input type="text" name="total_amount_foreign" class="form-control"
                                        id="total_amount_foreign" readonly /></div>
                            </div>
                            <input type="hidden" name="currency" id="currency" value="INR">

                        </div>

                        <div class="col-12">
                            <button class='btn btn-primary' type="submit" name="submit">Submit</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>

</div>
<!-- Include jQuery library -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function () {
        function refreshInvoiceNumber() {
            let newInvoice = $('#newInvoice').val();
            let clientId = $('#clientDropdown').val();
            $.ajax({
                type: 'post',
                url: 'invoice-type.php',
                data: {
                    newInvoice: newInvoice,
                    client_id: clientId
                },
                success: function (response) {
                    $('#newInvoiceNumber').val(response);
                },
                error: function (error) {
                    console.error('AJAX request error:', error);
                }
            });
        }

        refreshInvoiceNumber();
        $('#clientDropdown').on('change', refreshInvoiceNumber);

    });
</script>

<script>
    $(document).ready(function () {
        $('#oldInvoiceNumber').change(function () {
            var oldInvoiceNumber = $('#oldInvoiceNumber').val();


            $.ajax({
                type: 'POST',
                url: 'check-invoice.php',
                data: {
                    oldInvoiceNumber: oldInvoiceNumber
                },
                success: function (response) {
                    // Update the message span with the response from the server
                    $('#invoiceNumberMessage').html(response);
                },
                error: function (error) {
                    console.error('AJAX request error:', error);
                }
            });
        });
    });
</script>

<script>
    // Add event listener to handle radio button changes and input value
    document.addEventListener('DOMContentLoaded', function () {
        var newInvoiceRadio = document.getElementById('newInvoice');
        var oldInvoiceRadio = document.getElementById('oldInvoice');
        var oldInvoiceTextbox = document.getElementById('oldInvoiceTextbox');
        var oldInvoiceNumberInput = document.getElementById('oldInvoiceNumber');
        var newInvoiceNumber = document.getElementById('newInvoiceTextbox');

        // Initial state
        oldInvoiceTextbox.style.display = 'none';

        // Event listener for radio buttons
        function handleRadioChange() {
            if (newInvoiceRadio.checked) {
                oldInvoiceTextbox.style.display = 'none';
                newInvoiceNumber.style.display = 'block';
            } else if (oldInvoiceRadio.checked) {
                oldInvoiceTextbox.style.display = 'block';
                newInvoiceNumber.style.display = 'none';

            }
        }

        newInvoiceRadio.addEventListener('change', handleRadioChange);
        oldInvoiceRadio.addEventListener('change', handleRadioChange);

        // Event listener for input value


    });
</script>


<script>
    // Get the current date in the format "YYYY-MM-DD"
    const currentDate = new Date().toISOString().split('T')[0];

    // Set the current date as the default value for date input fields with class 'currentdate'
    document.querySelectorAll('.currentdate').forEach(function (inputField) {
        if (!inputField.value) {
            inputField.value = currentDate;
        }
    });
</script>

<script>
    $(document).ready(function () {
        var selectedCountry = '';
        var selectedState = '';

        function loadClientDetails(clientId) {

            if (!clientId) {
                selectedCountry = '';
                selectedState = '';
                $('#clientDetails').html('');
                updateTaxVisibility();
                updateSubtotal();
                return;
            }

            $.ajax({
                type: 'POST',
                url: 'get_client_details.php',
                dataType: 'json',
                data: {
                    client_id: clientId
                },
                success: function (response) {

                    if (!response || !response.ok) {
                        selectedCountry = '';
                        selectedState = '';
                        $('#clientDetails').html('');
                        updateTaxVisibility();
                        updateSubtotal();
                        return;
                    }

                    selectedCountry = (response.country_name || '').toLowerCase();
                    selectedState = (response.state_name || '').toLowerCase();

                    $('#clientDetails').html(
                        '<small><strong>Country:</strong> ' + (response.country_name || '-') +
                        '<br><strong>State:</strong> ' + (response.state_name || '-') +
                        '</small>'
                    );

                    updateTaxVisibility();
                    updateSubtotal();
                    prefillMilestonesFromGET();
                },
                error: function () {
                    alert('Error fetching client details.');
                }
            });
        }

        // Client Change Event
        $('#clientDropdown').change(function () {
            loadClientDetails($(this).val());
        });

        $('#discriptionSection').on('click', '.addMore', function () {
            var currentRow = $(this).closest('.row');
            var newRow = currentRow.clone(true);

            newRow.find('input').val('');
            newRow.find('.addMore').hide();
            newRow.find('.remove').show();

            currentRow.parent().append(newRow);

            updateSubtotal();
        });

        $('#discriptionSection').on('click', '.remove', function () {
            $(this).closest('.row').remove();
            updateSubtotal();
        });

        // Percentage to Amount Calculation
        $('#discriptionSection').on('input change', '.pct-calc', function () {
            var pct = parseFloat($(this).val()) || 0;
            var projectTotal = parseFloat($('#linked_project_total').val()) || 0;
            if (projectTotal > 0) {
                var calculatedAmt = (projectTotal * pct) / 100;
                $(this).closest('.row').find('.milestone-amt-field').val(calculatedAmt.toFixed(2));
                updateSubtotal();
            }
        });

        // Amount to Percentage Reverse Calculation (Optional)
        $('#discriptionSection').on('input change', '.milestone-amt-field', function () {
            var amt = parseFloat($(this).val()) || 0;
            var projectTotal = parseFloat($('#linked_project_total').val()) || 0;
            if (projectTotal > 0 && amt > 0) {
                var calculatedPct = (amt / projectTotal) * 100;
                $(this).closest('.row').find('.pct-calc').val(calculatedPct.toFixed(2));
            }
            updateSubtotal();
        });

        $('#discriptionSection').on('input', 'input[name="Amount[]"]', function () {
            updateSubtotal();
        });

        $('#discount').on('input', function () {
            updateFinalAmount();
            prefillMilestonesFromGET();
        });

        function updateTaxVisibility() {
            var isInternational = !((selectedCountry === '' || selectedCountry === 'india') && selectedState !== 'internation' && selectedState !== 'international');
            var isIndia = !isInternational;
            var isUttarPradesh = selectedState === 'uttar pradesh';

            $('#sgstAmount, #cgstAmount, #igstAmount, #bankChargeAmount').val('0.00');
            $('#bankChargePercent').val('0');

            if (isInternational) {
                $('#gst').hide();
                $('#igst').hide();
                $('#convertToUsd').prop('checked', true);
                $('#bankChargeRow').show();
            } else if (isUttarPradesh) {
                $('#gst').show();
                $('#igst').hide();
                $('#bankChargeRow').hide();
            } else {
                $('#gst').hide();
                $('#igst').show();
                $('#bankChargeRow').hide();
            }
            updateCurrencyConversion();
        }

        function updateSubtotal() {
            var totalAmount = 0;

            $('#discriptionSection .row').each(function () {
                var amount = parseFloat($(this).find('input[name="Amount[]"]').val()) || 0;
                totalAmount += amount;
            });

            $('#subtotal').val(totalAmount.toFixed(2));
            updateFinalAmount();
        }

        function updateFinalAmount() {
            var subtotal = parseFloat($('#subtotal').val()) || 0;
            var discount = parseFloat($('#discount').val()) || 0;

            var finalAmount = Math.max(0, subtotal - discount);

            $('#finalAmount').val(finalAmount.toFixed(2));

            if ($('#gst').is(':visible')) {

                var sgst = finalAmount * 0.09;
                var cgst = finalAmount * 0.09;

                $('#sgstAmount').val(sgst.toFixed(2));
                $('#cgstAmount').val(cgst.toFixed(2));
                $('#igstAmount').val('0.00');
                $('#bankChargeAmount').val('0.00');
                $('#bankChargePercent').val('0');

                $('#totalAmount').val((finalAmount + sgst + cgst).toFixed(2));

            } else if ($('#igst').is(':visible')) {

                var igst = finalAmount * 0.18;

                $('#sgstAmount').val('0.00');
                $('#cgstAmount').val('0.00');
                $('#igstAmount').val(igst.toFixed(2));
                $('#bankChargeAmount').val('0.00');
                $('#bankChargePercent').val('0');

                $('#totalAmount').val((finalAmount + igst).toFixed(2));

            } else if ($('#bankChargeRow').is(':visible')) {

                var bankCharge = finalAmount * 0.035;

                $('#sgstAmount').val('0.00');
                $('#cgstAmount').val('0.00');
                $('#igstAmount').val('0.00');
                $('#bankChargeAmount').val(bankCharge.toFixed(2));
                $('#bankChargePercent').val('3.5');

                $('#totalAmount').val((finalAmount + bankCharge).toFixed(2));

            } else {

                $('#sgstAmount').val('0.00');
                $('#cgstAmount').val('0.00');
                $('#igstAmount').val('0.00');
                $('#bankChargeAmount').val('0.00');
                $('#bankChargePercent').val('0');

                $('#totalAmount').val(finalAmount.toFixed(2));
            }

            updateCurrencyConversion();
        }

        function updateCurrencyConversion() {
            var shouldConvert = $('#convertToUsd').is(':checked');
            var totalAmount = parseFloat($('#totalAmount').val()) || 0;
            var exchangeRate = parseFloat($('#exchange_rate').val()) || 0;

            $('#currencyConversionRow').toggle(shouldConvert);
            $('#currency').val(shouldConvert ? 'USD' : 'INR');
            updateUsdEquivalentFields(shouldConvert, exchangeRate);

            if (shouldConvert && exchangeRate > 0) {
                $('#total_amount_foreign').val((totalAmount / exchangeRate).toFixed(2));
            } else {
                $('#total_amount_foreign').val('0.00');
            }
        }

        function updateUsdEquivalentFields(shouldConvert, exchangeRate) {
            var fields = ['subtotal', 'discount', 'finalAmount', 'sgstAmount', 'cgstAmount', 'igstAmount', 'bankChargeAmount', 'totalAmount'];

            fields.forEach(function (fieldId) {
                var amount = parseFloat($('#' + fieldId).val()) || 0;
                var usdText = '';
                if (shouldConvert && exchangeRate > 0) {
                    usdText = '($' + (amount / exchangeRate).toFixed(2) + ')';
                }
                $('#' + fieldId + '_usd').text(usdText);
            });
        }

        $('#exchange_rate').on('input', function () {
            updateFinalAmount();
        });
        $('#convertToUsd').on('change', function () {
            updateCurrencyConversion();
        });

        function prefillMilestonesFromGET() {

            var m1 = $('#milestone_1_amount').val();
            var m1due = $('#milestone_1_date').val();

            var total = parseFloat($('#totalAmount').val()) || 0;
            var m1Amt = parseFloat(m1) || 0;

            var remaining = total - m1Amt;

            if (remaining < 0) {
                remaining = 0;
            }

            $('#milestone_2_amount').val(
                remaining.toFixed(2)
            );

            if (m1due) {

                var d = new Date(m1due);
                var d2 = new Date(d);

                d2.setDate(d2.getDate() + 30);

                var formattedDate = d2.toISOString().split('T')[0];

                $('#milestone_2_date').val(formattedDate);
            }
        }

        $('#deliveryDate').on('change', function () {
            prefillMilestonesFromGET();
        });

        // Initial Load
        updateTaxVisibility();
        updateSubtotal();
        prefillMilestonesFromGET();

        // IMPORTANT: Edit page ke liye onload client details fetch
        if ($('#clientDropdown').val() && $('#clientDropdown').val() !== '0') {
            loadClientDetails($('#clientDropdown').val());
        }
    });
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
<?php include("EvaraFroentend/layout/footer.php");
$con->close(); ?>

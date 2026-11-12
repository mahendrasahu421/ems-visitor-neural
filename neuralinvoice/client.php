<?php
include("EvaraFroentend/layout/head.php");
include("EvaraFroentend/layout/header.php");
include_once("includes/auth.php");
include_once("includes/helpers.php");

$edit = null;
$showForm = !empty($_GET['add']);
if (!empty($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $con->prepare("SELECT * FROM clientmaster WHERE ClientID = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit = $result->fetch_assoc();
    $stmt->close();
    if ($edit) {
        $showForm = true;
    }
}

// Fetch countries for the form dropdown
$countries = [];
$countryResult = $con->query("SELECT * FROM countrymaster ORDER BY CountryName");
if ($countryResult) {
    while ($row = $countryResult->fetch_assoc()) {
        $countries[] = $row;
    }
    $countryResult->close();
}

// Fetch client list
$sqlFetchData = "SELECT clientmaster.*, 
                        countrymaster.CountryName,
                        statemaster.StateName
                FROM clientmaster
                LEFT JOIN countrymaster ON clientmaster.Country = countrymaster.CountryID
                LEFT JOIN statemaster ON clientmaster.StateID = statemaster.StateID
                WHERE clientmaster.DelStatus=1
                ORDER BY clientmaster.ClientID DESC";

$resultFetchData = $con->query($sqlFetchData);
if ($resultFetchData === FALSE) {
    die("Error fetching client data: " . $con->error);
}
?>
<style>
    label {
        font-weight: 700;
    }
</style>
<div class="container mt-15">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h3>Client Master</h3>
            <div>
                <?php if ($showForm): ?>
                    <a href="client.php" class="btn btn-outline-secondary btn-sm">Back to List</a>
                <?php else: ?>
                    <a href="client.php?add=1" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add New Client</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <?php if (!empty($_GET['success'])): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
            <?php endif; ?>
            <?php if (!empty($_GET['error'])): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <?php if ($showForm): ?>
                <form action="create-client.php" method="post" id="client-form" class="row g-3 mb-4"
                    enctype="multipart/form-data">
                    <input type="hidden" name="client_id" value="<?php echo (int) ($edit['ClientID'] ?? 0); ?>">
                    <input type="hidden" name="action" value="<?php echo $edit ? 'update' : 'create'; ?>">

                    <div class="col-md-4"><label>Client Name</label><input required type="text" name="client_name"
                            class="form-control" value="<?php echo htmlspecialchars($edit['ClientName'] ?? ''); ?>"></div>
                    <div class="col-md-4"><label>Concern Person</label><input required type="text" name="concern_person"
                            class="form-control" value="<?php echo htmlspecialchars($edit['ConcernOwner'] ?? ''); ?>"></div>
                    <div class="col-md-4"><label>CIN <span class="text-muted">(Optional)</span></label><input type="text"
                            name="cin" class="form-control" value="<?php echo htmlspecialchars($edit['Fax'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4"><label>Website</label><input required type="text" name="website"
                            class="form-control" value="<?php echo htmlspecialchars($edit['website'] ?? ''); ?>"></div>
                    <div class="col-md-4"><label>Email</label><input required type="email" name="email" class="form-control"
                            value="<?php echo htmlspecialchars($edit['EmailAddress'] ?? ''); ?>"></div>
                    <div class="col-md-4"><label>Mobile</label><input required type="tel" name="mobile" class="form-control"
                            value="<?php echo htmlspecialchars($edit['MobileNo'] ?? ''); ?>"></div>
                    <div class="col-md-4">
                        <label>Country</label>
                        <select name="country" id="country" class="form-control" required>
                            <option value="">--Select Country--</option>
                            <?php foreach ($countries as $country): ?>
                                <option value="<?php echo $country['CountryID']; ?>" <?php echo (isset($edit['Country']) && $edit['Country'] == $country['CountryID']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($country['CountryName']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>State</label>
                        <select name="state" id="state" class="form-control" required>
                            <option value="">--Select State--</option>
                        </select>
                    </div>
                    <div class="col-md-4"><label>GST Number <span class="text-muted">(Optional)</span></label><input
                            type="text" name="gst_number" class="form-control"
                            value="<?php echo htmlspecialchars($edit['gst_number'] ?? ''); ?>"></div>
                    <div class="col-md-8"><label>Address</label><textarea required name="address" class="form-control"
                            rows="2"><?php echo htmlspecialchars($edit['Address'] ?? ''); ?></textarea></div>
                    <div class="col-md-4 align-self-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i> <?php echo $edit ? 'Update Client' : 'Save Client'; ?>
                        </button>
                    </div>
                </form>
            <?php endif; ?>

            <table id="example" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th>Client Name</th>
                        <th>Consern Person </th>
                        <th>Email</th>
                        <th>Mobile No</th>
                        <th>Country</th>
                        <th>State</th>
                        <th>Address</th>
                        <th>CIN</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    while ($client = $resultFetchData->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo $client['ClientName']; ?></td>
                            <td><?php echo $client['ConcernOwner']; ?></td>
                            <td><?php echo $client['EmailAddress']; ?></td>
                            <td><?php echo $client['MobileNo']; ?></td>
                            <td><?php echo htmlspecialchars($client['CountryName'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($client['StateName'] ?? '-'); ?></td>
                            <td><?php echo $client['Address']; ?></td>
                            <td><?php echo $client['Fax']; ?></td>
                            <td><?php echo date('d-m-Y', strtotime($client['created_at'])); ?></td>
                            <td>
                                <a href="client.php?edit=<?php echo (int) $client['ClientID']; ?>"
                                    class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit"><i
                                        class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        
        function fetchStates(countryId, selectedStateId) {
            $('#state').html('<option value="">Loading...</option>');
            if (!countryId) {
                $('#state').html('<option value="">--Select Country First--</option>');
                return;
            }
            $.ajax({
                url: 'fetch-states.php',
                type: 'POST',
                data: { country_id: countryId },
                dataType: 'json',
                success: function (states) {
                    $('#state').html('<option value="">--Select State--</option>');
                    if (states.length > 0) {
                        $.each(states, function (index, state) {
                            const selected = (state.StateID == selectedStateId) ? ' selected' : '';
                            $('#state').append('<option value="' + state.StateID + '"' + selected + '>' + state.StateName + '</option>');
                        });
                        if (states.length === 1 && (states[0].StateName || '').toLowerCase() === 'international') {
                            $('#state').val(states[0].StateID);
                        }
                    } else {
                        $('#state').html('<option value="">No states available</option>');
                    }
                },
                error: function () {
                    alert('Error fetching states.');
                    $('#state').html('<option value="">--Error--</option>');
                },
            });
        }

        $('#country').change(function () {
            fetchStates($(this).val(), 0);
        });

        <?php if ($edit && !empty($edit['Country'])): ?>
            fetchStates(<?php echo (int) $edit['Country']; ?>, <?php echo (int) ($edit['StateID'] ?? 0); ?>);
        <?php endif; ?>
    });
</script>
<script>
    $(document).ready(function () {
        $('#example').DataTable({
            dom: 'Bfrtip',
            buttons: ['excelHtml5', 'csvHtml5']
        });
    })
</script>
<?php include("EvaraFroentend/layout/footer.php"); ?>
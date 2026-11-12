<?php
include 'EvaraFroentend/layout/head.php';
include 'includes/auth.php';
include 'EvaraFroentend/layout/header.php';

$countries = $con->query('SELECT CountryID, CountryName FROM countrymaster ORDER BY CountryName');

$edit = null;
if (!empty($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $r = $con->query("SELECT * FROM statemaster WHERE StateID = $id");
    $edit = $r ? $r->fetch_assoc() : null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $con->real_escape_string($_POST['state_name'] ?? '');
    $countryId = (int) ($_POST['country_id'] ?? 0);
    $sid = (int) ($_POST['state_id'] ?? 0);
    if ($name !== '' && $countryId > 0) {
        if ($sid > 0) {
            $con->query("UPDATE statemaster SET StateName='$name', CountryID=$countryId WHERE StateID=$sid");
        } else {
            $con->query("INSERT INTO statemaster (StateName, CountryID) VALUES ('$name', $countryId)");
        }
        header('Location: state-master.php?saved=1');
        exit;
    }
}

$list = $con->query("SELECT s.*, c.CountryName FROM statemaster s
    LEFT JOIN countrymaster c ON s.CountryID = c.CountryID
    ORDER BY c.CountryName, s.StateName");
?>
<div class="container mt-15">
    <div class="card">
        <div class="card-header">
            <h3>State Master</h3>
        </div>
        <div class="card-body">
            <?php if (!empty($_GET['saved'])): ?>
                <div class="alert alert-success">Saved.</div><?php endif; ?>
            <form method="post" class="row g-3 mb-4">
                <input type="hidden" name="state_id" value="<?php echo (int) ($edit['StateID'] ?? 0); ?>">
                <div class="col-md-4">
                    <label>Country</label>
                    <select name="country_id" class="form-control" required>
                        <option value="">-- Select --</option>
                        <?php
                        $countries->data_seek(0);
                        while ($c = $countries->fetch_assoc()):
                            $sel = isset($edit['CountryID']) && (int) $edit['CountryID'] === (int) $c['CountryID'] ? 'selected' : '';
                            ?>
                            <option value="<?php echo (int) $c['CountryID']; ?>" <?php echo $sel; ?>>
                                <?php echo htmlspecialchars($c['CountryName']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>State Name</label>
                    <input type="text" name="state_name" class="form-control" required
                        value="<?php echo htmlspecialchars($edit['StateName'] ?? ''); ?>">
                </div>
                <div class="col-md-4 align-self-end">
                    <button type="submit" class="btn btn-primary">
                        <?php if ($edit): ?>
                            <i class="fas fa-save"></i> Update
                        <?php else: ?>
                            <i class="fas fa-plus"></i> Add
                        <?php endif; ?>
                    </button>
                    <?php if ($edit): ?><a href="state-master.php" class="btn btn-secondary"><i
                                class="fas fa-times"></i> Cancel</a><?php endif; ?>
                </div>
            </form>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Country</th>
                        <th>State</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    while ($row = $list->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($row['CountryName']); ?></td>
                            <td><?php echo htmlspecialchars($row['StateName']); ?></td>
                            <td>
                                <a href="state-master.php?edit=<?php echo (int) $row['StateID']; ?>"
                                    class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="Edit"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'EvaraFroentend/layout/footer.php'; ?>
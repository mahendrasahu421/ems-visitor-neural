<?php
include 'EvaraFroentend/layout/head.php';
include 'includes/auth.php';
include 'EvaraFroentend/layout/header.php';

$edit = null;
if (!empty($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $r = $con->query("SELECT * FROM countrymaster WHERE CountryID = $id");
    $edit = $r ? $r->fetch_assoc() : null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $con->real_escape_string($_POST['country_name'] ?? '');
    $cid = (int) ($_POST['country_id'] ?? 0);
    if ($name !== '') {
        if ($cid > 0) {
            $con->query("UPDATE countrymaster SET CountryName = '$name' WHERE CountryID = $cid");
        } else {
            $con->query("INSERT INTO countrymaster (CountryName) VALUES ('$name')");
        }
        header('Location: country-master.php?saved=1');
        exit;
    }
}

$list = $con->query('SELECT * FROM countrymaster ORDER BY CountryName');
?>
<div class="container mt-15">
    <div class="card">
        <div class="card-header"><h3>Country Master</h3></div>
        <div class="card-body">
            <?php if (!empty($_GET['saved'])): ?><div class="alert alert-success">Saved.</div><?php endif; ?>
            <form method="post" class="row g-3 mb-4">
                <input type="hidden" name="country_id" value="<?php echo (int) ($edit['CountryID'] ?? 0); ?>">
                <div class="col-md-6">
                    <label>Country Name</label>
                    <input type="text" name="country_name" class="form-control" required
                        value="<?php echo htmlspecialchars($edit['CountryName'] ?? ''); ?>">
                </div>
                <div class="col-md-6 align-self-end">
                    <button type="submit" class="btn btn-primary">
                        <?php if ($edit): ?>
                            <i class="fas fa-save"></i> Update
                        <?php else: ?>
                            <i class="fas fa-plus"></i> Add
                        <?php endif; ?>
                    </button>
                    <?php if ($edit): ?><a href="country-master.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a><?php endif; ?>
                </div>
            </form>
            <table class="table table-bordered">
                <thead><tr><th>#</th><th>Country</th><th>Action</th></tr></thead>
                <tbody>
                <?php $i = 1; while ($row = $list->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($row['CountryName']); ?></td>
                        <td>
                            <a href="country-master.php?edit=<?php echo (int) $row['CountryID']; ?>" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'EvaraFroentend/layout/footer.php'; ?>

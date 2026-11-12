<?php
include 'EvaraFroentend/layout/head.php';
include 'includes/auth.php';
include 'includes/helpers.php';
include 'EvaraFroentend/layout/header.php';

$edit = null;
if (!empty($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $r = $con->query("SELECT * FROM work_type_master WHERE WorkTypeID = $id");
    $edit = $r ? $r->fetch_assoc() : null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['work_type_name'] ?? '');
    $sort = (int) ($_POST['sort_order'] ?? 0);
    $amcType = ($_POST['amc_type'] ?? '') === 'amc_only' ? 'amc_only' : 'project';
    $tid = (int) ($_POST['work_type_id'] ?? 0);
    if ($name !== '') {
        $esc = $con->real_escape_string($name);
        $hasCol = ni_work_type_has_amc_column($con);
        if ($tid > 0) {
            if ($hasCol) {
                $con->query("UPDATE work_type_master SET WorkTypeName='$esc', amc_type='$amcType', sort_order=$sort WHERE WorkTypeID=$tid");
            } else {
                $con->query("UPDATE work_type_master SET WorkTypeName='$esc', sort_order=$sort WHERE WorkTypeID=$tid");
            }
        } else {
            if ($hasCol) {
                $con->query("INSERT INTO work_type_master (WorkTypeName, amc_type, sort_order) VALUES ('$esc', '$amcType', $sort)");
            } else {
                $con->query("INSERT INTO work_type_master (WorkTypeName, sort_order) VALUES ('$esc', $sort)");
            }
        }
        header('Location: work-type-master.php?saved=1');
        exit;
    }
}

if (!empty($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $con->query("UPDATE work_type_master SET DelStatus = 0 WHERE WorkTypeID = $id");
    header('Location: work-type-master.php?saved=1');
    exit;
}

$list = $con->query('SELECT * FROM work_type_master WHERE DelStatus = 1 ORDER BY sort_order, WorkTypeName');
$tableMissing = !$list;
$editAmc = ($edit['amc_type'] ?? 'project') === 'amc_only' ? 'amc_only' : 'project';
?>
<div class="container mt-15">
    <div class="card">
        <div class="card-header">
            <h3>Work Type Master</h3>
            <small class="text-muted">AMC Type yahan set karo — project form par work type select karte hi form change hoga</small>
        </div>
        <div class="card-body">
            <?php if (!empty($_GET['saved'])): ?><div class="alert alert-success">Saved.</div><?php endif; ?>
            <?php if ($tableMissing): ?>
                <div class="alert alert-warning">Table missing. Refresh page or run <a href="run-schema-upgrade.php">DB Upgrade</a>.</div>
            <?php else: ?>
            <form method="post" class="row g-3 mb-4">
                <input type="hidden" name="work_type_id" value="<?php echo (int) ($edit['WorkTypeID'] ?? 0); ?>">
                <div class="col-md-4">
                    <label>Work Type Name</label>
                    <input type="text" name="work_type_name" class="form-control" required
                        placeholder="e.g. Web Application, AMC Support"
                        value="<?php echo htmlspecialchars($edit['WorkTypeName'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label>AMC Type <span class="text-danger">*</span></label>
                    <select name="amc_type" class="form-control" required>
                        <option value="project" <?php echo $editAmc === 'project' ? 'selected' : ''; ?>>
                            Project Work (development + payment milestones)
                        </option>
                        <option value="amc_only" <?php echo $editAmc === 'amc_only' ? 'selected' : ''; ?>>
                            AMC Only (sirf maintenance — project hamare paas nahi)
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Sort Order</label>
                    <input type="number" name="sort_order" class="form-control"
                        value="<?php echo (int) ($edit['sort_order'] ?? 0); ?>">
                </div>
                <div class="col-md-2 align-self-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <?php if ($edit): ?>
                            <i class="fas fa-save"></i> Update
                        <?php else: ?>
                            <i class="fas fa-plus"></i> Add
                        <?php endif; ?>
                    </button>
                </div>
                <?php if ($edit): ?><div class="col-12"><a href="work-type-master.php" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i> Cancel</a></div><?php endif; ?>
            </form>
            <table class="table table-bordered">
                <thead><tr><th>#</th><th>Work Type</th><th>AMC Type</th><th>Sort</th><th>Action</th></tr></thead>
                <tbody>
                <?php $i = 1; while ($row = $list->fetch_assoc()):
                    $at = ($row['amc_type'] ?? 'project') === 'amc_only' ? 'amc_only' : 'project';
                ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($row['WorkTypeName']); ?></td>
                        <td>
                            <?php if ($at === 'amc_only'): ?>
                                <span class="badge bg-info">AMC Only</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Project Work</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo (int) $row['sort_order']; ?></td>
                        <td>
                            <a href="work-type-master.php?edit=<?php echo (int) $row['WorkTypeID']; ?>" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="work-type-master.php?delete=<?php echo (int) $row['WorkTypeID']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deactivate?');" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"><i class="fas fa-trash-alt"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include 'EvaraFroentend/layout/footer.php'; ?>

<?php
/**
 * One-time DB upgrade. Open in browser once: http://localhost/neuralinvoice/run-schema-upgrade.php
 * Delete or protect this file after successful run.
 */
include 'connection.php';

$messages = [];
$sqlFile = __DIR__ . '/database/schema_upgrade.sql';
if (!file_exists($sqlFile)) {
    die('schema_upgrade.sql not found');
}

$raw = file_get_contents($sqlFile);
$statements = array_filter(array_map('trim', preg_split('/;\s*\n/', $raw)));

foreach ($statements as $stmt) {
    if ($stmt === '' || strpos($stmt, '--') === 0) {
        continue;
    }
    if ($con->query($stmt)) {
        $messages[] = 'OK: ' . substr(str_replace(["\r", "\n"], ' ', $stmt), 0, 80) . '...';
    } else {
        if (strpos($con->error, 'Duplicate column') !== false || strpos($con->error, 'already exists') !== false) {
            $messages[] = 'SKIP (exists): ' . substr($stmt, 0, 60);
        } else {
            $messages[] = 'ERR: ' . $con->error . ' | ' . substr($stmt, 0, 60);
        }
    }
}

// MySQL < 8 may not support IF NOT EXISTS on ADD COLUMN — fallback checks
$cols = ['client_logo'];
foreach ($cols as $col) {
    $r = $con->query("SHOW COLUMNS FROM clientmaster LIKE '$col'");
    if ($r && $r->num_rows === 0) {
        $con->query("ALTER TABLE clientmaster ADD COLUMN client_logo VARCHAR(255) NULL AFTER website");
        $messages[] = "Added column clientmaster.$col";
    }
}
$payCols = [
    'tds_amount' => 'DECIMAL(15,2) NOT NULL DEFAULT 0', 
    'received_net' => 'DECIMAL(15,2) NOT NULL DEFAULT 0',
    'payment_note' => 'VARCHAR(255) NULL',
    'received_date' => 'DATE NULL',
];
foreach ($payCols as $col => $def) {
    $r = $con->query("SHOW COLUMNS FROM recived_payment LIKE '$col'");
    if ($r && $r->num_rows === 0) {
        $con->query("ALTER TABLE recived_payment ADD COLUMN $col $def");
        $messages[] = "Added column recived_payment.$col";
    }
}

// Data cleanup: Backfill received_net for legacy records where it was never set
if ($con->query("UPDATE recived_payment SET received_net = recived_amount WHERE (received_net IS NULL OR received_net = 0) AND recived_amount > 0")) {
    $messages[] = 'Cleaned up received_payment: Backfilled received_net for legacy records';
}

$projectCols = [
    'engagement_type' => "ENUM('project','amc_only') NOT NULL DEFAULT 'project' AFTER project_name",
    'financial_year' => "VARCHAR(12) NULL AFTER amc_end",
    'amc_working_days' => "INT NULL DEFAULT 0 AFTER financial_year",
    'work_type' => "VARCHAR(100) NULL AFTER amc_working_days",
];
foreach ($projectCols as $col => $def) {
    $r = $con->query("SHOW COLUMNS FROM client_projects LIKE '$col'");
    if ($r && $r->num_rows === 0) {
        $con->query("ALTER TABLE client_projects ADD COLUMN $col $def");
        $messages[] = "Added column client_projects.$col";
    }
}

$rInv = $con->query("SHOW COLUMNS FROM project_payment_milestones LIKE 'invoiced_amount'");
if ($rInv && $rInv->num_rows === 0) {
    $con->query("ALTER TABLE project_payment_milestones ADD COLUMN invoiced_amount DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER amount_with_tax");
    $messages[] = 'Added project_payment_milestones.invoiced_amount';
}

$r = $con->query("SHOW COLUMNS FROM client_projects LIKE 'work_type_id'");
if ($r && $r->num_rows === 0) {
    $con->query('ALTER TABLE client_projects ADD COLUMN work_type_id INT NULL AFTER work_type');
    $messages[] = 'Added column client_projects.work_type_id';
}

$rAmc = $con->query("SHOW COLUMNS FROM work_type_master LIKE 'amc_type'");
if ($rAmc && $rAmc->num_rows === 0) {
    $con->query("ALTER TABLE work_type_master ADD COLUMN amc_type ENUM('project','amc_only') NOT NULL DEFAULT 'project' AFTER WorkTypeName");
    $messages[] = 'Added work_type_master.amc_type';
}
$con->query("UPDATE work_type_master SET amc_type = 'amc_only' WHERE WorkTypeName LIKE '%AMC%'");

$con->query("CREATE TABLE IF NOT EXISTS work_type_master (
  WorkTypeID INT AUTO_INCREMENT PRIMARY KEY,
  WorkTypeName VARCHAR(100) NOT NULL,
  DelStatus TINYINT NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_work_type_name (WorkTypeName)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$messages[] = 'Ensured work_type_master table';

$defaults = ['Mobile App', 'Web Application', 'Website', 'Digital Marketing', 'ERP / Software', 'AMC Support'];
$sort = 0;
foreach ($defaults as $name) {
    $esc = $con->real_escape_string($name);
    $chk = $con->query("SELECT WorkTypeID FROM work_type_master WHERE WorkTypeName = '$esc' LIMIT 1");
    if ($chk && $chk->num_rows === 0) {
        $con->query("INSERT INTO work_type_master (WorkTypeName, sort_order) VALUES ('$esc', $sort)");
        $messages[] = "Seeded work type: $name";
    }
    $sort++;
}

header('Content-Type: text/html; charset=utf-8');
echo '<h2>Schema upgrade</h2><ul>';
foreach ($messages as $m) {
    echo '<li>' . htmlspecialchars($m) . '</li>';
}
echo '</ul><p><a href="what-you-want.php">Dashboard</a></p>';

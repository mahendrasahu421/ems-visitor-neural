<?php
include("includes/auth.php");
include("EvaraFroentend/layout/head.php");
include("EvaraFroentend/layout/header.php");

function ni_table_exists(mysqli $con, string $table): bool
{
    $tableEsc = $con->real_escape_string($table);
    $res = $con->query("SHOW TABLES LIKE '$tableEsc'");
    return $res && $res->num_rows > 0;
}

function ni_scalar(mysqli $con, string $sql, $default = 0)
{
    $res = $con->query($sql);
    if (!$res) {
        return $default;
    }
    $row = $res->fetch_row();
    return $row ? ($row[0] ?? $default) : $default;
}

function ni_count_rows(mysqli $con, string $table, string $where = '1=1'): int
{
    if (!ni_table_exists($con, $table)) {
        return 0;
    }
    return (int) ni_scalar($con, "SELECT COUNT(*) FROM $table WHERE $where", 0);
}

function ni_sum_rows(mysqli $con, string $table, string $column, string $where = '1=1'): float
{
    if (!ni_table_exists($con, $table)) {
        return 0.0;
    }
    return (float) ni_scalar($con, "SELECT COALESCE(SUM($column), 0) FROM $table WHERE $where", 0);
}

function ni_money($amount): string
{
    return number_format((float) $amount, 2);
}

function ni_current_fy_start_year(): int
{
    $year = (int) date('Y');
    return (int) date('n') >= 4 ? $year : $year - 1;
}

function ni_fy_label(int $startYear): string
{
    return $startYear . '-' . substr((string) ($startYear + 1), -2);
}

function ni_valid_date($date): bool
{
    return is_string($date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) === 1;
}

function ni_sum_between(mysqli $con, string $table, string $amountColumn, string $dateColumn, string $from, string $to, string $where = '1=1'): float
{
    if (!ni_table_exists($con, $table)) {
        return 0.0;
    }
    $fromEsc = $con->real_escape_string($from);
    $toEsc = $con->real_escape_string($to);
    return (float) ni_scalar(
        $con,
        "SELECT COALESCE(SUM($amountColumn), 0)
         FROM $table
         WHERE $where AND DATE($dateColumn) BETWEEN '$fromEsc' AND '$toEsc'",
        0
    );
}

function ni_dashboard_buckets(string $period, int $fyStartYear, string $fromDate = '', string $toDate = ''): array
{
    if ($period === 'yearly') {
        $buckets = [];
        for ($year = $fyStartYear - 4; $year <= $fyStartYear; $year++) {
            $buckets[] = [
                'label' => 'FY ' . ni_fy_label($year),
                'from' => sprintf('%04d-04-01', $year),
                'to' => sprintf('%04d-03-31', $year + 1),
            ];
        }
        return $buckets;
    }

    if (ni_valid_date($fromDate) && ni_valid_date($toDate)) {
        return [[
            'label' => date('d M Y', strtotime($fromDate)) . ' - ' . date('d M Y', strtotime($toDate)),
            'from' => $fromDate,
            'to' => $toDate,
        ]];
    }

    if ($period === 'half_yearly') {
        return [
            ['label' => 'H1 Apr-Sep', 'from' => sprintf('%04d-04-01', $fyStartYear), 'to' => sprintf('%04d-09-30', $fyStartYear)],
            ['label' => 'H2 Oct-Mar', 'from' => sprintf('%04d-10-01', $fyStartYear), 'to' => sprintf('%04d-03-31', $fyStartYear + 1)],
        ];
    }

    return [
        ['label' => 'Q1 Apr-Jun', 'from' => sprintf('%04d-04-01', $fyStartYear), 'to' => sprintf('%04d-06-30', $fyStartYear)],
        ['label' => 'Q2 Jul-Sep', 'from' => sprintf('%04d-07-01', $fyStartYear), 'to' => sprintf('%04d-09-30', $fyStartYear)],
        ['label' => 'Q3 Oct-Dec', 'from' => sprintf('%04d-10-01', $fyStartYear), 'to' => sprintf('%04d-12-31', $fyStartYear)],
        ['label' => 'Q4 Jan-Mar', 'from' => sprintf('%04d-01-01', $fyStartYear + 1), 'to' => sprintf('%04d-03-31', $fyStartYear + 1)],
    ];
}

$today = date('Y-m-d');
$monthStart = date('Y-m-01');
$allowedPeriods = ['quarterly', 'half_yearly', 'yearly'];
$period = in_array(($_GET['period'] ?? ''), $allowedPeriods, true) ? $_GET['period'] : 'quarterly';
$selectedFyStart = isset($_GET['fy']) ? (int) $_GET['fy'] : ni_current_fy_start_year();
$fromDate = ni_valid_date($_GET['from_date'] ?? '') ? $_GET['from_date'] : '';
$toDate = ni_valid_date($_GET['to_date'] ?? '') ? $_GET['to_date'] : '';
if ($fromDate !== '' && $toDate !== '' && strtotime($fromDate) > strtotime($toDate)) {
    [$fromDate, $toDate] = [$toDate, $fromDate];
}

$buckets = ni_dashboard_buckets($period, $selectedFyStart, $fromDate, $toDate);
$rangeStart = $buckets[0]['from'];
$rangeEnd = $buckets[count($buckets) - 1]['to'];
$rangeLabel = date('d M Y', strtotime($rangeStart)) . ' - ' . date('d M Y', strtotime($rangeEnd));

$chartLabels = [];
$turnoverData = [];
$receivedData = [];
$expenseData = [];
$profitData = [];
foreach ($buckets as $bucket) {
    $turnover = ni_sum_between($con, 'invoice', 'totalAmount', 'invoiceDate', $bucket['from'], $bucket['to'], 'status <> 3');
    $received = ni_sum_between($con, 'recived_payment', 'recived_amount', 'received_date', $bucket['from'], $bucket['to'], 'status = 1');
    $expense = ni_sum_between($con, 'expenses', 'expense_cost', 'expense_date', $bucket['from'], $bucket['to']);
    $chartLabels[] = $bucket['label'];
    $turnoverData[] = round($turnover, 2);
    $receivedData[] = round($received, 2);
    $expenseData[] = round($expense, 2);
    $profitData[] = round($received - $expense, 2);
}

$filteredTurnover = array_sum($turnoverData);
$filteredReceived = array_sum($receivedData);
$filteredExpenses = array_sum($expenseData);
$filteredProfit = $filteredReceived - $filteredExpenses;

$comparisonLabels = [];
$comparisonTurnover = [];
$comparisonExpense = [];
$comparisonProfit = [];
for ($year = $selectedFyStart - 4; $year <= $selectedFyStart; $year++) {
    $start = sprintf('%04d-04-01', $year);
    $end = sprintf('%04d-03-31', $year + 1);
    $turnover = ni_sum_between($con, 'invoice', 'totalAmount', 'invoiceDate', $start, $end, 'status <> 3');
    $received = ni_sum_between($con, 'recived_payment', 'recived_amount', 'received_date', $start, $end, 'status = 1');
    $expense = ni_sum_between($con, 'expenses', 'expense_cost', 'expense_date', $start, $end);
    $comparisonLabels[] = 'FY ' . ni_fy_label($year);
    $comparisonTurnover[] = round($turnover, 2);
    $comparisonExpense[] = round($expense, 2);
    $comparisonProfit[] = round($received - $expense, 2);
}

$chartPayload = [
    'labels' => $chartLabels,
    'turnover' => $turnoverData,
    'received' => $receivedData,
    'expenses' => $expenseData,
    'profit' => $profitData,
    'comparisonLabels' => $comparisonLabels,
    'comparisonTurnover' => $comparisonTurnover,
    'comparisonExpense' => $comparisonExpense,
    'comparisonProfit' => $comparisonProfit,
];

$stats = [
    [
        'label' => 'Companies',
        'value' => ni_count_rows($con, 'companymaster', 'DelStatus = 1'),
        'hint' => 'Active company masters',
        'link' => 'company-master.php',
        'tone' => 'teal',
    ],
    [
        'label' => 'Clients',
        'value' => ni_count_rows($con, 'clientmaster', 'DelStatus = 1'),
        'hint' => 'Active clients',
        'link' => 'client.php',
        'tone' => 'blue',
    ],
    [
        'label' => 'Projects / AMC',
        'value' => ni_count_rows($con, 'client_projects', 'status = 1'),
        'hint' => 'Running client projects',
        'link' => 'client-project-list.php',
        'tone' => 'violet',
    ],
    [
        'label' => 'Proforma Invoices',
        'value' => ni_count_rows($con, 'invoice', 'status <> 3'),
        'hint' => 'Generated and sent',
        'link' => 'client-invoice-list.php',
        'tone' => 'amber',
    ],
    [
        'label' => 'Tax Invoices',
        'value' => ni_count_rows($con, 'recived_payment', 'status = 1'),
        'hint' => 'Receipt/tax invoice rows',
        'link' => 'recived-payment-form-clinet.php',
        'tone' => 'green',
    ],
    [
        'label' => 'Filtered Expenses',
        'value' => 'Rs. ' . ni_money($filteredExpenses),
        'hint' => $rangeLabel,
        'link' => 'add-expencese-list.php',
        'tone' => 'red',
    ],
];

$moneyStats = [
    [
        'label' => 'Turnover',
        'value' => 'Rs. ' . ni_money($filteredTurnover),
        'hint' => 'Invoice value for ' . $rangeLabel,
    ],
    [
        'label' => 'Received Amount',
        'value' => 'Rs. ' . ni_money($filteredReceived),
        'hint' => 'Payment receipts for selected period',
    ],
    [
        'label' => $filteredProfit >= 0 ? 'Profit' : 'Loss',
        'value' => 'Rs. ' . ni_money(abs($filteredProfit)),
        'hint' => 'Received minus expenses',
    ],
];

$dueInvoices = [];
if (ni_table_exists($con, 'invoice')) {
    $dueSql = "
        SELECT i.id, i.duedate, i.totalAmount, cm.ClientName
        FROM invoice i
        LEFT JOIN clientmaster cm ON i.ClientID = cm.ClientID
        WHERE i.status <> 3
          AND i.duedate IS NOT NULL
          AND i.duedate <= '$today'
        ORDER BY i.duedate ASC
        LIMIT 5
    ";
    $dueRes = $con->query($dueSql);
    if ($dueRes) {
        while ($row = $dueRes->fetch_assoc()) {
            $dueInvoices[] = $row;
        }
    }
}

$recentInvoices = [];
if (ni_table_exists($con, 'invoice')) {
    $recentSql = "
        SELECT i.id, i.invoiceDate, i.totalAmount, i.status, cm.ClientName
        FROM invoice i
        LEFT JOIN clientmaster cm ON i.ClientID = cm.ClientID
        ORDER BY i.id DESC
        LIMIT 6
    ";
    $recentRes = $con->query($recentSql);
    if ($recentRes) {
        while ($row = $recentRes->fetch_assoc()) {
            $recentInvoices[] = $row;
        }
    }
}

$quickActions = [
    ['label' => 'Create Proforma', 'link' => 'create-newInvoice.php', 'icon' => 'PI'],
    ['label' => 'Add Client', 'link' => 'create-new-client.php', 'icon' => 'CL'],
    ['label' => 'Add Project / AMC', 'link' => 'create-client-project.php', 'icon' => 'PR'],
    ['label' => 'Payment Receipt', 'link' => 'update-payment.php', 'icon' => 'PY'],
    ['label' => 'Tax Invoice', 'link' => 'recived-payment-form-clinet.php', 'icon' => 'TI'],
    ['label' => 'Day Book', 'link' => 'day-book.php', 'icon' => 'DB'],
];
?>
<style>
    body { background: #f3f6f8; }
    .dashboard-shell { padding: 28px 0 48px; }
    .dashboard-title { display: flex; align-items: end; justify-content: space-between; gap: 16px; margin-bottom: 20px; }
    .dashboard-title h1 { margin: 0; font-size: 28px; font-weight: 700; color: #15252b; }
    .dashboard-title p { margin: 6px 0 0; color: #66767c; }
    .status-pill { background: #ffffff; border: 1px solid #dce5e8; border-radius: 999px; padding: 8px 14px; color: #405158; font-size: 13px; white-space: nowrap; }
    .filter-panel { margin-bottom: 18px; padding: 16px 18px; }
    .filter-grid { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap: 12px; align-items: end; }
    .filter-grid label { display: block; font-size: 12px; color: #607178; margin-bottom: 5px; font-weight: 700; }
    .filter-grid .form-control { height: 42px; border-radius: 8px; border: 1px solid #dbe5e8; font-size: 13px; }
    .filter-actions { display: flex; gap: 8px; }
    .chart-grid { display: grid; grid-template-columns: 1.35fr 0.65fr; gap: 18px; margin-bottom: 18px; }
    .chart-panel .panel-body { min-height: 310px; position: relative; }
    .chart-panel canvas { width: 100% !important; height: 280px !important; }
    .chart-note { color: #78888e; font-size: 12px; }
    .stat-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; margin-bottom: 18px; }
    .stat-card, .panel { background: #ffffff; border: 1px solid #e0e8eb; border-radius: 8px; box-shadow: 0 8px 22px rgba(24, 42, 48, 0.06); }
    .stat-card { display: block; padding: 18px; text-decoration: none; color: inherit; border-top: 4px solid #188596; }
    .stat-card.blue { border-top-color: #386fa4; }
    .stat-card.violet { border-top-color: #6f5bb7; }
    .stat-card.amber { border-top-color: #d79424; }
    .stat-card.green { border-top-color: #2f8f5b; }
    .stat-card.red { border-top-color: #c9564a; }
    .stat-label { color: #607178; font-size: 13px; margin-bottom: 8px; }
    .stat-value { color: #14262d; font-size: 28px; font-weight: 750; line-height: 1.1; }
    .stat-hint { color: #7c8c92; font-size: 12px; margin-top: 8px; }
    .money-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; margin-bottom: 18px; }
    .money-card { padding: 16px 18px; }
    .money-card b { display: block; font-size: 22px; color: #14262d; margin: 4px 0; }
    .content-grid { display: grid; grid-template-columns: 1.25fr 0.75fr; gap: 18px; }
    .panel-header { padding: 16px 18px; border-bottom: 1px solid #edf2f4; display: flex; justify-content: space-between; align-items: center; }
    .panel-header h2 { margin: 0; font-size: 17px; color: #1c3037; }
    .panel-body { padding: 14px 18px 18px; }
    .quick-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
    .quick-action { display: flex; align-items: center; gap: 10px; padding: 12px; border: 1px solid #e0e8eb; border-radius: 8px; color: #24383f; text-decoration: none; background: #f9fbfc; }
    .quick-icon { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; background: #188596; color: #fff; border-radius: 8px; font-weight: 700; font-size: 12px; }
    .dashboard-table { width: 100%; border-collapse: collapse; }
    .dashboard-table th, .dashboard-table td { padding: 10px 8px; border-bottom: 1px solid #edf2f4; font-size: 13px; text-align: left; }
    .dashboard-table th { color: #607178; font-weight: 700; }
    .empty-state { color: #718187; font-size: 13px; padding: 10px 0; }
    @media (max-width: 900px) {
        .filter-grid, .stat-grid, .money-grid, .chart-grid, .content-grid { grid-template-columns: 1fr; }
        .dashboard-title { align-items: start; flex-direction: column; }
        .filter-actions { flex-direction: column; }
    }
</style>

<div class="container dashboard-shell">
    <div class="dashboard-title">
        <div>
            <h1>Dashboard</h1>
            <p>Welcome, <?php echo htmlspecialchars((string) ($_SESSION['username'] ?? 'User'), ENT_QUOTES, 'UTF-8'); ?>. Here is today's live business summary.</p>
        </div>
        <div class="status-pill">Updated: <?php echo date('d M Y, h:i A'); ?></div>
    </div>

    <form method="get" class="panel filter-panel">
        <div class="filter-grid">
            <div>
                <label for="period">Period</label>
                <select name="period" id="period" class="form-control">
                    <option value="quarterly" <?php echo $period === 'quarterly' ? 'selected' : ''; ?>>Quarterly</option>
                    <option value="half_yearly" <?php echo $period === 'half_yearly' ? 'selected' : ''; ?>>Half Yearly</option>
                    <option value="yearly" <?php echo $period === 'yearly' ? 'selected' : ''; ?>>Yearly Comparison</option>
                </select>
            </div>
            <div>
                <label for="fy">Financial Year</label>
                <select name="fy" id="fy" class="form-control">
                    <?php for ($fy = ni_current_fy_start_year() + 1; $fy >= ni_current_fy_start_year() - 8; $fy--): ?>
                        <option value="<?php echo $fy; ?>" <?php echo $selectedFyStart === $fy ? 'selected' : ''; ?>>
                            FY <?php echo htmlspecialchars(ni_fy_label($fy), ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <label for="from_date">From Date</label>
                <input type="date" name="from_date" id="from_date" class="form-control"
                    value="<?php echo htmlspecialchars($fromDate, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div>
                <label for="to_date">To Date</label>
                <input type="date" name="to_date" id="to_date" class="form-control"
                    value="<?php echo htmlspecialchars($toDate, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Apply</button>
                <a href="dashboard.php" class="btn btn-outline-secondary">Reset</a>
            </div>
        </div>
        <div class="chart-note mt-2">
            Custom date lagane par selected date range ka combined graph dikhega. Yearly mode hamesha selected FY tak last 5 FY compare karta hai.
        </div>
    </form>

    <div class="stat-grid">
        <?php foreach ($stats as $stat): ?>
            <a class="stat-card <?php echo htmlspecialchars($stat['tone'], ENT_QUOTES, 'UTF-8'); ?>" href="<?php echo htmlspecialchars($stat['link'], ENT_QUOTES, 'UTF-8'); ?>">
                <div class="stat-label"><?php echo htmlspecialchars($stat['label'], ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="stat-value"><?php echo htmlspecialchars((string) $stat['value'], ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="stat-hint"><?php echo htmlspecialchars($stat['hint'], ENT_QUOTES, 'UTF-8'); ?></div>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="money-grid">
        <?php foreach ($moneyStats as $stat): ?>
            <div class="panel money-card">
                <span class="stat-label"><?php echo htmlspecialchars($stat['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                <b><?php echo htmlspecialchars($stat['value'], ENT_QUOTES, 'UTF-8'); ?></b>
                <span class="stat-hint"><?php echo htmlspecialchars($stat['hint'], ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="chart-grid">
        <div class="panel chart-panel">
            <div class="panel-header">
                <div>
                    <h2>Turnover Trend</h2>
                    <span class="chart-note"><?php echo htmlspecialchars($rangeLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            </div>
            <div class="panel-body">
                <canvas id="turnoverChart"></canvas>
            </div>
        </div>

        <div class="panel chart-panel">
            <div class="panel-header">
                <div>
                    <h2>Profit / Loss</h2>
                    <span class="chart-note">Received minus expenses</span>
                </div>
            </div>
            <div class="panel-body">
                <canvas id="profitChart"></canvas>
            </div>
        </div>
    </div>

    <div class="chart-grid">
        <div class="panel chart-panel">
            <div class="panel-header">
                <div>
                    <h2>Year Wise Comparison</h2>
                    <span class="chart-note">Turnover, expenses and profit for last 5 financial years</span>
                </div>
            </div>
            <div class="panel-body">
                <canvas id="yearComparisonChart"></canvas>
            </div>
        </div>

        <div class="panel chart-panel">
            <div class="panel-header">
                <div>
                    <h2>Received vs Expenses</h2>
                    <span class="chart-note">Selected period split</span>
                </div>
            </div>
            <div class="panel-body">
                <canvas id="expenseChart"></canvas>
            </div>
        </div>
    </div>

    <div class="content-grid">
        <div class="panel">
            <div class="panel-header">
                <h2>Recent Proforma Invoices</h2>
                <a href="client-invoice-list.php">View all</a>
            </div>
            <div class="panel-body">
                <?php if (empty($recentInvoices)): ?>
                    <div class="empty-state">No invoices found.</div>
                <?php else: ?>
                    <table class="dashboard-table">
                        <thead><tr><th>#</th><th>Client</th><th>Date</th><th>Amount</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php foreach ($recentInvoices as $invoice): ?>
                            <tr>
                                <td><?php echo (int) $invoice['id']; ?></td>
                                <td><?php echo htmlspecialchars($invoice['ClientName'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo !empty($invoice['invoiceDate']) ? date('d-m-Y', strtotime($invoice['invoiceDate'])) : '-'; ?></td>
                                <td>Rs. <?php echo ni_money($invoice['totalAmount'] ?? 0); ?></td>
                                <td><?php echo ((int) ($invoice['status'] ?? 0)) === 1 ? 'Sent' : (((int) ($invoice['status'] ?? 0)) === 3 ? 'Cancelled' : 'Generated'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <h2>Quick Actions</h2>
            </div>
            <div class="panel-body">
                <div class="quick-grid">
                    <?php foreach ($quickActions as $action): ?>
                        <a class="quick-action" href="<?php echo htmlspecialchars($action['link'], ENT_QUOTES, 'UTF-8'); ?>">
                            <span class="quick-icon"><?php echo htmlspecialchars($action['icon'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span><?php echo htmlspecialchars($action['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <h2>Due Invoices</h2>
                <a href="pending-due-payment-report.php">Report</a>
            </div>
            <div class="panel-body">
                <?php if (empty($dueInvoices)): ?>
                    <div class="empty-state">No due invoices today.</div>
                <?php else: ?>
                    <table class="dashboard-table">
                        <thead><tr><th>Client</th><th>Due Date</th><th>Amount</th></tr></thead>
                        <tbody>
                        <?php foreach ($dueInvoices as $invoice): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($invoice['ClientName'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo !empty($invoice['duedate']) ? date('d-m-Y', strtotime($invoice['duedate'])) : '-'; ?></td>
                                <td>Rs. <?php echo ni_money($invoice['totalAmount'] ?? 0); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const dashboardData = <?php echo json_encode($chartPayload, JSON_NUMERIC_CHECK); ?>;
    const moneyFormatter = new Intl.NumberFormat('en-IN', {
        maximumFractionDigits: 0
    });
    const rupee = function(value) {
        return 'Rs. ' + moneyFormatter.format(value || 0);
    };

    function chartOptions(stacked = false) {
        return {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { labels: { boxWidth: 12, color: '#405158' } },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + rupee(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                x: {
                    stacked: stacked,
                    grid: { display: false },
                    ticks: { color: '#607178' }
                },
                y: {
                    stacked: stacked,
                    ticks: {
                        color: '#607178',
                        callback: function(value) { return rupee(value); }
                    },
                    grid: { color: '#edf2f4' }
                }
            }
        };
    }

    if (window.Chart) {
        new Chart(document.getElementById('turnoverChart'), {
            type: 'bar',
            data: {
                labels: dashboardData.labels,
                datasets: [
                    { label: 'Turnover', data: dashboardData.turnover, backgroundColor: '#386fa4', borderRadius: 6 },
                    { label: 'Received', data: dashboardData.received, backgroundColor: '#2f8f5b', borderRadius: 6 }
                ]
            },
            options: chartOptions()
        });

        new Chart(document.getElementById('profitChart'), {
            type: 'line',
            data: {
                labels: dashboardData.labels,
                datasets: [{
                    label: 'Profit / Loss',
                    data: dashboardData.profit,
                    borderColor: '#d79424',
                    backgroundColor: 'rgba(215, 148, 36, 0.16)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4
                }]
            },
            options: chartOptions()
        });

        new Chart(document.getElementById('yearComparisonChart'), {
            type: 'bar',
            data: {
                labels: dashboardData.comparisonLabels,
                datasets: [
                    { label: 'Turnover', data: dashboardData.comparisonTurnover, backgroundColor: '#386fa4', borderRadius: 6 },
                    { label: 'Expenses', data: dashboardData.comparisonExpense, backgroundColor: '#c9564a', borderRadius: 6 },
                    { label: 'Profit / Loss', data: dashboardData.comparisonProfit, backgroundColor: '#2f8f5b', borderRadius: 6 }
                ]
            },
            options: chartOptions()
        });

        new Chart(document.getElementById('expenseChart'), {
            type: 'doughnut',
            data: {
                labels: ['Received', 'Expenses'],
                datasets: [{
                    data: [
                        dashboardData.received.reduce((sum, value) => sum + Number(value || 0), 0),
                        dashboardData.expenses.reduce((sum, value) => sum + Number(value || 0), 0)
                    ],
                    backgroundColor: ['#2f8f5b', '#c9564a'],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, color: '#405158' } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + rupee(context.parsed);
                            }
                        }
                    }
                }
            }
        });
    }
</script>
<?php include("EvaraFroentend/layout/footer.php"); ?>

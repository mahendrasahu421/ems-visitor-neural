<?php
include("EvaraFroentend/layout/head.php");
// include("EvaraFroentend/layout/header.php");

// Fetch client records from the database
$sqlClients = "SELECT * FROM clientmaster";
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
$totalClients = count($clients);
?>

<style>
    :root {
        --brand-teal: #0f6e7d;
        /* --brand-teal-dark: #0a4d58; */
        --brand-amber: #e8a13d;
        --ink: #1c2b2e;
        --muted: #6b7c7f;
        --surface: #ffffff;
        --page-bg: #eef4f4;
    }

    body {
        margin: 0;
        padding: 0;
        min-height: 100vh;
        background: linear-gradient(160deg, var(--brand-teal-dark) 0%, var(--brand-teal) 45%, #14899a 100%);
        font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
    }

    #dashboard {
        display: flex;
        justify-content: center;
        padding: 56px 20px;
    }

    #dashboard-panel {
        width: 100%;
        max-width: 880px;
        background: var(--surface);
        border-radius: 18px;
        box-shadow: 0 24px 60px rgba(10, 40, 45, 0.35);
        overflow: hidden;
    }

    #dashboard-header {
        background: linear-gradient(120deg, var(--brand-teal-dark), var(--brand-teal));
        padding: 36px 40px 30px;
        text-align: center;
        color: #fff;
    }

    #dashboard-header img {
        width: 130px;
        margin-bottom: 12px;
        filter: drop-shadow(0 2px 6px rgba(0,0,0,0.25));
    }

    #dashboard-header h1 {
        margin: 6px 0 4px;
        font-size: 22px;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    #dashboard-header p {
        margin: 0;
        font-size: 13px;
        opacity: 0.85;
    }

    #dashboard-stat {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-top: 14px;
        background: rgba(255,255,255,0.14);
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: 30px;
        padding: 6px 16px;
        font-size: 12.5px;
    }

    #dashboard-stat b {
        font-size: 14px;
    }

    #action-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        padding: 32px 36px 12px;
    }

    @media (max-width: 640px) {
        #action-grid { grid-template-columns: repeat(2, 1fr); }
    }

    .action-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-decoration: none;
        color: var(--ink);
        background: #f6f9f9;
        border: 1px solid #e3ebeb;
        border-radius: 14px;
        padding: 22px 12px;
        text-align: center;
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
    }

    .action-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(15, 110, 125, 0.18);
        border-color: var(--brand-teal);
        color: var(--brand-teal-dark);
    }

    .action-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #fff;
        background: var(--brand-teal);
    }

    .action-card span {
        font-size: 13.5px;
        font-weight: 600;
        line-height: 1.3;
    }

    .action-card small {
        font-size: 11px;
        color: var(--muted);
        font-weight: 400;
    }

    .action-card.accent .action-icon { background: var(--brand-amber); }
    .action-card.dark .action-icon { background: var(--ink); }

    #utility-row {
        padding: 8px 36px 32px;
        text-align: center;
    }

    #utility-row a {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: var(--muted);
        text-decoration: none;
        border: 1px dashed #c7d3d3;
        border-radius: 20px;
        padding: 6px 14px;
        transition: color 0.15s ease, border-color 0.15s ease;
    }

    #utility-row a:hover {
        color: var(--brand-teal-dark);
        border-color: var(--brand-teal);
    }
</style>

<div id="dashboard">
    <div id="dashboard-panel">

        <div id="dashboard-header">
            <img src="neural-logo-redesign.png" alt="Evara Logo" />
            <!-- <h1>Evara Admin Panel</h1> -->
            <p>Manage clients, invoices and payments from one place</p>
            
        </div>

        <div id="action-grid">
           <a href="dashboard.php" class="action-card">
    <div class="action-icon">📊</div>
    <span>Dashboard</span>
    <small>View dashboard and analytics</small>
</a>
            <a href="create-newInvoice.php" class="action-card">
                <div class="action-icon">🧾</div>
                <span>Proforma Invoice</span>
                <small>Create a new proforma</small>
            </a>

            <a href="client-invoice-list.php" class="action-card">
                <div class="action-icon">📄</div>
                <span>Invoice List</span>
                <small>View all invoices</small>
            </a>

            <a href="create-new-client.php" class="action-card">
                <div class="action-icon">➕</div>
                <span>Add Client</span>
                <small>Register a new client</small>
            </a>

            <a href="create-client-project.php" class="action-card">
                <div class="action-icon">📁</div>
                <span>Client Project / AMC</span>
                <small>Manage projects &amp; AMC</small>
            </a>

            <a href="client-ledger.php" class="action-card">
                <div class="action-icon">📒</div>
                <span>Client Ledger</span>
                <small>View account ledger</small>
            </a>

            <a href="update-payment.php" class="action-card accent">
                <div class="action-icon">💳</div>
                <span>Payment Receipt</span>
                <small>Record a payment</small>
            </a>

            <a href="recived-payment-form-clinet.php" class="action-card accent">
                <div class="action-icon">🧮</div>
                <span>Tax Invoice</span>
                <small>Generate tax invoice</small>
            </a>
        </div>

        <div id="utility-row">
            <a href="run-schema-upgrade.php">⚙ DB Schema Upgrade</a>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<?php
include("EvaraFroentend/layout/footer.php");
// Close the database connection after all includes are done,
// in case footer.php or header.php also needs it
$con->close();
?>
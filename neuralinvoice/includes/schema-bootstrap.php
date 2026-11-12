<?php

/**
 * Creates missing tables/columns required by invoice & project modules.
 * Safe to call on every request (runs once per request via static flag).
 */
function ni_ensure_database_schema(mysqli $con)
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $con->query("CREATE TABLE IF NOT EXISTS companymaster (
        CompanyID INT AUTO_INCREMENT PRIMARY KEY,
        CompanyName VARCHAR(150) NOT NULL,
        CompanyCode VARCHAR(50) NULL,
        EmailAddress VARCHAR(150) NULL,
        MobileNo VARCHAR(30) NULL,
        Address TEXT NULL,
        GSTNumber VARCHAR(25) NULL,
        DelStatus TINYINT NOT NULL DEFAULT 1,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uk_company_name (CompanyName)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $companyCols = [
        'CompanyCode' => 'VARCHAR(50) NULL AFTER CompanyName',
        'CompanyLogo' => 'VARCHAR(255) NULL AFTER CompanyCode',
        'EmailAddress' => 'VARCHAR(150) NULL AFTER CompanyLogo',
        'MobileNo' => 'VARCHAR(30) NULL AFTER EmailAddress',
        'PhoneNo' => 'VARCHAR(30) NULL AFTER MobileNo',
        'Website' => 'VARCHAR(150) NULL AFTER PhoneNo',
        'Address' => 'TEXT NULL AFTER Website',
        'RegistrationNo' => 'VARCHAR(45) NULL AFTER Address',
        'PanNo' => 'VARCHAR(25) NULL AFTER RegistrationNo',
        'GSTNumber' => 'VARCHAR(25) NULL AFTER PanNo',
        'IECNo' => 'VARCHAR(30) NULL AFTER GSTNumber',
        'LUTNo' => 'VARCHAR(50) NULL AFTER IECNo',
        'TANNo' => 'VARCHAR(30) NULL AFTER LUTNo',
        'Bank1AccountName' => 'VARCHAR(150) NULL AFTER TANNo',
        'Bank1AccountNo' => 'VARCHAR(50) NULL AFTER Bank1AccountName',
        'Bank1BankName' => 'VARCHAR(150) NULL AFTER Bank1AccountNo',
        'Bank1IFSC' => 'VARCHAR(30) NULL AFTER Bank1BankName',
        'Bank1MICR' => 'VARCHAR(30) NULL AFTER Bank1IFSC',
        'Bank1SwiftCode' => 'VARCHAR(30) NULL AFTER Bank1MICR',
        'Bank1Branch' => 'VARCHAR(150) NULL AFTER Bank1SwiftCode',
        'Bank1BranchCode' => 'VARCHAR(30) NULL AFTER Bank1Branch',
        'Bank2AccountName' => 'VARCHAR(150) NULL AFTER Bank1BranchCode',
        'Bank2AccountNo' => 'VARCHAR(50) NULL AFTER Bank2AccountName',
        'Bank2BankName' => 'VARCHAR(150) NULL AFTER Bank2AccountNo',
        'Bank2IFSC' => 'VARCHAR(30) NULL AFTER Bank2BankName',
        'Bank2MICR' => 'VARCHAR(30) NULL AFTER Bank2IFSC',
        'Bank2SwiftCode' => 'VARCHAR(30) NULL AFTER Bank2MICR',
        'Bank2Branch' => 'VARCHAR(150) NULL AFTER Bank2SwiftCode',
        'Bank2BranchCode' => 'VARCHAR(30) NULL AFTER Bank2Branch',
        'DelStatus' => 'TINYINT NOT NULL DEFAULT 1 AFTER Bank2BranchCode',
        'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER DelStatus',
    ];
    foreach ($companyCols as $col => $def) {
        $r = $con->query("SHOW COLUMNS FROM companymaster LIKE '$col'");
        if ($r && $r->num_rows === 0) {
            $con->query("ALTER TABLE companymaster ADD COLUMN $col $def");
        }
    }
    $con->query("ALTER TABLE companymaster MODIFY COLUMN Address TEXT NULL");
    $con->query("ALTER TABLE companymaster MODIFY COLUMN GSTNumber VARCHAR(25) NULL");

    $clientCompanyCol = $con->query("SHOW COLUMNS FROM clientmaster LIKE 'CompanyID'");
    if ($clientCompanyCol && $clientCompanyCol->num_rows === 0) {
        $con->query("ALTER TABLE clientmaster ADD COLUMN CompanyID INT NULL AFTER ClientID");
    }

    $con->query("CREATE TABLE IF NOT EXISTS work_type_master (
        WorkTypeID INT AUTO_INCREMENT PRIMARY KEY,
        WorkTypeName VARCHAR(100) NOT NULL,
        amc_type ENUM('project','amc_only') NOT NULL DEFAULT 'project',
        DelStatus TINYINT NOT NULL DEFAULT 1,
        sort_order INT NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uk_work_type_name (WorkTypeName)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $rAmcCol = $con->query("SHOW COLUMNS FROM work_type_master LIKE 'amc_type'");
    if ($rAmcCol && $rAmcCol->num_rows === 0) {
        $con->query("ALTER TABLE work_type_master ADD COLUMN amc_type ENUM('project','amc_only') NOT NULL DEFAULT 'project' AFTER WorkTypeName");
    }
    $con->query("UPDATE work_type_master SET amc_type = 'amc_only' WHERE WorkTypeName LIKE '%AMC%'");

    $defaults = [
        'Mobile App',
        'Web Application',
        'Website',
        'Digital Marketing',
        'ERP / Software',
        'AMC Support',
    ];
    $sort = 0;
    foreach ($defaults as $name) {
        $esc = $con->real_escape_string($name);
        $chk = $con->query("SELECT WorkTypeID FROM work_type_master WHERE WorkTypeName = '$esc' LIMIT 1");
        if ($chk && $chk->num_rows === 0) {
            $con->query("INSERT INTO work_type_master (WorkTypeName, sort_order) VALUES ('$esc', $sort)");
        }
        $sort++;
    }

    $t = $con->query("SHOW TABLES LIKE 'client_projects'");
    if ($t && $t->num_rows > 0) {
        $cols = [
            'engagement_type' => "ENUM('project','amc_only') NOT NULL DEFAULT 'project' AFTER project_name",
            'has_amc' => "ENUM('yes','no') NOT NULL DEFAULT 'no' AFTER engagement_type",
            'tax_percent' => "DECIMAL(5,2) NOT NULL DEFAULT 0 AFTER total_value",
            'tax_amount' => "DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER tax_percent",
            'cgst_percent' => "DECIMAL(5,2) NOT NULL DEFAULT 0 AFTER tax_amount",
            'cgst_amount' => "DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER cgst_percent",
            'sgst_percent' => "DECIMAL(5,2) NOT NULL DEFAULT 0 AFTER cgst_amount",
            'sgst_amount' => "DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER sgst_percent",
            'igst_percent' => "DECIMAL(5,2) NOT NULL DEFAULT 0 AFTER sgst_amount",
            'igst_amount' => "DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER igst_percent",
            'tax_type' => "VARCHAR(50) NULL AFTER igst_amount",
            'total_value_with_tax' => "DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER tax_type",
            'total_with_tax' => "DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER total_value_with_tax",
            'final_amount' => "DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER total_with_tax",
            'project_start_date' => "DATE NULL AFTER payment_mode",
            'project_end_date' => "DATE NULL AFTER project_start_date",
            'project_duration_days' => "INT NOT NULL DEFAULT 0 AFTER project_end_date",
            'financial_year' => "VARCHAR(12) NULL AFTER amc_end",
            'amc_working_days' => "INT NULL DEFAULT 0 AFTER financial_year",
            'work_type' => "VARCHAR(100) NULL AFTER amc_working_days",
            'work_type_id' => 'INT NULL AFTER work_type',
        ];
        foreach ($cols as $col => $def) {
            $r = $con->query("SHOW COLUMNS FROM client_projects LIKE '$col'");
            if ($r && $r->num_rows === 0) {
                $con->query("ALTER TABLE client_projects ADD COLUMN $col $def");
            }
        }
    }

    $con->query("CREATE TABLE IF NOT EXISTS client_projects (
        project_id INT AUTO_INCREMENT PRIMARY KEY,
        client_id INT NOT NULL,
        project_name VARCHAR(255) NOT NULL,
        engagement_type ENUM('project','amc_only') NOT NULL DEFAULT 'project',
        payment_mode ENUM('percentage','module') NOT NULL DEFAULT 'percentage',
        total_value DECIMAL(15,2) NOT NULL DEFAULT 0,
        amc_start DATE NULL,
        amc_end DATE NULL,
        financial_year VARCHAR(12) NULL,
        amc_working_days INT NULL DEFAULT 0,
        work_type VARCHAR(100) NULL,
        work_type_id INT NULL,
        proposal_file VARCHAR(255) NULL,
        notes TEXT NULL,
        status TINYINT NOT NULL DEFAULT 1,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_client_projects_client (client_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $con->query("CREATE TABLE IF NOT EXISTS project_payment_milestones (
        milestone_id INT AUTO_INCREMENT PRIMARY KEY,
        project_id INT NOT NULL,
        milestone_label VARCHAR(255) NOT NULL,
        module_name VARCHAR(255) NULL,
        percentage DECIMAL(6,2) NULL,
        amount DECIMAL(15,2) NOT NULL DEFAULT 0,
        due_date DATE NULL,
        is_paid TINYINT NOT NULL DEFAULT 0,
        paid_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
        paid_date DATE NULL,
        sort_order INT NOT NULL DEFAULT 0,
        INDEX idx_milestones_project (project_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $milestoneCols = [
        'tax_amount' => "DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER amount",
        'amount_with_tax' => "DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER tax_amount",
        'invoiced_amount' => "DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER amount_with_tax",
        'last_paid_date' => "DATE NULL AFTER paid_date",
    ];
    foreach ($milestoneCols as $col => $def) {
        $r = $con->query("SHOW COLUMNS FROM project_payment_milestones LIKE '$col'");
        if ($r && $r->num_rows === 0) {
            $con->query("ALTER TABLE project_payment_milestones ADD COLUMN $col $def");
        }
    }

    $invoiceTable = $con->query("SHOW TABLES LIKE 'invoice'");
    if ($invoiceTable && $invoiceTable->num_rows > 0) {
        $durationCol = $con->query("SHOW COLUMNS FROM invoice LIKE 'duration_days'");
        if ($durationCol && $durationCol->num_rows === 0) {
            $con->query("ALTER TABLE invoice ADD COLUMN duration_days VARCHAR(100) NULL DEFAULT NULL AFTER duedate");
        } elseif ($durationCol) {
            $durationDef = $durationCol->fetch_assoc();
            if ($durationDef && stripos((string) $durationDef['Type'], 'int') !== false) {
                $con->query("ALTER TABLE invoice MODIFY COLUMN duration_days VARCHAR(100) NULL DEFAULT NULL");
            }
        }

        $invoiceCols = [
            'CompanyID' => "INT NULL AFTER ClientID",
            'bankChargePercent' => "DECIMAL(5,2) NOT NULL DEFAULT 0 AFTER igstAmount",
            'bankChargeAmount' => "DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER bankChargePercent",
            'currency' => "VARCHAR(10) NOT NULL DEFAULT 'INR' AFTER totalAmount",
            'exchange_rate' => "DECIMAL(10,4) NOT NULL DEFAULT 1.0000 AFTER currency",
            'total_amount_foreign' => "DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER exchange_rate",
            'sent_at' => "DATETIME NULL DEFAULT NULL AFTER status",
            'cancelled_at' => "DATETIME NULL DEFAULT NULL AFTER sent_at",
            'cancel_reason' => "VARCHAR(255) NULL AFTER cancelled_at",
        ];
        foreach ($invoiceCols as $col => $def) {
            $r = $con->query("SHOW COLUMNS FROM invoice LIKE '$col'");
            if ($r && $r->num_rows === 0) {
                $con->query("ALTER TABLE invoice ADD COLUMN $col $def");
            }
        }
    }

    $receivedTable = $con->query("SHOW TABLES LIKE 'recived_payment'");
    if ($receivedTable && $receivedTable->num_rows > 0) {
        $receivedAmountCol = $con->query("SHOW COLUMNS FROM recived_payment LIKE 'recived_amount'");
        if ($receivedAmountCol) {
            $receivedAmountDef = $receivedAmountCol->fetch_assoc();
            if ($receivedAmountDef && stripos((string) $receivedAmountDef['Type'], 'int') !== false) {
                $con->query("ALTER TABLE recived_payment MODIFY COLUMN recived_amount DECIMAL(15,2) NOT NULL DEFAULT 0");
            }
        }

        $cols = [
            'tds_amount' => "DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER recived_amount",
            'received_net' => "DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER tds_amount",
            'payment_note' => "VARCHAR(255) NULL AFTER received_net",
            'created_at' => "DATE NOT NULL DEFAULT (CURRENT_DATE)",
        ];
        foreach ($cols as $col => $def) {
            $r = $con->query("SHOW COLUMNS FROM recived_payment LIKE '$col'");
            if ($r && $r->num_rows === 0) {
                $con->query("ALTER TABLE recived_payment ADD COLUMN $col $def");
            }
        }
    }
}

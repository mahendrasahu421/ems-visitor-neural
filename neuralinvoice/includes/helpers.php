<?php

function ni_escape(mysqli $con, $value)
{
    return $con->real_escape_string(trim((string) $value));
}

function ni_upload_file(array $file, string $subdir, array $allowedExt = ['pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg'])
{
    if (empty($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) {
        return false;
    }
    $base = dirname(__DIR__) . '/uploads/' . $subdir;
    if (!is_dir($base)) {
        mkdir($base, 0755, true);
    }
    $name = uniqid('f_', true) . '.' . $ext;
    $dest = $base . '/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return false;
    }
    return 'uploads/' . $subdir . '/' . $name;
}

function ni_format_money($amount)
{
    return number_format((float) $amount, 2, '.', ',');
}

function ni_default_company_id(mysqli $con): int
{
    $r = $con->query("SELECT CompanyID FROM companymaster WHERE DelStatus = 1 ORDER BY CompanyID LIMIT 1");
    if ($r && $row = $r->fetch_assoc()) {
        return (int) $row['CompanyID'];
    }

    $r = $con->query("SELECT CompanyID FROM companymaster ORDER BY CompanyID LIMIT 1");
    if ($r && $row = $r->fetch_assoc()) {
        return (int) $row['CompanyID'];
    }

    return 0;
}

function ni_company_id_for_client(mysqli $con, int $clientId): int
{
    if ($clientId > 0) {
        $r = $con->query("
            SELECT cm.CompanyID
            FROM clientmaster cm
            JOIN companymaster co ON cm.CompanyID = co.CompanyID
            WHERE cm.ClientID = $clientId
            LIMIT 1
        ");
        if ($r && $row = $r->fetch_assoc()) {
            $companyId = (int) ($row['CompanyID'] ?? 0);
            if ($companyId > 0) {
                return $companyId;
            }
        }
    }

    return ni_default_company_id($con);
}

function ni_compute_gst_for_client_amount(mysqli $con, int $clientId, float $baseAmount): array
{
    $clientId = (int) $clientId;
    $sql = "
        SELECT COALESCE(c.CountryName, cm.Country) AS country_name,
               COALESCE(s.StateName, cm.StateID) AS state_name
        FROM clientmaster cm
        LEFT JOIN countrymaster c ON cm.Country = c.CountryID
        LEFT JOIN statemaster s ON cm.StateID = s.StateID
        WHERE cm.ClientID = $clientId
        LIMIT 1
    ";
    $res = $con->query($sql);
    $row = $res ? $res->fetch_assoc() : [];

    $country = strtolower(trim((string) ($row['country_name'] ?? '')));
    $state = strtolower(trim((string) ($row['state_name'] ?? '')));

    $cgstPercent = 0.0;
    $sgstPercent = 0.0;
    $igstPercent = 0.0;
    $taxType = 'international';

    $isInternational = !($country === '' || $country === 'india') || $state === 'internation' || $state === 'international';

    if (!$isInternational) {
        if ($state === 'uttar pradesh') {
            $cgstPercent = 9.0;
            $sgstPercent = 9.0;
            $taxType = 'intra_state';
        } else {
            $igstPercent = 18.0;
            $taxType = 'inter_state';
        }
    }

    $cgstAmount = round($baseAmount * $cgstPercent / 100, 2);
    $sgstAmount = round($baseAmount * $sgstPercent / 100, 2);
    $igstAmount = round($baseAmount * $igstPercent / 100, 2);
    $totalTaxAmount = round($cgstAmount + $sgstAmount + $igstAmount, 2);
    $bankChargePercent = $isInternational ? 3.5 : 0.0;
    $bankChargeAmount = round($baseAmount * $bankChargePercent / 100, 2);

    return [
        'country_name' => $row['country_name'] ?? '',
        'state_name' => $row['state_name'] ?? '',
        'tax_type' => $taxType,
        'tax_percent' => $cgstPercent + $sgstPercent + $igstPercent,
        'cgst_percent' => $cgstPercent,
        'sgst_percent' => $sgstPercent,
        'igst_percent' => $igstPercent,
        'cgst_amount' => $cgstAmount,
        'sgst_amount' => $sgstAmount,
        'igst_amount' => $igstAmount,
        'bank_charge_percent' => $bankChargePercent,
        'bank_charge_amount' => $bankChargeAmount,
        'total_tax_amount' => $totalTaxAmount,
        'total_amount' => round($baseAmount + $totalTaxAmount + $bankChargeAmount, 2),
    ];
}

function ni_country_is_india(mysqli $con, $countryId): bool
{
    $countryId = (int) $countryId;
    if ($countryId <= 0) {
        return false;
    }

    $res = $con->query("SELECT CountryName FROM countrymaster WHERE CountryID = $countryId LIMIT 1");
    $row = $res ? $res->fetch_assoc() : null;
    return strtolower(trim((string) ($row['CountryName'] ?? ''))) === 'india';
}

function ni_get_international_state_id(mysqli $con, $fallbackCountryId = 0): int
{
    $res = $con->query("
        SELECT StateID
        FROM statemaster
        WHERE LOWER(TRIM(StateName)) IN ('international', 'internation')
        ORDER BY StateID
        LIMIT 1
    ");
    if ($res && $row = $res->fetch_assoc()) {
        return (int) $row['StateID'];
    }

    $countryId = (int) $fallbackCountryId;
    if ($countryId <= 0) {
        $india = $con->query("SELECT CountryID FROM countrymaster WHERE LOWER(TRIM(CountryName)) = 'india' LIMIT 1");
        $indiaId = ($india && $r = $india->fetch_assoc()) ? (int) $r['CountryID'] : 0;
        $country = $con->query("SELECT CountryID FROM countrymaster " . ($indiaId > 0 ? "WHERE CountryID <> $indiaId " : "") . "ORDER BY CountryID LIMIT 1");
        $countryId = ($country && $r = $country->fetch_assoc()) ? (int) $r['CountryID'] : 0;
    }

    if ($countryId <= 0) {
        return 0;
    }

    $con->query("INSERT INTO statemaster (StateName, CountryID) VALUES ('International', $countryId)");
    return (int) $con->insert_id;
}

/** Indian FY: April 1 – March 31. Returns start year (e.g. 2025 for FY 2025-26). */
function ni_current_fy_start_year()
{
    $y = (int) date('Y');
    $m = (int) date('n');
    return $m >= 4 ? $y : $y - 1;
}

function ni_fy_label(int $startYear)
{
    $end = $startYear + 1;
    return $startYear . '-' . substr((string) $end, -2);
}

/** @return array{start:string,end:string,label:string,start_year:int} */
function ni_fy_to_period(int $startYear)
{
    $start = sprintf('%04d-04-01', $startYear);
    $end = sprintf('%04d-03-31', $startYear + 1);
    return [
        'start' => $start,
        'end' => $end,
        'label' => ni_fy_label($startYear),
        'start_year' => $startYear,
    ];
}

function ni_fy_options(int $past = 3, int $future = 2)
{
    $current = ni_current_fy_start_year();
    $options = [];
    for ($y = $current - $past; $y <= $current + $future; $y++) {
        $p = ni_fy_to_period($y);
        $options[] = $p;
    }
    return $options;
}

/**
 * Count working days (Mon–Fri only, 5-day week) between two dates inclusive.
 */
function ni_count_working_days(string $startDate, string $endDate)
{
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    if ($end < $start) {
        return 0;
    }
    $count = 0;
    $cursor = clone $start;
    while ($cursor <= $end) {
        $dow = (int) $cursor->format('N'); // 1=Mon … 7=Sun
        if ($dow <= 7) {
            $count++;
        }
        $cursor->modify('+1 day');
    }
    return $count;
}

/** @return array<int, array<string,mixed>> */
function ni_get_companies(mysqli $con)
{
    if (!function_exists('ni_ensure_database_schema')) {
        require_once dirname(__DIR__) . '/includes/schema-bootstrap.php';
    }
    ni_ensure_database_schema($con);

    $r = $con->query("SELECT CompanyID, CompanyName, COALESCE(CompanyCode, '') AS CompanyCode
        FROM companymaster
        WHERE DelStatus = 1
        ORDER BY CompanyName");
    if (!$r) {
        return [];
    }

    $companies = [];
    while ($row = $r->fetch_assoc()) {
        $companies[] = $row;
    }
    return $companies;
}

function ni_invoice_prefix_for_client(mysqli $con, int $clientId): string
{
    $source = '';
    if ($clientId > 0) {
        $r = $con->query("
            SELECT co.CompanyCode
            FROM clientmaster cm
            LEFT JOIN companymaster co ON cm.CompanyID = co.CompanyID
            WHERE cm.ClientID = $clientId
            LIMIT 1
        ");
        $row = $r ? $r->fetch_assoc() : null;
        $source = strtoupper((string) ($row['CompanyCode'] ?? ''));
    }
    if ($source === '') {
        $fallback = $con->query("SELECT CompanyCode FROM companymaster WHERE DelStatus = 1 AND COALESCE(CompanyCode, '') <> '' ORDER BY CompanyID LIMIT 1");
        $fallbackRow = $fallback ? $fallback->fetch_assoc() : null;
        $source = strtoupper((string) ($fallbackRow['CompanyCode'] ?? 'NSPL'));
    }
    $prefix = preg_replace('/[^A-Z0-9]+/', '', $source);
    return $prefix !== '' ? substr($prefix, 0, 12) : 'NSPL';
}

function ni_work_type_has_amc_column(mysqli $con): bool
{
    static $has = null;
    if ($has === null) {
        $r = $con->query("SHOW COLUMNS FROM work_type_master LIKE 'amc_type'");
        $has = $r && $r->num_rows > 0;
    }
    return $has;
}

/** project = full project + milestones | amc_only = sirf AMC manage */
function ni_engagement_from_work_type_id(mysqli $con, int $workTypeId): string
{
    if ($workTypeId <= 0) {
        return 'project';
    }
    if (ni_work_type_has_amc_column($con)) {
        $r = $con->query("SELECT amc_type, WorkTypeName FROM work_type_master WHERE WorkTypeID = $workTypeId AND DelStatus = 1 LIMIT 1");
        if ($r && $row = $r->fetch_assoc()) {
            return ($row['amc_type'] ?? '') === 'amc_only' ? 'amc_only' : 'project';
        }
    }
    $r = $con->query("SELECT WorkTypeName FROM work_type_master WHERE WorkTypeID = $workTypeId AND DelStatus = 1 LIMIT 1");
    if ($r && $row = $r->fetch_assoc()) {
        return stripos($row['WorkTypeName'], 'amc') !== false ? 'amc_only' : 'project';
    }
    return 'project';
}

/** @return array<int, array{WorkTypeID:int, WorkTypeName:string, amc_type:string}> */
function ni_get_work_types(mysqli $con)
{
    if (!function_exists('ni_ensure_database_schema')) {
        require_once dirname(__DIR__) . '/includes/schema-bootstrap.php';
    }
    ni_ensure_database_schema($con);

    $sql = ni_work_type_has_amc_column($con)
        ? 'SELECT WorkTypeID, WorkTypeName, amc_type FROM work_type_master WHERE DelStatus = 1 ORDER BY sort_order, WorkTypeName'
        : 'SELECT WorkTypeID, WorkTypeName FROM work_type_master WHERE DelStatus = 1 ORDER BY sort_order, WorkTypeName';

    try {
        $r = $con->query($sql);
    } catch (mysqli_sql_exception $e) {
        return [];
    }
    if (!$r) {
        return [];
    }
    $list = [];
    while ($row = $r->fetch_assoc()) {
        if (empty($row['amc_type'])) {
            $row['amc_type'] = stripos($row['WorkTypeName'], 'amc') !== false ? 'amc_only' : 'project';
        }
        $list[] = $row;
    }
    return $list;
}

function ni_engagement_type_label(string $type)
{
    return $type === 'amc_only' ? 'AMC Only' : 'Project + AMC';
}

/** Build default AMC-only milestone rows from annual fee and split plan. */
function ni_amc_only_milestones(float $annualFee, string $splitPlan, string $dueDate = '')
{
    $rows = [];
    if ($annualFee <= 0) {
        return $rows;
    }
    switch ($splitPlan) {
        case 'half_yearly':
            $parts = [
                ['label' => 'AMC 1st Half', 'pct' => 50],
                ['label' => 'AMC 2nd Half', 'pct' => 50],
            ];
            break;
        case 'quarterly':
            $parts = [
                ['label' => 'AMC Q1', 'pct' => 25],
                ['label' => 'AMC Q2', 'pct' => 25],
                ['label' => 'AMC Q3', 'pct' => 25],
                ['label' => 'AMC Q4', 'pct' => 25],
            ];
            break;
        default:
            $parts = [['label' => 'Annual AMC Fee', 'pct' => 100]];
    }
    foreach ($parts as $p) {
        $rows[] = [
            'milestone_label' => $p['label'],
            'module_name' => '',
            'percentage' => (string) $p['pct'],
            'milestone_amount' => (string) round($annualFee * $p['pct'] / 100, 2),
            'due_date' => $dueDate,
        ];
    }
    return $rows;
}

/** Ensure AMC dates fall within Indian FY (Apr 1 – Mar 31). */
function ni_validate_amc_dates_in_fy(string $amcStart, string $amcEnd, int $fyStartYear)
{
    $period = ni_fy_to_period($fyStartYear);
    if ($amcStart < $period['start'] || $amcEnd > $period['end']) {
        return false;
    }
    if ($amcEnd < $amcStart) {
        return false;
    }
    return true;
}

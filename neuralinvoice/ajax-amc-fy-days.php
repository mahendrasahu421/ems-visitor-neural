<?php
header('Content-Type: application/json; charset=utf-8');
include 'connection.php';
include 'includes/helpers.php';

$startYear = (int) ($_GET['fy_start_year'] ?? $_POST['fy_start_year'] ?? 0);
if ($startYear < 2000 || $startYear > 2100) {
    echo json_encode(['ok' => false, 'message' => 'Invalid financial year']);
    exit;
}

$period = ni_fy_to_period($startYear);
$amcStart = trim($_GET['amc_start'] ?? $_POST['amc_start'] ?? '');
$amcEnd = trim($_GET['amc_end'] ?? $_POST['amc_end'] ?? '');

if ($amcStart === '' || $amcEnd === '') {
    $amcStart = $period['start'];
    $amcEnd = $period['end'];
}

if (!ni_validate_amc_dates_in_fy($amcStart, $amcEnd, $startYear)) {
    echo json_encode([
        'ok' => false,
        'message' => 'Dates must be within FY ' . $period['label'] . ' (' . $period['start'] . ' to ' . $period['end'] . ')',
        'fy_min' => $period['start'],
        'fy_max' => $period['end'],
    ]);
    exit;
}

$workingDays = ni_count_working_days($amcStart, $amcEnd);
$calendarDays = (int) (new DateTime($amcStart))->diff(new DateTime($amcEnd))->days + 1;

echo json_encode([
    'ok' => true,
    'financial_year' => $period['label'],
    'fy_min' => $period['start'],
    'fy_max' => $period['end'],
    'amc_start' => $amcStart,
    'amc_end' => $amcEnd,
    'amc_working_days' => $workingDays,
    'calendar_days' => $calendarDays,
    'week_pattern' => '5',
]);

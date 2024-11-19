<?php
session_start();
require '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rank'] < 10) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$intervals = [
    'all_time' => 'All Time',
    '6_months' => 'Last 6 Months',
    '1_month' => 'Last Month',
    '1_week' => 'Last Week',
    '1_day' => 'Last Day',
];

$profits = [
    'bets' => [],
    'deposits' => [],
    'cashouts' => []
];

$totals = []; // Array to store total income/outgoings

foreach ($intervals as $key => $label) {
    switch ($key) {
        case '6_months':
            $time_filter = 'AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)';
            break;
        case '1_month':
            $time_filter = 'AND created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';
            break;
        case '1_week':
            $time_filter = 'AND created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)';
            break;
        case '1_day':
            $time_filter = 'AND created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)';
            break;
        default:
            $time_filter = '';
            break;
    }

    $stmt = $pdo->prepare("SELECT SUM(amount_played) AS profit FROM history WHERE status = 'lose' AND user_id IN (SELECT id FROM users WHERE rank < 10) $time_filter");
    $stmt->execute();
    $profits['bets'][] = (float)$stmt->fetchColumn() ?: 0;

    $stmt = $pdo->prepare("SELECT SUM(amount) AS total FROM orders WHERE category = 'Deposit' AND status = 'Completed' $time_filter");
    $stmt->execute();
    $deposits = (float)$stmt->fetchColumn() ?: 0;
    $profits['deposits'][] = $deposits;

    $stmt = $pdo->prepare("SELECT SUM(amount) AS total FROM orders WHERE category = 'Cash-Out' AND status = 'Completed' $time_filter");
    $stmt->execute();
    $cashouts = (float)$stmt->fetchColumn() ?: 0;
    $profits['cashouts'][] = $cashouts;

    // Calculate and store total for each interval
    $totals[] = $deposits + $cashouts;
}

echo json_encode([
    'success' => true,
    'labels' => array_values($intervals),
    'profits' => $profits,
    'totals' => $totals, // Include totals in the response
]);

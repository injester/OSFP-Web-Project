<?php
session_start();
require 'config.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$bet_amount = isset($_POST['bet_amount']) ? (int)$_POST['bet_amount'] : 0;

if ($bet_amount < 10 || $bet_amount > 500) {
    echo json_encode(['success' => false, 'message' => 'Invalid bet amount.']);
    exit;
}

try {
    // Fetch user balance, username, and rank from users table
    $stmt = $pdo->prepare("SELECT balance, username, rank FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    if ($bet_amount > $user['balance']) {
        echo json_encode(['success' => false, 'message' => 'Insufficient balance']);
        exit;
    }

    // Deduct bet amount from balance
    $new_balance = $user['balance'] - $bet_amount;

    // Generate a unique ID for the bet
    $bet_id = uniqid('bet_', true);

    // Update user's balance in users table
    $stmt = $pdo->prepare("UPDATE users SET balance = :new_balance WHERE id = :id");
    $stmt->execute([
        'new_balance' => $new_balance,
        'id' => $user_id
    ]);

    // Fetch or initialize user wager stats
    $stmt = $pdo->prepare("SELECT total_wager, claimable_rakeback FROM user_wager_stats WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $wager_stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Increment total wager by bet amount
    $total_wagered = ($wager_stats ? $wager_stats['total_wager'] : 0) + $bet_amount;

    // Insert or update wager stats with the current rank and claimable rakeback
    $stmt = $pdo->prepare("
        INSERT INTO user_wager_stats (user_id, username, total_wager, rank, claimable_rakeback)
        VALUES (:user_id, :username, :total_wager, :rank, :claimable_rakeback)
        ON DUPLICATE KEY UPDATE 
            total_wager = :total_wager, 
            rank = :rank,
            claimable_rakeback = claimable_rakeback  -- to keep existing rakeback amount
    ");
    $stmt->execute([
        'user_id' => $user_id,
        'username' => $user['username'],
        'total_wager' => $total_wagered,
        'rank' => $user['rank'],
        'claimable_rakeback' => $wager_stats['claimable_rakeback'] ?? 0
    ]);

    echo json_encode([
        'success' => true,
        'new_balance' => number_format($new_balance, 2),
        'total_wagered' => number_format($total_wagered, 2),
        'rank' => $user['rank']
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error processing bet: ' . $e->getMessage()]);
}

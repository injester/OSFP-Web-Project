<?php
session_start();
require 'config.php';

$user_id = $_SESSION['user_id'] ?? null;

$response = [
    'success' => false,
    'balance' => 0,
    'total_winnings' => 0,
    'last_bet' => 0
];

if ($user_id) {
    // Fetch balance and winnings data
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $response['balance'] = $user['balance'];

        // Total winnings
        $stmt = $pdo->prepare("SELECT SUM(amount_played * 2) as total_winnings FROM history WHERE user_id = :id AND status = 'win'");
        $stmt->execute(['id' => $user_id]);
        $winnings = $stmt->fetch(PDO::FETCH_ASSOC);
        $response['total_winnings'] = $winnings['total_winnings'] ?? 0;

        // Last bet
        $stmt = $pdo->prepare("SELECT amount_played FROM history WHERE user_id = :id ORDER BY created_at DESC LIMIT 1");
        $stmt->execute(['id' => $user_id]);
        $lastBet = $stmt->fetch(PDO::FETCH_ASSOC);
        $response['last_bet'] = $lastBet['amount_played'] ?? 0;

        $response['success'] = true;
    }
}

echo json_encode($response);

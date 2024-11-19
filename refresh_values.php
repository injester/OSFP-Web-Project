<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch balance, winnings, and last bet
$stmt = $pdo->prepare("SELECT balance FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$balance = $stmt->fetchColumn();

$winningsStmt = $pdo->prepare("SELECT SUM(amount_played) FROM history WHERE user_id = :user_id AND status = 'win'");
$winningsStmt->execute(['user_id' => $user_id]);
$total_winnings = $winningsStmt->fetchColumn();

$lastBetStmt = $pdo->prepare("SELECT amount_played FROM history WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 1");
$lastBetStmt->execute(['user_id' => $user_id]);
$last_bet = $lastBetStmt->fetchColumn() ?: 0;

echo json_encode([
    'success' => true,
    'balance' => number_format($balance, 2),
    'total_winnings' => number_format($total_winnings, 2),
    'last_bet' => number_format($last_bet, 2)
]);

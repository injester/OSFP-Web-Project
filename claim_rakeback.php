<?php
session_start();
require 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch the user's balance from `users` and claimable rakeback from `raking`
$stmt = $pdo->prepare("SELECT u.balance, r.claimable_rakeback FROM users u JOIN raking r ON u.id = r.user_id WHERE u.id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch();

$minimumRakeback = 10.00; // Minimum required amount for rakeback in millions

if ($user) {
    $claimableRakeback = $user['claimable_rakeback'];

    if ($claimableRakeback < $minimumRakeback) {
        echo json_encode(['success' => false, 'message' => 'Minimum 10M required for rakeback.']);
        exit;
    }

    // If claimable rakeback meets the minimum amount
    $new_balance = $user['balance'] + $claimableRakeback;

    // Update balance in `users`
    $stmt = $pdo->prepare("UPDATE users SET balance = :new_balance WHERE id = :id");
    $stmt->execute(['new_balance' => $new_balance, 'id' => $user_id]);

    // Reset claimable rakeback in `raking`
    $stmt = $pdo->prepare("UPDATE raking SET claimable_rakeback = 0 WHERE user_id = :id");
    $stmt->execute(['id' => $user_id]);

    echo json_encode(['success' => true, 'message' => 'Rakeback claimed successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'No rakeback to claim.']);
}
exit;

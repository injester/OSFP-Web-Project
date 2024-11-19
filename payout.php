<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];
$change_amount = $_POST['change_amount'];
$action = $_POST['action']; // either "add" or "subtract"

// Fetch current balance
$stmt = $pdo->prepare("SELECT balance FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

$current_balance = $user['balance'];
$new_balance = ($action === 'add') ? $current_balance + $change_amount : $current_balance - $change_amount;

if ($new_balance < 0) {
    echo json_encode(['success' => false, 'message' => 'Insufficient balance']);
    exit;
}

// Update balance
$updateStmt = $pdo->prepare("UPDATE users SET balance = :new_balance WHERE id = :id");
$updateStmt->execute(['new_balance' => $new_balance, 'id' => $user_id]);

echo json_encode(['success' => true, 'new_balance' => $new_balance]);
?>

<?php
session_start();
require '../config.php'; // Include the database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to deposit.']);
    exit;
}

// Get the user ID and the deposit amount
$user_id = $_SESSION['user_id'];
$amount = isset($_POST['amount']) ? (int) $_POST['amount'] : 0;

// Ensure the amount is valid
if ($amount < 10) {
    echo json_encode(['success' => false, 'message' => 'The minimum deposit amount is 10M.']);
    exit;
}

// Insert a new order with 'Pending' status for the deposit
$stmt = $pdo->prepare("INSERT INTO orders (category, amount, status, user_id) VALUES ('Deposit', :amount, 'Pending', :user_id)");
if ($stmt->execute(['amount' => $amount, 'user_id' => $user_id])) {
    echo json_encode(['success' => true, 'message' => 'Deposit order created successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to create deposit order.']);
}

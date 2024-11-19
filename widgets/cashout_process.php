<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to cash out.']);
    exit;
}

// Include the config to access the database
require '../config.php';

$user_id = $_SESSION['user_id'];
$amount = isset($_POST['amount']) ? (int) $_POST['amount'] : 0;

// Check the user's balance
$stmt = $pdo->prepare("SELECT balance FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}

$user_balance = (int) $user['balance'];

// Ensure the amount is valid (final check after clicking Cash Out)
$minCashout = 10; // Set minimum cash-out amount to 10
if ($amount < $minCashout) {
    echo json_encode(['success' => false, 'message' => "The minimum cash-out amount is {$minCashout}M."]);
    exit;
}

if ($amount > $user_balance) {
    echo json_encode(['success' => false, 'message' => 'Insufficient balance to cash out.']);
    exit;
}

// Begin transaction to ensure order and balance deduction happen atomically
$pdo->beginTransaction();

try {
    // Create a pending cash-out order
    $stmt = $pdo->prepare("INSERT INTO orders (category, amount, status, user_id) VALUES ('Cash-Out', :amount, 'Pending', :user_id)");
    $stmt->execute(['amount' => $amount, 'user_id' => $user_id]);

    // Deduct the amount from the user's balance
    $new_balance = $user_balance - $amount;
    $update_stmt = $pdo->prepare("UPDATE users SET balance = :balance WHERE id = :id");
    $update_stmt->execute(['balance' => $new_balance, 'id' => $user_id]);

    // Commit the transaction
    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Cash-out order created successfully!']);
} catch (Exception $e) {
    // Rollback the transaction if something went wrong
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Failed to create cash-out order.']);
}
?>

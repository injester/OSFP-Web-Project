<?php
session_start();
require '../config.php';

// Ensure the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['rank'] < 10) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized action']);
    exit;
}

// Get the order ID from POST
$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : null;

if (!$order_id) {
    echo json_encode(['success' => false, 'message' => 'Order ID not provided']);
    exit;
}

try {
    // Fetch the order details and lock the row to prevent race conditions
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = :order_id FOR UPDATE");
    $stmt->execute(['order_id' => $order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit;
    }

    // Check if the order is already completed or canceled
    if ($order['status'] == 'Completed') {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Order has already been completed.']);
        exit;
    }

    if ($order['status'] == 'Canceled') {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Order has already been canceled.']);
        exit;
    }

    // Add the amount to balance if it's a Deposit order
    if ($order['category'] == 'Deposit') {
        $stmt = $pdo->prepare("UPDATE users SET balance = balance + :amount WHERE id = :user_id");
        $stmt->execute(['amount' => $order['amount'], 'user_id' => $order['user_id']]);
    }

    // If it's a Cash-Out, no need to change the balance, just mark as completed

    // Mark the order as completed
    $stmt = $pdo->prepare("UPDATE orders SET status = 'Completed' WHERE order_id = :order_id");
    $stmt->execute(['order_id' => $order_id]);

    // Commit the transaction
    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Order successfully completed']);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Failed to complete order: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to complete the order']);
}

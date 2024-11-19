<?php
session_start();
require '../config.php'; // Include the database connection

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['rank'] < 10) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized action']);
    exit;
}

// Get the order ID from the POST request
$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : null;

if (!$order_id) {
    echo json_encode(['success' => false, 'message' => 'No order ID provided']);
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

    // If the order is already completed or canceled, prevent further changes
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

    // Refund balance if it's a Cash-Out order
    if ($order['category'] === 'Cash-Out') {
        // Fetch the user's current balance
        $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = :user_id FOR UPDATE");
        $stmt->execute(['user_id' => $order['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }

        // Refund the balance
        $new_balance = $user['balance'] + $order['amount'];
        $stmt = $pdo->prepare("UPDATE users SET balance = :new_balance WHERE id = :user_id");
        $stmt->execute([
            'new_balance' => $new_balance,
            'user_id' => $order['user_id']
        ]);
    }

    // Mark the order as canceled
    $stmt = $pdo->prepare("UPDATE orders SET status = 'Canceled' WHERE order_id = :order_id");
    $stmt->execute(['order_id' => $order_id]);

    // Commit the transaction
    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Order successfully canceled']);

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Failed to cancel order: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to cancel the order']);
}

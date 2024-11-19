<?php
require '../config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $message = $_POST['message'];
    $sender_id = $_SESSION['user_id']; // Admin or user ID

    if (empty($order_id) || empty($message) || empty($sender_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
        exit;
    }

    // Determine receiver (admin/user)
    $stmt = $pdo->prepare("SELECT user_id FROM orders WHERE order_id = :order_id");
    $stmt->execute(['order_id' => $order_id]);
    $order = $stmt->fetch();

    if (!$order) {
        echo json_encode(['status' => 'error', 'message' => 'Order not found']);
        exit;
    }

    // Admin ID assumed as 1
    $receiver_id = ($order['user_id'] == $sender_id) ? 1 : $order['user_id'];

    // Check if the same message with the same timestamp already exists (to prevent duplicate entries)
    try {
        $current_time = date('Y-m-d H:i:s');
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM chat_messages WHERE order_id = :order_id AND sender_id = :sender_id AND message = :message AND timestamp = :timestamp");
        $stmt->execute([
            'order_id' => $order_id,
            'sender_id' => $sender_id,
            'message' => htmlspecialchars($message),
            'timestamp' => $current_time
        ]);

        if ($stmt->fetchColumn() == 0) {
            // Insert the new message into chat_messages table
            $stmt = $pdo->prepare("INSERT INTO chat_messages (order_id, sender_id, receiver_id, message, timestamp) VALUES (:order_id, :sender_id, :receiver_id, :message, :timestamp)");
            $stmt->execute([
                'order_id' => $order_id,
                'sender_id' => $sender_id,
                'receiver_id' => $receiver_id,
                'message' => htmlspecialchars($message),
                'timestamp' => $current_time
            ]);

            // Return success response

        } else {
        }
    } catch (Exception $e) {
        error_log("Failed to send message: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Message sending failed']);
    }
}
?>

<?php
require '../config.php';
session_start();

// Get the order ID from the AJAX request
$order_id = $_GET['order_id'];

// Fetch chat messages for the order
$messages = [];

// Fetch the order user ID to compare sender ID with it
try {
    $stmt = $pdo->prepare("SELECT o.user_id, u.username FROM orders o JOIN users u ON o.user_id = u.id WHERE o.order_id = :order_id");
    $stmt->execute(['order_id' => $order_id]);
    $order_user = $stmt->fetch(PDO::FETCH_ASSOC);
    $order_user_id = $order_user['user_id']; // The user who created the order
    $order_username = $order_user['username']; // The username of the user who created the order
} catch (Exception $e) {
    error_log("Failed to fetch order user: " . $e->getMessage());
    exit('Error fetching order details');
}

// Fetch the chat messages for the order
try {
    $stmt = $pdo->prepare("SELECT * FROM chat_messages WHERE order_id = :order_id ORDER BY timestamp ASC");
    $stmt->execute(['order_id' => $order_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Failed to load chat: " . $e->getMessage());
}

// Display the chat messages
foreach ($messages as $message) {
    // Check if the logged-in user is an admin
    $is_admin = isset($_SESSION['rank']) && $_SESSION['rank'] >= 10;

    // Determine the sender name
    if ($message['sender_id'] == $_SESSION['user_id']) {
        // If the logged-in user is the one who sent the message
        $sender_name = 'You';
    } elseif ($message['sender_id'] == $order_user_id) {
        // If the message was sent by the order creator (user), display their username
        $sender_name = $order_username;
    } else {
        // Otherwise, it's the admin
        $sender_name = 'OSFP Admin';
    }

    // Apply different styles for user and admin messages
    $message_class = ($message['sender_id'] == $_SESSION['user_id']) ? 'user-message bg-green-500' : 'admin-message bg-blue-500';

    // Output each message with the correct styling
    echo "<div class='chat-message mb-2 {$message_class} p-2 rounded-lg'>";
    echo "<strong>{$sender_name}:</strong> " . htmlspecialchars($message['message']);
    echo "<small class='block text-gray-300'>" . date('M d, Y, h:i A', strtotime($message['timestamp'])) . "</small>";
    echo "</div>";
}
?>

<?php
session_start();
require 'config.php'; // Include the database connection
include('header.php'); // Keep the header

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get the order ID from the URL
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;

if (!$order_id) {
    echo "No order ID provided.";
    exit;
}

// Fetch the order details for the order widget
$order_details = [];
try {
    $stmt = $pdo->prepare("SELECT orders.*, users.username, users.rsn FROM orders INNER JOIN users ON orders.user_id = users.id WHERE orders.order_id = :order_id");
    $stmt->execute(['order_id' => $order_id]);
    $order_details = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Failed to load order details: " . $e->getMessage());
}

// Fetch chat messages associated with the order
$messages = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE order_id = :order_id ORDER BY timestamp ASC");
    $stmt->execute(['order_id' => $order_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Failed to load chat messages: " . $e->getMessage());
}

// Check if the order is completed or canceled
if ($order_details['status'] == 'Completed' || $order_details['status'] == 'Canceled') {
    echo "<div class='text-center mt-20 text-3xl font-bold text-red-500'>This order is " . $order_details['status'] . ", please make a new order.</div>";
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat for Order <?php echo $order_id; ?></title>
    <script src="https://cdn.tailwindcss.com"></script> <!-- Tailwind CDN for styles -->
    <style>
        /* Make the entire page unscrollable */
        html, body {
            overflow: hidden; /* Prevent page from scrolling */
            height: 100%;
            margin: 0;
        }

        .chat-message {
            max-width: 70%; /* Limit the bubble width */
            word-wrap: break-word; /* Ensure long messages wrap properly */
        }

        .user-message {
            align-self: flex-end;
            background-color: #34d399;
        }

        .admin-message {
            align-self: flex-start;
            background-color: #3b82f6;
        }

        /* Chat container adjustments */
        .chat-container {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 200px); /* Adjust this to control the height of the chat */
            overflow-y: auto; /* Allow chat box to scroll */
            padding-right: 12px; /* Add padding to the right to move scrollbar */
        }

        /* Custom scrollbar styling */
        .chat-container::-webkit-scrollbar {
            width: 8px; /* Width of the scrollbar */
        }

        .chat-container::-webkit-scrollbar-track {
            background: #2d2f36; /* Track (background) color */
        }

        .chat-container::-webkit-scrollbar-thumb {
            background-color: #4b5563; /* Thumb (handle) color */
            border-radius: 10px; /* Rounded corners */
        }

        .chat-container::-webkit-scrollbar-thumb:hover {
            background-color: #6b7280; /* Darker on hover */
        }

        .chat-box {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* Adjustments to the layout */
        .chat-section {
            width: 70%; /* Make the chat section wider */
        }

        .order-details-section {
            width: 30%; /* Make the order details section smaller */
        }

        /* History table styles */
        .history-container {
            margin-top: 20px;
        }
        .history-table {
            width: 100%;
            background-color: #1f2937;
            border-radius: 10px;
            padding: 20px;
        }
        .history-table td {
            padding: 10px;
            color: #d1d5db;
        }
        .history-table td.amount {
            text-align: right;
        }
    </style>
</head>
<body class="bg-gray-900 text-white">
    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- Chat Section -->
        <div class="chat-section p-4">
            <div class="bg-gray-800 h-full p-6 rounded-lg shadow-md flex flex-col">
                <div class="chat-container" id="chat-box">
                    <?php foreach ($messages as $message): ?>
                        <div class="chat-message mb-2 <?php echo $message['sender_id'] == $_SESSION['user_id'] ? 'user-message' : 'admin-message'; ?> p-2 rounded-lg">
                            <strong><?php echo $message['sender_id'] == $_SESSION['user_id'] ? 'You' : 'OSFP Admin'; ?>:</strong> 
                            <?php echo htmlspecialchars($message['message']); ?>
                            <small class="block text-gray-300"><?php echo date('M d, Y, h:i A', strtotime($message['timestamp'])); ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
                <form id="send-message-form" class="flex space-x-2 mt-4">
                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                    <input id="message-input" type="text" name="message" placeholder="Say something..." autocomplete="off" class="flex-grow p-2 rounded-lg bg-gray-700 text-white" required>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Send</button>
                </form>
            </div>
        </div>

        <!-- Order Details Section -->
        <div class="order-details-section p-4">
            <div class="bg-gray-800 p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-4">Order Details</h2>
                <div class="mb-4">
                    <p><strong>Order ID:</strong> <?php echo $order_id; ?></p>
                    <p><strong>Username:</strong> <?php echo $order_details['username']; ?></p>
                    <p><strong>RSN:</strong> <?php echo $order_details['rsn']; ?></p>
                    <p><strong>Category:</strong> <?php echo ucfirst($order_details['category']); ?></p>
                    <p><strong>Amount:</strong> <?php echo number_format($order_details['amount'], 2); ?>M</p>
                    <p><strong>Status:</strong> <?php echo $order_details['status']; ?></p>
                    <p><strong>Created At:</strong> <?php echo date('M d, Y, h:i A', strtotime($order_details['created_at'])); ?></p>
                </div>

                <!-- Complete/Cancel buttons for admin -->
                <?php if (isset($_SESSION['rank']) && $_SESSION['rank'] >= 10): ?>
                    <div class="mt-6 flex flex-col space-y-4">
                        <button class="bg-green-500 text-white px-4 py-2 rounded-lg complete-order" data-order-id="<?php echo $order_id; ?>">Complete</button>
                        <button class="bg-red-500 text-white px-4 py-2 rounded-lg cancel-order" data-order-id="<?php echo $order_id; ?>">Cancel</button>
                    </div>
                <?php endif; ?>
            </div>

<!-- Order History Section within the Card -->
<div class="bg-gray-800 p-6 rounded-lg shadow-md mt-6">
    <h2 class="text-xl font-bold mb-4 text-white">Order History</h2>

    <div class="order-history-container" style="max-height: 300px; overflow-y: auto;"> <!-- Added scrolling for overflow -->
    <?php
    // Fetch order history for the user
    $user_id = $order_details['user_id'];
    $history = [];
    try {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->execute(['user_id' => $user_id]);
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Failed to fetch order history: " . $e->getMessage());
    }

    if (!empty($history)): 
        foreach ($history as $index => $past_order): 
            // Alternate background colors
            $bg_class = $index % 2 == 0 ? 'bg-gray-700' : 'bg-gray-800';
    ?>
        <div class="p-4 <?php echo $bg_class; ?> rounded-lg mb-2">
            <table class="min-w-full text-sm text-white">
                <tr class="text-white">
                    <!-- Horizontal layout for Order Name, Status, and Amount -->
                    <td class="font-bold pr-4">Order Name:</td>
                    <td class="font-bold pr-4">Status:</td>
                    <td class="font-bold">Amount:</td>
                </tr>
                <tr class="text-gray-400">
                    <td><?php echo ucfirst($past_order['category']); ?></td>
                    <td><?php echo $past_order['status']; ?></td>
                    <td class="amount">
                        <?php if ($past_order['category'] == 'Cash-Out'): ?>
                            <span class="text-red-500">- <?php echo number_format($past_order['amount'], 2); ?>M</span>
                        <?php else: ?>
                            <span class="text-green-500">+ <?php echo number_format($past_order['amount'], 2); ?>M</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
    <?php 
        endforeach; 
    else: 
    ?>
        <p class="text-gray-500">No previous orders found.</p>
    <?php endif; ?>
    </div> <!-- End of order-history-container -->
</div>



    <!-- Include necessary JS for the chat -->
    <script src="scripts/chat.js"></script>

    <script>
        // Handling Complete and Cancel buttons for admins
        document.querySelector('.complete-order').addEventListener('click', function() {
            const order_id = this.getAttribute('data-order-id');
            if (confirm('Are you sure you want to mark this order as complete?')) {
                fetch('widgets/complete_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `order_id=${order_id}`
                })
                .then(response => response.json())
                .then(data => {
            if (data.success) {
                alert('Order completed successfully!');
                location.reload(); // Reload the page after completing the order
            } else {
                alert('Error: ' + data.error);
            }
        });
    }
});

        document.querySelector('.cancel-order').addEventListener('click', function() {
            const order_id = this.getAttribute('data-order-id');
            if (confirm('Are you sure you want to cancel this order?')) {
                fetch('widgets/cancel_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `order_id=${order_id}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                        const contentArea = document.querySelector('.content');
                        contentArea.innerHTML = '<div class="text-center mt-20 text-3xl font-bold text-red-500">This order is canceled, please make a new order.</div>';

                    } else {
                        alert('Error: ' + data.error);
                    }
                });
            }
        });

        // Handling message sending and auto scroll
        const messageForm = document.getElementById('send-message-form');
        const messageInput = document.getElementById('message-input');
        const chatBox = document.getElementById('chat-box');

        // Function to scroll to the bottom of the chat
        function scrollToBottom() {
            chatBox.scrollTop = chatBox.scrollHeight;
        }



        // Handle message submission via AJAX and clear input
        messageForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const message = messageInput.value.trim();
            if (message === '') return; // Don't send empty messages

            const order_id = messageForm.querySelector('input[name="order_id"]').value;

            // Disable input during sending
            messageInput.disabled = true;
            fetch('widgets/send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `order_id=${order_id}&message=${encodeURIComponent(message)}`
            })
            .then(response => response.text())
            .then(data => {
                // Clear the input and re-enable it
                messageInput.value = '';
                messageInput.disabled = false;

                // Append the new message to the chat box dynamically
                chatBox.innerHTML += data;
                scrollToBottom();  // Scroll to the bottom after appending the message
            })
            .catch(error => {
                console.error('Error:', error);
                messageInput.disabled = false; // Re-enable input on error
            });
        });

        // Initial scroll to bottom when the chat page loads
        window.onload = scrollToBottom;
    </script>
</body>
</html>

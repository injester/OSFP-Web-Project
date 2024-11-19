<?php
// Start session if it is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'config.php'; // Include the database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    return; // Do not show if the user is not logged in
}

$user_id = $_SESSION['user_id'];

// Fetch the user's orders from the database
$orders = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmt->execute(['user_id' => $user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Failed to fetch orders: " . $e->getMessage());
}
?>

<!-- Order History Card -->
<div class="bg-gray-900 p-6 rounded-lg shadow-md w-full">
    <!-- Search Bar -->
    <div class="mb-4">
        <input type="text" id="order-search" class="w-full p-2 rounded bg-gray-700 text-white" placeholder="Search Orders by ID, Category, Status, etc.">
    </div>

    <div class="bg-gray-800 p-6 rounded-lg shadow-md w-full">
        <h2 class="text-2xl font-bold text-white mb-4 text-center">Order History</h2>
        <table class="min-w-full bg-gray-800 text-white text-center">
            <thead>
                <tr>
                    <th class="py-4 px-6 text-center">Order ID</th>
                    <th class="py-4 px-6 text-center">Order Name</th>
                    <th class="py-4 px-6 text-center">RSN</th>
                    <th class="py-4 px-6 text-center">Ordered Date</th>
                    <th class="py-4 px-6 text-center">Order Status</th>
                    <th class="py-4 px-6 text-center">Amount</th>
                    <th class="py-4 px-6 text-center">Chat</th>
                </tr>
            </thead>
            <tbody id="order-history-body">
    <?php if (!empty($orders)): ?>
        <?php foreach ($orders as $order): ?>
            <tr class="border-b border-gray-700">
                <td class="py-4 px-6 text-center"><?php echo $order['order_id']; ?></td> <!-- Order ID -->
                <td class="py-4 px-6 text-center"><?php echo ucfirst($order['category']); ?></td> <!-- Order name -->
                <td class="py-4 px-6 text-center"><?php echo $_SESSION['username']; ?></td> <!-- RSN -->
                <td class="py-4 px-6 text-center"><?php echo date('M d, Y, h:i A', strtotime($order['created_at'])); ?></td> <!-- Ordered date -->
                <td class="py-4 px-6 text-center">
                    <?php if ($order['status'] == 'Completed'): ?>
                        <span class="bg-green-500 text-white px-2 py-1 rounded-full"><?php echo $order['status']; ?></span>
                    <?php elseif ($order['status'] == 'Canceled'): ?>
                        <span class="bg-red-500 text-white px-2 py-1 rounded-full"><?php echo $order['status']; ?></span>
                    <?php else: ?>
                        <span class="bg-yellow-500 text-white px-2 py-1 rounded-full"><?php echo $order['status']; ?></span>
                    <?php endif; ?>
                </td>
                <td class="py-4 px-6 text-center">
                    <!-- Display amount dynamically with color based on category -->
                    <?php if ($order['category'] == 'Cash-Out'): ?>
                        <span class="text-red-500">- <?php echo number_format($order['amount'], 2); ?> M</span>
                    <?php elseif ($order['category'] == 'Buy' || $order['category'] == 'Deposit'): ?>
                        <span class="text-green-500">+ <?php echo number_format($order['amount'], 2); ?> M</span>
                    <?php endif; ?>
                </td>
                <td class="py-4 px-6 text-center">
                    <!-- Redirect to chat page for Cash-Out and Deposit -->
                    <?php if ($order['category'] != 'Buy'): ?>
                        <a href="chat.php?order_id=<?php echo $order['order_id']; ?>" class="bg-blue-500 text-white px-2 py-1 rounded">Chat</a>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td colspan="6">
                    <hr class="border-gray-600"> <!-- Fine line after each row -->
                </td>
            </tr>
        <?php endforeach; ?>
        <?php else: ?>
    <tr>
        <td colspan="7" class="py-4 text-center text-gray-500">No orders found</td>
    </tr>
<?php endif; ?>

</tbody>

        </table>
    </div>
</div>

<script>
// Search functionality
document.getElementById('order-search').addEventListener('input', function () {
    const searchQuery = this.value.toLowerCase();
    const orderRows = document.querySelectorAll('#order-history-body tr');

    orderRows.forEach(row => {
        const rowText = row.innerText.toLowerCase();
        row.style.display = rowText.includes(searchQuery) ? '' : 'none';
    });
});
</script>

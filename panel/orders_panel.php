<?php
// Fetch all orders from the database
$orders = [];
try {
    $stmt = $pdo->query("SELECT * FROM orders ORDER BY FIELD(status, 'Pending') DESC, created_at ASC");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Failed to fetch orders: " . $e->getMessage());
}
?>

<div class="bg-gray-900 p-6 rounded-lg shadow-md w-full">
    <h2 class="text-2xl font-bold text-white mb-4">Manage Orders</h2>
    <input type="text" id="search-orders" class="w-full p-2 mb-4 rounded bg-gray-700 text-white" placeholder="Search by Order ID, Category, Amount, or Status">

    <table class="min-w-full bg-gray-800 text-white text-center">
        <thead>
            <tr class="bg-gray-700">
                <th>Order ID</th>
                <th>Category</th>
                <th>Amount</th>
                <th>Status</th>
                <th>User ID</th>
                <th>Ordered Date</th>
                <th>Chat</th>
            </tr>
        </thead>
        <tbody id="order-table">
            <?php foreach ($orders as $index => $order): ?>
                <tr class="<?php echo $index % 2 === 0 ? 'bg-gray-700' : 'bg-gray-800'; ?>">
                    <td><?php echo $order['order_id']; ?></td>
                    <td><?php echo ucfirst($order['category']); ?></td>
                    <td><?php echo number_format($order['amount'], 2); ?>M</td>
                    <td>
                        <span class="<?php echo $order['status'] == 'Completed' ? 'text-green-500' : ($order['status'] == 'Canceled' ? 'text-red-500' : 'text-yellow-500'); ?>">
                            <?php echo $order['status']; ?>
                        </span>
                    </td>
                    <td><?php echo $order['user_id']; ?></td>
                    <td><?php echo date('M d, Y, h:i A', strtotime($order['created_at'])); ?></td>
                    <td>
                        <a href="chat.php?order_id=<?php echo $order['order_id']; ?>">
                            <img src="assets/chat.png" alt="Chat" width="24" height="24">
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
document.getElementById('search-orders').addEventListener('input', function () {
    const query = this.value.toLowerCase().trim();
    const rows = document.querySelectorAll('#order-table tr');
    rows.forEach(row => {
        const rowText = Array.from(row.cells).map(cell => cell.innerText.toLowerCase()).join(' ');
        row.style.display = rowText.includes(query) ? '' : 'none';
    });
});
</script>

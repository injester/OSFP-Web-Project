<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];

    // Get the user's balance
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch();
    $user_balance = $user ? $user['balance'] : 0;
} else {
    $user_balance = 0;
    $username = 'Guest';
}
?>

<div class="bg-gray-800 p-6 rounded-lg shadow-md max-w-md w-full">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h2 class="text-3xl font-bold text-white">Cash-Out</h2>
        </div>
        <div class="text-white flex items-center">
            <?php if ($is_online): ?>
                <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse mr-2" title="Online"></div> <!-- Online indicator -->
            <?php else: ?>
                <div class="w-3 h-3 bg-red-500 rounded-full mr-2" title="Offline"></div> <!-- Offline indicator -->
            <?php endif; ?>
            <span><?php echo $is_online ? "Online" : "Offline"; ?></span> <!-- Tooltip with Online/Offline status -->
        </div>
    </div>

    <div class="mb-6">
        <p class="text-gray-400">Current Stock: <span class="text-green-500"><?php echo $formatted_stock; ?></span></p>
        <p class="text-gray-400">Cash your GP to OSRS!</p>
    </div>

    <div class="bg-gray-700 p-4 rounded-lg">
        <div class="flex justify-between items-center mb-4">
            <div class="text-white">
                <span class="text-xl">Amount to Cash-Out</span>
            </div>
            <div class="text-white flex items-center space-x-4">
                <button id="cashout-decrease" class="bg-gray-600 p-2 rounded">-</button>
                <input id="cashout-quantity" type="number" value="10" min="10" max="<?php echo $user_balance; ?>" class="w-16 text-center bg-gray-800 p-2 rounded">
                <button id="cashout-increase" class="bg-gray-600 p-2 rounded">+</button>
            </div>
        </div>

        <button id="cashout-btn" class="w-full bg-yellow-500 py-2 rounded hover:bg-yellow-400">Cash Out</button>
    </div>
    <p class="text-gray-500 text-xs mt-4">Minimum cash-out price is 10M</p>
</div>

<script>
    // Ensure the value doesn't drop below 10 or exceed the user's balance
    document.getElementById('cashout-decrease').addEventListener('click', function() {
        var input = document.getElementById('cashout-quantity');
        var currentValue = parseInt(input.value);
        if (currentValue > 10) {
            input.value = currentValue - 1;
        }
    });

    document.getElementById('cashout-increase').addEventListener('click', function() {
        var input = document.getElementById('cashout-quantity');
        var currentValue = parseInt(input.value);
        var maxBalance = parseInt(input.max); // max balance

        if (currentValue < maxBalance) {
            input.value = currentValue + 0;
        }
    });

    document.getElementById('cashout-btn').addEventListener('click', function() {
        var amount = document.getElementById('cashout-quantity').value;

        // Send the cashout request via AJAX
        fetch('widgets/cashout_process.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'amount=' + amount
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);  // Show success or error message

            if (data.success) {
                location.reload();  // Reload the page on success
            }
        })
        .catch(err => {
            console.error('Error:', err);
        });
    });

    // Allow typing directly into the amount field
    document.getElementById('cashout-quantity').addEventListener('input', function() {
        var input = document.getElementById('cashout-quantity');
        var value = parseInt(input.value);
        var minValue = 10;
        var maxBalance = parseInt(input.max);

        // Ensure the value stays within min/max limits
        if (value < minValue) {
            input.value = minValue;
        } else if (value > maxBalance) {
            input.value = maxBalance;
        }
    });
</script>

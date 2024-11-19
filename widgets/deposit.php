<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize variables for guests or non-logged-in users
$user_id = null;
$username = 'Guest';
$user_balance = 0; // Default balance for non-logged-in users

// Check if the user is logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];

    // Get the user's balance
    require 'config.php'; // Ensure the database connection is included

    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user data was found
    if ($user && isset($user['balance'])) {
        $user_balance = $user['balance'];
    } else {
        $user_balance = 0; // Set balance to 0 if not found
    }
}
?>
<div class="bg-gray-800 p-6 rounded-lg shadow-md max-w-md w-full">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h2 class="text-3xl font-bold text-white">Deposit OSRS Mills</h2>
        </div>
    </div>

    <div class="mb-6">
        <p class="text-gray-400">Current Balance: <span class="text-green-500"><?php echo number_format($user_balance, 1); ?>M</span></p>
        <p class="text-gray-400">Deposit your in-game currency to your balance.</p>
    </div>

    <div class="bg-gray-700 p-4 rounded-lg">
        <div class="flex justify-between items-center mb-4">
            <div class="text-white">
                <span class="text-xl">Amount to Deposit</span>
            </div>
            <div class="text-white flex items-center space-x-4">
                <button id="deposit-decrease" class="bg-gray-600 p-2 rounded">-</button>
                <input id="deposit-quantity" type="number" value="10" min="10" class="w-16 text-center bg-gray-800 p-2 rounded">
                <button id="deposit-increase" class="bg-gray-600 p-2 rounded">+</button>
            </div>
        </div>

        <button id="deposit-btn" class="w-full bg-yellow-500 py-2 rounded hover:bg-yellow-400">
            Deposit
        </button>
    </div>
    <p class="text-gray-500 text-xs mt-4">Minimum deposit is 10M</p>
</div>

<script>
// Increase and decrease functionality
document.getElementById('deposit-decrease').addEventListener('click', function() {
    var input = document.getElementById('deposit-quantity');
    var currentValue = parseInt(input.value);
    if (currentValue > 10) {
        input.value = currentValue - 1;
    }
});

document.getElementById('deposit-increase').addEventListener('click', function() {
    var input = document.getElementById('deposit-quantity');
    var currentValue = parseInt(input.value);
    input.value = currentValue + 1;
});

// Handle deposit button click
document.getElementById('deposit-btn').addEventListener('click', function() {
    var amount = document.getElementById('deposit-quantity').value;

    // Send the deposit request via AJAX
    fetch('widgets/deposit_process.php', {
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
</script>

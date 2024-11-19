<?php
// Test mode flag - set to true for testing with Linea ETH
$testMode = true;

// Set the appropriate wallet and network based on test mode
if ($testMode) {
    $my_wallet_address = "0xc2307e1278664DE9d61E29E6335008A39a6c34f3"; // Linea Test Wallet
    $etherscan_base_url = "https://sepolia.lineascan.build/tx/"; // Linea Test Network URL
} else {
    $my_wallet_address = "0xc2307e1278664DE9d61E29E6335008A39a6c34f3"; // Mainnet Wallet
    $etherscan_base_url = "https://etherscan.io/tx/"; // Ethereum Mainnet URL
}

// Check if session is already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Default values if user is not logged in
$is_logged_in = isset($_SESSION['user_id']);
$username = $is_logged_in ? $_SESSION['username'] : 'Guest';
$user_balance = 0;

// Fetch user balance if logged in
if ($is_logged_in) {
    // Get user details
    $user_id = $_SESSION['user_id'];
    require 'config.php'; // Ensure the config is included here
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch();
    $user_balance = $user['balance'];
}

// Fetch price per million from the database
$stmt = $pdo->prepare("SELECT price_per_million FROM price LIMIT 1");
$stmt->execute();
$price_row = $stmt->fetch();
$price_per_million = $price_row['price_per_million'];
?>
<script src="https://cdn.jsdelivr.net/npm/web3@1.6.0/dist/web3.min.js"></script>

<!-- Frontend for the buy widget -->
<div class="bg-gray-800 p-6 rounded-lg shadow-md max-w-md w-full">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h2 class="text-3xl font-bold text-white">OSFP</h2>
        </div>
        <div class="text-white">
            <span><?php echo $username; ?></span> <!-- Display username or 'Guest' -->
        </div>
    </div>

    <div class="mb-6">
        <p class="text-gray-400">Current Balance: <span class="text-green-500"><?php echo number_format($user_balance, 1); ?>M</span></p>
        <p class="text-gray-400">Top up your website balance using ETH payments.</p>
    </div>

    <!-- Price Calculation -->
    <div class="bg-gray-700 p-4 rounded-lg">
        <div class="flex justify-between items-center mb-4">
            <div class="text-white">
                <span class="text-2xl">€<span id="price" data-price-per-million="<?php echo $price_per_million; ?>"><?php echo number_format($price_per_million * 10, 2); ?></span></span>
                <span class="text-gray-400">/ Total</span>
            </div>
            <div class="text-white flex items-center space-x-4">
                <!-- Quantity controls -->
                <button id="decrease" class="bg-gray-600 p-2 rounded">-</button>
                <input id="quantity" type="number" value="10" min="10" class="w-16 text-center bg-gray-800 p-2 rounded">
                <button id="increase" class="bg-gray-600 p-2 rounded">+</button>
            </div>
        </div>

        <!-- Buy button -->
        <button id="buy-now" class="w-full bg-yellow-500 py-2 rounded hover:bg-yellow-400">
            Buy Now with ETH
        </button>
    </div>

    <p class="text-gray-500 text-xs mt-4">Payments are done with Metamask via <?php echo $testMode ? 'Linea Test Network' : 'Ethereum Mainnet'; ?>!</p>
</div>

<!-- Modal for transaction status -->
<div id="transaction-modal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center" style="display: none;">
    <div class="bg-gray-800 p-6 rounded-lg shadow-lg text-center relative">
        <img src="assets/metamask.png" alt="Metamask" class="mx-auto mb-4" width="64" height="64">
        <p id="transaction-message" class="text-white text-lg">Waiting for user to confirm...</p>
        <div id="loader" class="mt-4">
            <!-- Add a CSS spinner loader -->
            <div class="loader"></div>
        </div>
        <!-- Success or Failure Icons -->
        <div id="result-icon" class="hidden mt-4">
            <div class="success-icon hidden mx-auto">
                <div class="checkmark-circle">
                    <div class="checkmark"></div>
                </div>
            </div>
            <div class="failure-icon hidden mx-auto">
                <div class="cross-circle">
                    <div class="cross"></div>
                </div>
            </div>
        </div>
        <!-- Close Button -->
        <img src="assets/close.png" id="close-button" class="absolute top-2 right-2 cursor-pointer hidden" width="24" height="24">
    </div>
</div>

<style>
/* Basic CSS for loader */
.loader {
    border: 4px solid #f3f3f3;
    border-radius: 50%;
    border-top: 4px solid #3498db;
    width: 40px;
    height: 40px;
    animation: spin 2s linear infinite;
    margin: 0 auto; /* Ensure it's perfectly centered */
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Modal styles */
#transaction-modal {
    z-index: 9999;
    display: none; /* Initially hidden */
    width: 100vw; /* Ensure modal covers entire width */
    height: 100vh; /* Ensure modal covers entire height */
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: rgba(0, 0, 0, 0.7); /* Darken the background without blur */
}

/* Style for close button */
#close-button {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 24px;
    height: 24px;
    cursor: pointer;
}

/* Success and failure icon styles */
.checkmark-circle, .cross-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto; /* Centering the icons */
}

.checkmark-circle {
    background-color: #4CAF50;
}

.cross-circle {
    background-color: #F44336;
}

.checkmark, .cross {
    width: 30px;
    height: 30px;
}

.checkmark {
    border: solid white;
    border-width: 0 4px 4px 0;
    transform: rotate(45deg);
    width: 20px;
    height: 20px;
}

.cross {
    position: relative;
}

.cross::before, .cross::after {
    content: '';
    position: absolute;
    width: 4px;
    height: 30px;
    background-color: white;
}

.cross::before {
    transform: rotate(45deg);
}

.cross::after {
    transform: rotate(-45deg);
}

/* Ensure loader is perfectly centered */
#loader {
    display: flex;
    justify-content: center;
    align-items: center;
}
</style>



<!-- Link to the external buy.js script -->
<script src="scripts/buy.js"></script>
<script>
    const isLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
    const userId = <?php echo $is_logged_in ? $_SESSION['user_id'] : 'null'; ?>;
    const testMode = <?php echo $testMode ? 'true' : 'false'; ?>;
    const etherscanBaseUrl = '<?php echo $etherscan_base_url; ?>';
    const myWalletAddress = '<?php echo $my_wallet_address; ?>'; // Correctly pass wallet address as a string
</script>


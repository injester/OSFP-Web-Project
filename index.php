<?php
session_start();
require 'config.php'; // Include the database connection

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$username = $is_logged_in ? $_SESSION['username'] : null;
$user_balance = 0;

// If the user is logged in, fetch their balance
if ($is_logged_in) {
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();
    $user_balance = $user['balance'];
}

// Query to get all users with rank 10 or higher for stock info
$stmt = $pdo->prepare("SELECT username, balance, is_online FROM users WHERE rank >= 10");
$stmt->execute();
$users = $stmt->fetchAll();

// Fetch the current price per million from the database
$price_stmt = $pdo->query("SELECT price_per_million FROM price WHERE id = 1");
$price_per_million = $price_stmt->fetchColumn();

// Initialize variables
$total_stock = 0;
$is_online = false;

// Calculate total stock from balance and check online status
foreach ($users as $user) {
    $total_stock += $user['balance'];
    if ($user['is_online']) {
        $is_online = true;
    }
}

// Format stock as M (millions)
$formatted_stock = number_format($total_stock, 1) . 'M'; // Display stock in millions

// Delivery time status
$delivery_status = $is_online ? 
    "<span class='text-green-500'>Guaranteed delivery time: 10 min, Average: 3 min</span>" : 
    "<span class='text-red-500'>Offline</span>";

include('header.php'); // Header will handle logged-in status
?>

<!-- Main OSRS Mills Selling, Cash-Out, and Deposit UI -->
<div class="min-h-screen bg-gray-900 flex flex-col items-center space-y-8 relative">
    <!-- Add Image to Left Side -->
    <div class="absolute left-0 top-1/2 transform -translate-y-1/2 fp-guy-container">
        <img src="assets/fpguy.webp" alt="FP Guy" class="fp-guy">
    </div>

    <!-- Full Width Welcome Card -->
    <div class="welcome-card bg-yellow-500 text-white rounded-lg p-6 w-full shadow-md flex justify-center items-center">
        <div class="text-center">
            <h2 class="text-3xl font-bold mb-4">Welcome to OSFP</h2>
            <p class="mb-4">Safe and Easy Gambling</p>
            <p>Trade without fear - OSFP guarantees that all trades are legit and keeps you safe from scammers.</p>
            <p>It's quick and easy - You can top up your balance and gamble or directly cash out to OSRS!</p>
            <div class="flex justify-center mt-4">
    <a href="play.php" class="play-now-button flex items-center space-x-2 bg-blue-600 text-white py-3 px-6 rounded-full text-lg font-bold hover:bg-blue-700 transition duration-300 ease-in-out">
        <img src="assets/play.png" alt="Play Icon" class="w-6 h-6"> <!-- Play icon -->
        <span>Play Now</span>
    </a>
</div>
        </div>
    </div>
    <!-- Add Play Now Button -->



    <!-- Display Current Price -->

    <div class="flex justify-center space-x-8 w-full mt-8"> <!-- Flex container for side by side layout -->
        <!-- Include Buy Widget -->
        <?php include('widgets/buy.php'); ?>

        <!-- Include Cash-Out Widget -->
        <?php include('widgets/cashout.php'); ?>

        <!-- Include Deposit Widget -->
        <?php include('widgets/deposit.php'); ?>
    </div>

    <!-- Show either History or the Register button based on login status -->
    <div class="w-full flex justify-center mt-8">
        <?php if ($is_logged_in): ?>
            <?php include('widgets/history.php'); ?>
        <?php else: ?>
            <a href="register.php" class="bg-blue-500 text-white py-4 px-8 rounded-full text-lg font-bold animate-bounce hover:bg-blue-600 transition duration-300 ease-in-out">
                Register and play Flower Poker today!
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Remove arrows from number input fields -->
<style>
    /* Breathing effect for Play Now button */
@keyframes breathing {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.8; }
}

.play-now-button {
    background-color: #3b82f6; /* Blue color for the button */
    animation: breathing 2s ease-in-out infinite;
}

    /* Hide the arrows for number inputs */
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type="number"] {
        -moz-appearance: textfield; /* For Firefox */
    }

    /* Style the welcome card */
    .welcome-card {
        background-color: #f39c12; /* Set to #f39c12 for the welcome card */
        color: white;
        width: 100%; /* Make the card full width */
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px 20px;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    /* FP guy container - anchored using absolute position within the page content */
    .fp-guy-container {
        position: absolute; /* Ensures the FP guy is positioned relative to its nearest positioned ancestor */
        top: 430px; /* Adjust this to control the vertical position */
        left: 0; /* Anchored to the left */
        transform: translateY(-50%) scale(0.83); /* Center it vertically based on its height */
        z-index: 2; /* Keep it above other elements */
        pointer-events: none; /* Ensure it doesn’t block any interaction */
    }

    .fp-guy {
        width: 250px; /* Adjust size */
        height: auto;
        background-color: transparent;
    }

    /* Add animation for register button */
    .animate-bounce {
        margin-bottom: 40px;
        animation: bounce 3s infinite;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(100px);
        }
        40% {
            transform: translateY(130px);
        }
        60% {
            transform: translateY(115px);
        }
    }
</style>

<!-- Link to the external index.js script -->
<script src="scripts/index.js"></script>
<script src="scripts/history.js"></script>

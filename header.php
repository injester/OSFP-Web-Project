<?php
// Check if the session has already been started
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session if not already started
}

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$username = $is_logged_in ? $_SESSION['username'] : null;
$user_balance = 0;
$is_admin = false; // Default admin status

// Fetch user details if logged in
if ($is_logged_in) {
    require 'config.php'; // Include database connection
    $stmt = $pdo->prepare("SELECT balance, rank FROM users WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();
    $user_balance = $user['balance'];
    $is_admin = $user['rank'] >= 10; // Admin if rank 10
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OSFP - Old School RuneScape Flower Poker</title>
    <link rel="icon" href="assets/home.png" type="image/x-icon">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .nav-item {
            background-color: #111827;
            transition: transform 0.2s, background-color 0.2s, border 0.2s;
            border-radius: 8px;
            padding: 10px;
            position: relative;
        }

        .nav-item:hover {
            transform: scale(1.1); /* Slight expansion */
            background-color: #1f2937;
            border: 1px solid #4b5563;
        }

        .tooltip {
            position: absolute;
            bottom: -30px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #1f2937;
            color: #f3f4f6;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0.2s ease;
            white-space: nowrap;
            z-index: 10;
        }

        .nav-item:hover .tooltip {
            opacity: 1;
            visibility: visible;
        }

        .balance-section {
            background-color: #111827;
            border: 1px solid #4b5563;
            padding: 10px 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: transform 0.2s ease;
        }

        .balance-section:hover {
            transform: scale(1.1);
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0; /* Aligns the dropdown to the right */
            background: #111827;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 180px;
            z-index: 1000; /* Ensure it stays above other elements */
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }

        .no-pointer {
            pointer-events: none; /* Disables all pointer events */
        }
    </style>
</head>
<body class="bg-gray-900 text-white">
    <!-- Sticky Navbar -->
    <nav class="bg-gray-800 sticky top-0 z-50 px-4 py-3">
        <ul class="flex justify-end items-center space-x-4">
            <?php if ($is_logged_in): ?>
                <!-- Admin Panel (only for rank > 10) -->

                <!-- Balance Section -->
                <div class="balance-section no-pointer">
                    <img src="assets/wallet.png" alt="Wallet Icon" class="w-6 h-6">
                    <span>Balance:</span>
                    <span class="text-green-500"><?php echo number_format($user_balance, 2); ?>M</span>
                </div>

                    <!-- Admin Icon -->
                    <?php if ($is_admin): ?>
                        <a href="admin.php" class="nav-item">
                            <img src="assets/admin.png" alt="Admin Icon" class="w-6 h-6">
                            <div class="tooltip">Admin Panel</div>
                        </a>
                    <?php endif; ?>

                <!-- Home -->
                <a href="index.php" class="nav-item">
                    <img src="assets/home.png" alt="Home Icon" class="w-6 h-6">
                    <div class="tooltip">Home</div>
                </a>

                <!-- Play Now -->
                <a href="play.php" class="nav-item">
                    <img src="assets/play.png" alt="Play Now Icon" class="w-6 h-6">
                    <div class="tooltip">Play Now</div>
                </a>

                <!-- Rakeback -->
                <a href="rake.php" class="nav-item">
                    <img src="assets/rake.png" alt="Rakeback Icon" class="w-6 h-6">
                    <div class="tooltip">Rakeback Info</div>
                </a>

                <!-- Info Dropdown -->
                <div class="dropdown relative nav-item">
                    <a href="info.php" class="flex items-center">
                        <img src="assets/info.png" alt="Info Icon" class="w-6 h-6">
                    </a>
                    <div class="dropdown-menu">
                        <a href="info.php" class="flex items-center p-3 hover:bg-gray-700 rounded">
                            <img src="assets/learn.png" alt="Learn Icon" class="w-6 h-6 mr-2">
                            <span>Web Guide</span>
                        </a>
                        <a href="gamehelp.php" class="flex items-center p-3 hover:bg-gray-700 rounded">
                            <img src="assets/how.png" alt="How Icon" class="w-6 h-6 mr-2">
                            <span>Game Guide</span>
                        </a>
                        <a href="cryptohow.php" class="flex items-center p-3 hover:bg-gray-700 rounded">
                            <img src="assets/crypto.png" alt="Crypto Icon" class="w-6 h-6 mr-2">
                            <span>Crypto Guide</span>
                        </a>
                    </div>
                </div>

                <!-- Profile -->
                <a href="profile.php" class="nav-item">
                    <img src="assets/settings.png" alt="Profile Icon" class="w-6 h-6">
                    <div class="tooltip">Profile</div>
                </a>

                <!-- Logout -->
                <a href="widgets/logout.php" class="nav-item bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded flex items-center">
                    <img src="assets/logout.png" alt="Logout Icon" class="w-5 h-5 mr-2">Logout
                </a>
            <?php else: ?>
                <!-- Not Logged-in -->
                <a href="index.php" class="nav-item">
                    <img src="assets/home.png" alt="Home Icon" class="w-6 h-6">
                    <div class="tooltip">Home</div>
                </a>
                <div class="dropdown relative nav-item">
                    <a href="#" class="flex items-center">
                        <img src="assets/info.png" alt="Info Icon" class="w-6 h-6">
                    </a>
                    <div class="dropdown-menu">
                        <a href="info.php" class="flex items-center p-3 hover:bg-gray-700 rounded">
                            <img src="assets/learn.png" alt="Learn Icon" class="w-6 h-6 mr-2">
                            <span>Web Guide</span>
                        </a>
                        <a href="gamehelp.php" class="flex items-center p-3 hover:bg-gray-700 rounded">
                            <img src="assets/how.png" alt="How Icon" class="w-6 h-6 mr-2">
                            <span>Game Guide</span>
                        </a>
                        <a href="cryptohow.php" class="flex items-center p-3 hover:bg-gray-700 rounded">
                            <img src="assets/crypto.png" alt="Crypto Icon" class="w-6 h-6 mr-2">
                            <span>Crypto Guide</span>
                        </a>
                    </div>
                </div>
                <a href="login.php" class="nav-item bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded flex items-center">
                    <img src="assets/login.png" alt="Login Icon" class="w-5 h-5 mr-2">Login
                </a>
                <a href="register.php" class="nav-item bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">Register</a>
            <?php endif; ?>
        </ul>
    </nav>
</body>

</html>

    <!-- Content Section -->
    <?php if ($is_logged_in && basename($_SERVER['PHP_SELF']) !== 'chat.php'): ?>
        <?php include 'lobby.php'; ?>
    <?php endif; ?>
</body>
</html>

<script> 
    document.addEventListener('DOMContentLoaded', () => {
    const chatWidget = document.getElementById('chat-widget');
    const toggleChat = document.getElementById('toggle-chat');
    const contentArea = document.querySelector('.main-content'); // Update this selector based on your main content container

    let isOpen = false;

    if (window.location.pathname.includes('play.php')) {
        toggleChat.addEventListener('click', () => {
            isOpen = !isOpen;
            chatWidget.style.right = isOpen ? '0px' : '-385px';
            toggleChat.querySelector('img').src = isOpen ? 'assets/right.png' : 'assets/left.png';

            // Toggle the content-push class based on chat open/close
            if (isOpen) {
                contentArea.classList.add('content-push');
            } else {
                contentArea.classList.remove('content-push');
            }
        });
    }
});

</script>
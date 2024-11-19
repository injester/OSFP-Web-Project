<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
$user_balance = 0;
$total_winnings = 0;
$last_bet = 0;

if ($user_id) {
    // Fetch user balance
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_balance = $user ? $user['balance'] : 0;

    // Calculate total winnings by summing up all wins
    $stmt = $pdo->prepare("SELECT SUM(amount_played * 2) as total_winnings FROM history WHERE user_id = :id AND status = 'win'");
    $stmt->execute(['id' => $user_id]);
    $winnings = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_winnings = $winnings ? $winnings['total_winnings'] : 0;

    // Fetch the latest bet amount for the last bet
    $stmt = $pdo->prepare("SELECT amount_played, created_at FROM history WHERE user_id = :id ORDER BY created_at DESC LIMIT 1");
    $stmt->execute(['id' => $user_id]);
    $last_bet_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $last_bet = $last_bet_data ? $last_bet_data['amount_played'] : 0;
    $last_bet_time = $last_bet_data ? $last_bet_data['created_at'] : 'N/A';
}
?>

<script>
    // Using JSON encoding to safely pass PHP values to JavaScript
    let balance = <?php echo json_encode($user_balance ?? 0, JSON_NUMERIC_CHECK); ?>;
    let totalProfit = 0;
    let totalWagered = <?php echo json_encode($total_winnings ?? 0, JSON_NUMERIC_CHECK); ?>;
</script>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flower Poker Game</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Load custom RuneScape font */
        @font-face {
    font-family: 'RSFont';
    src: url('assets/rsfont.ttf') format('truetype');
}

#player-result-text, #house-result-text {
    font-family: 'RSFont', sans-serif;
    font-size: 1.2rem;
    color: #fff014; /* Gold color for results text */
}



        .slot {
            width: 50px;
            height: 50px;
            background-image: url('assets/slot.png');
            background-size: cover;
            margin: 8px;
        }

        .container {
            padding: 24px;
        }

        .bet-button {
            width: 80px;
            height: 80px;
        }

        h2, h3 {
            font-size: 2.2rem;
        }

        p {
            font-size: 1.5rem;
        }

        .game-section {
            display: flex;
            justify-content: space-between;
            max-width: 1700px;
            margin: 0 auto;
            padding: 30px;
        }

        .flex {
            display: flex;
            justify-content: center;
        }

        .slot-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            margin-top: 20px;
        }

        .gif-container img {
            width: 100%;
            max-width: 200px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        #bet-box {
            background-color: #1f2937;
            padding: 30px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .game-section > div {
            margin-left: 20px;
            margin-right: 20px;
        }

        input[type="number"] {
            text-align: center;
            color: #00ff00;
            background-color: #1f2937;
            border: none;
            font-size: 1.5rem;
            width: 100px;
            -moz-appearance: textfield;
        }

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .content-push {
    margin-right: 400px; /* Adjusts content width to match the chat's width */
}

    </style>

</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-black text-white overflow-hidden">


<div id="header-section">
    <?php include('header.php'); ?>
</div>

<div class="main-content">
<div class="min-h-screen flex items-center justify-center">
    <div class="bg-gray-800 pt-1 pr-10 pb-10 pl-10 rounded-lg w-full max-w-7xl">

            <h2 class="text-4xl font-bold text-center mb-10">Flower Poker Game</h2>

            <div class="game-section">
                <!-- Player Section -->
                <div class="w-1/3">
                    <div class="bg-gray-700 p-6 rounded-lg text-center">
                        <h3 class="text-3xl font-bold mb-6">Player</h3>
                        <div class="slot-container">
                            <div class="slot" id="slot1">
                                <img id="flower1" class="hidden" style="width: 50px; height: 50px;" />
                                <img id="seed1" class="hidden" src="assets/seed.png" onclick="plantFlower(1)">
                            </div>
                            <div class="slot" id="slot2">
                                <img id="flower2" class="hidden" style="width: 50px; height: 50px;" />
                                <img id="seed2" class="hidden" src="assets/seed.png" onclick="plantFlower(2)">
                            </div>
                            <div class="slot" id="slot3">
                                <img id="flower3" class="hidden" style="width: 50px; height: 50px;" />
                                <img id="seed3" class="hidden" src="assets/seed.png" onclick="plantFlower(3)">
                            </div>
                            <div class="slot" id="slot4">
                                <img id="flower4" class="hidden" style="width: 50px; height: 50px;" />
                                <img id="seed4" class="hidden" src="assets/seed.png" onclick="plantFlower(4)">
                            </div>
                            <div class="slot" id="slot5">
                                <img id="flower5" class="hidden" style="width: 50px; height: 50px;" />
                                <img id="seed5" class="hidden" src="assets/seed.png" onclick="plantFlower(5)">
                            </div>
                        </div>
                        <p id="player-result-text" class="text-yellow-400 rs-text">Player's results...</p>
                        <div class="mt-6 gif-container">
                            <img src="assets/timeout.png" id="player-gif">
                        </div>
                    </div>
                </div>

                <!-- Bet and Stats Section -->
                <div class="w-1/3 text-center">
                    <div id="bet-box" class="bg-gray-700 p-6 rounded-lg mt-6">
                        <h3 class="text-3xl font-bold mb-6">Place Your Bet</h3>
                        <div class="flex justify-center items-center space-x-6 mb-6">
                            <button id="decrease-bet" class="bg-gray-600 px-4 py-3 rounded">-</button>
                            <input type="number" id="bet-amount" value="10" class="bg-[#111827] text-white p-4 text-center w-32 rounded" min="10" max="500">
                            <button id="increase-bet" class="bg-gray-600 px-4 py-3 rounded">+</button>
                        </div>
                        <button id="place-bet" class="w-full bg-yellow-500 py-4 rounded hover:bg-yellow-400">Place Bet</button>
                        <div id="results" class="text-white mt-6">
                            <p id="player-hand" class="text-yellow-400"></p>
                            <p id="house-hand" class="text-yellow-400"></p>
                        </div>
                    </div>

                    <!-- Stats Box Only visible on place-bet -->
                    <div class="stat-container bg-[#111827] p-4 rounded-lg mb-4" id="win-rate-container" style="display: none;">
                        <p class="text-white">Open The Seeds</span></p>
                    </div>

                    <div class="stat-container bg-[#111827] p-4 rounded-lg mb-4" id="win-payout-container" style="display: none;">
                        <p class="text-white">Win Payout: <span id="win-payout" class="text-green-500">0.0M</span></p>
                    </div>

<div class="stat-container bg-[#111827] p-4 rounded-lg mb-4" id="profit-container" style="display: none;">
    <p class="text-white">Profit: <span id="profit" class="text-green-500">
        <?php 
            // Calculate total winnings (only winning bets)
            $stmt = $pdo->prepare("SELECT SUM(amount_played * 1.9) as total_wins FROM history WHERE user_id = :id AND status = 'win'");
            $stmt->execute(['id' => $user_id]);
            $total_wins = $stmt->fetchColumn();

            // Calculate total wagered (all bets placed)
            $stmt = $pdo->prepare("SELECT SUM(amount_played) as total_wagered FROM history WHERE user_id = :id");
            $stmt->execute(['id' => $user_id]);
            $total_wagered = $stmt->fetchColumn();

            // Calculate profit as winnings minus wagered amount
            $profit = $total_wins - $total_wagered;

            // Display the profit, formatted with 2 decimal places
            echo number_format($profit, 2); 
        ?>M
    </span></p>
</div>


                    <div class="stat-container bg-[#111827] p-4 rounded-lg mb-4" id="current-bet-container" style="display: none;">
                        <p class="text-white">Current Bet: <span id="current-bet" class="text-green-500">0.0M</span></p>
                    </div>

                    <div class="stat-container bg-[#111827] p-4 rounded-lg mb-4">
                        <p class="text-white">Last Bet: <span id="last-bet" class="text-green-500">
                            <?php echo $last_bet > 0 ? number_format($last_bet, 2) . "M" : "No bets yet"; ?>
                        </span></p>
                    </div>

                    <div class="stat-container bg-[#111827] p-4 rounded-lg mb-4">
                        <p class="text-white">Wagers: <span id="wagers" class="text-green-500">
                            <?php 
                                $stmt = $pdo->prepare("SELECT SUM(amount_played) as total_wagered FROM history WHERE user_id = :id");
                                $stmt->execute(['id' => $user_id]);
                                $total_wagered = $stmt->fetchColumn();
                                echo number_format($total_wagered, 2); ?>M
                        </span></p>
                    </div>
                </div>

                <!-- House Section -->
                <div class="w-1/3">
                    <div class="bg-gray-700 p-6 rounded-lg text-center">
                        <h3 class="text-3xl font-bold mb-6">House</h3>
                        <div class="slot-container">
                            <div class="slot" id="houseSlot1">
                                <img id="houseFlower1" class="hidden" style="width: 50px; height: 50px;" />
                                <img id="houseSeed1" class="hidden" src="assets/seed.png">
                            </div>
                            <div class="slot" id="houseSlot2">
                                <img id="houseFlower2" class="hidden" style="width: 50px; height: 50px;" />
                                <img id="houseSeed2" class="hidden" src="assets/seed.png">
                            </div>
                            <div class="slot" id="houseSlot3">
                                <img id="houseFlower3" class="hidden" style="width: 50px; height: 50px;" />
                                <img id="houseSeed3" class="hidden" src="assets/seed.png">
                            </div>
                            <div class="slot" id="houseSlot4">
                                <img id="houseFlower4" class="hidden" style="width: 50px; height: 50px;" />
                                <img id="houseSeed4" class="hidden" src="assets/seed.png">
                            </div>
                            <div class="slot" id="houseSlot5">
                                <img id="houseFlower5" class="hidden" style="width: 50px; height: 50px;" />
                                <img id="houseSeed5" class="hidden" src="assets/seed.png">
                            </div>
                        </div>
                        <p id="house-result-text" class="text-yellow-400 rs-text">House's results...</p>
                        <div class="mt-6 gif-container">
                            <img src="assets/timeout.png" id="house-gif">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<script>
    const userId = <?php echo json_encode($user_id); ?>;
    const username = <?php echo json_encode($username); ?>;
    const rank = <?php echo json_encode($rank ?? 0, JSON_NUMERIC_CHECK); ?>;
</script>


    <script>
        const user_id = <?php echo json_encode($user_id); ?>;
    </script>
    <script src="scripts/play.js"></script>
    <script>
    const betAmountInput = document.getElementById('bet-amount');
    const increaseButton = document.getElementById('increase-bet');
    const decreaseButton = document.getElementById('decrease-bet');

    increaseButton.addEventListener('click', () => {
        let currentBet = parseInt(betAmountInput.value) || 0;
        betAmountInput.value = currentBet + 1;
    });

    decreaseButton.addEventListener('click', () => {
        let currentBet = parseInt(betAmountInput.value) || 0;
        if (currentBet > parseInt(betAmountInput.min)) {
            betAmountInput.value = currentBet - 1;
        }
    });
    const contentArea = document.querySelector('.main-content'); // Replace with the appropriate selector for your content area

toggleChat.addEventListener('click', () => {
    isOpen = !isOpen;
    chatWidget.style.right = isOpen ? '0px' : '-385px';
    toggleChat.querySelector('img').src = isOpen ? 'assets/right.png' : 'assets/left.png';

    // Check if we're on play.php and apply the content-push class if chat is open
    if (window.location.pathname.includes('play.php')) {
        if (isOpen) {
            contentArea.classList.add('content-push');
        } else {
            contentArea.classList.remove('content-push');
        }
    }
});

</script>

</body>
</html>

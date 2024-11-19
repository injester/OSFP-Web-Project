<?php
session_start();
require 'config.php';
include 'header.php';

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch user and rakeback data
$stmt = $pdo->prepare("SELECT u.username, u.balance, u.rank, r.claimable_rakeback, r.total_wager FROM users u LEFT JOIN raking r ON u.id = r.user_id WHERE u.id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

// Set default values if data is not available
$user_rank = isset($user['rank']) ? $user['rank'] : 0;
$total_wager = isset($user['total_wager']) ? $user['total_wager'] : 0.00;
$claimable_rakeback = isset($user['claimable_rakeback']) ? $user['claimable_rakeback'] : 0.00;

// Updated rank data with organized percentages
$ranks = [
    ['rank' => 0, 'minWager' => 0, 'rakeback' => 0.25, 'color' => 'gray', 'image' => '0.png'],
    ['rank' => 1, 'minWager' => 10, 'rakeback' => 0.3, 'color' => 'white', 'image' => '1.png'],
    ['rank' => 2, 'minWager' => 50, 'rakeback' => 0.35, 'color' => 'green', 'image' => '2.png'],
    ['rank' => 3, 'minWager' => 100, 'rakeback' => 0.5, 'color' => 'lime', 'image' => '3.png'],
    ['rank' => 4, 'minWager' => 250, 'rakeback' => 0.75, 'color' => 'blue', 'image' => '4.png'],
    ['rank' => 5, 'minWager' => 500, 'rakeback' => 1.0, 'color' => 'cyan', 'image' => '5.png'],
    ['rank' => 6, 'minWager' => 1000, 'rakeback' => 1.25, 'color' => 'orange', 'image' => '6.png'],
    ['rank' => 7, 'minWager' => 5000, 'rakeback' => 1.5, 'color' => 'pink', 'image' => '7.png'],
    ['rank' => 8, 'minWager' => 10000, 'rakeback' => 2.0, 'color' => 'red', 'image' => '8.png'],
    ['rank' => 9, 'minWager' => 50000, 'rakeback' => 2.5, 'color' => 'rainbow-breathe', 'image' => '9.png'],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rakeback Information</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .rank-color-gray { color: gray; font-weight: bold; }
        .rank-color-white { color: white; font-weight: bold; }
        .rank-color-green { color: green; font-weight: bold; }
        .rank-color-lime { color: lime; font-weight: bold; }
        .rank-color-blue { color: blue; font-weight: bold; }
        .rank-color-cyan { color: cyan; font-weight: bold; }
        .rank-color-orange { color: orange; font-weight: bold; }
        .rank-color-pink { color: pink; font-weight: bold; }
        .rank-color-red { color: red; font-weight: bold; }
        .rank-color-rainbow-breathe { font-weight: bold; animation: rainbow-breathe 3s infinite; }

        .table-row {
            background-color: #111827;
            border: 1px solid #374151;
            border-radius: 0.375rem;
            padding: 0.25rem 0.4rem;
            display: flex;
            align-items: center;
            margin-bottom: 0.15rem;
        }

        .table-header, .table-data {
            padding: 0.4rem;
            display: flex;
            align-items: center;
            border-left: 1px solid #374151;
        }

        .header-rank, .data-rank {
            width: 40%;
        }

        .header-wager, .data-wager {
            width: 30%;
        }

        .header-rakeback, .data-rakeback {
            width: 30%;
        }

        .table-header:first-child, .table-data:first-child {
            border-left: none;
        }

        .table-header {
            font-weight: bold;
            color: #D1D5DB;
            background-color: #374151;
        }

        @keyframes rainbow-breathe {
            0% { color: #1E90FF; }
            20% { color: #8A2BE2; }
            40% { color: #FF6347; }
            60% { color: #FFD700; }
            80% { color: #1E90FF; }
        }
    </style>
</head>

<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center">
<div class="w-full max-w-2xl p-4 bg-gray-800 rounded-lg shadow-md mx-auto mt-12">
    <h2 class="text-2xl font-bold text-center mb-4">Rakeback Information</h2>

    <!-- Rank Table -->
    <div class="mb-4">
        <div class="table-row text-left table-header">
            <div class="table-header header-rank">Rank</div>
            <div class="table-header header-wager">Min Wager</div>
            <div class="table-header header-rakeback">Rakeback %</div>
        </div>
        <?php foreach ($ranks as $rank): ?>
            <div class="table-row">
                <div class="table-data data-rank rank-color-<?= $rank['color'] ?>">
                    <img src="assets/ranks/<?= $rank['image'] ?>" alt="Rank <?= $rank['rank'] ?>" class="inline w-5 h-5 rank-image">
                    Rank <?= $rank['rank'] ?>
                </div>
                <div class="table-data data-wager text-green-500 font-bold"><?= number_format($rank['minWager'], 2) ?>M</div>
                <div class="table-data data-rakeback"><?= $rank['rakeback'] ?>%</div>
            </div>
        <?php endforeach; ?>
    </div>

    <hr class="border-gray-600 my-4">

    <!-- User's Rank, Total Wagered, and Claimable Rakeback -->
    <div class="mb-4">
        <div class="table-row text-left table-header">
            <div class="table-header header-rank">Your Rank</div>
            <div class="table-header header-wager">Total Wagered</div>
            <div class="table-header header-rakeback">Claimable Rakeback</div>
        </div>
        <div class="table-row">
            <div class="table-data data-rank rank-color-<?= $ranks[$user_rank]['color'] ?>">
                <img src="assets/ranks/<?= $ranks[$user_rank]['image'] ?>" alt="Rank <?= $user_rank ?>" class="inline w-5 h-5 rank-image">
                Rank <?= $user_rank ?>
            </div>
            <div class="table-data data-wager text-green-500 font-bold"><?= number_format($total_wager, 2) ?>M</div>
            <div class="table-data data-rakeback text-green-500 font-bold"><?= number_format($claimable_rakeback, 2) ?>M</div>
        </div>
    </div>

    <hr class="border-gray-600 my-4">

    <!-- Claim Rakeback Button -->
    <form id="claim-form" class="text-center">
        <button 
            id="claim-button" 
            type="button" 
            class="w-full bg-yellow-500 py-1.5 rounded hover:bg-yellow-600 font-bold text-sm flex items-center justify-center">
            <span id="claim-button-text">Claim Now</span>
            <svg 
                id="loading-spinner" 
                class="hidden animate-spin ml-2 h-4 w-4 text-white" 
                xmlns="http://www.w3.org/2000/svg" 
                fill="none" 
                viewBox="0 0 24 24">
                <circle 
                    class="opacity-25" 
                    cx="12" 
                    cy="12" 
                    r="10" 
                    stroke="currentColor" 
                    stroke-width="4"></circle>
                <path 
                    class="opacity-75" 
                    fill="currentColor" 
                    d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
        </button>
    </form>
</div>

<script>
document.getElementById('claim-button').addEventListener('click', async () => {
    const claimButton = document.getElementById('claim-button');
    const claimText = document.getElementById('claim-button-text');
    const loadingSpinner = document.getElementById('loading-spinner');

    // Disable button and show spinner
    claimButton.disabled = true;
    claimText.classList.add('hidden');
    loadingSpinner.classList.remove('hidden');

    try {
        const response = await fetch('claim_rakeback.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });
        const result = await response.json();

        if (result.success) {
            alert(result.message);
            window.location.reload(); // Reload to update UI
        } else {
            alert(result.message || 'Failed to claim rakeback.'); // Display error message
        }
    } catch (error) {
        console.error('Error claiming rakeback:', error);
        alert('An error occurred while claiming rakeback.');
    } finally {
        // Reset button state
        claimButton.disabled = false;
        claimText.classList.remove('hidden');
        loadingSpinner.classList.add('hidden');
    }
});
</script>



</body>
</html>

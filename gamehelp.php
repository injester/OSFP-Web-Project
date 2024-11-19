<?php
session_start();
if (!isset($_SESSION['user_id'])) {
}
?>

<?php include('header.php'); ?>

<!-- Main Info Page -->
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black text-white relative">
    <div class="container mx-auto p-8 space-y-12">
        <!-- Welcome message -->
        <div class="text-center mb-12">
            <h1 class="text-5xl font-extrabold text-yellow-400 animate-fade-in-down drop-shadow-lg">Learn How to Play Flower Poker!</h1>
            <p class="text-lg text-gray-300 mt-4">Master the game and start your journey to big wins!</p>
        </div>

        <!-- How to Play Section -->
        <section id="how-to-play" class="p-8 bg-gray-800 rounded-lg shadow-xl transition transform hover:scale-105 duration-300 ease-out mb-16">
            <h2 class="text-4xl font-bold text-green-400 mb-6">How to Play</h2>
            <p class="text-gray-300 mb-6">Flower Poker is a fun and competitive game where you place a bet, plant seeds, and reveal flowers to create combinations. Here's how it works:</p>
            <ol class="list-decimal list-inside space-y-3 text-gray-200">
                <li>Choose your bet amount (minimum: <span class="text-green-500">10M</span>, maximum: <span class="text-green-500">500M</span>).</li>
                <li>Click "Place Bet" to begin.</li>
                <li>Click on the seeds (<img src="assets/seed.png" alt="Seed" class="inline w-8 h-8">) in each slot to reveal flowers.</li>
                <li>Aim for the best flower combination to beat the house.</li>
            </ol>
            <p class="text-gray-300 mt-6">
                Ready to try? Head over to <a href="play.php" class="text-blue-400 underline hover:text-blue-500">Play Now</a> and place your first bet!
            </p>
        </section>

        <!-- Winning Combinations Section -->
        <section id="combinations" class="p-8 bg-gray-800 rounded-lg shadow-xl transition transform hover:scale-105 duration-300 ease-out mb-16">
            <h2 class="text-4xl font-bold text-yellow-400 mb-6">Winning Combinations</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Examples -->
                <?php
                $combinations = [
                    ['name' => 'Five of a Kind', 'flowers' => ['red', 'red', 'red', 'red', 'red']],
                    ['name' => 'Four of a Kind', 'flowers' => ['blue', 'blue', 'blue', 'blue', 'yellow']],
                    ['name' => 'Full House', 'flowers' => ['purple', 'purple', 'purple', 'rainbow', 'rainbow']],
                    ['name' => 'Two Pair', 'flowers' => ['red', 'red', 'blue', 'blue', 'yellow']],
                    ['name' => 'One Pair', 'flowers' => ['rainbow', 'rainbow', 'blue', 'assorted', 'yellow']],
                    ['name' => 'Black or White', 'flowers' => ['black', 'white', 'seed', 'seed', 'seed']]
                ];

                foreach ($combinations as $combo) {
                    echo '<div class="text-center">';
                    echo '<div class="flex justify-center space-x-2">';
                    foreach ($combo['flowers'] as $flower) {
                        echo '<div class="relative w-12 h-12">';
                        echo '<img src="assets/slot.png" alt="Slot" class="absolute inset-0 w-full h-full">';
                        echo '<img src="assets/' . $flower . '.png" alt="' . ucfirst($flower) . ' Flower" class="relative">';
                        echo '</div>';
                    }
                    echo '</div>';
                    echo '<p class="font-rs text-yellow-400 mt-4">' . $combo['name'] . '</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </section>

        <!-- Rules Section -->
        <section id="rules" class="p-8 bg-gray-800 rounded-lg shadow-xl transition transform hover:scale-105 duration-300 ease-out mb-16">
            <h2 class="text-4xl font-bold text-red-400 mb-6">Game Rules</h2>
            <ul class="list-disc list-inside space-y-4 text-gray-200">
                <li>Minimum bet: <span class="text-green-500">10M</span>. Maximum bet: <span class="text-green-500">500M</span>.</li>
                <li>If both player and house have the same combination, it results in a draw.</li>
                <li>White or Black flowers require a "Replant".</li>
                <li>All bets are final once the game starts.</li>
            </ul>
        </section>

        <!-- Raking System Section -->
        <section id="raking" class="p-8 bg-gray-800 rounded-lg shadow-xl transition transform hover:scale-105 duration-300 ease-out">
            <h2 class="text-4xl font-bold text-yellow-400 mb-6">Raking System</h2>
            <p class="text-gray-300 mb-6">Earn rewards as you play! The more you wager, the higher your rank and rakeback percentage. Visit the <a href="rake.php" class="text-blue-400 underline hover:text-blue-500">Raking Page</a> to learn more.</p>
        </section>
    </div>
</div>

<style>
    @font-face {
        font-family: 'RSFont';
        src: url('assets/rsfont.ttf') format('truetype');
    }
    .font-rs {
        font-family: 'RSFont', sans-serif;
        font-size: 1.2rem;
    }
</style>
<style>
    @keyframes fade-in-down {
        0% {
            opacity: 0;
            transform: translateY(-10px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in-down {
        animation: fade-in-down 0.6s ease-out;
    }

    @keyframes fade-in-up {
        0% {
            opacity: 0;
            transform: translateY(10px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in-up {
        animation: fade-in-up 0.6s ease-out;
    }
</style>

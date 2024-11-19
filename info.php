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
            <h1 class="text-5xl font-extrabold text-yellow-400 animate-fade-in-down drop-shadow-lg">Learn How Deposits, Cash-Outs, and Buying Work</h1>
            <p class="text-lg text-gray-300 mt-4">Understand the seamless processes to manage your balance and play safely.</p>
        </div>

                <!-- Chat and Admin Coordination Section -->
                <section id="chat" class="p-8 bg-gray-800 rounded-lg shadow-xl transition transform hover:scale-105 duration-300 ease-out">
            <h2 class="text-4xl font-bold text-blue-400 mb-6">Chat Coordination</h2>
            <p class="text-gray-300 mb-6">
                Whether you're depositing or cashing out, you'll coordinate the in-game trade via the chat feature. Here's how it works:
            </p>
            <ul class="list-disc list-inside space-y-3 text-gray-200">
                <li>Chats are automatically created for each deposit or cash-out request.</li>
                <li>You can find the chat at the bottom of the <a href="index.php" class="text-blue-400 underline hover:text-blue-500">homepage</a>.</li>
                <li>Admins will guide you through the trade process in the chat.</li>
                <li>Completed deposits will reflect in your balance, while completed cash-outs will deduct the amount.</li>
            </ul>
            <p class="text-gray-300 mt-6">
                Stay safe and ensure all trades are coordinated with verified admins only.
            </p>
        </section>

        <!-- Deposit Section -->
        <section id="deposit" class="p-8 bg-gray-800 rounded-lg shadow-xl transition transform hover:scale-105 duration-300 ease-out mb-16">
            <h2 class="text-4xl font-bold text-green-400 mb-6">Deposit OSRS Mills</h2>
            <p class="text-gray-300 mb-6">Depositing OSRS mills into your account is straightforward and secure. Here's how it works:</p>
            <ol class="list-decimal list-inside space-y-3 text-gray-200">
                <li>Head over to the <a href="index.php" class="text-blue-400 underline hover:text-blue-500">Deposit Widget</a>.</li>
                <li>Enter the amount of mills you want to deposit (minimum: <span class="text-green-500">10M</span>).</li>
                <li>Click "Deposit" to initiate the transaction.</li>
                <li>A chat will be created at the bottom of the <a href="index.php" class="text-blue-400 underline hover:text-blue-500">homepage</a>, connecting you to an admin for an in-game meetup.</li>
                <li>Complete the trade in-game to finalize the deposit. The amount will be added to your account balance upon successful trade.</li>
            </ol>
            <p class="text-gray-300 mt-6">For any issues or questions, reach out to support in the chat.</p>
        </section>

        <!-- Cash-Out Section -->
        <section id="cash-out" class="p-8 bg-gray-800 rounded-lg shadow-xl transition transform hover:scale-105 duration-300 ease-out mb-16">
            <h2 class="text-4xl font-bold text-red-400 mb-6">Cash-Out</h2>
            <p class="text-gray-300 mb-6">Cashing out is just as easy! Here's the process:</p>
            <ol class="list-decimal list-inside space-y-3 text-gray-200">
                <li>Navigate to the <a href="index.php" class="text-blue-400 underline hover:text-blue-500">Cash-Out Widget</a>.</li>
                <li>Enter the amount you want to cash out from your balance (minimum: <span class="text-green-500">10M</span>).</li>
                <li>Click "Cash-Out" to create the request.</li>
                <li>A chat will open at the bottom of the <a href="index.php" class="text-blue-400 underline hover:text-blue-500">homepage</a> to coordinate with an admin for an in-game meetup.</li>
                <li>After the trade is completed in-game, the amount will be deducted from your balance.</li>
            </ol>
            <p class="text-gray-300 mt-6">If you reconsider during the process, the admin can cancel the request and refund the amount to your balance.</p>
        </section>

        <!-- Buy Section -->
        <section id="buy" class="p-8 bg-gray-800 rounded-lg shadow-xl transition transform hover:scale-105 duration-300 ease-out mb-16">
            <h2 class="text-4xl font-bold text-yellow-400 mb-6">Buy OSRS Mills with Crypto</h2>
            <p class="text-gray-300 mb-6">Buying mills with cryptocurrency is a safe and quick process. Here's how:</p>
            <ol class="list-decimal list-inside space-y-3 text-gray-200">
                <li>Go to the <a href="index.php" class="text-blue-400 underline hover:text-blue-500">Buy Widget</a>.</li>
                <li>Select the amount of mills you want to purchase.</li>
                <li>The widget will calculate the price in ETH based on the current rate.</li>
                <li>Confirm your transaction, and a payment request will be sent to your connected crypto wallet.</li>
                <li>Once the payment is confirmed on the Ethereum blockchain, the mills will be added directly to your balance.</li>
            </ol>
            <p class="text-gray-300 mt-6">
                New to crypto? Check out our detailed guide on <a href="cryptohow.php" class="text-blue-400 underline hover:text-blue-500">how to set up and use MetaMask</a>.
            </p>
        </section>
        
        <!-- Public Chat Section -->
        <section id="public-chat" class="p-8 bg-gray-800 rounded-lg shadow-xl transition transform hover:scale-105 duration-300 ease-out">
            <h2 class="text-4xl font-bold text-blue-400 mb-6">Public Chat</h2>
            <p class="text-gray-300 mb-6">The public chat is a place to connect, share your experiences, and stay updated with announcements. Here's how it works:</p>
            <ul class="list-disc list-inside space-y-3 text-gray-200">
                <li>Ranking up will give you a unique badge and color in the public chat channel, showcasing your achievements.</li>
                <li>To open the chat, click "<" on the right side of your screen.</li>
                <li>Admins often post announcements or highlight significant wins in golden messages.</li>
                <li>Interact with other players, share strategies, and celebrate your wins.</li>
                <li>Follow the chat rules to ensure a safe and enjoyable experience for everyone.</li>
            </ul>
            <h3 class="text-3xl font-semibold text-red-400 mt-8 mb-4">Public Chat Rules</h3>
            <ol class="list-decimal list-inside space-y-3 text-gray-200">
                <li>No spamming or flooding the chat with repeated messages.</li>
                <li>Be respectful to other players and admins. Harassment or abusive language is not tolerated.</li>
                <li>Do not share personal or sensitive information in the chat.</li>
                <li>Scamming players or Advertising in the chat is prohibited.</li>
                <li>Follow admin instructions at all times. Their decisions are final.</li>
                <li>Use the chat responsibly and keep the conversations on-topic.</li>
            </ol>
            <p class="text-gray-300 mt-6">
                <strong>*</strong> The public chat is monitored by admins to ensure compliance with the rules and to provide support when needed.
                <br><strong>*</strong> Golden messages are reserved for admins to post important updates or highlight notable events such as big wins.
                <br><strong>*</strong> Abuse of the public chat system may result in temporary or permanent bans from the chat feature.
            </p>
            <p class="text-gray-300 mt-6">Enjoy connecting with the community, and remember to follow the rules to keep the chat safe and engaging for everyone!</p>
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
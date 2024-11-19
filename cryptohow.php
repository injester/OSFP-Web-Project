<?php
session_start();
if (!isset($_SESSION['user_id'])) {
}
?>

<?php include('header.php'); ?>

<!-- Dashboard content -->
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white">
    <div class="container mx-auto p-8 space-y-12">
        <!-- Welcome message -->
        <div class="text-center mb-12">
            <h1 class="text-5xl font-extrabold text-yellow-400 animate-fade-in-down drop-shadow-lg">Learn How Create a Wallet & Buy Crypto</h1>
            <p class="text-lg text-gray-300 mt-4">Understand the seamless processes to manage your crypto wallet safely.</p>
        </div>
        
        <!-- How to Buy Crypto section -->
        <div class="p-8 bg-gray-800 rounded-lg shadow-xl transition transform hover:scale-105 duration-300 ease-out">
            <h2 class="text-4xl font-bold text-green-400 mb-6">How to Buy Crypto Through MetaMask</h2>

            <!-- Centered Image Above Text and Video -->
            <div class="flex justify-center mb-8">
                <img src="assets/tutorial3.webp" alt="MetaMask Buy Tutorial" class="rounded-lg shadow-lg border border-gray-700">
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <!-- Text instructions -->
                <div>
                    <p class="text-gray-300 mb-6">Once your MetaMask wallet is set up, follow these steps to buy crypto:</p>
                    <ol class="list-decimal list-inside space-y-3 text-gray-200">
                        <li>Open <a href="https://portfolio.metamask.io" target="_blank" class="text-blue-400 underline hover:text-blue-500">MetaMask Portfolio</a> and connect your wallet.</li>
                        <li>Click "Buy" to initiate the process.</li>
                        <li>Select your region and preferred payment method.</li>
                        <li>Choose the token (e.g., ETH) and network (e.g., Ethereum) to purchase.</li>
                        <li>Enter the amount in fiat (e.g., $100 for ETH) and click "Get Quotes".</li>
                        <li>Select a provider and complete the purchase on their site. Funds will appear in your wallet.</li>
                    </ol>
                </div>

                <!-- Video tutorial on the right side -->
                <div class="flex justify-center">
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/v8E2SHHyqms" title="How to Buy Crypto" class="rounded-lg shadow-lg" allowfullscreen></iframe>
                </div>
            </div>
        </div>

        <!-- Tips and Security Reminder -->
        <div class="p-8 bg-gray-800 rounded-lg shadow-xl transition transform hover:scale-105 duration-300 ease-out">
            <h3 class="text-4xl font-semibold text-red-400 mb-6">Security Tips</h3>
            <ul class="list-disc list-inside space-y-3 text-gray-300">
                <li>Ensure you're on the official MetaMask or MetaMask Portfolio site.</li>
                <li>Never share your 12-word recovery phrase with anyone.</li>
                <li>For large crypto holdings, consider using a hardware wallet.</li>
            </ul>
        </div>

        <!-- How to Set Up MetaMask section with image aligned to the right -->
        <div class="p-8 bg-gray-800 rounded-lg shadow-xl transition transform hover:scale-105 duration-300 ease-out">
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Left Side: Text instructions -->
                <div>
                    <h2 class="text-4xl font-bold text-yellow-400 mb-6">How to Set Up a MetaMask Wallet</h2>
                    <p class="text-gray-300 mb-6">You can set up MetaMask on a PC using Chrome, Firefox, Brave, Edge, or Opera. Follow these steps:</p>
                    <ol class="list-decimal list-inside space-y-3 text-gray-200 mb-10">
                        <li>Visit <a href="https://metamask.io" target="_blank" class="text-blue-400 underline hover:text-blue-500">metamask.io</a> to ensure it’s the official site.</li>
                        <li>Click "Download" and choose "Install MetaMask".</li>
                        <li>In Chrome, go to the Web Store, click "Add to Chrome," then "Add extension".</li>
                        <li>MetaMask will install as a browser extension.</li>
                    </ol>

                    <!-- Setting Up Your Wallet Section -->
                    <h3 class="text-3xl font-semibold text-yellow-300 mb-4">Setting Up Your Wallet</h3>
                    <p class="text-gray-300 mb-4">After installation:</p>
                    <ol class="list-decimal list-inside space-y-3 text-gray-200">
                        <li>Agree to the terms and conditions and select "Create a new wallet".</li>
                        <li>Create a secure password for your wallet.</li>
                        <li>Save your 12-word secret recovery phrase securely.</li>
                        <li>Confirm the phrase to complete setup.</li>
                    </ol>
                </div>



                <!-- Right Side: Image -->
                <div class="flex justify-center">
                    <img src="assets/tutorial2.webp" alt="Secret Recovery Phrase" class="rounded-lg shadow-lg border border-gray-700">
                </div>
            </div>
        </div>
        <!-- Tips and Security Reminder -->
        <div class="p-8 bg-gray-800 rounded-lg shadow-xl transition transform hover:scale-105 duration-300 ease-out">
            <h3 class="text-4xl font-semibold text-red-400 mb-6">Feel Free To Ask</h3>
            <ul class="list-disc list-inside space-y-3 text-gray-300">
                <li>On the right side of the screen you can always ask a community member.</li>
                <li>If you haven't figured that out feel free to ask an admin.</li>
                <li>Stay Safe & Aware!</li>
            </ul>
        </div>
    </div>
</div>

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

</body>
</html>

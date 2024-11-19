document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('buy-now').addEventListener('click', handleBuyNow);
});

async function handleBuyNow() {
    if (!isLoggedIn) {
        alert('Please log in to make a purchase.');
        window.location.href = 'login.php';
        return;
    }

    // Show the modal when the user clicks buy
    document.getElementById('transaction-modal').style.display = 'flex';
    document.getElementById('transaction-message').innerText = 'Waiting for user to confirm...';
    document.getElementById('loader').style.display = 'block'; // Show the loader
    document.getElementById('result-icon').style.display = 'none'; // Hide icons initially

    const quantity = parseInt(document.getElementById('quantity').value);
    const pricePerMillion = parseFloat(document.getElementById('price').dataset.pricePerMillion);
    const totalCostEUR = pricePerMillion * quantity;

    // Fetch ETH to EUR conversion rate (use an API like CoinGecko)
    const ethRate = await fetch('https://api.coingecko.com/api/v3/simple/price?ids=ethereum&vs_currencies=eur')
        .then(response => response.json())
        .then(data => data.ethereum.eur);

    const totalCostETH = (totalCostEUR / ethRate).toFixed(6); // Convert EUR to ETH and round

    if (typeof window.ethereum !== 'undefined') {
        const web3 = new Web3(window.ethereum);

        try {
            await ethereum.request({ method: 'eth_requestAccounts' });

            const accounts = await web3.eth.getAccounts();
            const sender = accounts[0];
            
            // Determine the current network
            const networkId = await web3.eth.net.getId();

            if (testMode) {
                // Test Mode: Handle Linea Sepolia Network
                if (networkId !== 11155111) { // Linea Sepolia Network ID (Test Mode)
                    await switchNetwork('0xaa36a7'); // Chain ID for Sepolia Testnet
                    await processPayment(web3, totalCostETH, quantity); // After switch, process payment
                } else {
                    await processPayment(web3, totalCostETH, quantity); // If already on Sepolia, process payment
                }
            } else {
                // Mainnet Mode: Handle Ethereum Mainnet
                if (networkId !== 1) { // Mainnet ID is 1
                    await switchNetwork('0x1'); // Ethereum Mainnet Chain ID
                    await processPayment(web3, totalCostETH, quantity); // After switch, process payment
                } else {
                    await processPayment(web3, totalCostETH, quantity); // If already on Mainnet, process payment
                }
            }

        } catch (error) {
            console.error('MetaMask transaction error:', error);
            hideModalWithError('Error: ' + error.message);
        }
    } else {
        alert('Please install MetaMask or another Ethereum wallet!');
    }
}

// Function to process the payment
async function processPayment(web3, totalCostETH, quantity) {
    try {
        const accounts = await web3.eth.getAccounts();
        const sender = accounts[0];

        web3.eth.sendTransaction({
            from: sender,
            to: myWalletAddress,  // Use the wallet address passed from PHP
            value: web3.utils.toWei(totalCostETH, 'ether')
        })
        .on('transactionHash', async function (hash) {
            document.getElementById('transaction-message').innerText = 'Transaction sent, waiting for confirmation...';

            // If the transaction is successful, handle server-side
            await fetch('widgets/add_balance.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    user_id: userId,
                    amount: quantity // Amount in Mills
                })
            });

        })
        .on('confirmation', function (confirmationNumber, receipt) {
            if (confirmationNumber > 0) {
                document.getElementById('transaction-message').innerText = 'Transaction completed!';
                document.getElementById('close-button').style.display = 'block';
                document.getElementById('loader').style.display = 'none'; // Hide loader
                document.getElementById('result-icon').style.display = 'block';
                document.querySelector('.success-icon').style.display = 'block'; // Show success icon
            }
        })
        .on('error', function (error) {
            hideModalWithError('Transaction failed: ' + error.message);
        });

    } catch (error) {
        hideModalWithError('Payment process error: ' + error.message);
    }
}

// Function to automatically switch the network
async function switchNetwork(chainId) {
    try {
        await ethereum.request({
            method: 'wallet_switchEthereumChain',
            params: [{ chainId: chainId }]
        });
        console.log(`Switched to network ${chainId}`);
    } catch (switchError) {
        // If the network has not been added to MetaMask, attempt to add it
        if (switchError.code === 4902) {
            try {
                await addNetwork(chainId);
            } catch (addError) {
                console.error('Failed to add the network:', addError);
            }
        } else {
            console.error('Failed to switch network:', switchError);
        }
    }
}

// Function to add the network if it doesn't exist in MetaMask
async function addNetwork(chainId) {
    try {
        await ethereum.request({
            method: 'wallet_addEthereumChain',
            params: [
                {
                    chainId: chainId,
                    rpcUrls: chainId === '0x1' ? ['https://mainnet.infura.io/v3/YOUR_INFURA_PROJECT_ID'] : ['https://rpc-sepolia.maticvigil.com/'], // Replace with correct RPC URL
                    chainName: chainId === '0x1' ? 'Ethereum Mainnet' : 'Linea Sepolia Testnet',
                    nativeCurrency: {
                        name: chainId === '0x1' ? 'Ethereum' : 'Linea Testnet ETH',
                        symbol: chainId === '0x1' ? 'ETH' : 'tETH',
                        decimals: 18,
                    },
                    blockExplorerUrls: [chainId === '0x1' ? 'https://etherscan.io' : 'https://sepolia.lineascan.build/'],
                },
            ],
        });
        console.log(`Added and switched to network ${chainId}`);
    } catch (addError) {
        console.error('Error adding network:', addError);
    }
}

// Function to hide modal with an error message
function hideModalWithError(errorMessage) {
    document.getElementById('transaction-message').innerText = errorMessage;
    document.getElementById('close-button').style.display = 'block';
    document.getElementById('loader').style.display = 'none'; // Hide loader
    document.getElementById('result-icon').style.display = 'block';
    document.querySelector('.failure-icon').style.display = 'block'; // Show failure icon
}

// Close modal function
function hideModal() {
    const modal = document.getElementById('transaction-modal');
    modal.style.display = 'none';
}

// Event listener for closing modal
document.getElementById('close-button').addEventListener('click', hideModal);

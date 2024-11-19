let isGameActive = false; // Track if the game is active

// Add confirmation when the user tries to leave mid-game
window.addEventListener('beforeunload', (e) => {
    if (isGameActive) {
        const confirmationMessage = "Are you sure you want to leave? If you leave mid-game, your progress might be lost.";
        e.returnValue = confirmationMessage; // Required for some browsers
        return confirmationMessage;
    }
});

document.getElementById('place-bet').addEventListener('click', placeBet);

const flowers = [
    { src: 'white.png', name: 'White', weight: 1 },
    { src: 'black.png', name: 'Black', weight: 2 },
    { src: 'assorted.png', name: 'Assorted', weight: 75 },
    { src: 'purple.png', name: 'Purple', weight: 150 },
    { src: 'yellow.png', name: 'Yellow', weight: 150 },
    { src: 'blue.png', name: 'Blue', weight: 150 },
    { src: 'orange.png', name: 'Orange', weight: 150 },
    { src: 'red.png', name: 'Red', weight: 150 },
    { src: 'rainbow.png', name: 'Rainbow', weight: 150 }
];

const flowersHouse = [
    { src: 'white.png', name: 'White', weight: 1 },
    { src: 'black.png', name: 'Black', weight: 2 },
    { src: 'assorted.png', name: 'Assorted', weight: 10 },
    { src: 'purple.png', name: 'Purple', weight: 150 },
    { src: 'yellow.png', name: 'Yellow', weight: 150 },
    { src: 'blue.png', name: 'Blue', weight: 150 },
    { src: 'orange.png', name: 'Orange', weight: 150 },
    { src: 'red.png', name: 'Red', weight: 150 },
    { src: 'rainbow.png', name: 'Rainbow', weight: 150 }
];

let currentSlot = 1;
let betAmount = parseInt(document.getElementById('bet-amount').value);
let potentialWin = 0;
const flowerResults = [];
const houseResults = [];

async function refreshBalance() {
    try {
        const response = await fetch('fetch_balance.php');
        const data = await response.json();

        if (data.success) {
            const balanceElement = document.querySelector('.balance-section .text-green-500');
            if (balanceElement) {
                balanceElement.innerText = `${data.balance.toFixed(2)}M`;
            }
            // Update the JavaScript balance variable
            balance = data.balance; // Update the global `balance` variable
        } else {
            console.error("Failed to fetch balance:", data.error);
        }
    } catch (error) {
        console.error("Error fetching balance:", error);
    }
}



// Start the game
async function placeBet() {
    betAmount = parseInt(document.getElementById('bet-amount').value);
    if (isNaN(betAmount) || betAmount < 10 || betAmount > 500) {
        alert("Please enter a valid bet amount between 10 and 500.");
        return;
    }

    if (betAmount > balance) {
        alert("Insufficient balance for this bet.");
        return;
    }

    // Deduct balance
    const balanceUpdate = await updateBalance(betAmount, 'subtract');
    if (!balanceUpdate.success) return;

    balance = balanceUpdate.new_balance;
    potentialWin = betAmount * 1.9;
    document.getElementById('bet-box').style.display = 'none';

    // Refresh the balance in the header
    await refreshBalance();
    showSeed(currentSlot);

    // Show additional stat containers
    document.getElementById('win-rate-container').style.display = 'block';
    document.getElementById('win-payout-container').style.display = 'block';
    document.getElementById('profit-container').style.display = 'block';
    document.getElementById('current-bet-container').style.display = 'block';

    // Set initial message for the player result
    document.getElementById('player-result-text').innerText = "Click the seeds to see the results.";

    // Update current bet and win payout display
    document.getElementById('current-bet').innerText = `${betAmount.toFixed(1)}M`;
    document.getElementById('win-payout').innerText = `${potentialWin.toFixed(1)}M`;

    // Mark game as active
    isGameActive = true;

    // Add bet amount to total wager in raking table and update rank/rakeback
    try {
        const rakebackResponse = await fetch('update_rakeback.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                user_id: userId,
                username: username,
                bet_amount: betAmount // Do not send rank; let the server handle it
            })
        });

        if (rakebackResponse.ok) {
            const data = await rakebackResponse.json();
            console.log("Rakeback updated successfully:", data);
        } else {
            console.error("Failed to update rakeback.");
        }
    } catch (error) {
        console.error("Error updating rakeback:", error);
    }

    // Refresh values after update
    refreshValues();
}


function plantFlower(slot) {
    document.getElementById(`seed${slot}`).style.display = 'none';
    playGIF();

    setTimeout(() => {
        const playerFlower = getRandomFlower('player'); // Use player odds
        const houseFlower = getRandomFlower('house');   // Use house odds

        document.getElementById(`flower${slot}`).src = `assets/${playerFlower.src}`;
        document.getElementById(`flower${slot}`).style.display = 'block';
        document.getElementById(`houseFlower${slot}`).src = `assets/${houseFlower.src}`;
        document.getElementById(`houseFlower${slot}`).style.display = 'block';

        flowerResults.push(playerFlower.name);
        houseResults.push(houseFlower.name);

        // Update the result text to show the actual results
        updateResultText(playerFlower, houseFlower);

        if (isReplant(playerFlower, houseFlower)) {
            setTimeout(() => {
                alert("Re-plant! Black or White flower.");
                replantGame();
            }, 2300);
        } else if (currentSlot === 5) {
            setTimeout(checkPokerHand, 500);
        } else {
            currentSlot++;
            showSeed(currentSlot);
        }

        resetGIFs();
    }, 2300);
}



                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
// End the game
async function checkPokerHand() {
    const playerBestHand = determineHand(countFlowers(flowerResults));
    const houseBestHand = determineHand(countFlowers(houseResults));
    let status, resultMessage;

    if (playerBestHand === houseBestHand) {
        alert("Draw! Re-planting...");
        replantGame();
    } else {
        status = playerBestHand > houseBestHand ? 'win' : 'lose';
        resultMessage = playerBestHand > houseBestHand ? "Player Wins!" : "House Wins!";
        alert(resultMessage);

        if (status === 'win' && potentialWin > 0) {
            await updateBalance(potentialWin, 'add');

            if (potentialWin >= 190) {
                const winMessage = `🎉 ${username} just won ${potentialWin.toFixed(1)}M 🎉`;
                postAnnouncementMessage(winMessage);
            }

            potentialWin = 0; // Reset potential win
        }

        // Store game history
        storeGameHistory(betAmount, status, flowerResults.join(', '), houseResults.join(', '));

        // Reset UI after game ends
        resetGameUI();

        // Mark game as inactive
        isGameActive = false;
    }
}
function resetGameUI() {
    refreshBalance()
    // Clear flower results
    flowerResults.length = 0;
    houseResults.length = 0;
    currentSlot = 1;

    // Reset the UI elements
    for (let i = 1; i <= 5; i++) {
        const flower = document.getElementById(`flower${i}`);
        const houseFlower = document.getElementById(`houseFlower${i}`);
        const seed = document.getElementById(`seed${i}`);

        if (flower) flower.style.display = 'none';
        if (houseFlower) houseFlower.style.display = 'none';
        if (seed) seed.style.display = 'none'; // Hide seed images
    }

    // Hide the first seed to ensure no new game starts
    const firstSeed = document.getElementById(`seed${currentSlot}`);
    if (firstSeed) firstSeed.style.display = 'none';

    // Reset stats display
    document.getElementById('win-rate-container').style.display = 'none';
    document.getElementById('win-payout-container').style.display = 'none';
    document.getElementById('profit-container').style.display = 'none';
    document.getElementById('current-bet-container').style.display = 'none';

    // Reset the bet-box
    document.getElementById('bet-box').style.display = 'block';
    document.getElementById('bet-amount').value = 10; // Reset default bet amount

    // Clear the result text
    document.getElementById('player-result-text').innerText = '';
    document.getElementById('house-result-text').innerText = '';
}






async function postAnnouncementMessage(message) {
    try {
        // Send the announcement as a 'super secret' command for the PHP logic to process it properly
        await fetch('send_message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                message: `${message}`, // Use the secret command
                is_secret: '1' // Indicate this is a secret announcement
            })
        });
    } catch (error) {
        console.error('Error posting announcement:', error);
    }
}



// Example usage when a player wins
const winMessage = `🎉 ${username} just won ${potentialWin.toFixed(1)}M 🎉`;
if (potentialWin >= 190) { // Only post if the win amount is 190M or more
    postAnnouncementMessage(winMessage);
}



function updateResultText(playerFlower, houseFlower) {
    document.getElementById('player-result-text').innerText = `Player planted a ${playerFlower.name} flower! ${getHandStatus(flowerResults)}`;
    document.getElementById('house-result-text').innerText = `House planted a ${houseFlower.name} flower! ${getHandStatus(houseResults)}`;
}

function getHandStatus(flowerArray) {
    const flowerCounts = countFlowers(flowerArray);
    const handType = determineHand(flowerCounts);

    switch (handType) {
        case 1: return "1 pair";
        case 2: return "2 pair";
        case 3: return "3 OAK";
        case 4: return "Full house";
        case 5: return "4 OAK";
        case 6: return "5 OAK";
        default: return "";
    }
}

async function refreshValues() {
    try {
        const response = await fetch('fetch_values.php');
        const data = await response.json();

        if (data.success) {
            const balanceElement = document.getElementById('balance');
            const winningsElement = document.getElementById('winnings');
            const lastBetElement = document.getElementById('last-bet');

            // Update elements only if they exist in the DOM
            if (balanceElement) {
                balanceElement.innerText = `${data.balance}M`;
            } else {
                console.warn("Element with ID 'balance' not found.");
            }

            if (winningsElement) {
                winningsElement.innerText = `Winnings: ${data.total_winnings}M`;
            } else {
                console.warn("Element with ID 'winnings' not found.");
            }

            if (lastBetElement) {
                lastBetElement.innerText = ` ${data.last_bet}M`;
            } else {
                console.warn("Element with ID 'last-bet' not found.");
            }
        } else {
            console.error('Failed to fetch updated values.');
        }
    } catch (error) {
        console.error('Error refreshing values:', error);
    }
}


async function updateBalance(amount, action) {
    const response = await fetch('payout.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ change_amount: amount, action: action })
    });
    const data = await response.json();
    if (!data.success) {
        alert(data.message);
    }
    return data;
}

function showSeed(slot) {
    if (slot <= 5) {
        document.getElementById(`seed${slot}`).style.display = 'block';
    }
}

function getRandomFlower(oddsType = 'player') {
    const odds = oddsType === 'house' ? flowersHouse : flowers; // Use the correct odds array
    const totalWeight = odds.reduce((sum, flower) => sum + flower.weight, 0);
    let randomWeight = Math.random() * totalWeight;

    for (const flower of odds) {
        if (randomWeight < flower.weight) return flower;
        randomWeight -= flower.weight;
    }
}


function countFlowers(results) {
    const flowerCounts = {};
    results.forEach(flower => flowerCounts[flower] = (flowerCounts[flower] || 0) + 1);
    return Object.values(flowerCounts).sort((a, b) => b - a);
}

function determineHand(counts) {
    if (counts[0] === 5) return 6;
    if (counts[0] === 4) return 5;
    if (counts[0] === 3 && counts[1] === 2) return 4;
    if (counts[0] === 3) return 3;
    if (counts[0] === 2 && counts[1] === 2) return 2;
    if (counts[0] === 2) return 1;
    return 0;
}

function isReplant(playerFlower, houseFlower) {
    return ['Black', 'White'].includes(playerFlower.name) || ['Black', 'White'].includes(houseFlower.name);
}

function replantGame() {
    flowerResults.length = 0;
    houseResults.length = 0;
    currentSlot = 1;

    for (let i = 1; i <= 5; i++) {
        document.getElementById(`flower${i}`).style.display = 'none';
        document.getElementById(`houseFlower${i}`).style.display = 'none';
        document.getElementById(`seed${i}`).style.display = 'none';
    }

    showSeed(currentSlot);
}

function resetGame() {
    flowerResults.length = 0;
    houseResults.length = 0;
    currentSlot = 1;

    for (let i = 1; i <= 5; i++) {
        document.getElementById(`flower${i}`).style.display = 'none';
        document.getElementById(`houseFlower${i}`).style.display = 'none';
        document.getElementById(`seed${i}`).style.display = 'none';
    }

    document.getElementById('bet-box').style.display = 'block';
}

function storeGameHistory(amountPlayed, status, playerPlay, housePlay) {
    return fetch('store_history.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ amount_played: amountPlayed, status: status, player_play: playerPlay, house_play: housePlay })
    });
}

function playGIF() {
    document.getElementById('player-gif').src = 'assets/plant.gif';
    document.getElementById('house-gif').src = 'assets/plant.gif';
}

function resetGIFs() {
    document.getElementById('player-gif').src = 'assets/timeout.png';
    document.getElementById('house-gif').src = 'assets/timeout.png';
}
function postWinMessageToChat(playerName, winAmount) {
    const chatMessages = document.getElementById('chat-messages');

    // Create the message container
    const winMessage = document.createElement('div');
    winMessage.classList.add('win-message');
    winMessage.innerHTML = `🎉 ${playerName} has just won ${winAmount.toFixed(1)}M 🎉`;

    // Append the message to the chat
    chatMessages.appendChild(winMessage);

    // Scroll to the bottom of the chat
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // Optional: Remove the message after a set time (e.g., 10 seconds)
    setTimeout(() => {
        winMessage.remove();
    }, 10000); // Remove after 10 seconds
}

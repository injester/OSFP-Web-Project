document.addEventListener('DOMContentLoaded', function() {
    const increaseBtn = document.getElementById('increase');
    const decreaseBtn = document.getElementById('decrease');
    const quantityInput = document.getElementById('quantity');
    const priceDisplay = document.getElementById('price');

    const cashoutIncreaseBtn = document.getElementById('cashout-increase');
    const cashoutDecreaseBtn = document.getElementById('cashout-decrease');
    const cashoutQuantityInput = document.getElementById('cashout-quantity');

    const depositIncreaseBtn = document.getElementById('deposit-increase');
    const depositDecreaseBtn = document.getElementById('deposit-decrease');
    const depositQuantityInput = document.getElementById('deposit-quantity');

    let pricePerMillion = parseFloat(priceDisplay.dataset.pricePerMillion);
    let minQuantity = 20;  // Minimum quantity for buy in millions
    let minCashOut = 10;   // Minimum cash-out is 50M
    let maxCashOut = 5000; // Set maximum cash-out to 5000M
    let minDeposit = 10;   // Minimum deposit is 10M

    // Update price display based on quantity for Buy
    const updatePrice = () => {
        let quantity = parseInt(quantityInput.value);
        let totalPrice = quantity * pricePerMillion;
        priceDisplay.textContent = totalPrice.toFixed(2);
    };

    // Increase quantity by 1M for Buy
    increaseBtn.addEventListener('click', () => {
        let quantity = parseInt(quantityInput.value);
        quantityInput.value = quantity + 1;  // Increase by 1M
        updatePrice();
    });

    // Decrease quantity by 1M but don't go below 20M for Buy
    decreaseBtn.addEventListener('click', () => {
        let quantity = parseInt(quantityInput.value);
        if (quantity > minQuantity) {
            quantityInput.value = quantity - 1;  // Decrease by 1M
            updatePrice();
        }
    });

    // Prevent typing values below 20M for Buy
    quantityInput.addEventListener('input', () => {
        let quantity = parseInt(quantityInput.value);
        if (quantity < minQuantity || isNaN(quantity)) {
            quantityInput.value = minQuantity;
        }
        updatePrice();
    });

    // Increase quantity for Cash-Out
    cashoutIncreaseBtn.addEventListener('click', () => {
        let quantity = parseInt(cashoutQuantityInput.value);
        if (quantity < maxCashOut) {
            cashoutQuantityInput.value = quantity + 1;  // Increase by 1M
        }
    });

    // Decrease quantity for Cash-Out but don't go below 50M
    cashoutDecreaseBtn.addEventListener('click', () => {
        let quantity = parseInt(cashoutQuantityInput.value);
        if (quantity > minCashOut) {
            cashoutQuantityInput.value = quantity - 1;  // Decrease by 1M
        }
    });

    // Prevent typing values below 50M for Cash-Out or above the user's balance
    cashoutQuantityInput.addEventListener('input', () => {
        let quantity = parseInt(cashoutQuantityInput.value);
        if (quantity < minCashOut || quantity > maxCashOut || isNaN(quantity)) {
            cashoutQuantityInput.value = minCashOut;  // Reset to min value if invalid
        }
    });

    // Increase quantity for Deposit
    depositIncreaseBtn.addEventListener('click', () => {
        let quantity = parseInt(depositQuantityInput.value);
        depositQuantityInput.value = quantity + 1;  // Increase by 1M
    });

    // Decrease quantity for Deposit but don't go below 10M
    depositDecreaseBtn.addEventListener('click', () => {
        let quantity = parseInt(depositQuantityInput.value);
        if (quantity > minDeposit) {
            depositQuantityInput.value = quantity - 1;  // Decrease by 1M
        }
    });

    // Prevent typing values below 10M for Deposit
    depositQuantityInput.addEventListener('input', () => {
        let quantity = parseInt(depositQuantityInput.value);
        if (quantity < minDeposit || isNaN(quantity)) {
            depositQuantityInput.value = minDeposit;  // Reset to min value if invalid
        }
    });

    // Tooltip for Online/Offline status
    const statusIndicator = document.querySelector('.status-indicator');

    // Tooltip: Add "Online" or "Offline" on hover
    if (statusIndicator) {
        statusIndicator.addEventListener('mouseover', function() {
            const isOnline = statusIndicator.classList.contains('bg-green-500');
            statusIndicator.title = isOnline ? "Online" : "Offline";
        });
    }
});

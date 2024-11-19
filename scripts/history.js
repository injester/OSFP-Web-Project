document.addEventListener('DOMContentLoaded', function() {
    const orderHistoryBody = document.getElementById('order-history-body');
    const searchOrdersInput = document.getElementById('search-orders');

    // Ensure elements exist before adding event listener
    if (searchOrdersInput && orderHistoryBody) {
        // Search functionality
        searchOrdersInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const rows = orderHistoryBody.querySelectorAll('tr');

            rows.forEach(row => {
                const rowText = row.innerText.toLowerCase();
                row.style.display = rowText.includes(query) ? '' : 'none';
            });
        });
    } else {

    }
});

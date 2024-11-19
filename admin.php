<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if not admin
if (!isset($_SESSION['user_id']) || $_SESSION['rank'] < 10) {
    header('Location: login.php');
    exit;
}

require 'config.php'; // Include database connection

// Fetch current price per million
$stmt = $pdo->query("SELECT price_per_million FROM price WHERE id = 1");
$current_price = $stmt->fetchColumn();

include('header.php'); // Include header
?>

<div class="min-h-screen bg-gray-900 text-white p-8">
    <h1 class="text-4xl mb-6">Admin Dashboard</h1>

    <div class="mb-6">
        <button id="reveal-users" class="bg-yellow-500 py-2 px-4 rounded mr-4">Manage Users</button>
        <button id="reveal-orders" class="bg-blue-500 py-2 px-4 rounded">Manage Orders</button>
        <button id="change-price" class="bg-green-500 py-2 px-4 rounded">Change Price</button>
        <button id="reveal-stats" class="bg-purple-500 py-2 px-4 rounded ml-4">View Profit Statistics</button>
    </div>

    <div id="users-section" class="hidden">
        <?php include('panel/users_panel.php'); ?>
    </div>

    <div id="orders-section" class="hidden">
        <?php include('panel/orders_panel.php'); ?>
    </div>

    <!-- Profit Statistics Section -->
    <div id="stats-section" class="hidden bg-gray-800 p-6 rounded-lg">
        <h2 class="text-3xl font-bold text-center mb-4">Combined Profit Statistics</h2>
        <!-- Toggle Filters -->
        <div class="flex justify-center space-x-4 mb-4">
        </div>

        <canvas id="profitChart" class="w-full h-64"></canvas>
    </div>
</div>

<!-- Modal for changing price -->
<div id="price-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
    <div class="bg-gray-800 p-8 rounded-lg">
        <h2 class="text-2xl font-bold mb-4">Change Price per Million</h2>
        <form id="price-form">
            <div class="mb-4">
                <label for="price" class="block text-white">Current Price (€):</label>
                <input type="text" id="price" name="price_per_million" value="<?php echo number_format($current_price, 2); ?>" class="w-full p-2 rounded bg-gray-700 text-white" required>
            </div>
            <div class="flex justify-end">
                <button type="button" id="close-price-modal" class="mr-4 bg-red-500 text-white px-4 py-2 rounded">Cancel</button>
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Import Chart.js for the chart functionality -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Custom JavaScript for the dashboard -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const revealUsersBtn = document.getElementById('reveal-users');
    const revealOrdersBtn = document.getElementById('reveal-orders');
    const usersSection = document.getElementById('users-section');
    const ordersSection = document.getElementById('orders-section');
    const revealStatsBtn = document.getElementById('reveal-stats');
    const statsSection = document.getElementById('stats-section');
    let chartInstance;
    let filters = { bets: true, deposits: true, cashouts: true };

    revealUsersBtn.addEventListener('click', function() {
        usersSection.classList.remove('hidden');
        ordersSection.classList.add('hidden');
        statsSection.classList.add('hidden');
    });

    revealOrdersBtn.addEventListener('click', function() {
        ordersSection.classList.remove('hidden');
        usersSection.classList.add('hidden');
        statsSection.classList.add('hidden');
    });

    // Handle the price modal visibility
    document.getElementById('change-price').addEventListener('click', function() {
        document.getElementById('price-modal').classList.remove('hidden');
    });

    document.getElementById('close-price-modal').addEventListener('click', function() {
        document.getElementById('price-modal').classList.add('hidden');
    });

    // Handle the price form submission
    document.getElementById('price-form').addEventListener('submit', function(event) {
        event.preventDefault();
        const price = document.getElementById('price').value;

        fetch('panel/update_price.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `price_per_million=${price}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Price updated successfully!');
                document.getElementById('price-modal').classList.add('hidden');
            } else {
                alert('Failed to update price!');
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Fetch and initialize the statistics chart
    revealStatsBtn.addEventListener('click', async function() {
        statsSection.classList.toggle('hidden');
        if (!chartInstance) {
            const response = await fetch('panel/profit_data.php');
            const data = await response.json();
            if (data.success) {
                chartInstance = createChart(data.labels, data.profits, data.totals);
            } else {
                alert("Failed to load profit data.");
            }
        }
    });



    function toggleFilter(category) {
        filters[category] = !filters[category];
        updateChart();
    }

    // Create Chart.js line chart with filterable datasets
    function createChart(labels, profits, totals) {
        const ctx = document.getElementById('profitChart').getContext('2d');
        return new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Bets',
                        data: profits.bets,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderWidth: 2,
                        hidden: !filters.bets,
                    },
                    {
                        label: 'Deposits',
                        data: profits.deposits,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.2)',
                        borderWidth: 2,
                        hidden: !filters.deposits,
                    },
                    {
                        label: 'Cash-Outs',
                        data: profits.cashouts,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.2)',
                        borderWidth: 2,
                        hidden: !filters.cashouts,
                    },
                    {
                        label: 'Total Income/Outgoings',
                        data: totals, // Total line data
                        borderColor: '#a855f7', // Purple color for the total line
                        backgroundColor: 'rgba(168, 85, 247, 0.2)',
                        borderWidth: 2,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true, labels: { color: '#ffffff' } }
                },
                scales: {
                    x: { beginAtZero: true, ticks: { color: '#ffffff' } },
                    y: { beginAtZero: true, ticks: { color: '#ffffff' } }
                }
            }
        });
    }

    function updateChart() {
        chartInstance.data.datasets.forEach((dataset) => {
            dataset.hidden = !filters[dataset.label.toLowerCase()];
        });
        chartInstance.update();
    }
});
</script>

<?php include('footer.php'); ?>

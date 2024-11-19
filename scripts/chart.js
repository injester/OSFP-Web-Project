document.addEventListener('DOMContentLoaded', function() {
    const revealStatsBtn = document.getElementById('reveal-stats');
    const statsSection = document.getElementById('stats-section');
    let chartInstance;
    let filters = { bets: true, deposits: true, cashouts: true };

    document.getElementById('toggle-bets').addEventListener('click', () => toggleFilter('bets'));
    document.getElementById('toggle-deposits').addEventListener('click', () => toggleFilter('deposits'));
    document.getElementById('toggle-cashouts').addEventListener('click', () => toggleFilter('cashouts'));

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
                        borderColor: '#a855f7', // Purple color
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

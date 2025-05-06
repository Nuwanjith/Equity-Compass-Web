<?php //include './backend/includes/auth.php'; //login ?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelani Tyre Stock Dashboard</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard {
            display: flex;
            flex-direction: column;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .chart-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 30px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <h1>Kelani Tyre Stock Analysis</h1>
        
        <div class="chart-container">
            <canvas id="priceChart"></canvas>
        </div>
        
        <div class="chart-container">
            <canvas id="volumeChart"></canvas>
        </div>
    </div>

    <script>
        // Fetch data from backend API
        async function fetchStockData() {
    try {
        // Use absolute path to API
        const response = await fetch('http://localhost:8000/backend/api/stock_prices.php');
        
        // First check if response is OK
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Check content type before parsing
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            throw new Error(`Invalid content type. Received: ${contentType}\nResponse: ${text.substring(0, 100)}...`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            renderCharts(result.data);
        } else {
            console.error('API returned error:', result.error);
            alert('Failed to load stock data: ' + (result.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('API request failed:', error);
        alert('Failed to connect to server. Check console for details.');
    }
}

        // Render charts with data
        function renderCharts(data) {
            // Prepare data
            const dates = data.map(item => new Date(item.trade_date).toLocaleDateString());
            const closes = data.map(item => item.close_price);
            const volumes = data.map(item => item.volume);

            // Price Chart
            const priceCtx = document.getElementById('priceChart').getContext('2d');
            new Chart(priceCtx, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'Closing Price (LKR)',
                        data: closes,
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        pointRadius: 3,
                        pointBackgroundColor: '#4e73df',
                        pointBorderColor: '#4e73df',
                        pointHoverRadius: 5,
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: false,
                            ticks: {
                                callback: function(value) {
                                    return 'LKR ' + value.toFixed(2);
                                }
                            }
                        }
                    }
                }
            });

            // Volume Chart
            const volumeCtx = document.getElementById('volumeChart').getContext('2d');
            new Chart(volumeCtx, {
                type: 'bar',
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'Trading Volume',
                        data: volumes,
                        backgroundColor: 'rgba(78, 115, 223, 0.5)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', fetchStockData);
    </script>
</body>
</html>
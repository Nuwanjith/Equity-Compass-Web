<?php include(__DIR__ . '/../backend/includes/auth.php'); ?>
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
                    console.log('Received data:', result.data); // Debug log
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

        function renderCharts(data) {
            if (!data || data.length === 0) {
                console.error('No data received or empty data array');
                alert('No data available to display charts.');
                return;
            }

            // Prepare monthly data - ensure we're accessing the correct properties
            const months = data.map(item => item.month || item.Month || item.date || 'N/A');
            const avgPrices = data.map(item => {
                // Try different possible property names for average price
                return item.avg_close || item.avg_price || item.close || item.average || 0;
            });
            const volumes = data.map(item => {
                // Try different possible property names for volume
                return item.total_volume || item.volume || item.trading_volume || 0;
            });

            console.log('Chart data prepared:', { months, avgPrices, volumes }); // Debug log

            // Price Chart
            const priceCtx = document.getElementById('priceChart').getContext('2d');
            new Chart(priceCtx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Monthly Average Closing Price (LKR)',
                        data: avgPrices,
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        borderWidth: 2,
                        tension: 0.3,
                        pointRadius: 4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Avg: LKR ${context.parsed.y.toFixed(2)}`;
                                }
                            }
                        },
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            ticks: {
                                callback: function(value) {
                                    return 'LKR ' + value.toFixed(2);
                                }
                            },
                            title: {
                                display: true,
                                text: 'Price (LKR)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Month'
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
                    labels: months,
                    datasets: [{
                        label: 'Monthly Trading Volume',
                        data: volumes,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return (value / 1000000).toFixed(1) + 'M';
                                }
                            },
                            title: {
                                display: true,
                                text: 'Volume (millions)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Month'
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






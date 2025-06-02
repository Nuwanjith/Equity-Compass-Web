<?php include(__DIR__ . '/../backend/includes/auth.php'); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelani Tyre Stock Dashboard</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            display: flex;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        
        .sidebar {
            width: 200px;
            background: #f8f9fa;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            height: 100vh;
            position: fixed;
        }
        
        .dashboard {
            flex: 1;
            margin-left: 220px;
            padding: 20px;
            max-width: 1200px;
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
        
        .company-selector {
            margin-bottom: 30px;
        }
        
        .company-selector label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        
        .company-selector select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="company-selector">
            <label for="companyCode">Select Company:</label>
            <select id="companyCode">
                <option value="KCAB">Kelani Cables (KCAB)</option>
                <option value="TYRE">Kelani Tyres (TYRE)</option>
                <option value="SAMP">Sampath Bank (SAMP)</option>
            </select>
        </div>
    </div>
    
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
        // Global variables to store chart instances
        let priceChart = null;
        let volumeChart = null;
        
        // Get selected company code
        function getSelectedCompany() {
            return document.getElementById('companyCode').value;
        }
        
        // Setup event listener for company selection change
        function setupCompanySelector() {
            document.getElementById('companyCode').addEventListener('change', function() {
                fetchStockData();
            });
        }

        // Fetch data from backend API
        async function fetchStockData() {
            const companyCode = getSelectedCompany();
            try {
                // Use absolute path to API with company code parameter
                const response = await fetch(`http://localhost:8000/backend/api/stock_prices.php?company=${companyCode}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    throw new Error(`Invalid content type. Received: ${contentType}\nResponse: ${text.substring(0, 100)}...`);
                }
                
                const result = await response.json();
                
                if (result.success) {
                    console.log('Received data:', result.data);
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

            // Prepare monthly data
            const months = data.map(item => item.month || item.Month || item.date || 'N/A');
            const avgPrices = data.map(item => item.avg_close || item.avg_price || item.close || item.average || 0);
            const volumes = data.map(item => item.total_volume || item.volume || item.trading_volume || 0);

            // Destroy previous charts if they exist
            if (priceChart) priceChart.destroy();
            if (volumeChart) volumeChart.destroy();

            // Price Chart
            const priceCtx = document.getElementById('priceChart').getContext('2d');
            priceChart = new Chart(priceCtx, {
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
            volumeChart = new Chart(volumeCtx, {
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
        document.addEventListener('DOMContentLoaded', function() {
            setupCompanySelector();
            fetchStockData();
        });
    </script>
</body>
</html>
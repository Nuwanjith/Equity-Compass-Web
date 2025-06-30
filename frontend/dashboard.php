<?php include(__DIR__ . '/../backend/includes/auth.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelani Tyre Stock Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@2.1.1/dist/chartjs-plugin-annotation.min.js"></script>
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #1cc88a;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --dark-color: #5a5c69;
            --light-color: #f8f9fc;
            --sidebar-width: 220px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        body {
            display: flex;
            min-height: 100vh;
            background-color: #f8f9fc;
            color: #333;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
            height: 100vh;
            position: fixed;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            padding: 20px 0;
            z-index: 100;
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        
        .sidebar-header h3 {
            font-weight: 800;
            font-size: 1.2rem;
            text-align: center;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-item {
            padding: 10px 20px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .menu-item:hover {
            background-color: rgba(255,255,255,0.1);
        }
        
        .menu-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .menu-item.active {
            background-color: rgba(255,255,255,0.2);
            border-left: 4px solid white;
        }
        
        /* Main Content Styles */
        .dashboard {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 20px;
            max-width: calc(100% - var(--sidebar-width));
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e3e6f0;
        }
        
        .header h1 {
            color: var(--dark-color);
            font-weight: 700;
            font-size: 1.8rem;
            margin: 0;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
        }
        
        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
        
        /* Card Styles */
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 30px;
            border: none;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.5rem 0 rgba(58, 59, 69, 0.2);
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 15px 20px;
            border-radius: 8px 8px 0 0;
            font-weight: 700;
            color: var(--dark-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-body {
            padding: 20px;
        }
        
        /* Chart Container */
        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
        }
        
        /* Company Selector */
        .company-selector {
            margin-bottom: 20px;
            padding: 0 20px;
        }
        
        .company-selector label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: rgba(255,255,255,0.8);
        }
        
        .company-selector select {
            width: 100%;
            padding: 10px;
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 4px;
            font-size: 14px;
            background-color: rgba(255,255,255,0.1);
            color: white;
            cursor: pointer;
        }
        
        .company-selector select option {
            background-color: var(--primary-color);
            color: white;
        }
        
        /* Loading Spinner */
        .loading-spinner {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 10;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            border-top-color: var(--primary-color);
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
                overflow: hidden;
            }
            
            .sidebar-header h3, 
            .menu-item span {
                display: none;
            }
            
            .menu-item {
                justify-content: center;
            }
            
            .menu-item i {
                margin-right: 0;
                font-size: 1.2rem;
            }
            
            .dashboard {
                margin-left: 80px;
                max-width: calc(100% - 80px);
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Equity Compass</h3>
        </div>
        
        <div class="company-selector">
            <label for="companyCode">Select Company</label>
            <select id="companyCode">
                <option value="KCAB">Kelani Cables (KCAB)</option>
                <option value="TYRE" selected>Kelani Tyres (TYRE)</option>
                <option value="SAMP">Sampath Bank (SAMP)</option>
            </select>
        </div>
        
        <div class="sidebar-menu">
            <div class="menu-item active">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </div>
            <div class="menu-item">
                <i class="fas fa-history"></i>
                <span>Historical Data</span>
            </div>
            <div class="menu-item">
                <i class="fas fa-chart-pie"></i>
                <span>Analysis</span>
            </div>
            <div class="menu-item">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </div>
        </div>
    </div>
    
    <!-- Main Dashboard Content -->
    <div class="dashboard">
        <div class="header">
            <h1>Stock Performance Dashboard</h1>
            <div class="user-profile">
                <img src="https://ui-avatars.com/api/?name=Admin&background=4e73df&color=fff" alt="User">
                <span>Admin</span>
            </div>
        </div>
        
        <!-- Price Chart Card -->
        <div class="card">
            <div class="card-header">
                <span>Price Trend Analysis</span>
                <div class="chart-actions">
                    <i class="fas fa-download"></i>
                </div>
            </div>
            <div class="card-body">
                <div class="loading-spinner" id="priceLoading">
                    <div class="spinner"></div>
                </div>
                <div class="chart-container">
                    <canvas id="priceChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Volume Chart Card -->
        <div class="card">
            <div class="card-header">
                <span>Volume Analysis</span>
                <div class="chart-actions">
                    <i class="fas fa-download"></i>
                </div>
            </div>
            <div class="card-body">
                <div class="loading-spinner" id="volumeLoading">
                    <div class="spinner"></div>
                </div>
                <div class="chart-container">
                    <canvas id="volumeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global chart instances
        let priceChart = null;
        let volumeChart = null;
        
        // DOM Ready
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the dashboard
            initDashboard();
            
            // Setup event listeners
            setupEventListeners();
            
            // Load initial data
            fetchStockData();
        });
        
        function initDashboard() {
            // Any initialization logic can go here
            console.log('Dashboard initialized');
        }
        
        function setupEventListeners() {
            // Company selector change event
            document.getElementById('companyCode').addEventListener('change', fetchStockData);
            
            // Menu item click events
            document.querySelectorAll('.menu-item').forEach(item => {
                item.addEventListener('click', function() {
                    document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        }
        
        async function fetchStockData() {
            const companyCode = document.getElementById('companyCode').value;
            
            // Show loading spinners
            document.getElementById('priceLoading').style.display = 'block';
            document.getElementById('volumeLoading').style.display = 'block';
            
            try {
                const response = await fetch(`/backend/api/stock_prices.php?company=${companyCode}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result.success) {
                    renderCharts(result.data);
                } else {
                    throw new Error(result.error || 'Unknown error from API');
                }
            } catch (error) {
                console.error('Error fetching stock data:', error);
                alert('Failed to load stock data: ' + error.message);
            } finally {
                // Hide loading spinners
                document.getElementById('priceLoading').style.display = 'none';
                document.getElementById('volumeLoading').style.display = 'none';
            }
        }
        
        function renderCharts(data) {
            if (!data || data.length === 0) {
                console.error('No data received or empty data array');
                alert('No data available to display charts.');
                return;
            }

            // Prepare chart data
            const labels = data.map(item => item.month || item.Month || item.date || 'N/A');
            const prices = data.map(item => parseFloat(item.avg_close || item.avg_price || item.close || item.average || 0));
            const volumes = data.map(item => parseInt(item.total_volume || item.volume || item.trading_volume || 0));
            
            // Determine predicted data points (last point and line segment)
            const isPredictedPoint = labels.map((_, i) => i === labels.length - 1);
            const isPredictedLine = labels.map((_, i) => i === labels.length - 2);

            // Destroy existing charts
            if (priceChart) priceChart.destroy();
            if (volumeChart) volumeChart.destroy();

            // Render Price Chart with predicted styling
            renderPriceChart(labels, prices, isPredictedPoint, isPredictedLine);
            
            // Render Volume Chart
            renderVolumeChart(labels, volumes);
        }
        
        function renderPriceChart(labels, prices, isPredictedPoint, isPredictedLine) {
            const ctx = document.getElementById('priceChart').getContext('2d');
            
            priceChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Closing Price (LKR)',
                        data: prices,
                        borderColor: labels.map((_, i) => 
                            isPredictedLine[i] ? 'rgba(255, 99, 132, 1)' : 'rgba(78, 115, 223, 1)'
                        ),
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        borderWidth: labels.map((_, i) => 
                            isPredictedLine[i] ? 3 : 2
                        ),
                        borderDash: labels.map((_, i) => 
                            isPredictedLine[i] ? [5, 5] : []
                        ),
                        tension: 0.3,
                        pointRadius: labels.map((_, i) => 
                            isPredictedPoint[i] ? 6 : 4
                        ),
                        pointBackgroundColor: labels.map((_, i) => 
                            isPredictedPoint[i] ? 'rgba(255, 99, 132, 1)' : 'rgba(78, 115, 223, 1)'
                        ),
                        pointBorderColor: labels.map((_, i) => 
                            isPredictedPoint[i] ? 'rgba(255, 99, 132, 1)' : 'rgba(78, 115, 223, 1)'
                        ),
                        pointHoverRadius: 6,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += 'LKR ' + context.parsed.y.toFixed(2);
                                    if (context.dataIndex === context.dataset.data.length - 1) {
                                        label += ' (predicted)';
                                    }
                                    return label;
                                }
                            }
                        },
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Time Period',
                                font: {
                                    weight: 'bold'
                                }
                            }
                        },
                        y: {
                            beginAtZero: false,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            title: {
                                display: true,
                                text: 'Price (LKR)',
                                font: {
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'LKR ' + value.toFixed(2);
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        }
        
        function renderVolumeChart(labels, volumes) {
            const ctx = document.getElementById('volumeChart').getContext('2d');
            
            volumeChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Trading Volume',
                        data: volumes,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += (context.parsed.y / 1000000).toFixed(2) + 'M';
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Time Period',
                                font: {
                                    weight: 'bold'
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            title: {
                                display: true,
                                text: 'Volume',
                                font: {
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                callback: function(value) {
                                    return (value / 1000000).toFixed(1) + 'M';
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
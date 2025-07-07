<?php include(__DIR__ . '/../backend/includes/auth.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/styles.css">
    <title>Kelani Tyre Stock Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@2.1.1/dist/chartjs-plugin-annotation.min.js"></script>
    <style>
        /* Analysis page styles */
        .analysis-page {
            display: none;
            padding: 20px;
        }
        
        .valuation-comparison {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .valuation-metrics {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        
        .metric-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            width: 23%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .metric-card h3 {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .metric-card .value {
            font-size: 24px;
            font-weight: bold;
        }
        
        .metric-card .positive {
            color: #28a745;
        }
        
        .metric-card .negative {
            color: #dc3545;
        }
        
        .comparison-chart {
            height: 300px;
            margin-top: 20px;
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
            <div class="menu-item active" data-page="dashboard">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </div>
            <div class="menu-item" data-page="historical">
                <i class="fas fa-history"></i>
                <span>Historical Data</span>
            </div>
            <div class="menu-item" data-page="analysis">
                <i class="fas fa-chart-pie"></i>
                <span>Analysis</span>
            </div>
            <div class="menu-item" data-page="settings">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </div>
        </div>
    </div>
    
    <!-- Main Dashboard Content -->
    <div class="dashboard" id="dashboard-page">
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
    
    <!-- Analysis Page Content -->
    <div class="dashboard" id="analysis-page" style="display: none;">
        <div class="header">
            <h1>Valuation Analysis</h1>
            <div class="user-profile">
                <img src="https://ui-avatars.com/api/?name=Admin&background=4e73df&color=fff" alt="User">
                <span>Admin</span>
            </div>
        </div>
        
        <div class="valuation-comparison">
            <h2>Current Price vs. Valuation</h2>
            <p>Compare the current market price with our intrinsic valuation metrics</p>
            
            <div class="valuation-metrics">
                <div class="metric-card">
                    <h3>Current Price</h3>
                    <div class="value" id="current-price">LKR 45.20</div>
                    <div class="change">+0.50 (1.12%)</div>
                </div>
                
                <div class="metric-card">
                    <h3>Intrinsic Value</h3>
                    <div class="value" id="intrinsic-value">LKR 52.75</div>
                    <div class="change positive">Undervalued by 14.3%</div>
                </div>
                
                <div class="metric-card">
                    <h3>52-Week High</h3>
                    <div class="value" id="high-price">LKR 58.30</div>
                    <div class="change negative">-22.5% from high</div>
                </div>
                
                <div class="metric-card">
                    <h3>52-Week Low</h3>
                    <div class="value" id="low-price">LKR 38.10</div>
                    <div class="change positive">+18.6% from low</div>
                </div>
            </div>
            
            <div class="comparison-chart">
                <canvas id="valuationChart"></canvas>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <span>Valuation Metrics</span>
            </div>
            <div class="card-body">
                <table class="valuation-table">
                    <thead>
                        <tr>
                            <th>Metric</th>
                            <th>Value</th>
                            <th>Sector Avg</th>
                            <th>Premium/Discount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>P/E Ratio</td>
                            <td>8.5x</td>
                            <td>12.3x</td>
                            <td class="positive">-30.9%</td>
                        </tr>
                        <tr>
                            <td>P/B Ratio</td>
                            <td>1.2x</td>
                            <td>1.8x</td>
                            <td class="positive">-33.3%</td>
                        </tr>
                        <tr>
                            <td>Dividend Yield</td>
                            <td>4.2%</td>
                            <td>3.1%</td>
                            <td class="positive">+35.5%</td>
                        </tr>
                        <tr>
                            <td>ROE</td>
                            <td>14.5%</td>
                            <td>11.2%</td>
                            <td class="positive">+29.5%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="assets/dashboard.js"></script>
    <script>
        // Page navigation functionality
        document.addEventListener('DOMContentLoaded', function() {
            const menuItems = document.querySelectorAll('.menu-item');
            
            menuItems.forEach(item => {
                item.addEventListener('click', function() {
                    // Remove active class from all items
                    menuItems.forEach(i => i.classList.remove('active'));
                    
                    // Add active class to clicked item
                    this.classList.add('active');
                    
                    // Get the page to show
                    const page = this.getAttribute('data-page');
                    
                    // Hide all pages
                    document.querySelectorAll('.dashboard').forEach(p => {
                        p.style.display = 'none';
                    });
                    
                    // Show the selected page
                    if (page === 'analysis') {
                        document.getElementById('analysis-page').style.display = 'block';
                        // You would load analysis data here
                        loadAnalysisData();
                    } else if (page === 'dashboard') {
                        document.getElementById('dashboard-page').style.display = 'block';
                    }
                    // Add other pages as needed
                });
            });
            
        });
async function fetchStockDataAndUpdate() {
    try {
        const response = await fetch('http://localhost:8000/backend/api/stock_prices.php?company=TYRE');
        const result = await response.json();

        if (result.success && Array.isArray(result.data)) {
            // Filter only historical entries
            const historicalData = result.data.filter(entry => entry.type === 'historical');

            if (historicalData.length > 0) {
                // Find the most recent historical record (based on sort_key)
                const latest = historicalData.reduce((a, b) =>
                    a.sort_key > b.sort_key ? a : b
                );

                const currentPrice = latest.avg_price;
                updateCurrentPrice(currentPrice);
            }
        }
    } catch (error) {
        console.error('Error fetching stock data:', error);
    }
}

function updateCurrentPrice(price) {
    // Update the DOM value
    const priceElement = document.getElementById('current-price');
    priceElement.textContent = `LKR ${price.toFixed(2)}`;

    // Update chart if needed
    if (valuationChart) {
        valuationChart.data.datasets[0].data[0] = price;
        valuationChart.update();
    }
}

let valuationChart;

function loadAnalysisData() {
    const ctx = document.getElementById('valuationChart').getContext('2d');
    valuationChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Current Price', 'Intrinsic Value', '52-Week High', '52-Week Low'],
            datasets: [{
                label: 'Price Comparison (LKR)',
                data: [0, 52.75, 58.3, 38.1], // Placeholder, first value will be replaced
                backgroundColor: [
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(255, 159, 64, 0.7)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: false,
                    title: {
                        display: true,
                        text: 'Price (LKR)'
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Price Valuation Comparison'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y.toFixed(2) + ' LKR';
                        }
                    }
                }
            }
        }
    });

    // Fetch current price from API and update
    fetchStockDataAndUpdate();
}
    </script>
</body>
</html>
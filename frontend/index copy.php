<!DOCTYPE html>
<html>
<head>
    <title>Stock Price Analysis Dashboard</title>
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .dashboard {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 220px;
            background-color: #2c3e50;
            color: white;
            padding: 20px 0;
        }
        .main-content {
            flex: 1;
            padding: 20px;
        }
        .logo {
            text-align: center;
            padding: 10px 0 30px;
            border-bottom: 1px solid #34495e;
        }
        .nav-menu {
            margin-top: 30px;
        }
        .nav-item {
            padding: 12px 20px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .nav-item:hover, .nav-item.active {
            background-color: #34495e;
        }
        .nav-item i {
            margin-right: 10px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }
        .company-selector {
            display: flex;
            align-items: center;
        }
        select {
            padding: 8px 15px;
            border-radius: 4px;
            border: 1px solid #ddd;
            background-color: white;
            font-size: 14px;
            margin-left: 10px;
        }
        .chart-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 30px;
        }
        .chart-title {
            margin-top: 0;
            color: #2c3e50;
        }
        .page {
            display: none;
        }
        .page.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <div class="logo">
                <h2>Stock Analytics</h2>
            </div>
            <div class="nav-menu">
                <div class="nav-item active" onclick="showPage('dashboard')">
                    <i>📊</i> Dashboard
                </div>
                <div class="nav-item" onclick="showPage('analysis')">
                    <i>📈</i> Price Analysis
                </div>
                <div class="nav-item" onclick="showPage('reports')">
                    <i>📑</i> Reports
                </div>
                <div class="nav-item" onclick="showPage('settings')">
                    <i>⚙️</i> Settings
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Header with Company Selector -->
            <div class="header">
                <h1 id="page-title">Dashboard</h1>
                <div class="company-selector">
                    <span>Select Company:</span>
                    <select id="company-select" onchange="loadCompanyData()">
                        <option value="AAPL">Apple Inc. (AAPL)</option>
                        <option value="GOOGL">Alphabet Inc. (GOOGL)</option>
                        <option value="MSFT">Microsoft (MSFT)</option>
                        <option value="AMZN">Amazon (AMZN)</option>
                        <option value="TSLA">Tesla (TSLA)</option>
                    </select>
                </div>
            </div>

            <!-- Dashboard Page -->
            <div id="dashboard-page" class="page active">
                <div class="chart-container">
                    <h2 class="chart-title">Overview</h2>
                    <div id="overview-chart" style="height: 300px;"></div>
                </div>
                <div class="chart-container">
                    <h2 class="chart-title">Recent Performance</h2>
                    <div id="performance-chart" style="height: 300px;"></div>
                </div>
            </div>

            <!-- Analysis Page -->
            <div id="analysis-page" class="page">
                <div class="chart-container">
                    <h2 class="chart-title">Price Trend Analysis</h2>
                    <div id="analysis-chart" style="height: 400px;"></div>
                </div>
            </div>

            <!-- Other Pages -->
            <div id="reports-page" class="page">
                <h2>Reports</h2>
                <p>Financial reports and analysis will appear here.</p>
            </div>
            <div id="settings-page" class="page">
                <h2>Settings</h2>
                <p>User preferences and settings will appear here.</p>
            </div>
        </div>
    </div>

    <script>
        // Sample data for different companies
        const companyData = {
            AAPL: {
                name: "Apple Inc.",
                ticker: "AAPL",
                historical: generatePriceData(150, 180),
                predicted: generatePriceData(180, 220, true)
            },
            GOOGL: {
                name: "Alphabet Inc.",
                ticker: "GOOGL",
                historical: generatePriceData(120, 150),
                predicted: generatePriceData(150, 180, true)
            },
            MSFT: {
                name: "Microsoft",
                ticker: "MSFT",
                historical: generatePriceData(250, 300),
                predicted: generatePriceData(300, 350, true)
            },
            AMZN: {
                name: "Amazon",
                ticker: "AMZN",
                historical: generatePriceData(100, 130),
                predicted: generatePriceData(130, 160, true)
            },
            TSLA: {
                name: "Tesla",
                ticker: "TSLA",
                historical: generatePriceData(180, 220),
                predicted: generatePriceData(220, 250, true)
            }
        };

        // Generate sample price data
        function generatePriceData(min, max, isFuture = false) {
            const months = getMonthLabels();
            const data = [];
            let currentValue = min + Math.random() * (max - min);
            
            months.forEach((month, index) => {
                // For historical data or all data if not future
                if (!isFuture || index < (12 - new Date().getMonth())) {
                    const fluctuation = (Math.random() - 0.5) * 10;
                    currentValue = Math.max(min, Math.min(max, currentValue + fluctuation));
                    data.push({ label: month, y: parseFloat(currentValue.toFixed(2)) });
                } else {
                    // For future predictions
                    const trend = 1 + (index - (12 - new Date().getMonth())) * 0.5;
                    const randomFactor = (Math.random() - 0.3) * 8;
                    const predictedValue = currentValue + trend + randomFactor;
                    data.push({ label: month, y: parseFloat(predictedValue.toFixed(2)) });
                }
            });
            
            return data;
        }

        // Get month labels for the last 12 months
        function getMonthLabels() {
            const months = [];
            for (let i = 11; i >= 0; i--) {
                const date = new Date();
                date.setMonth(date.getMonth() - i);
                months.push(date.toLocaleString('default', { month: 'short', year: 'numeric' }));
            }
            return months;
        }

        // Load data for selected company
        function loadCompanyData() {
            const select = document.getElementById('company-select');
            const company = companyData[select.value];
            
            // Update dashboard charts
            renderOverviewChart(company);
            renderPerformanceChart(company);
            renderAnalysisChart(company);
        }

        // Render overview chart
        function renderOverviewChart(company) {
            const chart = new CanvasJS.Chart("overview-chart", {
                theme: "light2",
                title: {
                    text: `${company.name} (${company.ticker})`
                },
                axisY: {
                    prefix: "$"
                },
                data: [{
                    type: "area",
                    dataPoints: company.historical.slice(-6) // Last 6 months
                }]
            });
            chart.render();
        }

        // Render performance chart
        function renderPerformanceChart(company) {
            const chart = new CanvasJS.Chart("performance-chart", {
                theme: "light2",
                title: {
                    text: "Daily Performance"
                },
                axisX: {
                    valueFormatString: "DD MMM"
                },
                axisY: {
                    prefix: "$"
                },
                data: [{
                    type: "candlestick",
                    risingColor: "#2ecc71",
                    fallingColor: "#e74c3c",
                    dataPoints: generateDailyData(company.historical[company.historical.length - 1].y)
                }]
            });
            chart.render();
        }

        // Render analysis chart
            // Render analysis chart with only two lines
            function renderAnalysisChart(companyData) {
                // Filter out only historical and predicted data points
                const historicalData = companyData.historical.map(item => ({
                    label: item.month_display || item.label,
                    y: item.avg_price || item.y
                }));
                
                const predictedData = companyData.predicted.map(item => ({
                    label: item.month_display || item.label,
                    y: item.avg_price || item.y
                }));

                const chart = new CanvasJS.Chart("analysis-chart", {
                    animationEnabled: true,
                    theme: "light2",
                    title: {
                        text: "Historical vs Predicted Prices"
                    },
                    axisX: {
                        labelAngle: -45,
                        title: "Month"
                    },
                    axisY: {
                        prefix: "$",
                        title: "Price"
                    },
                    toolTip: {
                        shared: true,
                        content: "{label}<br/>{name}: ${y}"
                    },
                    data: [
                        {
                            type: "line",
                            name: "Historical Prices",
                            showInLegend: true,
                            color: "#3498db",
                            lineThickness: 3,
                            dataPoints: historicalData
                        },
                        {
                            type: "line",
                            name: "Predicted Prices",
                            showInLegend: true,
                            color: "#e74c3c",
                            lineThickness: 3,
                            lineDashType: "dash",
                            dataPoints: predictedData
                        }
                    ]
                });
                chart.render();
            }

        // Generate sample daily data
        function generateDailyData(lastPrice) {
            const data = [];
            const basePrice = lastPrice;
            
            for (let i = 10; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                
                const open = basePrice + (Math.random() - 0.5) * 5;
                const close = open + (Math.random() - 0.5) * 3;
                const high = Math.max(open, close) + Math.random() * 2;
                const low = Math.min(open, close) - Math.random() * 2;
                
                data.push({
                    x: date,
                    y: [open, high, low, close]
                });
            }
            
            return data;
        }

        // Show selected page
        function showPage(pageId) {
            // Hide all pages
            document.querySelectorAll('.page').forEach(page => {
                page.classList.remove('active');
            });
            
            // Show selected page
            document.getElementById(`${pageId}-page`).classList.add('active');
            
            // Update active nav item
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            event.currentTarget.classList.add('active');
            
            // Update page title
            document.getElementById('page-title').textContent = 
                event.currentTarget.textContent.replace(/[^a-zA-Z ]/g, '').trim();
        }

        // Initialize the dashboard
        window.onload = function() {
            loadCompanyData();
        };
    </script>
</body>
</html>
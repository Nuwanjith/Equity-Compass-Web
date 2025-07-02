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

    <script src="assets/dashboard.js"></script>
</body>
</html>
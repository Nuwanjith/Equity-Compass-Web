<?php
include(__DIR__ . '/../backend/includes/auth.php');
$companies = ["TYRE", "SAMP", "KCAB", "DIPD"];
$selectedCompany = $_GET['company'] ?? $companies[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Analysis - <?= htmlspecialchars($selectedCompany) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .user-avatar { width:32px; height:32px; border-radius:50%; object-fit:cover; margin-right:10px; }
        .user-info { display:flex; align-items:center; margin-right:15px; color:white; }
        .loading-spinner {
            display:inline-block; width:20px; height:20px; border:3px solid rgba(0,0,0,.1);
            border-radius:50%; border-top-color:#007bff; animation:spin 1s ease-in-out infinite; margin-left:10px;
        }
        @keyframes spin { to { transform:rotate(360deg); } }
        .valuation-table { margin-top:30px; }
        .valuation-table th { background-color:#f8f9fa; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Dashboard</a>
        <div class="navbar-nav me-auto">
            <a class="nav-link active" href="analysis.php?company=<?= urlencode($selectedCompany) ?>">Analysis</a>
            <a class="nav-link" href="predictions.php?company=<?= urlencode($selectedCompany) ?>">Predictions</a>
        </div>
        <div class="d-flex align-items-center">
            <select class="form-select me-3" onchange="location='?company='+encodeURIComponent(this.value)">
                <?php foreach ($companies as $company): ?>
                    <option value="<?= htmlspecialchars($company) ?>" <?= $company === $selectedCompany ? 'selected' : '' ?>>
                        <?= htmlspecialchars($company) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($_SESSION['username'])): ?>
                <div class="user-info">
                    <img src="./assets/images/avatar.png" alt="Avatar" class="user-avatar">
                    <span><?= htmlspecialchars($_SESSION['username']) ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h2>Valuation Analysis for <strong id="companyName"><?= htmlspecialchars($selectedCompany) ?></strong> 
        <span id="loadingIndicator" class="loading-spinner" style="display:none"></span></h2>
    
    <div class="row">
        <div class="col-md-8">
            <canvas id="valuationChart" width="600" height="400"></canvas>
        </div>
        <div class="col-md-4">
            <table class="table table-bordered valuation-table">
                <thead>
                    <tr>
                        <th>Metric</th>
                        <th>Value (LKR)</th>
                    </tr>
                </thead>
                <tbody id="valuationTableBody">
                    <tr>
                        <td>NAV Valuation</td>
                        <td id="navValue">-</td>
                    </tr>
                    <tr>
                        <td>Graham Number</td>
                        <td id="grahamValue">-</td>
                    </tr>
                    <tr>
                        <td>Current Price</td>
                        <td id="currentPrice">-</td>
                    </tr>
                    <tr>
                        <td>Predicted Price</td>
                        <td id="predictedPrice">-</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('valuationChart').getContext('2d');
let valuationChart;

async function fetchData(url) {
    try {
        const res = await fetch(url);
        return await res.json();
    } catch (e) {
        console.error(`Error fetching ${url}:`, e);
        return null;
    }
}

async function loadChartData(company) {
    document.getElementById('loadingIndicator').style.display = 'inline-block';
    
    try {
        const [prices, valuations, predictions] = await Promise.all([
            fetchData(`http://localhost:8000/backend/api/stock_prices.php?company=${encodeURIComponent(company)}`),
            fetchData(`http://localhost:8000/backend/api/stock_valuations.php?company=${encodeURIComponent(company)}`),
            fetchData(`http://localhost:8000/backend/api/stock_predictions.php?company=${encodeURIComponent(company)}&limit=1`)
        ]);

        // Get current price
        let currentPrice = null;
        if (prices?.success && prices.data?.length > 0) {
            const historicalPrices = prices.data.filter(p => p.type === 'historical');
            if (historicalPrices.length > 0) {
                currentPrice = historicalPrices[0].avg_price;
            }
        }

        // Update table
        document.getElementById('currentPrice').textContent = currentPrice?.toFixed(2) || '-';
        document.getElementById('navValue').textContent = valuations?.data?.nav_valuation?.toFixed(2) || '-';
        document.getElementById('grahamValue').textContent = valuations?.data?.graham_valuation?.toFixed(2) || '-';
        document.getElementById('predictedPrice').textContent = predictions?.data?.[0]?.avg_price?.toFixed(2) || '-';

        // Prepare chart data
        const labels = ['NAV', 'Graham Number', 'Current Price', 'Predicted Price'];
        const values = [
            valuations?.data?.nav_valuation || 0,
            valuations?.data?.graham_valuation || 0,
            currentPrice || 0,
            predictions?.data?.[0]?.avg_price || 0
        ];

        console.log('Chart data prepared:', {labels, values}); // Debug log

        // Destroy previous chart if exists
        if (valuationChart) {
            valuationChart.destroy();
        }

        // Create new chart
        valuationChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Valuation Metrics (LKR)',
                    data: values,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(153, 102, 255, 0.7)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: { display: true, text: 'Valuation Comparison' },
                    legend: { display: false },
                    tooltip: { 
                        callbacks: { 
                            label: ctx => `LKR ${ctx.raw.toFixed(2)}`
                        } 
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: false,
                        title: { display: true, text: 'Value (LKR)' }
                    }
                }
            }
        });

    } catch (error) {
        console.error('Error loading chart:', error);
    } finally {
        document.getElementById('loadingIndicator').style.display = 'none';
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    const company = new URLSearchParams(window.location.search).get('company') || '<?= urlencode($companies[0]) ?>';
    loadChartData(company);
});
</script>
</body>
</html>
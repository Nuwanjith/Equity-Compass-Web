<?php
include(__DIR__ . '/../backend/includes/auth.php');
$companies = ["SAMP", "KCAB", "TYRE"];
$selectedCompany = $_GET['company'] ?? $companies[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Predictions - <?= htmlspecialchars($selectedCompany) ?></title>
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
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Dashboard</a>
        <div class="navbar-nav me-auto">
            <a class="nav-link" href="analysis.php?company=<?= urlencode($selectedCompany) ?>">Analysis</a>
            <a class="nav-link active" href="predictions.php?company=<?= urlencode($selectedCompany) ?>">Predictions</a>
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
                    <img src="../assets/images/avatar.png" alt="Avatar" class="user-avatar">
                    <span><?= htmlspecialchars($_SESSION['username']) ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h2>Price Forecast for <strong id="companyName"><?= htmlspecialchars($selectedCompany) ?></strong> 
        <span id="loadingIndicator" class="loading-spinner" style="display:none"></span></h2>
    <canvas id="priceChart" width="800" height="400"></canvas>
</div>

<script>
const ctx = document.getElementById('priceChart').getContext('2d');
let priceChart;

async function fetchData(url) {
    try {
        const res = await fetch(url);
        return await res.json();
    } catch (e) {
        console.error(`Error fetching ${url}:`, e);
        return null;
    }
}

function processData(historical, predictions) {
    const allDates = [
        ...(historical?.data?.map(item => item.month) || []),
        ...(predictions?.data?.map(item => item.month) || [])
    ].filter((v, i, a) => a.indexOf(v) === i).sort();
    
    return {
        labels: allDates,
        historical: allDates.map(date => 
            historical?.data?.find(h => h.month === date)?.avg_price || null),
        predictions: allDates.map(date => 
            predictions?.data?.find(p => p.month === date)?.avg_price || null)
    };
}

async function loadChartData(company) {
    document.getElementById('loadingIndicator').style.display = 'inline-block';
    
    const [historical, predictions] = await Promise.all([
        fetchData(`http://localhost:8000/backend/api/stock_prices.php?company=${encodeURIComponent(company)}`),
        fetchData(`http://localhost:8000/backend/api/stock_predictions.php?company=${encodeURIComponent(company)}`)
    ]);
    
    const {labels, historical: histData, predictions: predData} = processData(historical, predictions);
    
    if (!priceChart) {
        priceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Historical Price',
                        data: histData,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'transparent',
                        tension: 0.1
                    },
                    {
                        label: 'Predicted Price',
                        data: predData,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'transparent',
                        borderDash: [5, 5],
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: { display: true, text: 'Historical vs Predicted Prices' },
                    legend: { position: 'top' },
                    tooltip: { callbacks: { label: ctx => `${ctx.dataset.label}: ${ctx.raw?.toFixed(2) || 'N/A'}` } }
                },
                scales: {
                    y: { title: { display: true, text: 'Price ($)' } },
                    x: { title: { display: true, text: 'Month' } }
                }
            }
        });
    } else {
        priceChart.data.labels = labels;
        priceChart.data.datasets[0].data = histData;
        priceChart.data.datasets[1].data = predData;
        priceChart.update();
    }
    
    document.getElementById('loadingIndicator').style.display = 'none';
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    const company = new URLSearchParams(window.location.search).get('company') || '<?= urlencode($companies[0]) ?>';
    loadChartData(company);
});
</script>
</body>
</html>
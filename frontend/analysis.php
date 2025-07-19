<?php
session_start(); // Added to ensure session variables are available
$companies = ["TYRE", "SAMP", "KCAB", "DIPD"];
$selectedCompany = $_GET['company'] ?? $companies[0];
$valuationData = [
    ['metric' => 'Current Market Price', 'value' => 145.00, 'remarks' => 'Latest closing price'],
    ['metric' => 'Net Asset Value (NAV)', 'value' => 130.00, 'remarks' => 'From financial report'],
    ['metric' => 'Graham Number', 'value' => 160.45, 'remarks' => '√(22.5 × EPS × BVPS)'],
    ['metric' => 'P/B Ratio', 'value' => 1.12, 'remarks' => 'Price / Book Value'],
    ['metric' => 'Predicted Price', 'value' => 150.75, 'remarks' => 'Next month forecast', 'highlight' => true]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Analysis - <?=htmlspecialchars($selectedCompany)?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 8px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Dashboard</a>
        <div class="navbar-nav me-auto">
            <a class="nav-link active" href="analysis.php?company=<?=urlencode($selectedCompany)?>">Analysis</a>
            <a class="nav-link" href="predictions.php?company=<?=urlencode($selectedCompany)?>">Predictions</a>
        </div>
        <div class="d-flex align-items-center">
            <form class="d-flex me-3">
                <select class="form-select" onchange="location = this.value;">
                    <?php foreach ($companies as $company): ?>
                        <option value="?company=<?=urlencode($company)?>" <?=$company === $selectedCompany ? 'selected' : ''?>>
                            <?=$company?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
            <?php if (isset($_SESSION['username'])): ?>
                <div class="d-flex align-items-center text-white">
                    <img src="./assets/images/avatar.png" alt="User Avatar" class="user-avatar">
                    <span><?=htmlspecialchars($_SESSION['username'])?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="mb-4">Valuation for <strong><?=htmlspecialchars($selectedCompany)?></strong></h2>

    <div class="card mb-4">
        <div class="card-body p-3">
            <canvas id="valuationChart" height="120"></canvas>
        </div>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr><th>Metric</th><th>Value (LKR)</th><th>Remarks</th></tr>
        </thead>
        <tbody>
            <?php foreach ($valuationData as $row): ?>
                <tr <?=!empty($row['highlight']) ? 'class="table-success"' : ''?>>
                    <td><?=$row['metric']?></td>
                    <td><?=number_format($row['value'], 2)?></td>
                    <td><?=$row['remarks']?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    new Chart(document.getElementById('valuationChart'), {
        type: 'bar',
        data: {
            labels: <?=json_encode(array_column(array_filter($valuationData, function($item) { 
                return $item['metric'] !== 'P/B Ratio'; 
            }), 'metric'))?>,
            datasets: [{
                data: <?=json_encode(array_column(array_filter($valuationData, function($item) { 
                    return $item['metric'] !== 'P/B Ratio'; 
                }), 'value'))?>,
                backgroundColor: ['#36a2eb','#ff6384','#ffce56','#4bc0c0'],
                borderColor: ['#36a2eb','#ff6384','#ffce56','#4bc0c0'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: false } }
        }
    });
});
</script>
</body>
</html>
<?php
$companies = ["ABC Corp", "XYZ Ltd", "Kelani Tyres"];
$selectedCompany = $_GET['company'] ?? $companies[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Predictions - <?php echo htmlspecialchars($selectedCompany); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Dashboard</a>
        <div class="navbar-nav me-auto">
            <a class="nav-link" href="analysis.php?company=<?php echo urlencode($selectedCompany); ?>">Analysis</a>
            <a class="nav-link active" href="predictions.php?company=<?php echo urlencode($selectedCompany); ?>">Predictions</a>
        </div>
        <form class="d-flex">
            <select class="form-select" onchange="location = this.value;">
                <?php foreach ($companies as $company): ?>
                    <option value="?company=<?php echo urlencode($company); ?>" <?php echo ($company === $selectedCompany) ? 'selected' : ''; ?>>
                        <?php echo $company; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
</nav>

<div class="container mt-5">
    <h2>Price Forecast for <strong><?php echo htmlspecialchars($selectedCompany); ?></strong></h2>
    <canvas id="priceChart" width="800" height="400"></canvas>
</div>

<script>
const ctx = document.getElementById('priceChart').getContext('2d');
const priceChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [
            {
                label: 'Historical Price',
                data: [100, 110, 105, 115, 120, 125],
                borderColor: 'blue',
                backgroundColor: 'transparent',
                tension: 0.3
            },
            {
                label: 'Predicted Price',
                data: [128, 130, 135, 138, 140, 142],
                borderColor: 'green',
                backgroundColor: 'transparent',
                borderDash: [5, 5],
                tension: 0.3
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Historical vs Predicted Prices'
            },
            legend: {
                position: 'top',
            }
        }
    }
});
</script>

</body>
</html>

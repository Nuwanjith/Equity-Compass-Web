<?php
include(__DIR__ . '/../backend/includes/auth.php');
$companies = ["ABC Corp", "XYZ Ltd", "Kelani Tyres"];
$selectedCompany = $_GET['company'] ?? $companies[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Predictions - <?php echo htmlspecialchars($selectedCompany); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }
        .user-info {
            display: flex;
            align-items: center;
            margin-right: 15px;
            color: white;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Dashboard</a>
        <div class="navbar-nav me-auto">
            <a class="nav-link" href="analysis.php?company=<?php echo urlencode($selectedCompany); ?>">Analysis</a>
            <a class="nav-link active" href="predictions.php?company=<?php echo urlencode($selectedCompany); ?>">Predictions</a>
        </div>
        <div class="d-flex align-items-center">
            <!-- Company Selector (now on the left) -->
            <form class="d-flex me-3"> <!-- Added me-3 for right margin -->
                <select class="form-select" onchange="location = this.value;">
                    <?php foreach ($companies as $company): ?>
                        <option value="?company=<?php echo urlencode($company); ?>" <?php echo ($company === $selectedCompany) ? 'selected' : ''; ?>>
                            <?php echo $company; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
            
            <!-- User Info (now on the right) -->
            <?php if (isset($_SESSION['username'])): ?>
                <div class="user-info">
                    <img src="../assets/images/avatar.png" alt="User Avatar" class="user-avatar">
                    <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
            <?php endif; ?>
        </div>
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
<?php
$companies = ["ABC Corp", "XYZ Ltd", "Kelani Tyres"];
$selectedCompany = $_GET['company'] ?? $companies[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Analysis - <?php echo htmlspecialchars($selectedCompany); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Dashboard</a>
        <div class="navbar-nav me-auto">
            <a class="nav-link active" href="analysis.php?company=<?php echo urlencode($selectedCompany); ?>">Analysis</a>
            <a class="nav-link" href="predictions.php?company=<?php echo urlencode($selectedCompany); ?>">Predictions</a>
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
    <h2 class="mb-4">Valuation for <strong><?php echo htmlspecialchars($selectedCompany); ?></strong></h2>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
        <tr>
            <th>Metric</th>
            <th>Value (LKR)</th>
            <th>Remarks</th>
        </tr>
        </thead>
        <tbody>
        <tr><td>Current Market Price</td><td>145.00</td><td>Latest closing price</td></tr>
        <tr><td>Net Asset Value (NAV)</td><td>130.00</td><td>From financial report</td></tr>
        <tr><td>Graham Number</td><td>160.45</td><td>√(22.5 × EPS × BVPS)</td></tr>
        <tr><td>P/B Ratio</td><td>1.12</td><td>Price / Book Value</td></tr>
        <tr class="table-success"><td>Predicted Price</td><td>150.75</td><td>Next month forecast</td></tr>
        </tbody>
    </table>
</div>
</body>
</html>

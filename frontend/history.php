<?php
$pageTitle = "Dashboard Overview";
$pageIcon = "fas fa-home";
include('includes/header.php');
?>

<div class="row">
    <!-- Quick Stats -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-coins me-2"></i>Current Value</h5>
                <h2 class="text-primary">$12,457.32</h2>
                <p class="text-success"><i class="fas fa-arrow-up me-1"></i> 2.4% today</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-chart-bar me-2"></i>Top Performer</h5>
                <h4>AAPL</h4>
                <p class="text-success"><i class="fas fa-arrow-up me-1"></i> 5.2% this week</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-exclamation-triangle me-2"></i>Watchlist</h5>
                <p>Tesla (TSLA) approaching resistance at $280</p>
                <a href="predictions.php" class="btn btn-sm btn-outline-primary">View Predictions</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-line me-2"></i>Portfolio Performance</h5>
            </div>
            <div class="card-body">
                <canvas id="portfolioChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-star me-2"></i>Recommended Stocks</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <a href="valuations.php?stock=AAPL" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Apple (AAPL)</h6>
                            <small class="text-success">Undervalued</small>
                        </div>
                        <small>Target: $195 (12% upside)</small>
                    </a>
                    <a href="valuations.php?stock=MSFT" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Microsoft (MSFT)</h6>
                            <small class="text-warning">Fair Value</small>
                        </div>
                        <small>Target: $340 (5% upside)</small>
                    </a>
                    <a href="valuations.php?stock=GOOGL" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Alphabet (GOOGL)</h6>
                            <small class="text-danger">Overvalued</small>
                        </div>
                        <small>Target: $125 (8% downside)</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
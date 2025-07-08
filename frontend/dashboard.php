<?php include(__DIR__ . '/../backend/includes/auth.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar {
            background-color: #343a40;
            min-height: 100vh;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }
        .chart-container {
            position: relative;
            height: 300px;
        }
        .price-highlight {
            font-size: 1.8rem;
            font-weight: bold;
        }
        .prediction-up {
            color: #28a745;
        }
        .prediction-down {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-3 text-white">
                    <h4>Equity Compass</h4>
                </div>
                <div class="p-3">
                    <select id="companyCode" class="form-select bg-dark text-white">
                        <option value="KCAB">Kelani Cables</option>
                        <option value="TYRE" selected>Kelani Tyres</option>
                        <option value="SAMP">Sampath Bank</option>
                    </select>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" data-page="dashboard">
                            <i class="fas fa-chart-line me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-page="analysis">
                            <i class="fas fa-chart-pie me-2"></i> Analysis
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 p-4">
                <!-- Dashboard -->
                <div id="dashboard-page">
                    <div class="d-flex justify-content-between mb-4">
                        <h2 id="company-title">Kelani Tyres Dashboard</h2>
                    </div>
                    
                    <!-- Price Highlights -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6 text-center">
                                            <h6>Current Price</h6>
                                            <div class="price-highlight" id="current-price">LKR 45.20</div>
                                        </div>
                                        <div class="col-6 text-center">
                                            <h6>Next Month Prediction</h6>
                                            <div class="price-highlight prediction-up" id="predicted-price">LKR 47.80 (+5.8%)</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Charts -->
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Price Trend</h5>
                                    <div class="chart-container">
                                        <canvas id="priceChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Volume</h5>
                                    <div class="chart-container">
                                        <canvas id="volumeChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analysis Page (hidden by default) -->
                <div id="analysis-page" style="display:none">
                    <!-- Analysis content remains the same -->
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap & jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sample data - replace with actual API calls
        const companyData = {
            'TYRE': {
                currentPrice: 45.20,
                predictedPrice: 47.80,
                predictionChange: 5.8,
                priceHistory: [45, 47, 43, 48, 46, 47],
                volumeHistory: [12000, 15000, 10000, 18000, 14000, 16000],
                months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
            },
            'KCAB': {
                currentPrice: 38.50,
                predictedPrice: 36.20,
                predictionChange: -6.0,
                priceHistory: [40, 39, 38, 37, 38, 38.5],
                volumeHistory: [8000, 9500, 7000, 8500, 9000, 8200],
                months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
            },
            'SAMP': {
                currentPrice: 62.30,
                predictedPrice: 65.10,
                predictionChange: 4.5,
                priceHistory: [60, 62, 61, 63, 62, 62.3],
                volumeHistory: [20000, 22000, 18000, 24000, 21000, 23000],
                months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
            }
        };

        $(function() {
            // Initialize charts and prices
            updateDashboard('TYRE');
            
            // Company change handler
            $('#companyCode').change(function() {
                const company = $(this).val();
                $('#company-title').text(`${$(this).find('option:selected').text()} Dashboard`);
                updateDashboard(company);
            });

            // Page navigation
            $('[data-page]').click(function(e) {
                e.preventDefault();
                $('.nav-link').removeClass('active');
                $(this).addClass('active');
                $('[id$="-page"]').hide();
                $(`#${$(this).data('page')}-page`).show();
            });
        });

        function updateDashboard(companyCode) {
            const data = companyData[companyCode];
            
            // Update price displays
            $('#current-price').text(`LKR ${data.currentPrice.toFixed(2)}`);
            
            const predictionElement = $('#predicted-price');
            predictionElement.text(`LKR ${data.predictedPrice.toFixed(2)} (${data.predictionChange > 0 ? '+' : ''}${data.predictionChange.toFixed(1)}%)`);
            predictionElement.removeClass('prediction-up prediction-down')
                           .addClass(data.predictionChange >= 0 ? 'prediction-up' : 'prediction-down');
            
            // Update charts
            updateChart('priceChart', 'line', data.months, data.priceHistory, 'Price', 'rgba(75, 192, 192, 1)');
            updateChart('volumeChart', 'bar', data.months, data.volumeHistory, 'Volume', 'rgba(54, 162, 235, 0.7)');
        }

        function updateChart(chartId, type, labels, data, label, color) {
            const ctx = $(`#${chartId}`)[0].getContext('2d');
            
            // Destroy existing chart if it exists
            if (window[chartId]) {
                window[chartId].destroy();
            }
            
            // Create new chart
            window[chartId] = new Chart(ctx, {
                type: type,
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: data,
                        borderColor: type === 'line' ? color : undefined,
                        backgroundColor: type === 'bar' ? color : 'rgba(75, 192, 192, 0.1)',
                        tension: type === 'line' ? 0.1 : undefined
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
    </script>
</body>
</html>
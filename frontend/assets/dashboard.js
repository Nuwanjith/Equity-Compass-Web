// Global chart instances
        let priceChart = null;
        let volumeChart = null;
        
        // DOM Ready
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the dashboard
            initDashboard();
            
            // Setup event listeners
            setupEventListeners();
            
            // Load initial data
            fetchStockData();
        });
        
        function initDashboard() {
            // Any initialization logic can go here
            console.log('Dashboard initialized');
        }
        
        function setupEventListeners() {
            // Company selector change event
            document.getElementById('companyCode').addEventListener('change', fetchStockData);
            
            // Menu item click events
            document.querySelectorAll('.menu-item').forEach(item => {
                item.addEventListener('click', function() {
                    document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        }
        
        async function fetchStockData() {
            const companyCode = document.getElementById('companyCode').value;
            
            // Show loading spinners
            document.getElementById('priceLoading').style.display = 'block';
            document.getElementById('volumeLoading').style.display = 'block';
            
            try {
                const response = await fetch(`/backend/api/stock_prices.php?company=${companyCode}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result.success) {
                    renderCharts(result.data);
                } else {
                    throw new Error(result.error || 'Unknown error from API');
                }
            } catch (error) {
                console.error('Error fetching stock data:', error);
                alert('Failed to load stock data: ' + error.message);
            } finally {
                // Hide loading spinners
                document.getElementById('priceLoading').style.display = 'none';
                document.getElementById('volumeLoading').style.display = 'none';
            }
        }
        
        function renderCharts(data) {
            if (!data || data.length === 0) {
                console.error('No data received or empty data array');
                alert('No data available to display charts.');
                return;
            }

            // Prepare chart data
            const labels = data.map(item => item.month || item.Month || item.date || 'N/A');
            const prices = data.map(item => parseFloat(item.avg_close || item.avg_price || item.close || item.average || 0));
            const volumes = data.map(item => parseInt(item.total_volume || item.volume || item.trading_volume || 0));
            
            // Determine predicted data points (last point and line segment)
            const isPredictedPoint = labels.map((_, i) => i === labels.length - 1);
            const isPredictedLine = labels.map((_, i) => i === labels.length - 2);

            // Destroy existing charts
            if (priceChart) priceChart.destroy();
            if (volumeChart) volumeChart.destroy();

            // Render Price Chart with predicted styling
            renderPriceChart(labels, prices, isPredictedPoint, isPredictedLine);
            
            // Render Volume Chart
            renderVolumeChart(labels, volumes);
        }
        
        function renderPriceChart(labels, prices, isPredictedPoint, isPredictedLine) {
            const ctx = document.getElementById('priceChart').getContext('2d');
            
            priceChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Closing Price (LKR)',
                        data: prices,
                        borderColor: labels.map((_, i) => 
                            isPredictedLine[i] ? 'rgba(255, 99, 132, 1)' : 'rgba(78, 115, 223, 1)'
                        ),
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        borderWidth: labels.map((_, i) => 
                            isPredictedLine[i] ? 3 : 2
                        ),
                        borderDash: labels.map((_, i) => 
                            isPredictedLine[i] ? [5, 5] : []
                        ),
                        tension: 0.3,
                        pointRadius: labels.map((_, i) => 
                            isPredictedPoint[i] ? 6 : 4
                        ),
                        pointBackgroundColor: labels.map((_, i) => 
                            isPredictedPoint[i] ? 'rgba(255, 99, 132, 1)' : 'rgba(78, 115, 223, 1)'
                        ),
                        pointBorderColor: labels.map((_, i) => 
                            isPredictedPoint[i] ? 'rgba(255, 99, 132, 1)' : 'rgba(78, 115, 223, 1)'
                        ),
                        pointHoverRadius: 6,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += 'LKR ' + context.parsed.y.toFixed(2);
                                    if (context.dataIndex === context.dataset.data.length - 1) {
                                        label += ' (predicted)';
                                    }
                                    return label;
                                }
                            }
                        },
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Time Period',
                                font: {
                                    weight: 'bold'
                                }
                            }
                        },
                        y: {
                            beginAtZero: false,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            title: {
                                display: true,
                                text: 'Price (LKR)',
                                font: {
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'LKR ' + value.toFixed(2);
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        }
        
        function renderVolumeChart(labels, volumes) {
            const ctx = document.getElementById('volumeChart').getContext('2d');
            
            volumeChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Trading Volume',
                        data: volumes,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += (context.parsed.y / 1000000).toFixed(2) + 'M';
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Time Period',
                                font: {
                                    weight: 'bold'
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            title: {
                                display: true,
                                text: 'Volume',
                                font: {
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                callback: function(value) {
                                    return (value / 1000000).toFixed(1) + 'M';
                                }
                            }
                        }
                    }
                }
            });
        }
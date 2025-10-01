@vite('resources/css/app.css')
@include('admin.layouts.header')
@include('admin.layouts.sidebar')

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h1 class="h3 mb-0 text-gray-800">Analytics Dashboard</h1>
        </nav>

        <div class="container-fluid px-4">
            <!-- Year-to-Date Stats Card -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-left-primary shadow py-2">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-center border-right">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Revenue (Net)
                                    </div>
                                    <div class="h4 mb-0 font-weight-bold text-gray-800">
                                        ₱{{ number_format($yearStats->revenue ?? 0, 2) }}
                                    </div>
                                    <small class="text-muted">Actual earnings after discounts</small>
                                </div>
                                <div class="col-md-4 text-center border-right">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Total Discounts
                                    </div>
                                    <div class="h4 mb-0 font-weight-bold text-gray-800">
                                        ₱{{ number_format($yearStats->discounts ?? 0, 2) }}
                                    </div>
                                    <small class="text-muted">Amount given as discounts</small>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Total Transactions
                                    </div>
                                    <div class="h4 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($yearStats->count ?? 0) }}
                                    </div>
                                    <small class="text-muted">Completed transactions</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row">
                <!-- Revenue Trend Chart -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Monthly Revenue Trend ({{ $currentYear }})</h6>
                        </div>
                        <div class="card-body">
                            <div style="height: 320px; position: relative;">
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Breakdown Chart -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Revenue Breakdown</h6>
                        </div>
                        <div class="card-body">
                            <div style="height: 320px; position: relative;">
                                <canvas id="revenueBreakdownChart"></canvas>
                            </div>
                            <div class="mt-3 text-center small">
                                <span class="mr-3">
                                    <i class="fas fa-circle text-success"></i> Net Revenue
                                </span>
                                <span>
                                    <i class="fas fa-circle text-warning"></i> Discounts
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Analytics Row -->
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card shadow" style="height: 280px;">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Top Selling Items</h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted text-center mt-5">Chart will be added here</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card shadow" style="height: 280px;">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Payment Methods Distribution</h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted text-center mt-5">Chart will be added here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.layouts.script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const analyticsData = {
        labels: @json($labels),
        netRevenue: @json($netRevenue),
        discounts: @json($discounts),
    };

    // Revenue Trend Chart - Only showing Net Revenue
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: analyticsData.labels,
            datasets: [
                {
                    label: 'Net Revenue',
                    data: analyticsData.netRevenue,
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#1cc88a',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        padding: 15,
                        font: { size: 12 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ₱' + 
                                   context.parsed.y.toLocaleString('en-PH', {
                                       minimumFractionDigits: 2,
                                       maximumFractionDigits: 2
                                   });
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString('en-PH');
                        },
                        font: { size: 10 }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: { size: 10 }
                    }
                }
            }
        }
    });

    // Revenue Breakdown - Net Revenue vs Discounts
    const totalNet = analyticsData.netRevenue.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
    const totalDiscounts = analyticsData.discounts.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);

    const breakdownCtx = document.getElementById('revenueBreakdownChart').getContext('2d');
    const breakdownChart = new Chart(breakdownCtx, {
        type: 'doughnut',
        data: {
            labels: ['Net Revenue', 'Discounts Given'],
            datasets: [{
                data: [totalNet, totalDiscounts],
                backgroundColor: ['#1cc88a', '#f6c23e'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 10,
                        font: { size: 11 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return context.label + ': ₱' + 
                                   value.toLocaleString('en-PH', {
                                       minimumFractionDigits: 2,
                                       maximumFractionDigits: 2
                                   }) + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
</script>
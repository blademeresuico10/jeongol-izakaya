@include('admin.layouts.header')
@include('admin.layouts.sidebar')
@vite(['resources/js/app.js'])

<style>
    .ingredient-item {
        position: relative;
    }

    .floating-stock-alert {
        position: absolute;
        top: 50%;
        right: -280px;
        transform: translateY(-50%);
        width: 260px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        animation: floatIn 0.3s ease-out;
    }

    .floating-stock-alert::before {
        content: '';
        position: absolute;
        left: -8px;
        top: 50%;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border-top: 8px solid transparent;
        border-bottom: 8px solid transparent;
        border-right: 8px solid white;
    }

    .floating-stock-alert[data-status="critical"] {
        border-left: 4px solid #dc3545;
    }

    .floating-stock-alert[data-status="low"] {
        border-left: 4px solid #ffc107;
    }

    @keyframes floatIn {
        from {
            opacity: 0;
            right: -300px;
        }

        to {
            opacity: 1;
            right: -280px;
        }
    }

    @keyframes floatOut {
        from {
            opacity: 1;
            right: -280px;
        }

        to {
            opacity: 0;
            right: -300px;
        }
    }

    .close-alert:hover {
        color: #6b7280 !important;
    }

    .ingredient-item:hover .floating-stock-alert {
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
    }

    @media (max-width: 1400px) {
        .floating-stock-alert {
            right: auto;
            left: 50%;
            top: -80px;
            transform: translateX(-50%);
        }

        .floating-stock-alert::before {
            left: 50%;
            top: auto;
            bottom: -8px;
            transform: translateX(-50%) rotate(90deg);
        }

        @keyframes floatIn {
            from {
                opacity: 0;
                top: -100px;
            }

            to {
                opacity: 1;
                top: -80px;
            }
        }
    }
</style>
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        </nav>

        <div class="container-fluid px-4">
            <div class="row g-4 mb-4">
                <!-- Sales Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="flex-grow-1">
                                    <h2 class="mb-0 fw-bold" style="color: black">
                                        ₱{{ number_format($totalGrossSales, 2) }}</h2>
                                    <p class="text-black text-uppercase fw-semibold small mb-0 mt-1">Net Sales</p>
                                </div>
                                <div class="ms-3">
                                    <i class="fas fa-peso-sign fa-3x" style="color: #321ee9;"></i>
                                </div>
                            </div>
                            <div class="mb-2">
                                <span class="small {{ $salesChange >= 0 ? 'text-success' : 'text-danger' }}">
                                    <i class="fas {{ $salesChange >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                                    {{ $salesChange >= 0 ? '+' : '' }}{{ number_format(abs($salesChange), 1) }}%
                                </span>
                            </div>
                            <div class="chart-container" style="height: 60px;">
                                <canvas id="salesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="flex-grow-1">
                                    <h2 class="mb-0 fw-bold" style="color: black">{{ number_format($totalOrders) }}</h2>
                                    <p class="text-black text-uppercase fw-semibold small mb-0 mt-1">Orders</p>
                                </div>
                                <div class="ms-3">
                                    <i class="fas fa-shopping-bag" style="color: #321ee9; font-size: 3rem;"></i>
                                </div>
                            </div>
                            <div class="mb-2">
                                <span class="small {{ $ordersChange >= 0 ? 'text-success' : 'text-danger' }}">
                                    <i class="fas {{ $ordersChange >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                                    {{ $ordersChange >= 0 ? '+' : '' }}{{ number_format(abs($ordersChange), 1) }}%
                                </span>
                            </div>
                            <div class="chart-container" style="height: 60px;">
                                <canvas id="ordersChartMini"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="flex-grow-1">
                                    <h2 class="mb-0 fw-bold" style="color: black">{{ number_format($totalCustomers) }}
                                    </h2>
                                    <p class="text-black text-uppercase fw-semibold small mb-0 mt-1">Customers
                                    </p>
                                </div>
                                <div class="ms-3">
                                    <i class="fas fa-users" style="color: #321ee9; font-size: 3rem;"></i>
                                </div>
                            </div>
                            <div class="mb-2">
                                <span class="small {{ $customersChange >= 0 ? 'text-success' : 'text-danger' }}">
                                    <i class="fas {{ $customersChange >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                                    {{ $customersChange >= 0 ? '+' : '' }}{{ number_format(abs($customersChange), 1) }}%
                                </span>
                            </div>
                            <div class="chart-container" style="height: 60px;">
                                <canvas id="customersChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reservations Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="flex-grow-1">
                                    <h2 class="mb-0 fw-bold" style="color: black">
                                        {{ number_format($totalReservations) }}
                                    </h2>
                                    <p class="text-black text-uppercase fw-semibold small mb-0 mt-1">Reservations</p>
                                </div>
                                <div class="ms-3">
                                    <i class="fas fa-calendar-check" style="color: #321ee9; font-size: 3rem;"></i>
                                </div>
                            </div>
                            <div class="mb-2">
                                <span class="small {{ $reservationsChange >= 0 ? 'text-success' : 'text-danger' }}">
                                    <i class="fas {{ $reservationsChange >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                                    {{ $reservationsChange >= 0 ? '+' : '' }}{{ number_format(abs($reservationsChange), 1) }}%
                                </span>
                            </div>
                            <div class="chart-container" style="height: 60px;">
                                <canvas id="reservationsChartMini"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-xl-8">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 py-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-chart-line fs-5 me-3" style="color: #321ee9;"></i>
                                <h5 class="card-title mb-0 fw-bold">Sales Overview</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueChart" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 py-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-utensils fs-5 me-3" style="color: #321ee9;"></i>
                                <h5 class="card-title mb-0 fw-bold">Flagship Items</h5>
                            </div>
                        </div>

                        <div class="card-body overflow-auto" style="max-height: 400px;">
                            @forelse ($flagshipItems as $index => $item)
                                <div class="d-flex align-items-center justify-content-between mb-2 p-2 rounded border"
                                    style="transition: 0.2s; border-color: #f0f0f0;">

                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('storage/jeongol_menu/' . $item->image) }}" class="rounded me-2"
                                            style="width: 50px; height: 50px; object-fit: cover;">
                                        <div>
                                            <div class="fw-bold text-dark">{{ $item->menu_item }}</div>
                                        </div>
                                    </div>

                                    <div class="rounded-circle bg-warning text-white fw-bold d-flex align-items-center justify-content-center"
                                        style="width: 30px; height: 30px;">
                                        {{ $item->total_quantity }}
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle mb-2" style="font-size: 2rem; color: #ccc;"></i>
                                    <p class="mb-0">No orders.</p>
                                </div>
                            @endforelse
                        </div>

                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-xl-8 col-lg-12">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white border-0 py-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center">
                                    <div class="p-2 rounded-3 me-3" style="background-color: rgba(50, 30, 233, 0.1);">
                                        <i class="fas fa-boxes fs-5" style="color: #321ee9;"></i>
                                    </div>
                                    <h5 class="card-title mb-0 fw-bold text-gray-800">Ingredients Stock</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                            @forelse ($ingredients as $ingredient)
                                <div class="d-flex align-items-center justify-content-between p-3 mb-2 rounded-3 border"
                                    style="transition: all 0.2s ease; background-color: #fafbfc;">
                                    <div class="d-flex align-items-center flex-grow-1">
                                        <div class="me-3">
                                            @if($ingredient->image)
                                                <img src="{{ asset('storage/ingredients/' . $ingredient->image) }}"
                                                    alt="{{ $ingredient->name }}" class="rounded-circle"
                                                    style="width: 50px; height: 50px; object-fit: cover; border: 2px solid rgba(50, 30, 233, 0.1);">
                                            @else
                                                <div class="rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 50px; height: 50px; background-color: rgba(50, 30, 233, 0.08);">
                                                    <i class="fas fa-box" style="color: #321ee9; font-size: 18px;"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold" style="color: #1f2937;">{{ $ingredient->name }}
                                            </h6>
                                            <small class="text-muted">{{ $ingredient->category }}</small>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="badge {{ $ingredient->badge_class }} text-white px-3 py-2">
                                            <i class="fas {{ $ingredient->badge_icon }} me-1"></i>
                                            {{ $ingredient->badge_text }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="fas fa-box-open" style="font-size: 3rem; color: #e5e7eb;"></i>
                                    </div>
                                    <p class="text-muted mb-0">No ingredients found</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-12">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white border-0 py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="p-2 rounded-3 me-3" style="background-color: rgba(50, 30, 233, 0.1);">
                                        <i class="fas fa-clock fs-5" style="color: #321ee9;"></i>
                                    </div>
                                    <h5 class="card-title mb-0 fw-bold text-gray-800">Today's Activity</h5>
                                </div>
                            </div>
                        </div>

                        <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                            @if($recentActivities->isNotEmpty())
                                <div class="d-flex flex-column gap-2">
                                    @foreach($recentActivities as $activity)
                                        <div class="d-flex align-items-start p-3 rounded-3 border"
                                            style="transition: all 0.2s ease; background-color: #fafbfc;">
                                            <div class="me-3 d-flex justify-content-center align-items-center rounded-circle flex-shrink-0"
                                                style="background-color: {{ $activity['color'] }}15; width: 42px; height: 42px;">
                                                <i class="fas {{ $activity['icon'] }}"
                                                    style="color: {{ $activity['color'] }}; font-size: 16px;"></i>
                                            </div>

                                            <div class="flex-grow-1">
                                                <div class="fw-semibold mb-1" style="color: #1f2937; font-size: 14px;">
                                                    {{ $activity['type'] }}
                                                </div>
                                                <div class="d-flex align-items-center gap-2" style="font-size: 12px;">
                                                    <span class="text-muted">{{ $activity['time'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="fas fa-inbox" style="font-size: 3rem; color: #e5e7eb;"></i>
                                    </div>
                                    <p class="text-muted mb-0">No activities recorded today</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const salesTrend = @json($salesTrend ?? []);
        const ordersTrend = @json($ordersTrend ?? []);
        const customersTrend = @json($customersTrend ?? []);
        const reservationsTrend = @json($reservationsTrend ?? []);

        function prepare7DaysData(trendData) {
            const labels = [];
            const values = [];

            for (let i = 6; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                const dateStr = date.toISOString().split('T')[0];
                const label = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });

                labels.push(label);
                values.push(trendData[dateStr] || 0);
            }

            return { labels, values };
        }

        const salesData = prepare7DaysData(salesTrend);
        const ordersData = prepare7DaysData(ordersTrend);
        const customersData = prepare7DaysData(customersTrend);
        const reservationsData = prepare7DaysData(reservationsTrend);

        const miniChartConfig = {
            type: 'line',
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true,
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 8,
                        displayColors: false,
                        callbacks: {
                            title: function (context) {
                                return context[0]?.label || '';
                            },
                            label: function (context) {
                                const val = context.parsed.y ?? 0;
                                return 'Value: ' + val.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: { display: false },
                    y: { display: false }
                },
                elements: {
                    point: { radius: 0 },
                    line: {
                        borderWidth: 2,
                        tension: 0.4,
                        borderColor: '#321ee9'
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        };

        new Chart(document.getElementById('salesChart'), {
            ...miniChartConfig,
            data: {
                labels: salesData.labels,
                datasets: [{
                    data: salesData.values,
                    fill: true,
                    backgroundColor: 'rgba(50,30,233,0.1)'
                }]
            },
            options: {
                ...miniChartConfig.options,
                plugins: {
                    ...miniChartConfig.options.plugins,
                    tooltip: {
                        enabled: true,
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 8,
                        displayColors: false,
                        callbacks: {
                            title: function (context) {
                                return context[0]?.label || '';
                            },
                            label: function (context) {
                                return 'Sales: ₱' + (context.parsed.y ?? 0).toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('ordersChartMini'), {
            ...miniChartConfig,
            data: {
                labels: ordersData.labels,
                datasets: [{
                    data: ordersData.values,
                    fill: true,
                    backgroundColor: 'rgba(50,30,233,0.1)'
                }]
            },
            options: {
                ...miniChartConfig.options,
                plugins: {
                    ...miniChartConfig.options.plugins,
                    tooltip: {
                        enabled: true,
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 8,
                        displayColors: false,
                        callbacks: {
                            title: function (context) {
                                return context[0]?.label || '';
                            },
                            label: function (context) {
                                return 'Orders: ' + (context.parsed.y ?? 0);
                            }
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('customersChart'), {
            ...miniChartConfig,
            data: {
                labels: customersData.labels,
                datasets: [{
                    data: customersData.values,
                    fill: true,
                    backgroundColor: 'rgba(50,30,233,0.1)'
                }]
            },
            options: {
                ...miniChartConfig.options,
                plugins: {
                    ...miniChartConfig.options.plugins,
                    tooltip: {
                        enabled: true,
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 8,
                        displayColors: false,
                        callbacks: {
                            title: function (context) {
                                return context[0]?.label || '';
                            },
                            label: function (context) {
                                return 'Customers: ' + (context.parsed.y ?? 0);
                            }
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('reservationsChartMini'), {
            ...miniChartConfig,
            data: {
                labels: reservationsData.labels,
                datasets: [{
                    data: reservationsData.values,
                    fill: true,
                    backgroundColor: 'rgba(50,30,233,0.1)'
                }]
            },
            options: {
                ...miniChartConfig.options,
                plugins: {
                    ...miniChartConfig.options.plugins,
                    tooltip: {
                        enabled: true,
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 8,
                        displayColors: false,
                        callbacks: {
                            title: function (context) {
                                return context[0]?.label || '';
                            },
                            label: function (context) {
                                return 'Reservations: ' + (context.parsed.y ?? 0);
                            }
                        }
                    }
                }
            }
        });

        const months = ['January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'];

        const monthlySalesData = @json($monthlySalesData);

        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    data: monthlySalesData,
                    borderColor: '#321ee9',
                    backgroundColor: 'rgba(50, 30, 233, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#321ee9',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            color: '#6c757d'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            color: '#6c757d',
                            callback: function (value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                },
                events: []
            }
        });
    });

    let shownAlerts = new Set();

    document.addEventListener('DOMContentLoaded', function () {
        // Show alerts for critical and low stock items
        const ingredients = document.querySelectorAll('.ingredient-item');

        ingredients.forEach((item, index) => {
            const status = item.getAttribute('data-ingredient-status');
            const alertElement = item.querySelector('.floating-stock-alert');

            if ((status === 'critical' || status === 'low') && alertElement) {
                // Stagger the appearance of alerts
                setTimeout(() => {
                    alertElement.style.display = 'block';
                    alertElement.setAttribute('data-status', status);

                    // Auto-hide after 8 seconds
                    setTimeout(() => {
                        closeAlertWithAnimation(alertElement);
                    }, 8000);
                }, index * 200); // 200ms delay between each alert
            }
        });
    });

    function closeAlert(button) {
        const alert = button.closest('.floating-stock-alert');
        closeAlertWithAnimation(alert);
    }

    function closeAlertWithAnimation(alert) {
        alert.style.animation = 'floatOut 0.3s ease-in';
        setTimeout(() => {
            alert.style.display = 'none';
            alert.style.animation = '';
        }, 300);
    }
</script>

@include('admin.layouts.script')
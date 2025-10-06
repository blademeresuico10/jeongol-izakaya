@include('admin.layouts.header')
@include('admin.layouts.sidebar')
@vite(['resources/js/app.js'])

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        </nav>

        <div class="container-fluid px-4">
            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="flex-grow-1">
                                    <h2 class="mb-0 fw-bold" style="color: black">
                                        ₱{{ number_format($totalGrossSales, 2) }}</h2>
                                    <p class="text-black text-uppercase fw-semibold small mb-0 mt-1">Today's Sales</p>
                                </div>
                                <div class="ms-3">
                                    <i class="fas fa-peso-sign fa-3x" style="color: #321ee9;"></i>
                                </div>
                            </div>
                            <div class="mb-2">
                                <span
                                    class="badge bg-opacity-10 {{ $salesChange >= 0 ? 'text-success' : 'text-danger' }} small">
                                    <i class="fas {{ $salesChange >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                                    {{ number_format($salesChange, 1) }}%
                                </span>
                            </div>
                            <div class="chart-container" style="height: 60px;">
                                <canvas id="salesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="flex-grow-1">
                                    <h2 class="mb-0 fw-bold" style="color: black">{{ number_format($totalOrders) }}</h2>
                                    <p class="text-black text-uppercase fw-semibold small mb-0 mt-1">Today's Orders</p>
                                </div>
                                <div class="ms-3">
                                    <i class="fas fa-shopping-bag" style="color: #321ee9; font-size: 3rem;"></i>
                                </div>
                            </div>
                            <div class="mb-2">
                                <span
                                    class="badge bg-opacity-10 {{ $ordersChange >= 0 ? 'text-success' : 'text-danger' }} small">
                                    <i class="fas {{ $ordersChange >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                                    {{ number_format($ordersChange, 1) }}%
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
                                    <p class="text-black text-uppercase fw-semibold small mb-0 mt-1">Today's Customers
                                    </p>
                                </div>
                                <div class="ms-3">
                                    <i class="fas fa-users" style="color: #321ee9; font-size: 3rem;"></i>
                                </div>
                            </div>
                            <div class="mb-2">
                                <span
                                    class="badge bg-opacity-10 {{ $customersChange >= 0 ? 'text-success' : 'text-danger' }} small">
                                    <i class="fas {{ $customersChange >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                                    {{ number_format($customersChange, 1) }}%
                                </span>
                            </div>
                            <div class="chart-container" style="height: 60px;">
                                <canvas id="customersChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

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
                                <span
                                    class="badge bg-opacity-10 {{ $reservationsChange >= 0 ? 'text-success' : 'text-danger' }} small">
                                    <i class="fas {{ $reservationsChange >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                                    {{ number_format($reservationsChange, 1) }}%
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
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="text-end me-2">
                                            <div class="fw-bold" style="color: #1f2937; font-size: 18px;">
                                                {{ $ingredient->stocks }}</div>
                                            <small class="text-muted">{{ $ingredient->unit }}</small>
                                        </div>
                                        <div>
                                            @if($ingredient->stocks < 10)
                                                <span class="badge bg-danger bg-opacity-10 text-white px-3 py-2">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Low Stock
                                                </span>
                                            @elseif($ingredient->stocks < 50)
                                                <span class="badge bg-warning bg-opacity-10 text-white px-3 py-2">
                                                    <i class="fas fa-exclamation-circle me-1"></i>Medium
                                                </span>
                                            @else
                                                <span class="badge bg-success bg-opacity-10 text-white px-3 py-2">
                                                    <i class="fas fa-check-circle me-1"></i>Good
                                                </span>
                                            @endif
                                        </div>
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
                                                <div class="text-muted mb-2" style="font-size: 13px;">
                                                    {{ $activity['name'] }}
                                                </div>
                                                <div class="d-flex align-items-center gap-2" style="font-size: 12px;">
                                                    <span
                                                        class="badge bg-light text-dark px-2 py-1">{{ $activity['status'] }}</span>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const salesTrend = @json($salesTrend ?? []);
        const ordersTrend = @json($ordersTrend ?? []);
        const customersTrend = @json($customersTrend ?? []);
        const reservationsTrend = @json($reservationsTrend ?? []);

        function safeData(data) {
            if (!data || Object.keys(data).length === 0) {
                return {
                    labels: ['No Data'],
                    values: [0]
                };
            }
            return {
                labels: Object.keys(data),
                values: Object.values(data)
            };
        }

        const salesData = safeData(salesTrend);
        const ordersData = safeData(ordersTrend);
        const customersData = safeData(customersTrend);
        const reservationsData = safeData(reservationsTrend);

        // --- Mini Chart Config ---
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
                                const date = context[0]?.label || '';
                                return `Date: ${date}`;
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

        // Mini charts
        new Chart(document.getElementById('salesChart'), {
            ...miniChartConfig,
            data: { labels: salesData.labels, datasets: [{ data: salesData.values, fill: true, backgroundColor: 'rgba(50,30,233,0.1)' }] }
        });

        new Chart(document.getElementById('ordersChartMini'), {
            ...miniChartConfig,
            data: { labels: ordersData.labels, datasets: [{ data: ordersData.values, fill: true, backgroundColor: 'rgba(50,30,233,0.1)' }] }
        });

        new Chart(document.getElementById('customersChart'), {
            ...miniChartConfig,
            data: { labels: customersData.labels, datasets: [{ data: customersData.values, fill: true, backgroundColor: 'rgba(50,30,233,0.1)' }] }
        });

        new Chart(document.getElementById('reservationsChartMini'), {
            ...miniChartConfig,
            data: { labels: reservationsData.labels, datasets: [{ data: reservationsData.values, fill: true, backgroundColor: 'rgba(50,30,233,0.1)' }] }
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


</script>

@include('admin.layouts.script')
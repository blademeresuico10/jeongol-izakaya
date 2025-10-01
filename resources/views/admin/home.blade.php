@include('admin.layouts.header')
@include('admin.layouts.sidebar')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script
    src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        </nav>

        <div class="p-3 space-y-6">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 grid grid-cols-2 gap-4">
                    @php
                        $smallCards = [
                            [
                                'title' => 'Revenue',
                                'subtitle' => 'Today',
                                'value' => "₱" . number_format($todayRevenue, 2),
                                'icon' => 'fas fa-chart-line',
                                'bgColor' => 'bg-blue-100',
                                'textColor' => 'text-blue-600',
                                'id' => 'revenueValue'
                            ],
                            [
                                'title' => 'Customers',
                                'subtitle' => 'Today',
                                'value' => $todayCustomers,
                                'icon' => 'fas fa-users',
                                'bgColor' => 'bg-purple-100',
                                'textColor' => 'text-purple-600',
                                'id' => 'customersValue'
                            ]
                        ];
                    @endphp

                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="p-2 {{ $smallCards[0]['bgColor'] }} rounded-lg">
                                <i class="{{ $smallCards[0]['icon'] }} {{ $smallCards[0]['textColor'] }} text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-800">{{ $smallCards[0]['title'] }}</h3>
                                <p class="text-xs text-gray-500">{{ $smallCards[0]['subtitle'] }}</p>
                            </div>
                        </div>
                        <h2 id="{{ $smallCards[0]['id'] }}" class="text-3xl font-bold text-gray-900">
                            {{ $smallCards[0]['value'] }}
                        </h2>
                    </div>

                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="p-2 {{ $smallCards[1]['bgColor'] }} rounded-lg">
                                <i class="{{ $smallCards[1]['icon'] }} {{ $smallCards[1]['textColor'] }} text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-800">{{ $smallCards[1]['title'] }}</h3>
                                <p class="text-xs text-gray-500">{{ $smallCards[1]['subtitle'] }}</p>
                            </div>
                        </div>
                        <h2 id="{{ $smallCards[1]['id'] }}" class="text-3xl font-bold text-gray-900">
                            {{ $smallCards[1]['value'] }}
                        </h2>
                    </div>

                    <div
                        class="col-span-1 bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-yellow-100 rounded-lg">
                                    <i class="fas fa-utensils text-yellow-600 text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-800">Popular Orders</h3>
                                    <p class="text-xs text-gray-500">Menu distribution</p>
                                </div>
                            </div>
                            <i class="fas fa-chart-pie text-gray-400"></i>
                        </div>

                        <div class="flex space-x-1 mb-4 bg-gray-100 p-1 rounded-lg">
                            <button
                                class="popular-tab-btn flex-1 px-3 py-2 text-xs font-medium rounded-md transition-colors bg-indigo-600 text-white"
                                data-period="today">
                                Today
                            </button>
                            <button
                                class="popular-tab-btn flex-1 px-3 py-2 text-xs font-medium rounded-md transition-colors text-gray-700 hover:text-gray-900"
                                data-period="week">
                                This Week
                            </button>
                            <button
                                class="popular-tab-btn flex-1 px-3 py-2 text-xs font-medium rounded-md transition-colors text-gray-700 hover:text-gray-900"
                                data-period="month">
                                This Month
                            </button>
                        </div>

                        <div class="w-full h-64 relative">
                            <canvas id="ordersChart" class="absolute inset-0 w-full h-full"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800">Revenue Analytics</h4>
                        </div>
                    </div>
                    <div class="w-full h-80 relative mb-4">
                        <canvas id="revenueChart" class="absolute inset-0 w-full h-full"></canvas>
                    </div>

                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800">Reservations</h4>
                        </div>
                    </div>
                    <div class="w-full h-80 relative mb-4">
                        <canvas id="reservationsChart" class="absolute inset-0 w-full h-full"></canvas>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

@include('admin.layouts.script')

<script>
    const state = {
        salesData: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            revenue: @json($monthlyRevenue ?? array_fill(0, 12, 0)),
        },
        ordersData: {
            today: {
                labels: @json($popularMenusToday->pluck('menu_item')->toArray()),
                data: @json($popularMenusToday->pluck('total_quantity')->toArray())
            },
            week: {
                labels: @json($popularMenusWeek->pluck('menu_item')->toArray()),
                data: @json($popularMenusWeek->pluck('total_quantity')->toArray())
            },
            month: {
                labels: @json($popularMenusMonth->pluck('menu_item')->toArray()),
                data: @json($popularMenusMonth->pluck('total_quantity')->toArray())
            },
            currentPeriod: 'today'
        },
        revenueChart: null,
        ordersChart: null,
        today: new Date(),
        currentMonth: new Date().getMonth(),
        currentYear: new Date().getFullYear()
    };

    function initRevenueChart() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        state.revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: state.salesData.labels,
                datasets: [{
                    label: 'Revenue',
                    data: state.salesData.revenue,
                    backgroundColor: 'rgba(34,211,238,0.2)',
                    borderColor: '#22D3EE',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#22D3EE',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        cornerRadius: 8,
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function (context) {
                                return context.dataset.label + ': ₱' + context.raw.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            callback: function (value) {
                                return '₱' + value.toLocaleString();
                            },
                            color: '#9CA3AF',
                            font: {
                                size: 12
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#9CA3AF',
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    }

    function initReservations(){
        
    }

    function initOrdersChart() {
        const ctx = document.getElementById('ordersChart').getContext('2d');
        const colors = ['#EAB308', '#8B5CF6', '#3B82F6', '#EF4444', '#10B981'];

        const currentData = state.ordersData[state.ordersData.currentPeriod];
        const hasData = currentData.data.length > 0;

        state.ordersChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: hasData ? currentData.labels : ['No orders yet'],
                datasets: [{
                    data: hasData ? currentData.data : [1],
                    backgroundColor: colors,
                    borderColor: '#fff',
                    borderWidth: 2,
                    hoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    datalabels: {
                        display: true,
                        color: '#fff',
                        font: {
                            weight: 'bold',
                            size: 14
                        },
                        formatter: function (value, context) {
                            return hasData ? value : '';
                        }
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 11
                            },
                            generateLabels: function (chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length && hasData) {
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        return {
                                            text: `${label} (${value})`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                                return [{
                                    text: 'No orders yet',
                                    fillStyle: '#9CA3AF',
                                    hidden: false,
                                    index: 0
                                }];
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        cornerRadius: 8,
                        callbacks: {
                            label: function (context) {
                                if (!hasData) return 'No orders yet';
                                const label = context.label || '';
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} orders (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '60%',
                elements: {
                    arc: {
                        hoverBackgroundColor: function (ctx) {
                            return ctx.element.options.backgroundColor;
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    }

    function updateOrdersChart(period) {
        const currentData = state.ordersData[period];
        const hasData = currentData.data.length > 0;
        const colors = ['#EAB308', '#8B5CF6', '#3B82F6', '#EF4444', '#10B981'];

        if (state.ordersChart) {
            state.ordersChart.data.labels = hasData ? currentData.labels : ['No orders yet'];
            state.ordersChart.data.datasets[0].data = hasData ? currentData.data : [1];
            state.ordersChart.data.datasets[0].backgroundColor = colors;
            state.ordersChart.update();
        }
    }

    // Popular Orders Tab Switching
    document.querySelectorAll('.popular-tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            // Update tab appearance
            document.querySelectorAll('.popular-tab-btn').forEach(b => {
                b.classList.remove('bg-indigo-600', 'text-white');
                b.classList.add('text-gray-700');
            });
            btn.classList.add('bg-indigo-600', 'text-white');
            btn.classList.remove('text-gray-700');

            // Update chart data
            const period = btn.getAttribute('data-period');
            state.ordersData.currentPeriod = period;
            updateOrdersChart(period);
        });
    });

    async function refreshDashboard() {
        try {
            const res = await fetch("{{ route('home.dashboard.data') }}");
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }

            const data = await res.json();

            const revenueElement = document.getElementById('revenueValue');
            if (revenueElement && data.revenue !== undefined) {
                revenueElement.textContent = `₱${data.revenue}`;
            }

            const customersElement = document.getElementById('customersValue');
            if (customersElement && data.customers !== undefined) {
                customersElement.textContent = data.customers.toString();
            }

        } catch (err) {
            console.error('Dashboard refresh error:', err);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        initRevenueChart();
        initOrdersChart();

        refreshDashboard();

        setInterval(refreshDashboard, 30000);
    });
</script>
@include('admin.layouts.header')
@include('admin.layouts.sidebar')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        </nav>

        <div class="p-6 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $cards = [
                        [
                            'title' => 'Revenue',
                            'subtitle' => 'Total Revenue today',
                            'value' => "₱" . number_format($todayRevenue, 2),
                            'icon' => 'fas fa-chart-line',
                            'bgColor' => 'bg-blue-100',
                            'textColor' => 'text-blue-600',
                            'id' => 'revenueValue'
                        ],
                        [
                            'title' => 'Customers',
                            'subtitle' => "Today's total",
                            'value' => $todayCustomers,
                            'icon' => 'fas fa-users',
                            'bgColor' => 'bg-purple-100',
                            'textColor' => 'text-purple-600',
                            'id' => 'customersValue'
                        ],
                        [
                            'title' => 'Stock',
                            'subtitle' => 'Stock Monitoring',
                            'value' => '',
                            'icon' => 'fas fa-boxes',
                            'bgColor' => 'bg-green-100',
                            'textColor' => 'text-green-600',
                            'id' => 'stockContainer'
                        ]
                    ];
                @endphp

                @foreach($cards as $card)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 {{ $card['bgColor'] }} rounded-lg">
                                    <i class="{{ $card['icon'] }} {{ $card['textColor'] }} text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">{{ $card['title'] }}</h3>
                                    <p class="text-sm text-gray-500">{{ $card['subtitle'] }}</p>
                                </div>
                            </div>
                        </div>

                        @if($card['title'] !== 'Stock')
                            <h2 id="{{ $card['id'] }}" class="text-5xl md:text-6xl font-bold text-gray-900">
                                {{ $card['value'] }}
                            </h2>
                        @else
                            <div id="stockContainer" class="grid grid-cols-1 gap-2">
                                @foreach($stockChartData as $stock)
                                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg shadow-sm">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-6 h-2 rounded-sm" style="background-color:
                                                                                                            @if($stock['quantity'] >= 60) green
                                                                                                            @elseif($stock['quantity'] >= 30) orange
                                                                                                            @else red
                                                                                                            @endif;"></div>
                                            <span class="text-gray-800 font-medium">{{ $stock['name'] }}</span>
                                        </div>
                                        <div>
                                            <span class="text-sm font-semibold
                                                                                                            @if($stock['quantity'] >= 60) text-green-600
                                                                                                            @elseif($stock['quantity'] >= 30) text-orange-500
                                                                                                            @else text-red-600
                                                                                                            @endif">
                                                @if($stock['quantity'] >= 60) Sufficient
                                                @elseif($stock['quantity'] >= 30) Low
                                                @else Critical
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                <div class="lg:col-span-1 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-800">Transaction History</h4>
                        <i class="fas fa-receipt text-gray-400"></i>
                    </div>
                    <div id="transactionHistory" class="space-y-4 max-h-80 overflow-y-auto">
                        @foreach($transactions as $transaction)
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">
                                            {{ strtoupper(substr($transaction->user->firstname ?? 'U', 0, 1)) }}
                                            {{ strtoupper(substr($transaction->user->lastname ?? 'N', 0, 1)) }}
                                        </span>
                                    </div>
                                    <span class="text-gray-800 font-medium">
                                        {{ $transaction->user->firstname ?? 'Unknown' }}
                                        {{ $transaction->user->lastname ?? '' }}
                                    </span>
                                </div>
                                <span class="text-gray-900 font-semibold">
                                    ₱ {{ number_format($transaction->total + $transaction->advance_payment, 2) }}
                                </span>
                            </div>
                        @endforeach
                    </div>


                </div>

                <div class="lg:col-span-3 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-800">Sales Overview</h4>
                        <i class="fas fa-chart-area text-gray-400"></i>
                    </div>
                    <div class="flex space-x-2 mb-4">
                        <button
                            class="tab-btn px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium transition-colors"
                            data-period="weekly">Weekly</button>
                        <button
                            class="tab-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium transition-colors hover:bg-gray-200"
                            data-period="monthly">Monthly</button>
                        <button
                            class="tab-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium transition-colors hover:bg-gray-200"
                            data-period="quarterly">Quarterly</button>
                    </div>
                    <div class="w-full h-72 relative">
                        <canvas id="revenueChart" class="absolute inset-0 w-full h-full"></canvas>
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
            weekly: { labels: [], data: [] },
            monthly: { labels: [], data: [] },
            quarterly: { labels: [], data: [] }
        },
        revenueChart: null,
        today: new Date(),
        currentMonth: new Date().getMonth(),
        currentYear: new Date().getFullYear()
    };

    function initRevenueChart() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        state.revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: state.salesData.weekly.labels || [],
                datasets: [{
                    label: 'Sales',
                    data: state.salesData.weekly.data || [],
                    backgroundColor: 'rgba(79,70,229,0.1)',
                    borderColor: '#4F46E5',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#4F46E5',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
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
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                elements: {
                    point: { hoverBackgroundColor: '#4F46E5' }
                }
            }
        });
    }

    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('bg-indigo-600', 'text-white');
                b.classList.add('bg-gray-100', 'text-gray-700');
            });
            btn.classList.add('bg-indigo-600', 'text-white');
            btn.classList.remove('bg-gray-100', 'text-gray-700');

            const period = btn.dataset.period;
            if (state.revenueChart && state.salesData[period]) {
                state.revenueChart.data.labels = state.salesData[period].labels;
                state.revenueChart.data.datasets[0].data = state.salesData[period].data;
                state.revenueChart.update();
            }
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
            } else {
                console.warn('Revenue element not found or data.revenue is undefined', {
                    element: !!revenueElement,
                    revenue: data.revenue
                });
            }

            const customersElement = document.getElementById('customersValue');
            if (customersElement && data.customers !== undefined) {
                customersElement.textContent = data.customers.toString();
            } else {
                console.warn('Customers element not found or data.customers is undefined', {
                    element: !!customersElement,
                    customers: data.customers
                });
            }

            const stockContainer = document.getElementById('stockContainer');
            if (stockContainer && data.stock && Array.isArray(data.stock)) {
                if (data.stock.length > 0) {
                    stockContainer.innerHTML = data.stock.map(stock => {
                        const quantity = parseInt(stock.quantity) || 0;
                        const color = quantity >= 60 ? 'green' : quantity >= 30 ? 'orange' : 'red';
                        const status = quantity >= 60 ? 'Sufficient' : quantity >= 30 ? 'Low' : 'Critical';
                        const textColor = quantity >= 60 ? 'text-green-600' : quantity >= 30 ? 'text-orange-500' : 'text-red-600';

                        return `
                                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg shadow-sm">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-6 h-2 rounded-sm" style="background-color:${color};"></div>
                                            <span class="text-gray-800 font-medium">${stock.name}</span>
                                        </div>
                                        <div><span class="text-sm font-semibold ${textColor}">${status}</span></div>
                                    </div>`;
                    }).join('');
                } else {
                    stockContainer.innerHTML = '<div class="text-gray-500 text-sm">No stock data available</div>';
                }
            }

            const transactionHistory = document.getElementById('transactionHistory');
            if (transactionHistory && data.transactions && Array.isArray(data.transactions)) {
                if (data.transactions.length > 0) {
                    transactionHistory.innerHTML = data.transactions.map(transaction => `
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                            <span class="text-white text-sm font-medium">
                                                ${(transaction.cashier.firstname || '').charAt(0).toUpperCase()}${(transaction.cashier.lastname || '').charAt(0).toUpperCase()}
                                            </span>
                                        </div>
                                        <span class="text-gray-800 font-medium">
                                            ${transaction.cashier.firstname || ''} ${transaction.cashier.lastname || ''}
                                        </span>
                                    </div>
                                    <span class="text-gray-900 font-semibold">
                                        ₱${parseFloat(transaction.total_amount || 0).toFixed(2)}
                                    </span>
                                </div>
                            `).join('');
                } else {
                    transactionHistory.innerHTML = '<div class="text-gray-500 text-sm p-3">No transactions today</div>';
                }
            }

        } catch (err) {
            console.error('Error refreshing dashboard:', err);
        }
    }

    async function refreshSalesChart() {
        try {
            const res = await fetch("{{ route('home.sales.data') }}");;
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }

            const data = await res.json();

            if (data.weekly) {
                const weeklyLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                state.salesData.weekly = {
                    labels: weeklyLabels,
                    data: weeklyLabels.map(day => data.weekly[day] || 0)
                };
            }

            if (data.monthly) {
                const monthlyLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                state.salesData.monthly = {
                    labels: monthlyLabels,
                    data: monthlyLabels.map(month => data.monthly[month] || 0)
                };
            }

            if (data.quarterly) {
                const quarterlyLabels = ['Q1', 'Q2', 'Q3', 'Q4'];
                state.salesData.quarterly = {
                    labels: quarterlyLabels,
                    data: quarterlyLabels.map(quarter => data.quarterly[quarter] || 0)
                };
            }

            if (state.revenueChart) {
                const activeTab = document.querySelector('.tab-btn.bg-indigo-600');
                const period = activeTab ? activeTab.dataset.period : 'weekly';

                if (state.salesData[period]) {
                    state.revenueChart.data.labels = state.salesData[period].labels;
                    state.revenueChart.data.datasets[0].data = state.salesData[period].data;
                    state.revenueChart.update();
                }
            }

        } catch (err) {
            console.error('Error refreshing sales chart:', err);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {

        initRevenueChart();

        refreshDashboard();
        refreshSalesChart();

        setInterval(refreshDashboard, 30000);
        setInterval(refreshSalesChart, 60000);

    });
</script>
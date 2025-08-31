<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    @include('admin.layouts.header')
    @include('admin.layouts.sidebar')

    <div id="content-wrapper" class="flex flex-col min-h-screen">
        <div id="content" class="flex-1">
            <nav class="bg-white shadow-sm border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <button id="sidebarToggleTop" class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="text-2xl font-semibold text-gray-800">Dashboard</h1>
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
                        <div
                            class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
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
                                    {{ $card['value'] }}</h2>
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
                                                {{ strtoupper(substr($transaction->cashier->firstname, 0, 1)) }}
                                                {{ strtoupper(substr($transaction->cashier->lastname, 0, 1)) }}
                                            </span>
                                        </div>
                                        <span class="text-gray-800 font-medium">
                                            {{ $transaction->cashier->firstname }} {{ $transaction->cashier->lastname }}
                                        </span>
                                    </div>
                                    <span class="text-gray-900 font-semibold">
                                        ₱ {{ number_format($transaction->total_amount, 2) }}
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
                if (state.revenueChart) {
                    state.revenueChart.data.labels = state.salesData[period].labels;
                    state.revenueChart.data.datasets[0].data = state.salesData[period].data;
                    state.revenueChart.update();
                }
            });
        });

        const calendarGrid = document.getElementById('calendarGrid');
        const calendarMonth = document.getElementById('calendarMonth');
        const todayDate = document.getElementById('todayDate');
        const prevMonthBtn = document.getElementById('prevMonth');
        const nextMonthBtn = document.getElementById('nextMonth');

        async function refreshDashboard() {
            try {
                const res = await fetch("{{ route('home.dashboard.data') }}");
                const data = await res.json();

                const revenueElement = document.getElementById('revenueValue');
                if (revenueElement) {
                    const revenueValue = data.revenue.toString().startsWith('₱') ? data.revenue : '₱ ' + data.revenue;
                    revenueElement.textContent = revenueValue;
                }

                const customersElement = document.getElementById('customersValue');
                if (customersElement) {
                    customersElement.textContent = data.customers;
                }

                const stockContainer = document.getElementById('stockContainer');
                if (stockContainer && data.stock) {
                    stockContainer.innerHTML = '';
                    data.stock.forEach(stock => {
                        const color = stock.quantity >= 60 ? 'green' : stock.quantity >= 30 ? 'orange' : 'red';
                        const status = stock.quantity >= 60 ? 'Sufficient' : stock.quantity >= 30 ? 'Low' : 'Critical';
                        const textColor = stock.quantity >= 60 ? 'text-green-600' : stock.quantity >= 30 ? 'text-orange-500' : 'text-red-600';
                        stockContainer.innerHTML += `<div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg shadow-sm">
                            <div class="flex items-center space-x-3">
                                <div class="w-6 h-2 rounded-sm" style="background-color:${color};"></div>
                                <span class="text-gray-800 font-medium">${stock.name}</span>
                            </div>
                            <div><span class="text-sm font-semibold ${textColor}">${status}</span></div>
                        </div>`;
                    });
                }

                if (data.transactions) {
                    const transactionHistory = document.getElementById('transactionHistory');
                    if (transactionHistory) {
                        transactionHistory.innerHTML = '';
                        data.transactions.forEach(transaction => {
                            const transactionDiv = document.createElement('div');
                            transactionDiv.className = 'flex justify-between items-center p-3 bg-gray-50 rounded-lg';
                            transactionDiv.innerHTML = `
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">
                                            ${transaction.cashier.firstname.charAt(0).toUpperCase()}${transaction.cashier.lastname.charAt(0).toUpperCase()}
                                        </span>
                                    </div>
                                    <span class="text-gray-800 font-medium">
                                        ${transaction.cashier.firstname} ${transaction.cashier.lastname}
                                    </span>
                                </div>
                                <span class="text-gray-900 font-semibold">
                                    ₱ ${parseFloat(transaction.total_amount).toFixed(2)}
                                </span>
                            `;
                            transactionHistory.appendChild(transactionDiv);
                        });
                    }
                }
            } catch (err) {
                console.error('Error refreshing dashboard:', err);
            }
        }

        async function refreshSalesChart() {
            try {
                const res = await fetch("{{ route('home.dashboard.sales-data') }}");
                const data = await res.json();

                ['weekly', 'monthly', 'quarterly'].forEach(period => {
                    if (data[period]) {
                        state.salesData[period].labels = Object.keys(data[period]);
                        state.salesData[period].data = Object.values(data[period]);
                    }
                });

                const activeTab = document.querySelector('.tab-btn.bg-indigo-600');
                const period = activeTab ? activeTab.dataset.period : 'weekly';

                if (state.revenueChart) {
                    state.revenueChart.data.labels = state.salesData[period].labels;
                    state.revenueChart.data.datasets[0].data = state.salesData[period].data;
                    state.revenueChart.update();
                }
            } catch (err) {
                console.error('Error refreshing sales chart:', err);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            initRevenueChart();
            refreshDashboard();
            refreshSalesChart();
            setInterval(refreshDashboard, 5000);
            setInterval(refreshSalesChart, 5000);
        });
    </script>
</body>

</html>
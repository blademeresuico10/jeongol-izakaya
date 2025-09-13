<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
    @vite('resources/css/app.css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .print-area,
            .print-area * {
                visibility: visible;
            }

            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .no-print {
                display: none !important;
            }

            .print-header {
                text-align: center;
                margin-bottom: 30px;
            }

            .print-table {
                width: 100%;
                border-collapse: collapse;
            }

            .print-table th,
            .print-table td {
                border: 1px solid #000;
                padding: 8px;
                text-align: left;
            }

            .print-table th {
                background-color: #f0f0f0;
            }
        }
    </style>
</head>

<body class="bg-gray-100">

    @include('admin.layouts.header')
    @include('admin.layouts.sidebar')

    <div id="content-wrapper" class="flex flex-col min-h-screen">
        <div id="content" class="flex-1">

            <nav class="bg-white shadow-sm border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-800">Reports</h1>
            </nav>

            <div class="p-6 space-y-6">

                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6 max-w-10xl mx-auto">
                    <div class="flex flex-wrap gap-4 items-end">
                        <div class="flex-1 min-w-[150px]">
                            <label for="from_date" class="block text-sm font-semibold text-gray-700">From Date</label>
                            <input type="date" name="from_date" id="from_date"
                                class="w-full border border-gray-300 rounded-xl px-4 py-3"
                                value="{{ request('from_date', $dateFrom->toDateString()) }}">
                        </div>

                        <div class="flex-1 min-w-[150px]">
                            <label for="to_date" class="block text-sm font-semibold text-gray-700">To Date</label>
                            <input type="date" name="to_date" id="to_date"
                                class="w-full border border-gray-300 rounded-xl px-4 py-3"
                                value="{{ request('to_date', $dateTo->toDateString()) }}">
                        </div>

                        <div class="flex-1 min-w-[150px]">
                            <label class="block text-sm font-semibold text-gray-700">Quick Filters</label>
                            <select id="quickFilter"
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Custom Range</option>
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                                <option value="quarter">This Quarter</option>
                            </select>
                        </div>

                        <div class="flex gap-2">
                            <button type="button" onclick="exportToPdf()"
                                class="bg-red-600 text-white px-5 py-3 rounded-lg">
                                <i class="fas fa-file-pdf mr-2"></i> Export PDF
                            </button>
                            <!--<button type="button" onclick="exportToCsv()"
                                class="bg-blue-600 text-white px-5 py-3 rounded-lg">
                                <i class="fas fa-file-csv mr-2"></i> Export CSV
                            </button>-->
                        </div>
                    </div>
                </div>

                <!-- Fixed: Updated metrics cards -->
                <div id="reportContent" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white p-6 rounded-2xl shadow-md border">
                        <h3 class="text-lg font-semibold text-gray-700">Total Sales</h3>
                        <p class="text-2xl font-bold text-black mt-2">
                            ₱{{ number_format($totalSales ?? 0, 2) }}
                        </p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-md border">
                        <h3 class="text-lg font-semibold text-gray-700">Total Pax</h3>
                        <p class="text-2xl font-bold text-black mt-2">
                            {{ $totalPax ?? 0 }}
                        </p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-md border">
                        <h3 class="text-lg font-semibold text-gray-700">Total Discounts</h3>
                        <p class="text-2xl font-bold text-black mt-2">
                            ₱{{ number_format($totalDiscounts ?? 0, 2) }}
                        </p>
                    </div>
                    <!-- Added: New metric card -->
                    <div class="bg-white p-6 rounded-2xl shadow-md border">
                        <h3 class="text-lg font-semibold text-gray-700">Transactions</h3>
                        <p class="text-2xl font-bold text-black mt-2">
                            {{ $transactionCount ?? 0 }}
                        </p>
                    </div>
                </div>

                <!-- Added: Average Order Value -->
                <div class="bg-white p-6 rounded-2xl shadow-md border">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="text-center">
                            <h3 class="text-lg font-semibold text-gray-700">Average Order Value</h3>
                            <p class="text-2xl font-bold text-black mt-2">
                                ₱{{ number_format($averageOrderValue ?? 0, 2) }}
                            </p>
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg font-semibold text-gray-700">Date Range</h3>
                            <p class="text-lg text-gray-600 mt-2">
                                {{ $dateFrom->format('M j, Y') }} - {{ $dateTo->format('M j, Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-md border mt-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Product Consumption</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left">Product</th>
                                    <th class="px-4 py-2 text-left">Category</th>
                                    <th class="px-4 py-2 text-right">Quantity</th>
                                    <th class="px-4 py-2 text-right">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($productConsumption as $product)
                                    <tr class="border-t">
                                        <td class="px-4 py-2">{{ $product->menu_item }}</td>
                                        <td class="px-4 py-2">{{ ucfirst($product->category) }}</td>
                                        <td class="px-4 py-2 text-right">{{ $product->total_quantity }}</td>
                                        <td class="px-4 py-2 text-right">₱{{ number_format($product->total_revenue, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-2 text-center text-gray-500">No data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Fixed: Sales breakdown table -->
                <div class="bg-white p-6 rounded-2xl shadow-md border mt-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Sales Breakdown</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2">Transaction #</th>
                                    <th class="px-4 py-2">Date</th>
                                    <th class="px-4 py-2">Time</th>
                                    <th class="px-4 py-2">Table</th>
                                    <th class="px-4 py-2">Customer</th>
                                    <th class="px-4 py-2">Pax</th>
                                    <th class="px-4 py-2 text-right">Subtotal</th>
                                    <th class="px-4 py-2 text-right">Discount</th>
                                    <th class="px-4 py-2 text-right">Total</th>
                                    <th class="px-4 py-2">Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $s)
                                    <tr class="border-t">
                                        <td class="px-4 py-2">{{ $s->transaction_no ?? '#' . $s->id }}</td>
                                        <td class="px-4 py-2">{{ $s->date }}</td>
                                        <td class="px-4 py-2">{{ $s->time ?? 'N/A' }}</td>
                                        <td class="px-4 py-2">{{ $s->table_number }}</td>
                                        <td class="px-4 py-2">{{ $s->customer_name }}</td>
                                        <td class="px-4 py-2">{{ $s->pax }}</td>
                                        <td class="px-4 py-2 text-right">₱{{ number_format($s->subtotal, 2) }}</td>
                                        <td class="px-4 py-2 text-right">₱{{ number_format($s->discount_total ?? 0, 2) }}
                                        </td>
                                        <td class="px-4 py-2 text-right">₱{{ number_format($s->total, 2) }}</td>
                                        <td class="px-4 py-2">{{ $s->payment_method ?? 'Cash' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="px-4 py-2 text-center text-gray-500">No sales data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function exportToExcel() {
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = '{{ route("admin.reports.export") }}';
            form.target = '_blank';

            const fromDate = document.createElement('input');
            fromDate.type = 'hidden';
            fromDate.name = 'from_date';
            fromDate.value = document.getElementById('from_date').value;
            form.appendChild(fromDate);

            const toDate = document.createElement('input');
            toDate.type = 'hidden';
            toDate.name = 'to_date';
            toDate.value = document.getElementById('to_date').value;
            form.appendChild(toDate);

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }

        function fetchReportData() {
            const fromDate = document.getElementById('from_date').value;
            const toDate = document.getElementById('to_date').value;

            if (!fromDate || !toDate) return;

            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('from_date', fromDate);
            currentUrl.searchParams.set('to_date', toDate);

            window.location.href = currentUrl.toString();
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('quickFilter').addEventListener('change', function () {
                let today = new Date();
                let fromDate = document.getElementById('from_date');
                let toDate = document.getElementById('to_date');

                if (this.value === "today") {
                    fromDate.value = toDate.value = today.toISOString().split("T")[0];
                } else if (this.value === "week") {
                    let firstDay = new Date(today.setDate(today.getDate() - today.getDay() + 1));
                    let lastDay = new Date(today.setDate(firstDay.getDate() + 6));
                    fromDate.value = firstDay.toISOString().split("T")[0];
                    toDate.value = lastDay.toISOString().split("T")[0];
                } else if (this.value === "month") {
                    let firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                    let lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    fromDate.value = firstDay.toISOString().split("T")[0];
                    toDate.value = lastDay.toISOString().split("T")[0];
                } else if (this.value === "quarter") {
                    let quarter = Math.floor(today.getMonth() / 3);
                    let firstDay = new Date(today.getFullYear(), quarter * 3, 1);
                    let lastDay = new Date(today.getFullYear(), quarter * 3 + 3, 0);
                    fromDate.value = firstDay.toISOString().split("T")[0];
                    toDate.value = lastDay.toISOString().split("T")[0];
                }

                if (this.value !== "") fetchReportData();
            });

            document.getElementById('from_date').addEventListener('change', function () {
                document.getElementById('quickFilter').value = "";
                fetchReportData();
            });

            document.getElementById('to_date').addEventListener('change', function () {
                document.getElementById('quickFilter').value = "";
                fetchReportData();
            });
        });

        function exportToPdf() {
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = '{{ route("admin.reports.export") }}';
            form.target = '_blank';

            const fromDate = document.createElement('input');
            fromDate.type = 'hidden';
            fromDate.name = 'from_date';
            fromDate.value = document.getElementById('from_date').value;
            form.appendChild(fromDate);

            const toDate = document.createElement('input');
            toDate.type = 'hidden';
            toDate.name = 'to_date';
            toDate.value = document.getElementById('to_date').value;
            form.appendChild(toDate);

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }

        /*
        function exportToCsv() {
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = '{{ route("admin.reports.export-csv") }}';
            form.target = '_blank';

            const fromDate = document.createElement('input');
            fromDate.type = 'hidden';
            fromDate.name = 'from_date';
            fromDate.value = document.getElementById('from_date').value;
            form.appendChild(fromDate);

            const toDate = document.createElement('input');
            toDate.type = 'hidden';
            toDate.name = 'to_date';
            toDate.value = document.getElementById('to_date').value;
            form.appendChild(toDate);

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }*/
    </script>

</body>

</html>
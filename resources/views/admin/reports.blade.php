@include('admin.layouts.header')
@include('admin.layouts.sidebar')

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <div class="d-flex flex-column">
                <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Reports</h1>
            </div>
        </nav>

        <div class="container px-6 py-6">
            <div class="space-y-6">

                <div class="flex items-center justify-between bg-white border p-3 rounded shadow-sm">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-chart-line text-blue-600 text-lg"></i>
                        <h2 class="text-md font-semibold text-gray-800">Sales Report</h2>
                    </div>
                    <div class="flex items-center space-x-2">
                        <select data-filter="sales" class="border rounded px-3 py-1 text-sm">
                            <option value="daily">Today</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                        <button data-export="sales"
                            class="bg-green-600 text-white px-4 py-1.5 rounded text-sm font-medium">
                            Export
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between bg-white border p-3 rounded shadow-sm">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-user-tie text-purple-600 text-lg"></i>
                        <h2 class="text-md font-semibold text-gray-800">Transaction Report</h2>
                    </div>
                    <div class="flex items-center space-x-2">
                        <select data-filter="transaction" class="border rounded px-3 py-1 text-sm">
                            <option value="daily">Today</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                        <button data-export="transaction"
                            class="bg-green-600 text-white px-4 py-1.5 rounded text-sm font-medium">
                            Export
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between bg-white border p-3 rounded shadow-sm">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-box text-red-600 text-lg"></i>
                        <h2 class="text-md font-semibold text-gray-800">Stock Report</h2>
                    </div>
                    <div class="flex items-center space-x-2">
                        <select data-filter="stock" class="border rounded px-3 py-1 text-sm">
                            <option value="daily">Today</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                        <button data-export="stock"
                            class="bg-green-600 text-white px-4 py-1.5 rounded text-sm font-medium">
                            Export
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('[data-export]').forEach(button => {
    button.addEventListener('click', () => {
        const type = button.getAttribute('data-export');
        const filter = document.querySelector(`[data-filter="${type}"]`).value;

        const routes = {
            sales: "{{ route('admin.sales_report') }}",
            transaction: "{{ route('admin.transaction_reports') }}",
            stock: "{{ route('admin.stock_reports') }}"
        };

        if (routes[type]) {
            window.location.href = `${routes[type]}?filter=${filter}`;
        }
    });
});
</script>
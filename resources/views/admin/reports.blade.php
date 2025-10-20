@include('admin.layouts.header')
@include('admin.layouts.sidebar')

<style>
    button[disabled] {
        transition: all 0.3s ease;
        opacity: 0.6;
        cursor: not-allowed;
    }

    .fa-spinner {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .tab-content-show {
        animation: fadeIn 0.3s ease-in;
    }

    .report-content {
        animation: fadeIn 0.4s ease-in;
    }

    .preset-btn.active {
        background-color: #3b82f6;
        color: white;
    }
</style>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <!-- Header -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow-sm">
            <div class="d-flex align-items-center justify-content-between w-100">
                <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Reports Dashboard</h1>
            </div>
        </nav>

        <div class="px-4 py-3">
            <!-- Tabs Navigation -->
            <ul class="flex border-b-2 border-gray-200 mb-4" id="reportTabs" role="tablist">
                <li role="presentation">
                    <button
                        class="tab-link relative flex items-center px-6 py-3 font-medium text-blue-600 bg-transparent border-none active"
                        id="sales-tab" data-tab="sales" type="button" role="tab">
                        <span class="inline-flex items-center justify-center w-5 h-5 mr-2">
                            <i class="fas fa-chart-line"></i>
                        </span>
                        Sales Report
                    </button>
                </li>
                <li role="presentation">
                    <button
                        class="tab-link relative flex items-center px-6 py-3 font-medium text-gray-500 bg-transparent border-none "
                        id="transaction-tab" data-tab="transaction" type="button" role="tab">
                        <span class="inline-flex items-center justify-center w-5 h-5 mr-2">
                            <i class="fas fa-receipt"></i>
                        </span>
                        Transaction Report
                    </button>
                </li>
                <li role="presentation">
                    <button
                        class="tab-link relative flex items-center px-6 py-3 font-medium text-gray-500 bg-transparent border-none "
                        id="inventory-tab" data-tab="inventory" type="button" role="tab">
                        <span class="inline-flex items-center justify-center w-5 h-5 mr-2">
                            <i class="fas fa-box"></i>
                        </span>
                        Inventory Report
                    </button>
                </li>
                <li role="presentation">
                    <button
                        class="tab-link relative flex items-center px-6 py-3 font-medium text-gray-500 bg-transparent border-none"
                        id="menu-tab" data-tab="menu" type="button" role="tab">
                        <span class="inline-flex items-center justify-center w-5 h-5 mr-2">
                            <i class="fas fa-utensils"></i>
                        </span>
                        Menu Report
                    </button>
                </li>
            </ul>

            <!-- Tabs Content -->
            <div id="reportTabsContent">

                <!-- Sales Report Tab -->
                <div class="tab-pane block" id="sales" role="tabpanel">
                    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 text-white p-8 rounded-lg mb-6">
                        <h3 class="text-2xl font-semibold mb-2">
                            <i class="fas fa-chart-line mr-2"></i>Sales Report
                        </h3>
                    </div>

                    <!-- Report Filters -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6 shadow-sm">
                        <h5 class="text-lg font-semibold mb-4 text-gray-800">
                            <i class="fas fa-filter mr-2 text-gray-600"></i>Filter by Date Range
                        </h5>

                        <!-- Quick Presets -->
                        <div class="mb-4">
                            <label class="text-sm font-medium text-gray-600 mb-2 block">Quick Select</label>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    class="preset-btn px-4 py-2 text-sm border border-gray-300 rounded-md hover:bg-blue-50 hover:border-blue-300 transition-colors"
                                    data-preset="today" data-report="sales">
                                    Today
                                </button>

                                <button
                                    class="preset-btn px-4 py-2 text-sm border border-gray-300 rounded-md hover:bg-blue-50 hover:border-blue-300 transition-colors"
                                    data-preset="this-week" data-report="sales">
                                    This Week
                                </button>

                                <button
                                    class="preset-btn px-4 py-2 text-sm border border-gray-300 rounded-md hover:bg-blue-50 hover:border-blue-300 transition-colors"
                                    data-preset="this-month" data-report="sales">
                                    This Month
                                </button>

                            </div>
                        </div>

                        <!-- Custom Date Range -->
                        <div>
                            <label class="text-sm font-medium text-gray-600 mb-2 block">Custom Date Range</label>
                            <div class="flex gap-3 items-center">
                                <input type="date"
                                    class="flex-1 border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                    id="sales_start">
                                <span class="text-gray-500 font-medium">to</span>
                                <input type="date"
                                    class="flex-1 border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                    id="sales_end">
                            </div>
                        </div>

                        <div class="mt-6 flex gap-3 justify-end">
                            <button
                                class="generate-btn inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-2.5 rounded-md text-sm font-medium transition-all duration-200 hover:bg-blue-700 hover:shadow-lg active:scale-95"
                                data-report="sales">
                                <i class="fas fa-sync-alt"></i>
                                Generate Report
                            </button>
                            <button
                                class="export-btn inline-flex items-center gap-2 bg-green-600 text-white px-6 py-2.5 rounded-md text-sm font-medium transition-all duration-200 hover:bg-green-700 hover:shadow-lg active:scale-95"
                                data-report="Sales Report">
                                <i class="fas fa-download"></i>
                                Export PDF
                            </button>
                        </div>
                    </div>

                    <!-- Report Content Area -->
                    <div id="sales-report-content"></div>
                </div>

                <!-- Transaction Report Tab -->
                <div class="tab-pane hidden" id="transaction" role="tabpanel">
                    <div class="bg-gradient-to-br from-purple-500 to-pink-500 text-white p-8 rounded-lg mb-6">
                        <h3 class="text-2xl font-semibold mb-2">
                            <i class="fas fa-receipt mr-2"></i>Transaction Report
                        </h3>

                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6 shadow-sm">
                        <h5 class="text-lg font-semibold mb-4 text-gray-800">
                            <i class="fas fa-filter mr-2 text-gray-600"></i>Filter by Date Range
                        </h5>

                        <!-- Quick Presets -->
                        <div class="mb-4">
                            <label class="text-sm font-medium text-gray-600 mb-2 block">Quick Select</label>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    class="preset-btn px-4 py-2 text-sm border border-gray-300 rounded-md hover:bg-blue-50 hover:border-blue-300 transition-colors"
                                    data-preset="today" data-report="transaction">
                                    Today
                                </button>

                                <button
                                    class="preset-btn px-4 py-2 text-sm border border-gray-300 rounded-md hover:bg-blue-50 hover:border-blue-300 transition-colors"
                                    data-preset="this-week" data-report="transaction">
                                    This Week
                                </button>

                                <button
                                    class="preset-btn px-4 py-2 text-sm border border-gray-300 rounded-md hover:bg-blue-50 hover:border-blue-300 transition-colors"
                                    data-preset="this-month" data-report="transaction">
                                    This Month
                                </button>

                            </div>
                        </div>

                        <!-- Custom Date Range -->
                        <div>
                            <label class="text-sm font-medium text-gray-600 mb-2 block">Custom Date Range</label>
                            <div class="flex gap-3 items-center">
                                <input type="date"
                                    class="flex-1 border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                    id="transaction_start">
                                <span class="text-gray-500 font-medium">to</span>
                                <input type="date"
                                    class="flex-1 border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                    id="transaction_end">
                            </div>
                        </div>

                        <div class="mt-6 flex gap-3 justify-end">
                            <button
                                class="generate-btn inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-2.5 rounded-md text-sm font-medium transition-all duration-200 hover:bg-blue-700 hover:shadow-lg active:scale-95"
                                data-report="transaction">
                                <i class="fas fa-sync-alt"></i>
                                Generate Report
                            </button>
                            <button
                                class="export-btn inline-flex items-center gap-2 bg-green-600 text-white px-6 py-2.5 rounded-md text-sm font-medium transition-all duration-200 hover:bg-green-700 hover:shadow-lg active:scale-95"
                                data-report="Transaction Report">
                                <i class="fas fa-download"></i>
                                Export PDF
                            </button>
                        </div>
                    </div>

                    <!-- Report Content Area -->
                    <div id="transaction-report-content"></div>
                </div>

                <!-- Inventory Report Tab -->
                <div class="tab-pane hidden" id="inventory" role="tabpanel">
                    <div class="bg-gradient-to-br from-red-500 to-orange-500 text-white p-8 rounded-lg mb-6">
                        <h3 class="text-2xl font-semibold mb-2">
                            <i class="fas fa-box mr-2"></i>Inventory Report
                        </h3>
                    </div>

                    <!-- Report Filters -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6 shadow-sm">
                        <!-- Report Type Selection -->
                        <h5 class="text-lg font-semibold mb-4 text-gray-800">
                            <i class="fas fa-clipboard-list mr-2 text-gray-600"></i>Select Report Type
                        </h5>

                        <div class="mb-6">
                            <label class="text-sm font-medium text-gray-600 mb-3 block">Choose Report</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                <button
                                    class="inventory-type-btn px-4 py-3 text-sm border-2 border-blue-500 bg-blue-50 rounded-md hover:bg-blue-100 transition-colors active font-medium text-left"
                                    data-type="current-stock">
                                    <i class="fas fa-boxes text-blue-600 mr-2"></i>
                                    Current Stock
                                </button>

                                <button
                                    class="inventory-type-btn px-4 py-3 text-sm border border-gray-300 rounded-md hover:bg-blue-50 hover:border-blue-300 transition-colors font-medium text-left"
                                    data-type="stock-movement">
                                    <i class="fas fa-exchange-alt text-purple-600 mr-2"></i>
                                    Stock Movement
                                </button>

                                <button
                                    class="inventory-type-btn px-4 py-3 text-sm border border-gray-300 rounded-md hover:bg-blue-50 hover:border-blue-300 transition-colors font-medium text-left"
                                    data-type="consumption">
                                    <i class="fas fa-chart-area text-indigo-600 mr-2"></i>
                                    Consumption
                                </button>

                                <button
                                    class="inventory-type-btn px-4 py-3 text-sm border border-gray-300 rounded-md hover:bg-blue-50 hover:border-blue-300 transition-colors font-medium text-left"
                                    data-type="expired">
                                    <i class="fas fa-calendar-times text-red-600 mr-2"></i>
                                    Expired Items
                                </button>

                            </div>
                        </div>

                        <!-- Date Range Filter (conditionally shown) -->
                        <div id="inventory-date-section">
                            <hr class="my-6 border-gray-200">

                            <h5 class="text-lg font-semibold mb-4 text-gray-800">
                                <i class="fas fa-filter mr-2 text-gray-600"></i>Filter by Date Range
                            </h5>

                            <!-- Quick Presets -->
                            <div class="mb-4">
                                <label class="text-sm font-medium text-gray-600 mb-2 block">Quick Select</label>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        class="preset-btn px-4 py-2 text-sm border border-gray-300 rounded-md hover:bg-blue-50 hover:border-blue-300 transition-colors"
                                        data-preset="today" data-report="inventory">
                                        Today
                                    </button>

                                    <button
                                        class="preset-btn px-4 py-2 text-sm border border-gray-300 rounded-md hover:bg-blue-50 hover:border-blue-300 transition-colors"
                                        data-preset="this-week" data-report="inventory">
                                        This Week
                                    </button>

                                    <button
                                        class="preset-btn px-4 py-2 text-sm border border-gray-300 rounded-md hover:bg-blue-50 hover:border-blue-300 transition-colors"
                                        data-preset="this-month" data-report="inventory">
                                        This Month
                                    </button>
                                </div>
                            </div>

                            <!-- Custom Date Range -->
                            <div>
                                <label class="text-sm font-medium text-gray-600 mb-2 block">Custom Date Range</label>
                                <div class="flex gap-3 items-center">
                                    <input type="date"
                                        class="flex-1 border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                        id="inventory_start">
                                    <span class="text-gray-500 font-medium">to</span>
                                    <input type="date"
                                        class="flex-1 border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                        id="inventory_end">
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 flex gap-3 justify-end">
                            <button
                                class="generate-btn inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-2.5 rounded-md text-sm font-medium transition-all duration-200 hover:bg-blue-700 hover:shadow-lg active:scale-95"
                                data-report="inventory">
                                <i class="fas fa-sync-alt"></i>
                                Generate Report
                            </button>
                            <button
                                class="export-btn inline-flex items-center gap-2 bg-green-600 text-white px-6 py-2.5 rounded-md text-sm font-medium transition-all duration-200 hover:bg-green-700 hover:shadow-lg active:scale-95"
                                data-report="Inventory Report">
                                <i class="fas fa-download"></i>
                                Export PDF
                            </button>
                        </div>
                    </div>

                    <!-- Report Content Area -->
                    <div id="inventory-report-content"></div>
                </div>

                <!-- Menu Performance Tab -->
                <div class="tab-pane hidden" id="menu" role="tabpanel">
                    <div class="bg-gradient-to-br from-cyan-500 to-blue-500 text-white p-8 rounded-lg mb-6">
                        <h3 class="text-2xl font-semibold mb-2">
                            <i class="fas fa-utensils mr-2"></i>Menu Performance Report
                        </h3>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6 shadow-sm">
                        <h5 class="text-lg font-semibold mb-4 text-gray-800">
                            <i class="fas fa-filter mr-2 text-gray-600"></i>Filter by Date Range
                        </h5>

                        <!-- Quick Presets -->
                        <div class="mb-4">
                            <label class="text-sm font-medium text-gray-600 mb-2 block">Quick Select</label>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    class="preset-btn px-4 py-2 text-sm border border-gray-300 rounded-md hover:bg-blue-50 hover:border-blue-300 transition-colors"
                                    data-preset="today" data-report="menu">
                                    Today
                                </button>

                                <button
                                    class="preset-btn px-4 py-2 text-sm border border-gray-300 rounded-md hover:bg-blue-50 hover:border-blue-300 transition-colors"
                                    data-preset="this-week" data-report="menu">
                                    This Week
                                </button>

                                <button
                                    class="preset-btn px-4 py-2 text-sm border border-gray-300 rounded-md hover:bg-blue-50 hover:border-blue-300 transition-colors"
                                    data-preset="this-month" data-report="menu">
                                    This Month
                                </button>

                            </div>
                        </div>

                        <!-- Custom Date Range -->
                        <div>
                            <label class="text-sm font-medium text-gray-600 mb-2 block">Custom Date Range</label>
                            <div class="flex gap-3 items-center">
                                <input type="date"
                                    class="flex-1 border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                    id="menu_start">
                                <span class="text-gray-500 font-medium">to</span>
                                <input type="date"
                                    class="flex-1 border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                    id="menu_end">
                            </div>
                        </div>

                        <div class="mt-6 flex gap-3 justify-end">
                            <button
                                class="generate-btn inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-2.5 rounded-md text-sm font-medium transition-all duration-200 hover:bg-blue-700 hover:shadow-lg active:scale-95"
                                data-report="menu">
                                <i class="fas fa-sync-alt"></i>
                                Generate Report
                            </button>
                            <button
                                class="export-btn inline-flex items-center gap-2 bg-green-600 text-white px-6 py-2.5 rounded-md text-sm font-medium transition-all duration-200 hover:bg-green-700 hover:shadow-lg active:scale-95"
                                data-report="Menu Performance Report">
                                <i class="fas fa-download"></i>
                                Export PDF
                            </button>
                        </div>
                    </div>

                    <!-- Report Content Area -->
                    <div id="menu-report-content"></div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    let selectedInventoryType = 'current-stock';
    const reportTypesWithDateFilter = ['stock-movement', 'consumption', 'expired'];

    document.addEventListener('DOMContentLoaded', function () {
        const tabLinks = document.querySelectorAll('.tab-link');
        const tabPanes = document.querySelectorAll('.tab-pane');

        tabLinks.forEach(link => {
            link.addEventListener('click', function () {
                const targetTab = this.getAttribute('data-tab');

                tabLinks.forEach(l => {
                    l.classList.remove('active', 'text-blue-600');
                    l.classList.add('text-gray-500');
                });

                this.classList.add('active', 'text-blue-600');
                this.classList.remove('text-gray-500');

                tabPanes.forEach(pane => {
                    pane.classList.add('hidden');
                    pane.classList.remove('block', 'tab-content-show');
                });

                const targetPane = document.getElementById(targetTab);
                targetPane.classList.remove('hidden');
                targetPane.classList.add('block', 'tab-content-show');
            });
        });

        const presetButtons = document.querySelectorAll('.preset-btn');

        presetButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const preset = this.getAttribute('data-preset');
                const reportType = this.getAttribute('data-report');

                this.parentElement.querySelectorAll('.preset-btn').forEach(b => {
                    b.classList.remove('active');
                });
                this.classList.add('active');

                const dates = getPresetDates(preset);
                const startInput = document.getElementById(`${reportType}_start`);
                const endInput = document.getElementById(`${reportType}_end`);

                if (startInput && endInput) {
                    startInput.value = dates.start;
                    endInput.value = dates.end;
                }
            });
        });

        const generateButtons = document.querySelectorAll('.generate-btn');

        generateButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const reportType = this.getAttribute('data-report');
                generateReport(reportType, this);
            });
        });

        const exportButtons = document.querySelectorAll('.export-btn');

        exportButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const reportType = this.getAttribute('data-report');
                exportToPDF(reportType, this);
            });
        });


        const inventoryTypeButtons = document.querySelectorAll('.inventory-type-btn');
        const dateSection = document.getElementById('inventory-date-section');

        if (dateSection) {
            dateSection.style.display = 'none';
        }


        inventoryTypeButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                inventoryTypeButtons.forEach(b => {
                    b.classList.remove('active');
                    b.classList.remove('border-blue-500', 'bg-blue-50', 'border-2');
                    b.classList.add('border-gray-300', 'border');
                });

                this.classList.add('active');
                this.classList.remove('border-gray-300', 'border');
                this.classList.add('border-blue-500', 'bg-blue-50', 'border-2');

                selectedInventoryType = this.getAttribute('data-type');

                if (reportTypesWithDateFilter.includes(selectedInventoryType)) {
                    dateSection.style.display = 'block';
                } else {
                    dateSection.style.display = 'none';
                }
            });
        });


    });


    function getPresetDates(preset) {
        const today = new Date();
        const formatDate = (date) => date.toISOString().split('T')[0];

        let start, end;

        switch (preset) {
            case 'today':
                start = end = today;
                break;

            case 'yesterday':
                start = end = new Date(today.setDate(today.getDate() - 1));
                break;

            case 'this-week':
                const firstDay = today.getDate() - today.getDay();
                start = new Date(today.setDate(firstDay));
                end = new Date();
                break;

            case 'last-week':
                const lastWeekEnd = new Date(today.setDate(today.getDate() - today.getDay() - 1));
                const lastWeekStart = new Date(lastWeekEnd);
                lastWeekStart.setDate(lastWeekEnd.getDate() - 6);
                start = lastWeekStart;
                end = lastWeekEnd;
                break;

            case 'this-month':
                start = new Date(today.getFullYear(), today.getMonth(), 1);
                end = new Date();
                break;

            case 'last-month':
                start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                end = new Date(today.getFullYear(), today.getMonth(), 0);
                break;

            default:
                start = end = today;
        }

        return {
            start: formatDate(start),
            end: formatDate(end)
        };
    }

    function generateReport(reportType, button) {
        let startDate, endDate;

        if (reportType === 'inventory') {
            const dateSection = document.getElementById('inventory-date-section');

            if (dateSection.style.display !== 'none') {
                startDate = document.getElementById(`${reportType}_start`).value;
                endDate = document.getElementById(`${reportType}_end`).value;

                if (!startDate || !endDate) {
                    showAlert('error', 'Please select both start and end dates');
                    return;
                }

                if (new Date(startDate) > new Date(endDate)) {
                    showAlert('error', 'Start date cannot be after end date');
                    return;
                }
            } else {
                const today = new Date().toISOString().split('T')[0];
                startDate = endDate = today;
            }
        } else {
            startDate = document.getElementById(`${reportType}_start`).value;
            endDate = document.getElementById(`${reportType}_end`).value;

            if (!startDate || !endDate) {
                showAlert('error', 'Please select both start and end dates');
                return;
            }

            if (new Date(startDate) > new Date(endDate)) {
                showAlert('error', 'Start date cannot be after end date');
                return;
            }
        }

        const originalHTML = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner mr-2"></i>Generating...';

        fetchReportData(reportType, startDate, endDate)
            .then(data => {
                displayReport(reportType, data, startDate, endDate);
                button.disabled = false;
                button.innerHTML = originalHTML;
                showAlert('success', 'Report generated successfully!');
            })
            .catch(error => {
                button.disabled = false;
                button.innerHTML = originalHTML;
                showAlert('error', error.message || 'Failed to generate report');
                console.error('Error:', error);
            });
    }


    async function fetchReportData(reportType, startDate, endDate) {
        const endpoints = {
            'sales': '/admin/reports/sales',
            'transaction': '/admin/reports/transaction',
            'inventory': '/admin/reports/inventory',
            'menu': '/admin/reports/menu'
        };

        const endpoint = endpoints[reportType];

        if (!endpoint) {
            throw new Error('Invalid report type');
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.error('CSRF token not found!');
            throw new Error('CSRF token missing. Please refresh the page.');
        }

        try {
            let requestBody = {
                start_date: startDate,
                end_date: endDate
            };

            if (reportType === 'inventory') {
                requestBody.report_type = selectedInventoryType;
            }

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify(requestBody)
            });

            const responseText = await response.text();

            if (!response.ok) {
                try {
                    const errorData = JSON.parse(responseText);
                    throw new Error(errorData.message || `Server error: ${response.status}`);
                } catch {
                    throw new Error(`Server error: ${response.status} - ${responseText.substring(0, 100)}`);
                }
            }

            const result = JSON.parse(responseText);

            if (result.success) {
                return result.data;
            } else {
                throw new Error(result.message || 'Failed to generate report');
            }
        } catch (error) {
            throw error;
        }
    }

    function displayReport(reportType, data, startDate, endDate) {
        const contentDiv = document.getElementById(`${reportType}-report-content`);
        let html = '';

        const reportTypesWithDateFilter = ['stock-movement', 'consumption', 'expired'];
        const shouldShowDateRange = reportType === 'inventory' ? reportTypesWithDateFilter.includes(selectedInventoryType) : true;

        html += `
        <div class="report-content bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
        `;

        if (shouldShowDateRange) {
            const formattedStart = new Date(startDate).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
            const formattedEnd = new Date(endDate).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });

            html += `
            <div class="mb-4 pb-4 border-b border-gray-200">
                <h4 class="text-lg font-semibold text-gray-800">Report Period</h4>
                <p class="text-sm text-gray-600">${formattedStart} - ${formattedEnd}</p>
            </div>
            `;
        }

        switch (reportType) {
            case 'sales':
                html += generateSalesReport(data);
                break;
            case 'transaction':
                html += generateTransactionReport(data);
                break;
            case 'inventory':
                html += generateInventoryReport(data);
                break;
            case 'menu':
                html += generateMenuReport(data);
                break;
        }

        html += '</div>';
        contentDiv.innerHTML = html;
    }

    function generateSalesReport(data) {
        const summary = data.summary;
        const paymentMethods = data.payment_methods;
        const dailyBreakdown = data.daily_breakdown;
        const peakDay = data.peak_day;

        const growthColor = summary.sales_growth >= 0 ? 'text-green-600' : 'text-red-600';
        const growthIcon = summary.sales_growth >= 0 ? '↑' : '↓';

        return `
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">Gross Sales</div>
                    <i class="fas fa-chart-line text-xl opacity-75"></i>
                </div>
                <div class="text-2xl font-bold mb-1">₱${summary.gross_sales.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</div>
            </div>
            
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">Total Discounts</div>
                    <i class="fas fa-tags text-xl opacity-75"></i>
                </div>
                <div class="text-2xl font-bold mb-1">₱${summary.total_discounts.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</div>
            </div>
            
            <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">Net Sales</div>
                </div>
                <div class="text-2xl font-bold mb-1">₱${summary.net_sales.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</div>
            </div>
            
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">Total Orders</div>
                    <i class="fas fa-shopping-cart text-xl opacity-75"></i>
                </div>
                <div class="text-2xl font-bold mb-1">${summary.total_orders.toLocaleString()}</div>
            </div>
        </div>
        
        <!-- Payment Methods & Peak Day -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
           
            <!-- E-wallet Payment Methods -->
            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                <h5 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                    <i class="fas fa-credit-card mr-2 text-blue-600"></i>
                    E-wallet Payment Methods
                </h5>
                <div class="space-y-3">
                    <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-mobile-alt text-blue-600"></i>
                            </div>
                            <span class="font-medium text-gray-700">GCash</span>
                        </div>
                        <span class="text-lg font-semibold text-gray-900">₱${summary.gcash_total.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</span>
                    </div>
                    
                    <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-wallet text-green-600"></i>
                            </div>
                            <span class="font-medium text-gray-700">Maya</span>
                        </div>
                        <span class="text-lg font-semibold text-gray-900">₱${summary.maya_total.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</span>
                    </div>
                    
                    <div class="flex justify-between items-center pt-2 bg-gray-50 -mx-5 px-5 py-3 rounded-b-lg">
                        <span class="font-semibold text-gray-800">Total E-wallet</span>
                        <span class="text-xl font-bold text-blue-600">₱${summary.ewallet_total.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</span>
                    </div>
                </div>
            </div>

            <!-- Peak Day -->
            <div class="bg-gradient-to-br from-yellow-400 to-orange-500 text-white rounded-lg p-5 shadow-md">
                <h5 class="text-lg font-semibold mb-4 flex items-center">
                    <i class="fas fa-trophy mr-2"></i>
                    Peak Sales Day
                </h5>
                ${peakDay ? `
                    <div class="text-center py-4">
                        <div class="text-3xl font-bold mb-2">${peakDay.date}</div>
                        <div class="text-lg mb-4">₱${peakDay.sales.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="bg-white bg-opacity-20 rounded-lg p-3">
                                <div class="opacity-90 text-black">Orders</div>
                                <div class="text-xl font-bold text-black">${peakDay.orders}</div>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-lg p-3">
                                <div class="opacity-90 text-black">Discounts</div>
                                <div class="text-xl font-bold text-black">₱${peakDay.discounts.toLocaleString('en-PH', { maximumFractionDigits: 0 })}</div>
                            </div>
                        </div>
                    </div>
                ` : `
                    <div class="text-center py-8 opacity-75">
                        <i class="fas fa-chart-line text-4xl mb-3"></i>
                        <p>No sales data available</p>
                    </div>
                `}
            </div>
        </div>
        
        <!-- Daily Breakdown Table -->
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <h5 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>
                Daily Sales Breakdown
            </h5>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-black  uppercase tracking-wider">Orders</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-black  uppercase tracking-wider">Gross Sales</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-black  uppercase tracking-wider">Discounts</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-black  uppercase tracking-wider">Net Sales</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        ${dailyBreakdown.length > 0 ? dailyBreakdown.map(day => `
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-900 font-medium">${day.date}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 text-right">${day.orders}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 text-right">₱${day.gross_sales.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                                <td class="px-4 py-3 text-sm text-orange-600 text-right">-₱${day.discounts.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                                <td class="px-4 py-3 text-sm text-green-700 font-semibold text-right">₱${day.sales.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                            </tr>
                        `).join('') : `
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                    No sales data for this period
                                </td>
                            </tr>
                        `}
                    </tbody>
                    ${dailyBreakdown.length > 0 ? `
                        <tfoot class="bg-gray-100 font-semibold">
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">TOTAL</td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right">${summary.total_orders}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right">₱${summary.gross_sales.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                                <td class="px-4 py-3 text-sm text-orange-600 text-right">-₱${summary.total_discounts.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                                <td class="px-4 py-3 text-sm text-green-700 text-right">₱${summary.net_sales.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                            </tr>
                        </tfoot>
                    ` : ''}
                </table>
            </div>
        </div>
    `;
    }

    function generateTransactionReport(data) {
        const summary = data.summary;
        const transactions = data.transactions;

        return `
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">Total Transactions</div>
                    <i class="fas fa-receipt text-xl opacity-75"></i>
                </div>
                <div class="text-2xl font-bold mb-1">${summary.total_transactions.toLocaleString()}</div>
            </div>
            
            <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">Cash Transactions</div>
                    <i class="fas fa-money-bill-wave text-xl opacity-75"></i>
                </div>
                <div class="text-2xl font-bold mb-1">${summary.cash_transactions.toLocaleString()}</div>
                <div class="text-sm opacity-90 mt-2">₱${summary.cash_amount.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</div>
            </div>
            
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">E-wallet Transactions</div>
                    <i class="fas fa-mobile-alt text-xl opacity-75"></i>
                </div>
                <div class="text-2xl font-bold mb-1">${summary.ewallet_transactions.toLocaleString()}</div>
                <div class="text-sm opacity-90 mt-2">₱${summary.ewallet_amount.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</div>
            </div>
        </div>
        
        <!-- Total Amount Card -->
        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 text-white p-6 rounded-lg shadow-md mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-sm font-medium opacity-90 mb-1">Total Transaction Amount</div>
                    <div class="text-4xl font-bold">₱${summary.total_amount.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</div>
                </div>
                <div class="text-5xl opacity-75">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
        
        <!-- Transaction List -->
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <h5 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                <i class="fas fa-list mr-2 text-purple-600"></i>
                Transaction List
            </h5>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Order Type</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Payment Method</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-black uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        ${transactions.length > 0 ? transactions.map(transaction => `
                            <tr>
                                <td class="px-4 py-3 text-sm">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full ${transaction.order_type === 'Reservation' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'}">
                                        ${transaction.order_type}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 font-bold text-right">₱${transaction.amount.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full ${transaction.payment_method === 'Cash' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'
            }">
                                        ${transaction.payment_method}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-center">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full ${transaction.status === 'Completed' ? 'bg-green-100 text-green-800' :
                transaction.status === 'Pending' ? 'bg-yellow-100 text-yellow-800' :
                    'bg-red-100 text-red-800'
            }">
                                        ${transaction.status}
                                    </span>
                                </td>
                            </tr>
                        `).join('') : `
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                    No transactions for this period
                                </td>
                            </tr>
                        `}
                    </tbody>
                </table>
            </div>
        </div>
    `;
    }
    function generateInventoryReport(data) {
        const reportType = data.report_type;

        switch (reportType) {
            case 'current-stock':
                return generateCurrentStockReport(data);
            case 'stock-movement':
                return generateStockMovementReport(data);
            case 'expired':
                return generateExpiredItemsReport(data);
            case 'consumption':
                return generateConsumptionReport(data);
            default:
                return '<div class="text-center py-8 text-gray-500">Please select a report type</div>';
        }
    }


    function generateCurrentStockReport(data) {
        const summary = data.summary;
        const ingredients = data.ingredients;

        return `
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">Total Items</div>
                    <i class="fas fa-boxes text-xl opacity-75"></i>
                </div>
                <div class="text-2xl font-bold mb-1">${summary.total_items.toLocaleString()}</div>
            </div>
            
            <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">In Stock</div>
                    <i class="fas fa-check-circle text-xl opacity-75"></i>
                </div>
                <div class="text-2xl font-bold mb-1">${summary.in_stock.toLocaleString()}</div>
            </div>
            
            <div class="bg-gradient-to-br from-yellow-500 to-orange-500 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">Low Stock</div>
                    <i class="fas fa-exclamation-triangle text-xl opacity-75"></i>
                </div>
                <div class="text-2xl font-bold mb-1">${summary.low_stock.toLocaleString()}</div>
            </div>
            
            <div class="bg-gradient-to-br from-red-500 to-red-600 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">Out of Stock</div>
                    <i class="fas fa-times-circle text-xl opacity-75"></i>
                </div>
                <div class="text-2xl font-bold mb-1">${summary.out_of_stock.toLocaleString()}</div>
            </div>
        </div>
        
        <!-- Stock by Category -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                <h5 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                    <i class="fas fa-layer-group mr-2 text-blue-600"></i>
                    Stock by Category
                </h5>
                <div class="space-y-3">
                    ${summary.categories.map(cat => `
                        <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-${cat.color}-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-${cat.icon} text-${cat.color}-600"></i>
                                </div>
                                <span class="font-medium text-gray-700 capitalize">${cat.name}</span>
                            </div>
                            <span class="text-lg font-semibold text-gray-900">${cat.count} items</span>
                        </div>
                    `).join('')}
                </div>
            </div>

            <!-- Stock Status Overview -->
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-lg p-5 shadow-md">
                <h5 class="text-lg font-semibold mb-4 flex items-center">
                    <i class="fas fa-chart-pie mr-2"></i>
                    Stock Health Status
                </h5>
                <div class="space-y-3">
                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm opacity-90">Healthy</span>
                            <span class="font-bold">${summary.healthy_percentage}%</span>
                        </div>
                        <div class="w-full bg-white bg-opacity-30 rounded-full h-2">
                            <div class="bg-green-400 h-2 rounded-full" style="width: ${summary.healthy_percentage}%"></div>
                        </div>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm opacity-90">Needs Attention</span>
                            <span class="font-bold">${summary.attention_percentage}%</span>
                        </div>
                        <div class="w-full bg-white bg-opacity-30 rounded-full h-2">
                            <div class="bg-yellow-400 h-2 rounded-full" style="width: ${summary.attention_percentage}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stock Details Table -->
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <h5 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                <i class="fas fa-list mr-2 text-blue-600"></i>
                Current Stock Details
            </h5>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Ingredient</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Category</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">Current Stock</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-black uppercase tracking-wider">Unit</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-black uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        ${ingredients.length > 0 ? ingredients.map(item => `
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-900 font-medium">${item.name}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 capitalize">${item.category}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 font-bold text-right">${item.stocks.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 text-center uppercase">${item.unit}</td>
                                <td class="px-4 py-3 text-sm text-center">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full ${item.status === 'In Stock' ? 'bg-green-100 text-green-800' :
                item.status === 'Low Stock' ? 'bg-yellow-100 text-yellow-800' :
                    'bg-red-100 text-red-800'
            }">
                                        ${item.status}
                                    </span>
                                </td>
                            </tr>
                        `).join('') : `
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                    No inventory data available
                                </td>
                            </tr>
                        `}
                    </tbody>
                </table>
            </div>
        </div>
    `;
    }

    // Stock Movement Report
    function generateStockMovementReport(data) {
        const summary = data.summary;
        const movements = data.movements;

        return `
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">Stock In</div>
                    <i class="fas fa-arrow-down text-xl opacity-75"></i>
                </div>
                <div class="text-2xl font-bold mb-1">${summary.stock_in.toLocaleString()}</div>
                <div class="text-xs opacity-75 mt-1">${summary.stock_in_qty} ${summary.unit}</div>
            </div>
            
            <div class="bg-gradient-to-br from-red-500 to-red-600 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">Stock Out</div>
                    <i class="fas fa-arrow-up text-xl opacity-75"></i>
                </div>
                <div class="text-2xl font-bold mb-1">${summary.stock_out.toLocaleString()}</div>
                <div class="text-xs opacity-75 mt-1">${summary.stock_out_qty} ${summary.unit}</div>
            </div>
            
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">Adjustments</div>
                    <i class="fas fa-edit text-xl opacity-75"></i>
                </div>
                <div class="text-2xl font-bold mb-1">${summary.adjustments.toLocaleString()}</div>
            </div>
            
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">Total Movements</div>
                    <i class="fas fa-exchange-alt text-xl opacity-75"></i>
                </div>
                <div class="text-2xl font-bold mb-1">${summary.total_movements.toLocaleString()}</div>
            </div>
        </div>
        
        <!-- Movement Details Table -->
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <h5 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                <i class="fas fa-history mr-2 text-purple-600"></i>
                Stock Movement History
            </h5>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Ingredient</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-black uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">Quantity</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">Before</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">After</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        ${movements.length > 0 ? movements.map(move => `
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-900 font-medium">${move.date}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">${move.ingredient}</td>
                                <td class="px-4 py-3 text-sm text-center">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full ${move.type === 'stock_in' ? 'bg-green-100 text-green-800' :
                move.type === 'stock_out' ? 'bg-red-100 text-red-800' :
                    move.type === 'expired' ? 'bg-orange-100 text-orange-800' :
                        move.type === 'used' ? 'bg-blue-100 text-blue-800' :
                            'bg-gray-100 text-gray-800'
            }">
                                        ${move.type.replace('_', ' ')}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-right ${move.type === 'stock_in' ? 'text-green-700' : 'text-red-700'
            }">
                                    ${move.type === 'stock_in' ? '+' : '-'}${move.quantity.toLocaleString('en-PH', { minimumFractionDigits: 2 })}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 text-right">${move.stock_before.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 font-semibold text-right">${move.stock_after.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">${move.notes || '-'}</td>
                            </tr>
                        `).join('') : `
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                    No stock movements for this period
                                </td>
                            </tr>
                        `}
                    </tbody>
                </table>
            </div>
        </div>
    `;
    }

    // Expired Items Report
    function generateExpiredItemsReport(data) {
        const summary = data.summary;
        const expiredItems = data.expired_items;
        const expiringSoon = data.expiring_soon;

        return `
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-red-500 to-red-600 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">Expired Items</div>
                    <i class="fas fa-times-circle text-xl opacity-75"></i>
                </div>
                <div class="text-2xl font-bold mb-1">${summary.expired_count.toLocaleString()}</div>
                <div class="text-xs opacity-75 mt-1">${summary.expired_value.toLocaleString('en-PH', { style: 'currency', currency: 'PHP' })}</div>
            </div>
            
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">Expiring Soon</div>
                    <i class="fas fa-clock text-xl opacity-75"></i>
                </div>
                <div class="text-2xl font-bold mb-1">${summary.expiring_soon_count.toLocaleString()}</div>
                <div class="text-xs opacity-75 mt-1">Within 7 days</div>
            </div>
            
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">Total Waste</div>
                    <i class="fas fa-trash-alt text-xl opacity-75"></i>
                </div>
                <div class="text-2xl font-bold mb-1">${summary.total_waste_qty.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</div>
                <div class="text-xs opacity-75 mt-1">kg/L</div>
            </div>
            
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">Waste Value</div>
                    <i class="fas fa-money-bill-wave text-xl opacity-75"></i>
                </div>
                <div class="text-2xl font-bold mb-1">${summary.total_waste_value.toLocaleString('en-PH', { style: 'currency', currency: 'PHP' })}</div>
            </div>
        </div>
        
        <!-- Expiring Soon Alert -->
        ${expiringSoon.length > 0 ? `
        <div class="bg-orange-50 border-l-4 border-orange-500 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-orange-500 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-orange-800">Items Expiring Soon!</h3>
                    <div class="mt-2 text-sm text-orange-700">
                        <p>The following items will expire within 7 days. Please use or dispose of them accordingly:</p>
                        <ul class="list-disc list-inside mt-2">
                            ${expiringSoon.map(item => `<li><strong>${item.name}</strong> - Expires in ${item.days_until_expiry} days (${item.expiration_date})</li>`).join('')}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        ` : ''}
        
        <!-- Expired Items by Category -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                <h5 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                    <i class="fas fa-chart-pie mr-2 text-red-600"></i>
                    Waste by Category
                </h5>
                <div class="space-y-3">
                    ${summary.by_category.map(cat => `
                        <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-${cat.color}-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-${cat.icon} text-${cat.color}-600"></i>
                                </div>
                                <span class="font-medium text-gray-700 capitalize">${cat.name}</span>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-semibold text-gray-900">${cat.count} items</div>
                                <div class="text-xs text-gray-500">${cat.value.toLocaleString('en-PH', { style: 'currency', currency: 'PHP' })}</div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>

            <!-- Waste Trend -->
            <div class="bg-gradient-to-br from-red-500 to-pink-600 text-white rounded-lg p-5 shadow-md">
                <h5 class="text-lg font-semibold mb-4 flex items-center">
                    <i class="fas fa-chart-line mr-2"></i>
                    Waste Trend Analysis
                </h5>
                <div class="space-y-4">
                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm opacity-90">This Week</span>
                            <span class="font-bold">${summary.trend.this_week} items</span>
                        </div>
                        <div class="text-xs opacity-75">${summary.trend.this_week_value.toLocaleString('en-PH', { style: 'currency', currency: 'PHP' })}</div>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm opacity-90">This Month</span>
                            <span class="font-bold">${summary.trend.this_month} items</span>
                        </div>
                        <div class="text-xs opacity-75">${summary.trend.this_month_value.toLocaleString('en-PH', { style: 'currency', currency: 'PHP' })}</div>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm opacity-90">Average/Week</span>
                            <span class="font-bold">${summary.trend.avg_per_week.toLocaleString('en-PH', { minimumFractionDigits: 1 })} items</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Expired Items Table -->
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <h5 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                <i class="fas fa-calendar-times mr-2 text-red-600"></i>
                Expired Items Details
            </h5>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Ingredient</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Category</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-black uppercase tracking-wider">Batch ID</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">Quantity</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-black uppercase tracking-wider">Expired On</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-black uppercase tracking-wider">Days Expired</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">Value Lost</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        ${expiredItems.length > 0 ? expiredItems.map(item => `
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-900 font-medium">${item.name}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 capitalize">${item.category}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 text-center font-mono">#${item.batch_id}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 font-bold text-right">${item.quantity.toLocaleString('en-PH', { minimumFractionDigits: 2 })} ${item.unit}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 text-center">${item.expiration_date}</td>
                                <td class="px-4 py-3 text-sm text-center">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                        ${item.days_expired} days ago
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-red-700 font-semibold text-right">${item.value_lost.toLocaleString('en-PH', { style: 'currency', currency: 'PHP' })}</td>
                            </tr>
                        `).join('') : `
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-check-circle text-3xl text-green-500 mb-2 block"></i>
                                    No expired items for this period
                                </td>
                            </tr>
                        `}
                    </tbody>
                </table>
            </div>
        </div>
    `;
    }

    // Consumption Report
    function generateConsumptionReport(data) {
        const summary = data.summary;
        const consumptionData = data.consumption_data;
        const topConsumed = data.top_consumed;

        return `
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">Total Consumed</div>
                    <i class="fas fa-chart-line text-xl opacity-75"></i>
                </div>
                <div class="text-2xl font-bold mb-1">${summary.total_consumed.toLocaleString()}</div>
                <div class="text-xs opacity-75 mt-1">items used</div>
            </div>
            
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">Total Quantity</div>
                    <i class="fas fa-weight text-xl opacity-75"></i>
                </div>
                <div class="text-2xl font-bold mb-1">${summary.total_quantity.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</div>
                <div class="text-xs opacity-75 mt-1">kg/L</div>
            </div>
            
            <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">Total Value</div>
                    <i class="fas fa-dollar-sign text-xl opacity-75"></i>
                </div>
                <div class="text-2xl font-bold mb-1">${summary.total_value.toLocaleString('en-PH', { style: 'currency', currency: 'PHP' })}</div>
            </div>
            
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">Avg Daily Usage</div>
                    <i class="fas fa-calendar-day text-xl opacity-75"></i>
                </div>
                <div class="text-2xl font-bold mb-1">${summary.avg_daily.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</div>
                <div class="text-xs opacity-75 mt-1">kg/L per day</div>
            </div>
        </div>
        
        <!-- Top Consumed Items -->
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm mb-6">
            <h5 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                <i class="fas fa-trophy mr-2 text-yellow-600"></i>
                Top 10 Most Consumed Items
            </h5>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                ${topConsumed.map((item, index) => `
                    <div class="flex items-center p-3 bg-gradient-to-r from-${index === 0 ? 'yellow' : index === 1 ? 'gray' : index === 2 ? 'orange' : 'blue'
            }-50 to-white border border-gray-200 rounded-lg">
                        <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full bg-${index === 0 ? 'yellow' : index === 1 ? 'gray' : index === 2 ? 'orange' : 'blue'
            }-100 mr-3">
                            <span class="text-xl font-bold text-${index === 0 ? 'yellow' : index === 1 ? 'gray' : index === 2 ? 'orange' : 'blue'
            }-700">${index + 1}</span>
                        </div>
                        <div class="flex-grow">
                            <div class="font-semibold text-gray-900">${item.name}</div>
                            <div class="text-sm text-gray-600">
                                ${item.total_consumed.toLocaleString('en-PH', { minimumFractionDigits: 2 })} ${item.unit}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-gray-900">${item.usage_count} times</div>
                            <div class="text-xs text-gray-500">${item.value.toLocaleString('en-PH', { style: 'currency', currency: 'PHP' })}</div>
                        </div>
                    </div>
                `).join('')}
            </div>
        </div>

        <!-- Consumption by Category -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                <h5 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                    <i class="fas fa-layer-group mr-2 text-indigo-600"></i>
                    Consumption by Category
                </h5>
                <div class="space-y-3">
                    ${summary.by_category.map(cat => `
                        <div class="border-b border-gray-100 pb-3">
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-${cat.color}-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-${cat.icon} text-${cat.color}-600"></i>
                                    </div>
                                    <span class="font-medium text-gray-700 capitalize">${cat.name}</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">${cat.percentage}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-${cat.color}-500 h-2 rounded-full transition-all duration-500" style="width: ${cat.percentage}%"></div>
                            </div>
                            <div class="flex justify-between mt-1 text-xs text-gray-600">
                                <span>${cat.quantity.toLocaleString('en-PH', { minimumFractionDigits: 2 })} kg/L</span>
                                <span>${cat.value.toLocaleString('en-PH', { style: 'currency', currency: 'PHP' })}</span>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>

            <!-- Usage Trends -->
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-lg p-5 shadow-md">
                <h5 class="text-lg font-semibold mb-4 flex items-center">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Usage Trends
                </h5>
                <div class="space-y-4">
                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm opacity-90">Peak Usage Day</span>
                            <span class="font-bold">${summary.trends.peak_day}</span>
                        </div>
                        <div class="text-xs opacity-75">${summary.trends.peak_value.toLocaleString('en-PH', { minimumFractionDigits: 2 })} kg/L</div>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm opacity-90">Average/Day</span>
                            <span class="font-bold">${summary.trends.avg_per_day.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</span>
                        </div>
                        <div class="text-xs opacity-75">kg/L per day</div>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm opacity-90">Trend</span>
                            <span class="font-bold flex items-center">
                                ${summary.trends.direction === 'up' ?
                '<i class="fas fa-arrow-up mr-1"></i>Increasing' :
                '<i class="fas fa-arrow-down mr-1"></i>Decreasing'
            }
                            </span>
                        </div>
                        <div class="text-xs opacity-75">${summary.trends.change_percentage}% vs last period</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Consumption Details Table -->
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <h5 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                <i class="fas fa-table mr-2 text-indigo-600"></i>
                Detailed Consumption Log
            </h5>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Ingredient</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Category</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">Quantity Used</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Used For</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">Value</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        ${consumptionData.length > 0 ? consumptionData.map(item => `
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-900 font-medium">${item.date}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 font-medium">${item.ingredient}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 capitalize">${item.category}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 font-bold text-right">${item.quantity.toLocaleString('en-PH', { minimumFractionDigits: 2 })} ${item.unit}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    ${item.used_for ?
                    `<span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">${item.used_for}</span>` :
                    '<span class="text-gray-400">-</span>'
                }
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 font-semibold text-right">${item.value.toLocaleString('en-PH', { style: 'currency', currency: 'PHP' })}</td>
                            </tr>
                        `).join('') : `
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                    No consumption data for this period
                                </td>
                            </tr>
                        `}
                    </tbody>
                </table>
            </div>
        </div>
    `;
    }

    function generateMenuReport(data) {
        const summary = data.summary;
        const menuItems = data.menu_items;

        return `
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gradient-to-br from-cyan-500 to-blue-600 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">Total Items Sold</div>
                    <i class="fas fa-shopping-bag text-xl opacity-75"></i>
                </div>
                <div class="text-2xl font-bold mb-1">${summary.total_items_sold.toLocaleString()}</div>
            </div>
            
            <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">Gross Sales</div>
                    <i class="fas fa-dollar-sign text-xl opacity-75"></i>
                </div>
                <div class="text-2xl font-bold mb-1">₱${summary.total_revenue.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</div>
            </div>
            
            <div class="bg-gradient-to-br from-yellow-400 to-orange-500 text-white p-5 rounded-lg shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div class="text-sm font-medium opacity-90">Best Seller</div>
                </div>
                ${summary.best_selling ? `
                    <div class="text-lg font-bold mb-1">${summary.best_selling.name}</div>
                    <div class="flex justify-between text-sm opacity-90">
                        <span>${summary.best_selling.quantity} sold</span>
                        <span>₱${summary.best_selling.revenue.toLocaleString('en-PH', { maximumFractionDigits: 0 })}</span>
                    </div>
                ` : `
                    <div class="text-lg font-bold mb-1">No sales yet</div>
                `}
            </div>
        </div>
        
        <!-- Menu Performance Table -->
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <h5 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                <i class="fas fa-utensils mr-2 text-cyan-600"></i>
                Menu Performance
            </h5>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Rank</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Menu Item</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">Quantity Sold</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">Gross Sales</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        ${menuItems.length > 0 ? menuItems.map((item, index) => `
                            <tr class="hover:bg-gray-50 transition-colors ${item.quantity === 0 ? 'bg-gray-50 opacity-60' : ''}">
                                <td class="px-4 py-3 text-sm text-gray-900 font-medium">
                                    ${item.quantity > 0 ? `
                                        <div class="flex items-center">
                                            <span class="w-8 h-8 flex items-center justify-center rounded-full ${index === 0 ? 'bg-yellow-100 text-yellow-800' :
                    index === 1 ? 'bg-gray-200 text-gray-700' :
                        index === 2 ? 'bg-orange-100 text-orange-700' :
                            'bg-gray-100 text-gray-600'
                } font-bold text-xs">
                                                ${index + 1}
                                            </span>
                                            ${index === 0 ? '' : ''}
                                        </div>
                                    ` : `
                                        <span class="text-gray-400">-</span>
                                    `}
                                </td>
                                <td class="px-4 py-3 text-sm ${item.quantity === 0 ? 'text-gray-500' : 'text-gray-900 font-medium'}">
                                    ${item.menu_item}
                                    ${item.quantity === 0 ? '<span class="ml-2 text-xs text-red-500">(No sales)</span>' : ''}
                                </td>
                                <td class="px-4 py-3 text-sm text-right ${item.quantity === 0 ? 'text-gray-400' : 'text-gray-900 font-bold'}">
                                    ${item.quantity.toLocaleString()}
                                </td>
                                <td class="px-4 py-3 text-sm text-right ${item.quantity === 0 ? 'text-gray-400' : 'text-green-700 font-semibold'}">
                                    ₱${item.revenue.toLocaleString('en-PH', { minimumFractionDigits: 2 })}
                                </td>
                            </tr>
                        `).join('') : `
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                    No menu performance data for this period
                                </td>
                            </tr>
                        `}
                    </tbody>
                    ${menuItems.length > 0 && summary.total_items_sold > 0 ? `
                        <tfoot class="bg-gray-100 font-semibold">
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-sm text-gray-900">TOTAL</td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right">${summary.total_items_sold.toLocaleString()}</td>
                                <td class="px-4 py-3 text-sm text-green-700 text-right">₱${summary.total_revenue.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                            </tr>
                        </tfoot>
                    ` : ''}
                </table>
            </div>
        </div>
    `;
    }

    // Export to PDF Function
    function exportToPDF(reportType, button) {
        const originalHTML = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner mr-2"></i>Exporting...';

        let startDate, endDate;

        if (reportType === 'Sales Report') {
            startDate = document.getElementById('sales_start').value;
            endDate = document.getElementById('sales_end').value;
        } else if (reportType === 'Transaction Report') {
            startDate = document.getElementById('transaction_start').value;
            endDate = document.getElementById('transaction_end').value;
        } else if (reportType === 'Menu Performance Report') {
            startDate = document.getElementById('menu_start').value;
            endDate = document.getElementById('menu_end').value;
        } else if (reportType === 'Inventory Report') {
            startDate = document.getElementById('inventory_start').value;
            endDate = document.getElementById('inventory_end').value;
        }

        if (!startDate || !endDate) {
            showAlert('error', 'Please generate a report first');
            button.disabled = false;
            button.innerHTML = originalHTML;
            return;
        }

        if (reportType === 'Sales Report') {
            window.location.href = `/admin/reports/sales/pdf?start_date=${startDate}&end_date=${endDate}`;
        } else if (reportType === 'Transaction Report') {
            window.location.href = `/admin/reports/transaction/pdf?start_date=${startDate}&end_date=${endDate}`;
        } else if (reportType === 'Menu Performance Report') {
            window.location.href = `/admin/reports/menu/pdf?start_date=${startDate}&end_date=${endDate}`;
        }

        setTimeout(() => {
            button.disabled = false;
            button.innerHTML = originalHTML;
        }, 1500);
    }

    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg ${type === 'success' ? 'bg-green-500' : 'bg-red-500'
            } text-white font-medium`;
        alertDiv.style.animation = 'fadeIn 0.3s ease-in';
        alertDiv.textContent = message;

        document.body.appendChild(alertDiv);

        setTimeout(() => {
            alertDiv.style.opacity = '0';
            alertDiv.style.transition = 'opacity 0.3s ease';
            setTimeout(() => alertDiv.remove(), 300);
        }, 3000);
    }
</script>
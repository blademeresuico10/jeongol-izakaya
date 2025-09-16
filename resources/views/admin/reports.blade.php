<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    @vite('resources/css/app.css')
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

        .modal {
            max-height: 90vh;
            overflow-y: auto;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-radius: 50%;
            border-top: 3px solid #3498db;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="bg-gray-100">

    @include('admin.layouts.header')
    @include('admin.layouts.sidebar')
    @include('admin.reports.salestemplate')
    @include('admin.reports.revenuetemplate')
    @include('admin.reports.reservationtemplate')
    @include('admin.reports.stafftemplate')
    @include('admin.reports.stocktemplate')

    <div id="content-wrapper" class="flex flex-col min-h-screen bg-gray-50">
        <div id="content" class="flex-1">

            <nav class="bg-white shadow-sm border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-800">Reports</h1>
            </nav>

            <div class="container mx-auto mt-8 px-6">
                <div class="flex flex-col space-y-4 max-w-6xl mt-4">

                    <!-- Sales Reports -->
                    <div class="flex items-center justify-between bg-white p-4 rounded shadow">
                        <button data-open="sales"
                            class="flex items-center space-x-4 bg-transparent text-blue-600 py-2 px-3 rounded hover:bg-blue-600 hover:text-white transition">
                            <i class="fas fa-chart-line"></i>
                            <span>Sales Reports</span>
                        </button>
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-gray-700">Filter:</label>
                            <select data-filter="sales"
                                class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="daily">Today</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                    </div>

                    <!-- Revenue Report -->
                    <div class="flex items-center justify-between bg-white p-4 rounded shadow">
                        <button data-open="revenue"
                            class="flex items-center space-x-4 bg-transparent text-blue-600 py-2 px-3 rounded hover:bg-green-600 hover:text-white transition">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <span>Revenue Reports</span>
                        </button>
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-gray-700">Filter:</label>
                            <select data-filter="revenue"
                                class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="daily">Today</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                    </div>

                    <!-- Reservation Reports -->
                    <div class="flex items-center justify-between bg-white p-4 rounded shadow">
                        <button data-open="reservation"
                            class="flex items-center space-x-4 bg-transparent text-blue-600 py-2 px-3 rounded hover:bg-yellow-600 hover:text-white transition">
                            <i class="fas fa-book"></i>
                            <span>Reservation Reports</span>
                        </button>
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-gray-700">Filter:</label>
                            <select data-filter="reservation"
                                class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                <option value="daily">Today</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                    </div>

                    <!-- Staff Reports -->
                    <div class="flex items-center justify-between bg-white p-4 rounded shadow">
                        <button data-open="staff"
                            class="flex items-center space-x-4 bg-transparent text-blue-600 py-2 px-3 rounded hover:bg-purple-600 hover:text-white transition">
                            <i class="fas fa-user-tie"></i>
                            <span>Cashier Reports</span>
                        </button>
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-gray-700">Filter:</label>
                            <select data-filter="staff"
                                class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option value="daily">Today</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                    </div>

                    <!-- Stock Reports -->
                    <div class="flex items-center justify-between bg-white p-4 rounded shadow">
                        <button data-open="stock"
                            class="flex items-center space-x-4 bg-transparent text-blue-600 py-2 px-3 rounded hover:bg-red-600 hover:text-white transition">
                            <i class="fas fa-box"></i>
                            <span>Stock Reports</span>
                        </button>
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-gray-700">Filter:</label>
                            <select data-filter="stock"
                                class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="daily">Today</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Collect all modals
            const elements = {
                modals: {
                    sales: document.getElementById("salesModal"),
                    revenue: document.getElementById("revenueModal"),
                    reservation: document.getElementById("reservationModal"),
                    staff: document.getElementById("staffModal"),
                    stock: document.getElementById("stockModal"),
                }
            };

            // Print report
            const printReport = (name) => {
                const modal = elements.modals[name];
                if (!modal) return;

                const printContent = modal.querySelector('.print-area').innerHTML;
                const printWindow = window.open('', '', 'width=900,height=650');
                printWindow.document.write(`
            <html>
                <head>
                    <title>Print Report</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { border: 1px solid #000; padding: 8px; }
                        th { background: #f0f0f0; }
                    </style>
                </head>
                <body>
                    ${printContent}
                </body>
            </html>
        `);
                printWindow.document.close();
                printWindow.print();
            };

            // Export report as PDF
            const exportReport = (name) => {
                const modal = elements.modals[name];
                if (!modal) return;

                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();

                // Company Header
                doc.setFontSize(20);
                doc.setFont(undefined, 'bold');
                doc.text('JEONGOL RESTAURANT', 105, 20, { align: 'center' });
                doc.setFontSize(12);
                doc.setFont(undefined, 'normal');
                doc.text('Restaurant Management System', 105, 30, { align: 'center' });

                // Report Title
                const titles = {
                    'sales': 'SALES REPORT',
                    'revenue': 'REVENUE REPORT',
                    'reservation': 'RESERVATION REPORT',
                    'staff': 'CASHIER REPORT',
                    'stock': 'STOCK REPORT'
                };
                const reportTitle = titles[name] || 'REPORT';
                doc.setFontSize(16);
                doc.setFont(undefined, 'bold');
                doc.text(reportTitle, 105, 45, { align: 'center' });

                // Date & Filter
                const currentDate = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
                const filterElement = document.querySelector(`[data-filter="${name}"]`);
                const selectedFilter = filterElement ? filterElement.options[filterElement.selectedIndex].text : "N/A";

                doc.setFontSize(10);
                doc.setFont(undefined, 'normal');
                doc.text(`Generated on: ${currentDate}`, 105, 55, { align: 'center' });
                doc.text(`For: ${selectedFilter}`, 105, 62, { align: 'center' });

                // Line separator
                doc.line(20, 67, 190, 67);

                let yPosition = 75;
                const tables = modal.querySelectorAll('table');

                tables.forEach((table, index) => {
                    if (index > 0) yPosition += 10;

                    const headers = [];
                    const rows = [];
                    table.querySelectorAll('thead th').forEach(cell => headers.push(cell.textContent.trim()));

                    table.querySelectorAll('tbody tr').forEach(row => {
                        const rowData = [];
                        row.querySelectorAll('td').forEach(cell => {
                            let cellText = cell.textContent.trim();
                            cellText = cellText.replace(/₱/g, 'PHP ');
                            rowData.push(cellText);
                        });
                        if (rowData.length > 0) rows.push(rowData);
                    });

                    if (headers.length > 0 && rows.length > 0) {
                        doc.autoTable({
                            head: [headers],
                            body: rows,
                            startY: yPosition,
                            theme: 'grid',
                            headStyles: { fillColor: [240, 240, 240], textColor: [0, 0, 0], fontStyle: 'bold' },
                            bodyStyles: { textColor: [0, 0, 0] },
                            alternateRowStyles: { fillColor: [248, 248, 248] },
                            margin: { left: 20, right: 20 },
                            styles: { fontSize: 10, cellPadding: 5 }
                        });
                        yPosition = doc.lastAutoTable.finalY + 10;
                    }
                });

                const pageCount = doc.internal.getNumberOfPages();
                for (let i = 1; i <= pageCount; i++) {
                    doc.setPage(i);
                    doc.setFontSize(8);
                    doc.setFont(undefined, 'normal');
                    doc.text(`Page ${i} of ${pageCount}`, 105, 285, { align: 'center' });
                    doc.text('Generated by Restaurant Management System', 105, 290, { align: 'center' });
                }

                doc.save(`${name}_report_${new Date().toISOString().split('T')[0]}.pdf`);
            };


            document.querySelectorAll('[data-print]').forEach(btn => {
                btn.addEventListener('click', () => printReport(btn.dataset.print));
            });
            document.querySelectorAll('[data-export]').forEach(btn => {
                btn.addEventListener('click', () => exportReport(btn.dataset.export));
            });
        });
    </script>



</body>

</html>
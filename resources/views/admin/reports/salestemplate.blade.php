<div id="salesModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl">
        <div class="flex justify-between items-center border-b px-6 py-3">
            <h2 class="text-xl font-semibold">Sales Report</h2>
            <button data-close="sales" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>

        <div class="p-6 space-y-6 print-area" id="salesReportContent">
            <!-- Sales Summary Table -->
            <table class="w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-1">Metric</th>
                        <th class="border px-3 py-1">Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border px-3 py-1">Total Sales</td>
                        <td class="border px-3 py-1" id="totalSales"></td>
                    </tr>
                    <tr>
                        <td class="border px-3 py-1">Transaction Count</td>
                        <td class="border px-3 py-1" id="transactionCount"></td>
                    </tr>
                    <tr>
                        <td class="border px-3 py-1">Average Order Value</td>
                        <td class="border px-3 py-1" id="averageOrderValue"></td>
                    </tr>
                </tbody>
            </table>

            <!-- Top Selling Items Table -->
            <table class="w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-1">Item Name</th>
                        <th class="border px-3 py-1">Quantity Sold</th>
                    </tr>
                </thead>
                <tbody id="topSellingItems"></tbody>
            </table>
        </div>

        <div class="flex justify-end gap-3 border-t px-6 py-3">
            <button data-export="sales" class="bg-green-600 text-white px-4 py-2 rounded">Export</button>
            <button data-close="sales" class="bg-gray-500 text-white px-4 py-2 rounded">Close</button>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {

    function loadSalesData(filterValue) {
    // Show loading
    const totalSalesTd = document.getElementById("totalSales");
    const transactionCountTd = document.getElementById("transactionCount");
    const averageOrderValueTd = document.getElementById("averageOrderValue");
    const topSellingItemsTbody = document.getElementById("topSellingItems");

    totalSalesTd.textContent = "Loading...";
    transactionCountTd.textContent = "Loading...";
    averageOrderValueTd.textContent = "Loading...";
    topSellingItemsTbody.innerHTML = `<tr><td colspan="2" class="text-center">Loading...</td></tr>`;

    fetch(`/reports/sales?filter=${filterValue}`)
        .then(res => res.json())
        .then(data => {
            totalSalesTd.textContent = "₱" + parseFloat(data.totalSales || 0).toFixed(2);
            transactionCountTd.textContent = data.transactionCount || 0;
            averageOrderValueTd.textContent = "₱" + parseFloat(data.averageOrderValue || 0).toFixed(2);

            const topItemsRows = (data.topSellingItems || []).map(item => `
                <tr>
                    <td class="border px-3 py-1">${item.item_name}</td>
                    <td class="border px-3 py-1">${item.total_quantity}</td>
                </tr>
            `).join("");

            topSellingItemsTbody.innerHTML = topItemsRows || 
                '<tr><td colspan="2" class="border px-3 py-1 text-center">No data available</td></tr>';
        })
        .catch(error => {
            console.error('Error:', error);
            totalSalesTd.textContent = "-";
            transactionCountTd.textContent = "-";
            averageOrderValueTd.textContent = "-";
            topSellingItemsTbody.innerHTML = `<tr><td colspan="2" class="text-red-500 text-center">Failed to load sales data.</td></tr>`;
        });
}


    // Open modal
    document.querySelector('[data-open="sales"]').addEventListener("click", () => {
        const modal = document.getElementById("salesModal");
        modal.classList.remove("hidden");

        // Get filter from the landing page
        const filterValue = document.querySelector('[data-filter="sales"]').value;
        loadSalesData(filterValue);
    });

    // Reload modal if filter changes while open
    document.querySelector('[data-filter="sales"]').addEventListener("change", (e) => {
        const modal = document.getElementById("salesModal");
        if (!modal.classList.contains("hidden")) {
            loadSalesData(e.target.value);
        }
    });

    // Close modal
    document.querySelectorAll('[data-close="sales"]').forEach(btn => {
        btn.addEventListener("click", () => {
            document.getElementById("salesModal").classList.add("hidden");
        });
    });

});
</script>

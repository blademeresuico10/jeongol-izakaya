<div id="revenueModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl">
        <div class="flex justify-between items-center border-b px-6 py-3">
            <h2 class="text-xl font-semibold">Revenue Report</h2>
            <button data-close="revenue" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>

        <div class="p-6 space-y-6 print-area" id="revenueReportContent">
            <!-- Revenue Summary Table -->
            <table class="w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-1">Metric</th>
                        <th class="border px-3 py-1">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border px-3 py-1">Total Revenue</td>
                        <td class="border px-3 py-1" id="revenueTotal"></td>
                    </tr>
                    <tr>
                        <td class="border px-3 py-1">Total Discounts</td>
                        <td class="border px-3 py-1" id="revenueDiscounts"></td>
                    </tr>
                    <tr>
                        <td class="border px-3 py-1">Net Revenue</td>
                        <td class="border px-3 py-1" id="revenueNet"></td>
                    </tr>
                </tbody>
            </table>

            <!-- Revenue by Category -->
            <table class="w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-1">Category</th>
                        <th class="border px-3 py-1">Revenue</th>
                        <th class="border px-3 py-1">Items Sold</th>
                    </tr>
                </thead>
                <tbody id="revenueByCategory"></tbody>
            </table>
        </div>

        <div class="flex justify-end gap-3 border-t px-6 py-3">
            <button data-export="revenue" class="bg-green-600 text-white px-4 py-2 rounded">Export</button>
            <button data-close="revenue" class="bg-gray-500 text-white px-4 py-2 rounded">Close</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const loadRevenue = (filterValue) => {
            const modal = document.getElementById("revenueModal");
            fetch(`/reports/revenue?filter=${filterValue}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById("revenueTotal").textContent = "₱" + parseFloat(data.grossRevenue).toFixed(2);
                    document.getElementById("revenueDiscounts").textContent = "₱" + parseFloat(data.discounts).toFixed(2);
                    document.getElementById("revenueNet").textContent = "₱" + parseFloat(data.netRevenue).toFixed(2);

                    let categoryRows = data.byCategory.map(cat => `
                    <tr>
                        <td class="border px-3 py-1">${cat.category}</td>
                        <td class="border px-3 py-1">₱${parseFloat(cat.revenue).toFixed(2)}</td>
                        <td class="border px-3 py-1">${cat.items_sold}</td>
                    </tr>
                `).join("");

                    document.getElementById("revenueByCategory").innerHTML = categoryRows;
                })
                .catch(() => {
                    document.getElementById("revenueReportContent").innerHTML =
                        `<p class="text-red-500">Failed to load revenue data.</p>`;
                });
        }

        document.querySelector('[data-open="revenue"]').addEventListener("click", () => {
            const filterValue = document.querySelector('[data-filter="revenue"]').value;
            document.getElementById("revenueModal").classList.remove("hidden");
            loadRevenue(filterValue);
        });

        document.querySelector('[data-filter="revenue"]').addEventListener("change", (e) => {
            const modal = document.getElementById("revenueModal");
            if (!modal.classList.contains("hidden")) {
                loadRevenue(e.target.value);
            }
        });

        document.querySelectorAll('[data-close="revenue"]').forEach(btn => {
            btn.addEventListener("click", () => {
                document.getElementById("revenueModal").classList.add("hidden");
            });
        });
    });

</script>
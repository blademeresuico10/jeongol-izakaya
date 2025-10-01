<!-- Staff Report Modal -->
<div id="staffModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl">
        <div class="flex justify-between items-center border-b px-6 py-3">
            <h2 class="text-xl font-semibold">Cashier Transaction</h2>
            <button data-close="staff" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>

        <div class="p-6 space-y-6 print-area" id="staffReportContent">
            <table class="w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-1">Cashier</th>
                        <th class="border px-3 py-1">Transactions</th>
                        <th class="border px-3 py-1">Total Sales</th>
                    </tr>
                </thead>
                <tbody id="staffCashierPerformance"></tbody>
            </table>
        </div>

        <div class="flex justify-end gap-3 border-t px-6 py-3">
            <button data-export="staff" class="bg-green-600 text-white px-4 py-2 rounded">Export</button>
            <button data-close="staff" class="bg-gray-500 text-white px-4 py-2 rounded">Close</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const staffButton = document.querySelector('[data-open="staff"]');
        const staffFilter = document.querySelector('[data-filter="staff"]');
        const staffModal = document.getElementById("staffModal");

        const loadStaffReport = (filterValue) => {
            fetch(`/reports/staff?filter=${filterValue}`)
                .then(res => res.json())
                .then(data => {
                    const tbody = document.getElementById("staffCashierPerformance");
                    tbody.innerHTML = data.cashierPerformance.map(row => `
                    <tr>
                        <td class="border px-3 py-1">${row.cashier_name}</td>
                        <td class="border px-3 py-1">${row.transactions}</td>
                        <td class="border px-3 py-1">${parseFloat(row.total_sales).toFixed(2)}</td>
                    </tr>
                `).join("");
                })
                .catch(() => {
                    document.getElementById("staffReportContent").innerHTML =
                        `<p class="text-red-500">Failed to load staff data.</p>`;
                });
        };

        if (staffButton) {
            staffButton.addEventListener("click", () => {
                staffModal.classList.remove("hidden");
                const filterValue = staffFilter.value;
                loadStaffReport(filterValue);
            });
        }

        if (staffFilter) {
            staffFilter.addEventListener("change", (e) => {
                if (!staffModal.classList.contains("hidden")) {
                    loadStaffReport(e.target.value);
                }
            });
        }

        document.querySelectorAll('[data-close="staff"]').forEach(btn => {
            btn.addEventListener("click", () => {
                staffModal.classList.add("hidden");
            });
        });
    });

</script>
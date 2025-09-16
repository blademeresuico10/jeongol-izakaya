<div id="stockModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl">
        <div class="flex justify-between items-center border-b px-6 py-3">
            <h2 class="text-xl font-semibold">Stock Report</h2>
            <button data-close="stock" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>

        <div class="p-6 space-y-6 print-area" id="stockReportContent">
            <!-- Current Stock Levels -->
            <table class="w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-1">Item</th>
                        <th class="border px-3 py-1">Quantity</th>
                        <th class="border px-3 py-1">Remaining Stock</th>
                        <th class="border px-3 py-1">Updated</th>
                    </tr>
                </thead>
                <tbody id="stockLevels"></tbody>
            </table>
        </div>

        <div class="flex justify-end gap-3 border-t px-6 py-3">
            <button data-export="stock" class="bg-green-600 text-white px-4 py-2 rounded">Export</button>
            <button data-close="stock" class="bg-gray-500 text-white px-4 py-2 rounded">Close</button>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        document.querySelector('[data-open="stock"]').addEventListener("click", () => {
            const modal = document.getElementById("stockModal");
            modal.classList.remove("hidden");

            fetch("{{ route('reports.stock') }}")
                .then(res => res.json())
                .then(data => {
                    const tbody = document.getElementById("stockLevels");
                    tbody.innerHTML = data.stockData.map(item => `
                    <tr>
                        <td class="border px-3 py-1">${item.stock_name}</td>
                        <td class="border px-3 py-1">${item.initial_stock}</td>
                        <td class="border px-3 py-1">${item.remaining_stock}</td>
                        <td class="border px-3 py-1">${new Date(item.updated_at).toLocaleString()}</td>
                    </tr>
                `).join("");
                })
                .catch(() => {
                    document.getElementById("stockReportContent").innerHTML =
                        `<p class="text-red-500">Failed to load stock data.</p>`;
                });
        });

        document.querySelectorAll('[data-close="stock"]').forEach(btn => {
            btn.addEventListener("click", () => {
                document.getElementById("stockModal").classList.add("hidden");
            });
        });
    });
</script>
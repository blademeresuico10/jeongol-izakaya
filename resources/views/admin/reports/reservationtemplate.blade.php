<div id="reservationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl">
        <div class="flex justify-between items-center border-b px-6 py-3">
            <h2 class="text-xl font-semibold">Reservation Report</h2>
            <button data-close="reservation" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>

        <div class="p-6 space-y-6 print-area" id="reservationReportContent">
            <!-- Summary Table -->
            <table class="w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-1">Metric</th>
                        <th class="border px-3 py-1">Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border px-3 py-1">Total Reservations</td>
                        <td class="border px-3 py-1" id="reservationsTotal"></td>
                    </tr>
                    <tr>
                        <td class="border px-3 py-1">Total Pax</td>
                        <td class="border px-3 py-1" id="reservationsPax"></td>
                    </tr>
                </tbody>
            </table>

            <!-- Reservations by Status -->
            <table class="w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-1">Status</th>
                        <th class="border px-3 py-1">Count</th>
                        <th class="border px-3 py-1">Total Pax</th>
                    </tr>
                </thead>
                <tbody id="reservationsByStatus"></tbody>
            </table>
        </div>

        <div class="flex justify-end gap-3 border-t px-6 py-3">
            <button data-export="reservation" class="bg-green-600 text-white px-4 py-2 rounded">Export</button>
            <button data-close="reservation" class="bg-gray-500 text-white px-4 py-2 rounded">Close</button>
        </div>
    </div>
</div>

<script>
   document.addEventListener("DOMContentLoaded", () => {
    const loadReservations = (filterValue) => {
        const modal = document.getElementById("reservationModal");
        fetch(`/reports/reservations?filter=${filterValue}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById("reservationsTotal").textContent = data.totalReservations;
                document.getElementById("reservationsPax").textContent = data.totalPax;

                // Show Rejected and Cancelled if exists
                const rejectedInfo = document.getElementById("rejectedReservations");
                if (rejectedInfo) rejectedInfo.textContent = data.rejectedReservations;

                const cancelledInfo = document.getElementById("cancelledReservations");
                if (cancelledInfo) cancelledInfo.textContent = data.cancelledReservations;

                // Populate status table
                let statusRows = data.statusCounts.map(row => `
                    <tr>
                        <td class="border px-3 py-1">${row.status}</td>
                        <td class="border px-3 py-1">${row.count}</td>
                        <td class="border px-3 py-1">${row.total_pax}</td>
                    </tr>
                `).join("");

                document.getElementById("reservationsByStatus").innerHTML = statusRows;
            })
            .catch(() => {
                document.getElementById("reservationReportContent").innerHTML =
                    `<p class="text-red-500">Failed to load reservation data.</p>`;
            });
    }

    document.querySelector('[data-open="reservation"]').addEventListener("click", () => {
        const filterValue = document.querySelector('[data-filter="reservation"]').value;
        document.getElementById("reservationModal").classList.remove("hidden");
        loadReservations(filterValue);
    });

    document.querySelector('[data-filter="reservation"]').addEventListener("change", (e) => {
        const modal = document.getElementById("reservationModal");
        if (!modal.classList.contains("hidden")) {
            loadReservations(e.target.value);
        }
    });

    document.querySelectorAll('[data-close="reservation"]').forEach(btn => {
        btn.addEventListener("click", () => {
            document.getElementById("reservationModal").classList.add("hidden");
        });
    });
});


</script>
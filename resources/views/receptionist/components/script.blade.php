<script>
const modal = document.getElementById('tableModal');
const closeModal = document.getElementById('closeModal');
const tableLinks = document.querySelectorAll('.table-link');
const menuCheckboxes = document.querySelectorAll('.menu-item');
const specifyOrdersDiv = document.getElementById('specifyOrders');
const submitToCashierBtn = document.getElementById('submitToCashierBtn');
const arrivalTimeInput = document.getElementById('arrivalTimeInput');
const reserved_date = document.getElementById('reserved_date');
let selectedTableId = null;

const fullMenuPrices = @json($menuPricesMap);
let menuPrices = {};

function updateMenuPrices() {
    const [hours, minutes] = (arrivalTimeInput.value || "00:00").split(':').map(Number);
    const time = hours * 60 + minutes;
    const isLunch = time <= 900;

    menuPrices = {};
    for (const item in fullMenuPrices) {
        let lunch = fullMenuPrices[item].lunch;
        let dinner = fullMenuPrices[item].dinner;
        if (lunch == null) lunch = dinner;
        if (dinner == null) dinner = lunch;
        menuPrices[item] = isLunch ? lunch : dinner;
    }
}

function updateSpecifyOrders() {
    const old = {};
    specifyOrdersDiv.querySelectorAll('input[type="number"]').forEach(input => {
        old[input.name] = input.value;
    });

    specifyOrdersDiv.innerHTML = '';
    menuCheckboxes.forEach(cb => {
        if (cb.checked) {
            const val = old[cb.value] || 1;
            specifyOrdersDiv.innerHTML += `
                <label>${cb.value}
                    <input type="number" name="${cb.value}" min="1" value="${val}" style="width:50px;" 
                        onchange="calculateTotal()" oninput="calculateTotal()">
                </label><br>`;
        }
    });
    calculateTotal();
}

function calculateTotal() {
    let total = 0;
    menuCheckboxes.forEach(cb => {
        if (cb.checked) {
            const qty = parseInt(document.querySelector(`input[name="${cb.value}"]`).value || "0");
            let price = menuPrices[cb.value];
            if (price == null) {
                const prices = fullMenuPrices[cb.value];
                price = prices?.lunch ?? prices?.dinner ?? 0;
            }
            total += qty * parseFloat(price);
        }
    });
    document.getElementById('total').textContent = total.toFixed(2);
}

// modal open/close
tableLinks.forEach(link => {
    link.addEventListener('click', e => {
        e.preventDefault();
        tableLinks.forEach(l => l.classList.remove('selected'));
        link.classList.add('selected');
        selectedTableId = link.getAttribute('data-table-id');
        modal.style.display = 'flex';
        menuCheckboxes.forEach(cb => cb.checked = false);
        updateSpecifyOrders();
    });
});
closeModal.onclick = () => modal.style.display = 'none';
window.onclick = e => { if (e.target === modal) modal.style.display = 'none'; };

// event handlers
arrivalTimeInput.addEventListener('input', () => {
    updateMenuPrices();
    updateSpecifyOrders();
});
menuCheckboxes.forEach(cb => cb.addEventListener('change', updateSpecifyOrders));

// submit
submitToCashierBtn.addEventListener('click', () => {
    if (submitToCashierBtn.disabled) return; // already processing
    submitToCashierBtn.disabled = true;
    submitToCashierBtn.textContent = "Submitting...";

    const data = {
        customer_name: document.getElementById('customerName').value.trim(),
        pax: document.getElementById('numberOfPax').value.trim(),
        reserved_date: reserved_date.value,
        arrival_time: arrivalTimeInput.value,
        table_id: selectedTableId,
        advance_payment: document.getElementById('advance_payment').value.trim(),
        orders: []
    };

    menuCheckboxes.forEach(cb => {
        if (cb.checked) {
            const qty = parseInt(document.querySelector(`input[name="${cb.value}"]`).value || "0");
            if (qty > 0) {
                data.orders.push({
                    item: cb.value,
                    qty,
                    notes: document.getElementById('customerNotes').value.trim()
                });
            }

        }
    });

    fetch("{{ route('receptionist.storeReservation') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(response => {
        if (response.success) {
            alert("Reservation submitted!");
            modal.style.display = 'none';
            location.reload();
        } else {
            alert(response.message || "Failed to save reservation.");
            submitToCashierBtn.disabled = false;
            submitToCashierBtn.textContent = "Submit to cashier";
        }
    })
    .catch(() => {
        alert("An error occurred.");
        submitToCashierBtn.disabled = false;
        submitToCashierBtn.textContent = "Submit to cashier";
    });
});


// time clock
setInterval(() => {
    const now = new Date().toLocaleString("en-PH", { timeZone: "Asia/Manila", hour: "2-digit", minute: "2-digit", second: "2-digit", hour12: true });
    document.getElementById("manilaTimeDisplay").textContent = `Current Time: ${now}`;
}, 1000);
document.getElementById('reserved_date').value = new Date().toISOString().substring(0, 10);
</script>

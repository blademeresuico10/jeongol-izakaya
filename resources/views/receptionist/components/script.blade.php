<script>
    
const modal = document.getElementById('tableModal');
const closeModal = document.getElementById('closeModal');
const tableLinks = document.querySelectorAll('.table-link');
const menuCheckboxes = document.querySelectorAll('.menu-item');
const specifyOrdersDiv = document.getElementById('specifyOrders');
const submitBtn = document.getElementById('submitBtn');
const arrivalTimeInput = document.getElementById('arrivalTimeInput');
const reserved_date = document.getElementById('reserved_date');
let selectedTableId = null;
let isPlacingOrder = false;

const fullMenuPrices = @json($menuPricesMap);
let menuPrices = {};

function updateMenuPrices() {
    let [hours, minutes] = (arrivalTimeInput.value || "00:00").split(':').map(Number);
    let time = hours * 60 + minutes;

    const isLunch = time < 960; // before 4:00 PM
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
        document.querySelectorAll('.inline-options').forEach(opt => opt.style.display = 'none');
        link.querySelector('.inline-options').style.display = 'flex';
        selectedTableId = link.getAttribute('data-table-id');
    });
});

closeModal.onclick = () => modal.style.display = 'none';
window.onclick = e => { if (e.target === modal) modal.style.display = 'none'; };

// event handlers
arrivalTimeInput.addEventListener('input', () => {
    updateMenuPrices();
    updateSpecifyOrders();
    updateTimeFrameDisplay();
});
reserved_date.addEventListener('input', updateTimeFrameDisplay);
menuCheckboxes.forEach(cb => cb.addEventListener('change', updateSpecifyOrders));

// submit with validation
submitBtn.addEventListener('click', () => {
    if (submitBtn.disabled) return;

    const [hours, minutes] = (arrivalTimeInput.value || "00:00").split(':').map(Number);
    const timeInMinutes = hours * 60 + minutes;

    let minTime = 690; // 11:30 AM
    let maxTime = isPlacingOrder ? 1200 : 1080; // 8:00 PM vs 6:00 PM

    if (timeInMinutes < minTime || timeInMinutes > maxTime) {
        alert(`Invalid time chosen. Please select a time between 11:30 AM and ${isPlacingOrder ? "8:00 PM" : "6:00 PM"}.`);
        return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = "Submitting...";

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
            submitBtn.disabled = false;
            submitBtn.textContent = "Submit to cashier";
        }
    })
    .catch(() => {
        alert("An error occurred.");
        submitBtn.disabled = false;
        submitBtn.textContent = "Submit to cashier";
    });
});

function updateTimeFrameDisplay() {
    const arrivalTime = arrivalTimeInput.value;
    const dateVal = reserved_date.value;
    const timeFrame = document.getElementById('timeFrameDisplay');

    if (!arrivalTime || !dateVal) {
        timeFrame.textContent = '';
        return;
    }

    const [hours, minutes] = arrivalTime.split(':').map(Number);
    const start = new Date(dateVal);
    start.setHours(hours, minutes, 0, 0);

    const end = new Date(start);
    end.setHours(end.getHours() + 2);

    timeFrame.textContent = `${start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })} - ${end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`;
}

// time clock
setInterval(() => {
    const now = new Date().toLocaleString("en-PH", { timeZone: "Asia/Manila", hour: "2-digit", minute: "2-digit", second: "2-digit", hour12: true });
    document.getElementById("manilaTimeDisplay").textContent = `Current Time: ${now}`;
}, 1000);
document.getElementById('reserved_date').value = new Date().toISOString().substring(0, 10);

// inline options show on table click
document.querySelectorAll('.table-link').forEach(link => {
    link.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('.inline-options').forEach(opt => opt.style.display = 'none');
        const options = link.querySelector('.inline-options');
        if (options) options.style.display = 'block';
    });
});

function makeOrder(tableId) {
    selectedTableId = tableId;
    isPlacingOrder = true;
    modal.style.display = 'flex';

    const now = new Date();
    document.getElementById('reserved_date').value = now.toISOString().substring(0, 10);
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    document.getElementById('arrivalTimeInput').value = `${hours}:${minutes}`;

    document.getElementById('reserved_date').disabled = true;
    document.getElementById('arrivalTimeInput').disabled = true;
    document.getElementById('advance_payment').parentElement.style.display = 'none';

    menuCheckboxes.forEach(cb => cb.checked = false);
    updateMenuPrices();
    updateSpecifyOrders();
    updateTimeFrameDisplay();
}

function makeReservation(tableId) {
    selectedTableId = tableId;
    isPlacingOrder = false;
    modal.style.display = 'flex';

    document.getElementById('reserved_date').disabled = false;
    document.getElementById('arrivalTimeInput').disabled = false;

    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    document.getElementById('arrivalTimeInput').value = `${hours}:${minutes}`;
    document.getElementById('reserved_date').value = now.toISOString().substring(0, 10);
    document.getElementById('advance_payment').parentElement.style.display = '';

    updateMenuPrices();
    updateSpecifyOrders();
    updateTimeFrameDisplay();
}
</script>

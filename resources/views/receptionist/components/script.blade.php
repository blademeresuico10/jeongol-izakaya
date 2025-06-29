<script>
const modal = document.getElementById('tableModal');
const closeModal = document.getElementById('closeModal');
const tableLinks = document.querySelectorAll('.table-link');
const menuCheckboxes = document.querySelectorAll('.menu-item');
const specifyOrdersDiv = document.getElementById('specifyOrders');
const advance_payment = document.getElementById('advance_payment');
const submitToCashierBtn = document.getElementById('submitToCashierBtn');
const reserved_date = document.getElementById('reserved_date');
const arrivalTimeInput = document.getElementById('arrivalTimeInput');
const timeFrameDisplay = document.getElementById('timeFrameDisplay');
let selectedTableId = null;


// DATA FROM SERVER
const fullMenuPrices = @json($menuPricesMap);
let menuPrices = {};

const reservations = @json($reservations);

// TIME FUNCTIONS
function formatTime12Hour(date) {
    let hours = date.getHours();
    const minutes = date.getMinutes().toString().padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12 || 12;
    return `${hours}:${minutes} ${ampm}`;
}

function getReservationTimeFrame12Hour(startTimeStr) {
    const [hours, minutes] = startTimeStr.split(':').map(Number);
    const start = new Date();
    start.setHours(hours, minutes, 0, 0);
    const end = new Date(start.getTime() + 2 * 60 * 60 * 1000);
    return `${formatTime12Hour(start)} - ${formatTime12Hour(end)}`;
}

function isTimeWithinAllowedRange(timeStr) {
    const [hours, minutes] = timeStr.split(':').map(Number);
    const start = new Date();
    start.setHours(hours, minutes, 0, 0);
    const reservationEnd = new Date(start.getTime() + 2 * 60 * 60 * 1000);

    const openTime = new Date(); openTime.setHours(10, 0, 0, 0);
    const lastStart = new Date(); lastStart.setHours(18, 0, 0, 0);
    const closeTime = new Date(); closeTime.setHours(20, 0, 0, 0);

    return start >= openTime && start <= lastStart && reservationEnd <= closeTime;
}


// MENU HANDLING

function updateMenuPricesBasedOnTime() {
    const timeStr = arrivalTimeInput.value;
    if (!timeStr) return;

    const [hours, minutes] = timeStr.split(':').map(Number);
    const time = hours * 60 + minutes;

    const isLunch = time <= 900; 
    menuPrices = {};
    for (const key in fullMenuPrices) {
        menuPrices[key] = isLunch ? fullMenuPrices[key].lunch : fullMenuPrices[key].dinner;
    }
    updateSpecifyOrders();
}

function updateSpecifyOrders() {
    const previousValues = {};
    specifyOrdersDiv.querySelectorAll('input[type="number"]').forEach(input => {
        previousValues[input.name] = input.value;
    });

    specifyOrdersDiv.innerHTML = '';
    menuCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            const savedValue = previousValues[checkbox.value] || 1;
            specifyOrdersDiv.innerHTML += `
                <label>${checkbox.value}
                    <input type="number" name="${checkbox.value}" min="1" value="${savedValue}" style="width:50px;"
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
            const price = parseFloat(menuPrices[cb.value]) || 0;
            total += qty * price;
        }
    });
    document.getElementById('total').textContent = total.toFixed(2);
}


// Time updates
arrivalTimeInput.addEventListener('input', () => {
    const selectedTime = arrivalTimeInput.value;
    timeFrameDisplay.textContent = selectedTime ? getReservationTimeFrame12Hour(selectedTime) : '';
    updateMenuPricesBasedOnTime();
});
reserved_date.addEventListener('change', () => arrivalTimeInput.dispatchEvent(new Event('input')));

// Menu item checkbox change
menuCheckboxes.forEach(cb => cb.addEventListener('change', updateSpecifyOrders));

// Modal open
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

// Modal close
closeModal.onclick = () => modal.style.display = 'none';
window.onclick = e => { if (e.target === modal) modal.style.display = 'none'; };


// SUBMIT RESERVATION
submitToCashierBtn.addEventListener('click', () => {
    const customerName = document.getElementById('customerName').value.trim();
    const numberOfPax = document.getElementById('numberOfPax').value.trim();
    const reservedDate = reserved_date.value;
    const arrivalTime = arrivalTimeInput.value;
    const notes = document.getElementById('customerNotes').value.trim();

    if (!customerName || !arrivalTime || !reservedDate || !selectedTableId || !advance_payment.value.trim()) {
        alert("Please complete the form.");
        return;
    }
    if (numberOfPax <= 0) {
        alert("Pax must not be 0.");
        return;
    }
    if (!isTimeWithinAllowedRange(arrivalTime)) {
        alert("The time reservation is not acceptable!");
        return;
    }

    const orders = [];
    menuCheckboxes.forEach(cb => {
        if (cb.checked) {
            const qty = parseInt(document.querySelector(`input[name="${cb.value}"]`).value || "0");
            if (qty > 0) orders.push({ item: cb.value, qty });
        }
    });

    const data = {
        customer_name: customerName,
        pax: numberOfPax,
        reserved_date: reservedDate,
        arrival_time: arrivalTime,
        notes,
        orders,
        table_id: selectedTableId,
        advance_payment: advance_payment.value.trim()
    };

    fetch("{{ route('receptionist.storeReservation') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(response => {
        if (response.success) {
            alert("Reservation submitted!");
            modal.style.display = 'none';
            location.reload();
        } else {
            alert(response.message || "Time reservation not available!");
        }
    })
    .catch(error => {
        console.error(error);
        alert("An error occurred.");
    });
});


// MANILA TIME CLOCK
function updateManilaTime() {
    const now = new Date().toLocaleString("en-PH", {
        timeZone: "Asia/Manila",
        hour: "2-digit", minute: "2-digit", second: "2-digit",
        hour12: true,
    });
    document.getElementById("manilaTimeDisplay").textContent = `Current Time: ${now}`;
}
document.getElementById('reserved_date').value = new Date().toISOString().substring(0, 10);
setInterval(updateManilaTime, 1000);
updateManilaTime();


// FETCH AVAILABLE TIMES
function fetchAvailableTimes() {
    const date = reserved_date.value;
    const tableNumber = selectedTableId;
    if (!date || !tableNumber) return;

    fetch(`/receptionist/available-times?date=${date}&table_number=${tableNumber}`)
        .then(res => res.json())
        .then(times => {
            timeFrameDisplay.textContent = times.length === 0
                ? 'No available times'
                : 'Available: ' + times.join(', ');
        })
        .catch(() => timeFrameDisplay.textContent = '');
}
reserved_date.addEventListener('change', fetchAvailableTimes);




</script>

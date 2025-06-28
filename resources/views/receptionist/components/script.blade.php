<script>
    const modal = document.getElementById('tableModal');
    const closeModal = document.getElementById('closeModal');
    const tableLinks = document.querySelectorAll('.table-link');
    const menuCheckboxes = document.querySelectorAll('.menu-item');
    const specifyOrdersDiv = document.getElementById('specifyOrders');
    const advance_payment = document.getElementById('advance_payment');
    const submitBtn = document.querySelector('.submit-btn');
    const compileModal = document.getElementById('submit_to_kitchen');
    const closeCompileModal = document.getElementById('closeCompileModal');
    const compiledOrdersDiv = document.getElementById('compiledOrders');
    const compiledNotesDiv = document.getElementById('compiledNotes');
    const compiledCustomerNameSpan = document.getElementById('compiledCustomerName');
    const submitToCashierBtn = document.getElementById('submitToCashierBtn');
    const reserved_date = document.getElementById('reserved_date');
    const arrivalTimeInput = document.getElementById('arrivalTimeInput');
    const timeFrameDisplay = document.getElementById('timeFrameDisplay');
    let selectedTableId = null;

    const menuPrices = {
        @foreach($menuItems as $item)
            "{{ $item->menu_item }}": {{ $item->price }},
        @endforeach
    };

    const reservations = @json($reservations);

    function getReservationTimeFrame(startTimeStr) {
        const [hours, minutes] = startTimeStr.split(':').map(Number);
        const start = new Date();
        start.setHours(hours, minutes, 0, 0);
        const end = new Date(start.getTime() + 2 * 60 * 60 * 1000);
        return `${formatTime(start)} - ${formatTime(end)}`;
    }

    function formatTime(date) {
        const h = date.getHours().toString().padStart(2, '0');
        const m = date.getMinutes().toString().padStart(2, '0');
        return `${h}:${m}`;
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

    arrivalTimeInput.addEventListener('input', () => {
        const selectedTime = arrivalTimeInput.value;
        timeFrameDisplay.textContent = selectedTime ? getReservationTimeFrame(selectedTime) : '';
    });

    reserved_date.addEventListener('change', () => {
        arrivalTimeInput.dispatchEvent(new Event('input'));
    });

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

    function updateSpecifyOrders() {
        const previousValues = {};
        specifyOrdersDiv.querySelectorAll('input[type="number"]').forEach(input => {
            previousValues[input.name] = input.value;
        });

        specifyOrdersDiv.innerHTML = '';
        menuCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const savedValue = previousValues[checkbox.value] || 1;
                const price = menuPrices[checkbox.value];

                specifyOrdersDiv.innerHTML += `
                    <label>${checkbox.value}
                        <input type="number" name="${checkbox.value}" min="1" value="${savedValue}" style="width:50px;"
                            onchange="calculateTotal()" oninput="calculateTotal()">
                    </label><br>`;
            }
        });

        calculateTotal();
    }



    function updateTotal(itemName, price) {
    const qty = parseInt(document.querySelector(`input[name="${itemName}"]`).value) || 0;
    const total = qty * price;
    
    calculateTotal();
    }

    function calculateTotal() {
        let total = 0;
        let orders = [];

        menuCheckboxes.forEach(cb => {
            if (cb.checked) {
                const qty = parseInt(document.querySelector(`input[name="${cb.value}"]`).value || "0");
                const price = parseFloat(menuPrices[cb.value]) || 0;
                const itemTotal = qty * price;

                if (qty > 0) {
                    orders.push({ item: cb.value, qty });
                    total += itemTotal;
                }
            }
        });
        document.getElementById('total').textContent = total.toFixed(2);

        }


    menuCheckboxes.forEach(cb => cb.addEventListener('change', updateSpecifyOrders));

    closeModal.onclick = () => modal.style.display = 'none';
    closeCompileModal.onclick = () => compileModal.style.display = 'none';

    window.onclick = e => {
        if (e.target === modal) modal.style.display = 'none';
        if (e.target === compileModal) compileModal.style.display = 'none';
    };

    submitBtn.addEventListener('click', () => {
        const name = document.getElementById('customerName').value.trim();
        const pax = document.getElementById('numberOfPax').value.trim();
        const arrival = arrivalTimeInput.value;
        const notes = document.getElementById('customerNotes').value.trim();

        if (!name || pax <= 0) {
            alert("Please complete customer name and pax.");
            return;
        }

        if (!isTimeWithinAllowedRange(arrival)) {
            alert("Reservation must start between 10:00 and 18:00, and end before 20:00.");
            return;
        }

        compiledOrdersDiv.innerHTML = '';
        menuCheckboxes.forEach(cb => {
            if (cb.checked) {
                const qty = parseInt(document.querySelector(`input[name="${cb.value}"]`).value || "0");
                if (qty > 0) {
                    compiledOrdersDiv.innerHTML += `<p>${cb.value}: ${qty}</p>`;
                }
            }
        });

        compiledCustomerNameSpan.textContent = name;
        document.getElementById('compiledArrivalTime').textContent = arrival;
        compiledNotesDiv.textContent = notes || "(No notes)";
        compileModal.style.display = 'flex';
    });

    submitToCashierBtn.addEventListener('click', () => {
        const customerName = document.getElementById('customerName').value.trim();
        const numberOfPax = document.getElementById('numberOfPax').value.trim();
        const arrivalTime = arrivalTimeInput.value;
        const notes = document.getElementById('customerNotes').value.trim();

        if (!customerName || numberOfPax <= 0 || !arrivalTime || !selectedTableId) {
            alert("Please complete the form.");
            return;
        }

        if (!isTimeWithinAllowedRange(arrivalTime)) {
            alert("The time reservation is not acceptable!");
            return;
        }

        let orders = [];
        menuCheckboxes.forEach(cb => {
            if (cb.checked) {
                const qty = parseInt(document.querySelector(`input[name="${cb.value}"]`).value || "0");
                if (qty > 0) orders.push({ item: cb.value, qty });
            }
        });

        const data = {
            customer_name: customerName,
            pax: numberOfPax,
            arrival_time: arrivalTime,
            notes: notes,
            orders: orders,
            table_id: selectedTableId,
            advance_payment: advance_payment.value.trim() || null
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
                compileModal.style.display = 'none';
                location.reload();
            } else {
                alert("Time reservtion not available! " );
            }
        })
        .catch(error => {
            console.error(error);
            alert("An error occurred.");
        });
    });

    function updateManilaTime() {
        const now = new Date().toLocaleString("en-PH", {
            timeZone: "Asia/Manila",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            hour12: true,
        });
        document.getElementById("manilaTimeDisplay").textContent = `Current Time: ${now}`;
    }

    document.getElementById('reserved_date').value = new Date().toISOString().substring(0, 10);
    setInterval(updateManilaTime, 1000);
    updateManilaTime();
    
    function fetchAvailableTimes() {
    const date = document.getElementById('reserved_date').value;
    const tableNumber = selectedTableId;

        if (!date || !tableNumber) return;
            
        fetch(`/receptionist/available-times?date=${date}&table_number=${tableNumber}`)
            .then(res => res.json())
            .then(times => {
                const container = document.getElementById('timeFrameDisplay');

                if (times.length === 0) {
                    container.textContent = 'No available times';
                    return;
                }

                container.textContent = 'Available: ' + times.join(', ');
            })
            .catch(() => {
                document.getElementById('timeFrameDisplay').textContent = "Error fetching times";
            });
    }

    document.getElementById('reserved_date').addEventListener('change', fetchAvailableTimes);
    
    </script>


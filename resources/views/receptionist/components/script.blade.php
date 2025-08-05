<script>
    const modal = document.getElementById('tableModal');
    const closeModal = document.getElementById('closeModal');
    const tableLinks = document.querySelectorAll('.table-link');
    const submitBtn = document.getElementById('submitBtn');
    const arrivalTimeInput = document.getElementById('arrivalTimeInput');
    const reserved_date = document.getElementById('reserved_date');
    const notesInput = document.getElementById('customerNotes');

    let selectedTableId = null;
    let isPlacingOrder = false;

    const selectedOrders = {};
    const orderContainer = document.getElementById("selectedOrdersContainer");


    @if(isset($menuPricesMap))
        const fullMenuPrices = @json($menuPricesMap);
    @else
        const fullMenuPrices = {};
    @endif
    let menuPrices = {};

    function updateMenuPrices() {
        let [hours, minutes] = (arrivalTimeInput.value || "00:00").split(':').map(Number);
        let time = hours * 60 + minutes;

        const isLunch = time < 960;
        menuPrices = {};
        for (const id in fullMenuPrices) {
            let lunch = fullMenuPrices[id].lunch;
            let dinner = fullMenuPrices[id].dinner;
            if (lunch == null) lunch = dinner;
            if (dinner == null) dinner = lunch;
            menuPrices[id] = isLunch ? lunch : dinner;
        }
    }

    // Modal open/close
    closeModal.onclick = () => modal.style.display = 'none';
    window.onclick = e => { if (e.target === modal) modal.style.display = 'none'; };

    // Handle table click
    const inlineOptionHandler = link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            document.querySelectorAll('.inline-options').forEach(opt => opt.style.display = 'none');
            const options = link.querySelector('.inline-options');
            if (options) options.style.display = 'block';
        });
    };
    tableLinks.forEach(link => inlineOptionHandler(link));

    function makeOrder(tableId) {
        selectedTableId = tableId;
        isPlacingOrder = true;
        modal.style.display = 'flex';

        const now = new Date();
        document.getElementById('reserved_date').value = now.toISOString().substring(0, 10);
        document.getElementById('reserved_date').disabled = true;

        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        document.getElementById('arrivalTimeInput').value = `${hours}:${minutes}`;
        document.getElementById('arrivalTimeInput').disabled = true;

        document.getElementById('reservationInfoGroup').style.display = 'none';

        document.getElementById('advance_payment').parentElement.style.display = 'none';


        document.querySelectorAll('.menu-card').forEach(card => {
            card.classList.remove('selected');
            const qtyInput = card.querySelector('input[type="number"]');
            if (qtyInput) qtyInput.value = 1;
        });

        document.getElementById('selectedOrdersContainer').innerHTML = '';
        document.getElementById('customerName').value = '';
        document.getElementById('numberOfPax').value = 1;
        document.getElementById('customerNotes').value = '';
        document.getElementById('advance_payment').value = '';

        updateMenuPrices();
    }


    function makeReservation(tableId) {
        selectedTableId = tableId;
        isPlacingOrder = false;
        modal.style.display = 'flex';

        document.getElementById('reservationInfoGroup').style.display = '';
        document.getElementById('reserved_date').disabled = false;
        document.getElementById('arrivalTimeInput').disabled = false;
        document.getElementById('advance_payment').parentElement.style.display = '';

        const now = new Date();

        // Format date as YYYY-MM-DD
        const today = now.getFullYear() + '-' +
            String(now.getMonth() + 1).padStart(2, '0') + '-' +
            String(now.getDate()).padStart(2, '0');
        document.getElementById('reserved_date').value = today;

        // Format time as HH:MM
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        document.getElementById('arrivalTimeInput').value = `${hours}:${minutes}`;


        document.querySelectorAll('.menu-card').forEach(card => {
            card.classList.remove('selected');
            const qtyInput = card.querySelector('input[type="number"]');
            if (qtyInput) qtyInput.value = 1;
        });

        document.getElementById('arrivalTimeInput').addEventListener('input', updateTimeFrameDisplay);
        document.getElementById('selectedOrdersContainer').innerHTML = '';
        document.getElementById('customerName').value = '';
        document.getElementById('numberOfPax').value = 1;
        document.getElementById('customerNotes').value = '';
        document.getElementById('advance_payment').value = '';

        updateMenuPrices();
        updateTimeFrameDisplay();

    }

    function selectMenuItem(el) {
        const id = el.dataset.id;
        const name = el.dataset.name;
        const price = menuPrices[id] || parseFloat(el.dataset.price);

        if (selectedOrders[id]) {
            selectedOrders[id].quantity += 1;
        } else {
            selectedOrders[id] = { name, quantity: 1, price };
            el.classList.add("selected");
        }

        const img = el.querySelector('img');
        if (img) animateFlyToCart(img, '#viewOrdersBtn');

        renderOrderSummary();
    }

    function renderOrderSummary() {
        orderContainer.innerHTML = '';
        let total = 0;
        let totalQuantity = 0;
        let hasItems = Object.keys(selectedOrders).length > 0;


        if (hasItems) {
            const header = document.createElement('li');
            header.className = "flex justify-between mb-2 font-semibold border-b pb-1";
            header.innerHTML = `<span>Menu</span><span>Quantity</span>`;
            orderContainer.appendChild(header);
        }

        Object.entries(selectedOrders).forEach(([id, item]) => {
            total += item.price * item.quantity;
            totalQuantity += item.quantity;

            const row = document.createElement('li');
            row.className = "flex justify-between items-center mb-2";

            row.innerHTML = `
                <span class="font-medium">${item.name}</span>
                <input type="number" min="1" value="${item.quantity}" 
                    class="text-right px-1 py-0.5 border border-gray-400 rounded text-black text-sm"
                    style="width: 2.5rem;"  
                    data-id="${id}"
                    onchange="updateQuantity(this)">
                `;

            orderContainer.appendChild(row);
        });


        const advancePaymentInput = document.getElementById('advance_payment');
        if (!isPlacingOrder && advancePaymentInput) {
            const halfTotal = (total / 2).toFixed(2);
            advancePaymentInput.value = halfTotal;
        }

        if (document.getElementById('totalQuantity')) {
            document.getElementById('totalQuantity').textContent = totalQuantity;
        }
    }


    function updateQuantity(input) {
        const id = input.dataset.id;
        const newQuantity = parseInt(input.value);

        if (selectedOrders[id] && newQuantity > 0) {
            selectedOrders[id].quantity = newQuantity;
        }

        renderOrderSummary();
    }


    document.getElementById('clearOrdersBtn').addEventListener('click', () => {

        for (const key in selectedOrders) {
            delete selectedOrders[key];
        }


        document.querySelectorAll('.menu-card').forEach(card => {
            card.classList.remove('selected');
            const qtyInput = card.querySelector('input[type="number"]');
            if (qtyInput) qtyInput.value = 1;
        });

        // Clear order summary
        document.getElementById('selectedOrdersContainer').innerHTML = '';
        document.getElementById('advance_payment').value = '';

    });


    document.querySelectorAll('.table-link').forEach(link => {
        link.addEventListener('click', function () {
            const options = this.querySelector('.inline-options');


            const isVisible = options.style.display === 'flex';


            document.querySelectorAll('.inline-options').forEach(opt => {
                opt.style.display = 'none';
            });


            if (!isVisible) {
                options.style.display = 'flex';
            }
        });
    });
    document.addEventListener('click', function (event) {

        if (!event.target.closest('.table-link')) {
            document.querySelectorAll('.inline-options').forEach(opt => {
                opt.style.display = 'none';
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const viewOrdersBtn = document.getElementById('viewOrdersBtn');
        const defaultModal = document.getElementById('default-modal');
        const closeButtons = defaultModal.querySelectorAll('[data-modal-hide="default-modal"]');

        // Show modal
        viewOrdersBtn.addEventListener('click', () => {
            defaultModal.classList.remove('hidden');
        });

        // Hide modal
        closeButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                defaultModal.classList.add('hidden');
            });
        });

        // Optional: hide modal if clicked outside content
        defaultModal.addEventListener('click', (e) => {
            if (e.target === defaultModal) {
                defaultModal.classList.add('hidden');
            }
        });
    });
    


    submitBtn.addEventListener('click', () => {
        if (submitBtn.disabled) return;

        const [hours, minutes] = (arrivalTimeInput.value || "00:00").split(':').map(Number);
        const timeInMinutes = hours * 60 + minutes;

        let minTime = 690;
        let maxTime = isPlacingOrder ? 1200 : 1080;

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
            is_order: isPlacingOrder,
            advance_payment: document.getElementById('advance_payment').value.trim(),
            orders: Object.entries(selectedOrders).map(([id, item]) => ({
                menu_id: id,
                quantity: item.quantity,
                price: item.price,
                notes: document.getElementById('customerNotes').value.trim()

            }))

        };

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
                    submitBtn.textContent = "Submit";
                }
            })
            .catch(() => {
                alert("An error occurred.");
                submitBtn.disabled = false;
                submitBtn.textContent = "Submit";
            });
    });

    function animateFlyToCart(imageEl, targetSelector) {
        const imgRect = imageEl.getBoundingClientRect();
        const targetEl = document.querySelector(targetSelector);
        const targetRect = targetEl.getBoundingClientRect();

        const flyingImg = imageEl.cloneNode(true);
        flyingImg.style.position = 'fixed';
        flyingImg.style.left = `${imgRect.left}px`;
        flyingImg.style.top = `${imgRect.top}px`;
        flyingImg.style.width = `${imgRect.width}px`;
        flyingImg.style.height = `${imgRect.height}px`;
        flyingImg.style.transition = 'all 0.8s ease-in-out';
        flyingImg.style.filter = 'blur(2px)';
        flyingImg.style.zIndex = '10000';
        flyingImg.style.pointerEvents = 'none';
        flyingImg.style.borderRadius = '10px';

        document.getElementById('fly-animation-container').appendChild(flyingImg);

        requestAnimationFrame(() => {
            flyingImg.style.left = `${targetRect.left + targetRect.width / 2}px`;
            flyingImg.style.top = `${targetRect.top + targetRect.height / 2}px`;
            flyingImg.style.width = `0px`;
            flyingImg.style.height = `0px`;
            flyingImg.style.opacity = '0.3';
        });

        flyingImg.addEventListener('transitionend', () => {
            flyingImg.remove();
        });
    }

    // Manila time display
    setInterval(() => {
        const now = new Date().toLocaleString("en-PH", { timeZone: "Asia/Manila", hour: "2-digit", minute: "2-digit", second: "2-digit", hour12: true });
        document.getElementById("manilaTimeDisplay").textContent = `Current Time: ${now}`;
    }, 1000);
    document.getElementById('reserved_date').value = new Date().toISOString().substring(0, 10);

    function updateTimeFrameDisplay() {
        const arrivalTime = document.getElementById('arrivalTimeInput').value;

        if (!arrivalTime) {
            document.getElementById('timeFrameDisplay').textContent = '';
            return;
        }

        const [hours, minutes] = arrivalTime.split(':').map(Number);
        const start = new Date();
        start.setHours(hours);
        start.setMinutes(minutes);

        const end = new Date(start.getTime() + 2 * 60 * 60 * 1000); // add 2 hours

        const options = { hour: 'numeric', minute: '2-digit', hour12: true };
        const startStr = start.toLocaleTimeString('en-US', options);
        const endStr = end.toLocaleTimeString('en-US', options);

        document.getElementById('timeFrameDisplay').textContent = `${startStr} - ${endStr}`;
    }

</script>
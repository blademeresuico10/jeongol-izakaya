<script>
  document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('tableModal');
    const closeModal = document.getElementById('closeModal');
    const tableLinks = document.querySelectorAll('.table-link');

    const nameInput = document.getElementById('customerName');
    const contactInput = document.getElementById('contactNumber');
    const paxInput = document.getElementById('pax');
    const notesInput = document.getElementById('notes');

    const dateInput = document.getElementById('reserved_date');
    const timeInput = document.getElementById('arrivalTimeInput');

    const advance_payment = document.getElementById('advancePayment');

    const submitBtn = document.getElementById('submitBtn');
    const orderBtn = document.getElementById('order');

    const fullMenuPrices = @json($menuPricesMap);
    const selectedOrders = {}; // Store selected orders

    let selectedTableNumber = 0;

    // Handle table click (open reservation modal)
    tableLinks.forEach(link => {
      link.addEventListener('click', e => {
        e.preventDefault();
        selectedTableNumber = parseInt(link.getAttribute('data-table-id'));
        document.getElementById('selectedTableNumber').value = selectedTableNumber;
        modal.style.display = 'flex';
      });
    });

    closeModal.addEventListener('click', () => {
      modal.style.display = 'none';
      resetForm();
    });

    // Date input limits
    const now = new Date();
    if (now.getHours() > 18 || (now.getHours() === 18 && now.getMinutes() >= 30)) {
      now.setDate(now.getDate() + 1);
    }
    dateInput.value = now.toISOString().split('T')[0];
    const today = new Date();
    const maxDate = new Date();
    maxDate.setDate(today.getDate() + 2);
    dateInput.min = today.toISOString().split('T')[0];
    dateInput.max = maxDate.toISOString().split('T')[0];

    // Animate menu item into cart
    window.selectMenuItem = function (card) {
      const img = card.querySelector('img');
      if (img) {
        animateFlyToCart(img, '#ordersButton');
      }

      const id = card.dataset.id;
      const name = card.dataset.name;
      const category = card.dataset.category;
      const price = parseFloat(card.dataset.price);

      if (!selectedOrders[id]) {
        selectedOrders[id] = {
          id,
          name,
          category,
          price,
          quantity: 1,
          total: price
        };
      } else {
        selectedOrders[id].quantity += 1;
        selectedOrders[id].total = selectedOrders[id].quantity * price;
      }

      updateOrderSummary();
    };

    function updateOrderSummary() {
      const container = document.getElementById('selectedOrdersContainer');
      container.innerHTML = '';

      let total = 0;
      let totalQuantity = 0;
      const orderCount = Object.keys(selectedOrders).length;

      if (orderCount > 0) {
        const header = document.createElement('li');
        header.className = "flex justify-between mb-2 font-semibold border-b pb-1";
        header.innerHTML = `<span>Menu</span><span>Qty</span><span>Subtotal</span>`;
        container.appendChild(header);
      }

      Object.entries(selectedOrders).forEach(([id, item]) => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
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
            <span class="text-sm text-gray-700">₱${itemTotal.toFixed(2)}</span>
        `;

        container.appendChild(row);
      });

      // Advance payment calculation
      const advancePaymentInput = document.getElementById('advance_payment');
      const advancePaymentLabel = document.getElementById('advance_payment_label');

      if (advancePaymentInput) {
        let advance = 600; // default base amount
        if (orderCount > 0) {
          advance += (orderCount * 50); // ₱50 per unique menu item
        }

        advancePaymentInput.value = advance.toFixed(2);

        if (advancePaymentLabel) {
          advancePaymentLabel.textContent = 'Default amount';
        }
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

      updateOrderSummary();
    }



    // Clear orders button
    document.getElementById('clearOrdersBtn').addEventListener('click', () => {
      Object.keys(selectedOrders).forEach(k => delete selectedOrders[k]);
      updateOrderSummary();
    });

    // Show Order Modal
    orderBtn.addEventListener('click', () => {
      document.getElementById('orderModal').style.display = 'flex';
    });

    document.getElementById('closeOrderModal').addEventListener('click', () => {
      document.getElementById('orderModal').style.display = 'none';
    });

    // Gather reservation + order data
    function gatherReservationData() {
      const note = notesInput.value.trim();

      const menuItems = Object.values(selectedOrders).map(item => ({
        menu_id: item.id,
        quantity: item.quantity,
        price: parseFloat(item.price)
      }));

      return {
        pax: paxInput.value,
        customer_name: nameInput.value.trim(),
        contact_number: contactInput.value.trim(),
        reserved_date: dateInput.value,
        arrival_time: timeInput.value,
        table_id: selectedTableNumber,
        advance_payment: document.getElementById('advance_payment').value.trim(),
        notes: note,
        orders: menuItems
      };
    }


    // Simple validation
    function validateInputs() {
      return nameInput.value.trim() && contactInput.value.trim() && paxInput.value && timeInput.value && dateInput.value;
    }


    // Form submit handler
    submitBtn.addEventListener('click', async (e) => {
      e.preventDefault();

      if (!validateInputs()) {
        alert('Please complete all required fields.');
        return;
      }

      const data = gatherReservationData();
      submitBtn.disabled = true;
      submitBtn.textContent = 'Submitting...';

      try {
        const res = await fetch('/customer/reserve', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify(data)
        });


        const response = await res.json();

        if (res.ok && response.success) {
          alert('Reservation successful!');
          resetForm();
          modal.style.display = 'none';
        } else {
          alert(response.message || 'Reservation could not be completed.');
        }

      } catch (err) {
        console.error(err);
        alert('Something went wrong while submitting the reservation.');
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit Reservation';
      }
    });

    // Reset form
    function resetForm() {
      [nameInput, contactInput, dateInput, timeInput, notesInput].forEach(el => el.value = '');
      paxInput.value = '';

      selectedTableNumber = 0;
      document.getElementById('selectedTableNumber').value = '';
      Object.keys(selectedOrders).forEach(k => delete selectedOrders[k]);
      updateOrderSummary();
    }

    // Fly-to-cart animation
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

  });

  document.addEventListener("DOMContentLoaded", function () {
    const ordersButton = document.getElementById("ordersButton");
    const defaultModal = document.getElementById("default-modal");
    const closeButtons = defaultModal.querySelectorAll("[data-modal-hide='default-modal']");

    // Show modal on Orders button click
    ordersButton.addEventListener("click", () => {
      defaultModal.classList.remove("hidden");
    });

    // Hide modal when any "data-modal-hide" button is clicked
    closeButtons.forEach(btn => {
      btn.addEventListener("click", () => {
        defaultModal.classList.add("hidden");
      });
    });
  });
</script>
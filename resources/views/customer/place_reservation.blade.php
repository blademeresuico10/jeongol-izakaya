<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Jeongol Izakaya</title>

  <link rel="shortcut icon" type="x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  @vite('resources/css/app.css')

  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    header {
      padding: 1rem;
      text-align: center;
      font-size: 1.5rem;
    }

    header img {
      height: 45px;
    }

    .table-layout {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      justify-content: center;
      padding: 20px;
      flex-grow: 1;
    }

    .table-link {
      flex: 0 calc(15% - 10px);
      text-decoration: none;
    }

    .table {
      width: 100%;
      aspect-ratio: 1 / 1;
      display: flex;
      align-items: center;
      color: white;
      font-weight: bold;
      border-radius: 20px;
      background-color: #28a745;
    }

    .table:hover {
      background-color: #218838;
      transform: scale(1.03);
    }

    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }

    .modal-content {
      background: white;
      padding: 20px;
      border-radius: 10px;
      width: 90%;
      max-width: 500px;
      max-height: auto;
      position: relative;
    }

    .modal-section {
      margin-bottom: 8px;
    }

    .modal-order {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      justify-content: center;
      align-items: center;
      z-index: 1000;
      overflow-x: hidden !important;

    }

    .modal-order .modal-content {
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      width: 100%;
      max-width: 500px;
      max-height: 90vh;
      overflow-y: auto;
      overflow-x: hidden;
      position: relative;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
      box-sizing: border-box;

    }

    .modal-order h3 {
      font-size: 1.2rem;
      margin-bottom: 20px;
      text-align: center;
    }

    .modal-order .modal-section {
      display: flex;
      flex-direction: column;
      gap: 20px;
      margin-bottom: 20px;
    }

    .menu-image-container {
      width: 100%;
      height: 70px;
      overflow: hidden;
      border-top-left-radius: 8px;
      border-top-right-radius: 8px;
    }

    .menu-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .menu-cards-container {
      display: flex;
      flex-wrap: wrap;
      gap: 0.75rem;
      width: 100px;
      justify-content: center;
      max-width: 100%;
      overflow-x: hidden;
    }

    label {
      font-size: 14px;
      text-align: left;
      display: block;
    }

    input,
    textarea {
      padding: 8px;
      margin-top: 4px;
      width: 100%;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .modal-actions {
      display: flex;
      justify-content: center;
      margin-top: 20px;
    }

    .submit-btn {
      background-color: #007bff;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
    }

    .order_food {
      background-color: #ff0000;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
    }

    @media (max-width: 1024px) {
      .table-link {
        flex: 0 1 calc(25% - 10px);
      }
    }

    @media (max-width: 768px) {
      .table-link {
        flex: 0 1 calc(33.33% - 10px);
      }
    }

    @media (max-width: 768px) {
      .table-link {
        flex: 0 1 calc(33.33% - 10px);
      }
    }

    @media (max-width: 480px) {

      .table-layout {
        grid-template-columns: repeat(6, 2fr);
        gap: 10px;
      }

      .table-link .table {
        height: 100px;
        padding: 6px;
      }

      .table-link .table-number {
        font-size: 12px;
      }

      .table-link .absolute {
        font-size: 11px;
      }

      .modal-content {
        width: 70%;
        max-width: 700px;
        margin: 0 auto;
        padding: 12px;
      }

      .modal-section {
        margin-bottom: 8px;
      }

      .modal-section label {
        font-size: 13px;
      }

      .modal-section input,
      .modal-section textarea {
        font-size: 13px;
        padding: 6px;
      }

      .flex.gap-4 {
        flex-direction: row;
        gap: 6px;
      }

      .flex.gap-4 button {
        flex: 1;
        width: auto;
        font-size: 13px;
        padding: 3px;
      }

      .modal-content p.text-lg strong {
        font-size: 15px;
        margin-bottom: 6px;
      }

      /* order */
      #default-modal .relative {
        width: 95%;
        max-width: 360px;
        height: 65vh;
      }

      #default-modal h3 {
        font-size: 15px;
        margin-top: 6px;
        margin-left: 8px;
      }

      #orderSummary {
        font-size: 12px;
        padding: 7px;
      }

      #default-modal button {
        font-size: 12px;
        padding: 6px 12px;
      }

      #default-modal .flex.gap-4 {
        gap: 6px;
      }

      /*payment*/
      #paymentModal .bg-white {
        width: 80%;
        max-width: 300px;
        padding: 12px;
      }

      #paymentModal h2 strong {
        font-size: 17px;

      }

      #paymentModal button {
        font-size: 14px;
        padding: 8px 10px;
      }

      #paymentModal h2 {
        font-size: 16px;
      }

      #paymentModal .flex button {
        font-size: 14px;
      }

      #paymentModal #submitBtn {
        font-size: 14px;
        padding: 8px;
      }

      #paymentModal label,
      #paymentModal input,
      #paymentModal span {
        font-size: 13px;
      }
    }

    .main-menu-grid,
    .other-menu-grid {
      display: grid !important;
      grid-template-columns: repeat(3, 1fr) !important;
    }

    .menu-card img {
      height: 90px !important;
      width: 100% !important;
      border-radius: 3px;
    }

    .menu-card h5 {
      font-size: 12px;
    }

    .menu-card .p-2 {
      padding: 3px;
    }

    .input-error {
      border: 2px solid #ef4444;
    }

    @keyframes fade-in {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .animate-fade-in {
      animation: fade-in 0.3s ease-out;
    }

    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }

    input[type=number] {
      -moz-appearance: textfield;
    }
  </style>
</head>

<body>
  <header>
    <a href="{{ route('customer.index') }}" class="me-2 text-dark" style="text-decoration: none;">
      <i class="bi bi-arrow-left-circle-fill"></i>
    </a>
    Welcome to <strong>Jeongol Izakaya</strong>
  </header>

  <div class="table-layout grid grid-cols-2 gap-4">
    @foreach($tables as $table)
    <div class="table-link cursor-pointer" data-table-id="{{ $table->id }}"
      data-table-number="{{ $table->table_number }}">

      <div class="table available relative flex flex-col items-center justify-center h-50 border rounded bg-green-100">

      <div class="absolute top-1 text-xs ">{{ $table->capacity }} Pax</div>

      <div class="table-number text-lg font-semibold">Table {{ $table->table_number }}</div>
      </div>
    </div>
  @endforeach
  </div>

  <div id="tableModal" class="modal">
    <div class="modal-content">
      <span id="closeModal" class="close-modal text-end text-lg cursor-pointer">&times;</span>
      <h3 class="mb-2 text-lg text-center"><strong>Please Enter Reservation Details</strong></h3>
      <form id="reservationForm">
        <div class="modal-section">
          <label for="customerName">Customer</label>
          <input type="text" id="customerName" name="customer_name" placeholder="Enter your name" required />
        </div>

        <div class="modal-section">
          <label for="contactNumber">Contact Number</label>
          <input type="number" id="contactNumber" name="contact_number" placeholder="09XXXXXXXXX" maxlength="11" />
          <span class="inline-error text-red-500 text-sm hidden"></span>
        </div>


        <div class="modal-section">
          <label for="pax">Pax</label>
          <input type="number" id="pax" min="1" max="10" />
        </div>

        <div class="modal-section">
          <label for="arrivalTimeInput">Arrival Time</label>
          <input type="time" id="arrivalTimeInput" required />
        </div>

        <div class="modal-section">
          <label for="reserved_date">Reservation Date</label>
          <input type="date" id="reserved_date" required />
        </div>

        <label for="advance_payment">
          Advance Payment
        </label>
        <input type="number" id="advance_payment" class="form-control" value="600" readonly />
        <div class="modal-section">
          <label for="notesTextarea">Notes</label>
          <textarea id="notesTextarea" name="notes" rows="2"
            placeholder="Enter any special requests or notes"></textarea>
        </div>

        <input type="hidden" id="selectedTableNumber">

        <div class="flex gap-4 p-2 border-t border-gray-200 dark:border-gray-600">

          <button data-modal-hide="default-modal" type="button" id="order"
            class="w-1/2 bg-gray-500 hover:bg-gray-600 text-white font-medium text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-4 focus:ring-gray-300 dark:bg-gray-700 dark:hover:bg-gray-800 dark:focus:ring-gray-600">
            Order Food
          </button>

          <button type="button" id="paymentBtn"
            class="w-1/2 bg-blue-500 hover:bg-blue-600 text-white font-medium text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-700 dark:hover:bg-blue-800 dark:focus:ring-blue-600">
            Proceed to payment
          </button>
        </div>
      </form>
    </div>
  </div>

  <div id="paymentModal" class="fixed inset-0 z-[1100] hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">

      <button id="closePaymentModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">✕</button>
      <h2 class="text-xl font-semibold text-center mb-4">Payment</h2>

      <div class="flex border-b mb-4">
        <button type="button" data-tab="gcash" class="flex-1 text-center py-2 font-semibold">Gcash</button>
        <button type="button" data-tab="maya" class="flex-1 text-center py-2 font-semibold">Maya</button>
      </div>

      <x-payment-form method="gcash" :readonly="true" :data="['amount' => 0]" />
      <x-payment-form method="maya" :readonly="true" :data="['amount' => 0]" />


      <button id="submitBtn" type="button"
        class="mt-4 w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
        Submit Reservation
      </button>
    </div>
  </div>

  <div id="orderModal" class="modal-order">
    <div class="modal-content">
      <span class="close-modal" id="closeOrderModal" style="float: right; cursor: pointer;">&times;</span>
      <h3 class="text-lg">
        <strong>
          Place Order
          <span>
            <div class="flex justify-end">
              <button data-modal-target="default-modal" data-modal-toggle="default-modal" type="button"
                id="ordersButton" class="bg-green-700 hover:bg-green-800 text-white px-3 py-1 rounded">
                Orders
              </button>
            </div>

          </span>
        </strong>
      </h3>
      <div class="modal-section">
        @foreach(['main' => 'Main Menu'] as $key => $label)
        @if(isset($groupedMenu[$key]))
        <x-menu-category-grid :key="$key" :label="$label" :items="$groupedMenu[$key]" />
      @endif
    @endforeach
      </div>
    </div>

    <div id="default-modal" tabindex="-1" aria-hidden="true"
      class="fixed top-0 left-0 right-0 z-50 hidden w-700 p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex justify-center items-center">

      <div class="relative w-full max-w-lg h-[60vh]">
        <div class="relative h-full bg-white rounded-lg shadow flex flex-col">

          <div class="modal-section">
            <div class="flex items-center justify-between p-2 rounded-t bg-green-800">
              <h3 class="text-lg font-semibold text-white mt-4 ml-5">
                Orders Breakdown
              </h3>
            </div>
          </div>

          <div id="orderSummary"
            class="p-4 bg-white  text-sm text-gray-800 dark:text-white border  overflow-y-auto flex-1">
            <ul id="selectedOrdersContainer" class="text-sm list-disc pl-5 text-black-700 dark:text-black mt-2">
            </ul>
          </div>

          <div class="flex justify-end gap-4 p-2 border-t border-gray-200 dark:border-gray-600">

            <button data-modal-hide="default-modal" type="button"
              class="bg-gray-500 hover:bg-gray-600 text-white font-medium text-sm px-5 py-2.5 rounded-lg focus:outline-none focus:ring-4 focus:ring-gray-300 dark:bg-gray-700 dark:hover:bg-gray-800 dark:focus:ring-gray-600">
              Close
            </button>

            <button id="clearOrdersBtn" type="button"
              class="bg-red-600 hover:bg-red-700 text-white font-medium text-sm px-5 py-2.5 rounded-lg focus:outline-none focus:ring-4 focus:ring-red-300 dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-800">
              Clear
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="messageBox" style="
  display: none;
  position: fixed;
  top: 20px;
  right: 20px;
  padding: 15px 20px;
  border-radius: 6px;
  color: white;
  font-weight: bold;
  box-shadow: 0 2px 8px rgba(0,0,0,0.3);
  z-index: 9999;
"></div>

  <footer class="text-center p-3 bg-gray-900 text-white mt-5">
    <p>Contact us</p>
    <div>
      <a href="https://www.facebook.com/jeongol.izakaya" target="_blank" class="text-white mx-2">
        <i class="bi bi-facebook"></i>
      </a>
      <a href="#" class="text-white mx-2"><i class="bi bi-instagram"></i></a>
      <a href="#" class="text-white mx-2"><i class="bi bi-twitter"></i></a>
      <a href="#" class="text-white mx-2"><i class="bi bi-envelope-fill"></i></a>
    </div>
    <p class="mt-2">&copy; 2023 Jeongol Izakaya. All rights reserved.</p>
  </footer>

  <div id="fly-animation-container"
    style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 9999;"></div>

  <script>
    window.customer_jeongolConfig = {
      storeReservationUrl: "{{ route('customer.reserve') }}",
      csrfToken: "{{ csrf_token() }}"
    };

    const selectedOrders = {};
    let selectedTableNumber = 0;
    let defaultAdvancePayment = 0;

    let elements = {};

    function showMessageBox(message, type = 'success') {
      const box = document.getElementById('messageBox');
      box.textContent = message;

      const colors = {
        success: '#4CAF50',
        error: '#f44336',
        warning: '#ff9800'
      };

      box.style.background = colors[type] || colors.success;
      box.style.display = 'block';

      setTimeout(() => {
        box.style.display = 'none';
      }, 3000);
    }

    function animateFlyToCart(imageEl, targetSelector) {
      const imgRect = imageEl.getBoundingClientRect();
      const targetEl = document.querySelector(targetSelector);
      const targetRect = targetEl.getBoundingClientRect();

      const flyingImg = imageEl.cloneNode(true);
      Object.assign(flyingImg.style, {
        position: 'fixed',
        left: `${imgRect.left}px`,
        top: `${imgRect.top}px`,
        width: `${imgRect.width}px`,
        height: `${imgRect.height}px`,
        transition: 'all 0.8s ease-in-out',
        filter: 'blur(2px)',
        zIndex: '10000',
        pointerEvents: 'none',
        borderRadius: '10px'
      });

      document.getElementById('fly-animation-container').appendChild(flyingImg);

      requestAnimationFrame(() => {
        Object.assign(flyingImg.style, {
          left: `${targetRect.left + targetRect.width / 2}px`,
          top: `${targetRect.top + targetRect.height / 2}px`,
          width: '0px',
          height: '0px',
          opacity: '0.3'
        });
      });

      flyingImg.addEventListener('transitionend', () => {
        flyingImg.remove();
      });
    }

    function initializeTabs() {
      document.querySelectorAll('[data-tab]').forEach(tabBtn => {
        tabBtn.addEventListener('click', () => {
          document.querySelectorAll('[data-tab]').forEach(btn => {
            btn.classList.remove('bg-gray-200', 'font-bold');
            btn.classList.add('border-transparent');
          });

          tabBtn.classList.remove('border-transparent');
          tabBtn.classList.add('bg-gray-200', 'font-bold', 'rounded');

          const tabName = tabBtn.getAttribute('data-tab');
          document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
          });
          document.getElementById(`tab-${tabName}`)?.classList.remove('hidden');
        });
      });
    }

    function initializeModals() {
      const modal = elements.tableModal;
      const closeModal = elements.closeModal;

      elements.tableLinks.forEach(link => {
        link.addEventListener('click', e => {
          e.preventDefault();
          selectedTableNumber = parseInt(link.getAttribute('data-table-id'));
          elements.selectedTableNumber.value = selectedTableNumber;
          modal.style.display = 'flex';
        });
      });

      closeModal.addEventListener('click', () => {
        modal.style.display = 'none';
        resetReservationForm();
      });

      elements.paymentBtn.addEventListener('click', () => {
        const advancePayment = parseFloat(elements.advancePaymentInput.value) || 0;

        elements.paymentModal.querySelectorAll('.tab-content .amount').forEach(input => {
          input.value = advancePayment;
          input.min = advancePayment;
          input.readOnly = true;
        });

        elements.paymentModal.classList.remove('hidden');
        elements.paymentModal.classList.add('flex');
      });

      elements.closePaymentModal.addEventListener('click', () => {
        elements.paymentModal.classList.add('hidden');
        elements.paymentModal.classList.remove('flex');
      });

      elements.orderBtn.addEventListener('click', () => {
        elements.orderModal.style.display = 'flex';
      });

      elements.closeOrderModal.addEventListener('click', () => {
        elements.orderModal.style.display = 'none';
      });

      elements.ordersButton.addEventListener('click', () => {
        elements.defaultModal.classList.remove('hidden');
      });

      elements.closeDefaultModal.forEach(btn => {
        btn.addEventListener('click', () => {
          elements.defaultModal.classList.add('hidden');
        });
      });
    }

    function initializeDateTimeInputs() {
      const now = new Date();

      if (now.getHours() > 18 || (now.getHours() === 18 && now.getMinutes() >= 30)) {
        now.setDate(now.getDate() + 1);
      }

      elements.dateInput.value = now.toISOString().split('T')[0];

      const today = new Date();
      const maxDate = new Date();
      maxDate.setDate(today.getDate() + 2);

      elements.dateInput.min = today.toISOString().split('T')[0];
      elements.dateInput.max = maxDate.toISOString().split('T')[0];
    }

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
          id, name, category, price,
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
      const container = elements.selectedOrdersContainer;
      container.innerHTML = '';

      let total = 0;
      let totalQuantity = 0;
      const orderCount = Object.keys(selectedOrders).length;

      if (orderCount > 0) {
        const header = document.createElement('li');
        header.className = "grid grid-cols-3 mb-2 font-semibold border-b pb-1";
        header.innerHTML = `
          <span>Menu</span>
          <span class="text-center">Qty</span>
          <span class="text-right">Subtotal</span>
        `;
        container.appendChild(header);
      }

      Object.entries(selectedOrders).forEach(([id, item]) => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        totalQuantity += item.quantity;

        const row = document.createElement('li');
        row.className = "grid grid-cols-3 items-center mb-2 gap-2";
        row.innerHTML = `
          <span class="truncate">${item.name}</span>
          <input type="number" min="1" value="${item.quantity}" 
              class="w-14 text-center border border-gray-400 rounded text-black text-sm"
              data-id="${id}"
              onchange="updateQuantity(this)">
          <span class="text-sm text-gray-700 text-right">₱${itemTotal.toFixed(2)}</span>
        `;
        container.appendChild(row);
      });

      updateAdvancePayment(orderCount);

      if (elements.totalQuantity) {
        elements.totalQuantity.textContent = totalQuantity;
      }
      if (elements.totalPrice) {
        elements.totalPrice.textContent = `₱${total.toFixed(2)}`;
      }
    }

    function updateAdvancePayment(orderCount) {
      if (elements.advancePaymentInput) {
        let advance = defaultAdvancePayment;

        if (orderCount > 0) {
          advance += (orderCount * 50);
        }

        elements.advancePaymentInput.value = advance.toFixed(2);

        if (elements.advancePaymentLabel) {
          elements.advancePaymentLabel.textContent = advance.toFixed(2);
        }
      }
    }

    window.updateQuantity = function (input) {
      const id = input.dataset.id;
      const newQuantity = parseInt(input.value);

      if (selectedOrders[id] && newQuantity > 0) {
        selectedOrders[id].quantity = newQuantity;
        selectedOrders[id].total = selectedOrders[id].quantity * selectedOrders[id].price;
      }

      updateOrderSummary();
    };

    // New function to handle individual item notes
    window.updateItemNotes = function (input) {
      const id = input.dataset.id;
      const notes = input.value.trim();

      if (selectedOrders[id]) {
        selectedOrders[id].notes = notes;
      }
    };

    function clearOrders() {
      Object.keys(selectedOrders).forEach(k => delete selectedOrders[k]);
      updateOrderSummary();
    }

    function validateInputs() {
      let hasError = false;

      const requiredFields = [
        elements.nameInput,
        elements.contactInput,
        elements.paxInput,
        elements.timeInput,
        elements.dateInput
      ];

      requiredFields.forEach(field => {
        if (!field.value.trim()) {
          field.classList.add('input-error');
          hasError = true;
        } else {
          field.classList.remove('input-error');
        }
      });

      /* const selectedTime = elements.timeInput.value;
      if (selectedTime) {
        const [hours, minutes] = selectedTime.split(':').map(Number);
        if (hours < 11 || hours > 18 || (hours === 18 && minutes > 0)) {
          showMessageBox('Reservations are only allowed between 11:00 AM and 6:00 PM.', 'error');
          hasError = true;
        }
      }*/

      const activeTab = document.querySelector(".tab-content:not(.hidden)");
      if (activeTab) {
        const paymentFields = [
          activeTab.querySelector(".gcash-number, .maya-number"),
          activeTab.querySelector(".registered-name"),
          activeTab.querySelector(".ref-no"),
          activeTab.querySelector(".amount"),
          activeTab.querySelector(".proof")
        ];

        paymentFields.forEach(field => {
          if (field) {
            let value = field.value || (field.files && field.files.length > 0 ? field.files[0] : "");
            if (!value) {
              field.classList.add('input-error');
              const errorSpan = field.nextElementSibling;
              if (errorSpan) {
                errorSpan.textContent = 'This field is required';
                errorSpan.classList.remove('hidden');
              }
              hasError = true;
            } else {
              field.classList.remove('input-error');
              const errorSpan = field.nextElementSibling;
              if (errorSpan) {
                errorSpan.textContent = '';
                errorSpan.classList.add('hidden');
              }
            }
          }
        });
      }

      return !hasError;
    }

    function initializeContactValidation() {
      const contactInput = elements.contactInput;
      const error = contactInput.nextElementSibling;

      contactInput.addEventListener('input', () => {
        let value = contactInput.value.replace(/\D/g, '');

        if (value.length >= 11) {
          value = value.slice(0, 11);
        }

        if (value && !/^09\d{0,9}$/.test(value)) {
          error.textContent = 'Enter a valid contact number (09XXXXXXXXX)';
          error.classList.remove('hidden');
        } else {
          error.textContent = '';
          error.classList.add('hidden');
        }
        contactInput.value = value;
      });

      contactInput.addEventListener('keypress', (e) => {
        if (contactInput.value.replace(/\D/g, '').length >= 11 && !['Backspace', 'Delete'].includes(e.key)) {
          e.preventDefault();
        }
      });
    }



    function gatherReservationData() {
      const formData = new FormData();
      const activeTab = document.querySelector(".tab-content:not(.hidden)");

      if (!activeTab) {
        console.error('No active payment tab found');
        return formData;
      }

      const method = activeTab.id.includes("gcash") ? "Gcash" : "Maya";

      const orders = Object.values(selectedOrders);
      if (orders.length > 0) {
        // Get the general notes from the form to apply to ALL orders
        const generalNotes = elements.notesInput.value.trim();

        orders.forEach((item, index) => {
          formData.append(`orders[${index}][menu_id]`, item.id);
          formData.append(`orders[${index}][quantity]`, item.quantity);
          // Apply the general notes to each order item
          formData.append(`orders[${index}][notes]`, generalNotes);
        });
      }

      const basicData = {
        table_id: selectedTableNumber,
        customer_name: elements.nameInput.value.trim(),
        contact_number: elements.contactInput.value.trim(),
        pax: parseInt(elements.paxInput.value) || 1,
        reserved_date: elements.dateInput.value,
        arrival_time: elements.timeInput.value,
        advance_payment: parseFloat(elements.advancePaymentInput.value.trim()) || 0,
        payment_method: method
      };

      Object.entries(basicData).forEach(([key, value]) => {
        if (value !== null && value !== undefined && value !== '') {
          formData.append(key, value);
        }
      });

      const paymentFields = {
        number: activeTab.querySelector(".gcash-number, .maya-number"),
        registered_name: activeTab.querySelector(".registered-name"),
        amount: activeTab.querySelector(".amount"),
        ref_no: activeTab.querySelector(".ref-no")
      };

      Object.entries(paymentFields).forEach(([key, field]) => {
        if (field && field.value && field.value.trim()) {
          formData.append(key, field.value.trim());
        }
      });

      const proofInput = activeTab.querySelector(".proof");
      if (proofInput && proofInput.files && proofInput.files[0]) {
        formData.append('proof', proofInput.files[0]);
      }

      return formData;
    }

    function resetReservationForm() {
      [elements.nameInput, elements.contactInput, elements.dateInput,
      elements.timeInput, elements.notesInput].forEach(el => {
        if (el) el.value = '';
      });

      if (elements.paxInput) elements.paxInput.value = '';

      selectedTableNumber = 0;
      if (elements.selectedTableNumber) elements.selectedTableNumber.value = '';

      clearOrders();

      if (elements.advancePaymentInput) {
        elements.advancePaymentInput.value = defaultAdvancePayment.toFixed(2);
      }
      if (elements.advancePaymentLabel) {
        elements.advancePaymentLabel.textContent = defaultAdvancePayment.toFixed(2);
      }

      if (elements.paymentModal) {
        elements.paymentModal.classList.add('hidden');
        clearPaymentFields();
      }
    }

    function clearPaymentFields() {
      elements.paymentModal.querySelectorAll("input").forEach(input => {
        input.value = "";
        input.classList.remove("input-error");
      });
    }

    async function submitReservation() {
      if (!validateInputs()) {
        showMessageBox('Please complete all required fields.', 'error');
        return;
      }

      const data = gatherReservationData();
      const submitBtn = elements.submitBtn;

      submitBtn.disabled = true;
      submitBtn.textContent = "Submitting...";

      try {
        const response = await fetch("/customer/reserve", {
          method: "POST",
          headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            "Accept": "application/json"
          },
          body: data
        });

        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
          showMessageBox("Server error: Invalid response format", "error");
          return;
        }

        const json = await response.json();

        if (response.ok && json.success) {
          showMessageBox("Reservation successful!", "success");
          resetReservationForm();
          elements.tableModal.style.display = 'none';
        } else {
          const errors = json.errors || {};
          const messages = Object.values(errors).flat().join("\n");
          showMessageBox(messages || json.message || "Reservation failed", "error");
        }
      } catch (error) {
        showMessageBox("Network error: " + error.message, "error");
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = "Submit Reservation";
      }

    }

    function initializeElements() {
      elements = {
        tableModal: document.getElementById('tableModal'),
        paymentModal: document.getElementById('paymentModal'),
        orderModal: document.getElementById('orderModal'),
        defaultModal: document.getElementById('default-modal'),

        closeModal: document.getElementById('closeModal'),
        closePaymentModal: document.getElementById('closePaymentModal'),
        closeOrderModal: document.getElementById('closeOrderModal'),
        closeDefaultModal: document.querySelectorAll("[data-modal-hide='default-modal']"),

        paymentBtn: document.getElementById('paymentBtn'),
        orderBtn: document.getElementById('order'),
        submitBtn: document.getElementById('submitBtn'),
        ordersButton: document.getElementById('ordersButton'),
        clearOrdersBtn: document.getElementById('clearOrdersBtn'),

        nameInput: document.getElementById('customerName'),
        contactInput: document.getElementById('contactNumber'),
        paxInput: document.getElementById('pax'),
        notesInput: document.getElementById('notesTextarea'),
        dateInput: document.getElementById('reserved_date'),
        timeInput: document.getElementById('arrivalTimeInput'),
        advancePaymentInput: document.getElementById('advance_payment'),
        advancePaymentLabel: document.getElementById('advance_payment_label'),
        selectedTableNumber: document.getElementById('selectedTableNumber'),

        selectedOrdersContainer: document.getElementById('selectedOrdersContainer'),
        totalQuantity: document.getElementById('totalQuantity'),
        totalPrice: document.getElementById('totalPrice'),

        tableLinks: document.querySelectorAll('.table-link')
      };

      defaultAdvancePayment = parseFloat(elements.advancePaymentInput?.value) || 600;
    }

    document.addEventListener('DOMContentLoaded', () => {
      initializeElements();
      initializeTabs();
      initializeModals();
      initializeDateTimeInputs();
      initializeContactValidation();

      elements.clearOrdersBtn?.addEventListener('click', clearOrders);
      elements.submitBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        submitReservation();
      });

      updateOrderSummary();
    });
  </script>
</body>

</html>
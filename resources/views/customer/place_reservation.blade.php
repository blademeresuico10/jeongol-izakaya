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
        width: 95%;
        max-width: 400px;
        margin: 0 auto;
        padding: 16px;
      }

      .modal-section {
        margin-bottom: 12px;
      }

      .modal-section label {
        font-size: 14px;
      }

      .modal-section input,
      .modal-section textarea {
        font-size: 14px;
        padding: 8px;
      }

      /* Menu grid mobile adjustments */
      .main-menu-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 12px !important;
      }

      .menu-card {
        border-radius: 8px;
      }

      .menu-card .aspect-square {
        aspect-ratio: 1;
      }

      .menu-card img {
        height: 120px !important;
        width: 100% !important;
      }

      .menu-card .p-4 {
        padding: 12px !important;
      }

      .menu-card h5 {
        font-size: 13px !important;
        margin-bottom: 6px;
      }

      .menu-card p {
        font-size: 14px !important;
        font-weight: 600;
      }

      .flex.gap-4 {
        flex-direction: row;
        gap: 8px;
      }

      .flex.gap-4 button {
        flex: 1;
        width: auto;
        font-size: 13px;
        padding: 6px;
      }

      #orderSummary {
        font-size: 12px;
        padding: 8px;
        max-height: 150px;
      }

      #paymentModal .bg-white {
        width: 90%;
        max-width: 350px;
        padding: 16px;
      }

      #paymentModal h2 {
        font-size: 16px;
      }

      #paymentModal button {
        font-size: 14px;
        padding: 8px 12px;
      }

      #paymentModal label,
      #paymentModal input,
      #paymentModal span {
        font-size: 13px;
      }
    }

    /* General menu card styles (outside media query) */
    .main-menu-grid,
    .other-menu-grid {
      display: grid !important;
      grid-template-columns: repeat(3, 1fr) !important;
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
  <div id="tableModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl max-h-[80vh] overflow-y-auto">
      <div class="p-3">
        <div>
          <div class="flex items-center justify-between mb-4">
            <div></div>
            <h3 class="text-lg font-bold">Please Enter Reservation Details</h3>
            <span id="closeModal" class="close-modal text-3xl cursor-pointer">&times;</span>
          </div>
        </div>
        <form id="reservationForm" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label for="customerName">Customer</label>
              <input type="text" id="customerName" name="customer_name" placeholder="Enter your name" required
                class="w-full border rounded p-2" />
            </div>
            <div>
              <label for="contactNumber">Contact Number</label>
              <input type="number" id="contactNumber" name="contact_number" placeholder="09XXXXXXXXX" maxlength="11"
                class="w-full border rounded p-2" />
              <span class="text-red-500 text-sm hidden" id="contactError"></span>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label for="pax">Pax</label>
              <input type="number" id="pax" min="1" max="10" class="w-full border rounded p-2" />
            </div>
            <div>
              <label for="arrivalTimeInput">Arrival Time</label>
              <input type="time" id="arrivalTimeInput" required class="w-full border rounded p-2" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label for="reserved_date">Reservation Date</label>
              <input type="date" id="reserved_date" required class="w-full border rounded p-2" />
            </div>
            <div>
              <label for="advance_payment">Advance Payment <span class="text-red-600">(50% of your total
                  order)</span></label>
              <div class="flex items-center justify-start gap-2">
                <span
                  class="mt-1 text-lg font-semibold text-red-600 w-full border rounded p-2 bg-gray-50 flex items-center justify-start min-h-[42px]"
                  id="advance_payment_display">₱0.00</span>
                <input type="number" id="advance_payment" readonly class="hidden" />
              </div>
            </div>
          </div>

          <div>
            <label for="unavailableTime">Occupied Time</label>
            <select id="unavailableTime" name="unavailable_time" required class="w-full border rounded p-2">
              <option value="" selected>View Occupied Time</option>
            </select>
          </div>


          <div class="modal-section flex flex-col gap-6 w-full">
            @foreach(['main' => 'Main Menu', 'add_ons' => 'Add-ons', 'drinks' => 'Drinks', 'rice' => 'Rice'] as $key => $label)
            @if(isset($groupedMenu[$key]))
          <x-menu-category-grid :key="$key" :label="$label" :items="$groupedMenu[$key]" />
          @endif
      @endforeach
          </div>

          <div id="orderSummary" class="p-4 bg-white text-sm border rounded overflow-y-auto max-h-40">
            <h4 class="font-bold text-lg mb-4 text-start">Order Summary</h4>
            <ul id="selectedOrdersContainer" class="list-disc pl-5 text-gray-700"></ul>
          </div>

          <div class="flex justify-between items-center mt-3 pt-2 border-t font-bold">
            <div class="flex items-center gap-2">
              <p><span id="orderTotalLabel">Total: </span></p>
              <span id="orderTotalAmount" class="text-lg">₱0.00</span>
            </div>

            <button id="clearOrdersBtn" type="button"
              class="bg-red-600 hover:bg-red-700 text-white font-medium text-sm px-4 py-2 rounded-lg focus:outline-none focus:ring-4 focus:ring-red-300">
              Clear
            </button>
          </div>

          <div class="modal-section">
            <label for="notesTextarea">Notes</label>
            <textarea id="notesTextarea" name="notes" rows="2" placeholder="Enter any special requests or notes"
              class="w-full border rounded p-2"></textarea>
          </div>

          <input type="hidden" id="selectedTableNumber">

          <div class="flex justify-center border-t pt-4">
            <button type="button" id="paymentBtn"
              class="w-1/2 bg-blue-500 hover:bg-blue-600 text-white font-medium text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-4 focus:ring-blue-300">
              Proceed to payment
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="paymentModal" class="fixed inset-0 z-[1100] hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-xs p-3 relative">
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
    <p class="mt-2">&copy; 2025 Jeongol Izakaya. All rights reserved.</p>
  </footer>

  <div id="fly-animation-container"
    style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 9999;"></div>

  <script>
    class JeongolReservation {
      constructor() {
        this.selectedOrders = {};
        this.selectedTableNumber = 0;
        this.elements = {};
        this.submissionInProgress = false;
        this.init();
      }

      init() {
        document.addEventListener('DOMContentLoaded', () => {
          this.initializeElements();
          this.initializeTabs();
          this.initializeModals();
          this.initializeDateTimeInputs();
          this.initializeContactValidation();
          this.initializeEventListeners();
          this.updateOrderSummary();
          this.loadUnavailableTimes();

        });
      }

      initializeElements() {
        this.elements = {
          tableModal: document.getElementById('tableModal'),
          paymentModal: document.getElementById('paymentModal'),

          closeModal: document.getElementById('closeModal'),
          closePaymentModal: document.getElementById('closePaymentModal'),

          paymentBtn: document.getElementById('paymentBtn'),
          submitBtn: document.getElementById('submitBtn'),
          clearOrdersBtn: document.getElementById('clearOrdersBtn'),

          nameInput: document.getElementById('customerName'),
          contactInput: document.getElementById('contactNumber'),
          paxInput: document.getElementById('pax'),
          notesInput: document.getElementById('notesTextarea'),
          dateInput: document.getElementById('reserved_date'),
          timeInput: document.getElementById('arrivalTimeInput'),
          advancePaymentInput: document.getElementById('advance_payment'),
          selectedTableNumber: document.getElementById('selectedTableNumber'),

          selectedOrdersContainer: document.getElementById('selectedOrdersContainer'),
          totalQuantity: document.getElementById('totalQuantity'),
          totalPrice: document.getElementById('totalPrice'),

          tableLinks: document.querySelectorAll('.table-link')
        };
      }

      initializeEventListeners() {
        this.elements.clearOrdersBtn?.addEventListener('click', () => this.clearOrders());

        this.elements.submitBtn?.addEventListener('click', (e) => {
          e.preventDefault();
          this.submitReservation();
        });
      }

      initializeModals() {
        const { tableModal, closeModal, paymentModal, closePaymentModal, paymentBtn, tableLinks, selectedTableNumber } = this.elements;

        tableLinks.forEach(link => {
          link.addEventListener('click', e => {
            e.preventDefault();
            this.selectedTableNumber = parseInt(link.getAttribute('data-table-id'));
            selectedTableNumber.value = this.selectedTableNumber;
            this.showModal(tableModal);
          });
        });

        closeModal.addEventListener('click', () => {
          this.hideModal(tableModal);
          this.resetReservationForm();
        });

        paymentBtn.addEventListener('click', () => {
          const advancePayment = parseFloat(this.elements.advancePaymentInput.value) || 0;

          paymentModal.querySelectorAll('.tab-content .amount').forEach(input => {
            input.value = advancePayment;
            input.min = advancePayment;
            input.readOnly = true;
          });

          this.showModal(paymentModal);
        });

        closePaymentModal.addEventListener('click', () => {
          this.hideModal(paymentModal);
        });
      }

      showModal(modal) {
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      }

      hideModal(modal) {
        modal.style.display = 'none';
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }

      initializeTabs() {
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

      async loadUnavailableTimes() {
        try {
          const response = await fetch('/reservations/unavailable-times');
          if (!response.ok) throw new Error("Failed to fetch unavailable times");

          const reservations = await response.json();
          const select = document.getElementById('unavailableTime');

          select.innerHTML = '<option value="">Select unavailable time</option>';

          reservations.forEach(r => {
            const option = document.createElement('option');
            const start = new Date(r.reservation_time);
            const end = new Date(r.reservation_end_time);

            const startTime = start.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
            const endTime = end.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });

            option.value = r.id;
            option.textContent = `${startTime} - ${endTime}`;

            select.appendChild(option);
          });
        } catch (err) {
          console.error("Error loading unavailable times:", err);
        }
      }

      initializeDateTimeInputs() {
        const now = new Date();

        if (now.getHours() > 18 || (now.getHours() === 18 && now.getMinutes() >= 30)) {
          now.setDate(now.getDate() + 1);
        }

        this.elements.dateInput.value = now.toISOString().split('T')[0];

        const today = new Date();
        const maxDate = new Date();
        maxDate.setDate(today.getDate() + 2);

        this.elements.dateInput.min = today.toISOString().split('T')[0];
        this.elements.dateInput.max = maxDate.toISOString().split('T')[0];
      }


      selectMenuItem(card) {
        const img = card.querySelector('img');
        if (img) {
          this.animateFlyToCart(img, '#orderSummary');
        }

        const id = card.dataset.id;
        const name = card.dataset.name;
        const category = card.dataset.category;
        const price = parseFloat(card.dataset.price);

        if (!this.selectedOrders[id]) {
          this.selectedOrders[id] = {
            id, name, category, price,
            quantity: 1,
            total: price,
          };
        } else {
          this.selectedOrders[id].quantity += 1;
          this.selectedOrders[id].total = this.selectedOrders[id].quantity * price;
        }

        this.updateOrderSummary();
      }

      updateOrderSummary() {
        const container = this.elements.selectedOrdersContainer;
        container.innerHTML = '';

        const orderCount = Object.keys(this.selectedOrders).length;
        if (orderCount === 0) {
          container.innerHTML = 'No items selected';
          this.updateAdvancePayment();
          return;
        }

        this.createOrderHeader(container);
        const { total, totalQuantity } = this.createOrderRows(container);
        this.updateTotalDisplay(total, totalQuantity);
        this.updateAdvancePayment();
      }

      createOrderHeader(container) {
        const header = document.createElement('li');
        header.className = "grid grid-cols-3 mb-2 font-semibold border-b pb-1 text-sm";
        header.innerHTML = `
      <div>Menu</div>
      <div class="text-center">Qty</div>
      <div class="text-right">Subtotal</div>
    `;
        container.appendChild(header);
      }

      createOrderRows(container) {
        let total = 0;
        let totalQuantity = 0;

        Object.entries(this.selectedOrders).forEach(([id, item]) => {
          const itemTotal = item.price * item.quantity;
          total += itemTotal;
          totalQuantity += item.quantity;

          const row = document.createElement('li');
          row.className = "flex items-center justify-between mb-2 gap-2 py-1";
          row.innerHTML = `
        <div class="flex flex-col flex-1 min-w-0">
          <span class="truncate text-sm font-medium">${item.name}</span>
          <span class="text-xs text-gray-500">₱${item.price.toFixed(2)} each</span>
        </div>
        <div class="flex items-center gap-2">
          <input type="number" min="1" value="${item.quantity}" 
              class="w-14 text-center border border-gray-300 rounded text-black text-sm py-1"
              data-id="${id}"
              oninput="reservationSystem.updateQuantity(this)">
          <div class="text-sm font-semibold w-20 text-right">₱${itemTotal.toFixed(2)}</div>
          <button type="button" 
                  class="w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs font-bold transition-colors flex-shrink-0"
                  data-id="${id}"
                  onclick="reservationSystem.removeOrderItem(this)"
                  title="Remove item">
            ×
          </button>
        </div>
      `;
          container.appendChild(row);
        });

        return { total, totalQuantity };
      }

      updateTotalDisplay(total, totalQuantity) {
        const totalLabel = document.getElementById("orderTotalLabel");
        const totalAmount = document.getElementById("orderTotalAmount");

        if (totalLabel) {
          totalLabel.textContent = `Total (${totalQuantity} items): `;
        }
        if (totalAmount) {
          totalAmount.textContent = `₱${total.toFixed(2)}`;
        }
      }

      updateAdvancePayment() {
        const { total } = this.calculateTotals();
        const advancePayment = total * 0.5;

        const displayElement = document.getElementById('advance_payment_display');
        if (displayElement) {
          displayElement.textContent = `₱${advancePayment.toFixed(2)}`;
        }

        if (this.elements.advancePaymentInput) {
          this.elements.advancePaymentInput.value = advancePayment.toFixed(2);
        }
      }

      updateQuantity(input) {
        const id = input.dataset.id;
        const inputValue = input.value.trim();

        if (!this.selectedOrders[id] || inputValue === '') return;

        const newQuantity = parseInt(inputValue);

        if (isNaN(newQuantity) || newQuantity < 1) {
          input.value = this.selectedOrders[id].quantity;
          return;
        }

        this.selectedOrders[id].quantity = newQuantity;
        this.selectedOrders[id].total = this.selectedOrders[id].quantity * this.selectedOrders[id].price;

        const subtotalSpan = input.closest('li').querySelector('.text-right');
        if (subtotalSpan) {
          subtotalSpan.textContent = `₱${this.selectedOrders[id].total.toFixed(2)}`;
        }

        this.updateRealTimeTotals();
      }

      removeOrderItem(button) {
        const id = button.dataset.id;
        const listItem = button.closest('li');

        if (this.selectedOrders[id]) {
          delete this.selectedOrders[id];
          listItem.remove();

          if (Object.keys(this.selectedOrders).length === 0) {
            this.elements.selectedOrdersContainer.innerHTML = 'No items selected';
          }

          this.updateRealTimeTotals();
        }
      }

      updateRealTimeTotals() {
        const { total, totalQuantity } = this.calculateTotals();
        this.updateTotalDisplay(total, totalQuantity);
        this.updateAdvancePayment();
      }

      calculateTotals() {
        let total = 0;
        let totalQuantity = 0;

        Object.values(this.selectedOrders).forEach(item => {
          const itemTotal = item.price * item.quantity;
          total += itemTotal;
          totalQuantity += item.quantity;
          item.total = itemTotal;
        });

        return { total, totalQuantity };
      }

      clearOrders() {
        Object.keys(this.selectedOrders).forEach(k => delete this.selectedOrders[k]);
        this.elements.selectedOrdersContainer.innerHTML = 'No items selected';
        this.updateTotalDisplay(0, 0);
        this.updateAdvancePayment();
      }

      initializeContactValidation() {
        const { contactInput } = this.elements;
        const error = document.getElementById('contactError');

        if (!contactInput || !error) {
          console.warn('Contact input or error element not found');
          return;
        }

        contactInput.addEventListener('input', () => {
          let value = contactInput.value.replace(/\D/g, '');

          if (value.length > 11) {
            value = value.slice(0, 11);
          }

          if (value && !/^09\d{0,9}$/.test(value)) {
            error.textContent = 'Enter a valid contact number (09XXXXXXXXX)';
            error.classList.remove('hidden');
            contactInput.classList.add('input-error');
          } else {
            error.textContent = '';
            error.classList.add('hidden');
            contactInput.classList.remove('input-error');
          }
          contactInput.value = value;
        });

        ['keydown', 'paste'].forEach(eventType => {
          contactInput.addEventListener(eventType, this.handleContactInput.bind(this));
        });
      }

      handleContactInput(e) {
        const currentValue = this.elements.contactInput.value.replace(/\D/g, '');

        if (e.type === 'keydown') {
          if (currentValue.length >= 11 &&
            !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(e.key) &&
            !e.ctrlKey && !e.metaKey) {
            e.preventDefault();
          }

          if (!/\d/.test(e.key) &&
            !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(e.key) &&
            !e.ctrlKey && !e.metaKey) {
            e.preventDefault();
          }
        }

        if (e.type === 'paste') {
          e.preventDefault();
          const paste = (e.clipboardData || window.clipboardData).getData('text');
          const numbersOnly = paste.replace(/\D/g, '').slice(0, 11);
          this.elements.contactInput.value = numbersOnly;
          this.elements.contactInput.dispatchEvent(new Event('input'));
        }
      }

      validateInputs() {
        let hasError = false;

        const requiredFields = [
          this.elements.nameInput,
          this.elements.contactInput,
          this.elements.paxInput,
          this.elements.timeInput,
          this.elements.dateInput
        ];

        requiredFields.forEach(field => {
          if (!field.value.trim()) {
            field.classList.add('input-error');
            hasError = true;
          } else {
            field.classList.remove('input-error');
          }
        });

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
                hasError = true;
              } else {
                field.classList.remove('input-error');
              }
            }
          });
        }

        return !hasError;
      }

      gatherReservationData() {
        const formData = new FormData();
        const activeTab = document.querySelector(".tab-content:not(.hidden)");

        if (!activeTab) {
          console.error('No active payment tab found');
          return formData;
        }

        const method = activeTab.id.includes("gcash") ? "Gcash" : "Maya";

        const orders = Object.values(this.selectedOrders);
        if (orders.length > 0) {
          const generalNotes = this.elements.notesInput.value.trim();

          orders.forEach((item, index) => {
            formData.append(`orders[${index}][menu_id]`, item.id);
            formData.append(`orders[${index}][quantity]`, item.quantity);
            formData.append(`orders[${index}][notes]`, generalNotes);
          });
        }

        const basicData = {
          table_id: this.selectedTableNumber,
          customer_name: this.elements.nameInput.value.trim(),
          contact_number: this.elements.contactInput.value.trim(),
          pax: parseInt(this.elements.paxInput.value) || 1,
          reserved_date: this.elements.dateInput.value,
          arrival_time: this.elements.timeInput.value,
          advance_payment: parseFloat(this.elements.advancePaymentInput.value.trim()) || 0,
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

      async submitReservation() {

        if (this.submissionInProgress) {
          this.showMessageBox('Please wait, processing your reservation...', 'warning');
          return;
        }
        if (!this.validateInputs()) {
          this.showMessageBox('Please complete all required fields.', 'error');
          return;
        }

        const data = this.gatherReservationData();
        const submitBtn = this.elements.submitBtn;

        submitBtn.disabled = true;
        submitBtn.textContent = "Submitting...";

        await new Promise(resolve => setTimeout(resolve, Math.random() * 100));

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
            this.showMessageBox("Server error: Invalid response format", "error");
            return;
          }

          const json = await response.json();

          if (response.ok && json.success) {
            this.showMessageBox("Reservation successful!", "success");
            this.resetReservationForm();

            this.hideModal(this.elements.tableModal);
            this.hideModal(this.elements.paymentModal);
          } else {
            if (response.status === 409) {
              this.showMessageBox("Sorry! This time slot was just reserved by another customer. Please select a different time.", "error");
            } else {
              const errors = json.errors || {};
              const messages = Object.values(errors).flat().join("\n");
              this.showMessageBox(messages || json.message || "Reservation failed", "error");
            }
          }
        } catch (error) {
          this.showMessageBox("Network error: " + error.message, "error");
        } finally {
          submitBtn.disabled = false;
          submitBtn.textContent = "Submit Reservation";
        }
      }

      resetPaymentModal() {
        document.querySelectorAll('.tab-content').forEach(tabContent => {
          const inputs = tabContent.querySelectorAll('input:not([type="file"])');
          inputs.forEach(input => {
            input.value = '';
            input.classList.remove('input-error');
            input.readOnly = false;
            input.removeAttribute('min');
          });

          const fileInputs = tabContent.querySelectorAll('input[type="file"]');
          fileInputs.forEach(fileInput => {
            fileInput.value = '';
            fileInput.classList.remove('input-error');
          });
        });
      }

      resetReservationForm() {
        [this.elements.nameInput, this.elements.contactInput, this.elements.dateInput,
        this.elements.timeInput, this.elements.notesInput].forEach(el => {
          if (el) {
            el.value = '';
            el.classList.remove('input-error');
          }
        });

        if (this.elements.paxInput) {
          this.elements.paxInput.value = '';
          this.elements.paxInput.classList.remove('input-error');
        }

        this.selectedTableNumber = 0;
        if (this.elements.selectedTableNumber) this.elements.selectedTableNumber.value = '';

        Object.keys(this.selectedOrders).forEach(k => delete this.selectedOrders[k]);

        this.elements.selectedOrdersContainer.innerHTML = 'No items selected';

        this.updateTotalDisplay(0, 0);

        if (this.elements.advancePaymentInput) {
          this.elements.advancePaymentInput.value = '0.00';
        }
        const displayElement = document.getElementById('advance_payment_display');
        if (displayElement) {
          displayElement.textContent = '₱0.00';
        }

        this.resetPaymentModal();

        this.initializeDateTimeInputs();
      }

      animateFlyToCart(imageEl, targetSelector) {
        const imgRect = imageEl.getBoundingClientRect();
        const targetEl = document.querySelector(targetSelector);

        if (!targetEl) {
          console.warn(`Target element ${targetSelector} not found`);
          return;
        }

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

      showMessageBox(message, type = 'success') {
        const box = document.getElementById('messageBox');
        box.textContent = message;

        const colors = {
          success: '#4CAF50',
          error: '#f44336',
          warning: '#ff9800',
          info: '#2196F3'
        };

        box.style.background = colors[type] || colors.success;
        box.style.display = 'block';

        const timeout = (type === 'error' || type === 'warning') ? 5000 : 3000;

        setTimeout(() => {
          box.style.display = 'none';
        }, timeout);
      }
    }

    window.customer_jeongolConfig = {
      storeReservationUrl: "{{ route('customer.reserve') }}",
      csrfToken: "{{ csrf_token() }}"
    };

    const reservationSystem = new JeongolReservation();

    window.selectMenuItem = function (card) {
      reservationSystem.selectMenuItem(card);
    };

  </script>
</body>

</html>
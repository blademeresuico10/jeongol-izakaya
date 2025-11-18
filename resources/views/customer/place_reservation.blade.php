<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Jeongol Izakaya</title>
  <link rel="shortcut icon" type="x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
<link 
  rel="stylesheet" 
  href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  @livewireStyles
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
    .table.pending {
      background-color: #64a0ea !important;
      cursor: pointer;
    }
    .table.booked {
      background-color: #6c757d !important;
      cursor: not-allowed !important;
      opacity: 0.7;
    }
    .table.booked:hover {
      background-color: #5a6268 !important;
      transform: none !important;
    }
    .booked-label {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background-color: rgba(0, 0, 0, 0.7);
      color: white;
      padding: 4px 12px;
      border-radius: 4px;
      font-size: 12px;
      font-weight: bold;
      pointer-events: none;
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
  </style>
</head>
<body>
  <header class="bg-black/60 text-white flex items-center px-4 py-3 shadow-md relative">
  <a href="{{ route('customer.index') }}" 
     class="absolute left-4 flex items-center text-white">
    <i class="bi bi-arrow-left-circle-fill text-3xl"></i>
  </a>
  <h1 class="mx-auto text-lg sm:text-xl font-semibold text-center">
    Welcome to <strong>Jeongol Izakaya</strong>
  </h1>
</header>
  <div class="flex flex-col lg:flex-row justify-center gap-6 px-4 mt-3">
    <section class="w-full sm:w-96 lg:w-80 xl:w-96">
        <div class="w-full bg-white/90 text-gray-800 rounded-xl shadow-2xl p-4 sm:p-6 border border-gray-200 h-auto lg:h-[223px] flex flex-col">
            <h2 class="text-lg sm:text-xl font-bold mb-4 text-center text-indigo-600 border-b pb-2 border-indigo-200">Choose Time & Date</h2>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="flex flex-col">
                    <label for="date" class="text-gray-700 text-sm font-semibold mb-2">Date:</label>
                    <input type="date" id="date" name="date"
                        class="w-full px-3 py-2 rounded-lg border border-indigo-300 text-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150"
                        min="{{ date('Y-m-d') }}" 
                        value="{{ date('Y-m-d') }}">
                </div>
                <div class="flex flex-col">
                    <label for="time" class="text-gray-700 text-sm font-semibold mb-2">Time:</label>
                    <input type="time" id="time" name="time"
                        class="w-full px-3 py-2 rounded-lg border border-indigo-300 text-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150">
                </div>
            </div>
            <div id="operatingHoursAlert" class="mt-auto p-3 rounded-lg text-xs sm:text-sm text-center w-full bg-blue-100 text-blue-700 border border-blue-300">
                <p id="operatingHoursMessage">Checking operating hours...</p>
            </div>
        </div>
    </section>
    <section class="w-full sm:w-96 lg:w-80 xl:w-96">
        <div class="w-full bg-white/90 text-gray-800 rounded-xl shadow-2xl p-4 sm:p-6 border border-gray-200 h-auto lg:h-[223px] flex flex-col">
            <h3 class="text-lg sm:text-xl font-bold mb-4 text-center text-red-600 border-b pb-2 border-red-200">Unavailable Time Slots</h3>

            <div id="unavailableTimesList" class="flex-grow overflow-y-auto pr-2">
                <p class="text-gray-500 text-sm text-center italic">Select a date to view unavailable slots</p>
            </div>
        </div>
    </section>
</div>
  <div class="table-layout grid grid-cols-2 gap-4">
    @foreach($tables as $table)
    <div class="table-link cursor-pointer pointer-events-none" data-table-id="{{ $table->id }}"
      data-table-number="{{ $table->table_number }}">

      <div class="table available relative flex flex-col items-center justify-center h-50 border rounded bg-green-100">
      <div class="absolute top-1 text-xs">{{ $table->capacity }} Pax</div>
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
                minlength="3" class="w-full border rounded p-2" onkeypress="return /[a-zA-Z\s\-'\.]/i.test(event.key)"
                oninput="this.value = this.value.replace(/[^a-zA-Z\s\-'\.]/g, '')" />
              <small id="customerNameError" class="text-red-600 text-sm hidden"></small>
            </div>
            <div>
              <label for="email">Email</label>
              <input type="email" id="email" name="email" placeholder="Enter your email" required
                class="w-full border rounded p-2" />
              <span class="text-red-500 text-sm hidden" id="emailError"></span>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label for="pax">Pax</label>
              <input type="text" id="pax" name="pax" class="w-full border rounded p-2" required
                onkeypress="return /[0-9]/i.test(event.key)" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                pattern="[0-9]+" inputmode="numeric" />
            </div>

            <div>
              <label for="arrivalTimeInput">Arrival Time</label>
              <input type="time" id="arrivalTimeInput" required class="w-full border rounded p-2" readonly />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label for="reserved_date">Reservation Date</label>
              <input type="input" id="reserved_date" required class="w-full border rounded p-2" readonly />
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


          <div class="modal-section flex flex-col gap-6 w-full">
    @foreach($menuCategories as $category)
        @if(isset($groupedMenu[$category->name]))
            <x-menu-category-grid 
                :key="strtolower(str_replace(' ', '_', $category->name))" 
                :label="$category->name" 
                :items="$groupedMenu[$category->name]" 
            />
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

  <div id="paymentModal" class="fixed inset-0 z-[1100] hidden flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-4 relative">
      <button id="closePaymentModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">✕</button>
      <h2 class="text-xl font-semibold text-center mb-4">Payment</h2>

      <div class="flex border-b mb-4">
        <button type="button" data-tab="gcash"
          class="payment-tab flex-1 text-center py-2 font-semibold text-gray-500">GCash</button>
        <button type="button" data-tab="maya"
          class="payment-tab flex-1 text-center py-2 font-semibold text-gray-500">Maya</button>
      </div>

      <input type="hidden" name="ewallet_number" id="selectedPaymentMethod" />

      <div class="tab-wrapper">
        <x-payment-form method="gcash" :data="['amount' => 0]" />
        <x-payment-form method="maya" :data="['amount' => 0]" />
      </div>

      <button id="submitBtn" type="button"
        class="mt-4 w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
        Submit Reservation
      </button>
    </div>
  </div>

  <div id="operatingHoursModal" class="fixed inset-0 hidden bg-black/80 items-center justify-center p-4 z-50"
    style="backdrop-filter: blur(8px);">
    <div
      class="bg-gradient-to-br from-red-900 to-red-800 rounded-2xl shadow-2xl w-full max-w-md border-2 border-red-500">
      <div class="p-6 text-center">
        <div class="mb-4 flex justify-center">
          <div class="w-16 h-16 bg-red-500 rounded-full flex items-center justify-center">
            <i class="fas fa-clock text-white text-3xl"></i>
          </div>
        </div>
        <h2 class="text-2xl font-bold text-white mb-3">Closed</h2>
        <p id="modalOperatingMessage" class="text-gray-200 mb-6 text-base leading-relaxed">
        </p>
        <div class="space-y-3">
          <button onclick="closeModal('operatingHoursModal')"
            class="w-full px-6 py-3 bg-white text-red-900 rounded-lg font-bold hover:bg-gray-100 transition">
            Got it
          </button>

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
  <div id="fly-animation-container"
    style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 9999;"></div>

  @livewireScripts
</body>
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
        this.initializeCustomerNameValidation()
        this.initializeEmailValidation();
        this.initializeEventListeners();
        this.initializeAvailabilitySearch();
        this.updateOrderSummary();
        this.initializeDatePicker();
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
        emailInput: document.getElementById('email'),
        nameInput: document.getElementById('customerName'),
        paxInput: document.getElementById('pax'),
        notesInput: document.getElementById('notesTextarea'),
        dateInput: document.getElementById('reserved_date'),
        timeInput: document.getElementById('arrivalTimeInput'),
        advancePaymentInput: document.getElementById('advance_payment'),
        selectedTableNumber: document.getElementById('selectedTableNumber'),
        selectedOrdersContainer: document.getElementById('selectedOrdersContainer'),
        totalQuantity: document.getElementById('totalQuantity'),
        totalPrice: document.getElementById('totalPrice'),
        searchDateInput: document.getElementById('date'),
        searchTimeInput: document.getElementById('time'),
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
    initializeAvailabilitySearch() {
      const { searchDateInput, searchTimeInput } = this.elements;
      if (!searchDateInput || !searchTimeInput) return;
      const today = new Date();
      const yyyy = today.getFullYear();
      const mm = String(today.getMonth() + 1).padStart(2, '0');
      const dd = String(today.getDate()).padStart(2, '0');
      const formattedDate = `${yyyy}-${mm}-${dd}`;
      searchDateInput.value = formattedDate;
      searchDateInput.min = formattedDate;
      searchDateInput.addEventListener('change', () => this.checkAvailability());
      searchTimeInput.addEventListener('change', () => this.checkAvailability());
      searchTimeInput.addEventListener('input', () => {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => this.checkAvailability(), 500);
      });
    }
    initializeDatePicker() {
      const dateInput = document.getElementById('date');
      if (!dateInput) return;
      const today = new Date();
      const maxDate = new Date();
      maxDate.setDate(today.getDate() + 2);
      const formatDate = (date) => date.toISOString().split('T')[0];
      dateInput.min = formatDate(today);
      dateInput.max = formatDate(maxDate);
    }
    async checkAvailability() {
      const date = this.elements.searchDateInput?.value;
      const time = this.elements.searchTimeInput?.value;
      if (!date || !time) {
        this.resetTableAvailability();
        return;
      }
      try {
        const response = await fetch(`/customer/check-availability?date=${date}&time=${time}`);

        if (!response.ok) {
          throw new Error('Failed to check availability');
        }
        const data = await response.json();
        this.currentTableData = data.tables;
        this.updateTableAvailability(data.tables);

      } catch (error) {
        console.error('Error checking availability:', error);
        this.showMessageBox('Failed to check table availability', 'error');
      }
    }
    isAcceptableTime(dateValue, timeValue) {
      if (!dateValue || !timeValue) return false;
      const selectedDateTime = new Date(`${dateValue}T${timeValue}`);
      const now = new Date();
      const acceptableTime = new Date(now.getTime() + 2 * 60 * 60 * 1000);
      return selectedDateTime >= acceptableTime;
    }
    async updateTableAvailability(tables) {
      const dateInput = this.elements.searchDateInput;
      const timeInput = this.elements.searchTimeInput;
      const date = dateInput?.value;
      const time = timeInput?.value;
      const allowClick = date && time && this.isAcceptableTime(date, time);
      const withinOperatingHours = await this.checkOperatingHoursForTables(date, time);
      if (date && time && !allowClick) {
        [dateInput, timeInput].forEach(input => input.classList.add('border-red-500'));
        this.showMessageBox('Please reserve a table at least 2 hours before your arrival.', 'warning');
      } else if (date && time && !withinOperatingHours) {
        [dateInput, timeInput].forEach(input => input.classList.add('border-red-500'));
        this.showMessageBox('Selected time is closed.', 'warning');
      } else {
        [dateInput, timeInput].forEach(input => input.classList.remove('border-red-500'));
      }
      const canSelectTable = allowClick && withinOperatingHours;
      this.elements.tableLinks.forEach(link => {
        const tableId = parseInt(link.getAttribute('data-table-id'));
        const tableData = tables.find(t => t.id === tableId);
        if (!tableData) return;
        const tableDiv = link.querySelector('.table');
        let existingLabel = tableDiv.querySelector('.booked-label');
        tableDiv.classList.remove('booked', 'pending', 'available', 'bg-green-100', 'bg-gray-300', 'bg-blue-200', 'bg-gray-400');
        if (tableData.is_pending) {
          tableDiv.classList.add('pending', 'bg-blue-200');
          link.style.pointerEvents = 'auto';
          if (!existingLabel) {
            existingLabel = document.createElement('div');
            existingLabel.className = 'booked-label';
            tableDiv.appendChild(existingLabel);
          }
          existingLabel.textContent = 'Processing';

        } else if (tableData.is_active) {
          tableDiv.classList.add('booked', 'bg-gray-300');
          link.style.pointerEvents = 'none';

          if (!existingLabel) {
            existingLabel = document.createElement('div');
            existingLabel.className = 'booked-label';
            tableDiv.appendChild(existingLabel);
          }
          existingLabel.textContent = 'Reserved';
        } else {
          if (!withinOperatingHours && date && time) {
            tableDiv.classList.add('booked', 'bg-gray-400');
            link.style.pointerEvents = 'none';
            if (!existingLabel) {
              existingLabel = document.createElement('div');
              existingLabel.className = 'booked-label';
              tableDiv.appendChild(existingLabel);
            }
            existingLabel.textContent = 'Closed';
          } else {
            tableDiv.classList.add('available', 'bg-green-100');
            link.style.pointerEvents = canSelectTable ? 'auto' : 'none';

            if (existingLabel) {
              existingLabel.remove();
            }
          }
        }
      });
    }
    async checkOperatingHoursForTables(date, time) {
      if (!date || !time) {
        return true; 
      }
      try {
        const response = await fetch('{{ route("customer.check_operating_hours") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({ date, time })
        });

        const data = await response.json();
        return data.is_open === true;
      } catch (error) {
        console.error('Error checking operating hours:', error);
        return true; 
      }
    }
    resetTableAvailability() {
      const dateInput = this.elements.searchDateInput;
      const timeInput = this.elements.searchTimeInput;
      const date = dateInput?.value;
      const time = timeInput?.value;
      const allowClick = date && time && this.isAcceptableTime(date, time);
      if (date && time && !allowClick) {
        [dateInput, timeInput].forEach(input => input.classList.add('border-red-500'));
        this.showMessageBox('Please select a time at least 2 hours from now.');
      } else {
        [dateInput, timeInput].forEach(input => input.classList.remove('border-red-500'));
      }
      this.elements.tableLinks.forEach(link => {
        const tableDiv = link.querySelector('.table');
        const existingLabel = tableDiv.querySelector('.booked-label');

        tableDiv.classList.remove('booked');
        tableDiv.classList.add('available', 'bg-green-100');
        link.style.pointerEvents = allowClick ? 'auto' : 'none';

        if (existingLabel) existingLabel.remove();
      });
    }

    initializeModals() {
      const {
        tableModal,
        closeModal,
        paymentModal,
        closePaymentModal,
        paymentBtn,
        tableLinks,
        selectedTableNumber
      } = this.elements;

      const tableCapacities = {
        @foreach($tables as $table)
        {{ $table->id }}: {{ $table->capacity }},
    @endforeach
  };

  tableLinks.forEach(link => {
          link.addEventListener('click', e => {
            e.preventDefault();
            const tableId = parseInt(link.getAttribute('data-table-id'));
            const tableData = this.currentTableData?.find(t => t.id === tableId);
            if (tableData && tableData.is_pending) {
              this.showMessageBox("This table is currently being processed for reservation. Please select a different time or table.", "warning");
              return;
            }
            this.selectedTableNumber = tableId;
            selectedTableNumber.value = this.selectedTableNumber;
            const paxInput = document.getElementById('pax');
            const capacity = tableCapacities[this.selectedTableNumber];
            if (paxInput && capacity) {
              paxInput.value = capacity;
              paxInput.max = capacity;
              paxInput.min = capacity - 1;
              paxInput.readOnly = false;
              paxInput.addEventListener('input', function () {
                const value = parseInt(this.value);
                if (value > capacity) {
                  this.value = capacity;
                } else if (value < 1) {
                  this.value = 1;
                }
              });
            }
            const searchDate = this.elements.searchDateInput?.value;
            const searchTime = this.elements.searchTimeInput?.value;
            if (searchDate) {
              const reservedDateInput = document.getElementById('reserved_date');
              if (reservedDateInput) {
                reservedDateInput.value = searchDate;
              }
            }
            if (searchTime) {
              const arrivalTimeInput = document.getElementById('arrivalTimeInput');
              if (arrivalTimeInput) {
                arrivalTimeInput.value = searchTime;
              }
            }
            this.showModal(tableModal);
          });
});

  closeModal.addEventListener('click', () => {
    this.hideModal(tableModal);
    this.resetReservationForm();
  });
  paymentBtn.addEventListener('click', async () => {
    const orders = Object.values(this.selectedOrders);
    const hasMain = orders.some(item => item.category === 'Main Course');
    const nameInput = document.getElementById('customerName');
    const emailInput = document.getElementById('email');
    const dateInput = document.getElementById('reserved_date');
    const timeInput = document.getElementById('arrivalTimeInput');
    const paxInput = document.getElementById('pax');
    [nameInput, emailInput, dateInput, timeInput, paxInput].forEach(f => f.classList.remove('border-red-500'));
    const requiredFields = [nameInput, emailInput, dateInput, timeInput];
    const emptyFields = requiredFields.filter(f => !f.value.trim());
    if (emptyFields.length === requiredFields.length && !hasMain) {
      emptyFields.forEach(f => f.classList.add('border-red-500'));
      this.showMessageBox('You must fill out all required fields.', 'error');
      return;
    }
    if (emptyFields.length > 0) {
      emptyFields.forEach(f => f.classList.add('border-red-500'));
      this.showMessageBox('Please fill out all required fields.', 'error');
      return;
    }
    if (!this.validateEmail(emailInput.value.trim())) {
      emailInput.classList.add('border-red-500');
      this.showMessageBox('Please enter a valid email address.', 'error');
      return;
    }
    try {
      const response = await fetch(`{{ route('customer.check_email') }}?email=${encodeURIComponent(emailInput.value.trim())}`);
      const data = await response.json();

      if (!data.valid) {
        emailInput.classList.add('border-red-500');
        this.showMessageBox('This email address does not exist or is invalid.', 'error');
        return;
      }
    } catch (error) {
      emailInput.classList.add('border-red-500');
      this.showMessageBox('Error validating email. Please try again.', 'error');
      return;
    }

    if (!hasMain) {
      this.showMessageBox('You must choose at least one item in the main menu.', 'error');
      return;
    }

    const pax = parseInt(paxInput?.value || 0);
    const mainCount = orders
      .filter(item => item.category === 'Main Course')
      .reduce((sum, item) => sum + (item.quantity || 1), 0);

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
  document.querySelectorAll('[data-tab]').forEach(tabButton => {
    tabButton.addEventListener('click', (e) => {
      const targetTab = e.target.dataset.tab;

      document.querySelectorAll('[data-tab]').forEach(btn =>
        btn.classList.remove('font-bold', 'border-b-2', 'border-blue-500')
      );
      e.target.classList.add('font-bold', 'border-b-2', 'border-blue-500');

      document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.toggle('hidden', content.id !== `tab-${targetTab}`);
      });
    });
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

        const activeTab = document.getElementById(`tab-${tabName}`);
        activeTab?.classList.remove('hidden');

        if (activeTab) {
          activeTab.querySelectorAll('input:not([type="hidden"]):not(.amount)').forEach(input => {
            if (input.type !== 'file') {
              input.value = '';
            } else {
              input.value = '';
            }
            input.classList.remove('input-error');
          });
        }

        const ewalletEl = document.querySelector(`#tab-${tabName} .ewallet-id`);
        document.getElementById('selectedPaymentMethod').value = ewalletEl ? ewalletEl.value : '';
      });
    });

    const active = document.querySelector('.payment-tab.active');
    if (active) {
      const tabName = active.dataset.tab;
      const ewalletEl = document.querySelector(`#tab-${tabName} .ewallet-id`);
      document.getElementById('selectedPaymentMethod').value = ewalletEl ? ewalletEl.value : '';
    }
  }
      async loadUnavailableTimes(tableId = null) {
    try {
      if (!tableId) {
        const select = document.getElementById('unavailableTime');
        select.innerHTML = '<option value="">Check unavailable time</option>';
        return;
      }
      const response = await fetch(`/reservations/unavailable-times?table_id=${tableId}`);
      if (!response.ok) throw new Error("Failed to fetch unavailable times");
      const reservations = await response.json();
      const select = document.getElementById('unavailableTime');
      select.innerHTML = '<option value="">Check unavailable time</option>';
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
  initializeCustomerNameValidation() {
    const customerNameInput = document.getElementById('customerName');
    const customerNameError = document.getElementById('customerNameError');
    if (!customerNameInput || !customerNameError) return;
    customerNameInput.addEventListener('input', () => {
      customerNameInput.value = customerNameInput.value.replace(/[^a-zA-Z\s\-'\.]/g, '');
      const value = customerNameInput.value.trim();
      if (!value) {
        customerNameError.textContent = '';
        customerNameError.classList.add('hidden');
        customerNameInput.classList.remove('input-error');
        return;
      }
      if (value.length < 3) {
        customerNameError.textContent = 'Minimum 3 characters required';
        customerNameError.classList.remove('hidden');
        customerNameInput.classList.add('input-error');
      } else {
        customerNameError.textContent = '';
        customerNameError.classList.add('hidden');
        customerNameInput.classList.remove('input-error');
      }
    });
  }
  initializeEmailValidation() {
    const emailInput = document.getElementById('email');
    const emailError = document.getElementById('emailError');
    if (!emailInput || !emailError) return;
    let debounceTimer;
    let lastInvalid = true;
    emailInput.addEventListener('input', () => {
      const value = emailInput.value.trim();
      clearTimeout(debounceTimer);
      if (!value) {
        emailError.textContent = 'Email is required';
        emailError.classList.remove('hidden');
        emailInput.classList.add('input-error');
        lastInvalid = true;
        return;
      }
      if (!this.validateEmail(value)) {
        emailError.textContent = 'Enter a valid email address';
        emailError.classList.remove('hidden');
        emailInput.classList.add('input-error');
        lastInvalid = true;
        return;
      }
      debounceTimer = setTimeout(() => {
        fetch(`{{ route('customer.check_email') }}?email=${encodeURIComponent(value)}`)
          .then(response => response.json())
          .then(data => {
            if (!data.valid) {
              emailError.textContent = 'This email address does not exist.';
              emailError.classList.remove('hidden');
              emailInput.classList.add('input-error');
              lastInvalid = true;
            } else {
              emailError.textContent = '';
              emailError.classList.add('hidden');
              emailInput.classList.remove('input-error');
              lastInvalid = false;
            }
          })
          .catch(() => {
            emailError.textContent = 'Error checking email. Please try again.';
            emailError.classList.remove('hidden');
            emailInput.classList.add('input-error');
            lastInvalid = true;
          });
      }, 600);
    });
  }
  validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }
  validateInputs() {
    let hasError = false;
    const requiredFields = [
      this.elements.nameInput,
      this.elements.emailInput,
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
      const numberField = activeTab.querySelector(".gcash-number, .maya-number");
      const nameField = activeTab.querySelector(".registered-name");
      const amountField = activeTab.querySelector(".amount");
      const proofField = activeTab.querySelector(".proof");
      if (numberField) {
        if (!numberField.value.trim()) {
          numberField.classList.add('input-error');
          hasError = true;
        } else {
          numberField.classList.remove('input-error');
        }
      }
      if (nameField) {
        if (!nameField.value.trim()) {
          nameField.classList.add('input-error');
          hasError = true;
        } else {
          nameField.classList.remove('input-error');
        }
      }

      if (amountField) {
        if (!amountField.value.trim()) {
          amountField.classList.add('input-error');
          hasError = true;
        } else {
          amountField.classList.remove('input-error');
        }
      }

      if (proofField) {
        const hasFile = proofField.files && proofField.files.length > 0;
        if (!hasFile) {
          proofField.classList.add('input-error');
          hasError = true;
        } else {
          proofField.classList.remove('input-error');
        }
      }
    }
    return !hasError;
  }
  gatherReservationData() {
    const formData = new FormData();
    const activeTab = document.querySelector(".tab-content:not(.hidden)");
    if (!activeTab) return { formData, hasOrders: false };
    const method = activeTab.id.includes("gcash") ? "gcash" : "maya";
    const orders = Object.values(this.selectedOrders);
    const hasOrders = orders.length > 0;
    if (hasOrders) {
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
      email: this.elements.emailInput.value.trim(),
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

    const ewalletId = document.querySelector(`#tab-${method} .ewallet-id`)?.value || '';
    formData.append('ewallet_id', ewalletId);

    const registeredNumber = activeTab.querySelector(".gcash-number, .maya-number")?.value;
    if (registeredNumber) {
      formData.append('registered_number', registeredNumber.trim());
    }

    const registeredName = activeTab.querySelector(".registered-name")?.value;
    if (registeredName) {
      formData.append('registered_name', registeredName.trim());
    }

    const proofInput = activeTab.querySelector(".proof");
    if (proofInput?.files?.[0]) {
      formData.append('payment_proof', proofInput.files[0]);
    }

    return { formData, hasOrders };
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
    const { formData, hasOrders } = this.gatherReservationData();
    if (!hasOrders) {
      this.showMessageBox('Please add at least one order before submitting.', 'error');
      return;
    }
    const submitBtn = this.elements.submitBtn;
    submitBtn.disabled = true;
    submitBtn.textContent = "Submitting...";
    this.submissionInProgress = true;
    try {
      const response = await fetch("/customer/reserve", {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
          "Accept": "application/json"
        },
        body: formData
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
      } else if (response.status === 409) {
        this.showMessageBox("This table is currently being processed for reservation. Please select a different time or table..", "warning");
      } else {
        const errors = json.errors || {};
        const messages = Object.values(errors).flat().join("\n");
        this.showMessageBox(messages || json.message || "Reservation failed", "error");
      }

    } catch (error) {
      this.showMessageBox("Network error: " + error.message, "error");
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = "Submit Reservation";
      this.submissionInProgress = false;
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
    [this.elements.nameInput, this.elements.emailInput, this.elements.dateInput,
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
  alertMessageBox(message, type = 'alert') {
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

<script>
  let operatingCheckTimeout;

  document.getElementById('date').addEventListener('change', function() {
    checkOperatingHoursDebounced();
    fetchUnavailableSlots();
  });
  
  document.getElementById('time').addEventListener('change', checkOperatingHoursDebounced);

  function checkOperatingHoursDebounced() {
    clearTimeout(operatingCheckTimeout);
    operatingCheckTimeout = setTimeout(checkOperatingHours, 500);
  }

  async function checkOperatingHours(autoCheck = false) {
    const date = document.getElementById('date')?.value;
    const time = document.getElementById('time')?.value;
    const alert = document.getElementById('operatingHoursAlert');
    const message = document.getElementById('operatingHoursMessage');

    try {
  const response = await fetch('{{ route("customer.check_operating_hours") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify(autoCheck ? {} : { date, time })
  });

  const data = await response.json();
  alert.classList.remove('hidden');

  if (autoCheck || !date || !time) {
    if (data.open_time === 'Closed' && data.close_time === 'Closed') {
      alert.classList.remove('bg-blue-100', 'text-blue-700', 'border-blue-300', 'bg-green-100', 'text-green-800', 'border-green-300');
      alert.classList.add('bg-red-100', 'text-red-800', 'border-red-300');
      message.innerHTML = 'Closed!';
    } else {
      alert.classList.remove('bg-red-100', 'text-red-800', 'border-red-300', 'bg-green-100', 'text-green-800', 'border-green-300');
      alert.classList.add('bg-blue-100', 'text-blue-700', 'border-blue-300');
      message.innerHTML = `You can only reserve at: ${data.open_time} - ${data.close_time}`;
    }
  } else {
    if (data.is_open) {
      alert.classList.remove('bg-red-100', 'text-red-800', 'border-red-300', 'bg-blue-100', 'text-blue-700', 'border-blue-300');
      alert.classList.add('bg-green-100', 'text-green-800', 'border-green-300');
      message.innerHTML = `Selected time is available (${data.open_time} - ${data.close_time})`;
    } else {
      alert.classList.remove('bg-green-100', 'text-green-800', 'border-green-300', 'bg-blue-100', 'text-blue-700', 'border-blue-300');
      alert.classList.add('bg-red-100', 'text-red-800', 'border-red-300');
      if (data.open_time === 'Closed' && data.close_time === 'Closed') {
        message.innerHTML = 'Closed!';
      } else {
        message.innerHTML = `${data.message}`;
      }
    }
  }
} catch (error) {
  console.error('Error checking operating hours:', error);
  alert.classList.remove('hidden', 'bg-blue-100', 'text-blue-700', 'bg-green-100', 'text-green-800');
  alert.classList.add('bg-red-100', 'text-red-800', 'border-red-300');
  message.innerHTML = 'Error checking operating hours. Please try again.';
}
  }
  async function fetchUnavailableSlots() {
    const date = document.getElementById('date')?.value;
    const listElement = document.getElementById('unavailableTimesList');

    if (!date) {
        listElement.innerHTML = '<p class="text-gray-500 text-sm text-center italic">Select a date to view unavailable slots</p>';
        return;
    }
    listElement.innerHTML = '<p class="text-gray-500 text-sm text-center italic">Loading...</p>';

    try {
        const response = await fetch('{{ route("customer.get_unavailable_slots") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ date })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.error) {
            listElement.innerHTML = `<p class="text-red-500 text-sm text-center p-3 bg-red-50 rounded-lg border border-red-200">${data.message}</p>`;
            return;
        }

        if (data.slots && data.slots.length > 0) {
            listElement.innerHTML = `
                <div class="grid grid-cols-2 gap-2">
                    ${data.slots.map(slot => `
                        <div class="bg-red-50 p-2 rounded-lg border border-red-200 text-center">
                            <div class="font-semibold text-red-700 text-xs">${slot.start} - ${slot.end}</div>
                            <div class="text-xs text-gray-600 mt-0.5">TABLE ${slot.table}</div>
                        </div>
                    `).join('')}
                </div>
            `;
        } else {
            listElement.innerHTML = '<p class="text-blue-600 text-sm text-center p-3 bg-blue-50 rounded-lg border border-blue-200">No unavailable time slots!</p>';
        }
    } catch (error) {
        console.error('Error fetching unavailable slots:', error);
        listElement.innerHTML = '<p class="text-red-500 text-sm text-center p-3 bg-red-50 rounded-lg italic">Error loading slots. Please try again.</p>';
    }
}

  document.addEventListener('DOMContentLoaded', () => {
    const dateInput = document.getElementById('date');
    if (!dateInput.value) {
      dateInput.value = new Date().toISOString().split('T')[0];
    }
    
    checkOperatingHours(true);
    fetchUnavailableSlots();
  });

  function openModal(id) {
    const modal = document.getElementById(id);
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  function closeModal(id) {
    const modal = document.getElementById(id);
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }
</script>
</html>
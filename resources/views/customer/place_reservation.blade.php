<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Jeongol Izakaya</title>

  <!-- Bootstrap + Icons -->

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
      margin-top: 50px;
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
      margin-bottom: 15px;
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


    /* Tablets (landscape and portrait) */
    @media (max-width: 768px) {
      .table-link {
        flex: 0 1 calc(33.33% - 10px);
      }
    }

    /* Mobile devices */
    @media (max-width: 480px) {
      .table-link {
        flex: 0 1 calc(33.33% - 10px);
      }

    }

    .main-menu-grid,
    .other-menu-grid {
      display: grid !important;
      grid-template-columns: repeat(3, 1fr) !important;
    }

    /* Image size */
    .menu-card img {
      height: 90px !important;
      width: 100% !important;
      border-radius: 3px;

    }

    /* Reduce spacing if needed */
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

      <!-- Pax at top -->
      <div class="absolute top-1 text-xs ">{{ $table->capacity }} Pax</div>

      <!-- Table number centered -->
      <div class="table-number text-lg font-semibold">Table {{ $table->table_number }}</div>
      </div>
    </div>
  @endforeach
  </div>

  <!-- Reservation Modal -->
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
          <input type="text" id="contactNumber" name="contact_number" placeholder="09XXXXXXXXX" />
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
          Advance Payment: <span id="advance_payment_label" class="text-gray-500 text-sm">Default amount</span>
        </label>
        <input type="number" id="advance_payment" class="form-control" readonly />


        <div class="modal-section">
          <label for="notesTextarea">Notes</label>
          <textarea id="notesTextarea" rows="2"></textarea>
        </div>

        <input type="hidden" id="selectedTableNumber">

        <div class="flex gap-4 p-2 border-t border-gray-200 dark:border-gray-600">
          <!-- Order Food Button -->
          <button data-modal-hide="default-modal" type="button" id="order"
            class="w-1/2 bg-gray-500 hover:bg-gray-600 text-white font-medium text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-4 focus:ring-gray-300 dark:bg-gray-700 dark:hover:bg-gray-800 dark:focus:ring-gray-600">
            Order Food
          </button>

          <button type="button" id="paymentBtn"
            class="w-1/2 bg-blue-500 hover:bg-blue-600 text-white font-medium text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-700 dark:hover:bg-blue-800 dark:focus:ring-blue-600">
            Proceed to payment
          </button>
          <!-- Submit Reservation Button 
          <button id="submitBtn" type="button"
            class="w-1/2 bg-red-600 hover:bg-red-700 text-white font-medium text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-4 focus:ring-red-300 dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-800">
            Submit Reservation
          </button>
          -->
        </div>

      </form>
    </div>
  </div>

  <div id="paymentModal" class="fixed inset-0 z-[1100] hidden items-center justify-center bg-black bg-opacity-50">

    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">

      <!-- Close Button -->
      <button id="closePaymentModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
        ✕
      </button>

      <!-- Title -->
      <h2 class="text-xl font-semibold text-center mb-4">Payment</h2>

      <!-- Tabs -->
      <div class="flex border-b mb-4">
        <button type="button" data-tab="gcash" class="flex-1 text-center py-2 border-b-2 border-blue-500 font-semibold">
          Gcash
        </button>
        <button type="button" data-tab="maya"
          class="flex-1 text-center py-2 border-b-2 border-transparent hover:border-gray-300">
          Maya
        </button>
      </div>

      <!-- Gcash Content -->
      <div id="tab-gcash" class="tab-content">
        <label class="block mb-2 text-sm font-medium">Gcash Number</label>
        <input type="number" class="w-full border rounded px-3 py-2 mb-3" placeholder="09XXXXXXXXX">

        <label class="block mb-2 text-sm font-medium">Gcash Registered Name</label>
        <input type="text" class="w-full border rounded px-3 py-2 mb-3" placeholder="Full Name">

        <label class="block mb-2 text-sm font-medium">Amount</label>
        <input type="number" class="w-full border rounded px-3 py-2 mb-3" placeholder="Enter amount">

        <label class="block mb-2 text-sm font-medium">Upload Proof of Payment</label>
        <input type="file" class="w-full border rounded px-3 py-2">
      </div>

      <!-- Maya Content (hidden by default) -->
      <div id="tab-maya" class="tab-content hidden">
        <label class="block mb-2 text-sm font-medium">Maya Number</label>
        <input type="number" class="w-full border rounded px-3 py-2 mb-3" placeholder="09XXXXXXXXX">

        <label class="block mb-2 text-sm font-medium">Maya Registered Name</label>
        <input type="text" class="w-full border rounded px-3 py-2 mb-3" placeholder="Full Name">

        <label class="block mb-2 text-sm font-medium">Amount</label>
        <input type="number" class="w-full border rounded px-3 py-2 mb-3" placeholder="Enter amount">

        <label class="block mb-2 text-sm font-medium">Upload Proof of Payment</label>
        <input type="file" class="w-full border rounded px-3 py-2">
      </div>

      <!-- Submit Button -->
      <button id="submitBtn" type="button"
        class="mt-4 w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
        Sumbit Reservation
      </button>
    </div>
  </div>


  <!--Order Modal-->
  <div id="orderModal" class="modal-order">
    <div class="modal-content">
      <span class="close-modal" id="closeOrderModal" style="float: right; cursor: pointer;">&times;</span>
      <h3 class="text-lg">
        <strong>
          Select Menu Items
          <span>
            <div class="flex justify-end">
              <button data-modal-target="default-modal" data-modal-toggle="default-modal" type="button"
                id="ordersButton" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">
                Orders
              </button>
            </div>

          </span>
        </strong>
      </h3>
      <div class="modal-section">
        @foreach(['main' => 'Main Menu', 'add_ons' => 'Add-ons', 'drinks' => 'Drinks', 'rice' => 'Rice'] as $key => $label)
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

          <!-- Modal Header -->
          <div class="modal-section">
            <div class="flex items-center justify-between p-2 rounded-t bg-red-800">
              <h3 class="text-lg font-semibold text-white mt-4 ml-5">
                Orders Breakdown
              </h3>
            </div>
          </div>

          <!-- Modal Body -->
          <div id="orderSummary"
            class="p-4 bg-white  text-sm text-gray-800 dark:text-white border  overflow-y-auto flex-1">
            <ul id="selectedOrdersContainer" class="text-sm list-disc pl-5 text-black-700 dark:text-black mt-2">
            </ul>
          </div>

          <!-- Modal Footer -->
          <div class="flex justify-end gap-4 p-2 border-t border-gray-200 dark:border-gray-600">
            <!-- Close Button -->
            <button data-modal-hide="default-modal" type="button"
              class="bg-gray-500 hover:bg-gray-600 text-white font-medium text-sm px-5 py-2.5 rounded-lg focus:outline-none focus:ring-4 focus:ring-gray-300 dark:bg-gray-700 dark:hover:bg-gray-800 dark:focus:ring-gray-600">
              Close
            </button>

            <!-- Clear Button -->
            <button id="clearOrdersBtn" type="button"
              class="bg-red-600 hover:bg-red-700 text-white font-medium text-sm px-5 py-2.5 rounded-lg focus:outline-none focus:ring-4 focus:ring-red-300 dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-800">
              Clear
            </button>
          </div>

        </div>
      </div>
    </div>
  </div>

  <footer class="text-center p-3 bg-red-500 text-white mt-5">
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

    // Elements
    const paymentBtn = document.getElementById('paymentBtn');
    const paymentModal = document.getElementById('paymentModal');
    const closePaymentModal = document.getElementById('closePaymentModal');

    // Show modal
    paymentBtn.addEventListener('click', () => {
      paymentModal.classList.remove('hidden');
      paymentModal.classList.add('flex');
    });

    // Hide modal
    closePaymentModal.addEventListener('click', () => {
      paymentModal.classList.add('hidden');
      paymentModal.classList.remove('flex');
    });

    // Tab switching
    document.querySelectorAll('[data-tab]').forEach(tabBtn => {
      tabBtn.addEventListener('click', () => {
        document.querySelectorAll('[data-tab]').forEach(btn => btn.classList.remove('border-blue-500'));
        tabBtn.classList.add('border-blue-500');

        const tabName = tabBtn.getAttribute('data-tab');
        document.querySelectorAll('.tab-content').forEach(content => {
          content.classList.add('hidden');
        });
        document.getElementById(`tab-${tabName}`).classList.remove('hidden');
      });
    });

    document.addEventListener('DOMContentLoaded', () => {
      const modal = document.getElementById('tableModal');
      const closeModal = document.getElementById('closeModal');
      const tableLinks = document.querySelectorAll('.table-link');

      const nameInput = document.getElementById('customerName');
      const contactInput = document.getElementById('contactNumber');
      const paxInput = document.getElementById('pax');
      const notesInput = document.getElementById('notesTextarea');
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
        resetReservationForm();
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

        // Advance payment calculation
        const advancePaymentInput = document.getElementById('advance_payment');
        const advancePaymentLabel = document.getElementById('advance_payment_label');

        if (advancePaymentInput) {
          let advance = 600;
          if (orderCount > 0) {
            advance += (orderCount * 50);
          }
          advancePaymentInput.value = advance.toFixed(2);
          if (advancePaymentLabel) {
            advancePaymentLabel.textContent = 'Default amount';
          }
        }

        if (document.getElementById('totalQuantity')) {
          document.getElementById('totalQuantity').textContent = totalQuantity;
        }
        if (document.getElementById('totalPrice')) {
          document.getElementById('totalPrice').textContent = `₱${total.toFixed(2)}`;
        }
      }

      window.updateQuantity = function (input) {
        const id = input.dataset.id;
        const newQuantity = parseInt(input.value);
        if (selectedOrders[id] && newQuantity > 0) {
          selectedOrders[id].quantity = newQuantity;
        }
        updateOrderSummary();
      };

      document.getElementById('clearOrdersBtn').addEventListener('click', () => {
        Object.keys(selectedOrders).forEach(k => delete selectedOrders[k]);
        updateOrderSummary();
      });

      orderBtn.addEventListener('click', () => {
        document.getElementById('orderModal').style.display = 'flex';
      });
      document.getElementById('closeOrderModal').addEventListener('click', () => {
        document.getElementById('orderModal').style.display = 'none';
      });

      function gatherReservationData() {
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
          notes: notesInput.value.trim(),
          orders: menuItems
        };
      }

      function validateInputs() {
        let hasError = false;
        let requiredFields = [nameInput, contactInput, paxInput, timeInput, dateInput];
        requiredFields.forEach(field => {
          if (!field.value.trim()) {
            field.classList.add('input-error');
            hasError = true;
          } else {
            field.classList.remove('input-error');
          }
        });
        return !hasError;
      }

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
          const res = await fetch(window.customer_jeongolConfig.storeReservationUrl, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': window.customer_jeongolConfig.csrfToken
            },
            body: JSON.stringify(data)
          });

          console.log('Response status:', res.status);
          console.log('Response ok:', res.ok);

          const responseText = await res.text();
          console.log('Response text:', responseText);

          let responseJson = {};
          try {
            responseJson = JSON.parse(responseText);
          } catch (jsonErr) {
            console.error('Invalid JSON:', jsonErr);
          }

          if (res.ok && responseJson.success) {
            alert('Reservation successful!');
            resetReservationForm();
            modal.style.display = 'none';
          } else {
            if (responseJson.message === 'Time slot already taken.') {
              alert('Sorry, this time slot for the selected table is already booked. Please choose another time or table.');
            } else {
              alert(responseJson.message || 'Reservation could not be completed.');
            }
          }
        } catch (err) {
          console.error('Fetch error:', err);
          alert('Something went wrong while submitting the reservation.');
        } finally {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Submit Reservation';
        }
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

      // Orders modal
      const ordersButton = document.getElementById("ordersButton");
      const defaultModal = document.getElementById("default-modal");
      const closeButtons = defaultModal.querySelectorAll("[data-modal-hide='default-modal']");
      ordersButton.addEventListener("click", () => {
        defaultModal.classList.remove("hidden");
      });
      closeButtons.forEach(btn => {
        btn.addEventListener("click", () => {
          defaultModal.classList.add("hidden");
        });
      });

      // Payment modal
      const paymentModal = document.getElementById("paymentModal");
      const closePaymentModal = document.getElementById("closePaymentModal");



      function clearReservationForm() {
        [nameInput, contactInput, dateInput, timeInput, notesInput].forEach(el => el.value = '');
        paxInput.value = '';
        selectedTableNumber = 0;
        document.getElementById('selectedTableNumber').value = '';
        Object.keys(selectedOrders).forEach(k => delete selectedOrders[k]);
        updateOrderSummary();
      }

      function clearPaymentFields() {
        paymentModal.querySelectorAll("input").forEach(input => {
          input.value = "";
          input.classList.remove("input-error");
        });
      }


      closePaymentModal.addEventListener("click", () => {
        paymentModal.classList.add("hidden");
        clearPaymentFields();
      });
      document.getElementById("paymentBtn")?.addEventListener("click", () => {
        paymentModal.classList.remove("hidden");
      });

      function resetReservationForm() {
        [nameInput, contactInput, dateInput, timeInput, notesInput].forEach(el => el.value = '');
        paxInput.value = '';
        selectedTableNumber = 0;
        document.getElementById('selectedTableNumber').value = '';
        Object.keys(selectedOrders).forEach(k => delete selectedOrders[k]);
        updateOrderSummary();

        if (paymentModal) {
          paymentModal.classList.add('hidden')
          clearPaymentFields();
        }
        clearPaymentFields();

      }
    });


  </script>





</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Receptionist Page</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="shortcut icon" type="x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  @include('receptionist.components.css')
  @vite('resources/css/app.css')

  <style>
    ul::-webkit-scrollbar {
      width: 6px;
    }

    ul::-webkit-scrollbar-thumb {
      background-color: #a0aec0;
      border-radius: 4px;
    }

    ul::-webkit-scrollbar-thumb:hover {
      background-color: #718096;
    }

    ul {
      scrollbar-width: thin;
      scrollbar-color: #a0aec0 transparent;
    }

    [x-cloak] {
      display: none;
    }
  </style>
</head>

<body>
  <div class="relative">
    
    <div class="mt-2 border-b border-gray-200 flex items-center justify-between px-7">
      <div class="logo flex items-center ml-5">
        <img src="{{ asset('logo/jeongol_logo.jpg') }}" alt="Jeongol Logo" class="h-13 w-20" />
      </div>
      
      <div class="relative">
        <button id="userBtn" class="relative flex items-center gap-2 p-4 hover:bg-gray-100 z-50">
          <div class="w-14 h-14 rounded-full bg-gray-300 flex items-center justify-center font-bold text-black">
            {{ strtoupper(substr(Auth::user()->firstname, 0, 1)) }}
          </div>
          <span id="notifBadgeProfile"
            class="absolute top-1 right-1 items-center justify-center px-2 py-1 text-xs font-bold text-white bg-red-600 rounded-full hidden"
            data-count="{{ auth()->user()?->unreadNotifications->count() ?? 0 }}">
            {{ auth()->user()?->unreadNotifications->count() ?? 0 }}
          </span>
        </button>

        <div id="userMenu" class="hidden absolute top-full right-0 mt-2 w-56 bg-white border rounded-lg shadow-lg z-50">
          <div class="px-4 py-3 border-b">
            <p class="text-sm font-medium">{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</p>
            <p class="text-xs text-gray-500">{{ Auth::user()->role }}</p>
          </div>

          <a href="javascript:void(0)" id="notifBtn" class="block px-4 py-2 hover:bg-gray-100 relative">
            Notifications
            <span id="notifBadgeLink"
              class="absolute top-1 right-1 hidden items-center justify-center px-2 py-1 text-xs font-bold text-white bg-red-600 rounded-full">
              {{ auth()->user()?->unreadNotifications->count() ?? 0 }}
            </span>
          </a>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">Logout</button>
          </form>
        </div>

        <div id="notifModal"
          class="hidden fixed inset-0 flex items-start justify-end z-50 bg-black bg-opacity-20 p-4 overflow-auto">
          <div class="w-full max-w-xs sm:w-80 bg-white rounded-lg shadow-lg">
            <div class="p-5 relative">
              <h2 class="text-lg font-semibold mb-4">Notifications</h2>
              <ul id="notifList" class="space-y-2 max-h-96 overflow-y-auto"></ul>
              <button id="notifClose" class="absolute top-2 right-2">✖</button>
            </div>
          </div>
        </div>

        <div id="paymentModal"
          class="hidden fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50 p-4">
          <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md mx-auto relative">
            <button id="closePaymentBtn"
              class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 font-bold text-xl">×</button>
            <h2 class="text-lg font-bold mb-4">Payment Details</h2>
            <div>
              <p>Transaction Receipt</p>
              <img id="paymentProof" src="" class="mb-2 w-full object-contain" style="display:none;">
            </div>
            <p><strong>Required Amount:</strong> <span id="requiredAmount">N/A</span></p>
            <p><strong>Status:</strong> <span id="paymentStatus">N/A</span></p>
            <div id="actionButtons" class="mt-4 text-center flex justify-center gap-2">
              <form id="acceptForm" method="POST" class="inline">@csrf
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Accept</button>
              </form>
              <button id="cancelReservationBtn" class="px-4 py-2 bg-red-500 text-white">Cancel</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    @if(session('success'))
      <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
        class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded-lg shadow-md max-w-sm text-center z-[1000]">
        {{ session('success') }}
      </div>
    @endif

    <div id="fly-animation-container" style="position: fixed; top: 0; left: 0; pointer-events: none; z-index: 9999;"></div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

    <div class="table-layout">
      @foreach($tables as $table)
        <div class="table-link" data-table-id="{{ $table->id }}" data-table-number="{{ $table->table_number }}">
          <div class="table available">
            <div class="table-number text-center">Table {{ $table->table_number }}</div>
            <div class="inline-options"
              style="display:none; flex-direction: column; align-items: center; gap: 5px; margin-top: 10px;">
              <button
                class="bg-blue-800 text-white border-none px-2.5 py-1.5 my-[3px] rounded cursor-pointer text-[17px] hover:bg-blue-700"
                onclick="event.stopPropagation(); makeOrder({{ $table->id }})">Place Order</button>
              <button
                class="bg-blue-800 text-white border-none px-2.5 py-1.5 my-[3px] rounded cursor-pointer text-[17px] hover:bg-blue-700"
                onclick="event.stopPropagation(); makeReservation({{ $table->id }})">Make Reservation</button>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="bottom-buttons">
      <a class="view-button" href="{{ route('receptionist.reservations') }}">View Reservations</a>
      <a class="view-button" href="{{ route('receptionist.modify_orders') }}">View Orders</a>
    </div>

    <div id="tableModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="modal-content">
        <span id="closeModal" class="close-modal">&times;</span>
        <h2 class="text-lg font-bold text-center mb-2">Customer Info and Menu</h2>

        <div class="modal-section">
          <label><strong>Customer</strong></label>
          <input type="text" id="customerName" placeholder="Customer's name" required
            class="border border-gray-400 focus:border-black-500 p-2 rounded w-full" />
        </div>

        <div class="modal-section" id="contactinput">
          <label><strong>Contact Number</strong></label>
          <input type="number" id="contactNumber" placeholder="09xxxxx" required
            class="border border-gray-400 focus:border-black-500 p-2 rounded w-full" />
        </div>

        <div class="modal-section modal-flex">
          <div class="modal-column">
            <label><strong>Number of Pax</strong></label>
            <input id="numberOfPax" type="number" value="1" min="1" required
              class="border border-gray-400 focus:border-gray-700 p-2 rounded w-full" />
          </div>
          <div class="modal-column" id="reservationInfoGroup">
            <label class="mb-1"><strong>Reserved Now</strong></label>
            <input class="border border-gray-400 focus:border-gray-700 p-2 rounded w-full" type="date"
              id="reserved_date" />
            <input class="border border-gray-400 focus:border-gray-700 p-2 rounded w-full" type="time"
              id="arrivalTimeInput" min="11:30" max="18:00" required />
            <p>
              <strong>Reservation Time Frame:</strong><br>
              <span id="timeFrameDisplay" class="text-sm font-medium text-red-500"></span>
            </p>
          </div>
        </div>

        <hr class="mt-2 border-t-10 mb-2 border-black-300">

        <div class="modal-flex flex-col md:flex-row gap-6">
          <div class="modal-section flex flex-col gap-6 w-full">
            @foreach(['main' => 'Main Menu', 'add_ons' => 'Add-ons', 'drinks' => 'Drinks', 'rice' => 'Rice'] as $key => $label)
              @if(isset($groupedMenu[$key]))
                <x-menu-category-grid :key="$key" :label="$label" :items="$groupedMenu[$key]" />
              @endif
            @endforeach
          </div>
          
          <div class="modal-section">
            <label><strong>Advance Payment </strong></label>
            <input class="border border-gray-400 focus:border-gray-700 p-2 rounded w-full" type="number"
              id="advance_payment" readonly>
          </div><br><br>

          <div class="flex justify-center md:items-center mt-4 md:mt-2 mb-3">
            <button type="button" id="viewOrdersBtn"
              class="inline-btn w-full sm:w-auto text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-3 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
              <i class="fas fa-shopping-cart text-sm mr-2"></i> View Orders
            </button>
          </div>
        </div>

        <div class="modal-section">
          <textarea class="border border-gray-900 focus:border-gray-700 p-2 rounded w-full" id="customerNotes"
            placeholder="Add notes"></textarea>
        </div>

        <div class="modal-actions">
          <button class="pay-btn" id="submitBtn" type="button">Submit</button>
        </div>
      </div>

      <div id="default-modal" tabindex="-1" aria-hidden="true"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex justify-center items-center">
        <div class="relative w-full max-w-lg h-[60vh]">
          <div class="relative h-full bg-white rounded-lg shadow flex flex-col">
            <div class="modal-section">
              <div class="flex items-center justify-between p-3 rounded-t bg-red-800">
                <h3 class="text-lg font-semibold text-white">Orders Breakdown</h3>
              </div>
            </div>

            <div id="orderSummary"
              class="p-4 bg-white text-sm text-gray-800 dark:text-white border overflow-y-auto flex-1">
              <ul id="selectedOrdersContainer" class="text-sm list-disc pl-5 text-black-700 dark:text-black mt-2">
              </ul>
            </div>

            <div class="flex justify-end gap-4 p-2 border-t border-gray-200 dark:border-gray-600">
              <button id="clearOrdersBtn" type="button"
                class="bg-red-600 hover:bg-red-700 text-white font-medium text-sm px-5 py-2.5 rounded-lg focus:outline-none focus:ring-4 focus:ring-red-300 dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-800">
                Clear
              </button>
              <button data-modal-hide="default-modal" type="button"
                class="bg-gray-500 hover:bg-gray-600 text-white font-medium text-sm px-5 py-2.5 rounded-lg focus:outline-none focus:ring-4 focus:ring-gray-300 dark:bg-gray-700 dark:hover:bg-gray-800 dark:focus:ring-gray-600">
                Ok
              </button>
            </div>
          </div>
        </div>
      </div>

      <div id="successModal"
        class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 mx-4 max-w-sm w-full text-center">
          <div class="mb-4">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
              <i class="fas fa-check text-2xl text-green-600"></i>
            </div>
            <h2 id="successTitle" class="text-lg font-bold text-gray-800">Success!</h2>
          </div>
          <button id="successOkBtn" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded">
            OK
          </button>
        </div>
      </div>
    </div>

    <script>
      const userBtn = document.getElementById('userBtn');
      const userMenu = document.getElementById('userMenu');
      const notifBtn = document.getElementById('notifBtn');
      const notifModal = document.getElementById('notifModal');
      const notifClose = document.getElementById('notifClose');
      const badgeProfile = document.getElementById('notifBadgeProfile');
      const badgeLink = document.getElementById('notifBadgeLink');
      const notifList = document.getElementById('notifList');

      userBtn.addEventListener('click', e => { e.stopPropagation(); userMenu.classList.toggle('hidden'); });
      userMenu.addEventListener('click', e => e.stopPropagation());
      document.addEventListener('click', () => userMenu.classList.add('hidden'));

      notifBtn.addEventListener('click', e => { e.stopPropagation(); notifModal.classList.remove('hidden'); });
      notifClose.addEventListener('click', () => notifModal.classList.add('hidden'));
      notifModal.addEventListener('click', () => notifModal.classList.add('hidden'));
      notifModal.querySelector('div').addEventListener('click', e => e.stopPropagation());

      function fetchNotifications() {
        fetch('/receptionist/notifications')
          .then(res => res.json())
          .then(data => {
            let notifications = data.notifications ?? [];
            const pendingCount = notifications.filter(n => n.status === "Pending").length;
            
            [badgeProfile, badgeLink].forEach(b => {
              b.textContent = pendingCount;
              b.style.display = pendingCount ? 'inline-flex' : 'none';
            });

            if (!notifications.length) {
              notifList.innerHTML = `<li class="no-notifs p-3 text-center text-gray-500">No notifications</li>`;
              return;
            }
            notifList.innerHTML = '';

            notifications.sort((a, b) => {
              const order = { "Pending": 1, "Accepted": 2, "Rejected": 3 };
              return (order[a.status] || 4) - (order[b.status] || 4);
            });

            notifications.forEach(n => {
              const li = document.createElement('li');
              li.className = 'p-3 bg-gray-100 rounded cursor-pointer mb-2';
              li.dataset.reservationId = n.reservation_id;
              li.onclick = () => openNotifModal(n.reservation_id);

              let badgeClass = "bg-gray-300 text-gray-700";
              if (n.status === "Accepted") badgeClass = "bg-green-100 text-green-700";
              else if (n.status === "Rejected") badgeClass = "bg-red-100 text-red-700";

              li.innerHTML = `
                <div class="flex justify-between items-center">
                  <div>
                    <p class="text-sm font-medium">${n.name}</p>
                    <p class="text-xs text-gray-500">${n.message}</p>
                    <p class="text-xs text-gray-400 mt-1">${n.time}</p>
                  </div>
                  <span class="px-2 py-1 text-xs font-semibold rounded-full ${badgeClass}">
                    ${n.status}
                  </span>
                </div>
              `;
              notifList.appendChild(li);
            });
          })
          .catch(err => console.error(err));
      }

      setInterval(fetchNotifications, 3000);
      fetchNotifications();

      const paymentModal = document.getElementById('paymentModal');
      const acceptForm = document.getElementById('acceptForm');
      const cancelReservationBtn = document.getElementById('cancelReservationBtn');
      let reservationId = null;

      acceptForm.addEventListener('submit', (e) => {
        e.preventDefault();
        if (!reservationId) return;

        fetch(acceptForm.action, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
          }
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            paymentModal.classList.add('hidden');
            [badgeProfile, badgeLink].forEach(b => {
              b.textContent = data.unread_count;
              b.style.display = data.unread_count ? 'inline-flex' : 'none';
            });

            const li = notifList.querySelector(`[data-reservation-id="${data.reservationId}"]`);
            if (li) {
              li.querySelector('span').textContent = data.status;
              li.querySelector('span').className = `px-2 py-1 text-xs font-semibold rounded-full ${data.status === "Accepted" ? "bg-green-100 text-green-700" :
                data.status === "Rejected" ? "bg-red-100 text-red-700" : "bg-gray-300 text-gray-700"}`;
            }
            reservationId = null;
          } else {
            alert(data.message || 'Failed to accept reservation');
          }
        })
        .catch(err => { console.error(err); alert('Server error'); });
      });

      function openNotifModal(id) {
        reservationId = id;
        paymentModal.classList.remove('hidden');
        acceptForm.action = `/receptionist/accept-reservation/${id}`;

        fetch(`/payments/${id}`)
          .then(res => res.json())
          .then(data => {
            const paymentProof = document.getElementById('paymentProof');
            const requiredAmount = document.getElementById('requiredAmount');
            const paymentStatus = document.getElementById('paymentStatus');
            const actionButtons = document.getElementById('actionButtons');

            if (data.payment?.proof_path) {
              paymentProof.src = `/file-serve/${data.payment.proof_path}`;
              paymentProof.style.display = 'block';
            } else {
              paymentProof.style.display = 'none';
            }

            requiredAmount.textContent = data.advance_payment ?? 'N/A';
            paymentStatus.textContent = data.payment?.status ?? 'N/A';

            const reservationStatus = data.reservation?.status;
            if (reservationStatus === "Accepted" || reservationStatus === "Rejected") {
              actionButtons.style.display = "none";
            } else {
              actionButtons.style.display = "flex";
            }
          })
          .catch(err => {
            console.error('Error fetching payment details:', err);
            alert('Failed to load payment details.');
          });
      }

      document.getElementById('closePaymentBtn').addEventListener('click', () => {
        paymentModal.classList.add('hidden');
      });

      cancelReservationBtn.addEventListener('click', () => {
        if (!reservationId) return;

        fetch(`/receptionist/cancel-reservation/${reservationId}`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
          }
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            paymentModal.classList.add('hidden');
            const li = notifList.querySelector(`[data-reservation-id="${reservationId}"]`);
            if (li) {
              li.classList.add("text-red-600", "font-semibold");
              li.innerHTML += `<p class="text-xs">Cancelled</p>`;
            }
            reservationId = null;
          } else {
            alert(data.message || "Failed to cancel reservation");
          }
        })
        .catch(err => {
          console.error(err);
          alert("Server error while cancelling reservation.");
        });
      });

      function showSuccessModal(type) {
        const modal = document.getElementById('successModal');
        const title = document.getElementById('successTitle');
        const message = document.getElementById('successMessage');

        if (type === 'order') {
          title.textContent = 'Order Placed!';
        } else {
          title.textContent = 'Reservation Created!';
        }

        modal.classList.remove('hidden');
      }

      function closeSuccessModal() {
        document.getElementById('successModal').classList.add('hidden');
      }

      document.getElementById('successOkBtn').addEventListener('click', closeSuccessModal);
      document.getElementById('successModal').addEventListener('click', (e) => {
        if (e.target === e.currentTarget) closeSuccessModal();
      });

      const modal = document.getElementById('tableModal');
      const closeModal = document.getElementById('closeModal');
      const tableLinks = document.querySelectorAll('.table-link');
      const submitBtn = document.getElementById('submitBtn');
      const arrivalTimeInput = document.getElementById('arrivalTimeInput');
      const reserved_date = document.getElementById('reserved_date');

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

      closeModal.onclick = () => modal.style.display = 'none';
      window.onclick = e => { if (e.target === modal) modal.style.display = 'none'; };

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
        document.getElementById('contactinput').style.display = 'none';
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
        document.getElementById('contactinput').style.display = '';
        
        const now = new Date();
        const today = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
        document.getElementById('reserved_date').value = today;

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
        document.getElementById('contactNumber').value = '';
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

        viewOrdersBtn.addEventListener('click', () => {
          defaultModal.classList.remove('hidden');
        });

        closeButtons.forEach(btn => {
          btn.addEventListener('click', () => {
            defaultModal.classList.add('hidden');
          });
        });

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
          contact_number: document.getElementById('contactNumber').value.trim(),
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
          headers: {
            'Content-Type': 'application/json',
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            "Accept": "application/json"
          },
          body: JSON.stringify(data)
        })
        .then(async res => {
          if (!res.ok) {
            const errorText = await res.text();
            throw new Error(`Server responded with ${res.status}: ${errorText}`);
          }
          return res.json();
        })
        .then(response => {
          if (response.success) {
            document.getElementById('tableModal').classList.add('hidden');
            showSuccessModal(isPlacingOrder ? 'order' : 'reservation');
            submitBtn.disabled = false;
            submitBtn.textContent = "Submit";
          } else {
            showErrorModal(response.message || "Failed to save reservation.");
            submitBtn.disabled = false;
            submitBtn.textContent = "Submit";
          }
        })
        .catch(error => {
          console.error("Reservation Error:", error);
          alert("An error occurred while submitting the reservation.");
          submitBtn.disabled = false;
          submitBtn.textContent = "Submit";
        });
      });

      function showErrorModal(message) {
        let errorModal = document.getElementById('errorModal');
        if (!errorModal) {
          errorModal = document.createElement('div');
          errorModal.id = 'errorModal';
          errorModal.className = 'fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-50';
          errorModal.innerHTML = `
            <div class="bg-white rounded-lg shadow-lg p-6 mx-4 max-w-sm w-full text-center">
                <div class="mb-4">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-times text-2xl text-red-600"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-800">Error</h2>
                    <p id="errorMessage" class="text-gray-600 mt-2"></p>
                </div>
                <button id="errorOkBtn" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded">
                    OK
                </button>
            </div>
          `;
          document.body.appendChild(errorModal);

          document.getElementById('errorOkBtn').addEventListener('click', () => {
            errorModal.classList.add('hidden');
          });
          errorModal.addEventListener('click', (e) => {
            if (e.target === e.currentTarget) errorModal.classList.add('hidden');
          });
        }

        document.getElementById('errorMessage').textContent = message;
        errorModal.classList.remove('hidden');
      }

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

        const end = new Date(start.getTime() + 2 * 60 * 60 * 1000);

        const options = { hour: 'numeric', minute: '2-digit', hour12: true };
        const startStr = start.toLocaleTimeString('en-US', options);
        const endStr = end.toLocaleTimeString('en-US', options);

        document.getElementById('timeFrameDisplay').textContent = `${startStr} - ${endStr}`;
      }
    </script>
  </div>
</body>

</html>
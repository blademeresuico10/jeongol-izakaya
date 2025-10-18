<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Receptionist Page</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="shortcut icon" type="x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">


  @include('receptionist.components.css')
  @vite('resources/css/app.css')

  <style>
    #selectedOrdersContainer::-webkit-scrollbar {
      width: 6px;
    }

    #selectedOrdersContainer::-webkit-scrollbar-track {
      background: transparent;
    }

    #selectedOrdersContainer::-webkit-scrollbar-thumb {
      background: #cbd5e0;
      border-radius: 10px;
    }

    #selectedOrdersContainer::-webkit-scrollbar-thumb:hover {
      background: #a0aec0;
    }

    #selectedOrdersContainer>div {
      animation: slideInOrder 0.3s ease-out;
    }

    @keyframes slideInOrder {
      from {
        opacity: 0;
        transform: translateX(10px);
      }

      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    .order-item-card {
      transition: all 0.2s ease;
    }

    .order-item-card:hover {
      transform: translateX(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateX(20px);
      }

      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

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

    .spinner {
      animation: spin 0.6s linear infinite;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
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
            class="absolute top-1 right-1 items-center justify-center px-2 py-1 text-xs font-bold text-white bg-red-600 rounded-full hidden">
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
            </span>
          </a>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">Logout</button>
          </form>
        </div>

        <div id="notifModal"
          class="hidden fixed inset-0 flex items-start justify-end z-50 bg-black bg-opacity-20 p-4 overflow-auto">
          <div class="w-full max-w-md bg-white rounded-lg shadow-xl">
            <div class="sticky top-0 bg-white border-b border-gray-200 rounded-t-lg z-10">
              <div class="p-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800">Notifications</h2>
                <button id="notifClose" class="text-gray-500 hover:text-gray-700 transition-colors">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>

            <div class="max-h-[calc(100vh-200px)] overflow-y-auto">
              <ul id="notifList" class="divide-y divide-gray-100"></ul>
            </div>
          </div>
        </div>

        <div id="paymentModal"
          class="hidden fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50 p-4">
          <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md mx-auto relative">
            <button id="closePaymentBtn"
              class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 font-bold text-xl">×</button>
            <h2 class="text-lg font-bold mb-4">Payment Details</h2>
            <p><strong>Name</strong> <span id="customername">N/A</span></p>
            <p><strong>Table number</strong> <span id="tableNumber">N/A</span></p>
            <div>
              <p><strong>Transaction Receipt</strong></p>
              <img id="paymentProof" class="rounded border object-cover" style="width: 190px; height: 310px;">
            </div>
            <p><strong>Required Amount:</strong> <span id="requiredAmount">N/A</span></p>
            <p><strong>Pax:</strong> <span id="paxCount">N/A</span></p>
            <p><strong>Status:</strong> <span id="paymentStatus">N/A</span></p>
            <div id="actionButtons" class="mt-4 text-center flex justify-center gap-2">
              <form id="acceptForm" method="POST" class="inline">
                @csrf
                <button type="submit" id="acceptBtn"
                  class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 flex items-center justify-center gap-2 transition-all duration-200">
                  <span
                    class="spinner hidden w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                  <span class="btn-text">Accept</span>
                </button>
              </form>
              <button id="cancelReservationBtn"
                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 flex items-center justify-center gap-2 transition-all duration-200">
                <span
                  class="spinner hidden w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                <span class="btn-text">Reject</span>
              </button>
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

    <div id="fly-animation-container" style="position: fixed; top: 0; left: 0; pointer-events: none; z-index: 9999;">
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

    <livewire:table-layout />

    <div class="bottom-buttons">
      <a class="view-button" href="{{ route('receptionist.bookings') }}">Today's Reservation</a>
      <a class="view-button" href="{{ route('receptionist.modify_orders') }}">View Orders</a>
    </div>

    <div id="tableModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <<div class="modal-content">
        <span id="closeModal" class="close-modal">&times;</span>
        <h2 class="text-lg font-bold text-center mb-2">Customer Info and Menu</h2>

        <div class="modal-section">
          <label><strong>Customer</strong></label>
          <input type="text" id="customerName" placeholder="Customer's name" required minlength="3"
            class="border border-gray-400 focus:border-black-500 p-2 rounded w-full"
            onkeypress="return /[a-zA-Z\s\-'\.]/i.test(event.key)" />
          <div>
            <small id="customerNameError" class="text-red-600 text-sm hidden"></small>
          </div>
        </div>

        <div class="modal-section" id="contactinput">
          <label><strong>Contact Number</strong></label>
          <input type="text" id="contactNumber" required maxlength="11" placeholder="09xxxxxxxxx"
            class="border border-gray-400 focus:border-black-500 p-2 rounded w-full"
            onkeypress="return /[0-9]/i.test(event.key)" />
          <div>
            <small id="contactNumberError" class="text-red-600 text-sm hidden"></small>
          </div>
        </div>

        <div class="modal-section modal-flex">
          <div class="modal-column">
            <label><strong>Pax</strong></label>
            <input id="numberOfPax" type="number" required
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

    <div id="default-modal"
      class="fixed inset-y-0 right-0 z-50 transform translate-x-full transition-transform duration-300 ease-in-out"
      style="width: 480px;">

      <div id="modalBackdrop"
        class="fixed inset-0 bg-black bg-opacity-50 -z-10 opacity-0 transition-opacity duration-300 pointer-events-none">
      </div>

      <div class="h-full bg-white shadow-2xl flex flex-col">

        <div class="flex-shrink-0 bg-gradient-to-r from-red-700 to-red-800 px-6 py-5">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="bg-white/20 p-2 rounded-lg">
                <i class="fas fa-shopping-cart text-white text-xl"></i>
              </div>
              <div>
                <h3 class="text-xl font-bold text-white">Orders Breakdown</h3>
                <p id="totalItemsCount" class="text-white/80 text-sm mt-0.5">0 items</p>
              </div>
            </div>
            <button id="closeOrdersPanel" class="text-white hover:bg-white/20 rounded-lg p-2 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
        <div class="flex-1 overflow-y-auto bg-gray-50">

          <div id="emptyState" class="flex flex-col items-center justify-center h-full px-6 py-12">
            <div class="bg-gray-100 rounded-full p-8 mb-6">
              <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
              </svg>
            </div>
            <p class="text-xl font-semibold text-gray-600 mb-2">No orders yet</p>
            <p class="text-sm text-gray-400 text-center">Start adding items from the menu</p>
          </div>

          <div id="selectedOrdersContainer" class="p-4 space-y-3">
          </div>
        </div>

        <div id="orderTotal" class="flex-shrink-0 bg-white border-t-2 border-gray-200 p-4 hidden">
          <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4 space-y-3">
            <div class="flex items-center justify-between text-gray-700">
              <span class="font-medium">Total Items</span>
              <span id="totalQuantity" class="text-lg font-bold text-orange-600">0</span>
            </div>
            <div class="h-px bg-gray-300"></div>
            <div class="flex items-center justify-between">
              <span class="text-lg font-bold text-gray-800">Total Amount</span>
              <span id="grandTotal" class="text-2xl font-bold text-red-600">₱0.00</span>
            </div>
          </div>
        </div>

        <div class="flex-shrink-0 p-4 bg-white border-t border-gray-200">
          <div class="grid grid-cols-2 gap-3">
            <button id="clearOrdersBtn" type="button"
              class="bg-white hover:bg-gray-50 text-gray-700 font-semibold py-3.5 px-4 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 border-2 border-gray-300 hover:border-gray-400">
              <i class="fas fa-trash text-sm"></i>
              <span>Clear All</span>
            </button>
            <button data-modal-hide="default-modal" type="button"
              class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold py-3.5 px-4 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl">
              <i class="fas fa-check text-sm"></i>
              <span>Confirm</span>
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

</body>


<script>
  class ReceptionistDashboard {
    constructor() {
      this.selectedTableId = null;
      this.isPlacingOrder = false;
      this.selectedOrders = {};
      this.reservationId = null;
      this.selectedPaymentMethod = null;

      this.elements = {};
      this.init();
    }

    init() {
      document.addEventListener('DOMContentLoaded', () => {
        this.initializeElements();
        this.initializeEventListeners();
        this.initializeNotifications();
        this.initializeModalValidation();
      });
    }

    initializeElements() {
      this.elements = {
        userBtn: document.getElementById('userBtn'),
        userMenu: document.getElementById('userMenu'),
        notifBtn: document.getElementById('notifBtn'),
        notifModal: document.getElementById('notifModal'),
        notifClose: document.getElementById('notifClose'),
        notifList: document.getElementById('notifList'),
        badgeProfile: document.getElementById('notifBadgeProfile'),
        badgeLink: document.getElementById('notifBadgeLink'),
        tableModal: document.getElementById('tableModal'),
        paymentModal: document.getElementById('paymentModal'),
        defaultModal: document.getElementById('default-modal'),
        successModal: document.getElementById('successModal'),
        closeModal: document.getElementById('closeModal'),
        acceptForm: document.getElementById('acceptForm'),
        submitBtn: document.getElementById('submitBtn'),
        cancelReservationBtn: document.getElementById('cancelReservationBtn'),
        clearOrdersBtn: document.getElementById('clearOrdersBtn'),
        viewOrdersBtn: document.getElementById('viewOrdersBtn'),
        customerName: document.getElementById('customerName'),
        contactNumber: document.getElementById('contactNumber'),
        numberOfPax: document.getElementById('numberOfPax'),
        customerNotes: document.getElementById('customerNotes'),
        reservedDate: document.getElementById('reserved_date'),
        arrivalTimeInput: document.getElementById('arrivalTimeInput'),
        advancePayment: document.getElementById('advance_payment'),
        selectedOrdersContainer: document.getElementById('selectedOrdersContainer'),
        totalQuantity: document.getElementById('totalQuantity'),
        timeFrameDisplay: document.getElementById('timeFrameDisplay'),
        reservationInfoGroup: document.getElementById('reservationInfoGroup'),
        contactInput: document.getElementById('contactinput'),
        tableLinks: document.querySelectorAll('.table-link')
      };
    }

    initializeEventListeners() {
      this.setupUserMenuEvents();
      this.setupNotificationEvents();
      this.setupModalEvents();
      this.setupFormEvents();
      this.setupTableEvents();
    }

    setupUserMenuEvents() {
      if (this.elements.userBtn && this.elements.userMenu) {
        this.elements.userBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          this.elements.userMenu.classList.toggle('hidden');
        });

        this.elements.userMenu.addEventListener('click', (e) => e.stopPropagation());
        document.addEventListener('click', () => this.elements.userMenu.classList.add('hidden'));
      }
    }

    setupNotificationEvents() {
      if (this.elements.notifBtn && this.elements.notifModal) {
        this.elements.notifBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          this.elements.notifModal.classList.remove('hidden');
        });
      }

      if (this.elements.notifClose) {
        this.elements.notifClose.addEventListener('click', () => {
          this.elements.notifModal.classList.add('hidden');
        });
      }

      if (this.elements.notifModal) {
        this.elements.notifModal.addEventListener('click', () => {
          this.elements.notifModal.classList.add('hidden');
        });

        const notifContent = this.elements.notifModal.querySelector('div');
        if (notifContent) {
          notifContent.addEventListener('click', (e) => e.stopPropagation());
        }
      }
    }

    setupModalEvents() {
      if (this.elements.closeModal) {
        this.elements.closeModal.onclick = () => {
          this.elements.tableModal.style.display = 'none';
        };
      }

      const closePaymentBtn = document.getElementById('closePaymentBtn');
      if (closePaymentBtn) {
        closePaymentBtn.addEventListener('click', () => {
          this.elements.paymentModal.classList.add('hidden');
        });
      }

      const successOkBtn = document.getElementById('successOkBtn');
      if (successOkBtn) {
        successOkBtn.addEventListener('click', () => this.closeSuccessModal());
      }

      if (this.elements.successModal) {
        this.elements.successModal.addEventListener('click', (e) => {
          if (e.target === e.currentTarget) this.closeSuccessModal();
        });
      }

      if (this.elements.viewOrdersBtn && this.elements.defaultModal) {
        const backdrop = document.getElementById('modalBackdrop');
        const closeOrdersPanel = document.getElementById('closeOrdersPanel');

        this.elements.viewOrdersBtn.addEventListener('click', () => {
          this.elements.defaultModal.classList.remove('translate-x-full');
          if (backdrop) {
            backdrop.classList.remove('pointer-events-none', 'opacity-0');
            backdrop.classList.add('opacity-100');
          }
          document.body.style.overflow = 'hidden';
        });

        if (closeOrdersPanel) {
          closeOrdersPanel.addEventListener('click', () => {
            this.closeOrdersPanel();
          });
        }

        const confirmButtons = this.elements.defaultModal.querySelectorAll('[data-modal-hide="default-modal"]');
        confirmButtons.forEach(btn => {
          btn.addEventListener('click', () => {
            this.closeOrdersPanel();
          });
        });
      }

    }

    closeOrdersPanel() {
      if (this.elements.defaultModal) {
        this.elements.defaultModal.classList.add('translate-x-full');
      }

      const backdrop = document.getElementById('modalBackdrop');
      if (backdrop) {
        backdrop.classList.add('pointer-events-none', 'opacity-0');
        backdrop.classList.remove('opacity-100');
      }

      document.body.style.overflow = '';
    }

    setupFormEvents() {
      if (this.elements.acceptForm) {
        this.elements.acceptForm.addEventListener('submit', (e) => {
          this.handleAcceptReservation(e);
        });
      }

      if (this.elements.submitBtn) {
        this.elements.submitBtn.addEventListener('click', () => {
          this.handleSubmitReservation();
        });
      }

      if (this.elements.cancelReservationBtn) {
        this.elements.cancelReservationBtn.addEventListener('click', () => {
          this.handleCancelReservation();
        });
      }

      if (this.elements.clearOrdersBtn) {
        this.elements.clearOrdersBtn.addEventListener('click', () => {
          this.clearOrders();
        });
      }

      if (this.elements.arrivalTimeInput) {
        this.elements.arrivalTimeInput.addEventListener('input', () => {
          this.updateTimeFrameDisplay();
        });
      }
    }

    setupTableEvents() {
      const tableCapacities = {
        @foreach($tables as $table)
      {{ $table->id }}: {{ $table->capacity }},
      @endforeach
    };

    this.elements.tableLinks.forEach(link => {
          link.addEventListener('click', (e) => {
            e.preventDefault();

            const tableId = parseInt(link.getAttribute('data-table-id'));
            const capacity = tableCapacities[tableId];

            this.selectedTableCapacity = capacity;
            this.selectedTableId = tableId;

            this.handleTableClick(link);

            const paxInput = document.getElementById('numberOfPax');
            if (paxInput && capacity) {
              paxInput.max = capacity;
              paxInput.min = 1;
              paxInput.placeholder = `Max ${capacity} people`;
              paxInput.value = '';
              paxInput.readOnly = false;

              paxInput.addEventListener('input', function () {
                const maxCapacity = parseInt(this.max);
                const currentValue = parseInt(this.value);

                if (currentValue > maxCapacity) {
                  this.value = maxCapacity;
                  const toast = document.createElement('div');
                  toast.textContent = `Maximum ${maxCapacity} people allowed for this table`;
                  toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background-color: #f59e0b;
                color: white;
                padding: 14px 16px;
                border-radius: 4px;
                z-index: 9999;
                font-size: 14px;
                max-width: 250px;
              `;
                  document.body.appendChild(toast);
                  setTimeout(() => {
                    if (document.body.contains(toast)) {
                      document.body.removeChild(toast);
                    }
                  }, 2000);
                }
              });
            }
          });
        });

  setTimeout(() => {
    document.querySelectorAll('.make-walkin-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const tableId = parseInt(btn.getAttribute('data-table-id'));
        this.walkIn(tableId);
      });
    });

    document.querySelectorAll('.make-reservation-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const tableId = parseInt(btn.getAttribute('data-table-id'));
        this.makeReservation(tableId);
      });
    });
  }, 0);
  }

  initializeNotifications() {
    this.fetchNotifications();
    setInterval(() => this.fetchNotifications(), 3000);
  }

  fetchNotifications() {
    fetch('/receptionist/notifications')
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          const notifications = data.notifications ?? [];
          const pendingCount = notifications.filter(n => n.reservation_status === "Pending").length;

          this.updateNotificationBadges(pendingCount);
          this.renderNotifications(notifications);
        }
      })
      .catch(err => console.error('Notification fetch error:', err));
  }

  updateNotificationBadges(count) {
    [this.elements.badgeProfile, this.elements.badgeLink].forEach(badge => {
      if (badge) {
        badge.textContent = count;
        badge.style.display = count ? 'inline-flex' : 'none';
      }
    });
  }

  renderNotifications(notifications) {
    if (!this.elements.notifList) return;

    if (!notifications.length) {
      this.elements.notifList.innerHTML = `
            <li class="p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p class="text-gray-500 font-medium">No notifications</p>
            </li>
        `;
      return;
    }

    this.elements.notifList.innerHTML = '';

    notifications.sort((a, b) => {
      const order = { "Pending": 1, "Active": 2, "Rejected": 3 };
      return (order[a.reservation_status] || 4) - (order[b.reservation_status] || 4);
    });

    notifications.forEach(notification => {
      this.renderNotificationItem(notification);
    });
  }

  renderNotificationItem(notification) {
    const li = document.createElement('li');
    li.className = 'p-4 hover:bg-gray-50 cursor-pointer transition-colors duration-150';
    li.dataset.reservationId = notification.reservation_id;
    li.dataset.notification = JSON.stringify(notification);
    li.onclick = () => this.openNotificationModalDirect(notification);

    let badgeClass = "bg-gray-100 text-gray-700";
    let badgeText = notification.reservation_status;

    if (notification.reservation_status === "Active") {
      badgeClass = "bg-green-100 text-green-700";
      badgeText = "Accepted"
    } else if (notification.reservation_status === "Rejected") {
      badgeClass = "bg-red-100 text-red-700";
    } else if (notification.reservation_status === "Pending") {
      badgeClass = "bg-yellow-100 text-yellow-700";
    }

    const formatReservationTime = (startDateTime, endDateTime) => {
      if (!startDateTime || !endDateTime) return 'N/A';

      const startDate = new Date(startDateTime);
      const endDate = new Date(endDateTime);

      const dateStr = startDate.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
      });

      const startTime = startDate.toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
      }).toLowerCase();

      const endTime = endDate.toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
      }).toLowerCase();

      return { dateStr, timeRange: `${startTime} - ${endTime}` };
    };

    const { dateStr, timeRange } = formatReservationTime(
      notification.started_at,
      notification.ended_at
    );

    li.innerHTML = `
        <div class="flex items-start gap-3">
            <!-- Status Indicator -->
            <div class="flex-shrink-0 mt-1">
                <div class="w-2 h-2 rounded-full ${notification.reservation_status === "Pending" ? "bg-yellow-500 animate-pulse" :
        notification.reservation_status === "Active" ? "bg-green-500" :
          "bg-gray-400"
      }"></div>
            </div>
            
            <!-- Content -->
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2 mb-1">
                    <h3 class="font-semibold text-gray-900 truncate">${notification.customer_name}</h3>
                    <span class="flex-shrink-0 px-2 py-0.5 text-xs font-medium rounded-full ${badgeClass}">
                        ${badgeText}
                    </span>
                </div>
                
                <p class="text-sm text-gray-600 mb-2">${notification.message}</p>
                
                <div class="flex items-center gap-4 text-xs text-gray-500">
                    <div class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>${dateStr}</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>${timeRange}</span>
                    </div>
                </div>
            </div>
        </div>
    `;

    this.elements.notifList.appendChild(li);
  }

  openNotificationModalDirect(notification) {
    this.reservationId = notification.reservation_id;
    this.elements.paymentModal.classList.remove('hidden');
    this.elements.acceptForm.action = `/receptionist/accept-reservation/${notification.reservation_id}`;

    this.updatePaymentModalFromNotification(notification);
  }

  updatePaymentModalFromNotification(notification) {
    const paymentProof = document.getElementById('paymentProof');
    if (paymentProof) {
      if (notification.payment_proof) {
        let filename = notification.payment_proof.includes('/')
          ? notification.payment_proof.split('/').pop()
          : notification.payment_proof;
        paymentProof.src = `/file-serve/payment_proofs/${filename}`;
        paymentProof.style.display = 'block';
      } else {
        paymentProof.style.display = 'none';
      }
    }
    this.updateElementText('customername', notification.customer_name || 'N/A');
    this.updateElementText('tableNumber', notification.table_number || 'N/A');
    this.updateElementText('requiredAmount', notification.advance_payment ? `₱${parseFloat(notification.advance_payment).toFixed(2)}` : 'N/A');
    this.updateElementText('paxCount', notification.pax || 'N/A');
    this.updateElementText('paymentStatus', notification.reservation_status || 'N/A');
    const actionButtons = document.getElementById('actionButtons');
    if (actionButtons) {
      const shouldHideButtons = notification.reservation_status === "Active" || notification.reservation_status === "Rejected";
      actionButtons.style.display = shouldHideButtons ? "none" : "flex";
    }
  }


  openNotificationModal(id) {
    this.reservationId = id;
    this.elements.paymentModal.classList.remove('hidden');
    this.elements.acceptForm.action = `/receptionist/accept-reservation/${id}`;

    this.fetchPaymentDetails(id);
  }

  fetchPaymentDetails(id) {
    fetch(`/receptionist/payments/${id}`)
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        this.updatePaymentModal(data);
      })
      .catch(error => {
        console.error('Error fetching payment details:', error);
        alert('Failed to load payment details: ' + error.message);
      });
  }

  updatePaymentModal(data) {
    const paymentProof = document.getElementById('paymentProof');
    if (paymentProof) {
      if (data.payment?.proof_path) {
        let path = data.payment.proof_path;
        let filename = path.includes('/') ? path.split('/').pop() : path;
        paymentProof.src = `/file-serve/payment_proofs/${filename}`;
        paymentProof.style.display = 'block';
      } else {
        paymentProof.style.display = 'none';
      }
    }
    this.updateElementText('customername', data.name || 'N/A')
    this.updateElementText('tableNumber', data.table_id || 'N/A');
    this.updateElementText('requiredAmount', data.advance_payment || 'N/A');
    this.updateElementText('paxCount', data.pax || 'N/A');
    this.updateElementText('paymentStatus', data.payment?.status || 'N/A');
    const actionButtons = document.getElementById('actionButtons');
    if (actionButtons) {
      const reservationStatus = data.reservation?.status;
      const shouldHideButtons = reservationStatus === "Active" || reservationStatus === "Rejected";
      actionButtons.style.display = shouldHideButtons ? "none" : "flex";
    }
  }

  updateElementText(elementId, text) {
    const element = document.getElementById(elementId);
    if (element) {
      element.textContent = text;
    }
  }


  handleAcceptReservation(e) {
    e.preventDefault();
    const btn = this.elements.acceptForm.querySelector('button[type="submit"]');
    const spinner = btn.querySelector('.spinner');
    const btnText = btn.querySelector('.btn-text');

    if (!btn || !this.reservationId) {
      alert('Error: Missing button or reservation ID');
      return;
    }

    spinner.classList.remove('hidden');
    btnText.textContent = 'Processing...';
    btn.disabled = true;
    this.elements.cancelReservationBtn.disabled = true;

    const formAction = `/receptionist/accept-reservation/${this.reservationId}`;

    fetch(formAction, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({})
    })
      .then(res => {
        if (!res.ok) {
          throw new Error(`HTTP ${res.status}: ${res.statusText}`);
        }
        return res.json();
      })
      .then(data => {
        if (data.success) {
          this.elements.paymentModal.classList.add('hidden');
          this.showReservationSuccessModal('Reservation accepted successfully!');
          this.fetchNotifications();
        } else {
          this.showReservationErrorModal(data.message || 'Failed to accept reservation.');
        }
      })
      .catch(error => {
        console.error('Accept error:', error);
        this.showReservationErrorModal('Error: ' + error.message);
      })
      .finally(() => {
        btn.disabled = false;
        this.elements.cancelReservationBtn.disabled = false;
        spinner.classList.add('hidden');
        btnText.textContent = 'Accept';
      });
  }

  handleCancelReservation() {
    const cancelBtn = this.elements.cancelReservationBtn;
    const spinner = cancelBtn.querySelector('.spinner');
    const btnText = cancelBtn.querySelector('.btn-text');

    if (!cancelBtn || !this.reservationId) {
      alert('Error: Missing button or reservation ID');
      return;
    }

    spinner.classList.remove('hidden');
    btnText.textContent = 'Processing...';
    cancelBtn.disabled = true;
    this.elements.acceptForm.querySelector('button[type="submit"]').disabled = true;

    fetch(`/receptionist/cancel-reservation/${this.reservationId}`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({})
    })
      .then(res => {
        if (!res.ok) {
          throw new Error(`HTTP ${res.status}: ${res.statusText}`);
        }
        return res.json();
      })
      .then(data => {
        if (data.success) {
          this.elements.paymentModal.classList.add('hidden');
          this.showReservationSuccessModal('Reservation rejected successfully!');
          this.fetchNotifications();
        } else {
          this.showReservationErrorModal(data.message || 'Failed to reject reservation.');
        }
      })
      .catch(error => {
        console.error('Reject error:', error);
        this.showReservationErrorModal('Error: ' + error.message);
      })
      .finally(() => {
        cancelBtn.disabled = false;
        this.elements.acceptForm.querySelector('button[type="submit"]').disabled = false;
        spinner.classList.add('hidden');
        btnText.textContent = 'Reject';
      });
  }

  showReservationSuccessModal(message) {
    const modal = this.elements.successModal;
    const title = document.getElementById('successTitle');

    if (title) {
      title.textContent = message;
    }

    if (modal) {
      modal.classList.remove('hidden');
    }
  }

  showReservationErrorModal(message) {
    const errorModal = document.getElementById('errorModal') || this.createErrorModal();
    const errorMessage = document.getElementById('errorMessage');

    if (errorMessage) {
      errorMessage.textContent = message;
    }

    if (errorModal) {
      errorModal.classList.remove('hidden');
    }
  }


  createErrorModal() {
    const modal = document.createElement('div');
    modal.id = 'errorModal';
    modal.className = 'fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50';
    modal.innerHTML = `
            <div class="bg-white rounded-lg shadow-lg p-6 w-80 text-center">
                <h3 class="text-lg font-semibold mb-2 text-red-600">Error</h3>
                <p id="errorMessage" class="text-gray-700 mb-4"></p>
                <button onclick="this.parentElement.parentElement.classList.add('hidden')" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                    Close
                </button>
            </div>
        `;
    document.body.appendChild(modal);
    return modal;
  }

  updateNotificationStatus(reservationId, status) {
    const li = this.elements.notifList.querySelector(`[data-reservation-id="${reservationId}"]`);
    if (li) {
      const statusSpan = li.querySelector('span');
      if (statusSpan) {
        statusSpan.textContent = status;
        const badgeClass = status === "Active" ? "bg-green-100 text-green-700" :
          status === "Rejected" ? "bg-red-100 text-red-700" :
            "bg-gray-300 text-gray-700";
        statusSpan.className = `px-2 py-1 text-xs font-semibold rounded-full ${badgeClass}`;
      }
    }
  }

  initializeModalValidation() {
    const customerNameInput = document.getElementById('customerName');
    const customerNameError = document.getElementById('customerNameError');

    if (customerNameInput && customerNameError) {
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

    const contactNumberInput = document.getElementById('contactNumber');
    const contactNumberError = document.getElementById('contactNumberError');

    if (contactNumberInput && contactNumberError) {
      contactNumberInput.addEventListener('input', () => {
        contactNumberInput.value = contactNumberInput.value.replace(/[^0-9]/g, '');

        const value = contactNumberInput.value.trim();

        if (!value) {
          contactNumberError.textContent = '';
          contactNumberError.classList.add('hidden');
          contactNumberInput.classList.remove('input-error');
          return;
        }

        if (!value.startsWith('09')) {
          contactNumberError.textContent = 'Contact number must start with 09';
          contactNumberError.classList.remove('hidden');
          contactNumberInput.classList.add('input-error');
          return;
        }

        if (value.length < 11) {
          contactNumberError.textContent = 'Contact number must be 11 digits';
          contactNumberError.classList.remove('hidden');
          contactNumberInput.classList.add('input-error');
        } else {
          contactNumberError.textContent = '';
          contactNumberError.classList.add('hidden');
          contactNumberInput.classList.remove('input-error');
        }
      });
    }
  }

  handleTableClick(link) {
    document.querySelectorAll('.inline-options').forEach(opt => opt.style.display = 'none');
    const options = link.querySelector('.inline-options');
    if (options) options.style.display = 'block';
  }

  makeReservation(tableId) {
    this.selectedTableId = tableId;
    this.isPlacingOrder = false;
    this.elements.tableModal.style.display = 'flex';

    this.setupReservationForm();
    this.resetForm();
    this.updateTimeFrameDisplay();
  }

  walkIn(tableId) {
    this.selectedTableId = tableId;
    this.isPlacingOrder = true;
    this.elements.tableModal.style.display = 'flex';

    this.setupOrderForm();
    this.resetForm();
  }

  setupOrderForm() {
    const now = new Date();

    this.elements.reservedDate.value = now.toISOString().substring(0, 10);
    this.elements.reservedDate.disabled = true;
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    this.elements.arrivalTimeInput.value = `${hours}:${minutes}`;
    this.elements.arrivalTimeInput.disabled = true;
    this.elements.reservationInfoGroup.style.display = 'none';
    this.elements.contactInput.style.display = 'none';
    this.elements.advancePayment.parentElement.style.display = 'none';
  }


  setupReservationForm() {
    const now = new Date();
    this.elements.reservationInfoGroup.style.display = '';
    this.elements.reservedDate.disabled = false;
    this.elements.arrivalTimeInput.disabled = false;
    this.elements.advancePayment.parentElement.style.display = '';
    this.elements.contactInput.style.display = '';

    // Set minimum date to today
    const today = now.getFullYear() + '-' +
      String(now.getMonth() + 1).padStart(2, '0') + '-' +
      String(now.getDate()).padStart(2, '0');
    this.elements.reservedDate.value = today;
    this.elements.reservedDate.min = today;

    // Set default time to 2 hours from now
    const twoHoursLater = new Date(now.getTime() + (2 * 60 * 60 * 1000));
    const hours = twoHoursLater.getHours().toString().padStart(2, '0');
    const minutes = twoHoursLater.getMinutes().toString().padStart(2, '0');
    this.elements.arrivalTimeInput.value = `${hours}:${minutes}`;

    // Add validation event listeners
    const validateReservationTime = () => {
      const selectedDate = this.elements.reservedDate.value;
      const selectedTime = this.elements.arrivalTimeInput.value;

      if (!selectedDate || !selectedTime) return;

      const selectedDateTime = new Date(`${selectedDate}T${selectedTime}`);
      const currentTime = new Date();
      const minAllowedTime = new Date(currentTime.getTime() + (2 * 60 * 60 * 1000));

      if (selectedDateTime < minAllowedTime) {
        this.showToast('Reservation must be at least 2 hours from now', 'error');

        // Reset to minimum allowed time
        const resetHours = minAllowedTime.getHours().toString().padStart(2, '0');
        const resetMinutes = minAllowedTime.getMinutes().toString().padStart(2, '0');
        this.elements.arrivalTimeInput.value = `${resetHours}:${resetMinutes}`;

        // If the min time goes to next day, update date
        if (minAllowedTime.getDate() !== currentTime.getDate()) {
          const newDate = minAllowedTime.getFullYear() + '-' +
            String(minAllowedTime.getMonth() + 1).padStart(2, '0') + '-' +
            String(minAllowedTime.getDate()).padStart(2, '0');
          this.elements.reservedDate.value = newDate;
        }
      }
    };

    // Remove old listeners if they exist
    this.elements.reservedDate.removeEventListener('change', this.validateReservationTime);
    this.elements.arrivalTimeInput.removeEventListener('change', this.validateReservationTime);

    // Store the function reference for removal later
    this.validateReservationTime = validateReservationTime;

    // Add new listeners
    this.elements.reservedDate.addEventListener('change', validateReservationTime);
    this.elements.arrivalTimeInput.addEventListener('change', validateReservationTime);
    this.elements.arrivalTimeInput.addEventListener('blur', validateReservationTime);
  }

  selectMenuItem(element) {
    const id = element.dataset.id;
    const name = element.dataset.name;
    const price = parseFloat(element.dataset.price);
    const category = element.dataset.category;
    const image = element.dataset.image;

    const imgElement = element.querySelector('img');
    const imgSrc = imgElement ? imgElement.src : '';
    const imageFilename = imgSrc ? imgSrc.split('/').pop() : '';

    if (this.selectedOrders[id]) {
      this.selectedOrders[id].quantity += 1;
    } else {
      this.selectedOrders[id] = {
        name: name,
        quantity: 1,
        price: price,
        category: category,
        image: image || imageFilename
      };
      element.classList.add("selected");
    }

    const img = element.querySelector('img');
    if (img) this.animateFlyToCart(img, '#viewOrdersBtn');
    this.renderOrderSummary();
  }

  updateQuantity(input) {
    const id = input.dataset.id;
    const newQuantity = parseInt(input.value);

    if (this.selectedOrders[id] && newQuantity > 0) {
      this.selectedOrders[id].quantity = newQuantity;
      this.renderOrderSummary();
    }
  }

  clearOrders() {
    Object.keys(this.selectedOrders).forEach(key => {
      delete this.selectedOrders[key];
    });

    document.querySelectorAll('.menu-card').forEach(card => {
      card.classList.remove('selected');
      const qtyInput = card.querySelector('input[type="number"]');
      if (qtyInput) qtyInput.value = 1;
    });

    this.elements.selectedOrdersContainer.innerHTML = '';
    this.elements.advancePayment.value = '';
    this.renderOrderSummary();
  }

  renderOrderSummary() {
    const container = this.elements.selectedOrdersContainer;
    const emptyState = document.getElementById('emptyState');
    const orderTotal = document.getElementById('orderTotal');
    const totalItemsCount = document.getElementById('totalItemsCount');
    const totalQuantityEl = document.getElementById('totalQuantity');
    const grandTotalEl = document.getElementById('grandTotal');

    container.innerHTML = '';

    let total = 0;
    let totalQuantity = 0;
    const hasItems = Object.keys(this.selectedOrders).length > 0;

    if (hasItems) {
      if (emptyState) emptyState.classList.add('hidden');
      if (orderTotal) orderTotal.classList.remove('hidden');

      Object.entries(this.selectedOrders).forEach(([id, item]) => {
        total += item.price * item.quantity;
        totalQuantity += item.quantity;

        const getImagePath = () => {
          if (item.image && item.image !== 'default.jpg') {
            return `/storage/jeongol_menu/${item.image}`;
          }
          return 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjY0IiBoZWlnaHQ9IjY0IiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0yMCAyMEg0NEg0NEgyMFYyMFoiIGZpbGw9IiNEMUQ1REIiLz4KPHBhdGggZD0iTTI4IDI4SDM2VjM2SDI4VjI4WiIgZmlsbD0iIzlDQTNBRiIvPgo8L3N2Zz4K';
        };

        const defaultImage = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjY0IiBoZWlnaHQ9IjY0IiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0yMCAyMEg0NEg0NEgyMFYyMFoiIGZpbGw9IiNEMUQ1REIiLz4KPHBhdGggZD0iTTI4IDI4SDM2VjM2SDI4VjI4WiIgZmlsbD0iIzlDQTNBRiIvPgo8L3N2Zz4K';

        const orderItem = document.createElement('div');
        orderItem.innerHTML = `
        <div class="order-item-card bg-white rounded-xl p-4 border border-gray-200">
          <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
              <img src="${getImagePath()}" 
                   alt="${item.name}" 
                   class="w-20 h-20 object-cover rounded-lg border-2 border-gray-100"
                   onerror="this.src='${defaultImage}'">
            </div>
            
            <div class="flex-1 min-w-0">
              <h4 class="font-bold text-gray-900 text-base mb-1 truncate">${item.name}</h4>
              <p class="text-sm text-gray-500 mb-2">₱${item.price.toFixed(2)} each</p>
              
              <div class="flex items-center gap-2">
                <label class="text-xs text-gray-600 font-medium">Qty:</label>
                <input type="number" 
                       min="1" 
                       max="99" 
                       value="${item.quantity}" 
                       class="w-16 text-center px-2 py-1.5 border-2 border-gray-300 rounded-lg text-sm font-semibold focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all"
                       data-id="${id}"
                       onchange="dashboard.updateQuantity(this)"
                       onkeypress="return event.charCode >= 48 && event.charCode <= 57">
              </div>
            </div>
            
            <div class="flex flex-col items-end gap-2">
              <button type="button" 
                      onclick="dashboard.removeOrderItem('${id}')" 
                      class="w-8 h-8 flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors"
                      title="Remove item">
                <i class="fas fa-times text-sm"></i>
              </button>
              <div class="text-right">
                <p class="text-xs text-gray-500 mb-0.5">Subtotal</p>
                <p class="text-lg font-bold text-red-600">₱${(item.price * item.quantity).toFixed(2)}</p>
              </div>
            </div>
          </div>
        </div>
      `;
        container.appendChild(orderItem);
      });

      if (totalItemsCount) {
        totalItemsCount.textContent = `${totalQuantity} item${totalQuantity !== 1 ? 's' : ''}`;
      }
      if (totalQuantityEl) {
        totalQuantityEl.textContent = totalQuantity;
      }
      if (grandTotalEl) {
        grandTotalEl.textContent = `₱${total.toFixed(2)}`;
      }
    } else {
      if (emptyState) emptyState.classList.remove('hidden');
      if (orderTotal) orderTotal.classList.add('hidden');
      if (totalItemsCount) {
        totalItemsCount.textContent = '0 items';
      }
    }

    if (!this.isPlacingOrder && this.elements.advancePayment) {
      const halfTotal = (total / 2).toFixed(2);
      this.elements.advancePayment.value = halfTotal;
    }

    if (this.elements.totalQuantity) {
      this.elements.totalQuantity.textContent = totalQuantity;
    }
  }

  removeOrderItem(id) {
    if (this.selectedOrders[id]) {
      delete this.selectedOrders[id];
    }

    const menuCard = document.querySelector(`.menu-card[data-id="${id}"]`);
    if (menuCard) {
      menuCard.classList.remove('selected');

      const qtyInput = menuCard.querySelector('input[type="number"]');
      if (qtyInput) {
        qtyInput.value = 1;
      }
    }

    this.renderOrderSummary();
  }

  resetForm() {
    const fieldsToReset = [
      this.elements.customerName,
      this.elements.contactNumber,
      this.elements.numberOfPax,
      this.elements.customerNotes,
      this.elements.advancePayment
    ];

    this.clearOrders();
  }

      async handleSubmitReservation() {
    if (this.elements.submitBtn.disabled) return;

    const name = this.elements.customerName.value.trim();
    const contact = this.elements.contactNumber.value.trim();
    const paxInput = document.getElementById('numberOfPax');
    const dateInput = document.getElementById('reserved_date');
    const timeInput = document.getElementById('arrivalTimeInput');

    const paxCount = parseInt(paxInput?.value) || 0;
    const date = dateInput?.value;
    const time = timeInput?.value;

    const emptyFields = [];
    if (!name) emptyFields.push(this.elements.customerName);
    if (!this.isPlacingOrder && !contact) emptyFields.push(this.elements.contactNumber);
    if (!paxCount) emptyFields.push(paxInput);
    if (!date) emptyFields.push(dateInput);
    if (!time) emptyFields.push(timeInput);

    if (emptyFields.length > 0) {
      emptyFields.forEach(f => f.classList.add('border-red-500'));
      this.showErrorToast('Please fill in all required fields.');
      return;
    }

    /*
    if (!this.isPlacingOrder) {
      const selectedDateTime = new Date(`${date}T${time}`);
      const currentTime = new Date();
      const minAllowedTime = new Date(currentTime.getTime() + (2 * 60 * 60 * 1000));

      if (selectedDateTime < minAllowedTime) {
        this.showErrorToast('Reservation must be at least 2 hours from now.');
        dateInput.classList.add('border-red-500');
        timeInput.classList.add('border-red-500');
        return;
      }
    }
*/
    


    const orders = Object.values(this.selectedOrders || {});
    const hasMain = orders.some(item => item.category === 'main');

    if (!hasMain) {
      this.showErrorToast('You must order at least one main menu item.');
      return;
    }

    const mainMenuOrders = orders.filter(item => item.category === 'main');
    const totalMainMenuQuantity = mainMenuOrders.reduce((sum, item) => sum + item.quantity, 0);

    if (totalMainMenuQuantity !== paxCount) {
      this.showErrorToast('Match your main menu order quantity to your number of pax.');
      return;
    }

    this.elements.submitBtn.disabled = true;
    this.elements.submitBtn.textContent = "Submitting...";

    const data = this.prepareSubmissionData();
    this.submitOrder(data);
  }



  showErrorToast(message) {
    const toast = document.createElement('div');
    toast.textContent = message;
    toast.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background-color: #ef4444;
      color: white;
      padding: 12px 16px;
      border-radius: 6px;
      z-index: 9999;
      font-size: 14px;
      max-width: 300px;
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
      document.body.removeChild(toast);
    }, 3000);
  }

  prepareSubmissionData() {
    const baseData = {
      customer_name: this.elements.customerName.value.trim(),
      contact_number: this.elements.contactNumber.value.trim(),
      pax: this.elements.numberOfPax.value.trim(),
      table_id: this.selectedTableId,
      orders: Object.entries(this.selectedOrders).map(([id, item]) => ({
        menu_id: id,
        quantity: item.quantity,
        price: item.price,
        notes: this.elements.customerNotes.value.trim()
      }))
    };

    if (this.isPlacingOrder) {
      baseData.started_at = this.elements.arrivalTimeInput.value;
    } else {
      baseData.reserved_date = this.elements.reservedDate.value;
      baseData.arrival_time = this.elements.arrivalTimeInput.value;
      baseData.advance_payment = this.elements.advancePayment.value.trim() || 0;
    }

    return baseData;
  }

  submitOrder(data) {
    const storeUrl = this.isPlacingOrder
      ? '/receptionist/store-walkin'
      : '/receptionist/store-reservation';

    fetch(storeUrl, {
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
          console.error('Server error:', errorText);
          throw new Error(`Server responded with ${res.status}: ${errorText}`);
        }
        return res.json();
      })
      .then(response => {
        if (response.success) {
          this.elements.tableModal.classList.add('hidden');
          this.showSuccessModal(this.isPlacingOrder ? 'order' : 'reservation');
        } else {
          this.showErrorModal(response.message || "Failed to save reservation.");
        }
      })
      .catch(error => {
        console.error("Reservation Error:", error);
        alert("An error occurred while submitting the reservation: " + error.message);
      })
      .finally(() => {
        this.elements.submitBtn.disabled = false;
        this.elements.submitBtn.textContent = "Submit";
      });
  }

  updateTimeFrameDisplay() {
    const timeFrameDisplay = this.elements.timeFrameDisplay;
    if (!timeFrameDisplay || !this.elements.arrivalTimeInput.value) {
      if (timeFrameDisplay) timeFrameDisplay.textContent = '';
      return;
    }

    const [hours, minutes] = this.elements.arrivalTimeInput.value.split(':').map(Number);
    const start = new Date();
    start.setHours(hours, minutes, 0, 0);

    const end = new Date(start.getTime() + 2 * 60 * 60 * 1000);

    const options = { hour: 'numeric', minute: '2-digit', hour12: true };
    const startStr = start.toLocaleTimeString('en-US', options);
    const endStr = end.toLocaleTimeString('en-US', options);

    timeFrameDisplay.textContent = `${startStr} - ${endStr}`;
  }

  showSuccessModal(type) {
    const modal = this.elements.successModal;
    const title = document.getElementById('successTitle');

    if (title) {
      title.textContent = type === 'order' ? 'Order Placed!' : 'Reservation Created!';
    }

    if (modal) {
      modal.classList.remove('hidden');
    }
  }

  closeSuccessModal() {
    if (this.elements.successModal) {
      this.elements.successModal.classList.add('hidden');
    }

    if (this.elements.tableModal) {
      this.elements.tableModal.style.display = 'none';
    }
  }

  showErrorModal(message) {
    let errorModal = document.getElementById('errorModal');
    if (!errorModal) {
      errorModal = this.createErrorModal();
    }

    document.getElementById('errorMessage').textContent = message;
    errorModal.classList.remove('hidden');
  }

  createErrorModal() {
    const errorModal = document.createElement('div');
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

    return errorModal;
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

    const container = document.getElementById('fly-animation-container');
    if (container) {
      container.appendChild(flyingImg);
    } else {
      document.body.appendChild(flyingImg);
    }

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
}


  const dashboard = new ReceptionistDashboard();

  window.selectMenuItem = function (element) {
    dashboard.selectMenuItem(element);
  };

  window.updateQuantity = function (input) {
    dashboard.updateQuantity(input);
  };

  window.walkIn = function (tableId) {
    dashboard.walkIn(tableId);
  };

  window.makeReservation = function (tableId) {
    dashboard.makeReservation(tableId);
  };

  window.openNotifModal = function (id) {
    dashboard.openNotificationModal(id);
  };

  window.dashboard = dashboard;
</script>


</html>
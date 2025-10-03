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
            <p><strong>Name</strong> <span id="customername">N/A</span></p>
            <p><strong>Table number</strong> <span id="tableNumber">N/A</span></p>
            <div>
              <p><strong>Transaction Receipt</strong></p>
              <img id="paymentProof"
                class="mb-2 w-20 h-20 object-cover rounded border cursor-pointer hover:opacity-80 transition-opacity"
                style="display:none;" onclick="openImageModal(this.src)">
            </div>
            <p><strong>Required Amount:</strong> <span id="requiredAmount">N/A</span></p>
            <p><strong>Pax:</strong> <span id="paxCount">N/A</span></p>
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

    <div id="fly-animation-container" style="position: fixed; top: 0; left: 0; pointer-events: none; z-index: 9999;">
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

    <div class="table-layout">
      @foreach($tables as $table)
      <div class="table-link" data-table-id="{{ $table->id }}" data-table-number="{{ $table->table_number }}">
      <div class="table {{ $table->is_occupied ? 'occupied' : 'available' }}">
        <div class="table-number text-center">Table {{ $table->table_number }}</div>

        @if($table->is_occupied)
      <div class="status-indicator text-center">OCCUPIED</div>
      @endif

        <div class="inline-options text-center"
        style="display:none; flex-direction: column; align-items: center; gap: 5px; margin-top: 10px;">
        <button
          class="place-order-btn bg-blue-800 text-white border-none px-2.5 py-1.5 my-[3px] rounded cursor-pointer text-[17px] hover:bg-blue-700"
          data-table-id="{{ $table->id }}">Place Order</button>
        <button
          class="make-reservation-btn bg-blue-800 text-white border-none px-2.5 py-1.5 my-[3px] rounded cursor-pointer text-[17px] hover:bg-blue-700"
          data-table-id="{{ $table->id }}">Make Reservation</button>
        </div>
      </div>
      </div>
    @endforeach
    </div>

    <div class="bottom-buttons">
      <a class="view-button" href="{{ route('receptionist.bookings') }}">Today's Bookings</a>
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
          <input type="text" id="contactNumber" required maxlength="11" placeholder="09xxxxxxxxx"
            class="border border-gray-400 focus:border-black-500 p-2 rounded w-full"
            oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
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

      <div id="default-modal" tabindex="-1" aria-hidden="true"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex justify-center items-center">
        <div class="relative w-full max-w-2xl h-[450px]">
          <div class="relative h-full bg-white rounded-lg shadow flex flex-col">
            <div class="modal-section flex-shrink-0">
              <div class="flex items-center justify-between p-4 rounded-t bg-red-800">
                <h3 class="text-xl font-semibold text-white">Orders Breakdown</h3>
                <div class="text-white text-sm">
                  <span id="totalItemsCount" class="font-medium">0 items</span>
                </div>
              </div>
            </div>

            <div id="orderSummary"
              class="flex-1 p-4 bg-gray-50 text-sm text-gray-800 border overflow-y-auto overflow-x-hidden"
              style="max-height: calc(450px - 140px);">

              <div id="emptyState" class="flex flex-col items-center justify-center h-full text-gray-500 hidden">
                <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <p class="text-lg font-medium">No orders!</p>
              </div>

              <div id="selectedOrdersContainer" class="space-y-2">
              </div>

              <div id="orderTotal" class="mt-4 pt-3 border-t border-gray-300 bg-white rounded-lg p-3 shadow-sm hidden">
                <div class="flex justify-between items-center mb-2">
                  <span class="text-base font-semibold text-gray-700">Total Quantity:</span>
                  <span id="totalQuantity" class="text-base font-bold text-orange-600">0</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-lg font-semibold text-gray-800">Total:</span>
                  <span id="grandTotal" class="text-lg font-bold text-red-600">₱0.00</span>
                </div>
              </div>
            </div>

            <div class="flex justify-between gap-4 p-4 border-t border-gray-200 bg-white flex-shrink-0">
              <div class="flex gap-2">
                <button id="clearOrdersBtn" type="button"
                  class="bg-red-600 hover:bg-red-700 text-white font-medium text-sm px-5 py-2.5 rounded-lg focus:outline-none focus:ring-4 focus:ring-red-300 transition-colors">
                  <i class="fas fa-trash mr-2"></i>Clear All
                </button>
              </div>
              <div class="flex gap-2">
                <button data-modal-hide="default-modal" type="button"
                  class="bg-green-600 hover:bg-green-700 text-white font-medium text-sm px-5 py-2.5 rounded-lg focus:outline-none focus:ring-4 focus:ring-green-300 transition-colors">
                  Confirm Order
                </button>
              </div>
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

      <div id="paymentMethodModal"
        class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 mx-4 max-w-sm w-full text-center">
          <div class="mb-4">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Payment Method</h2>
            <p class="text-gray-600 mb-6">How will you pay for this reservation?</p>

            <div class="space-y-3">
              <button id="cashPayment"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                Cash
              </button>
              <button id="gcashPayment"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                <span class="font-bold text-lg">G</span><span class="font-normal">Cash</span>
              </button>
              <button id="mayaPayment"
                class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                <span class="font-bold text-lg italic">Maya</span>
              </button>
            </div>

            <button id="cancelPaymentMethod" class="mt-4 text-gray-500 hover:text-gray-700 text-sm">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

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
            this.elements.viewOrdersBtn.addEventListener('click', () => {
              this.elements.defaultModal.classList.remove('hidden');
            });

            const closeButtons = this.elements.defaultModal.querySelectorAll('[data-modal-hide="default-modal"]');
            closeButtons.forEach(btn => {
              btn.addEventListener('click', () => {
                this.elements.defaultModal.classList.add('hidden');
              });
            });

            this.elements.defaultModal.addEventListener('click', (e) => {
              if (e.target === this.elements.defaultModal) {
                this.elements.defaultModal.classList.add('hidden');
              }
            });
          }

          const paymentMethodModal = document.getElementById('paymentMethodModal');
          const cashPayment = document.getElementById('cashPayment');
          const gcashPayment = document.getElementById('gcashPayment');
          const mayaPayment = document.getElementById('mayaPayment');
          const cancelPaymentMethod = document.getElementById('cancelPaymentMethod');

          if (cashPayment) {
            cashPayment.addEventListener('click', () => this.selectPaymentMethod('Cash'));
          }
          if (gcashPayment) {
            gcashPayment.addEventListener('click', () => this.selectPaymentMethod('GCash'));
          }
          if (mayaPayment) {
            mayaPayment.addEventListener('click', () => this.selectPaymentMethod('Maya'));
          }
          if (cancelPaymentMethod) {
            cancelPaymentMethod.addEventListener('click', () => this.hidePaymentMethodModal());
          }

          if (paymentMethodModal) {
            paymentMethodModal.addEventListener('click', (e) => {
              if (e.target === e.currentTarget) {
                this.hidePaymentMethodModal();
              }
            });
          }

          window.onclick = (e) => {
            if (e.target === this.elements.tableModal) {
              this.elements.tableModal.style.display = 'none';
            }
          };
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
        document.querySelectorAll('.place-order-btn').forEach(btn => {
          btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const tableId = parseInt(btn.getAttribute('data-table-id'));
            this.makeOrder(tableId);
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
          this.elements.notifList.innerHTML =
            '<li class="no-notifs p-3 text-center text-gray-500">No notifications</li>';
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
        li.className = 'p-3 bg-gray-100 rounded cursor-pointer mb-2';
        li.dataset.reservationId = notification.reservation_id;
        li.dataset.notification = JSON.stringify(notification);
        li.onclick = () => this.openNotificationModalDirect(notification);

        let badgeClass = "bg-gray-300 text-gray-700";
        if (notification.reservation_status === "Active") badgeClass = "bg-green-100 text-green-700";
        else if (notification.reservation_status === "Rejected") badgeClass = "bg-red-100 text-red-700";

        const formatReservationTime = (startDateTime, endDateTime) => {
          if (!startDateTime || !endDateTime) return 'N/A';

          const startDate = new Date(startDateTime);
          const endDate = new Date(endDateTime);

          const dateStr = startDate.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric'
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

          return `${dateStr}, ${startTime} - ${endTime}`;
        };

        const reservationTimeRange = formatReservationTime(notification.reservation_time, notification.reservation_end_time);

        li.innerHTML = `
      <div class="flex justify-between items-center">
          <div>
              <p class="text-md font-semibold">${notification.customer_name}</p>
              <p class="text-xs text-gray-500">${notification.message}</p>
              <p class="text-xs text-gray-400 mt-1">${reservationTimeRange}</p>
          </div>
          <span class="px-2 py-1 text-xs font-semibold rounded-full ${badgeClass}">
              ${notification.reservation_status}
          </span>
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
            paymentProof.src = `/storage/${notification.payment_proof}`;
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
            paymentProof.src = `/file-serve/${data.payment.proof_path}`;
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
        if (!this.reservationId) return;

        fetch(this.elements.acceptForm.action, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
          }
        })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              this.elements.paymentModal.classList.add('hidden');
              this.updateNotificationBadges(data.unread_count);
              this.updateNotificationStatus(data.reservationId, data.status);
              this.reservationId = null;
            } else {
              alert(data.message || 'Failed to accept reservation');
            }
          })
          .catch(err => {
            console.error(err);
            alert('Server error');
          });
      }

      handleCancelReservation() {
        if (!this.reservationId) return;

        fetch(`/receptionist/cancel-reservation/${this.reservationId}`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
          }
        })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              this.elements.paymentModal.classList.add('hidden');
              const li = this.elements.notifList.querySelector(`[data-reservation-id="${this.reservationId}"]`);
              if (li) {
                li.classList.add("text-red-600", "font-semibold");
                li.innerHTML += '<p class="text-xs">Cancelled</p>';
              }
              this.reservationId = null;
            } else {
              alert(data.message || "Failed to cancel reservation");
            }
          })
          .catch(err => {
            console.error(err);
            alert("Server error while cancelling reservation.");
          });
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

      handleTableClick(link) {
        document.querySelectorAll('.inline-options').forEach(opt => opt.style.display = 'none');
        const options = link.querySelector('.inline-options');
        if (options) options.style.display = 'block';
      }

      makeOrder(tableId) {
        this.selectedTableId = tableId;
        this.isPlacingOrder = true;

        this.resetForm();       
        this.setupOrderForm();   

        this.elements.tableModal.style.display = 'flex';
      }

      makeReservation(tableId) {
        this.selectedTableId = tableId;
        this.isPlacingOrder = false;
        this.elements.tableModal.style.display = 'flex';

        this.setupReservationForm();
        this.resetForm();
        this.updateTimeFrameDisplay();
      }

      makeOrder(tableId) {
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

        const today = now.getFullYear() + '-' +
          String(now.getMonth() + 1).padStart(2, '0') + '-' +
          String(now.getDate()).padStart(2, '0');
        this.elements.reservedDate.value = today;

        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        this.elements.arrivalTimeInput.value = `${hours}:${minutes}`;
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
            orderItem.className = "bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow";
            orderItem.innerHTML = `
          <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
              <img src="${getImagePath()}" alt="${item.name}" 
                   class="w-16 h-16 object-cover rounded-lg border border-gray-200"
                   onerror="this.src='${defaultImage}'">
            </div>
            
            <div class="flex-1 min-w-0">
              <h4 class="text-base font-semibold text-gray-900 truncate">${item.name}</h4>
              <p class="text-sm text-gray-600">₱${item.price.toFixed(2)} each</p>
              <p class="text-sm font-medium text-orange-600">Subtotal: ₱${(item.price * item.quantity).toFixed(2)}</p>
            </div>
            
            <div class="flex items-center space-x-3">
              <input type="number" min="1" max="99" value="${item.quantity}" 
                     class="w-16 text-center px-2 py-1 border border-gray-300 rounded-md text-sm font-medium focus:ring-2 focus:ring-red-500 focus:border-red-500"
                     data-id="${id}"
                     onchange="dashboard.updateQuantity(this)"
                     onkeypress="return event.charCode >= 48 && event.charCode <= 57">
            </div>
            
            <button type="button" onclick="dashboard.removeOrderItem('${id}')" 
                    class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-red-100 hover:bg-red-200 text-red-600 rounded-full transition-colors"
                    title="Remove item">
              <span class="text-sm font-bold">×</span>
            </button>
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

      handleSubmitReservation() {
        if (this.elements.submitBtn.disabled) return;

        const [hours, minutes] = (this.elements.arrivalTimeInput.value || "00:00").split(':').map(Number);
        const timeInMinutes = hours * 60 + minutes;
        const minTime = 690;
        const maxTime = this.isPlacingOrder ? 1200 : 1080;

        if (timeInMinutes < minTime || timeInMinutes > maxTime) {
          this.showErrorToast(`Invalid time chosen. Please select a time between 11:30 AM and ${this.isPlacingOrder ? "8:00 PM" : "6:00 PM"}.`);
          return;
        }

        if (!this.elements.customerName.value.trim()) {
          this.showErrorToast('Please enter customer name.');
          return;
        }

        if (!this.isPlacingOrder && !this.elements.contactNumber.value.trim()) {
          this.showErrorToast('Please enter contact number.');
          return;
        }

        const paxInput = document.getElementById('numberOfPax');
        const paxCount = parseInt(paxInput.value) || 0;

        if (paxCount <= 0) {
          this.showErrorToast('Please enter a valid number of pax.');
          return;
        }

        if (!this.isPlacingOrder) {
          const orders = Object.values(this.selectedOrders);
          const hasMain = orders.some(item => item.category === 'main');

          if (!hasMain) {
            this.showErrorToast('Please add at least one main menu item to continue.');
            return;
          }

          const mainMenuOrders = orders.filter(item => item.category === 'main');
          const totalMainMenuQuantity = mainMenuOrders.reduce((sum, item) => sum + item.quantity, 0);

          if (totalMainMenuQuantity !== paxCount) {
            this.showErrorToast(`Match your main menu order to your pax.`);
            return;
          }
        }

        if (!this.isPlacingOrder) {
          this.elements.submitBtn.disabled = true;
          this.elements.submitBtn.textContent = "Processing...";
          this.showPaymentMethodModal();
        } else {
          this.elements.submitBtn.disabled = true;
          this.elements.submitBtn.textContent = "Submitting...";
          const data = this.prepareSubmissionData();
          this.submitOrder(data);
        }
      }

      showPaymentMethodModal() {
        const modal = document.getElementById('paymentMethodModal');
        if (modal) {
          modal.classList.remove('hidden');
        }
      }

      hidePaymentMethodModal() {
        const modal = document.getElementById('paymentMethodModal');
        if (modal) {
          modal.classList.add('hidden');
        }
        this.elements.submitBtn.disabled = false;
        this.elements.submitBtn.textContent = "Submit";
      }

      selectPaymentMethod(method) {
        this.selectedPaymentMethod = method;
        this.hidePaymentMethodModal();

        const data = this.prepareSubmissionData();
        data.payment_method = method;
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
        return {
          customer_name: this.elements.customerName.value.trim(),
          contact_number: this.elements.contactNumber.value.trim(),
          pax: this.elements.numberOfPax.value.trim(),
          reserved_date: this.elements.reservedDate.value,
          arrival_time: this.elements.arrivalTimeInput.value,
          table_id: this.selectedTableId,
          is_order: this.isPlacingOrder,
          advance_payment: this.elements.advancePayment.value.trim(),
          orders: Object.entries(this.selectedOrders).map(([id, item]) => ({
            menu_id: id,
            quantity: item.quantity,
            price: item.price,
            notes: this.elements.customerNotes.value.trim()
          }))
        };
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

      function openImageModal(imageSrc) {
        let modal = document.getElementById('imageModal');
        if (!modal) {
          modal = document.createElement('div');
          modal.id = 'imageModal';
          modal.innerHTML = `
      <div class="fixed inset-0 bg-black bg-opacity-10 flex items-center justify-center z-50" onclick="closeImageModal()">
          <div class="relative max-w-4xl max-h-[90vh] p-4">
              <button onclick="closeImageModal()" class="absolute top-2 right-2 text-white bg-black bg-opacity-50 rounded-full w-8 h-8 flex items-center justify-center hover:bg-opacity-70">×</button>
              <img id="modalImage" src="" class="max-w-full max-h-full object-contain rounded border shadow-lg bg-white p-4">
          </div>
      </div>
    `;
          document.body.appendChild(modal);
        }

        document.getElementById('modalImage').src = imageSrc;
        modal.style.display = 'block';
      }

      function closeImageModal() {
        const modal = document.getElementById('imageModal');
        if (modal) {
          modal.style.display = 'none';
        }
      }

      const dashboard = new ReceptionistDashboard();

      window.selectMenuItem = function (element) {
        dashboard.selectMenuItem(element);
      };

      window.updateQuantity = function (input) {
        dashboard.updateQuantity(input);
      };

      window.makeOrder = function (tableId) {
        dashboard.makeOrder(tableId);
      };

      window.makeReservation = function (tableId) {
        dashboard.makeReservation(tableId);
      };

      window.openNotifModal = function (id) {
        dashboard.openNotificationModal(id);
      };

      window.dashboard = dashboard;
    </script>
  </div>
</body>

</html>
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
</head>

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

<body>
  <div class="relative">
    <!-- Header -->
    <header class="mt-2 border-b border-gray-200 flex items-center justify-between px-7">
      <div class="logo flex items-center ml-5">
        <img src="{{ asset('logo/jeongol_logo.jpg') }}" alt="Jeongol Logo" class="h-13 w-20" />
      </div>

      <div class="relative">
        <!-- Profile Button -->
        <button id="userBtn" class="relative flex items-center gap-2 p-4 hover:bg-gray-100 z-50">
          <div class="w-14 h-14 rounded-full bg-gray-300 flex items-center justify-center font-bold text-black">
            {{ strtoupper(substr(Auth::user()->firstname, 0, 1)) }}
          </div>
          <span id="notifBadge"
            class="absolute top-1 right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold text-white bg-red-600 rounded-full"
            style="{{ auth()->user()?->unreadNotifications->count() ? '' : 'display:none;' }}">
            {{ auth()->user()?->unreadNotifications->count() ?? 0 }}
          </span>
        </button>
        <!-- User Dropdown -->
        <div id="userMenu" class="hidden absolute top-full right-0 mt-2 w-56 bg-white border rounded-lg shadow-lg z-50">
          <div class="px-4 py-3 border-b">
            <p class="text-sm font-medium">{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</p>
            <p class="text-xs text-gray-500">{{ Auth::user()->role }}</p>
          </div>

          <a href="javascript:void(0)" id="notifBtn" class="block px-4 py-2 hover:bg-gray-100 relative">
            <span id="notifBadge"
              class="absolute top-1 right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold text-white bg-red-600 rounded-full"
              style="{{ auth()->user()?->unreadNotifications->count() ? '' : 'display:none;' }}">
              {{ auth()->user()?->unreadNotifications->count() ?? 0 }}
            </span>Notifications</a>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">Logout</button>
          </form>
        </div>
      </div>
    </header>

    <!-- Notifications Modal -->
    <div id="notifModal"
      class="hidden fixed inset-0 flex items-start justify-end z-50 bg-black bg-opacity-20 p-4 overflow-auto">
      <div class="w-full max-w-xs sm:w-80 bg-white rounded-lg shadow-lg">
        <div class="p-5 relative">
          <h2 class="text-lg font-semibold mb-4">Notifications</h2>
          <ul id="notifList" class="space-y-2 max-h-96 overflow-y-auto">
            @foreach(auth()->user()?->unreadNotifications ?? [] as $n)
        <li class="p-3 bg-gray-100 rounded cursor-pointer" data-reservation-id="{{ $n->data['reservation_id'] }}">
          <p class="text-sm font-medium">{{ $n->data['name'] ?? 'Unknown' }}</p>
          <p class="text-xs text-gray-500">{{ $n->data['message'] ?? '' }}</p>
          <p class="text-xs text-gray-400 mt-1">{{ $n->created_at->diffForHumans() }}</p>
        </li>
      @endforeach
            @if((auth()->user()?->unreadNotifications->count() ?? 0) === 0)
        <li class="p-3 text-center text-gray-500">No notifications</li>
      @endif
          </ul>
          <button id="notifClose" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900">✖</button>
        </div>
      </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal"
      class="hidden fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50 p-4">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md mx-auto">
        <h2 class="text-lg font-bold mb-4">Payment Details</h2>
        <div>
          <p>Transaction Receipt</p>
          <img id="paymentProof" src="" class="mb-2 w-full object-contain" style="display:none;">
        </div>
        <p><strong>Required Amount:</strong> <span id="requiredAmount">N/A</span></p>
        <p><strong>Status:</strong> <span id="paymentStatus">N/A</span></p>
        <div class="mt-4 text-center flex justify-center gap-2">
          <form id="acceptForm" method="POST" class="inline">
            @csrf
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Accept</button>
          </form>
          <button id="closePaymentBtn" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Close</button>
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

  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;"> @csrf </form>

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
        onclick="event.stopPropagation(); makeReservation({{ $table->id }})">Make
        Reservation</button>
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

      <div class="modal-section">
        <label><strong>Advance Payment </strong></label>
        <input class="border border-gray-400 focus:border-gray-700 p-2 rounded w-full" type="number"
          id="advance_payment" readonly>
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

          <div class="modal-section ">
            <div class="flex items-center justify-between p-3 rounded-t bg-red-800">
              <h3 class="text-lg font-semibold text-white">
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

    <div></div>

  </div>

  <script>
    const userBtn = document.getElementById('userBtn');
    const userMenu = document.getElementById('userMenu');

    userBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      userMenu.classList.toggle('hidden');
    });

    userMenu.addEventListener('click', (e) => {
      e.stopPropagation();
    });

    document.addEventListener('click', () => {
      userMenu.classList.add('hidden');
    });

    const notifBtn = document.getElementById('notifBtn');
    const notifModal = document.getElementById('notifModal');
    const notifClose = document.getElementById('notifClose');

    notifBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      notifModal.classList.remove('hidden');
    });
    notifClose.addEventListener('click', () => notifModal.classList.add('hidden'));

    notifModal.addEventListener('click', () => notifModal.classList.add('hidden'));
    notifModal.querySelector('div').addEventListener('click', (e) => e.stopPropagation());

    const paymentModal = document.getElementById('paymentModal');
    const paymentProof = document.getElementById('paymentProof');
    const requiredAmount = document.getElementById('requiredAmount');
    const paymentStatus = document.getElementById('paymentStatus');
    const acceptForm = document.getElementById('acceptForm');
    const closePaymentBtn = document.getElementById('closePaymentBtn');
    let reservationId = null;

    document.querySelectorAll('#notifList li[data-reservation-id]').forEach(li => {
      li.addEventListener('click', () => {
        reservationId = li.dataset.reservationId;
        fetch(`/payments/${reservationId}`)
          .then(res => res.json())
          .then(data => {
            paymentModal.classList.remove('hidden');
            if (data.payment?.proof_path) {
              paymentProof.src = `/storage/${data.payment.proof_path}`;
              paymentProof.style.display = 'block';
            } else {
              paymentProof.style.display = 'none';
            }
            requiredAmount.textContent = data.advance_payment ?? 'N/A';
            paymentStatus.textContent = data.payment?.status ?? 'N/A';
            acceptForm.action = `/receptionist/accept-reservation/${reservationId}`;
          })
          .catch(err => {
            console.error(err);
            alert('Failed to load payment details.');
          });
      });
    });

    closePaymentBtn.addEventListener('click', () => {
      paymentModal.classList.add('hidden');
      paymentProof.src = '';
      reservationId = null;
      requiredAmount.textContent = 'N/A';
      paymentStatus.textContent = 'N/A';
    });
  </script>

  @include('receptionist.components.script')

</body>

</html>
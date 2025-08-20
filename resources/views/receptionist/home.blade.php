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

<body>
  <header class="mt-2">
    <div x-data="{ show: false, payment: {}, showImage: false, reservationId: null }" x-on:open-payment.window="
        reservationId = $event.detail.id;
        fetch('/payments/' + reservationId)
          .then(res => res.json())
          .then(data => { payment = data; show = true })
">
      <div x-show="show" x-cloak class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96 relative">
          <h2 class="text-lg font-bold mb-4">Payment Details</h2>

          <div>
            <p>Transaction Receipt</p>
            <template x-if="payment?.payment?.proof_path">
              <img :src="'/storage/' + payment.payment.proof_path">
            </template>

          </div>

          <p>
            <span class="font-semibold">Required Amount:</span>
            <span x-text="payment.advance_payment ?? 'N/A'"></span>
          </p>

          <p>
            <span class="font-semibold">Status:</span>
            <span x-text="payment.payment?.status ?? 'N/A'"></span>
          </p>

          <div class="mt-4 text-center">

            <form :action="'/receptionist/accept-reservation/' + reservationId" method="POST" style="display:inline;">
              @csrf
              <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Accept
              </button>
            </form>

            <button @click="show = false" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
              Close
            </button>
          </div>
        </div>
      </div>

      <div x-show="showImage" x-cloak
        class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-[100]"
        @click="showImage = false">
        <div class="relative max-w-3xl max-h-[90vh] w-auto">
          <img :src="'/storage/' + payment.payment?.proof_path" alt="Full Proof"
            class="max-w-full max-h-[90vh] rounded shadow-lg object-contain">
          <button @click.stop="showImage = false"
            class="absolute top-2 right-2 bg-red-600 text-white px-3 py-1 rounded">
            ✖
          </button>
        </div>
      </div>
    </div>

    <div class="border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-7 relative">

      <div class="flex items-center ml-5">
        <img src="{{ asset('logo/jeongol_logo.jpg') }}" alt="Jeongol Logo" class="h-13 w-20" />
      </div>

      <div class="relative z-0" x-data="{ open: false, notifModal: false }">

        <button @click="open = !open" class="flex items-center gap-2 p-4 hover:bg-gray-100">
          <div class="w-14 h-14 rounded-full bg-gray-300 flex items-center justify-center text-black font-bold">
            {{ strtoupper(substr(Auth::user()->firstname, 0, 1)) }}
          </div>
        </button>

        <div x-show="open" @click.away="open = false"
          class="absolute top-full right-0 mt-2 w-56 bg-white border rounded-lg shadow-lg z-50">

          <div class="px-4 py-3 border-b">
            <p class="text-sm font-medium text-gray-700">
              {{ Auth::user()->firstname }} {{ Auth::user()->lastname }}
            </p>
            <p class="text-xs text-gray-500">
              {{ Auth::user()->role }}
            </p>
          </div>

          <a href="javascript:void(0)" @click="notifModal = true; open = false"
            class="block px-4 py-2 hover:bg-gray-100">Notifications</a>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">Logout</button>
          </form>
        </div>

        <div x-show="notifModal" x-cloak
          class="absolute top-0 right-20 mt-16 w-80 bg-white rounded-lg shadow-lg border z-50">
          <div class="p-5 relative">
            <h2 class="text-lg font-semibold mb-4">Notifications</h2>

            @php
        $notifications = auth()->user()?->notifications ?? collect();
      @endphp

            <ul class="space-y-2 max-h-60 overflow-y-auto">
              @forelse($notifications as $n)
          <li class="p-3 bg-gray-100 rounded flex justify-between items-start">
          <div>
            <p class="text-sm font-medium text-gray-700">
            {{ $n->data['name'] ?? 'Unknown' }}
            </p>
            <p class="text-xs text-gray-500">
            {{ $n->data['message'] ?? '' }}
            </p>

            <p class="text-xs text-gray-400 mt-1">
            {{ $n->created_at->diffForHumans() }}
            </p>
          </div>

          <button class="text-blue-500 text-xs underline"
            @click="$dispatch('open-payment', { id: {{ $n->data['reservation_id'] ?? 'null' }} })">
            View
          </button>

          </li>
        @empty
          <li class="p-3 text-center text-gray-500">No notifications</li>
        @endforelse
            </ul>

            <button @click="notifModal = false" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900">
              ✖
            </button>
          </div>
        </div>
      </div>
    </div>
  </header>

  @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
    class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded-lg shadow-md max-w-sm text-center z-[1000]">
    {{ session('success') }}
    </div>
  @endif


  <div id="fly-animation-container" style="position: fixed; top: 0; left: 0; pointer-events: none; z-index: 9999;">
  </div>


  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;"> @csrf </form>

  <div class="time-display" id="manilaTimeDisplay"></div>

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
    <a class="view-button" href="{{ route('receptionist.view_kitchen') }}">View Kitchen</a>
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
    window.jeongolConfig = {
      storeReservationUrl: "{{ route('receptionist.storeReservation') }}",
      csrfToken: "{{ csrf_token() }}"
    };

    document.addEventListener("alpine:init", () => {
      window.addEventListener("open-payment", (e) => {
        const reservationId = e.detail.id;

        console.log("Opening payment modal for reservation:", reservationId);

        const input = document.getElementById("payment_reservation_id");
        if (input) {
          input.value = reservationId;
        }

        if (window.Alpine) {
          const root = document.querySelector('[x-data]');
          if (root && root.__x) {
            root.__x.$data.reservationId = reservationId;
          }
          Alpine.store("paymentModal", true);
        }

        fetchPayment(reservationId);
      });
    });

    function fetchPayment(id) {
      fetch(`/payments/${id}`)
        .then(res => res.json())
        .then(data => {
          const root = document.querySelector('[x-data]');
          if (root && root.__x) {
            root.__x.$data.payment = data;
            root.__x.$data.show = true;
          }
        })
        .catch(error => {
          console.error('Payment fetch error:', error);
          alert('Failed to load payment details.');
        });
    }
  </script>

  @include('receptionist.components.script')

</body>

</html>
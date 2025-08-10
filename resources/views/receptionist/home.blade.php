<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Receptionist Page</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="shortcut icon" type="x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  @include('receptionist.components.css')
  @vite('resources/css/app.css')
</head>

<body>
  <!-- Flying animation container (required for animation to work) -->
  <div id="fly-animation-container" style="position: fixed; top: 0; left: 0; pointer-events: none; z-index: 9999;">
  </div>

  <a class="logout-button top-logout" href="#"
    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
    <i class="fas fa-sign-out-alt"></i> Logout
  </a>
  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;"> @csrf </form>

  <div class="time-display" id="manilaTimeDisplay"></div>

  <div class="table-layout">
    @foreach($tables as $table)
    <div class="table-link" data-table-id="{{ $table->id }}" data-table-number="{{ $table->table_number }}">
      <div class="table available">
      <div class="table-number text-center">Table {{ $table->table_number }}</div>
      <div class="inline-options"
        style="display:none; flex-direction: column; align-items: center; gap: 5px; margin-top: 10px;">
        <button class="inline-btn" onclick="event.stopPropagation(); makeOrder({{ $table->id }})">Place Order</button>
        <button class="inline-btn" onclick="event.stopPropagation(); makeReservation({{ $table->id }})">Make
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


    <!-- Modal -->
    <div id="default-modal" tabindex="-1" aria-hidden="true"
      class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex justify-center items-center">

      <div class="relative w-full max-w-lg h-[60vh]">
        <div class="relative h-full bg-white rounded-lg shadow flex flex-col">

          <!-- Modal Header -->
          <div class="modal-section ">
            <div class="flex items-center justify-between p-3 rounded-t bg-red-800">
              <h3 class="text-lg font-semibold text-white">
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

  <script>
    window.jeongolConfig = {
      storeReservationUrl: "{{ route('receptionist.storeReservation') }}",
      csrfToken: "{{ csrf_token() }}"
    };
  </script>


  @include('receptionist.components.script')

</body>



</html>
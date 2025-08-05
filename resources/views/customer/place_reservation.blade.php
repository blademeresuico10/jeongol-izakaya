<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Jeongol Izakaya</title>

  <!-- Bootstrap + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="shortcut icon" type="x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
  @vite('resources/css/app.css')

  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      background-color: #fefefe;
      margin: 0;
      overflow-x: hidden !important;
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
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(145px, 1fr));
      gap: 15px;
      padding: 20px;
      max-width: 1000px;
      margin: 0 auto;
    }

    .table-link {
      text-decoration: none;
    }

    .table {
      aspect-ratio: 1 / 1;
      background-color: #28a745;
      color: white;
      border-radius: 20px;
      display: flex;
      justify-content: center;
      align-items: center;
      font-weight: bold;
      font-size: 1.1rem;
      transition: 0.3s ease-in-out;
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
      /* ✅ Important */
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
  </style>
</head>

<body>
  <header>
    <a href="{{ route('customer.index') }}" class="me-2 text-dark" style="text-decoration: none;">
      <i class="bi bi-arrow-left-circle-fill" style="font-size: 1.4rem;"></i>
    </a>
    Welcome to <strong>Jeongol Izakaya</strong>
  </header>

  <main class="table-layout">
    @foreach($tables as $table)
    <a href="#" class="table-link" data-table-id="{{ $table->id }}">
      <div class="table available">
      <div class="position-absolute w-100 text-center" style="top: 8px; font-size: 10px;">
        {{ $table->capacity }} Pax
      </div>
      Table {{ $table->table_number }}
      </div>
    </a>
  @endforeach
  </main>

  <!-- Reservation Modal -->
  <div id="tableModal" class="modal">
    <div class="modal-content">
      <span id="closeModal" class="close-modal text-end text-lg cursor-pointer">&times;</span>
      <h3 class="mb-2 text-lg text-center"><strong>Please Enter Reservation Details</strong></h3>
      <form id="reservationForm">
        <div class="modal-section">
          <label for="customerName">Customer</label>
          <input type="text" id="customerName" placeholder="Enter your name" required />
        </div>

        <div class="modal-section">
          <label for="contactNumber">Contact Number</label>
          <input type="text" id="contactNumber" placeholder="09XXXXXXXXX" />
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
          <label for="notes">Notes</label>
          <textarea id="notes" rows="2"></textarea>
        </div>

        <input type="hidden" id="selectedTableNumber">

        <div class="flex gap-4 p-2 border-t border-gray-200 dark:border-gray-600">
          <!-- Order Food Button -->
          <button data-modal-hide="default-modal" type="button" id="order"
            class="w-1/2 bg-gray-500 hover:bg-gray-600 text-white font-medium text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-4 focus:ring-gray-300 dark:bg-gray-700 dark:hover:bg-gray-800 dark:focus:ring-gray-600">
            Order Food
          </button>

          <!-- Submit Reservation Button -->
          <button id="submitBtn" type="submit"
            class="w-1/2 bg-red-600 hover:bg-red-700 text-white font-medium text-sm px-3 py-2 rounded-lg focus:outline-none focus:ring-4 focus:ring-red-300 dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-800">
            Submit Reservation
          </button>
        </div>

      </form>
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


  <footer class="text-center p-3 bg-danger text-white mt-5">
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


  @include('customer.script')
</body>

</html>
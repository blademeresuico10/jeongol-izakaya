<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Receptionist Page</title>
  <link rel="shortcut icon" type="x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  @include('receptionist.components.css')
</head>
<body>

  <a class="logout-button top-logout" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
    <i class="fas fa-sign-out-alt"></i> Logout
  </a>
  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;"> @csrf </form>

  <div class="time-display" id="manilaTimeDisplay"></div>

  <div class="table-layout">
    @foreach($tables as $table)
      <a href="#" class="table-link" data-table-id="{{ $table->id }}" data-table-number="{{ $table->table_number }}">
        <div class="table available">Table {{ $table->table_number }}</div>
      </a>
    @endforeach
  </div>

  <div class="bottom-buttons">
    <a class="logout-button" href="{{ url('/view_reservations') }}">View Reservation</a>
    <a class="logout-button" href="{{ route('kitchen.home') }}">View Kitchen</a>
  </div>

  {{-- Reservation Modal --}}
  <div id="tableModal" class="modal">
    <div class="modal-content">
      <span id="closeModal" class="close-modal">&times;</span>
      <h2>Customer Info and Menu</h2>

      <div class="modal-section">
        <label><strong>Customer </strong></label>
        <input type="text" id="customerName" placeholder="Customer's name" required>
      </div>

      <div class="modal-section modal-flex">
        <div class="modal-column">
          <label><strong>Number of Pax</strong></label>
          <input id="numberOfPax" type="number" value="1" min="1" required>
        </div>
        <div class="modal-column">
          <label><strong>Reserved Now</strong></label>
          <input type="date" id="reserved_date">
          <input type="time" id="arrivalTimeInput" required>
          <p><strong>Reservation Time Frame:</strong> <span id="timeFrameDisplay"></span></p>
        </div>
      </div>

      <div class="modal-section">
        <label><strong>Advance Payment </strong></label>
        <input type="text" id="advance_payment" placeholder="Enter Amount" >
      </div>
      <hr>

     <div class="modal-section modal-flex">
        <div class="modal-column">
          <p><strong>Place Order</strong></p>
          @foreach($menuItems as $item)
            <label>
              <input type="checkbox" class="menu-item" value="{{ $item->menu_item }}">
              {{ $item->menu_item }}
            </label><br>       
          @endforeach
          <br>
            <div><strong>Total: ₱<span id="total">0.00</span></strong></div>

        </div>

        <div class="modal-column">
          <p><strong>Order Quantity</strong></p>
          <div id="specifyOrders" class="order-input"></div>
        </div>
      </div>


      <div class="modal-section">
        <textarea id="customerNotes" placeholder="Add notes" rows="2"></textarea>
      </div>

      <div class="modal-actions">
        <button class="submit-btn" type="button">Show Reservation</button>
        <button class="pay-btn" id="submitToCashierBtn" type="button">Submit to cashier</button>
      </div>
    </div>
  </div>

  {{-- Confirmation Modal --}}
  <div id="submit_to_kitchen" class="modal">
    <div class="modal-content">
      <span id="closeCompileModal" class="close-modal">&times;</span>
      <h2>Order Information</h2>
      <p><strong>Customer Name:</strong> <span id="compiledCustomerName"></span></p>
      <p><strong>Time Ordered:</strong> <span id="compiledArrivalTime"></span></p>

      <div style="display: flex; flex-wrap: wrap; gap: 20px;">
        <div style="flex: 1 1 45%;">
          <h3>Orders</h3>
          <div id="compiledOrders"></div>
        </div>
        <div style="flex: 1 1 45%;">
          <h3>Notes</h3>
          <div id="compiledNotes" style="white-space: pre-wrap;"></div>
        </div>
      </div>
    </div>
  </div>

  

  {{-- JS --}}
  

  @include('receptionist.components.script')
</body>
</html>

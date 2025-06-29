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
    <a class="view-button" href="{{ url('/view_reservations') }}">View Reservation</a>
    <a class="view-button" href="{{ route('kitchen.home') }}">View Kitchen</a>
  </div>

  
  <div id="tableModal" class="modal">
    <div class="modal-content">
      <span id="closeModal" class="close-modal">&times;</span>
      <h2>Customer Info and Menu</h2>

      <div class="modal-section">
        <label><strong>Customer</strong></label>
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
         <p><strong>Reservation Time Frame:</strong> <span id="timeFrameDisplay" style="font-size: 0.9rem;"></span></p>

        </div>
      </div>

      <div class="modal-section">
        <label><strong>Advance Payment </strong></label>
        <input type="text" id="advance_payment" placeholder="Enter Amount" >
      </div>
      <hr>

     <div class="modal-section modal-flex">
        @php
            $uniqueMenuItems = [];
            $menuPricesMap = [];
            foreach($menuItems as $item) {
                $cleanName = str_replace([' Lunch', ' Dinner'], '', $item->menu_item);
                if (!isset($menuPricesMap[$cleanName])) {
                    $uniqueMenuItems[] = $cleanName;
                    $menuPricesMap[$cleanName] = ['lunch' => null, 'dinner' => null];
                }
                if (str_contains($item->menu_item, 'Lunch')) {
                    $menuPricesMap[$cleanName]['lunch'] = $item->price;
                } else {
                    $menuPricesMap[$cleanName]['dinner'] = $item->price;
                }
            }
        @endphp


        <div class="modal-column">
          <p><strong>Place Order</strong></p>
          @foreach($uniqueMenuItems as $cleanName)
            <label>
              <input type="checkbox" class="menu-item" value="{{ $cleanName }}">
              {{ $cleanName }}
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
        <button class="pay-btn" id="submitToCashierBtn" type="button">Submit to cashier</button>
      </div>
    </div>
  </div>

  

  @include('receptionist.components.script')
</body>
</html>

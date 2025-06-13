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

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
  @csrf
</form>

  <div class="time-display" id="manilaTimeDisplay"></div>

  <div class="table-layout">
    @foreach($tables as $table)
      <a href="#" class="table-link" data-table-id="{{ $table->id }}">
        <div class="table available">Table {{ $table->table_number }}</div>
      </a>
    @endforeach
  </div>

  <div class="bottom-buttons">
    <a class="logout-button" href="{{ url('/view_reservations') }}">View Reservation</a>
    <a class="logout-button" href="">View Kitchen</a>
</div>


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
          <label>Number of Pax</label>
          <input id="numberOfPax" type="number" value="1" min="1" required>
        </div>
        <div class="modal-column">
          <label>Arrival Time</label>
          <input type="time" id="arrivalTimeInput" required>
        </div>
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
        <button class="submit-btn">Show Reservation</button>
        <button class="pay-btn" id="submitToCashierBtn">Submit to cashier</button>
      </div>
    </div>
  </div>

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

  <script>
    const modal = document.getElementById('tableModal');
    const closeModal = document.getElementById('closeModal');
    const tableLinks = document.querySelectorAll('.table-link');
    const menuCheckboxes = document.querySelectorAll('.menu-item');
    const specifyOrdersDiv = document.getElementById('specifyOrders');
    const submitBtn = document.querySelector('.submit-btn');
    const compileModal = document.getElementById('submit_to_kitchen');
    const closeCompileModal = document.getElementById('closeCompileModal');
    const compiledOrdersDiv = document.getElementById('compiledOrders');
    const compiledNotesDiv = document.getElementById('compiledNotes');
    const compiledCustomerNameSpan = document.getElementById('compiledCustomerName');
    const submitToCashierBtn = document.getElementById('submitToCashierBtn');
    let selectedTableId = null;

    function getManilaTimeString() {
      const now = new Date().toLocaleString("en-US", {
        timeZone: "Asia/Manila",
        hour: "2-digit",
        minute: "2-digit",
        hour12: false
      });
      const [hours, minutes] = now.split(":");
      return `${hours.padStart(2, '0')}:${minutes.padStart(2, '0')}`;
    }

    function updateSpecifyOrders() {
      const previousValues = {};
      specifyOrdersDiv.querySelectorAll('input[type="number"]').forEach(input => {
        previousValues[input.name] = input.value;
      });

      specifyOrdersDiv.innerHTML = '';
      menuCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
          const savedValue = previousValues[checkbox.value] || 1;
          specifyOrdersDiv.innerHTML += `
            <label>${checkbox.value}
              <input type="number" name="${checkbox.value}" min="1" value="${savedValue}" style="width:50px;">
            </label><br>
          `;
        }
      });
    }

    tableLinks.forEach(link => {
      link.addEventListener('click', e => {
        e.preventDefault();
        tableLinks.forEach(l => l.classList.remove('selected'));
        link.classList.add('selected');

        selectedTableId = link.getAttribute('data-table-id');
        modal.style.display = 'flex';
        menuCheckboxes.forEach(cb => cb.checked = false);
        updateSpecifyOrders();
        document.getElementById('arrivalTimeInput').value = getManilaTimeString();
      });
    });

    closeModal.onclick = () => modal.style.display = 'none';
    closeCompileModal.onclick = () => compileModal.style.display = 'none';

    window.onclick = e => {
      if (e.target === modal) modal.style.display = 'none';
      if (e.target === compileModal) compileModal.style.display = 'none';
    };

    menuCheckboxes.forEach(cb => {
      cb.addEventListener('change', updateSpecifyOrders);
    });

    submitBtn.addEventListener('click', () => {
      const name = document.getElementById('customerName').value.trim();
      const pax = document.getElementById('numberOfPax').value.trim();
      const arrival = document.getElementById('arrivalTimeInput').value.trim();
      const notes = document.getElementById('customerNotes').value.trim();

      if (!name) {
        alert("Please fill out name.");
        return;
      } else if(pax <= 0) {
        alert("Please fill number of pax.");
        return;
      }

      let orders = [];
      compiledOrdersDiv.innerHTML = "";
      menuCheckboxes.forEach(cb => {
        if (cb.checked) {
          const qty = parseInt(document.querySelector(`input[name="${cb.value}"]`).value || "0");
          if (qty > 0) {
            orders.push({ item: cb.value, qty });
            compiledOrdersDiv.innerHTML += `<p>${cb.value}: ${qty}</p>`;
          }
        }
      });

      compiledCustomerNameSpan.textContent = name;
      document.getElementById('compiledArrivalTime').textContent = arrival;
      compiledNotesDiv.textContent = notes || "(No notes)";
      compileModal.style.display = 'flex';
    });

    submitToCashierBtn.addEventListener('click', () => {
      const customerName = document.getElementById('customerName').value.trim();
      const numberOfPax = document.getElementById('numberOfPax').value.trim();
      const arrivalTime = document.getElementById('arrivalTimeInput').value.trim();
      const notes = document.getElementById('customerNotes').value.trim();

      if (!customerName || numberOfPax <= 0 || !arrivalTime || !selectedTableId) {
        alert("Please complete the form.");
        return;
      }

      let orders = [];
      menuCheckboxes.forEach(cb => {
        if (cb.checked) {
          const qty = parseInt(document.querySelector(`input[name="${cb.value}"]`).value || "0");
          if (qty > 0) {
            orders.push({ item: cb.value, qty });
          }
        }
      });

      const data = {
        customer_name: customerName,
        pax: numberOfPax,
        arrival_time: arrivalTime,
        notes: notes,
        orders: orders,
        table_id: selectedTableId
      };

      fetch("{{ route('receptionist.storeReservation') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
      })
      .then(res => res.json())
      .then(response => {
        if (response.success) {
          alert("Reservation submitted to cashier!");
          modal.style.display = 'none';
          compileModal.style.display = 'none';
        } else {
          alert("Submission failed.");
        }
      })
      .catch(error => {
        console.error(error);
        alert("An error occurred.");
      });
    });

    function updateManilaTime() {
      const now = new Date().toLocaleString("en-PH", {
        timeZone: "Asia/Manila",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: true,
      });
      document.getElementById("manilaTimeDisplay").textContent = `Current Time: ${now}`;
    }

    setInterval(updateManilaTime, 1000);
    updateManilaTime();
  </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}"> 
  <title>Jeongol Izakaya</title>

  <!-- Bootstrap + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    html, body {
      height: 100%;
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #fefefe;
    }

    body {
      display: flex;
      flex-direction: column;
      text-align: center;
    }

    main {
      flex: 1;
    }

    header {
      padding: 1rem;
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

    /* Modal styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 9999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.5);
      align-items: center;
      justify-content: center;
    }

    .modal-content {
      background-color: #fff;
      padding: 20px 30px;
      border-radius: 10px;
      width: 90%;
      max-width: 500px;
      text-align: left;
    }

    .close-modal {
      float: right;
      font-size: 1.5rem;
      font-weight: bold;
      cursor: pointer;
    }

    .modal-section {
      margin-bottom: 1rem;
    }

    .modal-actions {
      display: flex;
      justify-content: space-between;
      gap: 10px;
    }

    .modal-actions button {
      flex: 1;
      padding: 10px;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
    }

    .submit-btn {
      background-color: #007bff;
      color: white;
    }

    .pay-btn {
      background-color: #28a745;
      color: white;
    }

    #customerNotes {
      width: 100%;
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 0.9rem;
    }

    /* Order list styling */
    .modal-flex {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .menu-item-label {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .menu-item-label span {
      flex: 1;
      margin-left: 8px;
    }

    footer {
      background-color: #e60707;
      color: white;
      padding: .1rem 0;
    }

    footer a {
      color: white;
      margin: 0 10px;
      font-size: .9rem;
    }

    footer a:hover {
      color: #ddd;
    }

    /* Mobile responsiveness */
    @media (max-width: 480px) {
      .table-layout {
        grid-template-columns: repeat(5, 1fr);
      }

      .table {
        font-size: 0.9rem;
        height: 70px;
        aspect-ratio: auto;
        border-radius: 12px;
      }
    }
  </style>
</head>
<body>

  <main>
    <header>
      <div>
        <a href="{{ route('customer.index')}}" class="me-2 text-dark" style="text-decoration: none;">
          <i class="bi bi-arrow-left-circle-fill" style="font-size: 1.4rem;"></i>
        </a>
        <img src="/assets/spoon-and-fork.png" alt="Logo">
        Welcome to <strong>Jeongol Izakaya</strong>
      </div>
    </header>

    <div class="table-layout">
      @foreach($tables as $table)
        <a href="#" class="table-link" data-table-id="{{ $table->id }}" data-table-number="{{ $table->table_number }}">             
        <div class="table available position-relative">
          <div class="top-label position-absolute w-100 text-center" style="top: 8px; font-size: 10px;">
             {{ $table->capacity }}  Pax
          </div>
          Table {{ $table->table_number }}
          
        </div>
        </a>
      @endforeach
    </div>



  </main>

  <!-- Reservation Modal -->
  <div id="tableModal" class="modal">
    <div class="modal-content">
      <span id="closeModal" class="close-modal">&times;</span>
      <h3><strong>Please Enter Reservation Details</strong></h3>

      <div class="modal-section">
        <label>Customer</label><br>
        <input type="text" id="customerName" placeholder="Enter your name" required><br>
        <label>Contact Number</label><br>
        <input type="text" id="contactNumber" placeholder="Enter your contact number" required>
      </div>
      <div class="modal-section">
        <label>Reservation Date</label><br>
        <input type="date" id="reserved_date" required><br>

        <label>Reservation Time</label><br>
        <input type="time" id="arrivalTimeInput" required>
        
        <!-- Message area -->
        <small id="validUntilMessage" style="color: #888; font-style: italic; display: block; margin-top: 5px;"></small>
        </div>


<div><p> <strong> Available food to reserve:</strong></p></div>
<div class="modal-section modal-flex">
  @foreach(['Samgyupsal', 'Hotpot', 'Fusion'] as $item)
    <div class="menu-item-label">
      <label>
        <input type="checkbox" class="menu-item" value="{{ $item }}">
        <span>{{ $item }}</span>
      </label>
      <input
        type="number"
        placeholder=""
        min="1"
        class="form-control ms-2 menu-qty"
        name="quantity[{{ $item }}]"
        style="width: 60px;"
        disabled
      >
    </div>
  @endforeach
</div>

      <div class="modal-section">
        <textarea id="customerNotes" placeholder="Add notes" rows="3"></textarea>
      </div>

      <div class="modal-actions">
        <button class="pay-btn" id="submitToCashierBtn" type="button">Proceed to Payment</button>
      </div>
    </div>
  </div>

  <!-- Payment Modal -->

    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="paymentModalLabel">Process Payment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <p>Gcash</p>
        <label for="gcashNumber">Phone Number:</label><br>
        <span id="gcashNumber" style="font-weight: bold;">09123456789</span>
        <i class="bi bi-clipboard" id="copyIcon" style="cursor: pointer; margin-left: 8px;" title="Copy number"></i><br>

        <img src="/assets/gcash-qr.jpg" alt="Gcash QR" style="width: 50%; max-width: 100px; margin-bottom: 15px;"><br>


        <p>Paymaya</p>
        <img src="/assets/gcash.png" alt="Paymaya Logo" style="max-width: 100px; margin-bottom: 15px;"><br>


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Upload Receipt</button>
        <button type="button" class="btn btn-success" id="submitReservationBtn">Submit Reservation</button>
      </div>
    </div>
  </div>
</div>


  <!-- Footer -->
  <footer>
    <div class="container">
      <p class="mb-2">Contact us</p>
      <div class="mb-3">
        <a href="#"><i class="bi bi-facebook"></i></a>
        <a href="#"><i class="bi bi-instagram"></i></a>
        <a href="#"><i class="bi bi-twitter"></i></a>
        <a href="mailto:info@jeongolizakaya.com"><i class="bi bi-envelope-fill"></i></a>
      </div>
      <p class="mb-0">© 2023 Jeongol Izakaya. All rights reserved.</p>
    </div>
  </footer>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('tableModal');
  const closeModal = document.getElementById('closeModal');
  const tableLinks = document.querySelectorAll('.table-link');
  const payment_modal = document.getElementById('paymentModal');

  const dateInput = document.getElementById('reserved_date');
  // Auto-set date to tomorrow if current time is after 6:30 PM
  const now = new Date();
  if (now.getHours() > 18 || (now.getHours() === 18 && now.getMinutes() >= 30)) {
    now.setDate(now.getDate() + 1); // move to tomorrow
  }
  dateInput.value = now.toISOString().split('T')[0];

  const timeInput = document.getElementById('arrivalTimeInput');
  const validUntilMessage = document.getElementById('validUntilMessage');

  const submitToCashierBtn = document.getElementById('submitToCashierBtn');
  const submitReservationBtn = document.getElementById('submitReservationBtn');

  let selectedTableNumber = 0;

  // Set min/max reservation date
  const today = new Date();
  const maxDate = new Date();
  maxDate.setDate(today.getDate() + 2);
  const toYMD = d => d.toISOString().split('T')[0];
  dateInput.min = toYMD(today);
  dateInput.max = toYMD(maxDate);

  // Open modal when table is clicked
  tableLinks.forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault();
      selectedTableNumber = link.getAttribute('data-table-number') || 0;
      modal.style.display = 'flex';
    });
  });

  // Close modal
  closeModal.addEventListener('click', () => modal.style.display = 'none');
  window.addEventListener('click', e => {
    if (e.target === modal) modal.style.display = 'none';
  });

  // Enable/disable quantity input
  document.querySelectorAll('.menu-item').forEach(checkbox => {
    checkbox.addEventListener('change', function () {
      const qtyInput = this.closest('.menu-item-label').querySelector('.menu-qty');
      qtyInput.disabled = !this.checked;
      qtyInput.value = this.checked ? 1 : '';
      qtyInput.style.border = '';
    });
  });

  // Helper to highlight invalid inputs
  function highlightInvalidField(field) {
    field.style.border = '2px solid red';
    setTimeout(() => field.style.border = '', 2000);
  }

  // Display "must arrive by" time
  timeInput.addEventListener('input', () => {
    const time = timeInput.value;
    if (time) {
      const [hour, minute] = time.split(':').map(Number);
      const reservationTime = new Date();
      reservationTime.setHours(hour);
      reservationTime.setMinutes(minute + 30);
      validUntilMessage.textContent = `You must arrive by ${reservationTime.toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
      })}`;
    } else {
      validUntilMessage.textContent = '';
    }
  });

  // Show payment modal
  submitToCashierBtn.addEventListener('click', () => {
    const name = document.getElementById('customerName');
    const contact = document.getElementById('contactNumber');
    const date = document.getElementById('reserved_date');
    const time = document.getElementById('arrivalTimeInput');
    const notes = document.getElementById('customerNotes');

    let hasError = false;
    [name, contact, date, time].forEach(field => field.style.border = '');

    if (!name.value.trim()) { highlightInvalidField(name); hasError = true; }
    if (!contact.value.trim()) { highlightInvalidField(contact); hasError = true; }
    if (!date.value.trim()) { highlightInvalidField(date); hasError = true; }
    if (!time.value.trim()) { highlightInvalidField(time); hasError = true; }

    if (time.value && (time.value < '10:00' || time.value > '18:00')) {
      alert('Reservation time must be between 10:00 AM and 6:00 PM.');
      highlightInvalidField(time);
      hasError = true;
    }

    // Validate quantities for checked items
    document.querySelectorAll('.menu-item:checked').forEach(menu => {
      const qtyInput = menu.closest('.menu-item-label').querySelector('.menu-qty');
      if (!qtyInput.value || parseInt(qtyInput.value) < 1) {
        highlightInvalidField(qtyInput);
        hasError = true;
      }
    });

    if (hasError) {
      alert('Please complete all required fields correctly.');
      return;
    }

    // Build reservation data (include all menu items, even unchecked)
    const reservationData = {
      customer_name: name.value.trim(),
      contact_number: contact.value.trim(),
      reserved_date: date.value,
      arrival_time: time.value,
      notes: notes.value.trim(),
      table_number: selectedTableNumber,
      menu: []
    };

    document.querySelectorAll('.menu-item').forEach(menu => {
      const qtyInput = menu.closest('.menu-item-label').querySelector('.menu-qty');
      reservationData.menu.push({
        item: menu.value,
        quantity: menu.checked ? parseInt(qtyInput.value) : 0
      });
    });

    localStorage.setItem('reservation_data', JSON.stringify(reservationData));
    modal.style.display = 'none';
    const bsModal = new bootstrap.Modal(payment_modal);
    bsModal.show();
  });

  // Final submit to backend
  submitReservationBtn.addEventListener('click', () => {
    const reservationData = JSON.parse(localStorage.getItem('reservation_data'));
    if (!reservationData) {
      alert('No reservation data found. Please fill the reservation form first.');
      return;
    }

    if (submitReservationBtn.disabled) return;
    submitReservationBtn.disabled = true;

    fetch('/customer/store', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(reservationData)
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Reservation submitted successfully!');
        localStorage.removeItem('reservation_data');

        // Reset form
        document.getElementById('customerName').value = '';
        document.getElementById('contactNumber').value = '';
        document.getElementById('reserved_date').value = '';
        document.getElementById('arrivalTimeInput').value = '';
        document.getElementById('customerNotes').value = '';
        document.getElementById('validUntilMessage').textContent = '';
        document.querySelectorAll('.menu-item').forEach(cb => cb.checked = false);
        document.querySelectorAll('.menu-qty').forEach(qty => {
          qty.value = '';
          qty.disabled = true;
        });

        location.href = "{{ route('customer.place_reservation') }}";
      } else {
        alert('Reservation failed. Please try again.');
        submitReservationBtn.disabled = false;
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred. Please try again.');
      submitReservationBtn.disabled = false;
    });
  });
});


</script>


</body>
</html>

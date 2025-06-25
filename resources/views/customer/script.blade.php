<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
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
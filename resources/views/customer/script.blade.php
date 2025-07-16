<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Elements
  const modal = document.getElementById('tableModal');
  const closeModal = document.getElementById('closeModal');
  const paymentModal = document.getElementById('paymentModal');
  const tableLinks = document.querySelectorAll('.table-link');

  const nameInput = document.getElementById('customerName');
  const contactInput = document.getElementById('contactNumber');
  const dateInput = document.getElementById('reserved_date');
  const timeInput = document.getElementById('arrivalTimeInput');
  const notesInput = document.getElementById('customerNotes');
  const validUntilMessage = document.getElementById('validUntilMessage');

  const submitToCashierBtn = document.getElementById('submitToCashierBtn');
  const submitReservationBtn = document.getElementById('submitReservationBtn');

  let selectedTableNumber = 0;

  // ----------- Helpers -----------
  const highlightInvalidField = field => {
    field.style.border = '2px solid red';
    setTimeout(() => (field.style.border = ''), 2000);
  };

  const resetForm = () => {
    [nameInput, contactInput, dateInput, timeInput, notesInput].forEach(el => el.value = '');
    validUntilMessage.textContent = '';
    document.querySelectorAll('.menu-item').forEach(cb => cb.checked = false);
    document.querySelectorAll('.menu-qty').forEach(qty => {
      qty.value = '';
      qty.disabled = true;
    });
  };

  const gatherReservationData = () => ({
    customer_name: nameInput.value.trim(),
    contact_number: contactInput.value.trim(),
    reserved_date: dateInput.value,
    arrival_time: timeInput.value,
    notes: notesInput.value.trim(),
    table_number: selectedTableNumber,
    menu: Array.from(document.querySelectorAll('.menu-item')).map(menu => ({
      item: menu.value,
      quantity: menu.checked ? parseInt(menu.closest('.menu-item-label').querySelector('.menu-qty').value) : 0,
      notes: notesInput.value.trim()
    }))
  });

  // ----------- Date Setup -----------
  const now = new Date();
  if (now.getHours() > 18 || (now.getHours() === 18 && now.getMinutes() >= 30)) {
    now.setDate(now.getDate() + 1);
  }
  dateInput.value = now.toISOString().split('T')[0];

  const today = new Date();
  const maxDate = new Date();
  maxDate.setDate(today.getDate() + 2);
  dateInput.min = today.toISOString().split('T')[0];
  dateInput.max = maxDate.toISOString().split('T')[0];

  // ----------- Event Listeners -----------
  tableLinks.forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault();
      selectedTableNumber = link.getAttribute('data-table-number') || 0;
      modal.style.display = 'flex';
    });
  });

  closeModal.addEventListener('click', () => (modal.style.display = 'none'));
  window.addEventListener('click', e => { if (e.target === modal) modal.style.display = 'none'; });

  // Enable/disable qty inputs
  document.querySelectorAll('.menu-item').forEach(checkbox => {
    checkbox.addEventListener('change', () => {
      const qtyInput = checkbox.closest('.menu-item-label').querySelector('.menu-qty');
      qtyInput.disabled = !checkbox.checked;
      qtyInput.value = checkbox.checked ? 1 : '';
      qtyInput.style.border = '';
    });
  });

  // Show must arrive by
  timeInput.addEventListener('input', () => {
    if (!timeInput.value) return validUntilMessage.textContent = '';
    const [hour, minute] = timeInput.value.split(':').map(Number);
    const expireTime = new Date();
    expireTime.setHours(hour);
    expireTime.setMinutes(minute + 30);
    validUntilMessage.textContent = `You must arrive by ${expireTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true })}`;
  });

  // ----------- Proceed to Payment -----------
  submitToCashierBtn.addEventListener('click', () => {
    let hasError = false;
    [nameInput, contactInput, dateInput, timeInput].forEach(f => f.style.border = '');

    if (!nameInput.value.trim()) { highlightInvalidField(nameInput); hasError = true; }
    if (!contactInput.value.trim()) { highlightInvalidField(contactInput); hasError = true; }
    if (!dateInput.value.trim()) { highlightInvalidField(dateInput); hasError = true; }
    if (!timeInput.value.trim()) { highlightInvalidField(timeInput); hasError = true; }

    if (timeInput.value && (timeInput.value < '10:00' || timeInput.value > '18:00')) {
      alert('Reservation time must be between 10:00 AM and 6:00 PM.');
      highlightInvalidField(timeInput);
      hasError = true;
    }

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

    localStorage.setItem('reservation_data', JSON.stringify(gatherReservationData()));
    modal.style.display = 'none';
    new bootstrap.Modal(paymentModal).show();
  });

  // ----------- Submit to backend -----------
  submitReservationBtn.addEventListener('click', () => {
    const reservationData = JSON.parse(localStorage.getItem('reservation_data'));
    if (!reservationData) {
      alert('No reservation data found. Please fill the reservation form.');
      return;
    }

    if (submitReservationBtn.disabled) return;
    submitReservationBtn.disabled = true;

    fetch('/customer/store', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify(reservationData)
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Reservation submitted successfully!');
        localStorage.removeItem('reservation_data');
        resetForm();
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

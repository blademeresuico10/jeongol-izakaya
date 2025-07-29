<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('tableModal');
  const closeModal = document.getElementById('closeModal');
  const tableLinks = document.querySelectorAll('.table-link');

  const nameInput = document.getElementById('customerName');
  const contactInput = document.getElementById('contactNumber');
  const dateInput = document.getElementById('reserved_date');
  const timeInput = document.getElementById('arrivalTimeInput');
  const notesInput = document.getElementById('customerNotes');
  const validUntilMessage = document.getElementById('validUntilMessage');
  const submitBtn = document.getElementById('submitBtn');

  let selectedTableNumber = 0;

  const fullMenuPrices = @json($menuPricesMap);

  const highlightInvalidField = field => {
    field.classList.add('is-invalid');
    setTimeout(() => field.classList.remove('is-invalid'), 2000);
  };

  const resetForm = () => {
    [nameInput, contactInput, dateInput, timeInput, notesInput].forEach(el => el.value = '');
    validUntilMessage.textContent = '';
    document.getElementById('pax').value = '';
    selectedTableNumber = 0;
    document.getElementById('selectedTableNumber').value = '';
    document.querySelectorAll('.menu-item').forEach(cb => cb.checked = false);
    document.querySelectorAll('.menu-qty').forEach(qty => {
      qty.value = '';
      qty.disabled = true;
      qty.classList.remove('is-invalid');
    });
  };

  const gatherReservationData = () => {
    const note = notesInput.value.trim();
    const arrival = timeInput.value || '';
    const [hourStr] = arrival.split(':');
    const hour = parseInt(hourStr) || 0;
    const isLunch = hour < 17;


    const menuItems = Array.from(document.querySelectorAll('.menu-item'))
      .filter(menu => menu.checked)
      .map(menu => {
        const qtyInput = menu.closest('.menu-item-label').querySelector('.menu-qty');
        const itemName = menu.value;
        const price = isLunch ? fullMenuPrices[itemName]['lunch'] : fullMenuPrices[itemName]['dinner'];

        const order = {
          item: itemName,
          qty: parseInt(qtyInput.value),
          order_price: parseFloat(price)
        };

        order.notes = note || '';


        return order;
      });

    return {
      pax: document.getElementById('pax').value,
      customer_name: nameInput.value.trim(),
      contact_number: contactInput.value.trim(),
      reserved_date: dateInput.value,
      arrival_time: timeInput.value,
      table_id: selectedTableNumber,
      notes: note,
      orders: menuItems
    };
  };

   const validateInputs = () => {
    let valid = true;

    if (!nameInput.value.trim()) highlightInvalidField(nameInput), valid = false;
    if (!contactInput.value.trim()) highlightInvalidField(contactInput), valid = false;
    if (!dateInput.value) highlightInvalidField(dateInput), valid = false;
    if (!timeInput.value) highlightInvalidField(timeInput), valid = false;
    if (!document.getElementById('pax').value) highlightInvalidField(document.getElementById('pax')), valid = false;
    if (!selectedTableNumber) {
      alert('Please select a table first.');
      valid = false;
    }

    document.querySelectorAll('.menu-item').forEach(item => {
      const qtyInput = item.closest('.menu-item-label').querySelector('.menu-qty');
      if (item.checked && (!qtyInput.value || parseInt(qtyInput.value) < 1)) {
        highlightInvalidField(qtyInput);
        valid = false;
      }
    });

    return valid; 
  };


  // Toggle quantity enable/disable
  document.querySelectorAll('.menu-item').forEach(checkbox => {
      checkbox.addEventListener('change', function () {
        const targetId = this.dataset.target;
        const qtyInput = document.getElementById(targetId);
        if (this.checked) {
          qtyInput.disabled = false;
          qtyInput.value = 1;
        } else {
          qtyInput.disabled = true;
          qtyInput.value = '';
        }
      });
    });

  // Open modal
  tableLinks.forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault();
      selectedTableNumber = parseInt(link.getAttribute('data-table-id'));
      document.getElementById('selectedTableNumber').value = selectedTableNumber;
      modal.style.display = 'flex';
    });
  });

  // Close modal
  closeModal.addEventListener('click', () => {
    modal.style.display = 'none';
    resetForm();
  });

  // Date range defaults
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

  // Display valid until time
  timeInput.addEventListener('input', () => {
    if (!timeInput.value) return validUntilMessage.textContent = '';
    const [hour, minute] = timeInput.value.split(':').map(Number);
    const expireTime = new Date();
    expireTime.setHours(hour);
    expireTime.setMinutes(minute + 30);
    validUntilMessage.textContent = `You must arrive by ${expireTime.toLocaleTimeString([], {
      hour: '2-digit',
      minute: '2-digit',
      hour12: true
    })}`;
  });

  // Submit
  submitBtn.addEventListener('click', async (e) => {
    e.preventDefault();

    if (!validateInputs()) {
      alert('Please complete all required fields including selected menu quantities.');
      return;
    }

    const data = gatherReservationData();
    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';

    try {
      const res = await fetch('/customer/reserve', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
      });

      const response = await res.json();

      if (res.ok && response.success) {
        alert('Reservation successful!');
        resetForm();
        modal.style.display = 'none';
      } else {
        alert(response.message || 'Reservation could not be completed.');
      }

    } catch (err) {
      console.error(err);
      alert('Something went wrong while submitting the reservation.');
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Submit Reservation';
    }
  });
});
</script>

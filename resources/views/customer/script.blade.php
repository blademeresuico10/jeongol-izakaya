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

  const highlightInvalidField = field => {
    field.style.border = '2px solid red';
    setTimeout(() => field.style.border = '', 2000);
  };

  const resetForm = () => {
    [nameInput, contactInput, dateInput, timeInput, notesInput].forEach(el => el.value = '');
    validUntilMessage.textContent = '';
    selectedTableNumber = 0;
    document.getElementById('selectedTableNumber').value = '';
    document.querySelectorAll('.menu-item').forEach(cb => cb.checked = false);
    document.querySelectorAll('.menu-qty').forEach(qty => {
      qty.value = '';
      qty.disabled = true;
    });
  };

  const gatherReservationData = () => {
    const menuItems = Array.from(document.querySelectorAll('.menu-item'))
      .filter(menu => menu.checked)
      .map(menu => {
        const wrapper = menu.closest('.menu-item-label');
        const qtyInput = wrapper.querySelector('.menu-qty');
        return {
          item: menu.value,
          quantity: parseInt(qtyInput.value) || 1, // default to 1 if not entered
          notes: notesInput.value.trim() 
        };
      });

    return {
      customer_name: nameInput.value.trim(),
      contact_number: contactInput.value.trim(),
      reserved_date: dateInput.value,
      arrival_time: timeInput.value,
      table_number: selectedTableNumber,
      notes: notesInput.value.trim(),
      menu: menuItems
    };
  };

  const validateInputs = () => {
    let valid = true;
    if (!nameInput.value.trim()) {
      highlightInvalidField(nameInput);
      valid = false;
    }
    if (!contactInput.value.trim()) {
      highlightInvalidField(contactInput);
      valid = false;
    }
    if (!dateInput.value) {
      highlightInvalidField(dateInput);
      valid = false;
    }
    if (!timeInput.value) {
      highlightInvalidField(timeInput);
      valid = false;
    }
    if (!selectedTableNumber) {
      alert('Please select a table.');
      valid = false;
    }
    return valid;
  };

  // Enable/disable quantity field when checkbox is checked
  document.querySelectorAll('.menu-item').forEach(item => {
    item.addEventListener('change', () => {
      const qtyInput = item.closest('.menu-item-label').querySelector('.menu-qty');
      qtyInput.disabled = !item.checked;
      if (item.checked && !qtyInput.value) qtyInput.value = 1;
    });
  });

  // Modal open on table click
  tableLinks.forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault();
      selectedTableNumber = link.getAttribute('data-table-number');
      document.getElementById('selectedTableNumber').value = selectedTableNumber;
      modal.style.display = 'flex';
    });
  });

  // Modal close
  closeModal.addEventListener('click', () => {
    modal.style.display = 'none';
    resetForm();
  });

  // Auto date settings
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

  // Valid until display
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

  // Form submission
  submitBtn.addEventListener('click', async (e) => {
    e.preventDefault();

    if (!validateInputs()) {
      alert('Please complete all required fields.');
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

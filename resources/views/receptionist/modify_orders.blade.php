<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Customer Orders</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


</head>

<body class="bg-gray-100 min-h-screen font-sans">
  <div class="items-center mt-4 mb-3 text-center">
    <h2 class="text-3xl font-bold text-gray-800">Customer Orders</h2>
  </div>

  <!-- Search -->
  <div class="flex justify-center mt-4 mb-4">
    <div class="flex items-center">
      <input type="text" id="searchInput" placeholder="Search by customer name"
        class="border rounded-lg px-3 py-2 w-80 shadow-sm focus:ring-2 focus:ring-blue-500 outline-none" />
    </div>
  </div>

  <div class="flex justify-center mb-4 px-6">

    <div class="border border-gray-200 rounded-lg shadow-md bg-white p-3 w-full max-w-6xl"
      style="max-height:500px; overflow:auto;">
      <div class="overflow-x-auto">
        <table class="w-full border-collapse">
          <thead>
            <tr class="bg-green-600 text-white">
              <th class="border border-gray-300 px-4 py-3 text-left font-semibold">Customer Name</th>
              <th class="border border-gray-300 px-4 py-3 text-left font-semibold">Table</th>
              <th class="border border-gray-300 px-4 py-3 text-left font-semibold">Pax</th>
              <th class="border border-gray-300 px-4 py-3 text-left font-semibold">Orders</th>
              <th class="border border-gray-300 px-4 py-3 text-left font-semibold">Note</th>
              <th class="border border-gray-300 px-4 py-3 text-left font-semibold">Modify</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($groupedOrders as $order)
        <tr class="hover:bg-gray-50">
          <td class="border border-gray-300 px-4 py-3">{{ $order->customer_name }}</td>
          <td class="border border-gray-300 px-4 py-3">{{ $order->table_number }}</td>
          <td class="border border-gray-300 px-4 py-3">{{ $order->pax }}</td>
          <td class="border border-gray-300 px-4 py-3">{{ $order->orders }}</td>
          <td class="border border-gray-300 px-4 py-3">{{ $order->note }}</td>
          <td class="border border-gray-300 px-4 py-3">
          <button type="button" data-reservation-id="{{ $order->reservation_id }}" data-pax="{{ $order->pax }}"
            data-orders="{{ $order->orders }}" data-note="{{ $order->note }}" onclick="openModal(this)"
            class="bg-blue-100 text-blue-600 hover:bg-blue-200 px-2 py-1 rounded text-sm border border-blue-300">
            <i class="fas fa-edit"></i>
          </button>
          </td>
        </tr>
      @endforeach
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <!-- Mobile Cards -->
  <div class="md:hidden px-4">
    @foreach ($groupedOrders as $order)
    <div class="border p-4 mb-3 bg-white shadow-sm">
      <p><strong>Customer Name:</strong> {{ $order->customer_name }}</p>
      <p><strong>Table:</strong> {{ $order->table_number }}</p>
      <p><strong>Pax:</strong> {{ $order->pax }}</p>
      <p><strong>Orders:</strong> {{ $order->orders }}</p>
      <p><strong>Note:</strong> {{ $order->note }}</p>
      <button type="button" data-reservation-id="{{ $order->reservation_id }}" data-pax="{{ $order->pax }}"
      data-orders="{{ $order->orders }}" data-note="{{ $order->note }}" onclick="openModal(this)"
      class="mt-2 bg-gray-200 text-blue-600 hover:text-blue-800 px-3 py-1 rounded-lg text-sm">
      <i class="fas fa-edit"></i> Modify
      </button>
    </div>
  @endforeach
  </div>

  <!-- Modal -->
  <div id="crud-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-md w-full max-w-md">
      <div class="flex items-center justify-between px-5 py-3 border-b bg-green-600">
        <h3 class="text-lg font-semibold text-white">Modify Order Form</h3>
        <button onclick="closeModal()" class="text-white hover:text-gray-200">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <form id="updateOrderForm" method="POST" action="{{ route('receptionist.updateOrder') }}"
        class="px-5 py-4 space-y-4 max-h-[70vh] overflow-y-auto">
        @csrf
        <input type="hidden" name="reservation_id" id="reservation_id">

        <div>
          <label for="pax" class="block text-sm font-medium">Pax</label>
          <input type="number" name="pax" id="pax"
            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" required>
        </div>

        <div>
          <label for="add_item" class="block text-sm font-medium">Add Order</label>
          <select id="add_item" name="add_item"
            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            <option value="" disabled selected>Select an item</option>
            @foreach ($menuItems as $item)
        <option value="{{ $item->menu_item }}">{{ $item->menu_item }}</option>
      @endforeach
          </select>
        </div>

        <div>
          <p class="block text-sm font-medium">Orders</p>
          <div id="ordersBox" class="w-full min-h-[3rem] p-2 border rounded-lg flex flex-wrap gap-2 bg-white"></div>
          <input type="hidden" name="orders" id="ordersInput" required>
        </div>

        <div>
          <label for="note" class="block text-sm font-medium">Note</label>
          <textarea id="note" rows="3" name="note"
            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"></textarea>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium">
          <i class="fas fa-save mr-1"></i> Update Order
        </button>
      </form>
    </div>
  </div>

  <!-- Toast -->
  <div id="toast"
    class="hidden fixed top-5 right-5 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg transition-opacity duration-500">
    <span id="toast-message"></span>
  </div>

  <!-- Back Button -->
  <a class="fixed bottom-5 right-5 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded z-50 transition-colors duration-200 
           md:bottom-4 md:right-4 md:py-3 md:px-5
           sm:bottom-3 sm:right-3 sm:py-2 sm:px-3 sm:text-sm
           max-sm:bottom-2 max-sm:right-2 max-sm:py-1 max-sm:px-2 max-sm:text-xs"
    href="{{ route('receptionist.home') }}">
    Back to main page
  </a>

  <script>
    function openModal(button) {
      document.getElementById('crud-modal').classList.remove('hidden');
      document.getElementById('crud-modal').classList.add('flex');

      document.getElementById('reservation_id').value = button.getAttribute('data-reservation-id');
      document.getElementById('pax').value = button.getAttribute('data-pax');
      document.getElementById('note').value = button.getAttribute('data-note');

      const ordersBox = document.getElementById('ordersBox');
      ordersBox.innerHTML = '';

      const ordersStr = button.getAttribute('data-orders');
      const orders = ordersStr.split(',').map(item => item.trim()).filter(Boolean);

      orders.forEach(order => {
        const [itemName, qty = 1] = order.split(' x ');
        addOrderTag(itemName, parseInt(qty));
      });

      updateOrdersInput();
    }

    function closeModal() {
      document.getElementById('crud-modal').classList.add('hidden');
      document.getElementById('crud-modal').classList.remove('flex');
    }

    document.getElementById('searchInput').addEventListener('input', function () {
      const filter = this.value.toLowerCase();
      const rows = document.querySelectorAll('tbody tr, .table-card');
      rows.forEach(row => {
        const name = row.querySelector('td:first-child, p:first-child')?.textContent.toLowerCase() || '';
        row.style.display = name.includes(filter) ? '' : 'none';
      });
    });

    function updateOrdersInput() {
      const tags = document.querySelectorAll('#ordersBox .order-tag');
      const orders = Array.from(tags).map(tag => {
        const item = tag.dataset.item;
        const qty = parseInt(tag.querySelector('input')?.value || 1);
        return { menu_name: item, quantity: qty };
      });
      document.getElementById('ordersInput').value = JSON.stringify(orders);
    }

    function addOrderTag(itemName, qty = 1) {
      const ordersBox = document.getElementById('ordersBox');
      const exists = Array.from(ordersBox.children).some(tag => tag.dataset.item === itemName);
      if (exists) return;

      const tag = document.createElement('span');
      tag.className = "order-tag bg-gray-200 text-black px-3 py-1 me-2 mb-2 rounded-full text-sm flex items-center gap-2";
      tag.dataset.item = itemName;

      const text = document.createElement('span');
      text.textContent = itemName;

      const input = document.createElement('input');
      input.type = 'number';
      input.value = qty;
      input.min = 1;
      input.className = "w-12 text-center bg-transparent border-0 outline-none";
      input.addEventListener('click', e => e.stopPropagation());
      input.addEventListener('change', updateOrdersInput);

      const removeBtn = document.createElement('button');
      removeBtn.innerHTML = '&times;';
      removeBtn.className = "text-red-600 bg-gray-300 rounded-full w-8 h-6 flex items-center justify-center";
      removeBtn.type = "button";
      removeBtn.onclick = () => { ordersBox.removeChild(tag); updateOrdersInput(); };

      tag.appendChild(text);
      tag.appendChild(input);
      tag.appendChild(removeBtn);
      ordersBox.appendChild(tag);
    }

    document.addEventListener('DOMContentLoaded', function () {
      const select = document.getElementById('add_item');
      select.addEventListener('change', function () {
        const selectedItem = select.value;
        if (!selectedItem) return;
        addOrderTag(selectedItem);
        updateOrdersInput();
        select.selectedIndex = 0;
      });
    });

    document.getElementById('updateOrderForm').addEventListener('submit', function (e) {
      e.preventDefault();
      const form = e.target;
      const formData = new FormData(form);

      fetch(form.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
        },
        body: formData
      })
        .then(async response => {
          const data = await response.json();
          if (data.success) {
            alert(data.message);
            closeModal();
            location.reload();
          } else {
            alert(data.message + "\n" + (data.error ?? ''));
          }
        })
        .catch(error => { console.error(error); alert('An error occurred while updating the reservation.'); });
    });

    function showToast(message) {
      const toast = document.getElementById("toast");
      const msg = document.getElementById("toast-message");
      msg.textContent = message;
      toast.classList.remove("hidden", "opacity-0");
      toast.classList.add("opacity-100");

      setTimeout(() => {
        toast.classList.remove("opacity-100");
        toast.classList.add("opacity-0");
        setTimeout(() => toast.classList.add("hidden"), 500);
      }, 2000);
    }
  </script>
</body>

</html>
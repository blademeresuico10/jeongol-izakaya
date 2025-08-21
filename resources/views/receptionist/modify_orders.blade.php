<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Customer Orders</title>
  @vite('resources/css/app.css')
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<style>
  body {
    background: #f9f9f9;
  }

  .back-button {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #0c6cc6;
    color: white;
    padding: 10px 15px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
    z-index: 1000;
  }

  .back-button:hover {
    background: blue;
  }

  thead th {
    background-color: #0c6cc6;
    color: white;
    text-align: left;
    padding: 12px;
  }

  .table-card {
    display: none;
    border: 1px solid #ccc;
    border-radius: 0.5rem;
    margin-bottom: 10px;
    padding: 10px;
    background-color: #fff;
  }

  .table-card p {
    margin: 3px 0;
  }

  @media (max-width: 768px) {
    .w-11/12 {
      width: 95%;
    }

    table {
      display: none;
    }

    .table-card {
      display: block;
    }

    .back-button {
      bottom: 15px;
      right: 15px;
      padding: 8px 12px;
      font-size: 0.875rem;
    }
  }

  @media (max-width: 480px) {
    .back-button {
      bottom: 10px;
      right: 10px;
      padding: 6px 10px;
      font-size: 0.75rem;
    }
  }
</style>

<body>
  <div class="items-center mt-4 mb-3 text-center">
    <h2 style="font-size: 2rem; font-weight: 700;">Customer Orders</h2>
  </div>

  <div class="flex justify-center mt-4 mb-4">
    <div class="flex items-center">
      <input type="text" id="searchInput" placeholder="Search by customer name" class="form-control"
        style="width: 350px;" />
    </div>
  </div>

  <div class="w-full flex flex-col items-center mt-6">
    @if ($groupedOrders->isEmpty())
    <div class="alert alert-warning mb-3">No orders found for today.</div>
    @endif

    <div class="w-11/12 max-w-6xl bg-white rounded-2xl shadow-lg p-6">
      <!-- Desktop Table -->
      <div class="overflow-x-auto border rounded">
        <table class="w-full table-fixed">
          <thead class="bg-success text-white">
            <tr>
              <th>Customer Name</th>
              <th>Table</th>
              <th>Pax</th>
              <th>Orders</th>
              <th>Note</th>
              <th>Modify</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($groupedOrders as $order)
            <tr>
              <td>{{ $order->customer_name }}</td>
              <td>{{ $order->table_number }}</td>
              <td>{{ $order->pax }}</td>
              <td>{{ $order->orders }}</td>
              <td>{{ $order->note }}</td>
              <td>
                <button type="button" data-reservation-id="{{ $order->reservation_id }}" data-pax="{{ $order->pax }}"
                  data-orders="{{ $order->orders }}" data-note="{{ $order->note }}" onclick="openModal(this)"
                  class="text-blue-600 hover:text-blue-800 font-semibold rounded-full bg-gray-200 p-1">
                  <i class="fas fa-edit"></i>
                </button>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Mobile Cards -->
      @foreach ($groupedOrders as $order)
      <div class="table-card">
        <p><strong>Customer Name:</strong> {{ $order->customer_name }}</p>
        <p><strong>Table:</strong> {{ $order->table_number }}</p>
        <p><strong>Pax:</strong> {{ $order->pax }}</p>
        <p><strong>Orders:</strong> {{ $order->orders }}</p>
        <p><strong>Note:</strong> {{ $order->note }}</p>
        <p>
          <button type="button" data-reservation-id="{{ $order->reservation_id }}" data-pax="{{ $order->pax }}"
            data-orders="{{ $order->orders }}" data-note="{{ $order->note }}" onclick="openModal(this)"
            class="text-blue-600 hover:text-blue-800 font-semibold rounded-full bg-gray-200 p-1">
            <i class="fas fa-edit"></i> Modify
          </button>
        </p>
      </div>
      @endforeach
    </div>
  </div>

  <!-- Modal -->
  <div id="crud-modal" tabindex="-1" aria-hidden="true"
    class="hidden fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden p-4">
    <div class="relative w-full max-w-md">
      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b bg-green-600">
          <h3 class="text-xlg font-semibold text-white">Modify Order Form</h3>
          <button><i class="fas fa-times text-black" onclick="closeModal()"></i></button>
        </div>

        <form id="updateOrderForm" method="POST" action="{{ route('receptionist.updateOrder') }}"
          class="px-5 py-4 overflow-y-auto max-h-[70vh]">
          @csrf
          <input type="hidden" name="reservation_id" id="reservation_id">

          <div class="grid gap-4 mb-4 grid-cols-2">
            <div class="col-span-2 sm:col-span-1">
              <label for="pax" class="block mb-2 text-sm font-medium text-black-900">Pax</label>
              <input type="number" name="pax" id="pax" class="w-full p-2.5 text-sm text-gray-900 border border-black-300 rounded-lg"
                required>
            </div>

            <div class="col-span-2 sm:col-span-1">
              <label for="add_item" class="block mb-2 text-sm font-medium text-black-900">Add Order</label>
              <select id="add_item" name="add_item" class="w-full p-2.5 text-sm text-gray-900 border border-black-300 rounded-lg">
                <option value="" disabled selected>Select an item</option>
                @foreach ($menuItems as $item)
                <option value="{{ $item->menu_item }}">{{ $item->menu_item }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-span-2">
              <p class="block mb-2 text-sm font-medium text-black-900">Orders</p>
              <div id="ordersBox"
                class="w-full min-h-[3rem] p-2.5 text-sm text-gray-900 border border-black-300 rounded-lg flex flex-wrap gap-2 bg-white">
              </div>
              <input type="hidden" name="orders" id="ordersInput" required>
            </div>

            <div class="col-span-2">
              <label for="note" class="block mb-2 text-sm font-medium text-black-900">Note</label>
              <textarea id="note" rows="3" name="note" class="w-full p-2.5 text-sm text-gray-900 border border-black-300 rounded-lg"></textarea>
            </div>
          </div>

          <button type="submit"
            class="w-full text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
            <svg class="inline w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd"
                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                clip-rule="evenodd"></path>
            </svg>
            Update Order
          </button>
        </form>
      </div>
    </div>
  </div>

  <a class="back-button" href="{{ route('receptionist.home') }}">Back to main page</a>

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
  </script>
</body>

</html>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Customer Orders</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen font-sans">
  <div class="items-center mt-4 mb-6 text-center">
    <h2 class="text-3xl font-bold text-gray-800">Customer Orders</h2>
  </div>

  <div class="flex justify-center mt-4 mb-6 px-4">
    <div class="relative w-full max-w-md">
      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
      </div>
      <input type="text" id="searchInput" placeholder="Search by customer name"
        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200" />
    </div>
  </div>

  <div class="flex justify-center mb-4 px-4">
    <div class="w-full max-w-4xl space-y-4" style="max-height:500px; overflow:auto;">
      @foreach ($groupedOrders as $order)
        <div class="order-card bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-200">
          <div class="p-5">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="customer-name text-lg font-bold text-gray-900">{{ $order->customer_name }}</h3>
                    <p class="text-sm text-gray-500">Customer</p>
                  </div>
                </div>
                
                <div class="flex items-center space-x-2">
                  <div class="bg-green-100 px-3 py-1 rounded-full">
                    <span class="text-green-800 font-semibold text-sm">Table {{ $order->table_number }}</span>
                  </div>
                </div>
                
                <div class="flex items-center space-x-2">
                  <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                  </svg>
                  <span class="text-gray-700 font-medium">{{ $order->pax }} pax</span>
                </div>
              </div>
              
              <button type="button" 
                      data-reservation-id="{{ $order->reservation_id }}" 
                      data-pax="{{ $order->pax }}"
                      data-orders="{{ $order->orders }}" 
                      data-note="{{ $order->note }}" 
                      onclick="openModal(this)"
                      class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span class="font-medium">Modify</span>
              </button>
            </div>
            
            <div class="mt-4">
              <div class="flex items-center space-x-2 mb-2">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <span class="text-sm font-semibold text-gray-700">Orders:</span>
              </div>
              
              <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                @if($order->orders && $order->orders !== 'No orders')
                  <p class="text-gray-800 leading-relaxed">{{ $order->orders }}</p>
                @else
                  <div class="flex items-center justify-center py-2 text-gray-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <span class="italic">No orders placed yet</span>
                  </div>
                @endif
              </div>
            </div>
            
            @if($order->note && trim($order->note) !== '')
              <div class="mt-4">
                <div class="flex items-center space-x-2 mb-2">
                  <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                  </svg>
                  <span class="text-sm font-semibold text-gray-700">Special Notes:</span>
                </div>
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-3">
                  <p class="text-amber-800">{{ $order->note }}</p>
                </div>
              </div>
            @endif
          </div>
        </div>
      @endforeach
      
      @if($groupedOrders->isEmpty())
        <div class="text-center py-12">
          <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
          </svg>
          <h3 class="text-lg font-semibold text-gray-600 mb-2">No Orders Found</h3>
          <p class="text-gray-500">There are no customer orders to display at the moment.</p>
        </div>
      @endif
    </div>
  </div>

  <div id="crud-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 max-h-[90vh] overflow-hidden">
      <div class="flex items-center justify-between px-6 py-4 border-b bg-gradient-to-r from-blue-600 to-blue-700">
        <h3 class="text-lg font-semibold text-white flex items-center space-x-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
          </svg>
          <span>Modify Order</span>
        </h3>
        <button onclick="closeModal()" class="text-white hover:text-gray-200 transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <form id="updateOrderForm" method="POST" action="{{ route('receptionist.updateOrder') }}"
        class="px-6 py-4 space-y-4 max-h-[calc(90vh-80px)] overflow-y-auto">
        @csrf
        <input type="hidden" name="reservation_id" id="reservation_id">

        <div>
          <label for="pax" class="block text-sm font-semibold text-gray-700 mb-2">Number of Guests</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
            <input type="number" name="pax" id="pax" min="1"
              class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" required>
          </div>
        </div>

        <div>
          <label for="add_item" class="block text-sm font-semibold text-gray-700 mb-2">Add Menu Item</label>
          <select id="add_item" name="add_item"
            class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
            <option value="" disabled selected>Select an item to add</option>
            @foreach ($menuItems as $item)
              <option value="{{ $item->menu_item }}">{{ $item->menu_item }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Current Orders</label>
          <div id="ordersBox" class="w-full min-h-[100px] p-3 border border-gray-300 rounded-lg flex flex-wrap gap-2 bg-gray-50">
           
          </div>
          <input type="hidden" name="orders" id="ordersInput" required>
          <p class="text-xs text-gray-500 mt-1">Click on items above to add them, or adjust quantities as needed.</p>
        </div>

        <div>
          <label for="note" class="block text-sm font-semibold text-gray-700 mb-2">Special Notes</label>
          <textarea id="note" rows="3" name="note" placeholder="Any special requests or notes..."
            class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none resize-none"></textarea>
        </div>

        <div class="pt-4 border-t border-gray-200">
          <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-semibold transition-colors duration-200 flex items-center justify-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>Update Order</span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <div id="toast"
    class="hidden fixed top-5 right-5 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg transition-opacity duration-500 flex items-center space-x-2 z-50">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
    </svg>
    <span id="toast-message"></span>
  </div>

  <a class="fixed bottom-5 right-5 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-5 rounded-lg z-40 transition-all duration-200 transform hover:scale-105 shadow-lg flex items-center space-x-2
           md:bottom-4 md:right-4
           sm:bottom-3 sm:right-3 sm:py-2 sm:px-3 sm:text-sm
           max-sm:bottom-2 max-sm:right-2 max-sm:py-1 max-sm:px-2 max-sm:text-xs" 
     href="{{ route('receptionist.home') }}">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
    </svg>
    <span>Back to main page</span>
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
      if (ordersStr && ordersStr !== 'No orders') {
        const orders = ordersStr.split(',').map(item => item.trim()).filter(Boolean);
        orders.forEach(order => {
          const [itemName, qty = 1] = order.split(' x ');
          addOrderTag(itemName, parseInt(qty));
        });
      }

      updateOrdersInput();
    }

    function closeModal() {
      document.getElementById('crud-modal').classList.add('hidden');
      document.getElementById('crud-modal').classList.remove('flex');
    }

    document.getElementById('searchInput').addEventListener('input', function () {
      const filter = this.value.toLowerCase();
      const orderCards = document.querySelectorAll('.order-card');
      
      orderCards.forEach(card => {
        const customerName = card.querySelector('.customer-name')?.textContent.toLowerCase() || '';
        if (customerName.includes(filter)) {
          card.style.display = '';
          card.classList.remove('hidden');
        } else {
          card.style.display = 'none';
          card.classList.add('hidden');
        }
      });

      const visibleCards = Array.from(orderCards).filter(card => !card.classList.contains('hidden'));
      if (visibleCards.length === 0 && filter) {
      }
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

      const tag = document.createElement('div');
      tag.className = "order-tag bg-white border-2 border-blue-200 text-gray-800 px-3 py-2 rounded-lg text-sm flex items-center gap-3 shadow-sm hover:shadow-md transition-shadow";
      tag.dataset.item = itemName;

      const text = document.createElement('span');
      text.textContent = itemName;
      text.className = "font-medium";

      const qtyContainer = document.createElement('div');
      qtyContainer.className = "flex items-center gap-1";

      const input = document.createElement('input');
      input.type = 'number';
      input.value = qty;
      input.min = 1;
      input.className = "w-14 text-center bg-gray-50 border border-gray-200 rounded px-2 py-1 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none";
      input.addEventListener('click', e => e.stopPropagation());
      input.addEventListener('change', updateOrdersInput);

      const removeBtn = document.createElement('button');
      removeBtn.innerHTML = '×';
      removeBtn.className = "text-red-600 hover:text-red-800 hover:bg-red-100 rounded-full w-6 h-6 flex items-center justify-center font-bold transition-colors ml-2";
      removeBtn.type = "button";
      removeBtn.onclick = () => { 
        ordersBox.removeChild(tag); 
        updateOrdersInput(); 
      };

      qtyContainer.appendChild(input);
      tag.appendChild(text);
      tag.appendChild(qtyContainer);
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

      const submitBtn = form.querySelector('button[type="submit"]');
      const originalContent = submitBtn.innerHTML;
      submitBtn.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Updating...';
      submitBtn.disabled = true;

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
            showToast(data.message);
            closeModal();
            setTimeout(() => location.reload(), 1000);
          } else {
            alert(data.message + "\n" + (data.error ?? ''));
          }
        })
        .catch(error => { 
          console.error(error); 
          alert('An error occurred while updating the reservation.'); 
        })
        .finally(() => {
          submitBtn.innerHTML = originalContent;
          submitBtn.disabled = false;
        });
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

    const style = document.createElement('style');
    style.textContent = `
      .order-card {
        transition: all 0.2s ease-in-out;
      }
      
      .order-card:hover {
        transform: translateY(-1px);
      }
      
      @keyframes fadeIn {
        from {
          opacity: 0;
          transform: translateY(10px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }
      
      .order-card {
        animation: fadeIn 0.3s ease-out;
      }
    `;
    document.head.appendChild(style);
  </script>
</body>

</html>
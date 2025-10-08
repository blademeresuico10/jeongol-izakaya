<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Kitchen Dashboard</title>
  <link rel="shortcut icon" type="x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
  @vite('resources/css/app.css')

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8fafc;
    }

    [x-cloak] {
      display: none;
    }

    .expand {
      transition: all 0.25s ease-in-out;
    }
  </style>
</head>

<body class="p-5">

  @if(session('success') || session('error'))
  <div id="flash-message" 
       class="fixed top-4 right-4 p-4 rounded shadow-lg text-white z-50
              {{ session('success') ? 'bg-green-500' : 'bg-red-500' }}">
      {{ session('success') ?? session('error') }}
  </div>

  <script>
      setTimeout(() => {
          const msg = document.getElementById('flash-message');
          if (msg) msg.remove();
      }, 2000); 
  </script>
  @endif

  <div class="flex justify-between items-center mb-3 px-4">
    <div class="flex items-center gap-2">
      <img src="{{ asset('logo/jeongol_logo.jpg') }}" alt="Jeongol Logo" class="h-14 w-20 object-contain" />
      <h1 class="text-3xl font-semibold text-gray-800">Kitchen Dashboard</h1>
    </div>

    <div class="relative" x-data="{ open: false }">
      <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
        <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center font-bold text-black">
          {{ strtoupper(substr(Auth::user()->firstname, 0, 1)) }}
        </div>
      </button>

      <div x-show="open" x-transition x-cloak
        class="absolute right-0 mt-2 w-56 bg-white border rounded-lg shadow-lg z-50">
        <div class="px-4 py-3 border-b">
          <p class="text-sm font-medium">{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</p>
          <p class="text-xs text-gray-500">{{ Auth::user()->role }}</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">Logout</button>
        </form>
      </div>
    </div>
  </div>

  <div class="border-b border-gray-300 mb-6"></div>

  <div class="flex gap-6 max-w-9xl mx-auto items-start">

    <!-- Left Side: Orders with Tabs -->
    <div class="bg-white border rounded-xl shadow-lg w-1/2 flex flex-col" x-data="{ activeTab: 'pending' }">
      
      <!-- Tabs Header -->
      <div class="border-b bg-gray-100 rounded-t-xl flex">
        <button 
          @click="activeTab = 'pending'" 
          :class="activeTab === 'pending' ? 'bg-white border-b-2 border-orange-600 text-orange-600' : 'text-gray-600 hover:bg-gray-50'"
          class="flex-1 p-4 text-lg font-semibold transition-colors">
          Pending Orders
        </button>
        <button 
          @click="activeTab = 'served'" 
          :class="activeTab === 'served' ? 'bg-white border-b-2 border-green-600 text-green-600' : 'text-gray-600 hover:bg-gray-50'"
          class="flex-1 p-4 text-lg font-semibold transition-colors">
          Served Orders
        </button>
      </div>

      <!-- Pending Orders Tab -->
      <div x-show="activeTab === 'pending'" class="overflow-y-auto max-h-[75vh] p-4 space-y-4">
        @php
          $pendingOrdersFiltered = $pendingOrders->filter(function ($group) {
            return $group->first()->status === 'Pending';
          });
        @endphp

        @forelse($pendingOrdersFiltered as $groupKey => $orderGroup)
          <div x-data="{ open: false }" class="border rounded-lg p-4 bg-white cursor-pointer" @click="open = !open">
            <div class="flex justify-between items-center">
              <div class="flex-1">
                <h3 class="font-semibold text-gray-800 text-lg">
                  @php
                    $tableNumber = $orderGroup->first()->reservation->table->table_number
                      ?? $orderGroup->first()->walkin->table->table_number
                      ?? $orderGroup->first()->table->table_number
                      ?? null;
                    
                    $hasNotes = $orderGroup->filter(function($order) {
                      return !empty($order->notes) && trim($order->notes) !== '';
                    })->isNotEmpty();
                  @endphp

                  @if($tableNumber)
                    Table {{ $tableNumber }}
                  @else
                    No Table
                  @endif
                </h3>
               
              </div>
              <span class="text-sm text-white bg-orange-600 rounded px-3 py-1">Pending</span>
            </div>

            <div x-show="open" x-transition class="mt-3 pt-3 border-t">
              <ul class="space-y-3">
                @foreach($orderGroup as $order)
                  <li class="flex justify-between items-start">
                    <div class="flex-1">
                      <div class="flex items-center gap-2">
                        <span class="font-medium">{{ $order->menu->menu_item ?? 'Menu item not found' }}</span>
                        <span class="font-semibold text-gray-700">x{{ $order->quantity }}</span>
                      </div>
                    </div>
                  </li>
                @endforeach
              </ul>

              @php
                $firstOrder = $orderGroup->first();
                $groupNote = $firstOrder->notes ?? null;
              @endphp

              <div class="mt-3 pt-3 border-t">
                <div class="text-sm text-black bg-yellow-100 border-l-4 border-yellow-500 px-3 py-2 rounded">
                  <span class="font-semibold">Note:</span> 
                  {{ !empty($groupNote) && trim($groupNote) !== '' ? $groupNote : 'No notes' }}
                </div>
              </div>

              <form action="{{ route('kitchen.served') }}" method="POST" class="mt-4" @click.stop>
                @csrf
                <input type="hidden" name="order_id" value="{{ $orderGroup->first()->id }}">
                <button type="submit"
                  class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded transition-colors">
                  Mark as Served
                </button>
              </form>
            </div>
          </div>
        @empty
          <p class="text-center text-gray-500 py-8">No pending orders</p>
        @endforelse
      </div>

      <!-- Served Orders Tab -->
      <div x-show="activeTab === 'served'" class="overflow-y-auto max-h-[75vh] p-4 space-y-4">
        @php
          $servedOrdersFiltered = $pendingOrders->filter(function ($group) {
            return $group->first()->status === 'Served';
          });
        @endphp

        @forelse($servedOrdersFiltered as $groupKey => $orderGroup)
          <div x-data="{ open: false }" class="border rounded-lg p-4 bg-white cursor-pointer" @click="open = !open">
            <div class="flex justify-between items-center">
              <div class="flex-1">
                <h3 class="font-semibold text-gray-800 text-lg">
                  @php
                    $tableNumber = $orderGroup->first()->reservation->table->table_number
                      ?? $orderGroup->first()->walkin->table->table_number
                      ?? $orderGroup->first()->table->table_number
                      ?? null;
                    
                    $hasNotes = $orderGroup->filter(function($order) {
                      return !empty($order->notes) && trim($order->notes) !== '';
                    })->isNotEmpty();
                  @endphp

                  @if($tableNumber)
                    Table {{ $tableNumber }}
                  @else
                    No Table
                  @endif
                </h3>
                <p class="text-sm mt-1 flex items-center gap-1 {{ $hasNotes ? 'text-yellow-700' : 'text-gray-500' }}">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                  </svg>
                  {{ $hasNotes ? 'Has notes' : 'No notes' }}
                </p>
              </div>
              <span class="text-sm text-white bg-green-600 rounded px-3 py-1">Served</span>
            </div>

            <div x-show="open" x-transition class="mt-3 pt-3 border-t">
              <ul class="space-y-2">
                @foreach($orderGroup as $order)
                  <li class="flex justify-between items-start">
                    <div class="flex-1">
                      <span class="font-medium">{{ $order->menu->menu_item ?? 'Menu item not found' }}</span>
                      @if($order->notes)
                        <p class="text-xs text-gray-600 bg-yellow-50 px-2 py-1 rounded mt-1 inline-block">
                          Note: {{ $order->notes }}
                        </p>
                      @endif
                    </div>
                    <span class="font-semibold ml-2">x{{ $order->quantity }}</span>
                  </li>
                @endforeach
              </ul>
            </div>
          </div>
        @empty
          <p class="text-center text-gray-500 py-8">No served orders</p>
        @endforelse
      </div>

    </div>

    <!-- Right Side: Two Forms -->
    <div class="w-1/2 space-y-6">

      <!-- FORM 1: Unlimited Refills -->
      <div class="bg-white border rounded-xl shadow-lg flex flex-col">
        <div class="border-b bg-blue-50 rounded-t-xl">
          <p class="p-4 text-lg font-semibold text-gray-700">Unlimited Refills</p>
        </div>

        <div class="p-4">
          <form action="{{ route('kitchen.unlimited.refill') }}" method="POST" class="space-y-4">
            @csrf

            <div>
              <label for="table_unlimited" class="block text-sm font-medium text-gray-700">Table Number</label>
              <select name="table_id" id="table_unlimited" required
                  class="mt-1 w-full px-3 py-2 border rounded-lg text-base focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="" disabled selected>Select a table</option>
                @foreach($tables as $table)
                  <option value="{{ $table->id }}">
                    Table {{ $table->table_number }}
                  </option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Select Ingredients</label>
              <div class="border rounded-lg p-3 max-h-64 overflow-y-auto space-y-2">
                @foreach($unlimitedIngredients as $ingredient)
                  <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                    <input type="checkbox" name="ingredients[{{ $ingredient->id }}][selected]" value="1"
                      class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500 rounded"
                      onchange="toggleQuantityInput({{ $ingredient->id }})">

                    <span class="ml-3 flex-1">
                      <span class="font-medium">{{ $ingredient->name }}</span>
                    </span>

                    <input type="number" name="ingredients[{{ $ingredient->id }}][quantity]"
                      id="quantity_{{ $ingredient->id }}" min="50" step="50" placeholder="Grams" disabled
                      class="w-24 px-2 py-1 border rounded text-sm focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed">
                  </label>
                @endforeach
              </div>
            </div>

            <div>
              <button type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                Add Selected Refills
              </button>
            </div>

          </form>
        </div>
      </div>

      <!-- FORM 2: Additional Orders -->
      <div class="bg-white border rounded-xl shadow-lg flex flex-col">
        <div class="border-b bg-green-50 rounded-t-xl">
          <p class="p-4 text-lg font-semibold text-gray-700">Additional Orders</p>
        </div>

        <div class="p-4">
          <form action="{{ route('kitchen.orders.additional') }}" method="POST" class="space-y-4">
            @csrf

            <div>
              <label for="table_order" class="block text-sm font-medium text-gray-700">Table Number</label>
              <select name="table_id" id="table_order" required
                  class="mt-1 w-full px-3 py-2 border rounded-lg text-base focus:ring-2 focus:ring-green-500 focus:outline-none">
                <option value="" disabled selected>Select a table</option>
                @foreach($tables as $table)
                  <option value="{{ $table->id }}">
                    Table {{ $table->table_number }}
                  </option>
                @endforeach
              </select>
            </div>

            <div>
              <label for="menu_id" class="block text-sm font-medium text-gray-700">Menu Item</label>
              <select name="menu_id" id="menu_id" required
                  class="mt-1 w-full px-3 py-2 border rounded-lg text-base focus:ring-2 focus:ring-green-500 focus:outline-none">
                <option value="" disabled selected>Select menu item</option>
                @foreach($aLaCarteMenus as $menu)
                  <option value="{{ $menu->id }}" data-price="{{ $menu->price }}">
                    {{ $menu->menu_item }}
                  </option>
                @endforeach
              </select>
            </div>

            <div>
              <label for="quantity_order" class="block text-sm font-medium text-gray-700">Quantity</label>
              <input type="number" name="quantity" id="quantity_order" required min="1" value="1"
                class="mt-1 w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <div>
              <button type="submit"
                class="w-full bg-green-600 text-white py-2 rounded-lg font-semibold hover:bg-green-700 transition">
                Add to Order
              </button>
            </div>

          </form>
        </div>
      </div>

    </div>

  </div>

  <script>
    function toggleQuantityInput(ingredientId) {
      const checkbox = document.querySelector(`input[name="ingredients[${ingredientId}][selected]"]`);
      const quantityInput = document.getElementById(`quantity_${ingredientId}`);

      if (checkbox.checked) {
        quantityInput.disabled = false;
        quantityInput.focus();
        quantityInput.required = true;
      } else {
        quantityInput.disabled = true;
        quantityInput.value = '';
        quantityInput.required = false;
      }
    }
  </script>

</body>

</html>
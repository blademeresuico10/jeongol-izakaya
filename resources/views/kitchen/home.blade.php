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

  {{-- Success/Error Messages --}}
  @if(session('success'))
    <div class="max-w-9xl mx-auto mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
    {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="max-w-9xl mx-auto mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
    {{ session('error') }}
    </div>
  @endif

  <div class="flex gap-6 max-w-9xl mx-auto items-start">

    <div class="bg-white border rounded-xl shadow-lg w-1/2 flex flex-col">
      <div class="border-b bg-gray-100 rounded-t-xl">
        <p class="p-4 text-lg font-semibold text-gray-700">Current Active Orders</p>
      </div>

      <div class="overflow-y-auto max-h-[75vh] p-4 space-y-4">
        @forelse($pendingOrders->filter(function ($group) {
        $order = $group->first();
        $status = $order->reservation->status
        ?? $order->walkin->status
        ?? $order->status;
        return $status !== 'Completed';
      }) as $groupKey => $orderGroup)

            <div x-data="{ open: false }" class="border rounded-lg p-4 bg-white cursor-pointer" @click="open = !open">

            <div class="flex justify-between items-center">
          <h3 class="font-semibold text-gray-800 text-lg">
              @php
          $tableNumber = $orderGroup->first()->reservation->table->table_number
          ?? $orderGroup->first()->walkin->table->table_number
          ?? $orderGroup->first()->table->table_number
          ?? null;

        @endphp

             @if($tableNumber)
          Table {{ $tableNumber }}
        @else
          No Table
        @endif
            </h3>
            @if ($orderGroup->first()->status === 'Pending')
        <span class="text-sm text-white bg-orange-600 rounded p-2">Pending</span>
        @elseif ($orderGroup->first()->status === 'Served')
        <span class="text-sm text-white bg-green-600 rounded p-2">Served</span>
      @elseif ($orderGroup->first()->status === 'Cancelled')
        <span  pan class="text-sm text-white bg-gray-500 rounded p-2">Cancelled</span>
       @endif

              </div>

            <div x-show="open" x-transition class="mt-3 pt-3 border-t">
            <ul class="space-y-2">
            @foreach($orderGroup as $order)
          <li class="flex justify-between">
          <span>{{ $order->menu->menu_item ?? 'Menu item not found' }}</span>
          <span class="font-semibold">x{{ $order->quantity }}</span>
          </li>
        @endforeach
          </ul>

            @php
          $notes = $orderGroup->whereNotNull('notes')->pluck('notes')->unique();
        @endphp

            @if($notes->isNotEmpty())
            <div class="mt-3 pt-3 border-t">
          <p class="font-semibold mb-1">Notes:</p>
            @foreach($notes as $note)
          <p class="text-sm text-gray-600">{{ $note }}</p>
          @endforeach
            </div>
        @endif
            @if ($orderGroup->first()->status === 'Pending')
          <form action="{{ route('kitchen.served') }}" method="POST" class="mt-4" @click.stop>
            @csrf
            <input type="hidden" name="order_id" value="{{ $orderGroup->first()->id }}">
          <button type="submit"
          class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded">
          Mark as Served
            </button>
          </form>
        @endif

          </div>
        </div>
    @empty
      <p class="text-center text-gray-500 py-8">No pending orders</p>
  @endforelse
      </div>
    </div>

    {{-- Right Side: Two Forms --}}
    <div class="w-1/2 space-y-6">

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
                 <label   abel class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                  <input type="checkbox" name="ingredients[{{ $ingredient->id }}][selected]" value="1"
              class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500 rounded"
                  onchange="toggleQuantityInput({{ $ingredient->id }})">

               <span class="ml-3 flex-1">
                  <span class="font-medium">{{ $ingredient->name }}</span>
                  <span class="text-xs text-gray-500 ml-2">

                     </span>
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

      {{-- FORM 2: Additional Orders (PAID) --}}
      <div class="bg-white border rounded-xl shadow-lg flex flex-col">
        <div class="border-b bg-green-50 rounded-t-xl">
          <p class="p-4 text-lg font-semibold text-gray-700">Additional Orders</p>
        </div>

        <div class="p-4">
          <form action="{{ route('kitchen.orders.additional') }}" method="POST" class="space-y-4"> @csrf

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
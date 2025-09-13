<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Kitchen</title>
  <link rel="shortcut icon" type="x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
  @vite('resources/css/app.css')
  <style>
    .progress-vertical {
      width: 20px;
      height: 60px;
      position: relative;
      background-color: #e5e7eb;
      border-radius: 4px;
      overflow: hidden;
      display: flex;
      align-items: flex-end;
    }

    .progress-bar-vertical {
      width: 100%;
      transition: height 0.4s;
    }

    .bg-success {
      background-color: #10b981;
    }

    .bg-warning {
      background-color: #f59e0b;
    }

    .bg-danger {
      background-color: #ef4444;
    }
  </style>
</head>

<body class="font-sans p-5 bg-gray-50">

  <div class="flex justify-between items-center bg-white p-4 border border-gray-300 mb-5">
    <h2 class="text-left whitespace-nowrap text-xl font-semibold">Meat Stock Levels</h2>

    <div class="flex ml-4">
      @foreach ($stock as $item)
        @php
        $qty = $item->stock_quantity;
        $statusColor = $qty >= 60 ? 'bg-success' :
        ($qty >= 30 ? 'bg-warning' : 'bg-danger');
      @endphp
        <div class="flex flex-col items-center mx-3">
        <div class="progress-vertical mb-1">
          <div class="progress-bar-vertical {{ $statusColor }}" style="height: {{ $qty }}%;"></div>
        </div>
        <div class="font-medium">{{ $item->stock_name }}</div>
        </div>
    @endforeach
    </div>

    <div class="flex flex-col items-start ml-4">
      <div class="flex items-center mb-1">
        <div class="w-5 h-2.5 bg-green-500"></div>
        <span class="ml-2 whitespace-nowrap">60kg up</span>
      </div>
      <div class="flex items-center mb-1">
        <div class="w-5 h-2.5 bg-orange-500"></div>
        <span class="ml-2 whitespace-nowrap">30kg - 59kg</span>
      </div>
      <div class="flex items-center">
        <div class="w-5 h-2.5 bg-red-500"></div>
        <span class="ml-2 whitespace-nowrap">Below 29kg</span>
      </div>
    </div>
    <button class="py-2.5 px-4 bg-blue-600 text-white border-none rounded cursor-pointer ml-4 hover:bg-blue-700"
      onclick="openModal()">
      Update Stock
    </button>
  </div>

  @php
  $reservationGroups = $reservations->groupBy('reservation_id');
  @endphp

  <div class="reservation-section">
    <h4 class="text-lg font-semibold mb-3">Order List</h4>
    @if ($reservationGroups->isEmpty())
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">No reservations found for
      today.</div>
  @else
    <div class="overflow-x-auto">
      <table class="min-w-full border border-gray-300 bg-white">
      <thead class="bg-gray-50">
        <tr>
        <th class="border border-gray-300 px-4 py-2 text-left font-medium text-gray-700">Table Number</th>
        <th class="border border-gray-300 px-4 py-2 text-left font-medium text-gray-700">No. of Pax</th>
        <th class="border border-gray-300 px-4 py-2 text-left font-medium text-gray-700">Orders/Quantity</th>
        <th class="border border-gray-300 px-4 py-2 text-left font-medium text-gray-700">Note</th>
        <th class="border border-gray-300 px-4 py-2 text-left font-medium text-gray-700">Order Time</th>
        <th class="border border-gray-300 px-4 py-2 text-left font-medium text-gray-700">Added Order</th>
        </tr>
      </thead>
      <tbody class="align-middle">
        @foreach ($reservationGroups as $reservationId => $group)
        <tr>
        <td class="border border-gray-300 px-4 py-2">{{ $group->first()->table_id }}</td>
        <td class="border border-gray-300 px-4 py-2">{{ $group->first()->pax }}</td>
        <td class="border border-gray-300 px-4 py-2">
        @php
      $orders = $group->map(function ($r) {
        if (!$r->menu_item)
        return null;
        $cleanName = str_replace([' Lunch', ' Dinner'], '', $r->menu_item);
        return $r->quantity . 'x ' . $cleanName;
      })->filter()->implode(', ');
      @endphp
        {{ $orders ?: 'No orders' }}
        </td>
        <td class="border border-gray-300 px-4 py-2">
        @php
      $notes = $group->pluck('order_notes')->filter()->unique()->implode(', ');
      @endphp
        {{ $notes ?: 'None' }}
        </td>
        <td class="border border-gray-300 px-4 py-2">
        {{ \Carbon\Carbon::parse($group->first()->reservation_time)->format('h:i A') }}
        </td>
        <td class="border border-gray-300 px-4 py-2">
        @php
      $reservationId = $group->first()->reservation_id;

      $recentChanges = \App\Models\OrderDetail::where('reservation_id', $reservationId)
        ->whereNotNull('change_type')
        ->orderBy('change_timestamp', 'desc')
        ->limit(5)
        ->get()
        ->map(function ($order) {
        $menu = \App\Models\menu::find($order->menu_id);
        $menuName = $menu ? str_replace([' Lunch', ' Dinner'], '', $menu->menu_item) : 'Unknown Item';

        // Calculate quantity based on change type
        $quantity = $order->quantity;
        if ($order->change_type === 'addition' && $order->previous_quantity) {
        // For additions: show only the amount added (new - old)
        $quantity = $order->quantity - $order->previous_quantity;
        } else if ($order->change_type === 'reduction' && $order->previous_quantity) {
        // For reductions: show only the amount reduced (old - new)
        $quantity = $order->previous_quantity - $order->quantity;
        } else if ($order->change_type === 'removal') {
        // For removals: show the full quantity that was removed
        $quantity = $order->quantity;
        }

        return [
        'type' => $order->change_type,
        'menu_name' => $menuName,
        'quantity' => $quantity,
        'timestamp' => $order->change_timestamp ? $order->change_timestamp->format('h:i A') : '',
        ];
        });
      @endphp

        @if($recentChanges->count() > 0)
        <div class="space-y-1">
        @foreach($recentChanges as $change)
        <div class="flex items-start justify-between text-sm">
        <div class="flex items-start space-x-1">
        @if($change['type'] === 'addition')
        <span class="text-green-600 font-bold text-xs mt-0.5">+</span>
        <div class="text-green-600 font-semibold">
        {{ $change['quantity'] }}x {{ $change['menu_name'] }}
        </div>
        @elseif($change['type'] === 'reduction')
        <span class="text-orange-600 font-bold text-xs mt-0.5">-</span>
        <div class="text-orange-600 font-semibold">
        {{ $change['quantity'] }}x {{ $change['menu_name'] }}
        </div>
        @elseif($change['type'] === 'removal')
        <span class="text-red-600 font-bold text-xs mt-0.5">×</span>
        <div class="text-red-600 font-semibold">
        {{ $change['quantity'] }}x {{ $change['menu_name'] }}
        </div>
        @elseif($change['type'] === 'modification')
        <span class="text-blue-600 font-bold text-xs mt-0.5">~</span>
        <div class="text-blue-600 font-semibold">
        Modified {{ $change['menu_name'] }}
        </div>
        @endif
        </div>

        @if($change['timestamp'])
        <div class="text-xs text-gray-500 ml-2 flex-shrink-0">
        {{ $change['timestamp'] }}
        </div>
        @endif
        </div>
      @endforeach
        </div>
      @else
        <div class="text-center"> 
        <span class="text-gray-400 text-sm">No recent changes</span>
        </div>
      @endif
        </td>
        </tr>
      @endforeach
      </tbody>
      </table>
    </div>
  @endif
  </div>

  <!-- Modal -->
  <div id="updateStockModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
    role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()">
      </div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <div
        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
        <form action="{{ route('kitchen.updateStock') }}" method="POST">
          @csrf
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Update Stock Levels</h3>
              <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal()">
                <span class="sr-only">Close</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <div class="p-4">
              <table class="w-full border border-gray-300 text-center">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="border border-gray-300 px-4 py-2 font-medium text-gray-700">Item</th>
                    <th class="border border-gray-300 px-4 py-2 font-medium text-gray-700">Stock Level (kg)</th>
                    <th class="border border-gray-300 px-4 py-2 font-medium text-gray-700">Status</th>
                    <th class="border border-gray-300 px-4 py-2 font-medium text-gray-700">Action</th>
                  </tr>
                </thead>
                <tbody class="align-middle">
                  @foreach ($stock as $item)
                @php
            $qty = $item->stock_quantity;
            $statusColor = $qty >= 60 ? 'bg-success' :
            ($qty >= 30 ? 'bg-warning' : 'bg-danger');
          @endphp
                <tr>
                <td class="border border-gray-300 px-4 py-2">{{ $item->stock_name }}</td>
                <td class="border border-gray-300 px-4 py-2">
                  <input type="number" name="stocks[{{ $item->id }}]" value="{{ $qty }}" min="0" max="100"
                  class="w-20 px-2 py-1 border border-gray-300 rounded text-center stock-input bg-gray-50"
                  readonly required>
                </td>
                <td class="border border-gray-300 px-4 py-2">
                  <div class="progress-vertical mx-auto">
                  <div class="progress-bar-vertical {{ $statusColor }}" style="height: {{ $qty }}%;"></div>
                  </div>
                </td>
                <td class="border border-gray-300 px-4 py-2">
                  <svg class="w-5 h-5 text-blue-600 cursor-pointer edit-stock mx-auto" data-id="{{ $item->id }}"
                  fill="currentColor" viewBox="0 0 20 20">
                  <path
                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                  </path>
                  </svg>
                </td>
                </tr>
          @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button type="submit"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
              Save Changes
            </button>
            <button type="button"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              onclick="closeModal()">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <a class="fixed bottom-5 right-5 bg-red-600 text-white py-2.5 px-4 rounded no-underline font-bold z-50 hover:bg-red-700"
    href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
    Logout
  </a>
  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>

  <script>
    function openModal() {
      document.getElementById('updateStockModal').classList.remove('hidden');
    }

    function closeModal() {
      document.getElementById('updateStockModal').classList.add('hidden');
    }

    document.addEventListener("DOMContentLoaded", function () {
      document.querySelectorAll('.edit-stock').forEach(icon => {
        icon.addEventListener('click', function () {
          const id = this.dataset.id;
          const input = document.querySelector(`input[name="stocks[${id}]"]`);
          if (input) {
            input.removeAttribute('readonly');
            input.classList.remove('bg-gray-50');
            input.classList.add('bg-white');
            input.focus();
          }
        });
      });
    });
  </script>

</body>

</html>
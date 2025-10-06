<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Reservation</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="shortcut icon" type="x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
  @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 min-h-screen">
  <div class="reservation-section">
    <div class="flex items-center justify-center mt-4 mb-6">
      <h2 class="text-3xl font-bold text-gray-800">Today's Reservation</h2>
    </div>

    @php
      $groups = $combined->groupBy('reservation_id');
    @endphp

    <div class="flex justify-center mb-6 px-4">
      <div class="w-full max-w-4xl">
        <div class="flex bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
          <button onclick="showTab('active')" id="active-tab"
            class="tab-button flex-1 py-3 px-6 text-center font-semibold bg-blue-600 text-white">
            Active
          </button>
          <button onclick="showTab('finished')" id="finished-tab"
            class="tab-button flex-1 py-3 px-6 text-center font-semibold bg-gray-200 text-gray-700">
            Finished
          </button>
          <button onclick="showTab('cancelled')" id="cancelled-tab"
            class="tab-button flex-1 py-3 px-6 text-center font-semibold bg-gray-200 text-gray-700">
            Cancelled
          </button>
        </div>
      </div>
    </div>

    <div class="flex justify-center mb-4 px-4">
      <div class="w-full max-w-4xl space-y-3" style="max-height:500px; overflow:auto;">
        @foreach ($groups as $resId => $group)
          @php
            $first = $group->first();
            $customerName = $first->customer_name ?? 'Unknown Customer';
            $orders = $group->map(fn($r) => $r->menu_item ? ['name' => str_replace([' Lunch', ' Dinner'], '', $r->menu_item), 'quantity' => $r->quantity] : null)
                            ->filter()
                            ->values();
            $totalQuantity = $group->sum('quantity');
            $orderTime = \Carbon\Carbon::parse($first->started_at)->format('h:i A');

            if ($first->status === 'Rejected') {
              $statusText = 'Cancelled';
              $statusClasses = 'bg-red-600 text-white';
              $tabClass = 'cancelled-item';
            } elseif ($first->status === 'Active') {
              $statusText = 'Active';
              $statusClasses = 'bg-orange-600 text-white';
              $tabClass = 'active-item';
            } elseif ($first->status === 'Completed') {
              $statusText = 'Finished';
              $statusClasses = 'bg-green-100 text-green-700';
              $tabClass = 'finished-item';
            } elseif ($first->status === 'Pending') {
              $statusText = 'Pending';
              $statusClasses = 'bg-yellow-100 text-yellow-700';
              $tabClass = 'active-item';
            } else {
              $statusText = 'Unknown';
              $statusClasses = 'bg-gray-100 text-gray-700';
              $tabClass = 'active-item';
            }
          @endphp

          <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden {{ $tabClass }}">
            <div class="reservation-header cursor-pointer p-4 hover:bg-gray-50 transition-colors duration-200"
              onclick="toggleOrders('reservation-{{ $resId }}')">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                  <span class="text-lg font-bold text-gray-900">{{ $customerName }}</span>
                  <div class="bg-green-600 text-white px-3 py-1 rounded-full font-semibold text-sm">
                    Table {{ $first->table_number }}
                  </div>
                  <span class="text-gray-700 font-medium">{{ $first->pax }} pax</span>

                  <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-semibold">
                    {{ ucfirst($first->source ?? 'Reservation') }} 
                  </span>

                  <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $statusClasses }}">
                    {{ $statusText }}
                  </span>
                </div>
                <div class="flex items-center space-x-3">
                  <span class="text-gray-700 font-medium">{{ $orderTime }}</span>
                  <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200 chevron-icon"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 9l-7 7-7-7"></path>
                  </svg>
                </div>
              </div>
            </div>

            <div id="reservation-{{ $resId }}" class="order-details hidden border-t border-gray-200">
              <div class="p-4 bg-gray-50">
                <h4 class="text-sm font-semibold text-gray-800 mb-3">Order Details</h4>
                @if(count($orders) > 0)
                  <div class="grid gap-2">
                    @foreach($orders as $order)
                      <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200">
                        <span class="text-gray-800 font-medium">{{ $order['name'] }}</span>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm font-semibold">
                          {{ $order['quantity'] }}x
                        </span>
                      </div>
                    @endforeach
                  </div>
                @else
                  <div class="text-center text-gray-500 py-2">No orders placed</div>
                @endif
              </div>
            </div>
          </div>
        @endforeach

        @if($groups->isEmpty())
          <div class="text-center py-12 text-gray-500">No Reservations or Walk-ins</div>
        @endif
      </div>
    </div>
  </div>

  <a class="fixed bottom-5 right-5 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-lg z-50 transition-all duration-200 transform hover:scale-105"
    href="{{ route('receptionist.home') }}">
    Back to main page
  </a>

  <script>
    function showTab(tabName) {
      document.querySelectorAll('.tab-button').forEach(btn => {
        btn.className = 'tab-button flex-1 py-3 px-6 text-center font-semibold bg-gray-200 text-gray-700';
      });
      document.getElementById(tabName + '-tab').className = 'tab-button flex-1 py-3 px-6 text-center font-semibold bg-blue-600 text-white';

      document.querySelectorAll('.active-item, .finished-item, .cancelled-item').forEach(el => el.style.display = 'none');
      document.querySelectorAll('.' + tabName + '-item').forEach(el => el.style.display = 'block');
    }
    document.addEventListener('DOMContentLoaded', () => showTab('active'));
    function toggleOrders(id) {
      const el = document.getElementById(id);
      const chev = el.previousElementSibling.querySelector('.chevron-icon');
      el.classList.toggle('hidden');
      chev.style.transform = el.classList.contains('hidden') ? 'rotate(0)' : 'rotate(180deg)';
    }
  </script>
</body>
</html>

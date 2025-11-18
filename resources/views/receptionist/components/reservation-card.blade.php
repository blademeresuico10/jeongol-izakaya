@php
  $first = $group->first();
  $customerName = $first->customer_name ?? 'Unknown Customer';
  $orders = $group->map(function ($r) {
    if (!$r->menu_item) return null;
    $cleanName = str_replace([' Lunch', ' Dinner'], '', $r->menu_item);
    return ['name' => $cleanName, 'quantity' => $r->quantity];
  })->filter()->values();
  $totalQuantity = $group->sum('quantity');
  $orderTime = \Carbon\Carbon::parse($first->reservation_time)->format('h:i A');
  if ($first->status === 'Rejected') {
    $statusText = 'Cancelled';
    $statusClasses = 'bg-red-100 text-red-700';
  } elseif ($first->status === 'Pending') {
    $statusText = 'Pending';
    $statusClasses = 'bg-yellow-100 text-yellow-700';
  } elseif ($first->status === 'Accepted') {
    $isFinished = $servedTransactions->contains($first->reservation_id);
    $statusText = $isFinished ? 'Finished' : 'Active';
    $statusClasses = $isFinished ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700';
  } else {
    $statusText = 'Unknown';
    $statusClasses = 'bg-gray-100 text-gray-700';
  }
@endphp
<div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
  <div class="reservation-header cursor-pointer p-4 hover:bg-gray-50 transition-colors duration-200"
       onclick="toggleOrders('reservation-{{ $reservationId }}')">
    <div class="flex items-center justify-between">
      <div class="flex items-center space-x-4">
        <div class="flex items-center space-x-2">
          <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
          <span class="text-lg font-bold text-gray-900">{{ $customerName }}</span>
        </div>
        <div class="bg-green-600 text-white px-3 py-1 rounded-full font-semibold text-sm">
          Table {{ $first->table_number }}
        </div>
        <div class="flex items-center space-x-2">
          <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-4.5l1.5 1.5-1.5 1.5">
            </path>
          </svg>
          <span class="text-gray-700 font-medium">{{ $first->pax }} pax</span>
        </div>
        <div class="flex items-center space-x-2">
          <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
            </path>
          </svg>
          <span class="text-gray-700">{{ count($orders) }} items ({{ $totalQuantity }} total)</span>
          <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $statusClasses }}">
            {{ $statusText }}
          </span>
        </div>
      </div>
      <div class="flex items-center space-x-3">
        <div class="flex items-center space-x-2">
          <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <span class="text-gray-700 font-medium">{{ $orderTime }}</span>
        </div>
        <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200 chevron-icon" fill="none"
             stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
      </div>
    </div>
  </div>
  <div id="reservation-{{ $reservationId }}" class="order-details hidden border-t border-gray-200">
    <div class="p-4 bg-gray-50">
      <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
        <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
          </path>
        </svg>
        Order Details
      </h4>
      @if(count($orders) > 0)
        <div class="grid gap-2">
          @foreach($orders as $order)
            <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200">
              <span class="text-gray-800 font-medium">{{ $order['name'] }}</span>
              <div class="flex items-center space-x-2">
                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm font-semibold">
                  {{ $order['quantity'] }}x
                </span>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="text-center py-4">
          <div class="text-gray-500 mb-2">
            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
              </path>
            </svg>
            No orders placed yet
          </div>
        </div>
      @endif
      <div class="mt-4 pt-3 border-t border-gray-200">
        <div class="grid grid-cols-3 gap-4 text-sm">
          <div>
            <span class="text-gray-500">Customer:</span>
            <span class="text-gray-800 font-lg ml-2">{{ $customerName }}</span>
          </div>
          <div>
            <span class="text-gray-500">Total Items:</span>
            <span class="text-gray-800 font-medium ml-2">{{ $totalQuantity }}</span>
          </div>
          <div>
            <span class="text-gray-500">Status:</span>
            <span class="text-gray-800 font-medium ml-2">{{ $statusText }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
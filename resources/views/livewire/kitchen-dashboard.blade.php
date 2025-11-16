<div>
  <div x-data="{ 
    show: false, 
    type: 'success', 
    message: '' 
  }" @notify.window="
    show = true; 
    type = $event.detail.type; 
    message = $event.detail.message;
    setTimeout(() => show = false, 3000);
  " x-show="show" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-x-full"
    x-transition:enter-end="opacity-100 transform translate-x-0" x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" :class="{
      'bg-green-500': type === 'success',
      'bg-red-500': type === 'error',
      'bg-yellow-500': type === 'warning'
    }" class="fixed top-4 right-4 p-4 rounded shadow-lg text-white z-50" style="display: none;">
    <span x-text="message"></span>
  </div>



  <div class="border-b border-gray-300 mb-6"></div>

  <div class="max-w-full mx-auto px-4">
    <div wire:poll.10s class="w-full">

      <!-- Grid Layout for Orders -->
      <div class="grid grid-cols-2 md:grid-cols-2 xl:grid-cols-4 gap-4">

        {{-- Pending / Ready Orders --}}
        @forelse($this->pendingOrders as $groupKey => $orderGroup)
        @php
        $tableNumber = $orderGroup->first()->reservation->table->table_number
        ?? $orderGroup->first()->walkin->table->table_number
        ?? 'N/A';
        $status = $orderGroup->first()->status ?? 'Pending';
        $statusColor = $status === 'Pending' ? 'bg-red-400' : 'bg-yellow-400';
        @endphp

        @if(in_array($status, ['Pending', 'Ready']))
        <div class="border rounded-lg shadow-md bg-white overflow-hidden flex flex-col h-[420px]">
          <!-- Header -->
          <div class="p-4 {{ $statusColor }} text-white">
          <h3 class="text-xl font-bold">Table {{ $tableNumber }}</h3>
          </div>

          <!-- Order Items -->
          <div class="p-4 space-y-3 flex-1 overflow-y-auto max-h-[280px]">
          @foreach($orderGroup as $order)
        <div class="flex justify-between items-center bg-gray-50 rounded px-3 py-2">
          <span class="font-medium text-sm text-gray-800">
          {{ $order->quantity }} x {{ $order->menu->menu_item ?? 'Unknown' }}
          </span>
          <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd"
          d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
          clip-rule="evenodd" />
          </svg>
        </div>
        @endforeach

          @if($orderGroup->first()->notes)
        <div class="mt-3 bg-yellow-50 border-l-4 border-yellow-400 rounded px-3 py-2">
          <p class="text-xs font-semibold text-gray-800">
          Note: {{ $orderGroup->first()->notes }}
          </p>
        </div>
        @endif
          </div>

          <!-- Action Button -->
          <div class="p-4 pt-9">
          @if($status === 'Pending')
        <button wire:click="markAsReady({{ $orderGroup->first()->id }})"
          class="w-full bg-white border-2 border-gray-300 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
          Mark as Ready
        </button>
        @elseif($status === 'Ready')
        <button class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold cursor-default">
          Ready
        </button>
        @endif
          </div>
        </div>
      @endif
    @empty
    @endforelse


        {{-- Pending Refills --}}
        @forelse($this->pendingRefills as $refill)
        @php
        $tableNumber = $refill->order->reservation->table->table_number
        ?? $refill->order->walkin->table->table_number
        ?? 'N/A';
        $status = $refill->status ?? 'Pending';
        $statusColor = $status === 'Pending' ? 'bg-blue-400' : 'bg-blue-600';
        @endphp

        @if(in_array($status, ['Pending', 'Ready']))
        <div class="border rounded-lg shadow-md bg-white overflow-hidden flex flex-col">

          <!-- Header -->
          <div class="p-4 {{ $statusColor }} text-white">
          <h3 class="text-xl font-bold">Table {{ $tableNumber }}</h3>
          </div>

          <!-- Refill Item -->
          <div class="p-4 space-y-3 flex-1 overflow-y-auto">
          <div class="flex justify-between items-center bg-gray-50 rounded px-3 py-2">
          <span class="font-medium text-sm text-gray-800">
          {{ $refill->quantity }} x {{ $refill->ingredient->name ?? 'Unknown' }} (Refill)
          </span>
          <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd"
            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
            clip-rule="evenodd" />
          </svg>
          </div>
          </div>

          <!-- Action Button -->
          <div class="p-4">
          @if($status === 'Pending')
        <button wire:click="markAsReady({{ $refill->id }}, 'refill')"
          class="w-full bg-white border-2 border-gray-300 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-50 transition">
          Mark as Ready
        </button>
        @else
        <button class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold cursor-default">
          Ready
        </button>
        @endif
          </div>

        </div>
      @endif

    @empty
    @endforelse

        {{-- Empty State --}}
        @if($this->pendingOrders->isEmpty() && $this->pendingRefills->isEmpty())
      <div class="col-span-full text-center py-12">
        <p class="text-gray-500 text-lg">No orders at the moment</p>
      </div>
    @endif

      </div>


    </div>
  </div>

  <!-- Success Modal -->
  @if (session()->has('success'))
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-data="{ show: true }"
    x-show="show" x-transition>
    <div class="bg-white p-6 rounded-xl text-center shadow-lg">
      <h2 class="text-lg font-semibold text-green-600 mb-2">Success</h2>
      <p class="text-gray-700 mb-4">{{ session('success') }}</p>
      <button @click="show = false" class="bg-green-600 text-white px-4 py-2 rounded">OK</button>
    </div>
    </div>
  @endif

  <!-- Error Modal -->
  @if (session()->has('error'))
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-data="{ show: true }"
    x-show="show" x-transition>
    <div class="bg-white p-6 rounded-xl text-center shadow-lg">
      <h2 class="text-lg font-semibold text-red-600 mb-2">Error</h2>
      <p class="text-gray-700 mb-4">{{ session('error') }}</p>
      <button @click="show = false" class="bg-red-600 text-white px-4 py-2 rounded">OK</button>
    </div>
    </div>
  @endif

</div>
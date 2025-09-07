<div class="flex flex-col">
  <p class="text-xl font-semibold text-gray-800 mb-4">{{ $label }}</p>

  <div class="w-full overflow-x-hidden">
    <div class="grid gap-4 mt-1 grid-cols-3 {{ $key === 'main' ? 'main-menu-grid' : '' }}">

      @foreach($items as $item)
        <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden cursor-pointer menu-card"
             onclick="selectMenuItem(this)" 
             data-id="{{ $item->id }}" 
             data-name="{{ $item->display_name }}" 
             data-category="{{ $key }}"
             data-price="{{ $item->price }}"
             @if($item->is_time_based ?? false)
               data-is-time-based="true"
               data-lunch-price="{{ $item->lunch_price }}"
               data-dinner-price="{{ $item->dinner_price }}"
             @else
               data-is-time-based="false"
             @endif>

          <div class="aspect-square w-full overflow-hidden">
            <img src="{{ asset('assets/jeongol-menu/' . $item->image) }}" 
                 alt="{{ $item->display_name }}" 
                 class="w-full h-full object-cover" />
          </div>

          <div class="p-4 text-center">
            <h5 class="font-small text-gray-900 text-sm mb-2">{{ $item->display_name }}</h5>
            <p class="text-md font-semibold text-orange-600 menu-price">
              @if($item->is_time_based ?? false)
                ₱{{ number_format($item->lunch_price, 2) }} 
              @else
                ₱{{ number_format($item->price, 2) }}
              @endif
            </p>
          </div>
        </div>
      @endforeach

    </div>
  </div>
</div>
<div class="flex flex-col">
  <p class="text-xl font-semibold text-gray-800 mb-4">{{ $label }}</p>

  <div class="w-full overflow-x-hidden">
    <div class="grid gap-4 mt-1 grid-cols-3 {{ strtolower($label) === 'main course' ? 'main-menu-grid' : '' }}">

      @foreach($items as $item)
      <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden menu-card 
        {{ $item->status === 'Blocked' ? 'opacity-50 pointer-events-none cursor-not-allowed' : 'cursor-pointer' }}"
        @if($item->status !== 'Blocked') onclick="selectMenuItem(this)" @endif 
        data-id="{{ $item->id }}"
        data-name="{{ $item->menu_item }}" 
        data-category="{{ $item->category_name }}" 
        data-category-id="{{ $item->category_id }}"
        data-price="{{ $item->regular_price }}"
        data-has-discount="{{ $item->has_customer_discount ? 'true' : 'false' }}">

        <div class="aspect-square w-full overflow-hidden relative">
          <img src="{{ asset('storage/jeongol_menu/' . $item->image) }}" 
               alt="{{ $item->menu_item }}"
               class="w-full h-full object-cover" />

          @if($item->status === 'Blocked')
          <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <span class="bg-white rounded-full p-4 shadow-md flex items-center justify-center">
              <i class="fas fa-lock text-3xl text-red-600"></i>
            </span>
          </div>
          @endif
        </div>

        <div class="p-4 text-center">
          <h5 class="font-small text-gray-900 text-sm mb-2">{{ $item->menu_item }}</h5>
          <p class="text-md font-semibold text-orange-600 menu-price">
            ₱{{ number_format($item->regular_price, 2) }}
          </p>
        </div>
      </div>
      @endforeach

    </div>
  </div>
</div>
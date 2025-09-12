<div class="flex flex-col">
  <p class="text-xl font-semibold text-gray-800 mb-4">{{ $label }}</p>

  <div class="w-full overflow-x-hidden">
    <div class="grid gap-4 mt-1 grid-cols-3 {{ $key === 'main' ? 'main-menu-grid' : '' }}">

      @foreach($items as $item)
      <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden cursor-pointer menu-card"
      onclick="selectMenuItem(this)" data-id="{{ $item->id }}" data-name="{{ $item->menu_item }}"
      data-category="{{ $key }}" data-price="{{ $item->regular_price }}"
      data-has-discount="{{ $item->has_customer_discount ? 'true' : 'false' }}" @if($item->has_customer_discount)
    data-student-price="{{ $item->student_price }}" data-govt-price="{{ $item->govt_employee_price }}" @endif>

      <div class="aspect-square w-full overflow-hidden">
        <img src="{{ asset('assets/jeongol-menu/' . $item->image) }}" alt="{{ $item->menu_item }}"
        class="w-full h-full object-cover" />
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
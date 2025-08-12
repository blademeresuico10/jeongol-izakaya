<div class="flex flex-col">
  <p class="text-lg font-semibold text-black-800 dark:text-blak">{{ $label }}</p>

  <div class="w-full overflow-x-hidden">
    <div class="grid gap-5 mt-1
      {{ $key === 'main' ? 'grid-cols-3 main-menu-grid' : 'grid-cols-3 sm:grid-cols-3 md:grid-cols-3' }}">

      @php $displayedMainItems = []; @endphp

      @foreach($items as $item)
        @php
        $baseName = $key === 'main'
        ? str_replace([' Lunch', ' Dinner'], '', $item->menu_item)
        : $item->menu_item;
      @endphp
  
        @if($key !== 'main' || !in_array($baseName, $displayedMainItems))
        @if($key === 'main') @php $displayedMainItems[] = $baseName; @endphp @endif

        <div
        class="menu-card bg-gray-150 border border-gray-300 rounded-md shadow hover:shadow-md hover:bg-gray-100 cursor-pointer transition duration-200 mt-5"
        onclick="selectMenuItem(this)" data-id="{{ $item->id }}" data-name="{{ $baseName }}" data-category="{{ $key }}"
        data-price="{{ $item->price }}">
        <div class="menu-image-container flex justify-center items-center h-[90px] w-full">
        <img src="{{ asset('assets/jeongol-menu/' . $item->image) }}" alt="{{ $baseName }}"
        class="w-full h-full object-cover" />
        </div>

        <div class="p-2">
        <h5 class="text-[13px] font-medium text-center text-black truncate">{{ $baseName }}</h5>
        </div>
        </div>
      @endif
    @endforeach

    </div>
  </div>
</div>
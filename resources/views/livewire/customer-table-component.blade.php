<div class="table-layout grid grid-cols-2 gap-4">
    @foreach($tables as $table)
        <div class="table-link {{ $table['is_available'] ? 'cursor-pointer' : 'cursor-not-allowed pointer-events-none' }}"
            wire:click="{{ $table['is_available'] ? 'selectTable(' . $table['id'] . ')' : '' }}"
            data-table-id="{{ $table['id'] }}">

            <div class="table relative flex flex-col items-center justify-center h-50 border rounded 
                  {{ $table['is_available'] ? 'bg-green-100' : 'bg-gray-400' }}">
                <div class="absolute top-1 text-xs">{{ $table['capacity'] }} Pax</div>

                @if(!$table['is_available'])
                    <div class="absolute top-1 left-1 bg-gray-700 text-white text-xs px-2 py-1 rounded">Reserved</div>
                @endif

                <div class="table-number text-lg font-semibold">Table {{ $table['table_number'] }}</div>
            </div>
        </div>
    @endforeach
</div>
<div class="table-layout" wire:poll.10s="loadTables">
    @foreach($tables as $table)
    <div class="table-link" data-table-id="{{ $table->id }}" data-table-number="{{ $table->table_number }}">
        <div class="table {{ $table->is_occupied ? 'occupied' : 'available' }}">
            <div class="table-number text-center">Table {{ $table->table_number }}</div>

            @if($table->is_occupied)
            <div class="text-center">OCCUPIED</div>
            @endif

            <div class="inline-options text-center"
                style="display:none; flex-direction: column; align-items: center; gap: 5px; margin-top: 10px;">
                
                <button
                    class="make-walkin-btn bg-blue-800 text-white border-none px-2.5 py-1.5 my-[3px] rounded cursor-pointer text-[17px] hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed"
                    data-table-id="{{ $table->id }}"
                    @if($table->is_occupied) hidden @endif>
                    Walk-in Order
                </button>

                <button
                    class="make-reservation-btn bg-blue-800 text-white border-none px-2.5 py-1.5 my-[3px] rounded cursor-pointer text-[17px] hover:bg-blue-700"
                    data-table-id="{{ $table->id }}">
                    Make Reservation
                </button>
            </div>
        </div>
    </div>
    @endforeach
</div>

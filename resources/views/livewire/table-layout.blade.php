<div class="table-layout" wire:poll.10s="loadTables">
    @foreach($tables as $table)
    <div class="table-link" data-table-id="{{ $table->id }}" data-table-number="{{ $table->table_number }}">
        <div class="table 
            {{ $table->is_occupied ? 'occupied' : ($table->just_reserved ? 'reserved' : 'available') }}">
            
            <div class="table-number text-center">Table {{ $table->table_number }}</div>

            @if($table->is_occupied)
                <div class="text-center">OCCUPIED</div>
            @endif

            <div class="inline-options text-center"
                style="display:none; flex-direction: column; align-items: center; gap: 5px; margin-top: 10px;">
                
                <button
                    class="make-walkin-btn bg-blue-800 text-white border-none px-3 py-2 sm:px-4 sm:py-2.5 my-[3px] rounded cursor-pointer text-sm sm:text-base md:text-[17px] hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed w-full max-w-[200px] sm:max-w-[220px]"
                    data-table-id="{{ $table->id }}"
                    @if($table->is_occupied || $table->just_reserved) hidden @endif>
                    Walk-in Order
                </button>

                <button
                    class="make-reservation-btn bg-blue-800 text-white border-none px-3 py-2 sm:px-4 sm:py-2.5 my-[3px] rounded cursor-pointer text-sm sm:text-base md:text-[17px] hover:bg-blue-700 w-full max-w-[200px] sm:max-w-[220px]"
                    data-table-id="{{ $table->id }}"
                    @if($table->is_occupied) @endif>
                    Make Reservation
                </button>
            </div>
        </div>
    </div>
    @endforeach
</div>
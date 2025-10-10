<div wire:poll.10s="loadData">
    <div id="dineInContent" class="flex justify-center m-5">
        <div class="table-layout grid lg:grid-cols-5 gap-10 justify-center">
            @foreach($tables as $table)
                @php
                    $isOccupied = in_array($table->table_number, $occupiedTables);
                    $reservationId = $table->current_reservation_id ?? $table->current_session_id ?? '';
                @endphp
                <div class="table-link cursor-pointer" data-reservation-id="{{ $reservationId }}"
                    data-table-number="{{ $table->table_number }}" data-table-capacity="{{ $table->capacity }}"
                    data-occupied="{{ $isOccupied ? '1' : '0' }}">
                    <div class="flex justify-center">
                        <div
                            class="relative h-40 w-48 bg-white rounded-3xl shadow-md flex items-center justify-center {{ $isOccupied ? 'table-occupied' : 'table-available' }}">
                            <div class="absolute mt-2 -top-1 px-3 bg-gray-200 text-black text-xs rounded-full shadow">
                                {{ $table->capacity }} Pax
                            </div>

                            <div class="flex flex-col items-center mt-6">
                                <div
                                    class="w-20 h-20 rounded-full {{ $isOccupied ? 'bg-red-600' : 'bg-green-600' }} text-white flex items-center justify-center shadow">
                                    <span class="text-lg font-semibold">T-{{ $table->table_number }}</span>
                                </div>

                                @if($isOccupied)
                                    @if(isset($table->remaining_seconds) && $table->remaining_seconds > 0)
                                        <span class="text-red-600 font-medium mt-2 flex items-center space-x-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="countdown" data-seconds="{{ $table->remaining_seconds }}">
                                                {{ sprintf(
                                                    '%02d:%02d:%02d',
                                                    floor($table->remaining_seconds / 3600),
                                                    floor(($table->remaining_seconds % 3600) / 60),
                                                    $table->remaining_seconds % 60
                                                ) }}
                                            </span>
                                        </span>
                                    @elseif(isset($table->elapsed_seconds))
                                        <span class="text-red-600 font-medium mt-2 flex items-center space-x-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="countup" data-seconds="{{ $table->elapsed_seconds }}">
                                                {{ sprintf(
                                                    '%02d:%02d:%02d',
                                                    floor($table->elapsed_seconds / 3600),
                                                    floor(($table->elapsed_seconds % 3600) / 60),
                                                    $table->elapsed_seconds % 60
                                                ) }}
                                            </span>
                                        </span>
                                    @else
                                        <span class="text-red-600 font-medium mt-2">Occupied</span>
                                    @endif
                                @else
                                    <span class="text-green-600 font-medium mt-2">Available</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
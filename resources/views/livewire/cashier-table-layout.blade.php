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
                        <div class="relative h-40 w-48 bg-white rounded-3xl shadow-md flex items-center justify-center {{ $isOccupied ? 'table-occupied' : 'table-available' }}">
                           
                            <div class="flex flex-col items-center mt-6">
                                <div class="w-20 h-20 rounded-full {{ $isOccupied ? 'bg-red-600 pulse-red' : 'bg-green-600' }} text-white flex items-center justify-center shadow">
                                    <span class="text-lg font-semibold">T-{{ $table->table_number }}</span>
                                </div>

                                @if($isOccupied)
                                    @if(isset($table->is_expired) && $table->is_expired)
                                        <span class="text-red-600 font-bold mt-2 animate-pulse">
                                            00:00:00    
                                            @if(isset($table->days_overdue) && $table->days_overdue > 0)
                                                <span class="block text-xs">({{ $table->days_overdue }} day(s) overdue)</span>
                                            @endif
                                        </span>
                                    @elseif(isset($table->is_upcoming) && $table->is_upcoming)
                                        <span class="text-orange-600 font-medium mt-2">Reserved</span>
                                    @elseif(isset($table->remaining_seconds) && $table->remaining_seconds > 0)
                                        <span class="text-red-600 font-medium mt-2 flex items-center space-x-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="countdown font-semibold" data-seconds="{{ $table->remaining_seconds }}">
                                                {{ sprintf('%02d:%02d:%02d', floor($table->remaining_seconds / 3600), floor(($table->remaining_seconds % 3600) / 60), $table->remaining_seconds % 60) }}
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

    <style>
        .table-occupied {
            border: 2px solid #DC2626;
        }
        .table-available {
            border: 2px solid #16A34A;
        }

        @keyframes pulse-red {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(220, 38, 38, 0);
            }
        }

        .pulse-red {
            animation: pulse-red 8s ease-in-out infinite;
        }
    </style>

    <script>
        function initializeCountdowns() {
            if (window.timers) {
                window.timers.forEach(t => clearInterval(t));
            }
            window.timers = [];

            document.querySelectorAll('.countdown').forEach(el => {
                let sec = parseInt(el.dataset.seconds) || 0;

                // If seconds is already 0 or negative, don't start countdown
                if (sec <= 0) {
                    el.textContent = "00:00:00";
                    const parent = el.closest('.flex.items-center');
                    if (parent) {
                        parent.innerHTML = '<span class="text-red-600 font-bold animate-pulse">TIME\'S UP!</span>';
                    }
                    return;
                }

                const timer = setInterval(() => {
                    if (sec <= 0) {
                        el.textContent = "00:00:00";
                        const parent = el.closest('.flex.items-center');
                        if (parent) {
                            parent.innerHTML = '<span class="text-red-600 font-bold animate-pulse">TIME\'S UP!</span>';
                        }
                        clearInterval(timer);
                        return;
                    }

                    const h = Math.floor(sec / 3600);
                    const m = Math.floor((sec % 3600) / 60);
                    const s = sec % 60;

                    el.textContent = `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
                    sec--;
                }, 1000);

                window.timers.push(timer);
            });
        }

        document.addEventListener('DOMContentLoaded', initializeCountdowns);
        document.addEventListener('livewire:load', () => {
            initializeCountdowns();
            
            Livewire.hook('message.processed', () => {
                initializeCountdowns();
            });
        });
    </script>
</div>
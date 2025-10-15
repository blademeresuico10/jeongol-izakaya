<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\table;
use Carbon\Carbon;

class TableLayout extends Component
{
    public $tables;

    public function mount()
    {
        $this->loadTables();
    }

    public function loadTables()
    {
        $currentTime = Carbon::now();
        $twoHoursLater = $currentTime->copy()->addHours(2);

        $this->tables = table::with([
            'reservation' => function($query) {
                $query->whereDoesntHave('transactions'); // Exclude paid reservations
            },
            'walkin' => function($query) {
                $query->whereDoesntHave('transactions'); // Exclude paid walk-ins
            }
        ])->get()->map(function ($table) use ($currentTime, $twoHoursLater) {
            // Active: session has started and not ended
            $activeReservation = $table->reservation
                ->where('status', 'Active')
                ->where('started_at', '<=', $currentTime)
                ->where('ended_at', '>=', $currentTime)
                ->first();

            $activeWalkin = $table->walkin
                ->where('status', 'Active')
                ->where('started_at', '<=', $currentTime)
                ->where('ended_at', '>=', $currentTime)
                ->first();

            $upcomingReservation = $table->reservation
                ->where('status', 'Active')
                ->where('started_at', '>', $currentTime) 
                ->where('started_at', '<=', $twoHoursLater) 
                ->first();

            $table->is_occupied = (bool)($activeReservation || $activeWalkin);

            return $table;
        });
    }

    public function render()
    {
        return view('livewire.table-layout', [
            'tables' => $this->tables
        ]);
    }
}
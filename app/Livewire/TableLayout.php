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

        $this->tables = table::with(['reservation', 'walkin'])->get()->map(function ($table) use ($currentTime, $twoHoursLater) {
            // Active (currently occupied)
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

            // Reservation starting soon (within 2 hours)
            $upcomingReservation = $table->reservation
                ->where('status', 'Active')
                ->whereBetween('started_at', [$currentTime, $twoHoursLater])
                ->first();

            $table->is_occupied = $activeReservation || $activeWalkin;
            $table->just_reserved = !$table->is_occupied && $upcomingReservation ? true : false;

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

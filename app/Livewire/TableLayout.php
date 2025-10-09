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

        $this->tables = table::with(['reservation', 'walkin'])
            ->get()
            ->map(function ($table) use ($currentTime) {
                $table->is_occupied = $table->reservation->isNotEmpty() || $table->walkin->isNotEmpty();
                return $table;
            });
    }

    public function render()
    {
        return view('livewire.table-layout');
    }
}

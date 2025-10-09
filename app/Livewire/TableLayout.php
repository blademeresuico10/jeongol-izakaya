<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Table;
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

        $this->tables = table::with([
            'reservation' => function ($query) use ($currentTime) {
                $query->where('status', 'Active')
                      ->where('started_at', '<=', $currentTime)
                      ->where('ended_at', '>=', $currentTime);
            },
            'walkin' => function ($query) use ($currentTime) {
                $query->where('status', 'Active')
                      ->where('started_at', '<=', $currentTime)
                      ->where('ended_at', '>=', $currentTime);
            },
        ])->get()->map(function ($table) {
            $table->is_occupied = $table->reservation->isNotEmpty() || $table->walkin->isNotEmpty();
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

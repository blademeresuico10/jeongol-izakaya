<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\table;
use App\Models\reservation;
use App\Models\walkin;
use Carbon\Carbon;

class CashierTableLayout extends Component
{
    public $tables;
    public $occupiedTables = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $now = Carbon::now();
        $today = Carbon::today();

        // Fetch active reservations (started and not ended)
        $activeReservations = reservation::with('table')
            ->where('status', 'Active')
            ->whereDate('started_at', $today)
            ->where('started_at', '<=', $now)
            ->where('ended_at', '>=', $now)
            ->whereDoesntHave('transactions')
            ->get();

        // Fetch active walk-ins (started and not ended)
        $activeWalkins = walkin::with('table')
            ->where('status', 'Active')
            ->whereDate('started_at', $today)
            ->where('started_at', '<=', $now)
            ->where('ended_at', '>=', $now)
            ->whereDoesntHave('transactions')
            ->get();

        // Load all tables and map their status
        $this->tables = table::all()->map(function ($table) use ($activeReservations, $activeWalkins, $now) {
            $res = $activeReservations->firstWhere('table_id', $table->id);
            $session = $activeWalkins->firstWhere('table_id', $table->id);

            if ($res) {
                $table->current_reservation_id = $res->id;
                $table->current_session_id = null;
                $table->is_walk_in = false;
                
                $endTime = Carbon::parse($res->ended_at);
                $table->remaining_seconds = max(0, $now->diffInSeconds($endTime, false));
                $table->is_expired = false;
                $table->is_occupied = true;
                
            } elseif ($session) {
                $table->current_reservation_id = null;
                $table->current_session_id = $session->id;
                $table->is_walk_in = true;
                
                $endTime = Carbon::parse($session->ended_at);
                $table->remaining_seconds = max(0, $now->diffInSeconds($endTime, false));
                $table->is_expired = false;
                $table->is_occupied = true;
                
            } else {
                $table->current_reservation_id = null;
                $table->current_session_id = null;
                $table->is_walk_in = false;
                $table->remaining_seconds = null;
                $table->is_expired = false;
                $table->is_occupied = false;
            }

            return $table;
        });

        // Build occupied tables array for easy checking
        $this->occupiedTables = $this->tables
            ->filter(fn($t) => $t->is_occupied)
            ->pluck('table_number')
            ->toArray();
    }

    public function render()
    {
        return view('livewire.cashier-table-layout');
    }
}
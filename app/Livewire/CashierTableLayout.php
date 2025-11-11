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

        $activeReservations = reservation::with('table')
            ->where('status', 'Active')
            ->whereDoesntHave('transactions')
            ->where('started_at', '<=', $now)
            ->where('ended_at', '>=', $now)
            ->orderBy('started_at', 'asc')
            ->get();

        $activeWalkins = walkin::with('table')
            ->where('status', 'Active')
            ->whereDoesntHave('transactions')
            ->where('started_at', '<=', $now)
            ->where('ended_at', '>=', $now)
            ->orderBy('started_at', 'asc')
            ->get();

        $this->tables = table::all()->map(function ($table) use ($activeReservations, $activeWalkins, $now) {
            $res = $activeReservations->firstWhere('table_id', $table->id);
            $session = $activeWalkins->firstWhere('table_id', $table->id);

            if ($res) {
                $endTime = Carbon::parse($res->ended_at);
                $secondsRemaining = $now->diffInSeconds($endTime, false);

                $table->current_reservation_id = $res->id;
                $table->current_session_id = null;
                $table->is_walk_in = false;
                $table->remaining_seconds = $secondsRemaining;
                $table->is_expired = $secondsRemaining <= 0;
                $table->is_upcoming = false; 
                $table->days_overdue = $table->is_expired ? $now->diffInDays($endTime) : 0;
                $table->is_occupied = true;
            } elseif ($session) {
                $endTime = Carbon::parse($session->ended_at);
                $secondsRemaining = $now->diffInSeconds($endTime, false);

                $table->current_reservation_id = null;
                $table->current_session_id = $session->id;
                $table->is_walk_in = true;
                $table->remaining_seconds = $secondsRemaining;
                $table->is_expired = $secondsRemaining <= 0;
                $table->is_upcoming = false; 
                $table->days_overdue = $table->is_expired ? $now->diffInDays($endTime) : 0;
                $table->is_occupied = true;
            } else {
                $table->current_reservation_id = null;
                $table->current_session_id = null;
                $table->is_walk_in = false;
                $table->remaining_seconds = null;
                $table->is_expired = false;
                $table->is_upcoming = false;
                $table->days_overdue = 0;
                $table->is_occupied = false;
            }

            return $table;
        });

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

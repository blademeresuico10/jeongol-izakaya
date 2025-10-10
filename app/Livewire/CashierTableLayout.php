<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\table;
use App\Models\reservation;
use App\Models\walkin;
use App\Models\orders;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CashierTableLayout extends Component
{
    public $tables;
    public $menuItems;
    public $reservations;
    public $walkin;
    public $groupedMenu;
    public $occupiedTables = [];
    public $menuData = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $now = Carbon::now();

        $this->tables = DB::table('tables')->get();
        $this->menuItems = DB::table('menu')->get();

        $this->reservations = reservation::with('table')
            ->where('status', 'Active')
            ->where('started_at', '<=', $now)
            ->where('ended_at', '>', $now)
            ->whereDoesntHave('transactions')
            ->get();

        $this->walkin = walkin::with('table')
            ->where('status', 'Active')
            ->where('started_at', '<=', $now)
            ->where('ended_at', '>', $now)
            ->get();

        $reservationIds = $this->reservations->pluck('id')->toArray();
        $walkinIds = $this->walkin->pluck('id')->toArray();

        $orders = orders::with('menu')
            ->where(function ($query) use ($reservationIds, $walkinIds) {
                $query->whereIn('reservation_id', $reservationIds)
                    ->orWhereIn('walk_in_id', $walkinIds);
            })
            ->get()
            ->groupBy(function ($order) {
                return $order->reservation_id ?? ('walkin_' . $order->walk_in_id);
            });

        $this->occupiedTables = [];
        foreach ($this->tables as $table) {
            $res = $this->reservations->firstWhere('table_id', $table->id);
            $session = $this->walkin->firstWhere('table_id', $table->id);

            if ($res) {
                $table->current_reservation_id = $res->id;
                $table->current_session_id = null;
                $table->is_walk_in = false;

                $endTime = Carbon::parse($res->ended_at);
                $secondsRemaining = $now->diffInSeconds($endTime);
                $table->remaining_seconds = min(7200, max(0, $secondsRemaining));

                $table->current_orders = $orders[$res->id] ?? [];
                $this->occupiedTables[] = $res->table->table_number ?? $table->table_number;
            } elseif ($session) {
                $table->current_reservation_id = null;
                $table->current_session_id = $session->id;
                $table->is_walk_in = true;

                $endTime = Carbon::parse($session->ended_at);
                $secondsRemaining = $now->diffInSeconds($endTime);
                $table->elapsed_seconds = min(7200, $secondsRemaining);

                $table->current_orders = $orders[$session->id] ?? [];
                $this->occupiedTables[] = $session->table->table_number ?? $table->table_number;
            } else {
                $table->current_reservation_id = null;
                $table->current_session_id = null;
                $table->is_walk_in = false;
                $table->remaining_seconds = null;
                $table->elapsed_seconds = null;
                $table->current_orders = [];
            }
        }

        $menuDiscounts = DB::table('menu_discounts')->get()->groupBy('menu_id');
        $this->menuData = [];

        foreach ($this->menuItems as $item) {
            $discounts = $menuDiscounts[$item->id] ?? collect();

            $studentDisc = $discounts->firstWhere('discount_type', 'Student');
            $govtDisc    = $discounts->firstWhere('discount_type', 'Government Employee');
            $seniorDisc  = $discounts->firstWhere('discount_type', 'Senior Citizen');
            $pwdDisc     = $discounts->firstWhere('discount_type', 'PWD');

            $this->menuData[] = [
                'menu_item'       => $item->menu_item,
                'regular_price'   => (float) $item->regular_price,
                'student_percent' => $studentDisc->discount_percentage ?? null,
                'govt_percent'    => $govtDisc->discount_percentage ?? null,
                'senior_percent'  => $seniorDisc->discount_percentage ?? null,
                'pwd_percent'     => $pwdDisc->discount_percentage ?? null,
                'has_discount'    => $item->has_customer_discount,
            ];
        }

        $this->groupedMenu = [];
        foreach ($this->menuItems as $item) {
            $this->groupedMenu[$item->category][] = $item;
        }
    }

    public function render()
    {
        return view('livewire.cashier-table-layout');
    }
}
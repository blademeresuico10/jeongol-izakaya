<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\orders;
use App\Models\Reservation;

class OrdersTable extends Component
{
    public $orders;

    protected $listeners = ['refreshOrders' => '$refresh'];

    public function mount()
    {
        $this->loadOrders();
    }

    public function loadOrders()
    {
        $this->orders = Orders::with(['menu', 'reservation.table'])
            ->latest()
            ->take(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.orders-table');
    }
}

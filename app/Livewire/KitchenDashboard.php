<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\orders;
use App\Models\ingredients;
use App\Models\table;
use App\Models\menu;
use Illuminate\Support\Facades\Auth;

class KitchenDashboard extends Component
{
    public $activeTab = 'pending';
    public $selectedTableUnlimited;
    public $selectedTableOrder;
    public $selectedMenuId;
    public $orderQuantity = 1;
    public $selectedIngredients = [];
    
    protected $listeners = ['refreshDashboard' => '$refresh'];

    public function mount()
    {
        // Initialize component
    }

    public function markAsServed($orderId)
    {
        $order = orders::findOrFail($orderId);
        
        // Get all orders in the same group
        if ($order->reservation_id) {
            $groupOrders = orders::where('reservation_id', $order->reservation_id)
                ->where('status', 'Pending')
                ->get();
        } else {
            $groupOrders = orders::where('walk_in_id', $order->walk_in_id)
                ->where('status', 'Pending')
                ->get();
        }

        foreach ($groupOrders as $groupOrder) {
            $groupOrder->update(['status' => 'Served']);
        }

        session()->flash('success', 'Order marked as served successfully!');
        $this->dispatch('refreshDashboard');
    }

    public function addUnlimitedRefill()
    {
        $this->validate([
            'selectedTableUnlimited' => 'required|exists:tables,id',
            'selectedIngredients' => 'required|array|min:1',
        ], [
            'selectedTableUnlimited.required' => 'Please select a table.',
            'selectedIngredients.required' => 'Please select at least one ingredient.',
        ]);

        // Process the refill logic here
        // Add your business logic for unlimited refills

        session()->flash('success', 'Refills added successfully!');
        $this->reset(['selectedTableUnlimited', 'selectedIngredients']);
        $this->dispatch('refreshDashboard');
    }

    public function addAdditionalOrder()
    {
        $this->validate([
            'selectedTableOrder' => 'required|exists:tables,id',
            'selectedMenuId' => 'required|exists:menus,id',
            'orderQuantity' => 'required|integer|min:1',
        ]);

        $table = table::findOrFail($this->selectedTableOrder);
        
        // Determine if it's a reservation or walk-in
        $reservationId = $table->reservation()->where('status', 'active')->first()?->id;
        $walkinId = $table->walkin()->where('status', 'active')->first()?->id;

        orders::create([
            'table_id' => $this->selectedTableOrder,
            'menu_id' => $this->selectedMenuId,
            'quantity' => $this->orderQuantity,
            'reservation_id' => $reservationId,
            'walk_in_id' => $walkinId,
            'status' => 'Pending',
        ]);

        session()->flash('success', 'Additional order added successfully!');
        $this->reset(['selectedTableOrder', 'selectedMenuId', 'orderQuantity']);
        $this->orderQuantity = 1;
        $this->dispatch('refreshDashboard');
    }

    public function render()
    {
        $pendingOrders = orders::with(['table', 'menu', 'reservation', 'walkin'])
            ->whereNotNull('status')
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy(function ($order) {
                if ($order->reservation_id) {
                    return 'reservation_' . $order->reservation_id;
                }
                return 'walkin_' . $order->walk_in_id;
            })
            ->filter(function ($orderGroup) {
                $order = $orderGroup->first();
                $status = $order->reservation->status
                    ?? $order->walkin->status
                    ?? $order->status;
                return $status !== 'Completed';
            });

        $tables = table::whereHas('reservation', function ($query) {
            $query->whereRaw('LOWER(status) = ?', ['active'])
                ->whereHas('orders');
        })
            ->orWhereHas('walkin', function ($query) {
                $query->whereRaw('LOWER(status) = ?', ['active'])
                    ->whereHas('orders');
            })
            ->with(['reservation' => function ($query) {
                $query->whereRaw('LOWER(status) = ?', ['active']);
            }, 'walkin' => function ($query) {
                $query->whereRaw('LOWER(status) = ?', ['active']);
            }])
            ->get();

        $unlimitedMenuIds = [1, 2, 3];
        $unlimitedIngredients = ingredients::whereHas('menuIngredients', function ($query) use ($unlimitedMenuIds) {
            $query->whereIn('menu_id', $unlimitedMenuIds);
        })->get();

        $aLaCarteMenus = menu::whereNotIn('id', $unlimitedMenuIds)->get();

        return view('livewire.kitchen-dashboard', [
            'pendingOrders' => $pendingOrders,
            'tables' => $tables,
            'unlimitedIngredients' => $unlimitedIngredients,
            'aLaCarteMenus' => $aLaCarteMenus,
        ]);
    }
}
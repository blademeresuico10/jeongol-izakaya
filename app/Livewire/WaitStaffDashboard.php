<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\orders;
use App\Models\OrderRefill;
use App\Models\table;
use App\Models\Menu;
use App\Models\MenuIngredient;
use App\Models\ingredients;
use App\Models\reservation;
use App\Models\walkin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\RefillConfiguration;

class WaitStaffDashboard extends Component
{
    public $tables = [];
    public $activeTable = null;
    public $menuItems = [];
    public $orders = [];
    public $refills = [];
    public $tableNote = '';
    public $availableRefills = [];
    public $hasUnlimitedPackage = false;

    public function mount()
    {
        $this->loadTables();
        $this->loadMenuItems();
    }

    public function loadTables()
    {
        $now = now();

        $this->tables = Table::with(['reservation' => function ($query) use ($now) {
            $query->where('status', 'Active')
                ->whereDate('started_at', $now->toDateString())
                ->where('started_at', '<=', $now)
                ->where(function ($q) use ($now) {
                    $q->where('ended_at', '>=', $now)
                        ->orWhereNull('ended_at');
                });
        }, 'walkin' => function ($query) use ($now) {
            $query->where('status', 'Active')
                ->whereDate('started_at', $now->toDateString())
                ->where('started_at', '<=', $now)
                ->where('ended_at', '>=', $now);
        }])
            ->orderBy('table_number')
            ->get()
            ->map(function ($table) {
                $hasActiveSession = $table->reservation->isNotEmpty() || $table->walkin->isNotEmpty();

                return [
                    'id' => $table->id,
                    'number' => $table->table_number,
                    'capacity' => $table->capacity,
                    'hasOrders' => $this->tableHasOrders($table->id),
                    'hasPending' => $this->tableHasPendingOrders($table->id),
                    'hasActiveSession' => $hasActiveSession,
                    'hasReadyItems' => $this->tableHasReadyItems($table->id),
                ];
            });
    }

    public function loadMenuItems()
    {
        $menus = Menu::where('status', 'Active')
            ->get();

        $categoryLabels = [
            'main' => 'Main Dishes',
            'add_ons' => 'Add-Ons',
        ];

        $this->menuItems = $menus->groupBy('category')->mapWithKeys(function ($items, $key) use ($categoryLabels) {
            $displayName = $categoryLabels[$key] ?? ucfirst($key);

            return [
                $displayName => $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'menu_item' => $item->menu_item,
                        'regular_price' => $item->regular_price,
                        'isUnlimited' => in_array($item->id, [1, 2, 3])
                    ];
                })->values()
            ];
        });
    }

    public function loadAvailableRefills($tableId)
    {
        $table = table::find($tableId);

        $reservation = $table->reservation()
            ->where('status', 'Active')
            ->whereDate('started_at', '<=', today())
            ->where(function ($q) {
                $q->whereDate('ended_at', '>=', today())
                    ->orWhereNull('ended_at');
            })
            ->first();

        $walkin = $table->walkin()
            ->where('status', 'Active')
            ->whereDate('started_at', today())
            ->first();

        if (!$reservation && !$walkin) {
            $this->availableRefills = [];
            return;
        }

        $unlimitedOrders = orders::where(function ($query) use ($reservation, $walkin) {
            $query->where('reservation_id', $reservation?->id)
                ->orWhere('walk_in_id', $walkin?->id);
        })
            ->whereIn('menu_id', [1, 2, 3])
            ->whereIn('status', ['Pending', 'Ready', 'Served'])
            ->pluck('menu_id')
            ->unique();

        if ($unlimitedOrders->isEmpty()) {
            $this->availableRefills = [];
            $this->hasUnlimitedPackage = false;
            return;
        }

        $this->hasUnlimitedPackage = true;

        $ingredientData = MenuIngredient::whereIn('menu_id', $unlimitedOrders)
            ->with('ingredient')
            ->get()
            ->groupBy('ingredient_id')
            ->map(function ($group) {
                $ingredient = $group->first()->ingredient;
                $config = RefillConfiguration::where('ingredient_id', $ingredient->id)->first();

                return [
                    'id' => $ingredient->id,
                    'name' => $ingredient->name,
                    'category' => $ingredient->category,
                    'quantity_per_plate' => $config ? $config->quantity_per_plate : 0,
                    'unit' => $ingredient->unit
                ];
            })
            ->values();

        $this->availableRefills = $ingredientData->toArray();
    }

    public function loadOrders($tableId)
    {
        $this->activeTable = $tableId;

        $table = table::find($tableId);

        $reservation = $table->reservation()
            ->where('status', 'Active')
            ->whereDate('started_at', '<=', today())
            ->where(function ($q) {
                $q->whereDate('ended_at', '>=', today())
                    ->orWhereNull('ended_at');
            })
            ->first();

        $walkin = $table->walkin()
            ->where('status', 'Active')
            ->whereDate('started_at', today())
            ->first();

        $query = orders::with('menu');

        if ($reservation) {
            $query->where('reservation_id', $reservation->id);
        } elseif ($walkin) {
            $query->where('walk_in_id', $walkin->id);
        } else {
            $this->orders = [];
            $this->refills = [];
            $this->tableNote = '';
            $this->hasUnlimitedPackage = false;
            $this->availableRefills = [];
            $this->dispatch('ordersUpdated', [
                'orders' => $this->orders,
                'refills' => $this->refills,
                'tableNote' => $this->tableNote,
                'availableRefills' => $this->availableRefills,
                'hasUnlimitedPackage' => $this->hasUnlimitedPackage,
            ]);
            return;
        }

        $tableNoteOrder = orders::query()
            ->when($reservation, fn($q) => $q->where('reservation_id', $reservation->id))
            ->when($walkin, fn($q) => $q->where('walk_in_id', $walkin->id))
            ->whereNull('menu_id')
            ->whereDate('created_at', today())
            ->first();

        $this->tableNote = $tableNoteOrder?->notes ?? '';

        $ordersList = $query->whereNotNull('menu_id')
            ->whereDate('created_at', today())
            ->get();

        $this->orders = $ordersList->map(function ($order) use ($tableId) {
            return [
                'id' => $order->id,
                'item' => $order->menu->menu_item,
                'menu_id' => $order->menu_id,
                'quantity' => $order->quantity,
                'price' => $order->price,
                'status' => $order->status,
                'created_at' => $order->created_at->format('h:i A'),
                'isUnlimited' => in_array($order->menu_id, [1, 2, 3]),
                'table_id' => $tableId
            ];
        })
            ->toArray();

        $orderIds = $ordersList->pluck('id')->toArray();

        $refillsList = OrderRefill::whereIn('order_id', $orderIds)
            ->whereDate('created_at', today())
            ->with(['ingredient', 'order.menu'])
            ->get();

        $this->refills = $refillsList->map(function ($refill) use ($tableId) {
            return [
                'id' => $refill->id,
                'order_id' => $refill->order_id,
                'ingredient_name' => $refill->ingredient->name ?? 'Unknown',
                'quantity' => $refill->quantity,
                'status' => $refill->status,
                'created_at' => $refill->created_at->format('h:i A'),
                'table_id' => $tableId
            ];
        })
            ->toArray();

        $this->loadAvailableRefills($tableId);

        $this->dispatch('ordersUpdated', [
            'orders' => $this->orders,
            'refills' => $this->refills,
            'tableNote' => $this->tableNote,
            'availableRefills' => $this->availableRefills,
            'hasUnlimitedPackage' => $this->hasUnlimitedPackage,
        ]);
    }

    public function addRefill($tableId, $selectedItems)
    {
        $table = table::find($tableId);

        $reservation = $table->reservation()
            ->where('status', 'Active')
            ->whereDate('started_at', '<=', today())
            ->where(function ($q) {
                $q->whereDate('ended_at', '>=', today())
                    ->orWhereNull('ended_at');
            })
            ->first();

        $walkin = $table->walkin()
            ->where('status', 'Active')
            ->whereDate('started_at', today())
            ->first();

        if (!$reservation && !$walkin) {
            $this->dispatch('error', 'No active session for this table');
            return;
        }

        $unlimitedOrder = orders::where(function ($query) use ($reservation, $walkin) {
            if ($reservation) {
                $query->where('reservation_id', $reservation->id);
            } elseif ($walkin) {
                $query->where('walk_in_id', $walkin->id);
            }
        })
            ->whereIn('menu_id', [1, 2, 3])
            ->whereIn('status', ['Pending', 'Ready', 'Served'])
            ->first();

        if (!$unlimitedOrder) {
            $this->dispatch('error', 'This table does not have an unlimited package!');
            return;
        }

        if (empty($selectedItems)) {
            $this->dispatch('error', 'Please select at least one item!');
            return;
        }

        $refillCount = 0;

        DB::transaction(function () use ($selectedItems, $unlimitedOrder, &$refillCount) {
            foreach ($selectedItems as $item) {
                if (!isset($item['selected']) || !$item['selected'] || !isset($item['plates'])) {
                    continue;
                }

                $ingredient = ingredients::findOrFail($item['ingredient_id']);
                $plates = (int) $item['plates'];

                $refillCount++;

                OrderRefill::create([
                    'order_id' => $unlimitedOrder->id,
                    'ingredient_id' => $ingredient->id,
                    'quantity' => $plates,
                    'status' => 'Pending',
                ]);
            }
        });

        if ($refillCount === 0) {
            $this->dispatch('error', 'Please select at least one item!');
            return;
        }

        $this->loadOrders($tableId);
        $this->loadTables();
        $this->dispatch('success', "Added {$refillCount} refill item(s) successfully!");
    }

    public function updateOrderQuantity($orderId, $newQuantity)
    {
        $order = orders::find($orderId);

        if ($order && $order->status === 'Pending') {
            $menu = Menu::find($order->menu_id);

            if ($menu) {
                $order->quantity = $newQuantity;
                $order->price = ($menu->regular_price ?? 0) * $newQuantity;
                $order->save();
                $this->loadOrders($this->activeTable);
            }
        }
    }

    public function removeOrder($orderId)
    {
        $order = orders::find($orderId);

        if ($order && $order->status === 'Pending') {
            $order->delete();
            $this->loadOrders($this->activeTable);
            $this->loadTables();
        }
    }

    public function removeRefill($refillId)
    {
        $refill = OrderRefill::find($refillId);

        if ($refill && $refill->status === 'Pending') {
            $refill->delete();
            $this->loadOrders($this->activeTable);
            $this->loadTables();
        }
    }

    public function serveAllReady($tableId)
    {
        $table = table::find($tableId);

        $reservation = $table->reservation()
            ->where('status', 'Active')
            ->whereDate('started_at', '<=', today())
            ->where(function ($q) {
                $q->whereDate('ended_at', '>=', today())
                    ->orWhereNull('ended_at');
            })
            ->first();

        $walkin = $table->walkin()
            ->where('status', 'Active')
            ->whereDate('started_at', today())
            ->first();

        if (!$reservation && !$walkin) {
            session()->flash('error', 'No active session for this table');
            return;
        }

        DB::beginTransaction();
        try {
            // Serve all ready orders
            $readyOrders = orders::query()
                ->when($reservation, fn($q) => $q->where('reservation_id', $reservation->id))
                ->when($walkin, fn($q) => $q->where('walk_in_id', $walkin->id))
                ->where('status', 'Ready')
                ->whereNotNull('menu_id')
                ->whereDate('created_at', today())
                ->get();

            foreach ($readyOrders as $order) {
                $order->status = 'Served';
                $order->save();
            }

            // Serve all ready refills
            $orderIds = orders::query()
                ->when($reservation, fn($q) => $q->where('reservation_id', $reservation->id))
                ->when($walkin, fn($q) => $q->where('walk_in_id', $walkin->id))
                ->pluck('id')
                ->toArray();

            $readyRefills = OrderRefill::whereIn('order_id', $orderIds)
                ->where('status', 'Ready')
                ->whereDate('created_at', today())
                ->get();

            foreach ($readyRefills as $refill) {
                $refill->status = 'Served';
                $refill->save();
            }

            $totalServed = $readyOrders->count() + $readyRefills->count();

            DB::commit();

            if ($totalServed > 0) {
                session()->flash('success', "Served {$totalServed} item(s) successfully!");
            } else {
                session()->flash('error', 'No ready items to serve');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to serve items: ' . $e->getMessage());
        }

        $this->loadOrders($tableId);
        $this->loadTables();
    }
    public function serveRefill($refillId)
    {
        $refill = OrderRefill::find($refillId);

        if (!$refill || $refill->status !== 'Ready') {
            session()->flash('error', 'Refill is not ready to be served!');
            return;
        }

        $refill->status = 'Served';
        $refill->save();

        $this->loadOrders($this->activeTable);
        $this->loadTables();
        session()->flash('success', 'Refill marked as served!');
    }

    private function tableHasOrders($tableId)
    {
        $table = table::find($tableId);

        $reservation = $table->reservation()
            ->where('status', 'Active')
            ->whereDate('started_at', '<=', today())
            ->where(function ($q) {
                $q->whereDate('ended_at', '>=', today())
                    ->orWhereNull('ended_at');
            })
            ->first();

        $walkin = $table->walkin()
            ->where('status', 'Active')
            ->whereDate('started_at', today())
            ->first();

        if ($reservation) {
            return orders::where('reservation_id', $reservation->id)
                ->whereNotNull('menu_id')
                ->whereDate('created_at', today())
                ->exists();
        }

        if ($walkin) {
            return orders::where('walk_in_id', $walkin->id)
                ->whereNotNull('menu_id')
                ->whereDate('created_at', today())
                ->exists();
        }

        return false;
    }

    private function tableHasPendingOrders($tableId)
    {
        $table = table::find($tableId);

        $reservation = $table->reservation()
            ->where('status', 'Active')
            ->whereDate('started_at', '<=', today())
            ->where(function ($q) {
                $q->whereDate('ended_at', '>=', today())
                    ->orWhereNull('ended_at');
            })
            ->first();

        $walkin = $table->walkin()
            ->where('status', 'Active')
            ->whereDate('started_at', today())
            ->first();

        $hasPendingOrders = false;

        if ($reservation) {
            $hasPendingOrders = orders::where('reservation_id', $reservation->id)
                ->whereNotNull('menu_id')
                ->where('status', 'Pending')
                ->whereDate('created_at', today())
                ->exists();

            if (!$hasPendingOrders) {
                $orderIds = orders::where('reservation_id', $reservation->id)->pluck('id');
                $hasPendingOrders = OrderRefill::whereIn('order_id', $orderIds)
                    ->where('status', 'Pending')
                    ->whereDate('created_at', today())
                    ->exists();
            }
        }

        if ($walkin && !$hasPendingOrders) {
            $hasPendingOrders = orders::where('walk_in_id', $walkin->id)
                ->whereNotNull('menu_id')
                ->where('status', 'Pending')
                ->whereDate('created_at', today())
                ->exists();

            if (!$hasPendingOrders) {
                $orderIds = orders::where('walk_in_id', $walkin->id)->pluck('id');
                $hasPendingOrders = OrderRefill::whereIn('order_id', $orderIds)
                    ->where('status', 'Pending')
                    ->whereDate('created_at', today())
                    ->exists();
            }
        }

        return $hasPendingOrders;
    }
    private function tableHasReadyItems($tableId)
    {
        $table = table::find($tableId);

        $reservation = $table->reservation()
            ->where('status', 'Active')
            ->whereDate('started_at', '<=', today())
            ->where(function ($q) {
                $q->whereDate('ended_at', '>=', today())
                    ->orWhereNull('ended_at');
            })
            ->first();

        $walkin = $table->walkin()
            ->where('status', 'Active')
            ->whereDate('started_at', today())
            ->first();

        $hasReadyItems = false;

        if ($reservation) {
            $hasReadyItems = orders::where('reservation_id', $reservation->id)
                ->whereNotNull('menu_id')
                ->where('status', 'Ready')
                ->whereDate('created_at', today())
                ->exists();

            if (!$hasReadyItems) {
                $orderIds = orders::where('reservation_id', $reservation->id)->pluck('id');
                $hasReadyItems = OrderRefill::whereIn('order_id', $orderIds)
                    ->where('status', 'Ready')
                    ->whereDate('created_at', today())
                    ->exists();
            }
        }

        if ($walkin && !$hasReadyItems) {
            $hasReadyItems = orders::where('walk_in_id', $walkin->id)
                ->whereNotNull('menu_id')
                ->where('status', 'Ready')
                ->whereDate('created_at', today())
                ->exists();

            if (!$hasReadyItems) {
                $orderIds = orders::where('walk_in_id', $walkin->id)->pluck('id');
                $hasReadyItems = OrderRefill::whereIn('order_id', $orderIds)
                    ->where('status', 'Ready')
                    ->whereDate('created_at', today())
                    ->exists();
            }
        }

        return $hasReadyItems;
    }

    public function addOrder($tableId, $menuId, $quantity = 1, $note = null)
    {
        $table = table::find($tableId);
        $menu = Menu::find($menuId);

        if (!$menu) {
            session()->flash('error', 'Menu item not found');
            return;
        }

        $reservation = $table->reservation()
            ->where('status', 'Active')
            ->whereDate('started_at', '<=', today())
            ->where(function ($q) {
                $q->whereDate('ended_at', '>=', today())
                    ->orWhereNull('ended_at');
            })
            ->first();

        $walkin = $table->walkin()
            ->where('status', 'Active')
            ->whereDate('started_at', today())
            ->first();

        if (!$reservation && !$walkin) {
            session()->flash('error', 'No active session for this table');
            return;
        }

        $pricePerItem = $menu->regular_price ?? $menu->price ?? 0;
        $totalPrice = $pricePerItem * $quantity;

        orders::create([
            'reservation_id' => $reservation?->id,
            'walk_in_id' => $walkin?->id,
            'menu_id' => $menuId,
            'quantity' => $quantity,
            'notes' => $note,
            'price' => $totalPrice,
            'status' => 'Pending'
        ]);

        $this->loadOrders($tableId);
        $this->loadTables();
        $this->dispatch('tablesUpdated', ['tables' => $this->tables]);
    }

    public function render()
    {
        return view('livewire.wait-staff-dashboard', [
            'tables' => $this->tables,
            'menuItems' => $this->menuItems,
            'orders' => $this->orders,
            'refills' => $this->refills,
            'tableNote' => $this->tableNote,
            'availableRefills' => $this->availableRefills,
            'hasUnlimitedPackage' => $this->hasUnlimitedPackage,
            'activeTable' => $this->activeTable,

        ]);
    }
}

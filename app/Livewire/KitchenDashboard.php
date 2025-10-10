<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\orders;
use App\Models\ingredients;
use App\Models\ingredientMovements;
use App\Models\MenuIngredient;
use App\Models\UnlimitedMeatLog;
use App\Models\table;
use App\Models\menu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KitchenDashboard extends Component
{
    public $activeTab = 'pending';
    public $selectedTableUnlimited;
    public $selectedTableOrder;
    public $selectedMenuId;
    public $orderQuantity = 1;
    public $selectedIngredients = [];

    protected $listeners = [
        'echo:kitchen,order.created' => 'refreshDashboard',
        'echo:kitchen,order.status.updated' => 'refreshDashboard',
    ];

    public function refreshDashboard()
    {
        $this->emit('$refresh');
    }


    public function handleOrderCreated($event)
    {
        $this->dispatch('notify', type: 'info', message: 'New order received!');
    }

    public function handleOrderStatusUpdated($event)
    {
        $this->dispatch('notify', type: 'success', message: 'Order status updated!');
    }

    public function mount() {}

    public function markAsServed($orderId)
    {
        $order = orders::findOrFail($orderId);

        if ($order->reservation_id) {
            $orders = orders::where('reservation_id', $order->reservation_id)
                ->where('status', 'pending')
                ->with('menu')
                ->get();
        } elseif ($order->walk_in_id) {
            $orders = orders::where('walk_in_id', $order->walk_in_id)
                ->where('status', 'pending')
                ->with('menu')
                ->get();
        } else {
            session()->flash('error', 'Order not found!');
            return;
        }

        if ($orders->isEmpty()) {
            session()->flash('error', 'No pending orders found!');
            return;
        }

        DB::beginTransaction();

        try {
            $mainOrders = $orders->filter(fn($o) => $o->menu && $o->menu->category === 'main');
            $addonOrders = $orders->filter(fn($o) => $o->menu && $o->menu->category === 'add_ons');

            $processedIngredients = [];

            $processIngredients = function ($singleOrder, $isAddon = false) use (&$processedIngredients) {
                $menuIngredients = MenuIngredient::where('menu_id', $singleOrder->menu_id)->get();

                foreach ($menuIngredients as $menuIngredient) {
                    $ingredient = ingredients::find($menuIngredient->ingredient_id);
                    if (!$ingredient) continue;

                    if (!$isAddon && in_array($ingredient->id, $processedIngredients)) continue;

                    $quantityNeeded = $isAddon
                        ? $menuIngredient->quantity * $singleOrder->quantity
                        : $menuIngredient->quantity;

                    $ingredient->deductStock(
                        $quantityNeeded,
                        $singleOrder->id,
                        Auth::id(),
                        ($isAddon ? "Add-on" : "Main") . ": " . ($isAddon ? "{$singleOrder->quantity} x " : "") . "{$singleOrder->menu->menu_item}"
                    );

                    if (!$isAddon) {
                        $processedIngredients[] = $ingredient->id;
                    }
                }

                $singleOrder->status = 'served';
                $singleOrder->save();
            };

            foreach ($mainOrders as $singleOrder) {
                $processIngredients($singleOrder);
            }

            foreach ($addonOrders as $singleOrder) {
                $processIngredients($singleOrder, true);
            }

            DB::commit();

            session()->flash('success', 'Order marked as served successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to process order: ' . $e->getMessage());
        }
    }

    public function addUnlimitedRefill()
    {
        if (!$this->selectedTableUnlimited) {
            session()->flash('error', 'Please select a table.');
            return;
        }

        $hasQuantity = false;
        $ingredientsToProcess = [];

        foreach ($this->selectedIngredients as $ingredientId => $data) {
            if (isset($data['quantity']) && !empty($data['quantity']) && $data['quantity'] > 0) {
                $hasQuantity = true;
                $ingredientsToProcess[$ingredientId] = $data['quantity'];
            }
        }

        if (!$hasQuantity) {
            session()->flash('error', 'Please enter quantity for at least one ingredient.');
            return;
        }

        $table = table::with(['reservation', 'walkin'])->findOrFail($this->selectedTableUnlimited);

        $reservationId = $table->reservation->first()?->id;
        $walkInId = $table->walkin->first()?->id;

        DB::beginTransaction();

        try {
            $refillCount = 0;

            foreach ($ingredientsToProcess as $ingredientId => $quantity) {
                $ingredient = ingredients::findOrFail($ingredientId);

                if ($ingredient->stocks < $quantity) {
                    $available = $ingredient->unit === 'kg'
                        ? number_format($ingredient->stocks / 1000, 2) . ' kg'
                        : $ingredient->stocks . ' pieces';

                    DB::rollBack();
                    session()->flash('error', "Insufficient {$ingredient->name}! Only {$available} available.");
                    return;
                }

                $stockBefore = $ingredient->stocks;
                $ingredient->decrement('stocks', $quantity);
                $ingredient->refresh();
                $stockAfter = $ingredient->stocks;

                ingredientMovements::create([
                    'ingredient_id' => $ingredient->id,
                    'user_id' => Auth::id(),
                    'type' => 'used',
                    'quantity' => $quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                ]);

                UnlimitedMeatLog::create([
                    'table_id' => $this->selectedTableUnlimited,
                    'ingredient_id' => $ingredientId,
                    'quantity' => $quantity,
                    'unit' => $ingredient->unit === 'kg' ? 'g' : 'pieces',
                    'reservation_id' => $reservationId,
                    'walk_in_id' => $walkInId,
                ]);

                $refillCount++;
            }

            DB::commit();

            $this->dispatch('notify', type: 'success', message: 'Stock updated.');
            $this->reset(['selectedTableUnlimited', 'selectedIngredients']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', type: 'error', message: 'Failed to add refills: ' . $e->getMessage());
        }
    }

    public function addAdditionalOrder()
    {
        $this->validate([
            'selectedTableOrder' => 'required|exists:tables,id',
            'selectedMenuId' => 'required|exists:menu,id',
            'orderQuantity' => 'required|integer|min:1',
        ], [
            'selectedTableOrder.required' => 'Please select a table.',
            'selectedMenuId.required' => 'Please select a menu item.',
            'orderQuantity.required' => 'Please enter quantity.',
            'orderQuantity.min' => 'Quantity must be at least 1.',
        ]);

        if (in_array($this->selectedMenuId, [1, 2, 3])) {
            session()->flash('error', 'Cannot add unlimited packages as additional orders!');
            return;
        }

        $menu = menu::findOrFail($this->selectedMenuId);
        $table = table::with(['reservation', 'walkin'])->findOrFail($this->selectedTableOrder);
        $reservationId = $table->reservation->first()?->id;
        $walkInId = $table->walkin->first()?->id;

        $menuIngredients = MenuIngredient::where('menu_id', $menu->id)->get();

        foreach ($menuIngredients as $menuIngredient) {
            $ingredient = ingredients::find($menuIngredient->ingredient_id);
            $quantityNeeded = $menuIngredient->quantity * $this->orderQuantity;

            if ($ingredient->stocks < $quantityNeeded) {
                $available = $ingredient->unit === 'kg'
                    ? number_format($ingredient->stocks / 1000, 2) . ' kg'
                    : $ingredient->stocks . ' pieces';
                session()->flash('error', "Insufficient {$ingredient->name}! Only {$available} available.");
                return;
            }
        }

        DB::beginTransaction();

        try {
            $pricePerItem = $menu->regular_price ?? $menu->price ?? 0;
            $totalPrice = $pricePerItem * $this->orderQuantity;

            $order = orders::create([
                'reservation_id' => $reservationId,
                'walk_in_id' => $walkInId,
                'menu_id' => $this->selectedMenuId,
                'quantity' => $this->orderQuantity,
                'price' => $totalPrice,
                'status' => 'Served',
            ]);

            foreach ($menuIngredients as $menuIngredient) {
                $ingredient = ingredients::find($menuIngredient->ingredient_id);
                $quantityNeeded = $menuIngredient->quantity * $this->orderQuantity;

                $stockBefore = $ingredient->stocks;
                $ingredient->decrement('stocks', $quantityNeeded);
                $ingredient->refresh();
                $stockAfter = $ingredient->stocks;

                ingredientMovements::create([
                    'ingredient_id' => $ingredient->id,
                    'user_id' => Auth::id(),
                    'order_id' => $order->id,
                    'type' => 'used',
                    'quantity' => $quantityNeeded,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                ]);
            }

            DB::commit();
            $this->dispatch('notify', type: 'success', message: 'Additional order added successfully!');
            $this->reset(['selectedTableOrder', 'selectedMenuId', 'orderQuantity']);
            $this->orderQuantity = 1;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', type: 'error', message: 'Failed to add order: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $now = now();
        $today = $now->toDateString();
        $currentTime = $now->toTimeString();

        $pendingOrders = orders::with(['table', 'menu', 'reservation', 'walkin'])
            ->whereNotNull('status')
            ->where(function ($query) use ($today, $currentTime) {
                $query->whereHas('reservation', function ($q) use ($today, $currentTime) {
                    $q->where('status', 'Active')
                        ->whereDate('started_at', $today)
                        ->whereTime('started_at', '<=', $currentTime)
                        ->where(function ($timeQuery) use ($currentTime) {
                            $timeQuery->whereTime('ended_at', '>=', $currentTime)
                                ->orWhereNull('ended_at');
                        });
                })
                    ->orWhereHas('walkin', function ($q) use ($today, $currentTime) {
                        $q->where('status', 'active')
                            ->whereDate('started_at', $today)
                            ->whereTime('started_at', '<=', $currentTime)
                            ->where(function ($timeQuery) use ($currentTime) {
                                $timeQuery->whereTime('ended_at', '>=', $currentTime)
                                    ->orWhereNull('ended_at');
                            });
                    });
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy(function ($order) {
                if ($order->reservation_id) {
                    return 'reservation_' . $order->reservation_id;
                }
                return 'walkin_' . $order->walk_in_id;
            });

        $tables = table::whereHas('reservation', function ($query) use ($today, $currentTime) {
            $query->where('status', 'Active')
                ->whereDate('started_at', $today)
                ->whereTime('started_at', '<=', $currentTime)
                ->where(function ($timeQuery) use ($currentTime) {
                    $timeQuery->whereTime('ended_at', '>=', $currentTime)
                        ->orWhereNull('ended_at');
                })
                ->whereHas('orders', function ($q) {
                    $q->whereNotNull('id');
                })
                ->whereDoesntHave('orders', function ($q) {
                    $q->where('status', 'Pending');
                });
        })
            ->orWhereHas('walkin', function ($query) use ($today, $currentTime) {
                $query->where('status', 'active')
                    ->whereDate('started_at', $today)
                    ->whereTime('started_at', '<=', $currentTime)
                    ->where(function ($timeQuery) use ($currentTime) {
                        $timeQuery->whereTime('ended_at', '>=', $currentTime)
                            ->orWhereNull('ended_at');
                    })
                    ->whereHas('orders', function ($q) {
                        $q->whereNotNull('id');
                    })
                    ->whereDoesntHave('orders', function ($q) {
                        $q->where('status', 'Pending');
                    });
            })
            ->with(['reservation' => function ($query) use ($today, $currentTime) {
                $query->where('status', 'Active')
                    ->whereDate('started_at', $today)
                    ->whereTime('started_at', '<=', $currentTime)
                    ->where(function ($timeQuery) use ($currentTime) {
                        $timeQuery->whereTime('ended_at', '>=', $currentTime)
                            ->orWhereNull('ended_at');
                    });
            }, 'walkin' => function ($query) use ($today, $currentTime) {
                $query->where('status', 'active')
                    ->whereDate('started_at', $today)
                    ->whereTime('started_at', '<=', $currentTime)
                    ->where(function ($timeQuery) use ($currentTime) {
                        $timeQuery->whereTime('ended_at', '>=', $currentTime)
                            ->orWhereNull('ended_at');
                    });
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

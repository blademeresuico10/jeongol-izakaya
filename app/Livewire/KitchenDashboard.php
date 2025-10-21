<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\orders;
use App\Models\ingredients;
use App\Models\table;
use App\Models\OrderRefill;
use App\Models\walkin;
use App\Models\RefillConfiguration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class KitchenDashboard extends Component
{
    public $activeTab = 'pending';



    public function refreshDashboard()
    {
        $this->dispatch('$refresh');
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

    public function getPendingOrdersProperty()
    {
        $now = now();

        return orders::with(['menu', 'reservation.table', 'walkin.table'])
            ->whereIn('status', ['Pending', 'Ready'])
            ->whereNotNull('menu_id')
            ->where(function ($query) use ($now) {
                $query->whereHas('reservation', function ($q) use ($now) {
                    $q->where('started_at', '<=', $now)
                        ->where(function ($subQ) use ($now) {
                            $subQ->where('ended_at', '>=', $now)
                                ->orWhereNull('ended_at');
                        });
                })
                    ->orWhereHas('walkin', function ($q) use ($now) {
                        $q->where('started_at', '<=', $now)
                            ->where('ended_at', '>=', $now);
                    });
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy(function ($order) {
                return ($order->reservation_id ?? 'w') . '-' . ($order->walk_in_id ?? 'r');
            });
    }

    public function getPendingRefillsProperty()
    {
        $now = now();

        return OrderRefill::with(['ingredient', 'order.reservation.table', 'order.walkin.table'])
            ->whereIn('status', ['Pending', 'Ready'])
            ->whereHas('order', function ($query) use ($now) {
                $query->where(function ($q) use ($now) {
                    $q->whereHas('reservation', function ($reservationQuery) use ($now) {
                        $reservationQuery->where('started_at', '<=', $now)
                            ->where(function ($subQ) use ($now) {
                                $subQ->where('ended_at', '>=', $now)
                                    ->orWhereNull('ended_at');
                            });
                    })
                        ->orWhereHas('walkin', function ($walkinQuery) use ($now) {
                            $walkinQuery->where('started_at', '<=', $now)
                                ->where('ended_at', '>=', $now);
                        });
                });
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($refill) {
                $config = RefillConfiguration::where('ingredient_id', $refill->ingredient_id)->first();
                $refill->grams_per_plate = $config ? $config->quantity_per_plate : 0;
                $refill->total_grams = $refill->grams_per_plate * $refill->quantity;
                return $refill;
            });
    }

    public function getReadyOrdersProperty()
    {
        $now = now();

        return orders::with(['menu', 'reservation.table', 'walkin.table'])
            ->where('status', 'Ready')
            ->whereNotNull('menu_id')
            ->where(function ($query) use ($now) {
                $query->whereHas('reservation', function ($q) use ($now) {
                    $q->where('started_at', '<=', $now)
                        ->where(function ($subQ) use ($now) {
                            $subQ->where('ended_at', '>=', $now)
                                ->orWhereNull('ended_at');
                        });
                })
                    ->orWhereHas('walkin', function ($q) use ($now) {
                        $q->where('started_at', '<=', $now)
                            ->where('ended_at', '>=', $now);
                    });
            })
            ->orderBy('updated_at', 'asc')
            ->get()
            ->groupBy(function ($order) {
                return ($order->reservation_id ?? 'w') . '-' . ($order->walk_in_id ?? 'r');
            });
    }

    public function getReadyRefillsProperty()
    {
        $now = now();

        return OrderRefill::with(['ingredient', 'order.reservation.table', 'order.walkin.table'])
            ->where('status', 'Ready')
            ->whereHas('order', function ($query) use ($now) {
                $query->where(function ($q) use ($now) {
                    $q->whereHas('reservation', function ($reservationQuery) use ($now) {
                        $reservationQuery->where('started_at', '<=', $now)
                            ->where(function ($subQ) use ($now) {
                                $subQ->where('ended_at', '>=', $now)
                                    ->orWhereNull('ended_at');
                            });
                    })
                        ->orWhereHas('walkin', function ($walkinQuery) use ($now) {
                            $walkinQuery->where('started_at', '<=', $now)
                                ->where('ended_at', '>=', $now);
                        });
                });
            })
            ->orderBy('updated_at', 'asc')
            ->get()
            ->map(function ($refill) {
                $config = RefillConfiguration::where('ingredient_id', $refill->ingredient_id)->first();
                $refill->grams_per_plate = $config ? $config->quantity_per_plate : 0;
                $refill->total_grams = $refill->grams_per_plate * $refill->quantity;
                return $refill;
            });
    }

    public function getServedOrdersProperty()
    {
        $now = now();

        return orders::with(['menu', 'reservation.table', 'walkin.table'])
            ->whereIn('status', ['Served', 'Ready'])
            ->whereNotNull('menu_id')
            ->where(function ($query) use ($now) {
                $query->whereHas('reservation', function ($q) use ($now) {
                    $q->where('started_at', '<=', $now)
                        ->where(function ($subQ) use ($now) {
                            $subQ->where('ended_at', '>=', $now)
                                ->orWhereNull('ended_at');
                        });
                })
                    ->orWhereHas('walkin', function ($q) use ($now) {
                        $q->where('started_at', '<=', $now)
                            ->where('ended_at', '>=', $now);
                    });
            })
            ->orderBy('updated_at', 'desc')
            ->get()
            ->groupBy(function ($order) {
                return ($order->reservation_id ?? 'w') . '-' . ($order->walk_in_id ?? 'r');
            });
    }

    public function getServedRefillsProperty()
    {
        $now = now();

        return OrderRefill::with(['ingredient', 'order.reservation.table', 'order.walkin.table'])
            ->whereIn('status', ['Served', 'Ready'])
            ->whereHas('order', function ($query) use ($now) {
                $query->where(function ($q) use ($now) {
                    $q->whereHas('reservation', function ($reservationQuery) use ($now) {
                        $reservationQuery->where('started_at', '<=', $now)
                            ->where(function ($subQ) use ($now) {
                                $subQ->where('ended_at', '>=', $now)
                                    ->orWhereNull('ended_at');
                            });
                    })
                        ->orWhereHas('walkin', function ($walkinQuery) use ($now) {
                            $walkinQuery->where('started_at', '<=', $now)
                                ->where('ended_at', '>=', $now);
                        });
                });
            })
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($refill) {
                $config = RefillConfiguration::where('ingredient_id', $refill->ingredient_id)->first();
                $refill->grams_per_plate = $config ? $config->quantity_per_plate : 0;
                $refill->total_grams = $refill->grams_per_plate * $refill->quantity;
                return $refill;
            });
    }
    public function markAsReady($orderOrRefillId, $type = 'order')
    {
        if ($type === 'refill') {
            $refill = OrderRefill::with('ingredient', 'order')->find($orderOrRefillId);

            if (!$refill || $refill->status !== 'Pending') {
                session()->flash('error', 'No pending refill found!');
                return;
            }

            $ingredient = $refill->ingredient;
            if (!$ingredient) {
                session()->flash('error', 'Ingredient not found!');
                return;
            }

            DB::beginTransaction();
            try {
                $config = RefillConfiguration::where('ingredient_id', $ingredient->id)->first();

                if (!$config) {
                    throw new \Exception("Refill configuration not found for {$ingredient->name}");
                }

                $gramsPerPlate = $config->quantity_per_plate;
                $totalGrams = $gramsPerPlate * $refill->quantity;
                $totalKg = $totalGrams / 1000;

                if ($ingredient->stocks < $totalKg) {
                    throw new \Exception(
                        "Insufficient stock for '{$ingredient->name}'. Required: {$totalKg} kg, Available: {$ingredient->stocks} kg"
                    );
                }

                $stockBefore = $ingredient->stocks;
                $stockAfter = $stockBefore - $totalKg;

                DB::table('ingredients')->where('id', $ingredient->id)
                    ->update(['stocks' => $stockAfter, 'updated_at' => now()]);

                DB::table('ingredient_movements')->insert([
                    'ingredient_id' => $ingredient->id,
                    'ingredient_batch_id' => null,
                    'user_id' => Auth::id(),
                    'order_id' => $refill->order_id,
                    'type' => 'used',
                    'quantity' => $totalKg,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'notes' => sprintf(
                        "Refill #%d: %dx %s - Deducted %.3f kg (%.0f g per plate)",
                        $refill->id,
                        $refill->quantity,
                        $ingredient->name,
                        $totalKg,
                        $gramsPerPlate
                    ),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $refill->update(['status' => 'Ready']);

                DB::commit();

                $this->dispatch('refillStatusUpdated');
                session()->flash('success', "Refill is ready for pickup!");
            } catch (\Exception $e) {
                DB::rollBack();
                session()->flash('error', 'Failed to process refill: ' . $e->getMessage());
            }
        } else {
            $order = orders::findOrFail($orderOrRefillId);

            if ($order->reservation_id) {
                $orders = orders::where('reservation_id', $order->reservation_id)
                    ->where('status', 'Pending')
                    ->with(['menu'])
                    ->get();
            } elseif ($order->walk_in_id) {
                $orders = orders::where('walk_in_id', $order->walk_in_id)
                    ->where('status', 'Pending')
                    ->with(['menu'])
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
                foreach ($orders as $singleOrder) {
                    if (!$singleOrder->menu) continue;

                    $menuItem = $singleOrder->menu;
                    $menuIngredients = DB::table('menu_ingredients')
                        ->where('menu_id', $menuItem->id)
                        ->get();

                    foreach ($menuIngredients as $menuIngredient) {
                        $ingredient = DB::table('ingredients')->find($menuIngredient->ingredient_id);
                        if (!$ingredient) continue;

                        // Adjust deduction formula based on unit
                        if ($ingredient->unit === 'pieces') {
                            // For items like Coke, Coco Island
                            $quantityNeeded = $menuIngredient->quantity * $singleOrder->quantity;
                        } else {
                            // For items in kg (meat, veggies, soupbase)
                            $quantityNeeded = ($menuIngredient->quantity / 1000) * $singleOrder->quantity;
                        }

                        // Check stock
                        if ($ingredient->stocks < $quantityNeeded) {
                            throw new \Exception(
                                "Insufficient stock for '{$ingredient->name}'. Required: {$quantityNeeded} {$ingredient->unit}, Available: {$ingredient->stocks} {$ingredient->unit}"
                            );
                        }

                        $stockBefore = $ingredient->stocks;
                        $stockAfter = $stockBefore - $quantityNeeded;

                        DB::table('ingredients')->where('id', $ingredient->id)
                            ->update(['stocks' => $stockAfter, 'updated_at' => now()]);

                        DB::table('ingredient_movements')->insert([
                            'ingredient_id' => $ingredient->id,
                            'ingredient_batch_id' => null,
                            'user_id' => Auth::id(),
                            'order_id' => $singleOrder->id,
                            'type' => 'used',
                            'quantity' => $quantityNeeded,
                            'stock_before' => $stockBefore,
                            'stock_after' => $stockAfter,
                            'notes' => sprintf(
                                "Order #%d: %dx %s (%s) - Deducted %.3f %s of %s (%.0f %s per serving)",
                                $singleOrder->id,
                                $singleOrder->quantity,
                                $menuItem->menu_item,
                                $menuItem->category,
                                $quantityNeeded,
                                $ingredient->unit,
                                $ingredient->name,
                                $menuIngredient->quantity,
                                $ingredient->unit
                            ),
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }

                    $singleOrder->update(['status' => 'Ready']);
                }

                DB::commit();

                $this->dispatch('orderStatusUpdated');
                session()->flash('success', $orders->count() . " order(s) ready for pickup!");
            } catch (\Exception $e) {
                DB::rollBack();
                session()->flash('error', 'Failed to process orders: ' . $e->getMessage());
            }
        }
    }


    public function render()
    {
        return view('livewire.kitchen-dashboard');
    }
}

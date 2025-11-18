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
                $quantityPerPlate = $config->quantity_per_plate;
                $configUnit = strtolower($config->unit);
                $totalQuantityInConfigUnit = $quantityPerPlate * $refill->quantity;
                if ($configUnit === 'g') {
                    $totalQuantityInKg = $totalQuantityInConfigUnit / 1000;
                } else {
                    $totalQuantityInKg = $totalQuantityInConfigUnit;
                }
                if ($ingredient->stocks < $totalQuantityInKg) {
                    throw new \Exception(
                        "Insufficient stock for '{$ingredient->name}'. Required: {$totalQuantityInKg} kg, Available: {$ingredient->stocks} kg"
                    );
                }
                $this->deductFromBatches($ingredient->id, $totalQuantityInKg, $refill->order_id, 'refill', [
                    'refill_id' => $refill->id,
                    'refill_quantity' => $refill->quantity,
                    'ingredient_name' => $ingredient->name,
                    'quantity_per_plate' => $quantityPerPlate,
                    'unit' => $configUnit
                ]);
                $stockBefore = $ingredient->stocks;
                $stockAfter = $stockBefore - $totalQuantityInKg;
                DB::table('ingredients')->where('id', $ingredient->id)
                    ->update(['stocks' => $stockAfter, 'updated_at' => now()]);

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
                    $menuIngredients = DB::table('menu_ingredients as mi')
                        ->join('ingredients as i', 'mi.ingredient_id', '=', 'i.id')
                        ->join('ingredient_units as iu', 'i.unit_id', '=', 'iu.id')
                        ->where('mi.menu_id', $menuItem->id)
                        ->select(
                            'mi.id as menu_ingredient_id',
                            'mi.quantity as recipe_quantity',
                            'mi.unit as recipe_unit',
                            'i.id as ingredient_id',
                            'i.name as ingredient_name',
                            'i.stocks',
                            'iu.abbreviation as stock_unit'
                        )
                        ->get();
                    foreach ($menuIngredients as $menuIngredient) {
                        $recipeQuantity = $menuIngredient->recipe_quantity * $singleOrder->quantity;
                        $recipeUnit = strtolower($menuIngredient->recipe_unit ?? $menuIngredient->stock_unit);
                        $stockUnit = strtolower($menuIngredient->stock_unit);
                        $quantityToDeduct = $this->convertUnit(
                            $recipeQuantity,
                            $recipeUnit,
                            $stockUnit
                        );
                        if ($menuIngredient->stocks < $quantityToDeduct) {
                            throw new \Exception(
                                "Insufficient stock for '{$menuIngredient->ingredient_name}'. " .
                                    "Required: {$quantityToDeduct} {$stockUnit}, " .
                                    "Available: {$menuIngredient->stocks} {$stockUnit}"
                            );
                        }
                        $this->deductFromBatches($menuIngredient->ingredient_id, $quantityToDeduct, $singleOrder->id, 'order', [
                            'menu_item' => $menuItem->menu_item,
                            'order_quantity' => $singleOrder->quantity,
                            'ingredient_name' => $menuIngredient->ingredient_name,
                            'recipe_quantity' => $menuIngredient->recipe_quantity,
                            'recipe_unit' => $recipeUnit
                        ]);
                        $stockBefore = $menuIngredient->stocks;
                        $stockAfter = $stockBefore - $quantityToDeduct;
                        DB::table('ingredients')
                            ->where('id', $menuIngredient->ingredient_id)
                            ->update([
                                'stocks' => $stockAfter,
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
    private function deductFromBatches($ingredientId, $quantityNeeded, $orderId, $type = 'order', $context = [])
    {
        $batches = DB::table('ingredient_batches')
            ->where('ingredient_id', $ingredientId)
            ->where('status', '!=', 'expired')
            ->where('quantity', '>', 0)
            ->where(function ($query) {
                $query->whereNull('expiration_date')
                    ->orWhere('expiration_date', '>=', now());
            })
            ->orderBy('arrived_at', 'asc') 
            ->orderBy('id', 'asc')
            ->get();

        if ($batches->isEmpty()) {
            DB::table('ingredient_movements')->insert([
                'ingredient_id' => $ingredientId,
                'ingredient_batch_id' => null,
                'user_id' => Auth::id(),
                'order_id' => $orderId,
                'type' => 'used',
                'quantity' => $quantityNeeded,
                'stock_before' => 0,
                'stock_after' => 0,
                'notes' => $this->generateMovementNotes($type, $context, $quantityNeeded, null),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            return;
        }
        $remainingQuantity = $quantityNeeded;
        foreach ($batches as $batch) {
            if ($remainingQuantity <= 0) break;

            $deductFromThisBatch = min($remainingQuantity, $batch->quantity);

            $batchBefore = $batch->quantity;
            $batchAfter = $batchBefore - $deductFromThisBatch;
            DB::table('ingredient_batches')
                ->where('id', $batch->id)
                ->update([
                    'quantity' => $batchAfter,
                    'status' => $batchAfter <= 0 ? 'depleted' : $batch->status,
                    'updated_at' => now()
                ]);
            DB::table('ingredient_movements')->insert([
                'ingredient_id' => $ingredientId,
                'ingredient_batch_id' => $batch->id,
                'user_id' => Auth::id(),
                'order_id' => $orderId,
                'type' => 'used',
                'quantity' => $deductFromThisBatch,
                'stock_before' => $batchBefore,
                'stock_after' => $batchAfter,
                'notes' => $this->generateMovementNotes($type, $context, $deductFromThisBatch, $batch),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $remainingQuantity -= $deductFromThisBatch;
        }
    }
    private function generateMovementNotes($type, $context, $quantity, $batch = null)
    {
        $batchInfo = $batch ? "Batch: {$batch->batch_code}" : "No batch";
        if ($type === 'refill') {
            return sprintf(
                "Refill #%d: %dx %s - Deducted %.3f kg (%.2f %s per plate) | %s",
                $context['refill_id'],
                $context['refill_quantity'],
                $context['ingredient_name'],
                $quantity,
                $context['quantity_per_plate'],
                $context['unit'],
                $batchInfo
            );
        } else {
            return sprintf(
                "Order #%d: %dx %s - Deducted %.3f kg of %s (Recipe: %.3f %s per serving) | %s",
                $context['order_id'] ?? 'N/A',
                $context['order_quantity'],
                $context['menu_item'],
                $quantity,
                $context['ingredient_name'],
                $context['recipe_quantity'],
                $context['recipe_unit'],
                $batchInfo
            );
        }
    }
    private function convertUnit($quantity, $fromUnit, $toUnit)
    {
        $fromUnit = strtolower(trim($fromUnit));
        $toUnit = strtolower(trim($toUnit));
        if ($fromUnit === $toUnit) {
            return $quantity;
        }
        $pieceUnits = ['pcs', 'pieces', 'piece', 'pc'];
        if (in_array($fromUnit, $pieceUnits) && in_array($toUnit, $pieceUnits)) {
            return $quantity;
        }
        $weightUnits = ['kg', 'kilogram', 'kilograms', 'g', 'gram', 'grams'];
        if (in_array($fromUnit, $weightUnits) && in_array($toUnit, $weightUnits)) {
            $grams = $quantity;
            if (in_array($fromUnit, ['kg', 'kilogram', 'kilograms'])) {
                $grams = $quantity * 1000;
            }
            if (in_array($toUnit, ['kg', 'kilogram', 'kilograms'])) {
                return $grams / 1000;
            }
            return $grams;
        }
        return $quantity;
    }
    public function render()
    {
        return view('livewire.kitchen-dashboard');
    }
}

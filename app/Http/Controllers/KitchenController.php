<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\orders;
use App\Models\ingredients;
use App\Models\MenuIngredient;
use App\Models\ingredientMovements;
use App\Models\table;
use App\Models\menu;
use App\Models\UnlimitedMeatLog;


class KitchenController extends Controller
{
    public function home()
    {
        return view('kitchen.home');
    }

    public function markAsServed(Request $request)
    {
        $orderId = $request->order_id;

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
            return redirect()->back()->with('error', 'Order not found!');
        }

        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'No pending orders found!');
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
                        auth()->id(),
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

            return redirect()->back()->with('success');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to process order: ' . $e->getMessage());
        }
    }


    public function storeUnlimitedRefill(Request $request)
    {
        $validated = $request->validate([
            'table_id' => 'required|exists:tables,id',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.selected' => 'sometimes|boolean',
            'ingredients.*.quantity' => 'required_with:ingredients.*.selected|numeric|min:50',
        ]);

        $table = table::with(['reservation', 'walkin'])->findOrFail($validated['table_id']);

        $reservationId = $table->reservation->first()?->id;
        $walkInId = $table->walkin->first()?->id;

        $hasUnlimitedPackage = orders::where(function ($query) use ($reservationId, $walkInId) {
            $query->where('reservation_id', $reservationId)
                ->orWhere('walk_in_id', $walkInId);
        })
            ->whereIn('menu_id', [1, 2, 3])
            ->whereIn('status', ['Pending', 'Served'])
            ->exists();


        $refillCount = 0;
        $totalDeducted = [];

        foreach ($validated['ingredients'] as $ingredientId => $data) {
            if (isset($data['selected']) && $data['selected'] && isset($data['quantity'])) {
                $ingredient = ingredients::findOrFail($ingredientId);
                $quantity = $data['quantity'];

                if ($ingredient->stocks < $quantity) {
                    $available = $ingredient->unit === 'kg'
                        ? number_format($ingredient->stocks / 1000, 2) . ' kg'
                        : $ingredient->stocks . ' pieces';
                    return back()->with('error', "Insufficient {$ingredient->name}! Only {$available} available.");
                }

                $stockBefore = $ingredient->stocks;
                $ingredient->decrement('stocks', $quantity);
                $stockAfter = $ingredient->stocks;

                ingredientMovements::create([
                    'ingredient_id' => $ingredient->id,
                    'user_id' => auth()->id(),
                    'type' => 'used',
                    'quantity' => $quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                ]);

                UnlimitedMeatLog::create([
                    'table_id' => $validated['table_id'],
                    'ingredient_id' => $ingredientId,
                    'quantity' => $quantity,
                    'unit' => $ingredient->unit === 'kg' ? 'g' : 'pieces',
                    'reservation_id' => $reservationId,
                    'walk_in_id' => $walkInId,
                ]);

                $display = $ingredient->unit === 'kg'
                    ? number_format($quantity / 1000, 2) . ' kg'
                    : $quantity . ' pieces';

                $totalDeducted[] = "{$display} of {$ingredient->name}";
                $refillCount++;
            }
        }

        if ($refillCount === 0) {
            return back()->with('error', 'Please select at least one ingredient!');
        }

        return back()->with('success');
    }


    public function storeAdditionalOrder(Request $request)
    {
        $validated = $request->validate([
            'table_id' => 'required|exists:tables,id',
            'menu_id' => 'required|exists:menu,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if (in_array($validated['menu_id'], [1, 2, 3])) {
            return back()->with('error', 'Cannot add unlimited packages as additional orders!');
        }

        $menu = menu::findOrFail($validated['menu_id']);
        $table = table::with(['reservation', 'walkin'])->findOrFail($validated['table_id']);
        $reservationId = $table->reservation->first()?->id;
        $walkInId = $table->walkin->first()?->id;

        $menuIngredients = MenuIngredient::where('menu_id', $menu->id)->get();

        foreach ($menuIngredients as $menuIngredient) {
            $ingredient = ingredients::find($menuIngredient->ingredient_id);
            $quantityNeeded = $menuIngredient->quantity * $validated['quantity'];

            if ($ingredient->stocks < $quantityNeeded) {
                $available = $ingredient->unit === 'kg'
                    ? number_format($ingredient->stocks / 1000, 2) . ' kg'
                    : $ingredient->stocks . ' pieces';
                return back()->with('error', "Insufficient {$ingredient->name}! Only {$available} available.");
            }
        }

        DB::transaction(function () use ($validated, $menu, $table, $menuIngredients, $reservationId, $walkInId) {

            $pricePerItem = $menu->regular_price ?? $menu->price ?? 0;
            $totalPrice = $pricePerItem * $validated['quantity'];

            $order = orders::create([
                'reservation_id' => $reservationId,
                'walk_in_id' => $walkInId,
                'menu_id' => $validated['menu_id'],
                'quantity' => $validated['quantity'],
                'price' => $totalPrice,
                'status' => 'Served',
            ]);

            foreach ($menuIngredients as $menuIngredient) {
                $ingredient = ingredients::find($menuIngredient->ingredient_id);
                $quantityNeeded = $menuIngredient->quantity * $validated['quantity'];

                $stockBefore = $ingredient->stocks;
                $ingredient->decrement('stocks', $quantityNeeded);
                $ingredient->refresh();
                $stockAfter = $ingredient->stocks;

                ingredientMovements::create([
                    'ingredient_id' => $ingredient->id,
                    'user_id' => auth()->id(),
                    'order_id' => $order->id,
                    'type' => 'used',
                    'quantity' => $quantityNeeded,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                ]);
            }
        });

        $pricePerItem = $menu->regular_price ?? $menu->price ?? 0;
        return back()->with('success');
    }
}

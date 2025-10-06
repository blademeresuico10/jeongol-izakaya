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


        $ingredients = ingredients::all();

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

        return view('kitchen.home', compact('pendingOrders', 'ingredients', 'tables', 'unlimitedIngredients', 'aLaCarteMenus'));
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
            $mainOrders = $orders->filter(function ($order) {
                return $order->menu && $order->menu->category === 'main';
            });

            $addonOrders = $orders->filter(function ($order) {
                return $order->menu && $order->menu->category === 'add_ons';
            });

            $processedMainIngredients = [];

            foreach ($mainOrders as $singleOrder) {
                $menuIngredients = MenuIngredient::where('menu_id', $singleOrder->menu_id)->get();

                foreach ($menuIngredients as $menuIngredient) {
                    $ingredientKey = $menuIngredient->ingredient_id;

                    if (in_array($ingredientKey, $processedMainIngredients)) {
                        continue;
                    }

                    $ingredient = ingredients::find($menuIngredient->ingredient_id);

                    if (!$ingredient) {
                        continue;
                    }

                    $quantityToDeduct = $menuIngredient->quantity;

                    if ($ingredient->unit === 'kg') {
                        $availableGrams = $ingredient->stocks * 1000;

                        if ($availableGrams < $quantityToDeduct) {
                            DB::rollBack();
                            return redirect()->back()->with('error', "Not enough {$ingredient->name}! Need {$quantityToDeduct}g but only have {$availableGrams}g available.");
                        }

                        $ingredient->deductStock(
                            $quantityToDeduct,
                            $singleOrder->id,
                            auth()->id(),
                            "Main: {$singleOrder->menu->menu_item} - Default serving"
                        );
                    } elseif ($ingredient->unit === 'pieces') {
                        if ($ingredient->stocks < $quantityToDeduct) {
                            DB::rollBack();
                            return redirect()->back()->with('error', "Not enough {$ingredient->name}! Need {$quantityToDeduct} pieces but only have {$ingredient->stocks} available.");
                        }

                        $stockBefore = $ingredient->stocks;
                        $ingredient->stocks -= $quantityToDeduct;
                        $ingredient->save();

                        ingredientMovements::create([
                            'ingredient_id' => $ingredient->id,
                            'user_id' => Auth::id(),
                            'order_id' => $singleOrder->id,
                            'type' => 'used',
                            'quantity' => $quantityToDeduct,
                            'stock_before' => $stockBefore,
                            'stock_after' => $ingredient->stocks,
                            'notes' => "Main: {$singleOrder->menu->menu_item} - Default serving"
                        ]);
                    }

                    $processedMainIngredients[] = $ingredientKey;
                }

                $singleOrder->status = 'served';
                $singleOrder->save();
            }

            // Process ADD-ONS orders
            foreach ($addonOrders as $singleOrder) {
                $menuIngredients = MenuIngredient::where('menu_id', $singleOrder->menu_id)->get();

                foreach ($menuIngredients as $menuIngredient) {
                    $ingredient = ingredients::find($menuIngredient->ingredient_id);

                    if (!$ingredient) {
                        continue;
                    }

                    $totalNeeded = $menuIngredient->quantity * $singleOrder->quantity;

                    if ($ingredient->unit === 'kg') {
                        $availableGrams = $ingredient->stocks * 1000;

                        if ($availableGrams < $totalNeeded) {
                            DB::rollBack();
                            return redirect()->back()->with('error', "Not enough {$ingredient->name}! Need {$totalNeeded}g but only have {$availableGrams}g available.");
                        }

                        $ingredient->deductStock(
                            $totalNeeded,
                            $singleOrder->id,
                            auth()->id(),
                            "Add-on: {$singleOrder->quantity} x {$singleOrder->menu->menu_item}"
                        );
                    } elseif ($ingredient->unit === 'pieces') {
                        if ($ingredient->stocks < $totalNeeded) {
                            DB::rollBack();
                            return redirect()->back()->with('error', "Not enough {$ingredient->name}! Need {$totalNeeded} pieces but only have {$ingredient->stocks} available.");
                        }

                        $stockBefore = $ingredient->stocks;
                        $ingredient->stocks -= $totalNeeded;
                        $ingredient->save();

                        ingredientMovements::create([
                            'ingredient_id' => $ingredient->id,
                            'user_id' => auth()->id(),
                            'order_id' => $singleOrder->id,
                            'type' => 'used',
                            'quantity' => $totalNeeded,
                            'stock_before' => $stockBefore,
                            'stock_after' => $ingredient->stocks,
                            'notes' => "Add-on: {$singleOrder->quantity} x {$singleOrder->menu->menu_item}"
                        ]);
                    }
                }

                $singleOrder->status = 'served';
                $singleOrder->save();
            }
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
                    'notes' => 'Unlimited refill deduction for table ' . $table->id,
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
                    'notes' => "Additional order for table {$table->id}, menu: {$menu->menu_item}",
                ]);
            }
        });

        $pricePerItem = $menu->regular_price ?? $menu->price ?? 0;
        return back()->with('success');
    }
}

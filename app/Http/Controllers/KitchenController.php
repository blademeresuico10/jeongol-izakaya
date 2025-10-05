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


class KitchenController extends Controller
{
    public function home()
    {
        $pendingOrders = orders::with(['table', 'menu', 'reservation', 'walkin.table'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy(function ($order) {
                if ($order->reservation_id) {
                    return 'reservation_' . $order->reservation_id;
                }
                return 'walkin_' . $order->walk_in_id;
            });

        return view('kitchen.home', compact('pendingOrders'));
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

            DB::commit();
            return redirect()->back()->with('success', 'Orders marked as served and ingredients deducted!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to process order: ' . $e->getMessage());
        }
    }
}

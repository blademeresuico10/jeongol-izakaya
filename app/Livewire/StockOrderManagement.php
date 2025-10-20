<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ingredients;
use App\Models\StockOrder;
use App\Models\ingredientBatch;
use Livewire\WithFileUploads;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\ingredientMovements;
use Illuminate\Support\Facades\DB;

class StockOrderManagement extends Component
{
    public $activeTab = 'stock-requests-list';

    public $showReceiveModal = false;
    public $selectedOrder = null;
    public $orderedQuantity = 0;
    public $receivedQuantity = 0;
    public $ingredientName = '';
    public $unit = '';
    public $expirationDate = null;

    protected $listeners = [
        'orderCreated' => '$refresh',
        'orderCompleted' => '$refresh',
    ];

    // NEW METHOD: Create stock order from button click
    public function createStockOrder($ingredientId)
    {
        $ingredient = ingredients::find($ingredientId);
        
        if (!$ingredient) {
            session()->flash('error', 'Ingredient not found.');
            return;
        }

        $alertLevel = $ingredient->stockAlertLevel;
        
        if (!$alertLevel) {
            session()->flash('error', 'No stock alert level configured for this ingredient.');
            return;
        }

        // Check if there's already a pending order
        $existingOrder = StockOrder::where('ingredient_id', $ingredientId)
            ->where('status', 'pending')
            ->exists();

        if ($existingOrder) {
            session()->flash('info', 'A pending stock order already exists for this ingredient.');
            return;
        }

        // Create the stock order
        StockOrder::create([
            'ingredient_id' => $ingredientId,
            'alert_id' => $alertLevel->id,
            'quantity' => $alertLevel->reorder_quantity,
            'status' => 'pending',
        ]);

        session()->flash('success', "Stock order created successfully for {$ingredient->name}!");
        
        // Switch to pending orders tab
        $this->activeTab = 'pending-orders-list';
        
        $this->dispatch('orderCreated');
    }

    public function createOrder($ingredientId)
    {
        $ingredient = ingredients::findOrFail($ingredientId);
        $order = StockOrder::autoGenerateOrder($ingredient);

        if ($order) {
            $this->dispatch('orderCreated');
            session()->flash('success', "Stock order created for {$ingredient->name}");
        } else {
            session()->flash('error', 'Order already exists or alert not configured');
        }
    }

    public function createAllOrders()
    {
        $ingredients = ingredients::with('stockAlertLevel')->get();
        $ordersCreated = 0;

        foreach ($ingredients as $ingredient) {
            $alertLevel = $ingredient->stockAlertLevel;

            if ($alertLevel && $ingredient->stocks <= $alertLevel->low_stock) {
                if (StockOrder::autoGenerateOrder($ingredient)) {
                    $ordersCreated++;
                }
            }
        }

        $this->dispatch('orderCreated');

        if ($ordersCreated > 0) {
            session()->flash('success', "{$ordersCreated} stock order(s) created successfully");
        } else {
            session()->flash('info', 'No new orders needed');
        }
    }



    public function openReceiveModal($orderId)
    {
        $order = StockOrder::with('ingredient')->findOrFail($orderId);

        $this->selectedOrder = $orderId;
        $this->orderedQuantity = $order->quantity;
        $this->receivedQuantity = $order->quantity;
        $this->ingredientName = $order->ingredient->name;
        $this->unit = $order->ingredient->unit;
        $this->expirationDate = null;
        $this->showReceiveModal = true;
    }

    public function closeReceiveModal()
    {
        $this->showReceiveModal = false;
        $this->reset(['selectedOrder', 'orderedQuantity', 'receivedQuantity', 'ingredientName', 'unit', 'expirationDate']);
    }

    public function confirmReceive()
    {
        // Check if unit is pieces and validate whole numbers
        $isPieces = in_array(strtolower($this->unit), ['pieces', 'pcs', 'piece']);
        
        if ($isPieces) {
            // For pieces: validate integer only
            $this->validate([
                'receivedQuantity' => [
                    'required',
                    'numeric',
                    'min:1',
                    'regex:/^\d+$/' // Only whole numbers, no decimals
                ],
                'expirationDate' => 'nullable|date|after_or_equal:today',
            ], [
                'receivedQuantity.required' => 'Received quantity is required',
                'receivedQuantity.numeric' => 'Received quantity must be a number',
                'receivedQuantity.min' => 'Received quantity must be at least 1',
                'receivedQuantity.regex' => 'Pieces must be a whole number (no decimals allowed)',
                'expirationDate.after_or_equal' => 'Expiration date cannot be in the past'
            ]);
        } else {
            // For other units: allow decimals
            $this->validate([
                'receivedQuantity' => 'required|numeric|min:0.01',
                'expirationDate' => 'nullable|date|after_or_equal:today',
            ], [
                'receivedQuantity.required' => 'Received quantity is required',
                'receivedQuantity.numeric' => 'Received quantity must be a number',
                'receivedQuantity.min' => 'Received quantity must be greater than 0',
                'expirationDate.after_or_equal' => 'Expiration date cannot be in the past'
            ]);
        }

        $order = StockOrder::findOrFail($this->selectedOrder);
        $ingredient = $order->ingredient;

        DB::transaction(function () use ($order, $ingredient) {
            $stockBefore = $ingredient->stocks;

            $ingredient->stocks += $this->receivedQuantity;
            $ingredient->save();

            $batch = ingredientBatch::create([
                'ingredient_id' => $ingredient->id,
                'quantity' => $this->receivedQuantity,
                'arrived_at' => now(),
                'expiration_date' => $this->expirationDate,
                'status' => 'active'
            ]);

            $order->status = 'completed';
            $order->save();

            ingredientMovements::create([
                'ingredient_id' => $ingredient->id,
                'ingredient_batch_id' => $batch->id,
                'user_id' => Auth::id(),
                'type' => 'stock_in',
                'quantity' => $this->receivedQuantity,
                'stock_before' => $stockBefore,
                'stock_after' => $ingredient->stocks,
                'notes' => "Stock order #{$order->id} completed. Ordered: {$this->orderedQuantity} {$this->unit}, Received: {$this->receivedQuantity} {$this->unit}"
            ]);
        });

        $difference = $this->orderedQuantity - $this->receivedQuantity;

        if ($difference > 0) {
            session()->flash('warning', "Order completed! Shortage: {$difference} {$this->unit}");
        } elseif ($difference < 0) {
            session()->flash('success', "Order completed! Excess: " . abs($difference) . " {$this->unit}");
        } else {
            session()->flash('success', "Stock received successfully! Added {$this->receivedQuantity} {$this->unit}");
        }

        $this->closeReceiveModal();
        $this->dispatch('orderCompleted');
    }

    public function switchToPendingTab()
    {
        $this->activeTab = 'pending-orders-list';
    }

    public function render()
    {
        $ingredients = ingredients::with('stockAlertLevel')->get();

        // Only fetch pending stock orders
        $stockOrders = StockOrder::with('ingredient')
            ->where('status', 'pending')
            ->latest()
            ->get();

        // Get critical stock ingredients with their pending order quantities
        $criticalStockIngredients = $ingredients->filter(
            fn($i) => $i->stockAlertLevel &&
                $i->stockAlertLevel->critical_stock &&
                $i->stocks <= $i->stockAlertLevel->critical_stock
        )->map(function ($ingredient) {
            // Fetch the exact order quantity from stock_orders table
            $pendingOrder = StockOrder::where('ingredient_id', $ingredient->id)
                ->where('status', 'pending')
                ->first();

            if ($pendingOrder) {
                // Use the quantity from stock_orders, not from stock_level_alerts
                $ingredient->pending_order_quantity = $pendingOrder->quantity;
                $ingredient->pending_order_id = $pendingOrder->id;
            } else {
                // Fallback to reorder_quantity from stock_level_alerts if no pending order
                $ingredient->pending_order_quantity = $ingredient->stockAlertLevel->reorder_quantity ?? null;
                $ingredient->pending_order_id = null;
            }

            return $ingredient;
        });

        // Get low stock ingredients with their pending order quantities
        $lowStockIngredients = $ingredients->filter(
            fn($i) => $i->stockAlertLevel &&
                $i->stocks <= $i->stockAlertLevel->low_stock &&
                (!$i->stockAlertLevel->critical_stock || $i->stocks > $i->stockAlertLevel->critical_stock)
        )->map(function ($ingredient) {
            // Fetch the exact order quantity from stock_orders table
            $pendingOrder = StockOrder::where('ingredient_id', $ingredient->id)
                ->where('status', 'pending')
                ->first();

            if ($pendingOrder) {
                // Use the quantity from stock_orders, not from stock_level_alerts
                $ingredient->pending_order_quantity = $pendingOrder->quantity;
                $ingredient->pending_order_id = $pendingOrder->id;
            } else {
                // Fallback to reorder_quantity from stock_level_alerts if no pending order
                $ingredient->pending_order_quantity = $ingredient->stockAlertLevel->reorder_quantity ?? null;
                $ingredient->pending_order_id = null;
            }

            return $ingredient;
        });

        // Calculate request count (only ingredients WITHOUT pending orders)
        $criticalWithoutOrders = $criticalStockIngredients->filter(fn($i) => !$i->pending_order_id);
        $lowWithoutOrders = $lowStockIngredients->filter(fn($i) => !$i->pending_order_id);
        $requestCount = $criticalWithoutOrders->count() + $lowWithoutOrders->count();

        return view('livewire.stock-order-management', [
            'stockOrders' => $stockOrders,
            'allStockRequests' => $stockOrders, // Now only pending orders
            'criticalStockIngredients' => $criticalStockIngredients,
            'lowStockIngredients' => $lowStockIngredients,
            'pendingCount' => $stockOrders->count(),
            'requestCount' => $requestCount,
        ]);
    }
}
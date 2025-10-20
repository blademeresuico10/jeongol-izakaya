<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\ingredients;
use App\Models\StockAlertLevel;
use App\Models\ingredientMovements;

class StockOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'ingredient_id',
        'alert_id',
        'quantity',
        'status',
    ];


    public function ingredient()
    {
        return $this->belongsTo(ingredients::class, 'ingredient_id');
    }

    public function alert()
    {
        return $this->belongsTo(stockAlertLevel::class, 'alert_id');
    }


    public static function autoGenerateOrder(ingredients $ingredient)
    {
        $alertLevel = $ingredient->stockAlertLevel;

        if (!$alertLevel) return null;

        $existingOrder = self::where('ingredient_id', $ingredient->id)
            ->where('status', 'pending')
            ->exists();

        if ($existingOrder) return null;

        return self::create([
            'ingredient_id' => $ingredient->id,
            'alert_id' => $alertLevel->id, 
            'quantity' => $alertLevel->reorder_quantity,
            'status' => 'pending',
        ]);
    }

    public function complete()
    {
        $ingredient = $this->ingredient;
        $stockBefore = $ingredient->stocks;

        $ingredient->stocks += $this->quantity;
        $ingredient->save();

        ingredientMovements::create([
            'ingredient_id' => $ingredient->id,
            'user_id' => Auth::id(),
            'type' => 'stock_in',
            'quantity' => $this->quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $ingredient->stocks,
            'notes' => "Stock order #{$this->id} received",
        ]);

        $this->update(['status' => 'completed']);
        return $this;
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
        return $this;
    }


    public function getQuantityWithUnitAttribute()
    {
        return $this->quantity . ' ' . $this->ingredient->unit;
    }


    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeCritical($query)
    {
        return $query->whereHas('ingredient', function ($q) {
            $q->whereHas('stockAlertLevel', function ($sq) {
                $sq->whereRaw('ingredients.stocks <= stock_level_alerts.critical_stock');
            });
        });
    }


    public static function createManualOrder($ingredientId, $quantity)
    {
        return self::create([
            'ingredient_id' => $ingredientId,
            'quantity' => $quantity,
            'status' => 'pending',
        ]);
    }
}

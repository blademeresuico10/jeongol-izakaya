<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ingredients extends Model
{
    protected $fillable = ['name', 'category_id', 'unit_id', 'stocks'];

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($ingredient) {
            $ingredient->checkAndCreateStockOrder();
        });
    }

    public function category()
    {
        return $this->belongsTo(IngredientCategory::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(IngredientUnit::class, 'unit_id');
    }

    public function batches()
    {
        return $this->hasMany(ingredientBatch::class);
    }

    public function movements()
    {
        return $this->hasMany(ingredientMovements::class);
    }

    public function expiredRecords()
    {
        return $this->hasMany(expiredIngredients::class);
    }

    public function menuIngredients()
    {
        return $this->hasMany(MenuIngredient::class, 'ingredient_id');
    }

    public function menus()
    {
        return $this->belongsToMany(menu::class, 'menu_ingredients', 'ingredient_id', 'menu_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function stockAlertLevel()
    {
        return $this->hasOne(StockAlertLevel::class, 'ingredient_id');
    }

    public function stockOrders()
    {
        return $this->hasMany(StockOrder::class, 'ingredient_id');
    }

    public function convertGramsToKg($grams)
    {
        return $grams / 1000;
    }

    public function deductStock($grams, $orderId, $userId, $notes = null)
    {
        $stockBefore = $this->stocks;
        $kgToDeduct = $this->convertGramsToKg($grams);

        $this->stocks -= $kgToDeduct;
        $this->save();

        ingredientMovements::create([
            'ingredient_id' => $this->id,
            'user_id' => $userId,
            'order_id' => $orderId,
            'type' => 'used',
            'quantity' => $kgToDeduct,
            'stock_before' => $stockBefore,
            'stock_after' => $this->stocks,
            'notes' => $notes ?? "Used for order #{$orderId}"
        ]);

        return $this;
    }

    public function checkAndCreateStockOrder()
    {
        $alertLevel = $this->stockAlertLevel;

        if (!$alertLevel) {
            return null;
        }

        if ($this->stocks <= $alertLevel->low_stock) {

            $existingOrder = StockOrder::where('ingredient_id', $this->id)
                ->where('status', 'pending')
                ->exists();

            if ($existingOrder) {
                return null;
            }

            return StockOrder::create([
                'ingredient_id' => $this->id,
                'alert_id'      => $alertLevel->id,
                'quantity'      => $alertLevel->reorder_quantity,
                'status'        => 'pending',
            ]);
        }

        return null;
    }

    public function getStockStatus()
    {
        $alertLevel = $this->stockAlertLevel;

        if (!$alertLevel) {
            return 'unknown';
        }

        if ($this->stocks <= $alertLevel->critical_stock) {
            return 'critical';
        }

        if ($this->stocks <= $alertLevel->low_stock) {
            return 'low';
        }

        return 'normal';
    }

    public function getStockStatusBadge()
    {
        $status = $this->getStockStatus();

        $badges = [
            'critical' => '<span class="badge bg-danger">Critical</span>',
            'low' => '<span class="badge bg-warning">Low</span>',
            'normal' => '<span class="badge bg-success">Normal</span>',
            'unknown' => '<span class="badge bg-secondary">Unknown</span>',
        ];

        return $badges[$status] ?? $badges['unknown'];
    }

    public function formatQuantity($quantity)
    {
        if (in_array(strtolower($this->unit->abbreviation), ['pcs', 'pieces', 'pc'])) {
            return number_format($quantity, 0);
        }
        return $quantity;
    }
}

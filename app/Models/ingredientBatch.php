<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ingredientBatch extends Model
{
    protected $fillable = ['status', 'ingredient_id', 'arrived_at', 'expiration_date', 'quantity'];

    public function ingredient()
    {
        return $this->belongsTo(ingredients::class);
    }

    protected static function booted()
    {
        static::deleting(function ($batch) {
            $ingredient = $batch->ingredient;

            if ($ingredient && $batch->quantity > 0) {
                $stockBefore = $ingredient->stocks;
                $stockAfter = max(0, $stockBefore - $batch->quantity);

                $ingredient->update(['stocks' => $stockAfter]);

                // Log movement
                ingredientMovements::create([
                    'ingredient_id' => $ingredient->id,
                    'ingredient_batch_id' => $batch->id,
                    'user_id' => Auth::id(),
                    'type' => 'deleted_batch',
                    'quantity' => -$batch->quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'notes' => "Batch deleted (ID: {$batch->id}) — {$batch->quantity} {$ingredient->unit} deducted."
                ]);
            }
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\expiredIngredients;
use App\Models\ingredientMovements;
use Illuminate\Support\Facades\Auth;

class ingredientBatch extends Model
{
    protected $fillable = ['status', 'ingredient_id', 'arrived_at', 'expiration_date', 'quantity'];

    // REMOVED the problematic global scope

    /**
     * Check and process expired batches efficiently
     * Only checks batches that need checking
     */
    public static function processExpiredBatches()
    {
        $now = Carbon::now();

        // Only get batches that are expired but not yet marked as expired
        $expiredBatches = self::where('status', '!=', 'expired')
            ->where('quantity', '>', 0)
            ->whereDate('expiration_date', '<', $now)
            ->get();

        if ($expiredBatches->isEmpty()) {
            return ['processed' => 0, 'message' => 'No expired batches'];
        }

        $processed = 0;

        foreach ($expiredBatches as $batch) {
            try {
                $batch->markAsExpired();
                $processed++;
            } catch (\Exception $e) {
            }
        }

        return ['processed' => $processed, 'message' => "$processed batches processed"];
    }


    public function markAsExpired()
    {
        if ($this->status === 'expired' || $this->quantity <= 0) {
            return false;
        }

        if (!$this->expiration_date || $this->expiration_date >= Carbon::now()) {
            return false;
        }

        $alreadyLogged = ingredientMovements::where('ingredient_batch_id', $this->id)
            ->where('type', 'expired')
            ->exists();

        if ($alreadyLogged) {
            return false;
        }

        DB::transaction(function () {
            $ingredient = $this->ingredient;
            $expiredQty = $this->quantity;

            expiredIngredients::create([
                'quantity' => $expiredQty,
                'expired_at' => Carbon::now(),
                'ingredient_id' => $this->ingredient_id,     
                'ingredient_batch_id' => $this->id,
            ]);

            ingredientMovements::create([
                'ingredient_id' => $this->ingredient_id,
                'ingredient_batch_id' => $this->id,
                'user_id' => Auth::id() ?? 1,                 
                'type' => 'expired',
                'quantity' => $expiredQty,
                'stock_before' => $ingredient->stocks,
                'stock_after' => $ingredient->stocks - $expiredQty,
                'notes' => 'Auto-deducted (expired batch)',
            ]);

            if ($ingredient) {
                $ingredient->decrement('stocks', $expiredQty);
            }

            $this->update([
                'status' => 'expired',
                'quantity' => 0,
            ]);
        });

        return true;
    }

    public function ingredient()
    {
        return $this->belongsTo(ingredients::class);
    }
}

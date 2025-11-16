<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ingredients;
use App\Models\ingredientBatch;

class StocksTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        ingredientBatch::processExpiredBatches();

        $ingredients = ingredients::with(['category', 'unit', 'stockAlertLevel'])
            ->orderBy('name', 'asc')
            ->paginate(10);

        $ingredients->getCollection()->transform(function ($ingredient) {
            $lowStock = $ingredient->stockAlertLevel->low_stock ?? 50;
            $criticalStock = $ingredient->stockAlertLevel->critical_stock ?? 10;

            if ($ingredient->stocks <= $criticalStock) {
                $ingredient->badge_class = 'bg-danger';
                $ingredient->badge_text = 'Critical';
            } elseif ($ingredient->stocks <= $lowStock) {
                $ingredient->badge_class = 'bg-warning';
                $ingredient->badge_text = 'Low Stock';
            } else {
                $ingredient->badge_class = 'bg-success';
                $ingredient->badge_text = 'Good';
            }

            return $ingredient;
        });

        return view('livewire.stocks-table', [
            'ingredients' => $ingredients
        ]);
    }

    
}
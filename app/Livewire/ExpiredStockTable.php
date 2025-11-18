<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\expiredIngredients;
use App\Models\ingredients;
use App\Models\ingredientBatch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class ExpiredStockTable extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    // Filters
    public $period = 'thisweek';
    public $ingredientFilter = 'all';
    public function mount()
    {
        $this->period = 'thisweek';
    }
    public function updatedPeriod()
    {
        $this->resetPage();
    }

    public function updatedIngredientFilter()
    {
        $this->resetPage();
    }
    public function updatedSearchTerm()
    {
        $this->resetPage();
    }
    public function getExpiredStocksProperty()
    {
        [$startDate, $endDate] = $this->getDateRange();

        $query = DB::table('expired_ingredients as ei')
            ->join('ingredient_batches as ib', 'ei.ingredient_batch_id', '=', 'ib.id')
            ->join('ingredients as i', 'ib.ingredient_id', '=', 'i.id')
            ->join('ingredient_units as iu', 'i.unit_id', '=', 'iu.id')
            ->select(
                'ei.id',
                'ib.batch_code',
                'i.name as ingredient_name',
                'i.id as ingredient_id',
                'ei.quantity',
                'iu.abbreviation as unit',
                'ei.expired_at'
            )
            ->whereBetween('ei.expired_at', [$startDate, $endDate]);
        if ($this->ingredientFilter !== 'all') {
            $query->where('i.id', $this->ingredientFilter);
        }
        return $query->orderBy('ei.expired_at', 'desc')->paginate(10);
    }
    private function getDateRange()
    {
        $now = Carbon::now();

        if ($this->period === 'thisweek') {
            return [
                $now->copy()->startOfWeek(),
                $now->copy()->endOfWeek()
            ];
        }
        return [
            $now->copy()->startOfMonth(),
            $now->copy()->endOfMonth()
        ];
    }
    public function render()
    {
        return view('livewire.expired-stock-table', [
            'expiredStocks' => $this->expiredStocks,
            'ingredients' => ingredients::whereIn('id', ingredientBatch::pluck('ingredient_id'))->get()
        ]);
    }
}
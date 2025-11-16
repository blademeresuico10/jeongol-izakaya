<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ingredientBatch;
use App\Models\ingredients;
use App\Models\expiredIngredients;
use App\Models\ingredientMovements;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StockBatchesManagement extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filters
    public $period = 'thisweek';
    public $ingredientFilter = 'all';
    
    // Edit Modal Properties
    public $editBatchId;
    public $editBatchCode;
    public $editArrivedAt;
    public $editExpiryDate;
    public $showEditModal = false;

    protected $rules = [
        'editArrivedAt' => 'required|date',
        'editExpiryDate' => 'required|date|after:today',
    ];

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

    public function getBatchesProperty()
    {
        ingredientBatch::processExpiredBatches();

        [$startDate, $endDate] = $this->getDateRange();

        $query = $this->buildBatchQuery($startDate, $endDate);
        
        $batches = $query->orderBy('ib.arrived_at', 'desc')->paginate(10);

        $batches->getCollection()->transform(function ($batch) {
            $batch->original_quantity = $this->getOriginalQuantity($batch->id) ?? $batch->quantity;
            return $batch;
        });

        return $batches;
    }

    private function getDateRange()
    {
        if ($this->period === 'thisweek') {
            return [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ];
        }
        
        return [
            Carbon::now()->subWeek()->startOfWeek(),
            Carbon::now()->subWeek()->endOfWeek()
        ];
    }

    private function buildBatchQuery($startDate, $endDate)
    {
        $query = DB::table('ingredient_batches as ib')
            ->join('ingredients as i', 'ib.ingredient_id', '=', 'i.id')
            ->join('ingredient_units as iu', 'i.unit_id', '=', 'iu.id')
            ->leftJoin('stock_level_alerts as sla', 'i.id', '=', 'sla.ingredient_id')
            ->select(
                'ib.id',
                'ib.batch_code',
                'ib.status',
                'i.name as ingredient_name',
                'ib.ingredient_id',
                'ib.quantity',
                'ib.expiration_date',
                'iu.abbreviation as unit',
                'ib.arrived_at',
                'sla.reorder_quantity'
            )
            ->whereNotIn('ib.status', ['expired', 'depleted'])
            ->where('ib.quantity', '>', 0)
            ->whereBetween('ib.arrived_at', [$startDate, $endDate]);

        if ($this->ingredientFilter !== 'all') {
            $query->where('ib.ingredient_id', $this->ingredientFilter);
        }

        return $query;
    }

    private function getOriginalQuantity($batchId)
    {
        return DB::table('ingredient_movements')
            ->where('ingredient_batch_id', $batchId)
            ->where('type', 'received')
            ->value('quantity');
    }

    public function editBatch($id, $batchCode, $arrivedAt, $expiryDate)
    {
        $this->editBatchId = $id;
        $this->editBatchCode = $batchCode;
        $this->editArrivedAt = Carbon::parse($arrivedAt)->format('Y-m-d');
        $this->editExpiryDate = Carbon::parse($expiryDate)->format('Y-m-d');
        $this->showEditModal = true;
    }

    public function updateBatch()
    {
        $this->validate();

        try {
            $batch = ingredientBatch::findOrFail($this->editBatchId);
            
            $batch->update([
                'arrived_at' => $this->editArrivedAt,
                'expiration_date' => $this->editExpiryDate,
            ]);

            $this->closeEditModal();
            session()->flash('success', 'Batch updated successfully!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update batch: ' . $e->getMessage());
        }
    }

    public function expireBatch($id)
    {
        DB::beginTransaction();
        
        try {
            $batch = ingredientBatch::findOrFail($id);

            if ($batch->status === 'expired' || $batch->quantity <= 0) {
                session()->flash('error', 'Batch is already expired.');
                DB::rollBack();
                return;
            }

            $ingredient = $batch->ingredient;
            $expiredQty = $batch->quantity;
            $stockBefore = $ingredient->stocks;
            $stockAfter = $stockBefore - $expiredQty;

            // Create expired record
            expiredIngredients::create([
                'ingredient_id' => $batch->ingredient_id,
                'quantity' => $expiredQty,
                'expired_at' => now(),
                'ingredient_batch_id' => $batch->id,
            ]);

            ingredientMovements::create([
                'ingredient_id' => $batch->ingredient_id,
                'ingredient_batch_id' => $batch->id,
                'user_id' => Auth::id() ?? 1,
                'type' => 'expired',
                'quantity' => $expiredQty,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'notes' => 'Manually marked as expired',
            ]);

            $ingredient->decrement('stocks', $expiredQty);

            $batch->update([
                'status' => 'expired',
                'quantity' => 0,
            ]);

            DB::commit();
            session()->flash('success', 'Batch marked as expired successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to expire batch: ' . $e->getMessage());
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset(['editBatchId', 'editBatchCode', 'editArrivedAt', 'editExpiryDate']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.stock-batches-management', [
            'batches' => $this->batches,
            'ingredients' => ingredients::whereIn('id', ingredientBatch::pluck('ingredient_id'))->get()
        ]);
    }
}
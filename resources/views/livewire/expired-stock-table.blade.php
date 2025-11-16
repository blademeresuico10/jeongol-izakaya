<div>
    {{-- Filters --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <ul class="nav nav-pills" role="tablist">
            <li class="nav-item">
                <button wire:click="$set('period', 'thisweek')" type="button"
                    class="nav-link {{ $period === 'thisweek' ? 'active' : '' }}">
                    This Week
                </button>
            </li>
            <li class="nav-item">
                <button wire:click="$set('period', 'thismonth')" type="button"
                    class="nav-link {{ $period === 'thismonth' ? 'active' : '' }}">
                    This Month
                </button>
            </li>
        </ul>

        <div class="d-flex gap-2 align-items-center">
            
            <div class="border rounded p-1">
                <select wire:model.live="ingredientFilter" class="form-select form-select-sm" style="width: 200px;">
                    <option value="all">All Ingredients</option>
                    @foreach ($ingredients as $ingredient)
                        <option value="{{ $ingredient->id }}">{{ $ingredient->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Expired Stocks Table --}}
    @if ($expiredStocks->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
            <p>No expired stocks found for this period</p>
        </div>
    @else
        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
            <table class="table table-bordered mb-0">
                <thead class="thead-light" style="position: sticky; top: 0; z-index: 10; background-color: #f8f9fa;">
                    <tr>
                        <th>Batch Code</th>
                        <th>Ingredient</th>
                        <th>Quantity</th>
                        <th>Expired Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expiredStocks as $stock)
                        @php
                            $isPieces = in_array(strtolower($stock->unit), ['pcs', 'pieces', 'piece', 'pc']);
                            $formattedQty = $isPieces ? floor($stock->quantity) : number_format($stock->quantity, 2);
                        @endphp
                        <tr>
                            <td>
                                <code class="text-dark bg-light px-2 py-1 rounded">
                                    {{ $stock->batch_code ?? 'N/A' }}
                                </code>
                            </td>
                            <td><strong>{{ $stock->ingredient_name }}</strong></td>
                            <td>
                                <span class="font-weight-medium text-danger">{{ $formattedQty }}</span>
                                <span class="text-muted">{{ $stock->unit }}</span>
                            </td>
                            <td>
                                <span class="badge badge-danger">
                                    {{ \Carbon\Carbon::parse($stock->expired_at)->format('M d, Y') }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3 d-flex justify-content-center expired-pagination">
            {{ $expiredStocks->links() }}
        </div>
    @endif

    <style>
        .expired-pagination p {
            display: none !important;
        }

        .gap-2 {
            gap: 0.5rem;
        }
    </style>
</div>
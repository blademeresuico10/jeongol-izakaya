<div wire:poll.20s>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Ingredient Name</th>
                    <th>Category</th>
                    <th>Stock Level</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ingredients as $ingredient)
                    @php
                        $unit = $ingredient->unit->abbreviation ?? 'unit';
                        $isPieces = in_array(strtolower($unit), ['pcs', 'pieces', 'piece', 'pc']);
                        $formattedStock = $isPieces ? floor($ingredient->stocks) : number_format($ingredient->stocks, 2);
                    @endphp
                    <tr>
                        <td class="font-weight-bold">{{ $ingredient->name }}</td>
                        <td class="text-capitalize">{{ $ingredient->category->name ?? 'N/A' }}</td>
                        <td>
                            <span class="font-semibold">{{ $formattedStock }}</span>
                            <span class="text-muted">{{ $unit }}</span>
                            <span class="ml-2 px-2 py-1 text-white text-xs font-semibold rounded {{ $ingredient->badge_class }}">
                                {{ $ingredient->badge_text }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">No ingredients available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3 d-flex justify-content-center stocks-pagination">
        {{ $ingredients->onEachSide(1)->links() }}
    </div>

    <style>
        .stocks-pagination p {
            display: none;
        }
    </style>
</div>
<div wire:poll.10s>
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session()->has('info'))
        <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
            {{ session('info') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <ul class="nav nav-tabs mt-3 mb-3 border-bottom-0" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'stock-requests-list' ? 'active' : '' }}"
                wire:click="$set('activeTab', 'stock-requests-list')" href="#" role="tab">
                Stock Alerts
                @if(isset($requestCount) && $requestCount > 0)
                    <span class="badge badge-danger ml-1">{{ $requestCount }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'pending-orders-list' ? 'active' : '' }}"
                wire:click="$set('activeTab', 'pending-orders-list')" href="#" role="tab">
                Pending Orders
                @if($pendingCount > 0)
                    <span class="badge badge-warning ml-1">{{ $pendingCount }}</span>
                @endif
            </a>
        </li>
    </ul>

    <div class="tab-content">

        <!-- Stock Alerts Tab -->
        <div class="tab-pane fade {{ $activeTab === 'stock-requests-list' ? 'show active' : '' }}">
            <div class="card shadow-sm mb-3">
                <div class="p-3">
                    <h5 class="mb-3 font-weight-bold text-dark">Low Stock Alerts</h5>

                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="bg-danger text-white font-weight-bold sticky-top">
                                <tr>
                                    <th>Ingredient</th>
                                    <th>Current Stock</th>
                                    <th>Reorder Qty</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $criticalWithoutOrders = $criticalStockIngredients->filter(function ($ingredient) {
                                        return !$ingredient->pending_order_id;
                                    });
                                @endphp

                                @foreach($criticalWithoutOrders as $ingredient)
                                    <tr>
                                        <td>
                                            <i class="fas fa-exclamation-circle text-danger"></i>
                                            <strong>{{ $ingredient->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="text-danger font-weight-bold">
                                                @if(in_array(strtolower($ingredient->unit->abbreviation), ['pcs', 'pieces', 'piece', 'pc']))
                                                    {{ number_format($ingredient->stocks, 0) }}
                                                @else
                                                    {{ number_format($ingredient->stocks, 2) }}
                                                @endif
                                                {{ $ingredient->unit->abbreviation }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($ingredient->stockAlertLevel)
                                                @if(in_array(strtolower($ingredient->unit->abbreviation), ['pcs', 'pieces', 'piece', 'pc']))
                                                    {{ number_format($ingredient->stockAlertLevel->reorder_quantity, 0) }}
                                                @else
                                                    {{ number_format($ingredient->stockAlertLevel->reorder_quantity, 2) }}
                                                @endif
                                                {{ $ingredient->unit->abbreviation }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-danger">
                                                <i class="fas fa-exclamation-circle"></i> Critical
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary"
                                                onclick="confirmCreateOrder({{ $ingredient->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="createStockOrder({{ $ingredient->id }})">
                                                <span wire:loading.remove
                                                    wire:target="createStockOrder({{ $ingredient->id }})">
                                                    <i class="fas fa-plus"></i> Create Order
                                                </span>
                                                <span wire:loading wire:target="createStockOrder({{ $ingredient->id }})">
                                                    <i class="fas fa-spinner fa-spin"></i>
                                                </span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach

                                @php
                                    $lowWithoutOrders = $lowStockIngredients->filter(function ($ingredient) {
                                        return !$ingredient->pending_order_id;
                                    });
                                @endphp

                                @foreach($lowWithoutOrders as $ingredient)
                                    <tr>
                                        <td>
                                            <i class="fas fa-exclamation-triangle text-warning"></i>
                                            <strong>{{ $ingredient->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="text-warning font-weight-bold">
                                                @if(in_array(strtolower($ingredient->unit->abbreviation), ['pcs', 'pieces', 'piece', 'pc']))
                                                    {{ number_format($ingredient->stocks, 0) }}
                                                @else
                                                    {{ number_format($ingredient->stocks, 2) }}
                                                @endif
                                                {{ $ingredient->unit->abbreviation }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($ingredient->stockAlertLevel)
                                                {{ number_format($ingredient->stockAlertLevel->reorder_quantity, 2) }}
                                                {{ $ingredient->unit->abbreviation }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-warning">
                                                <i class="fas fa-exclamation-triangle"></i> Low
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary"
                                                wire:click="createStockOrder({{ $ingredient->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="createStockOrder({{ $ingredient->id }})">
                                                <span wire:loading.remove
                                                    wire:target="createStockOrder({{ $ingredient->id }})">
                                                    <i class="fas fa-plus"></i> Create Order
                                                </span>
                                                <span wire:loading wire:target="createStockOrder({{ $ingredient->id }})">
                                                    <i class="fas fa-spinner fa-spin"></i>
                                                </span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach

                                @if($criticalWithoutOrders->count() == 0 && $lowWithoutOrders->count() == 0)
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                                            <p class="mb-0">No low stock ingredients!</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Orders Tab -->
        <div class="tab-pane fade {{ $activeTab === 'pending-orders-list' ? 'show active' : '' }}">
            <div class="card shadow-sm mb-3">
                <div class="p-3">
                    <h5 class="mb-3 font-weight-bold text-dark">Pending Stock Orders</h5>

                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="bg-warning text-dark font-weight-bold sticky-top">
                                <tr>
                                    <th>Ingredient</th>
                                    <th>Current Stock</th>
                                    <th>Order Quantity</th>
                                    <th width="100">Status</th>
                                    <th width="120">Created</th>
                                    <th class="text-center" width="180">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stockOrders->where('status', 'pending') as $order)
                                    <tr>
                                        <td>
                                            <strong>{{ $order->ingredient->name }}</strong>
                                            @if($order->ingredient->getStockStatus() === 'critical')
                                                <i class="fas fa-exclamation-circle text-danger ml-1"
                                                    title="Critical stock"></i>
                                            @elseif($order->ingredient->getStockStatus() === 'low')
                                                <i class="fas fa-exclamation-triangle text-warning ml-1" title="Low stock"></i>
                                            @endif
                                        </td>
                                        <td>
                                            @if(in_array(strtolower($order->ingredient->unit->abbreviation), ['pcs', 'pieces', 'piece', 'pc']))
                                                {{ number_format($order->ingredient->stocks, 0) }}
                                            @else
                                                {{ number_format($order->ingredient->stocks, 2) }}
                                            @endif
                                            {{ $order->ingredient->unit->abbreviation }}
                                        </td>
                                        <td>
                                            <strong>
                                                @if(in_array(strtolower($order->ingredient->unit->abbreviation), ['pcs', 'pieces', 'piece', 'pc']))
                                                    {{ number_format($order->quantity, 0) }}
                                                @else
                                                    {{ number_format($order->quantity, 2) }}
                                                @endif
                                                {{ $order->ingredient->unit->abbreviation }}
                                            </strong>
                                        </td>
                                        <td>
                                            <span class="badge badge-warning">
                                                <i class="fas fa-clock"></i> Pending
                                            </span>
                                        </td>
                                        <td><small>{{ $order->created_at->format('M d, Y') }}</small></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-success"
                                                wire:click="openReceiveModal({{ $order->id }})"
                                                wire:loading.attr="disabled">
                                                <i class="fas fa-check"></i> Receive
                                            </button>
                                            <a href="{{ route('admin.stock-request.print', $order->ingredient_id) }}"
                                                target="_blank" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p class="mb-0">No pending stock orders.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Receive Stock Modal -->
        @if($showReceiveModal)
            <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">Confirm Received Stock</h5>
                            <button type="button" class="close text-white" wire:click="closeReceiveModal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <strong>Ingredient:</strong> {{ $ingredientName }}
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="font-weight-bold">Ordered Quantity:</label>
                                    <div class="form-control bg-light">
                                        @if(in_array(strtolower($unit), ['pcs', 'pieces', 'piece', 'pc']))
                                            {{ number_format($orderedQuantity, 0) }}
                                        @else
                                            {{ number_format($orderedQuantity, 2) }}
                                        @endif
                                        {{ $unit }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="font-weight-bold text-success">
                                        Received Quantity: <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" id="receivedQuantityInput"
                                        class="form-control @error('receivedQuantity') is-invalid @enderror"
                                        wire:model="receivedQuantity"
                                        min="{{ in_array(strtolower($unit), ['pcs', 'pieces', 'piece', 'pc']) ? '1' : '0.01' }}"
                                        step="{{ in_array(strtolower($unit), ['pcs', 'pieces', 'piece', 'pc']) ? '1' : '0.01' }}"
                                        @if(in_array(strtolower($unit), ['pcs', 'pieces', 'piece', 'pc']))
                                        oninput="this.value = Math.floor(Math.abs(this.value))" @endif
                                        placeholder="Enter received quantity">
                                    @error('receivedQuantity')
                                        <div class="text-danger small mt-1">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </div>
                                    @enderror
                                    @if(in_array(strtolower($unit), ['pcs', 'pieces', 'piece', 'pc']))
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="font-weight-bold">
                                    Expiration Date: <span class="text-danger">*</span>
                                </label>
                                <input type="date" id="expirationDateInput"
                                    class="form-control @error('expirationDate') is-invalid @enderror"
                                    wire:model="expirationDate" min="{{ date('Y-m-d') }}">
                                @error('expirationDate')
                                    <div class="text-danger small mt-1">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeReceiveModal">
                                Close
                            </button>
                            <button type="button" class="btn btn-success" onclick="confirmReceiveStock()"
                                wire:loading.attr="disabled" wire:target="confirmReceive">
                                <span wire:loading.remove wire:target="confirmReceive">
                                    <i class="fas fa-check"></i> Confirm
                                </span>
                                <span wire:loading wire:target="confirmReceive">
                                    <i class="fas fa-spinner fa-spin"></i> Processing...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('stock-received-success', () => {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'New Stock Added!',
                showConfirmButton: false,
                timer: 3000
            });
        });
    });

    function confirmCreateOrder(ingredientId) {
        Swal.fire({
            title: 'Create Stock Order?',
            text: 'Are you sure you want to create a stock order for this ingredient?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('createStockOrder', ingredientId);
            }
        });
    }

    function confirmReceiveStock() {
        const receivedQty = document.getElementById('receivedQuantityInput').value;
        const expirationDate = document.getElementById('expirationDateInput').value;

        if (!receivedQty || !expirationDate) {
            @this.call('confirmReceive');
            return;
        }

        Swal.fire({
            title: 'Confirm Received Stock?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Confirm',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('confirmReceive');
            }
        });
    }
</script>

@push('scripts')
    <script>
        window.addEventListener('print-stock-request', event => {
            const data = event.detail[0];
            document.getElementById('print-date').textContent = data.date;
            document.getElementById('print-user').textContent = data.requestedBy;
            document.getElementById('print-ingredient').textContent = data.ingredient.name;
            document.getElementById('print-stock').textContent = `${data.ingredient.stocks} ${data.ingredient.unit.abbreviation}`;
            document.getElementById('print-reorder').textContent = `${data.alertLevel.reorder_quantity} ${data.ingredient.unit.abbreviation}`;
            document.getElementById('print-status').textContent = data.order.status;
            document.getElementById('print-quantity').textContent = data.order.quantity + ' ' + data.ingredient.unit.abbreviation;

            const printContent = document.getElementById('printable-stock-request').innerHTML;
            const printWindow = window.open('', '', 'width=800,height=600');
            printWindow.document.write('<html><head><title>Stock Request Preview</title>');
            printWindow.document.write('<style>body{font-family:Arial,sans-serif;color:#333;font-size:14px;padding:20px;} h2{margin-bottom:10px;}</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write(printContent);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        });
    </script>
@endpush
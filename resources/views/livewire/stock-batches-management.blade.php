<div wire:poll.20s>
    <ul class="nav nav-pills mb-3 align-items-center" role="tablist">
        <li class="nav-item">
            <button wire:click="$set('period', 'thisweek')" type="button"
                class="nav-link {{ $period === 'thisweek' ? 'active' : '' }}">
                This Week
            </button>
        </li>
        <li class="nav-item">
            <button wire:click="$set('period', 'lastweek')" type="button"
                class="nav-link {{ $period === 'lastweek' ? 'active' : '' }}">
                Previous Week
            </button>
        </li>
        <li class="nav-item ms-auto border rounded p-1">
            <select wire:model.live="ingredientFilter" class="form-select" style="width: 200px;">
                <option value="all">All Ingredients</option>
                @foreach ($ingredients as $ingredient)
                    <option value="{{ $ingredient->id }}">{{ $ingredient->name }}</option>
                @endforeach
            </select>
        </li>
    </ul>

    @if ($batches->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="fas fa-box-open fa-3x mb-3"></i>
            <p>No stock batches found for this period</p>
        </div>
    @else
        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
            <table class="table table-bordered mb-0">
                <thead class="thead-light" style="position: sticky; top: 0; z-index: 10; background-color: #f8f9fa;">
                    <tr>
                        <th>Batch Code</th>
                        <th>Ingredient</th>
                        <th>Request Qty</th>
                        <th>Received Qty</th>
                        <th>Arrived Date</th>
                        <th>Expiration Date</th>
                        <th width="150" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($batches as $batch)
                        @php
                            $isPieces = in_array(strtolower($batch->unit), ['pcs', 'pieces', 'piece', 'pc']);
                            
                            $formattedCurrentQty = $isPieces ? floor($batch->quantity) : number_format($batch->quantity, 2);
                            
                            $formattedReceivedQty = $batch->original_quantity
                                ? ($isPieces ? floor($batch->original_quantity) : number_format($batch->original_quantity, 2))
                                : $formattedCurrentQty;
                            
                            $formattedRequestQty = $batch->request_quantity
                                ? ($isPieces ? floor($batch->request_quantity) : number_format($batch->request_quantity, 2))
                                : 'N/A';

                            $expirationDate = \Carbon\Carbon::parse($batch->expiration_date);
                            $today = \Carbon\Carbon::now()->startOfDay();
                            $daysUntilExpiry = $today->diffInDays($expirationDate, false);

                            $qtyColorClass = '';
                            $qtyBadge = '';
                            $isLowStock = $batch->original_quantity && $batch->quantity < $batch->original_quantity * 0.3;

                            if ($isLowStock) {
                                $qtyColorClass = 'text-warning font-weight-bold';
                                $qtyBadge = '<small class="badge badge-warning ml-2">Low</small>';
                            }
                        @endphp
                        <tr>
                            <td>
                                <code class="text-dark bg-light px-2 py-1 rounded">
                                    {{ $batch->batch_code ?? 'N/A' }}
                                </code>
                            </td>
                            <td><strong>{{ $batch->ingredient_name }}</strong></td>
                            
                            <td>
                                <span class="font-weight-medium">{{ $formattedRequestQty }}</span>
                                @if($formattedRequestQty !== 'N/A')
                                    <span class="text-muted">{{ $batch->unit }}</span>
                                @endif
                            </td>
                            
                            <td>
                                <span class="font-weight-medium">{{ $formattedReceivedQty }}</span>
                                <span class="text-muted">{{ $batch->unit }}</span>
                                
                                @if ($batch->original_quantity && $batch->quantity < $batch->original_quantity)
                                    <br>
                                    <small class="text-muted">
                                        Current: {{ $formattedCurrentQty }} {{ $batch->unit }}
                                        {!! $qtyBadge !!}
                                    </small>
                                @endif
                            </td>
                            
                            <td>{{ \Carbon\Carbon::parse($batch->arrived_at)->format('M d, Y') }}</td>
                            <td>
                                <div>{{ $expirationDate->format('M d, Y') }}</div>
                                @if ($daysUntilExpiry <= 30)
                                    <small class="text-{{ $daysUntilExpiry <= 7 ? 'danger' : 'warning' }} font-weight-bold">
                                        ({{ $daysUntilExpiry }} day{{ $daysUntilExpiry !== 1 ? 's' : '' }} left)
                                    </small>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button
                                        wire:click="editBatch({{ $batch->id }}, '{{ $batch->batch_code ?? 'N/A' }}', '{{ $batch->arrived_at }}', '{{ $batch->expiration_date }}')"
                                        class="btn btn-sm btn-primary" style="min-width: 70px;">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button
                                        onclick="confirmExpireBatch({{ $batch->id }}, '{{ $batch->batch_code ?? $batch->ingredient_name }}')"
                                        class="btn btn-sm btn-danger" style="min-width: 80px;">
                                        <i class="fas fa-ban"></i> Expire
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-3 d-flex justify-content-center batch-pagination">
            {{ $batches->links() }}
        </div>
    @endif

    {{-- Edit Modal --}}
    @if ($showEditModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h6 class="modal-title mb-0">Edit Batch: {{ $editBatchCode }}</h6>
                        <button type="button" class="close" wire:click="closeEditModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form id="editBatchForm">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="small">Arrived Date <span class="text-danger">*</span></label>
                                <input type="date" wire:model="editArrivedAt" class="form-control form-control-sm" required>
                                @error('editArrivedAt')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-0">
                                <label class="small">Expiration Date <span class="text-danger">*</span></label>
                                <input type="date" wire:model="editExpiryDate" class="form-control form-control-sm" required
                                    min="{{ date('Y-m-d') }}">
                                @error('editExpiryDate')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer p-2">
                            <button type="button" class="btn btn-secondary btn-sm" wire:click="closeEditModal">
                                Cancel
                            </button>
                            <button type="button" class="btn btn-warning btn-sm" onclick="confirmUpdateBatch()">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Custom Styles --}}
    <style>
        .batch-pagination p {
            display: none !important;
        }


        .alert {
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .gap-2 {
            gap: 0.5rem;
        }
    </style>

    @script
    <script>
        window.confirmUpdateBatch = function () {
            const arrivedAt = $wire.editArrivedAt;
            const expiryDate = $wire.editExpiryDate;
            const batchCode = $wire.editBatchCode;

            if (!arrivedAt || !expiryDate) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Please fill in all required fields',
                    timer: 2000
                });
                return;
            }

            const arrivedFormatted = new Date(arrivedAt).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
            const expiryFormatted = new Date(expiryDate).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });

            Swal.fire({
                title: 'Update Batch?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Confirm',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.updateBatch();
                }
            });
        };

        window.confirmExpireBatch = function (batchId, batchName) {
            Swal.fire({
                title: 'Mark as Expired?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Confirm',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.expireBatch(batchId);
                }
            });
        };

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('batch-updated', () => {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Batch has been updated successfully!',
                    showConfirmButton: false,
                    timer: 3000
                });
            });

            Livewire.on('batch-expired', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Batch Expired!',
                    text: 'Batch has been marked as expired successfully',
                    timer: 3000,
                    showConfirmButton: false
                });
            });
        });

        Livewire.on('batch-error', (event) => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: event.message || 'An error occurred'
            });
        });
    </script>
    @endscript
</div>
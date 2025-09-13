@include('admin.layouts.header')
@include('admin.layouts.sidebar')

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h1 class="h3 mb-0 text-gray-800">Stock Management</h1>
        </nav>

        <div class="container-fluid">

            <div class="mb-3">
                <a href="{{ route('admin.stock_management') }}"
                    class="btn btn-sm {{ !request()->has('show_deleted') ? 'btn-primary' : 'btn-outline-primary' }}">
                    Active Items
                </a>
                <a href="{{ route('admin.stock_management', ['show_deleted' => true]) }}"
                    class="btn btn-sm {{ request()->has('show_deleted') ? 'btn-secondary' : 'btn-outline-secondary' }}">
                    Deleted Items
                </a>
            </div>

            <div class="card mt-2" style="max-width: 100%;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Stock {{ request()->has('show_deleted') ? '(Deleted Items)' : '' }}</h5>
                    @if(!request()->has('show_deleted'))
                        <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#addStockModal">Add
                            Stock</button>
                    @endif
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-sm text-start">
                        <thead class="thead-light">
                            <tr>
                                <th>Stock Name</th>
                                <th>Quantity (KG)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stocks as $stock)
                                <tr>
                                    <td>{{ $stock->stock_name }}</td>
                                    <td>{{ $stock->stock_quantity }}</td>
                                    <td>
                                        @if($stock->deleted_at)
                                            <button type="button" class="btn btn-sm btn-success"
                                                onclick="showRestoreModal({{ $stock->id }}, '{{ addslashes($stock->stock_name) }}')"
                                                title="Restore">
                                                <i class="fas fa-undo"></i>
                                            </button>

                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="showForceDeleteModal({{ $stock->id }}, '{{ addslashes($stock->stock_name) }}')"
                                                title="Delete Permanently">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <a href="#" title="Edit" data-toggle="modal"
                                                data-target="#editStockModal{{ $stock->id }}"
                                                style="all: unset; cursor: pointer;">
                                                <i class="fas fa-edit text-primary"></i>
                                            </a>

                                            <button type="button" class="btn btn-sm btn-link p-0 ml-2"
                                                onclick="showDeleteModal({{ $stock->id }}, '{{ addslashes($stock->stock_name) }}')"
                                                title="Delete" style="all: unset; cursor: pointer;">
                                                <i class="fas fa-trash text-danger"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">
                                        {{ request()->has('show_deleted') ? 'No deleted items found' : 'No stock items found' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Replace your addStockModal with this enhanced version -->
            <div class="modal fade" id="addStockModal" tabindex="-1" role="dialog" aria-labelledby="addStockModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="addStockModalLabel">
                                <i class="fas fa-plus"></i> Add New Stock Item
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('admin.storeStock') }}" method="POST" id="addStockForm">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="stock_name">Stock Name</label>
                                    <input type="text" name="stock_name" id="stock_name" class="form-control" required
                                        value="{{ old('stock_name') }}">
                                    @error('stock_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="stock_quantity">Quantity (KG)</label>
                                    <input type="number" name="stock_quantity" id="stock_quantity" class="form-control"
                                        step="0.01" min="0" required value="{{ old('stock_quantity') }}">
                                    @error('stock_quantity')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                    <small class="form-text text-muted">Enter quantity in kilograms</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Add Stock Item
                                </button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @foreach ($stocks as $stock)
                @if(!$stock->deleted_at)
                    <div class="modal fade" id="editStockModal{{ $stock->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="{{ route('admin.updateStock', $stock->id) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">Edit Stock</h5>
                                        <button type="button" class="close text-white"
                                            data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <label>Stock Name</label>
                                        <input type="text" name="stock_name" value="{{ $stock->stock_name }}"
                                            class="form-control" required>

                                        <label class="mt-2">Quantity (KG)</label>
                                        <input type="number" name="stock_quantity" value="{{ $stock->stock_quantity }}"
                                            class="form-control" step="0.01" min="0" required>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            @endforeach

            <div class="modal fade" id="deleteConfirmModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h5>
                            <button type="button" class="close text-white"
                                data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete <strong><span id="deleteItemName"></span></strong> from
                                the list?</p>
                        </div>
                        <div class="modal-footer bg-light">
                            <form id="deleteForm" method="POST">@csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i>
                                    Delete</button>
                            </form>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="restoreConfirmModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title"><i class="fas fa-undo"></i> Confirm Restore</h5>
                            <button type="button" class="close text-white"
                                data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to restore <strong><span id="restoreItemName"></span></strong>?</p>
                        </div>
                        <div class="modal-footer bg-light">
                            <form id="restoreForm" method="POST">@csrf @method('PATCH')
                                <button type="submit" class="btn btn-success"><i class="fas fa-undo"></i>
                                    Restore</button>
                            </form>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="forceDeleteConfirmModal" tabindex="-1">
                <div class="modal-dialog" role="document">
                    <div class="modal-content border-danger">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title"><i class="fas fa-exclamation-circle"></i> CRITICAL WARNING
                            </h5>
                            <button type="button" class="close text-white"
                                data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body bg-light">
                            <div class="alert alert-danger border-danger">
                                <p class="text-center">Are you sure you want to <strong class="text-danger">permanently
                                        delete table </strong>
                                    <span class="badge badge-danger" id="forceDeleteItemName"></span> ?
                                </p>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <form id="forceDeleteForm" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger text-white border-danger">Delete
                                    Permanently</button>
                            </form>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.layouts.script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {

        // Function to safely show modals with proper error handling
        window.showDeleteModal = function (id, itemName) {
            try {
                const deleteItemNameElement = document.getElementById('deleteItemName');
                const deleteFormElement = document.getElementById('deleteForm');

                if (deleteItemNameElement && deleteFormElement) {
                    deleteItemNameElement.textContent = itemName;
                    deleteFormElement.action = "{{ url('deletestock') }}/" + id;
                    $('#deleteConfirmModal').modal('show');
                } else {
                    console.error('Delete modal elements not found');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to show delete confirmation. Please refresh the page.',
                        toast: true,
                        position: 'top',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            } catch (error) {
                console.error('Error showing delete modal:', error);
            }
        };

        window.showRestoreModal = function (id, itemName) {
            try {
                const restoreItemNameElement = document.getElementById('restoreItemName');
                const restoreFormElement = document.getElementById('restoreForm');

                if (restoreItemNameElement && restoreFormElement) {
                    restoreItemNameElement.textContent = itemName;
                    restoreFormElement.action = "{{ url('restorestock') }}/" + id;
                    $('#restoreConfirmModal').modal('show');
                } else {
                    console.error('Restore modal elements not found');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to show restore confirmation. Please refresh the page.',
                        toast: true,
                        position: 'top',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            } catch (error) {
                console.error('Error showing restore modal:', error);
            }
        };

        window.showForceDeleteModal = function (id, itemName) {
            try {
                const forceDeleteItemNameElement = document.getElementById('forceDeleteItemName');
                const forceDeleteFormElement = document.getElementById('forceDeleteForm');

                if (forceDeleteItemNameElement && forceDeleteFormElement) {
                    forceDeleteItemNameElement.textContent = itemName;
                    forceDeleteFormElement.action = "{{ url('forcedeletestock') }}/" + id;
                    $('#forceDeleteConfirmModal').modal('show');
                } else {
                    console.error('Force delete modal elements not found');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to show permanent delete confirmation. Please refresh the page.',
                        toast: true,
                        position: 'top',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            } catch (error) {
                console.error('Error showing force delete modal:', error);
            }
        };

        // If there are validation errors, reopen the add modal
        @if ($errors->any())
            $('#addStockModal').modal('show');
        @endif

        // Clear form when add modal is hidden
        $('#addStockModal').on('hidden.bs.modal', function () {
            try {
                $(this).find('form')[0].reset();
            } catch (error) {
                console.error('Error resetting form:', error);
            }
        });

        // Handle success messages with SweetAlert2
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: {!! json_encode(session('success')) !!},
                toast: true,
                position: 'top',
                timer: 3000,
                showConfirmButton: false,
                background: '#d4edda',
                color: '#155724'
            });
        @endif

        // Handle general error messages
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: {!! json_encode(session('error')) !!},
                toast: true,
                position: 'top',
                timer: 4000,
                showConfirmButton: false,
                background: '#f8d7da',
                color: '#721c24'
            });
        @endif

        // Handle specific validation errors for duplicate stock names
        @if($errors->has('stock_name'))
            Swal.fire({
                icon: 'error',
                title: 'Duplicate Stock Item',
                text: {!! json_encode($errors->first('stock_name')) !!},
                toast: true,
                position: 'top',
                timer: 5000,
                showConfirmButton: false,
                background: '#f8d7da',
                color: '#721c24'
            });
        @endif

            // Handle other validation errors
            @if($errors->any() && !$errors->has('stock_name'))
                let errorMessages = [];
                @foreach($errors->all() as $error)
                    errorMessages.push({!! json_encode($error) !!});
                @endforeach

                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Errors',
                    html: errorMessages.join('<br>'),
                    toast: true,
                    position: 'top',
                    timer: 5000,
                    showConfirmButton: false,
                    background: '#fff3cd',
                    color: '#856404'
                });
            @endif

        // Form submission confirmation for add stock
        $('form[action="{{ route('admin.storeStock') }}"]').on('submit', function (e) {
            const submitButton = $(this).find('button[type="submit"]');
            submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');

            // Re-enable button after 3 seconds in case of issues
            setTimeout(function () {
                submitButton.prop('disabled', false).html('<i class="fas fa-save"></i> Add');
            }, 3000);
        });

        // Form submission confirmation for update stock
        $('form[action*="updateStock"]').on('submit', function (e) {
            const submitButton = $(this).find('button[type="submit"]');
            submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');

            // Re-enable button after 3 seconds in case of issues
            setTimeout(function () {
                submitButton.prop('disabled', false).html('<i class="fas fa-save"></i> Update');
            }, 3000);
        });

        // Auto-focus on stock name input when add modal opens
        $('#addStockModal').on('shown.bs.modal', function () {
            $('input[name="stock_name"]').focus();
        });

        // Format quantity inputs to 2 decimal places on blur
        $('input[name="stock_quantity"]').on('blur', function () {
            if (this.value && !isNaN(this.value)) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });

        // Prevent negative values in quantity inputs
        $('input[name="stock_quantity"]').on('input', function () {
            if (this.value < 0) {
                this.value = 0;
            }
        });

        // Validate unusually high quantities (over 10000 KG)
        $('input[name="stock_quantity"]').on('blur', function () {
            if (this.value && parseFloat(this.value) > 10000) {
                Swal.fire({
                    icon: 'warning',
                    title: 'High Quantity Alert',
                    text: 'You entered ' + this.value + ' KG. Are you sure this quantity is correct?',
                    toast: true,
                    position: 'top',
                    timer: 4000,
                    showConfirmButton: false,
                    background: '#fff3cd',
                    color: '#856404'
                });
            }
        });

        // Capitalize first letter of stock name
        $('input[name="stock_name"]').on('blur', function () {
            if (this.value) {
                this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
            }
        });

    });
</script>
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

            <div class="modal fade" id="addStockModal" tabindex="-1">
                <div class="modal-dialog">
                    <form action="{{ route('admin.storeStock') }}" method="POST" id="addStockForm">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">Add New Stock Item</h5>
                                <button type="button" class="close text-white"
                                    data-dismiss="modal"><span>&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <label>Stock Name</label>
                                <input type="text" name="stock_name" class="form-control" required>

                                <label class="mt-2">Quantity (KG)</label>
                                <input type="number" name="stock_quantity" class="form-control" step="0.01" min="0"
                                    required>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Add</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </form>
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
                <div class="modal-dialog">
                    <div class="modal-content border-danger">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title"><i class="fas fa-exclamation-circle"></i> Permanent Delete Warning
                            </h5>
                            <button type="button" class="close text-white"
                                data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body bg-light">
                            <div class="alert alert-danger border-danger">
                                <h6 class="text-danger"><strong>⚠️ CRITICAL WARNING</strong></h6>
                                <p class="text-center">Are you sure you want to <strong class="text-danger">permanently
                                        delete</strong>
                                    <span class="badge badge-danger" id="forceDeleteItemName"></span> ?
                                </p>
                            </div>

                        </div>
                        <div class="modal-footer bg-danger">
                            <form id="forceDeleteForm" method="POST">@csrf @method('DELETE')
                                <button type="submit" class="btn btn-light text-danger border-danger"><i
                                        class="fas fa-trash"></i> Delete Permanently</button>
                            </form>
                            <button type="button" class="btn btn-outline-light" data-dismiss="modal">Cancel</button>
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
        window.showDeleteModal = function (id, itemName) {
            $('#deleteItemName').text(itemName);
            $('#deleteForm').attr('action', "{{ url('deletestock') }}/" + id);
            $('#deleteConfirmModal').modal('show');
        };

        window.showRestoreModal = function (id, itemName) {
            $('#restoreItemName').text(itemName);
            $('#restoreForm').attr('action', "{{ url('restorestock') }}/" + id);
            $('#restoreConfirmModal').modal('show');
        };

        window.showForceDeleteModal = function (id, itemName) {
            $('#forceDeleteItemName').text(itemName);
            $('#forceDeleteForm').attr('action', "{{ url('forcedeletestock') }}/" + id);
            $('#forceDeleteConfirmModal').modal('show');
        };

        @if(session('success'))
            $('#successMessage').text("{{ session('success') }}");
            $('#successModal').modal('show');
        @endif
});

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: "{{ session('success') }}",
            toast: true,
            position: 'top',
            timer: 2000,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: "{{ session('error') }}",
            toast: true,
            position: 'top',
            timer: 2000,
            showConfirmButton: false
        });
    @endif
</script>
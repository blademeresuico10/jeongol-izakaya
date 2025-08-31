@include('admin.layouts.header')
@include('admin.layouts.sidebar')

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h1 class="h3 mb-0 text-gray-800">Menu Management</h1>
        </nav>
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="mb-3">
                <a href="{{ route('admin.menu_management') }}"
                    class="btn btn-sm {{ !request()->has('show_deleted') ? 'btn-primary' : 'btn-outline-primary' }}">
                    Active Items
                </a>
                <a href="{{ route('admin.menu_management', ['show_deleted' => true]) }}"
                    class="btn btn-sm {{ request()->has('show_deleted') ? 'btn-secondary' : 'btn-outline-secondary' }}">
                    Deleted Items
                </a>
            </div>

            <div class="card mt-2" style="max-width: 100%;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Menu {{ request()->has('show_deleted') ? '(Deleted Items)' : '' }}</h5>
                    @if(!request()->has('show_deleted'))
                        <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#addMenuModal">Add
                            Menu</button>
                    @endif
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-sm text-start">
                        <thead class="thead-light">
                            <tr>
                                <th>Menu Item</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($menu as $item)
                                <tr>
                                    <td>{{ $item->menu_item }}</td>
                                    <td>₱{{ number_format($item->price, 2) }}</td>
                                    <td>
                                        @if($item->deleted_at)
                                            <button type="button" class="btn btn-sm btn-success"
                                                onclick="showRestoreModal({{ $item->id }}, '{{ addslashes($item->menu_item) }}')"
                                                title="Restore">
                                                <i class="fas fa-undo"></i>
                                            </button>

                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="showForceDeleteModal({{ $item->id }}, '{{ addslashes($item->menu_item) }}')"
                                                title="Delete Permanently">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <a href="#" title="Edit" data-toggle="modal"
                                                data-target="#editMenuModal{{ $item->id }}"
                                                style="all: unset; cursor: pointer;">
                                                <i class="fas fa-edit text-primary"></i>
                                            </a>

                                            <button type="button" class="btn btn-sm btn-link p-0 ml-2"
                                                onclick="showDeleteModal({{ $item->id }}, '{{ addslashes($item->menu_item) }}')"
                                                title="Delete" style="all: unset; cursor: pointer;">
                                                <i class="fas fa-trash text-danger"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">
                                        {{ request()->has('show_deleted') ? 'No deleted items found' : 'No menu items found' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

           <div class="modal fade" id="addMenuModal" tabindex="-1" role="dialog" aria-labelledby="addMenuModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form action="{{ route('storeMenu') }}" method="POST">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addMenuModalLabel">Add New Menu Item</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <label>Menu Item</label>
                                <input type="text" name="menu_item" class="form-control" required>

                                <label class="mt-2">Price</label>
                                <input type="number" name="price" class="form-control" step="0.01" min="0" required>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Add</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-exclamation-triangle"></i> Confirm Delete
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-danger">
                                <i class="fas fa-info-circle"></i> This item will be moved to deleted items and can be restored later.
                            </div>
                            <p>Are you sure you want to delete <strong><span id="deleteItemName"></span></strong>?</p>
                        </div>
                        <div class="modal-footer bg-light">
                            <form id="deleteForm" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="restoreConfirmModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-undo"></i> Confirm Restore
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success">
                                <i class="fas fa-info-circle"></i> This item will be restored to the active menu.
                            </div>
                            <p>Are you sure you want to restore <strong><span id="restoreItemName"></span></strong>?</p>
                        </div>
                        <div class="modal-footer bg-light">
                            <form id="restoreForm" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-undo"></i> Restore
                                </button>
                            </form>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="forceDeleteConfirmModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content border-danger">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-exclamation-circle"></i> Permanent Delete Warning
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body bg-light">
                            <div class="alert alert-danger border-danger">
                                <h6 class="text-danger"><strong>⚠️ CRITICAL WARNING</strong></h6>
                                <p class="mb-0">This action cannot be undone! The item will be completely removed from the database.</p>
                            </div>
                            <p class="text-center">
                                Are you sure you want to <strong class="text-danger">permanently delete</strong> 
                                <span class="badge badge-danger" id="forceDeleteItemName"></span>?
                            </p>
                        </div>
                        <div class="modal-footer bg-danger">
                            <form id="forceDeleteForm" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-light text-danger border-danger">
                                    <i class="fas fa-trash"></i> Delete Permanently
                                </button>
                            </form>
                            <button type="button" class="btn btn-outline-light" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="successModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-check-circle"></i> Success
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p id="successMessage"></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>

            @foreach ($menu as $item)
                @if(!$item->deleted_at)
                    <div class="modal fade" id="editMenuModal{{ $item->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="editMenuModalLabel{{ $item->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('admin.updatemenu', $item->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="editMenuModalLabel{{ $item->id }}">
                                            <i class="fas fa-edit"></i> Edit Menu Item
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <label>Menu Item</label>
                                        <input type="text" name="menu_item" value="{{ $item->menu_item }}" class="form-control"
                                            required>

                                        <label class="mt-2">Price</label>
                                        <input type="number" name="price" value="{{ $item->price }}" class="form-control"
                                            step="0.01" min="0" required>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update
                                        </button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>

@include('admin.layouts.script')

<script>
    $(document).ready(function () {
        window.showDeleteModal = function (id, itemName) {
            const deleteItemNameElement = document.getElementById('deleteItemName');
            const deleteFormElement = document.getElementById('deleteForm');

            if (deleteItemNameElement && deleteFormElement) {
                deleteItemNameElement.textContent = itemName;
                deleteFormElement.action = "{{ url('admin/deletemenu') }}/" + id;
                $('#deleteConfirmModal').modal('show');
            } else {
                console.error('Delete modal elements not found');
            }
        };

        window.showRestoreModal = function (id, itemName) {
            const restoreItemNameElement = document.getElementById('restoreItemName');
            const restoreFormElement = document.getElementById('restoreForm');

            if (restoreItemNameElement && restoreFormElement) {
                restoreItemNameElement.textContent = itemName;
                restoreFormElement.action = "{{ url('admin/restoremenu') }}/" + id;
                $('#restoreConfirmModal').modal('show');
            } else {
                console.error('Restore modal elements not found');
            }
        };

        window.showForceDeleteModal = function (id, itemName) {
            const forceDeleteItemNameElement = document.getElementById('forceDeleteItemName');
            const forceDeleteFormElement = document.getElementById('forceDeleteForm');

            if (forceDeleteItemNameElement && forceDeleteFormElement) {
                forceDeleteItemNameElement.textContent = itemName;
                forceDeleteFormElement.action = "{{ url('admin/forcedeletemenu') }}/" + id;
                $('#forceDeleteConfirmModal').modal('show');
            } else {
                console.error('Force delete modal elements not found:', {
                    forceDeleteItemNameElement,
                    forceDeleteFormElement
                });
            }
        };

        window.confirmAddMenu = function () {
            const menuItemElement = document.getElementById('add_menu_item');
            const priceElement = document.getElementById('add_price');

            if (menuItemElement && priceElement) {
                const menuItem = menuItemElement.value;
                const price = priceElement.value;

                if (menuItem && price) {
                    const confirmMenuItemElement = document.getElementById('confirmMenuItem');
                    const confirmPriceElement = document.getElementById('confirmPrice');

                    if (confirmMenuItemElement && confirmPriceElement) {
                        confirmMenuItemElement.textContent = menuItem;
                        confirmPriceElement.textContent = parseFloat(price).toFixed(2);

                        $('#addMenuModal').modal('hide');
                        $('#addConfirmModal').modal('show');
                    } else {
                        console.error('Confirmation modal elements not found');
                    }
                } else {
                    alert('Please fill in all fields!');
                }
            } else {
                console.error('Add menu form elements not found');
            }
        };

        window.submitAddMenu = function () {
            const addMenuFormElement = document.getElementById('addMenuForm');
            if (addMenuFormElement) {
                $('#addConfirmModal').modal('hide');
                addMenuFormElement.submit();
            } else {
                console.error('Add menu form not found');
            }
        };

        window.showSuccessMessage = function (message) {
            const successMessageElement = document.getElementById('successMessage');
            if (successMessageElement) {
                successMessageElement.textContent = message;
                $('#successModal').modal('show');
            } else {
                console.error('Success message element not found');
            }
        };

        @if(session('success'))
            showSuccessMessage("{{ session('success') }}");
        @endif

        const addMenuModal = $('#addMenuModal');
        if (addMenuModal.length) {
            addMenuModal.on('hidden.bs.modal', function () {
                const addMenuFormElement = document.getElementById('addMenuForm');
                if (addMenuFormElement) {
                    addMenuFormElement.reset();
                }
            });
        }

    });
</script>
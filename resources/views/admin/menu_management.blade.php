@include('admin.layouts.header')
@include('admin.layouts.sidebar')

<div id="content-wrapper" class="d-flex flex-column h-screen overflow-y-auto">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h1 class="h3 mb-0 text-gray-800">Menu Management</h1>
        </nav>
        <div class="container-fluid">


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
                    <h5 class="mb-0">
                        Menu {{ request()->has('show_deleted') ? '(Deleted Items)' : '' }}
                    </h5>
                    @if(!request()->has('show_deleted'))
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#addMenuModal">
                                Add Menu
                            </button>
                            <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#menuIngredientsModal">
                                Menu Ingredients
                            </button>
                        </div>
                    @endif
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-sm text-start">
                        <thead class="thead-light">
                            <tr>
                                <th>Menu Image</th>
                                <th>Menu Item</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($menu as $item)
                                <tr>
                                    <td>
                                        <div class="mb-3 text-center">
                                            @if ($item->image)
                                                <img src="{{ asset('storage/jeongol_menu/' . $item->image) }}"
                                                    alt="{{ $item->menu_item }}" width="100" height="100"
                                                    style="object-fit: cover; border-radius: 80%;">
                                            @else
                                                <span class="text-muted">No Picture</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $item->menu_item }}</td>
                                    <td>
                                        Regular: ₱{{ number_format($item->regular_price, 2) }}
                                    </td>
                                    <td>{{ $item->status }}</td>

                                    <td>
                                        @if ($item->deleted_at)
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
                                    <td colspan="5" class="text-center">
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
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="addMenuModalLabel">
                                <i class="fas fa-plus"></i> Add New Menu Item
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('storeMenu') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="menu_item">Menu Item</label>
                                    <input type="text" name="menu_item" id="menu_item" class="form-control" required
                                        value="{{ old('menu_item') }}">
                                    @error('menu_item')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="category">Category</label>
                                    <select name="category" id="category" class="form-control" required>
                                        <option value="">Select Category</option>
                                        <option value="main" {{ old('category') == 'main' ? 'selected' : '' }}>Main
                                        </option>
                                        <option value="add_ons" {{ old('category') == 'add_ons' ? 'selected' : '' }}>
                                            Add-ons</option>
                                    </select>
                                    @error('category')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="image">Menu Item Image <span class="text-danger">*</span></label>
                                    <input type="file" name="image" id="image" class="form-control-file" required
                                        accept="image/jpeg,image/png,image/jpg,image/gif">

                                    @error('image')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="regular_price">Regular Price</label>
                                    <input type="number" name="regular_price" id="regular_price" class="form-control"
                                        step="0.01" min="0" required value="{{ old('regular_price') }}">
                                    @error('regular_price')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Customer Discount Available</label>
                                    <select name="has_customer_discount" class="form-control" required>
                                        <option value="1" {{ old('has_customer_discount', 1) == 1 ? 'selected' : '' }}>Yes
                                        </option>
                                        <option value="0" {{ old('has_customer_discount', 1) == 0 ? 'selected' : '' }}>No
                                        </option>
                                    </select>
                                    <small class="form-text text-muted">Whether this item is eligible for
                                        student/government discounts</small>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Add Menu Item
                                </button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="menuIngredientsModal" tabindex="-1" role="dialog"
                aria-labelledby="menuIngredientsLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title" id="menuIngredientsLabel">
                                <i class="fas fa-utensils"></i> Menu Ingredients
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
                            <div id="ingredientsContent"></div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success" id="saveIngredientsBtn">Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Ingredient Modal -->
            <div class="modal fade" id="addIngredientModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-plus"></i> Add Ingredient to <span id="menuNameLabel"></span>
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="addIngredientForm">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="font-weight-bold">Ingredient</label>
                                    <select id="ingredientSelect" class="form-control" required>
                                        <option value="">Select an ingredient</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Quantity<span id="unitLabel"
                                            class="text-muted"></span></label>
                                    <input type="number" id="ingredientQty" class="form-control" min="1" step="any"
                                        placeholder="Enter quantity" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Add
                                </button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
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
                            <p>Are you sure you want to delete <strong><span id="deleteItemName"></span></strong> from
                                the list?</p>

                        </div>
                        <div class="modal-footer bg-light">
                            <form id="deleteForm" method="POST">
                                @method('DELETE')
                                @csrf
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
                                <i class="fas fa-exclamation-circle"></i> CRITICAL WARNING
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body bg-light">
                            <div class="alert alert-danger border-danger">
                                <p class="text-center">
                                    Are you sure you want to <strong class="text-danger">permanently delete</strong>
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
                                        <div class="form-group">
                                            <label>Menu Item</label>
                                            <input type="text" name="menu_item" value="{{ $item->menu_item }}"
                                                class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label>Category</label>
                                            <select name="category" class="form-control" required>
                                                <option value="main" {{ $item->category == 'main' ? 'selected' : '' }}>Main Dishes
                                                </option>
                                                <option value="add_ons" {{ $item->category == 'add_ons' ? 'selected' : '' }}>
                                                    Add-ons</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Regular Price</label>
                                            <input type="number" name="regular_price" value="{{ $item->regular_price }}"
                                                class="form-control" step="0.01" min="0" required>
                                        </div>

                                        <div class="form-group">
                                            <label>Student Price (Optional)</label>
                                            <input type="number" name="student_price" value="{{ $item->student_price }}"
                                                class="form-control" step="0.01" min="0">
                                        </div>

                                        <div class="form-group">
                                            <label>Gov't Employee Price (Optional)</label>
                                            <input type="number" name="govt_employee_price"
                                                value="{{ $item->govt_employee_price }}" class="form-control" step="0.01"
                                                min="0">
                                        </div>

                                        <div class="form-group">
                                            <label>Customer Discount Available</label>
                                            <select name="has_customer_discount" class="form-control" required>
                                                <option value="1" {{ $item->has_customer_discount ? 'selected' : '' }}>Yes
                                                </option>
                                                <option value="0" {{ !$item->has_customer_discount ? 'selected' : '' }}>No
                                                </option>
                                            </select>
                                            <small class="form-text text-muted">Whether this item is eligible for
                                                student/government discounts</small>
                                        </div>
                                        <label>Status</label>
                                        <select name="status" class="form-control" required>
                                            <option value="Active" {{ $item->status == 'Active' ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="Blocked" {{ $item->status == 'Blocked' ? 'selected' : '' }}>Block
                                            </option>
                                        </select>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

    window.showDeleteModal = function (id, itemName) {
        try {
            const deleteItemNameElement = document.getElementById('deleteItemName');
            const deleteFormElement = document.getElementById('deleteForm');

            if (deleteItemNameElement && deleteFormElement) {
                deleteItemNameElement.textContent = itemName;
                deleteFormElement.action = "{{ url('deletemenu') }}/" + id;
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
                restoreFormElement.action = "{{ url('restoremenu') }}/" + id;
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
                forceDeleteFormElement.action = "{{ url('forcedeletemenu') }}/" + id;
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

    $(document).ready(function () {
        let currentMenuId = null;

        $('#menuIngredientsModal').on('show.bs.modal', function () {
            const content = document.getElementById('ingredientsContent');
            content.innerHTML = `
            <div class="text-center py-3">
                <i class="fas fa-spinner fa-spin fa-2x text-info"></i>
                <p class="mt-2">Loading ingredients...</p>
            </div>
        `;

            fetch("{{ route('admin.menu_ingredients') }}")
                .then(res => res.json())
                .then(data => {
                    if (!data.menus.length) {
                        content.innerHTML = `<p class="text-center text-muted">No menus found.</p>`;
                        return;
                    }

                    let html = '';
                    data.menus.forEach(menu => {
                        html += `
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 font-weight-bold">${menu.menu_item}</h6>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Ingredient</th>
                                            <th width="150">Quantity(grams)</th>
                                            <th width="80">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                    `;

                        const ingList = data.ingredients[menu.id] || [];

                        if (ingList.length === 0) {
                            html += `
                            <tr>
                                <td colspan="3" class="text-center text-muted py-2">
                                    No ingredients added yet
                                </td>
                            </tr>
                        `;
                        } else {
                            ingList.forEach(ing => {
                                html += `
                                <tr>
                                    <td class="align-middle">${ing.ingredient_name}</td>
                                    <td>
                                        <input type="number" 
                                               class="form-control form-control-sm ingredient-qty"
                                               data-id="${ing.id}" 
                                               value="${ing.quantity}"
                                               min="0.01"
                                               step="any">
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" 
                                                class="btn btn-sm btn-danger removeIngredientBtn" 
                                                data-id="${ing.id}"
                                                title="Remove">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                            });
                        }

                        html += `
                                    </tbody>
                                </table>
                                <div class="card-footer bg-light">
                                    <button type="button" 
                                            class="btn btn-sm btn-success addIngredientBtn" 
                                            data-menu-id="${menu.id}"
                                            data-menu-name="${menu.menu_item}">
                                        <i class="fas fa-plus"></i> Add Ingredient
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    });

                    content.innerHTML = html;
                })
                .catch(err => {
                    console.error('Error fetching ingredients:', err);
                    content.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> 
                        Failed to load menu ingredients. Please try again.
                    </div>
                `;
                });
        });

        $('#saveIngredientsBtn').on('click', function () {
            const updates = [];
            let hasError = false;

            document.querySelectorAll('.ingredient-qty').forEach(input => {
                const value = parseFloat(input.value);
                if (isNaN(value) || value <= 0) {
                    hasError = true;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                    updates.push({
                        id: input.dataset.id,
                        quantity: value
                    });
                }
            });

            if (hasError) {
                Swal.fire('Invalid Input', 'Please enter valid quantities (greater than 0)', 'warning');
                return;
            }

            if (updates.length === 0) {
                Swal.fire('Warning', 'No ingredients to update', 'warning');
                return;
            }

            Swal.fire({
                title: 'Saving...',
                didOpen: () => Swal.showLoading(),
                allowOutsideClick: false
            });

            fetch('/menu_ingredients/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ updates })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Ingredients updated successfully',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            $('#menuIngredientsModal').modal('hide');
                        });
                    } else {
                        Swal.fire('Error', data.message || 'Failed to update', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error', 'Failed to update ingredients', 'error');
                });
        });

        $(document).on('click', '.removeIngredientBtn', function () {
            const ingredientId = $(this).data('id');
            const row = $(this).closest('tr');

            Swal.fire({
                title: 'Remove Ingredient?',
                text: "This ingredient will be removed from the menu",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, remove it'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/menu_ingredients/${ingredientId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                row.fadeOut(300, function () {
                                    $(this).remove();
                                    const tbody = row.closest('tbody');
                                    if (tbody.find('tr').length === 0) {
                                        tbody.html(`
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-2">
                                            No ingredients added yet
                                        </td>
                                    </tr>
                                `);
                                    }
                                });
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Removed',
                                    text: 'Ingredient removed successfully',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire('Error', data.message || 'Failed to remove', 'error');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error', 'Failed to remove ingredient', 'error');
                        });
                }
            });
        });

        $(document).on('click', '.addIngredientBtn', function () {
            currentMenuId = $(this).data('menu-id');
            const menuName = $(this).data('menu-name');

            $('#menuNameLabel').text(menuName);
            $('#ingredientQty').val('');

            Swal.fire({
                title: 'Loading ingredients...',
                didOpen: () => Swal.showLoading(),
                allowOutsideClick: false
            });

            fetch("{{ route('ingredients.list') }}")
                .then(res => res.json())
                .then(data => {
                    Swal.close();

                    if (!data.ingredients || data.ingredients.length === 0) {
                        Swal.fire('No Ingredients', 'No ingredients available', 'info');
                        return;
                    }

                    const grouped = data.ingredients.reduce((acc, ing) => {
                        if (!acc[ing.category]) acc[ing.category] = [];
                        acc[ing.category].push(ing);
                        return acc;
                    }, {});

                    let options = '<option value="">Select an ingredient</option>';
                    Object.keys(grouped).forEach(category => {
                        options += `<optgroup label="${category.charAt(0).toUpperCase() + category.slice(1)}">`;
                        grouped[category].forEach(ing => {
                            options += `<option value="${ing.id}" data-unit="${ing.unit}">
                            ${ing.name} (Stock: ${ing.stocks} ${ing.unit})
                        </option>`;
                        });
                        options += '</optgroup>';
                    });

                    $('#ingredientSelect').html(options);
                    $('#addIngredientModal').modal('show');
                })
                .catch(err => {
                    console.error('Error loading ingredients:', err);
                    Swal.fire('Error', 'Failed to load ingredients', 'error');
                });
        });

        $(document).on('change', '#ingredientSelect', function () {
            const selected = $(this).find('option:selected');
            const unit = selected.data('unit');
            $('#unitLabel').text(unit ? `(${unit})` : '');
        });

        $('#addIngredientForm').on('submit', function (e) {
            e.preventDefault();

            const ingredientId = $('#ingredientSelect').val();
            const quantity = $('#ingredientQty').val();

            if (!ingredientId) {
                Swal.fire('Error', 'Please select an ingredient', 'warning');
                return;
            }

            if (!quantity || parseFloat(quantity) <= 0) {
                Swal.fire('Error', 'Please enter a valid quantity', 'warning');
                return;
            }

            Swal.fire({
                title: 'Adding ingredient...',
                didOpen: () => Swal.showLoading(),
                allowOutsideClick: false
            });

            fetch(`/menu/${currentMenuId}/add-ingredient`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    ingredient_id: ingredientId,
                    quantity: parseFloat(quantity)
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        $('#addIngredientForm')[0].reset();
                        $('#unitLabel').text('');
                        $('#addIngredientModal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Ingredient added successfully',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        const content = document.getElementById('ingredientsContent');
                        content.innerHTML = `
                    <div class="text-center py-3">
                        <i class="fas fa-spinner fa-spin fa-2x text-info"></i>
                        <p class="mt-2">Refreshing...</p>
                    </div>
                `;

                        fetch("{{ route('admin.menu_ingredients') }}")
                            .then(res => res.json())
                            .then(data => {
                                let html = '';
                                data.menus.forEach(menu => {
                                    html += `
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 font-weight-bold">${menu.menu_item}</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Ingredient</th>
                                                    <th width="150">Quantity</th>
                                                    <th width="80">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                            `;

                                    const ingList = data.ingredients[menu.id] || [];

                                    if (ingList.length === 0) {
                                        html += `
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-2">
                                            No ingredients added yet
                                        </td>
                                    </tr>
                                `;
                                    } else {
                                        ingList.forEach(ing => {
                                            html += `
                                        <tr>
                                            <td class="align-middle">${ing.ingredient_name}</td>
                                            <td>
                                                <input type="number" 
                                                       class="form-control form-control-sm ingredient-qty"
                                                       data-id="${ing.id}" 
                                                       value="${ing.quantity}"
                                                       min="0.01"
                                                       step="any">
                                            </td>
                                            <td class="align-middle">
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger removeIngredientBtn" 
                                                        data-id="${ing.id}"
                                                        title="Remove">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    `;
                                        });
                                    }

                                    html += `
                                            </tbody>
                                        </table>
                                        <div class="card-footer bg-light">
                                            <button type="button" 
                                                    class="btn btn-sm btn-success addIngredientBtn" 
                                                    data-menu-id="${menu.id}"
                                                    data-menu-name="${menu.menu_item}">
                                                <i class="fas fa-plus"></i> Add Ingredient
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `;
                                });
                                content.innerHTML = html;
                            });
                    } else {
                        Swal.fire('Error', data.message || 'Failed to add ingredient', 'error');
                    }
                })
                .catch(err => {
                    console.error('Error adding ingredient:', err);
                    Swal.fire('Error', 'Failed to add ingredient', 'error');
                });
        });

        $('#addIngredientModal').on('hidden.bs.modal', function () {
            $('#addIngredientForm')[0].reset();
            $('#unitLabel').text('');
        });
    });

    const style = document.createElement('style');
    style.textContent = `
    input[type="number"].ingredient-qty::-webkit-outer-spin-button,
    input[type="number"].ingredient-qty::-webkit-inner-spin-button,
    #ingredientQty::-webkit-outer-spin-button,
    #ingredientQty::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    
    input[type="number"].ingredient-qty,
    #ingredientQty {
        -moz-appearance: textfield;
    }
`;
    document.head.appendChild(style);
</script>
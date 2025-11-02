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
                                Menu Servings
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

            <div class="modal fade" id="addMenuModal" data-backdrop="static" data-keyboard="false">
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
                        <form action="{{ route('storeMenu') }}" method="POST" id="addMenuForm"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="menu_item">Menu Item <span class="text-danger">*</span></label>
                                    <input type="text" name="menu_item" id="menu_item" class="form-control" required
                                        minlength="3" value="{{ old('menu_item') }}">
                                    <div>
                                        <small id="menuItemError" class="text-danger text-sm"
                                            style="display: none;"></small>
                                    </div>
                                    @error('menu_item')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="category">Category <span class="text-danger">*</span></label>
                                    <select name="category" id="category" class="form-control" required>
                                        <option value="">Select Category</option>
                                        <option value="main" {{ old('category') == 'main' ? 'selected' : '' }}>Main
                                        </option>
                                        <option value="add_ons" {{ old('category') == 'add_ons' ? 'selected' : '' }}>
                                            Add-ons</option>
                                    </select>
                                    <div>
                                        <small id="categoryError" class="text-danger text-sm"
                                            style="display: none;"></small>
                                    </div>
                                    @error('category')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="image">Menu Item Image <span class="text-danger">*</span></label>
                                    <input type="file" name="image" id="image" class="form-control-file" required
                                        accept="image/jpeg,image/png,image/jpg,image/gif">
                                    <div>
                                        <small id="imageError" class="text-danger text-sm"
                                            style="display: none;"></small>
                                    </div>
                                    @error('image')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="regular_price">Regular Price <span class="text-danger">*</span></label>
                                    <input type="number" name="regular_price" id="regular_price" class="form-control"
                                        step="0.01" min="0.01" required value="{{ old('regular_price') }}">
                                    <div>
                                        <small id="regularPriceError" class="text-danger text-sm"
                                            style="display: none;"></small>
                                    </div>
                                    @error('regular_price')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Customer Discount Available <span class="text-danger">*</span></label>
                                    <select name="has_customer_discount" id="has_customer_discount" class="form-control"
                                        required>
                                        <option value="1" {{ old('has_customer_discount', 1) == 1 ? 'selected' : '' }}>Yes
                                        </option>
                                        <option value="0" {{ old('has_customer_discount', 1) == 0 ? 'selected' : '' }}>No
                                        </option>
                                    </select>
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

            <div class="modal fade" id="postMenuCreationModal" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content border-success">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-check-circle"></i> Menu Added Successfully!
                            </h5>
                        </div>
                        <div class="modal-body text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-utensils fa-3x text-success"></i>
                            </div>
                            <h6 class="font-weight-bold mb-3">
                                "<span id="newMenuName"></span>" has been added to the menu.
                            </h6>

                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-success" id="configureIngredientsBtn">
                                <i class="fas fa-cog"></i> Configure Ingredients
                            </button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times"></i> Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="menuIngredientsModal" data-backdrop="static" data-keyboard="false"
                aria-labelledby="menuIngredientsLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title" id="menuIngredientsLabel">
                                <i class="fas fa-utensils"></i> Menu Servings
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
            <div class="modal fade" id="addIngredientModal" data-backdrop="static" data-keyboard="false">
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
                                    <input type="number" id="ingredientQty" class="form-control"
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

            <div class="modal fade" id="deleteConfirmModal" data-backdrop="static" data-keyboard="false">
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

            <div class="modal fade" id="restoreConfirmModal" data-backdrop="static" data-keyboard="false">
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

            <div class="modal fade" id="forceDeleteConfirmModal" data-backdrop="static" data-keyboard="false">
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
                    <div class="modal fade" id="editMenuModal{{ $item->id }}" data-backdrop="static" data-keyboard="false"
                        aria-labelledby="editMenuModalLabel{{ $item->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('admin.updatemenu', $item->id) }}" method="POST"
                                id="editMenuForm{{ $item->id }}">
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
                                            <label>Menu Item <span class="text-danger">*</span></label>
                                            <input type="text" name="menu_item" id="edit_menu_item{{ $item->id }}"
                                                value="{{ $item->menu_item }}" class="form-control" required minlength="3">
                                            <div>
                                                <small id="edit_menuItemError{{ $item->id }}" class="text-danger text-sm"
                                                    style="display: none;"></small>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Category <span class="text-danger">*</span></label>
                                            <select name="category" id="edit_category{{ $item->id }}" class="form-control"
                                                required>
                                                <option value="">Select Category</option>
                                                <option value="main" {{ $item->category == 'main' ? 'selected' : '' }}>Main Dishes
                                                </option>
                                                <option value="add_ons" {{ $item->category == 'add_ons' ? 'selected' : '' }}>
                                                    Add-ons</option>
                                            </select>
                                            <div>
                                                <small id="edit_categoryError{{ $item->id }}" class="text-danger text-sm"
                                                    style="display: none;"></small>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Regular Price <span class="text-danger">*</span></label>
                                            <input type="number" name="regular_price" id="edit_regular_price{{ $item->id }}"
                                                value="{{ $item->regular_price }}" class="form-control" step="0.01" min="0.01"
                                                required>
                                            <div>
                                                <small id="edit_regularPriceError{{ $item->id }}" class="text-danger text-sm"
                                                    style="display: none;"></small>
                                            </div>
                                        </div>

                                        <label>Status <span class="text-danger">*</span></label>
                                        <select name="status" id="edit_status{{ $item->id }}" class="form-control" required>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function checkMenuItemAvailability(menuItem, menuId = null) {
        if (!menuItem || menuItem.trim().length < 3) return;

        $.ajax({
            url: "{{ route('check.menu.availability') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                menu_item: menuItem,
                menu_id: menuId
            },
            success: function (response) {
                const menuItemInput = menuId ?
                    document.getElementById('edit_menu_item' + menuId) :
                    document.getElementById('menu_item');
                const menuItemError = menuId ?
                    document.getElementById('edit_menuItemError' + menuId) :
                    document.getElementById('menuItemError');

                if (!response.available) {
                    menuItemError.textContent = 'This menu item already exists';
                    menuItemError.style.display = 'block';
                    menuItemInput.classList.add('is-invalid');

                    menuItemInput.value = '';

                    Swal.fire({
                        icon: 'error',
                        title: 'Duplicate Menu Item',
                        text: `"${menuItem}" already exists in the menu`,
                        toast: true,
                        position: 'top',
                        timer: 3000,
                        showConfirmButton: false,
                        background: '#f8d7da',
                        color: '#721c24'
                    });
                } else {
                    if (menuItemError.textContent.includes('already exists')) {
                        menuItemError.textContent = '';
                        menuItemError.style.display = 'none';
                        menuItemInput.classList.remove('is-invalid');
                    }
                }
            }
        });
    }

    function initializeMenuFormValidation() {
        // Menu Item Validation
        const menuItemInput = document.getElementById('menu_item');
        const menuItemError = document.getElementById('menuItemError');

        if (menuItemInput && menuItemError) {
            const debouncedMenuCheck = debounce(function (value) {
                checkMenuItemAvailability(value);
            }, 500);

            $(menuItemInput).off('input.menuItem');
            $(menuItemInput).on('input.menuItem', function () {
                this.value = this.value.replace(/[^a-zA-Z0-9\s\-'\"().,&]/g, '');

                if (this.value.length > 0) {
                    this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
                }

                const value = this.value.trim();

                if (!value) {
                    menuItemError.textContent = '';
                    menuItemError.style.display = 'none';
                    this.classList.remove('is-invalid');
                    return;
                }

                if (value.length < 3) {
                    menuItemError.textContent = 'Minimum 3 characters required';
                    menuItemError.style.display = 'block';
                    this.classList.add('is-invalid');
                } else {
                    menuItemError.textContent = '';
                    menuItemError.style.display = 'none';
                    this.classList.remove('is-invalid');
                    debouncedMenuCheck(value);
                }
            });
        }

        // Category Validation
        const categoryInput = document.getElementById('category');
        const categoryError = document.getElementById('categoryError');

        if (categoryInput && categoryError) {
            $(categoryInput).off('change.category');
            $(categoryInput).on('change.category', function () {
                const value = this.value;

                if (!value) {
                    categoryError.textContent = 'Please select a category';
                    categoryError.style.display = 'block';
                    this.classList.add('is-invalid');
                } else {
                    categoryError.textContent = '';
                    categoryError.style.display = 'none';
                    this.classList.remove('is-invalid');
                }
            });
        }

        // Image Validation
        const imageInput = document.getElementById('image');
        const imageError = document.getElementById('imageError');

        if (imageInput && imageError) {
            $(imageInput).off('change.image');
            $(imageInput).on('change.image', function () {
                const file = this.files[0];

                if (!file) {
                    imageError.textContent = '';
                    imageError.style.display = 'none';
                    this.classList.remove('is-invalid');
                    return;
                }

                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                const maxSize = 2 * 1024 * 1024; // 2MB

                if (!allowedTypes.includes(file.type)) {
                    imageError.textContent = 'Only JPG, JPEG, PNG, and GIF images are allowed';
                    imageError.style.display = 'block';
                    this.classList.add('is-invalid');
                    this.value = '';
                    return;
                }

                if (file.size > maxSize) {
                    imageError.textContent = 'Image size must not exceed 2MB';
                    imageError.style.display = 'block';
                    this.classList.add('is-invalid');
                    this.value = '';
                    return;
                }

                imageError.textContent = '';
                imageError.style.display = 'none';
                this.classList.remove('is-invalid');
            });
        }

        // Regular Price Validation
        const regularPriceInput = document.getElementById('regular_price');
        const regularPriceError = document.getElementById('regularPriceError');

        if (regularPriceInput && regularPriceError) {
            $(regularPriceInput).off('input.regularPrice');
            $(regularPriceInput).on('input.regularPrice', function () {
                const value = parseFloat(this.value);

                if (!this.value) {
                    regularPriceError.textContent = '';
                    regularPriceError.style.display = 'none';
                    this.classList.remove('is-invalid');
                    return;
                }

                if (isNaN(value) || value <= 0) {
                    regularPriceError.textContent = 'Price must be greater than 0';
                    regularPriceError.style.display = 'block';
                    this.classList.add('is-invalid');
                } else {
                    regularPriceError.textContent = '';
                    regularPriceError.style.display = 'none';
                    this.classList.remove('is-invalid');
                }
            });
        }
    }

    // Add Menu Modal Events
    $('#addMenuModal').on('shown.bs.modal', function () {
        initializeMenuFormValidation();
        $('#menu_item').focus();
    });

    $('#addMenuModal').on('hidden.bs.modal', function () {
        $('#addMenuForm')[0].reset();
        $('#addMenuForm input, #addMenuForm select').removeClass('is-invalid');
        $('small[id$="Error"]').hide().text('');
    });

    function initializeEditMenuFormValidation(itemId) {
        // Menu Item Validation
        const menuItemInput = document.getElementById('edit_menu_item' + itemId);
        const menuItemError = document.getElementById('edit_menuItemError' + itemId);

        if (menuItemInput && menuItemError) {
            const debouncedEditMenuCheck = debounce(function (value) {
                checkMenuItemAvailability(value, itemId);
            }, 500);

            $(menuItemInput).off('input.menuItem');
            $(menuItemInput).on('input.menuItem', function () {
                this.value = this.value.replace(/[^a-zA-Z0-9\s\-'\"().,&]/g, '');

                if (this.value.length > 0) {
                    this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
                }

                const value = this.value.trim();

                if (!value) {
                    menuItemError.textContent = '';
                    menuItemError.style.display = 'none';
                    this.classList.remove('is-invalid');
                    return;
                }

                if (value.length < 3) {
                    menuItemError.textContent = 'Minimum 3 characters required';
                    menuItemError.style.display = 'block';
                    this.classList.add('is-invalid');
                } else {
                    menuItemError.textContent = '';
                    menuItemError.style.display = 'none';
                    this.classList.remove('is-invalid');
                    debouncedEditMenuCheck(value);
                }
            });
        }

        // Category Validation
        const categoryInput = document.getElementById('edit_category' + itemId);
        const categoryError = document.getElementById('edit_categoryError' + itemId);

        if (categoryInput && categoryError) {
            $(categoryInput).off('change.category');
            $(categoryInput).on('change.category', function () {
                const value = this.value;

                if (!value) {
                    categoryError.textContent = 'Please select a category';
                    categoryError.style.display = 'block';
                    this.classList.add('is-invalid');
                } else {
                    categoryError.textContent = '';
                    categoryError.style.display = 'none';
                    this.classList.remove('is-invalid');
                }
            });
        }

        // Regular Price Validation
        const regularPriceInput = document.getElementById('edit_regular_price' + itemId);
        const regularPriceError = document.getElementById('edit_regularPriceError' + itemId);

        if (regularPriceInput && regularPriceError) {
            $(regularPriceInput).off('input.regularPrice');
            $(regularPriceInput).on('input.regularPrice', function () {
                const value = parseFloat(this.value);

                if (!this.value) {
                    regularPriceError.textContent = '';
                    regularPriceError.style.display = 'none';
                    this.classList.remove('is-invalid');
                    return;
                }

                if (isNaN(value) || value <= 0) {
                    regularPriceError.textContent = 'Price must be greater than 0';
                    regularPriceError.style.display = 'block';
                    this.classList.add('is-invalid');
                } else {
                    regularPriceError.textContent = '';
                    regularPriceError.style.display = 'none';
                    this.classList.remove('is-invalid');
                }
            });
        }
    }

    $('[id^="editMenuModal"]').on('shown.bs.modal', function () {
        const modalId = $(this).attr('id');
        const itemId = modalId.replace('editMenuModal', '');
        initializeEditMenuFormValidation(itemId);
    });

    $('[id^="editMenuModal"]').on('hidden.bs.modal', function () {
        const modalId = $(this).attr('id');
        const itemId = modalId.replace('editMenuModal', '');
        $(`#editMenuForm${itemId} input, #editMenuForm${itemId} select`).removeClass('is-invalid');
        $(`small[id^="edit_"][id$="Error${itemId}"]`).hide().text('');
    });

    $('#addMenuForm').on('submit', function (e) {
        const hasInvalidFields = $(this).find('.is-invalid').length > 0;

        if (hasInvalidFields) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please fix all errors before submitting',
                toast: true,
                position: 'top',
                timer: 3000,
                showConfirmButton: false,
                background: '#f8d7da',
                color: '#721c24'
            });
            return false;
        }
    });

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
                    showConfirmButton: false,
                    background: '#f8d7da',
                    color: '#721c24'
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
                    showConfirmButton: false,
                    background: '#f8d7da',
                    color: '#721c24'
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
                    showConfirmButton: false,
                    background: '#f8d7da',
                    color: '#721c24'
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
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-info"></i>
                    <p class="mt-2">Loading menu ingredients...</p>
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
                        const ingList = data.ingredients[menu.id] || [];
                        const hasIngredients = ingList.length > 0;

                        const isNewWithSuggestions = hasIngredients && menu.created_recently;
                        const cardClass = isNewWithSuggestions ? 'border-success' : '';
                        const headerClass = isNewWithSuggestions ? 'bg-success text-white' : 'bg-light';

                        html += `
                            <div class="card mb-3 ${cardClass}">
                                <div class="card-header ${headerClass}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 font-weight-bold">${menu.menu_item}</h6>
                                        <small class="text-${isNewWithSuggestions ? 'white' : 'muted'}">
                                            Category: ${menu.category}
                                        </small>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Ingredient</th>
                                                <th>Category</th>
                                                <th width="150">Quantity (grams/pcs)</th>
                                                <th width="80">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                        `;

                        if (ingList.length === 0) {
                            html += `
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">
                                        <i class="fas fa-info-circle"></i> Empty
                                    </td>
                                </tr>
                            `;
                        } else {
                            ingList.forEach(ing => {
                                html += `
                                    <tr>
                                        <td class="align-middle">
                                            <strong>${ing.ingredient_name}</strong>
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge badge-info">${ing.category}</span>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <input type="number" 
                                                       class="form-control ingredient-qty"
                                                       data-id="${ing.id}" 
                                                       value="${ing.quantity}"
                                                       min="0.01"
                                                       step="any"
                                                       placeholder="Quantity">
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
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
                                        <div class="d-flex justify-content-between align-items-center">
                                            <button type="button"
                                                class="btn btn-sm btn-success addIngredientBtn"
                                                data-menu-id="${menu.id}"
                                                data-menu-name="${menu.menu_item}">
                                                <i class="fas fa-plus"></i> Add Ingredient
                                            </button>
                                        </div>
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
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Input',
                    text: 'Please enter valid quantities (greater than 0)',
                    toast: true,
                    position: 'top',
                    timer: 3000,
                    showConfirmButton: false,
                    background: '#fff3cd',
                    color: '#856404'
                });
                return;
            }

            if (updates.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Changes',
                    text: 'No ingredients to update',
                    toast: true,
                    position: 'top',
                    timer: 3000,
                    showConfirmButton: false,
                    background: '#fff3cd',
                    color: '#856404'
                });
                return;
            }

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
                        $('#menuIngredientsModal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Ingredients updated successfully',
                            toast: true,
                            position: 'top',
                            timer: 3000,
                            showConfirmButton: false,
                            background: '#d4edda',
                            color: '#155724'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to update',
                            toast: true,
                            position: 'top',
                            timer: 3000,
                            showConfirmButton: false,
                            background: '#f8d7da',
                            color: '#721c24'
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update ingredients',
                        toast: true,
                        position: 'top',
                        timer: 3000,
                        showConfirmButton: false,
                        background: '#f8d7da',
                        color: '#721c24'
                    });
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
                                    title: 'Ingredient removed successfully',
                                    toast: true,
                                    position: 'top',
                                    timer: 3000,
                                    showConfirmButton: false,
                                    background: '#d4edda',
                                    color: '#155724'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message || 'Failed to remove',
                                    toast: true,
                                    position: 'top',
                                    timer: 3000,
                                    showConfirmButton: false,
                                    background: '#f8d7da',
                                    color: '#721c24'
                                });
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to remove ingredient',
                                toast: true,
                                position: 'top',
                                timer: 3000,
                                showConfirmButton: false,
                                background: '#f8d7da',
                                color: '#721c24'
                            });
                        });
                }
            });
        });

        $(document).on('click', '.addIngredientBtn', function () {
            currentMenuId = $(this).data('menu-id');
            const menuName = $(this).data('menu-name');

            $('#menuNameLabel').text(menuName);
            $('#ingredientQty').val('');

            fetch("{{ route('ingredients.list') }}")
                .then(res => res.json())
                .then(data => {
                    if (!data.ingredients || data.ingredients.length === 0) {
                        Swal.fire({
                            icon: 'info',
                            title: 'No Ingredients',
                            text: 'No ingredients available',
                            toast: true,
                            position: 'top',
                            timer: 3000,
                            showConfirmButton: false,
                        });
                        return;
                    }

                    const grouped = data.ingredients.reduce((acc, ing) => {
                        if (!acc[ing.category]) acc[ing.category] = [];
                        acc[ing.category].push(ing);
                        return acc;
                    }, {});

                    fetch(`{{ url('menu') }}/${currentMenuId}/existing-ingredients`)
                        .then(res => res.json())
                        .then(existingData => {
                            const existingIds = existingData.ingredient_ids || [];

                            let options = '<option value="">Select an ingredient</option>';

                            const categoryOrder = ['meat', 'vegetables', 'soupbase', 'beverage'];
                            categoryOrder.forEach(category => {
                                if (!grouped[category]) return;

                                const available = grouped[category].filter(ing => !existingIds.includes(ing.id));
                                if (available.length === 0) return;

                                options += `<optgroup label="${category.charAt(0).toUpperCase() + category.slice(1)}">`;
                                available.forEach(ing => {
                                    let displayStock = ing.stocks;
                                    let displayUnit = ing.unit;

                                    if (ing.unit === 'kg') {
                                        displayStock = ing.stocks * 1000;
                                        displayUnit = 'grams';
                                    }

                                    options += `<option value="${ing.id}" data-unit="${displayUnit}">
                                        ${ing.name}
                                    </option>`;
                                });
                                options += '</optgroup>';
                            });

                            $('#ingredientSelect').html(options);
                            $('#addIngredientModal').modal('show');
                        })
                        .catch(() => {
                            let options = '<option value="">Select an ingredient</option>';
                            Object.keys(grouped).forEach(category => {
                                options += `<optgroup label="${category.charAt(0).toUpperCase() + category.slice(1)}">`;
                                grouped[category].forEach(ing => {
                                    let displayStock = ing.stocks;
                                    let displayUnit = ing.unit;

                                    if (ing.unit === 'kg') {
                                        displayStock = ing.stocks * 1000;
                                        displayUnit = 'grams';
                                    }

                                    options += `<option value="${ing.id}" data-unit="${displayUnit}">
                                        ${ing.name} (Stock: ${displayStock} ${displayUnit})
                                    </option>`;
                                });
                                options += '</optgroup>';
                            });
                            $('#ingredientSelect').html(options);
                            $('#addIngredientModal').modal('show');
                        });
                })
                .catch(err => {
                    console.error('Error loading ingredients:', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load ingredients',
                        toast: true,
                        position: 'top',
                        timer: 3000,
                        showConfirmButton: false,
                    });
                });
        });

        $(document).on('change', '#ingredientSelect', function () {
            const selected = $(this).find('option:selected');
            const unit = selected.data('unit');

            let defaultQty = 1;
            if (unit === 'grams') {
                defaultQty = 50;
            } else if (unit === 'pieces') {
                defaultQty = 10;
            }

            $('#ingredientQty').val(defaultQty);
            $('#unitLabel').text(' (grams)');
        });

        $('#addIngredientForm').on('submit', function (e) {
            e.preventDefault();

            const ingredientId = $('#ingredientSelect').val();
            const quantity = $('#ingredientQty').val();

            if (!ingredientId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Selection',
                    text: 'Please select an ingredient',
                    toast: true,
                    position: 'top',
                    timer: 3000,
                    showConfirmButton: false,
                    background: '#fff3cd',
                    color: '#856404'
                });
                return;
            }

            if (!quantity || parseFloat(quantity) <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Quantity',
                    text: 'Please enter a valid quantity',
                    toast: true,
                    position: 'top',
                    timer: 3000,
                    showConfirmButton: false,
                    background: '#fff3cd',
                    color: '#856404'
                });
                return;
            }

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
                            title: 'Ingredient added successfully',
                            toast: true,
                            position: 'top',
                            timer: 3000,
                            showConfirmButton: false,
                            background: '#d4edda',
                            color: '#155724'
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
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to add ingredient',
                            toast: true,
                            position: 'top',
                            timer: 3000,
                            showConfirmButton: false,
                            background: '#f8d7da',
                            color: '#721c24'
                        });
                    }
                })
                .catch(err => {
                    console.error('Error adding ingredient:', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to add ingredient',
                        toast: true,
                        position: 'top',
                        timer: 3000,
                        showConfirmButton: false,
                        background: '#f8d7da',
                        color: '#721c24'
                    });
                });
        });

        $('#addIngredientModal').on('hidden.bs.modal', function () {
            $('#addIngredientForm')[0].reset();
            $('#unitLabel').text('');
        });
    });

    // Add CSS to hide number input spinners
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

    // Session flash messages
    @if(session('success'))
        @if(session('new_menu_name'))
            $('#newMenuName').text("{{ session('new_menu_name') }}");
            $('#postMenuCreationModal').modal('show');
        @else
            Swal.fire({
                icon: 'success',
                title: '{{ session('success') }}',
                toast: true,
                position: 'top',
                timer: 3000,
                showConfirmButton: false,
                background: '#d4edda',
                color: '#155724'
            });
        @endif
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: '{{ session('error') }}',
            toast: true,
            position: 'top',
            timer: 3000,
            showConfirmButton: false,
            background: '#f8d7da',
            color: '#721c24'
        });
    @endif

    @if(session('new_menu_name'))
        $('#newMenuName').text("{{ session('new_menu_name') }}");
        $('#postMenuCreationModal').modal({
            backdrop: 'static',
            keyboard: false
        });
    @endif

    $('#configureIngredientsBtn').on('click', function () {
        $('#postMenuCreationModal').modal('hide');

        setTimeout(function () {
            $('#menuIngredientsModal').modal('show');
        }, 300);
    });

    let autoCloseTimer;
    $('#postMenuCreationModal').on('shown.bs.modal', function () {
        autoCloseTimer = setTimeout(function () {
            $('#postMenuCreationModal').modal('hide');
        }, 10000);
    });

    $('#postMenuCreationModal').on('hidden.bs.modal', function () {
        clearTimeout(autoCloseTimer);
    });
</script>
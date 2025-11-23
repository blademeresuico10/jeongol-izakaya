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
                            <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#addCategoryModal">
                                Add Menu Category
                            </button>
                            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#updateCategoryModal">
                                Update Menu Category
                            </button>
                            <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#addMenuModal">
                                Add Menu
                            </button>
                            <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#menuIngredientsModal">
                                Menu Servings
                            </button>
                            <button class="btn btn-sm btn-secondary" data-toggle="modal"
                                data-target="#quantityPerPlateModal">
                                Qty.Per Plate
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
            <div class="modal fade" id="addCategoryModal" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-white">
                            <h5 class="modal-title" id="addCategoryModalLabel">
                                <i class="fas fa-plus"></i> Add New Menu Category
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="addCategoryForm">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="category_name">Category Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="category_name" class="form-control" required
                                        minlength="3" placeholder="e.g., Main Course"
                                        style="text-transform: capitalize;" pattern="[A-Za-z\s]+"
                                        title="Only letters are allowed"
                                        oninput="this.value = this.value.replace(/[0-9]/g, '').replace(/\b\w/g, char => char.toUpperCase())">
                                    <small id="categoryNameError" class="text-danger" style="display: none;"></small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Add Category
                                </button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="updateCategoryModal" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title" id="updateCategoryModalLabel">
                                <i class="fas fa-edit"></i> Update Category Status
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="updateCategoryForm" method="POST">
                            @csrf
                            <input type="hidden" name="category_id" id="update_category_id">

                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="category_select">Select Category <span
                                            class="text-danger">*</span></label>
                                    <select id="category_select" class="form-control" required>
                                        <option value="">Choose a category...</option>
                                        @foreach($allCategories as $category)
                                            <option value="{{ $category->id }}" data-status="{{ $category->is_active }}">
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Status <span class="text-danger">*</span></label>
                                    <select name="is_active" id="is_active" class="form-control" required>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                    @error('is_active')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-info">
                                    <i class="fas fa-save"></i> Update Status
                                </button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
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
                                    <select name="category_id" id="category_id" class="form-control" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div>
                                        <small id="categoryError" class="text-danger text-sm"
                                            style="display: none;"></small>
                                    </div>
                                    @error('category_id')
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
            <!-- Quantity Per Plate Modal -->
            <div class="modal fade" id="quantityPerPlateModal" data-backdrop="static" data-keyboard="false"
                aria-labelledby="quantityPerPlateLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-secondary text-white">
                            <h5 class="modal-title" id="quantityPerPlateLabel">
                                <i class="fas fa-fill-drip"></i> Refill Qty. Per Plate
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered ">
                                    <thead class="thead-light"
                                        style="position: sticky; top: 0; z-index: 10; background-color: #f8f9fa;">
                                        <tr>
                                            <th>Ingredient</th>
                                            <th width="150">Refill Qty. Per Plate</th>
                                            <th width="100">Unit</th>
                                        </tr>
                                    </thead>
                                    <tbody id="quantityPerPlateContent">
                                        @forelse($ingredients as $ingredient)
                                            <tr>
                                                <td>
                                                    <strong>{{ $ingredient->name }}</strong>
                                                    @if(isset($ingredient->used_in_menus))
                                                        <br><small class="text-muted">Used in:
                                                            {{ $ingredient->used_in_menus }}</small>
                                                    @endif
                                                    <input type="hidden" class="ingredient-id"
                                                        value="{{ $ingredient->id }}">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm refill-qty"
                                                        step="0.01" min="0"
                                                        value="{{ $ingredient->quantity_per_plate ?? 0 }}"
                                                        placeholder="0.00">
                                                </td>
                                                <td>
                                                    <select class="form-control form-control-sm refill-unit">
                                                        @if($ingredient->unit == 'kg')
                                                            <option value="kg">kg</option>
                                                            <option value="g" selected>g</option>
                                                        @else
                                                            <option value="{{ $ingredient->unit }}" selected>
                                                                {{ $ingredient->unit }}</option>
                                                        @endif
                                                    </select>
                                                </td>

                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">No ingredients used in any menu</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                                <i class="fas fa-times"></i> Close
                            </button>
                            <button type="button" class="btn btn-success btn-sm" id="saveQtyPerPlateBtn">
                                <i class="fas fa-save"></i> Save
                            </button>
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
                                    <label class="font-weight-bold">Ingredient <span
                                            class="text-danger">*</span></label>
                                    <select id="ingredientSelect" class="form-control" required>
                                        <option value="">Select an ingredient</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">
                                        Quantity <span class="text-danger">*</span><span id="unitLabel"></span>
                                    </label>
                                    <input type="number" id="ingredientQty" class="form-control"
                                        placeholder="Enter quantity" step="0.01" min="0.01" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Add Ingredient
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
                                            <select name="category_id" id="edit_category{{ $item->id }}" class="form-control"
                                                required>
                                                <option value="">Select Category</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ $item->category_id == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
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
                const maxSize = 2 * 1024 * 1024;

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

                        html += `
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 font-weight-bold">${menu.menu_item}</h6>
                                <span class="badge badge-primary">${menu.category_name}</span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm  mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Ingredient</th>
                                        <th>Category</th>
                                        <th width="120">Quantity</th>
                                        <th width="80">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                `;

                        if (ingList.length === 0) {
                            html += `
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">
                                <i class="fas fa-info-circle"></i> Empty
                            </td>
                        </tr>
                    `;
                        } else {
                            ingList.forEach(ing => {
                                const currentUnit = ing.stored_unit || ing.base_unit;
                                const baseUnit = ing.base_unit;

                                const isPieces = ['pcs', 'pieces', 'piece', 'pc'].includes(currentUnit.toLowerCase());
                                const isKilogram = ['kg', 'kilogram', 'kilograms'].includes(baseUnit.toLowerCase());

                                const step = isPieces ? '1' : '0.01';
                                const min = isPieces ? '1' : '0.01';
                                const oninput = isPieces ? 'this.value = Math.floor(Math.abs(this.value))' : '';
                                const displayQty = isPieces ? Math.floor(ing.quantity) : parseFloat(ing.quantity).toFixed(2);

                                html += `
                            <tr>
                                <td class="align-middle">
                                    <strong>${ing.ingredient_name}</strong>
                                </td>
                                <td class="align-middle">
                                    <span class="badge badge-info">${ing.category}</span>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm" style="width: 150px;">
                                        <input type="number" 
                                               class="form-control ingredient-qty"
                                               data-id="${ing.id}" 
                                               data-base-unit="${baseUnit}"
                                               value="${displayQty}"
                                               min="${min}"
                                               step="${step}"
                                               ${oninput ? `oninput="${oninput}"` : ''}
                                               placeholder="Quantity">
                                        <div class="input-group-append">
                        `;

                                if (isKilogram) {
                                    const selectedKg = (currentUnit && currentUnit.toLowerCase() === 'kg') ? 'selected' : '';
                                    const selectedG = (currentUnit && currentUnit.toLowerCase() === 'g') ? 'selected' : '';

                                    html += `
                                            <select class="form-control form-control-sm unit-selector" 
                                                    data-id="${ing.id}"
                                                    style="max-width: 60px; border-left: none; margin-left: 5px;">
                                                <option value="kg" ${selectedKg}>kg</option>
                                                <option value="g" ${selectedG}>g</option>
                                            </select>
                            `;
                                } else {
                                    html += `
                                            <span class="input-group-text">${currentUnit}</span>
                            `;
                                }

                                html += `
                                        </div>
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

                    document.querySelectorAll('.unit-selector').forEach(select => {
                        select.addEventListener('change', function () {
                            const ingredientId = this.dataset.id;
                            const qtyInput = document.querySelector(`.ingredient-qty[data-id="${ingredientId}"]`);
                            const currentValue = parseFloat(qtyInput.value) || 0;
                            const previousUnit = this.dataset.previousUnit || select.value;
                            const newUnit = this.value;

                            if (previousUnit !== newUnit) {
                                if (newUnit === 'g' && previousUnit === 'kg') {
                                    qtyInput.value = (currentValue * 1000).toFixed(2);
                                } else if (newUnit === 'kg' && previousUnit === 'g') {
                                    qtyInput.value = (currentValue / 1000).toFixed(2);
                                }
                            }

                            this.dataset.previousUnit = newUnit;
                        });

                        select.dataset.previousUnit = select.value;
                    });
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
                const min = parseFloat(input.min);

                if (isNaN(value) || value < min) {
                    hasError = true;
                    input.classList.add('is-invalid');

                } else {
                    input.classList.remove('is-invalid');

                    const ingredientId = input.dataset.id;
                    const unitSelector = document.querySelector(`.unit-selector[data-id="${ingredientId}"]`);
                    const selectedUnit = unitSelector ? unitSelector.value : input.dataset.baseUnit;
                    updates.push({
                        id: input.dataset.id,
                        quantity: value,
                        unit: selectedUnit

                    });
                }
            });

            if (hasError) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Input',
                    text: 'Please enter valid quantities',
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

            console.log('=== Add Ingredient Clicked ===');
            console.log('Menu ID:', currentMenuId);
            console.log('Menu Name:', menuName);

            $('#menuNameLabel').text(menuName);
            $('#ingredientQty').val('');
            $('#unitLabel').html('');

            fetch("{{ route('ingredients.list') }}")
                .then(res => {
                    console.log('Ingredients response status:', res.status);
                    return res.json();
                })
                .then(data => {
                    console.log('Ingredients data received:', data);

                    if (!data.ingredients || data.ingredients.length === 0) {
                        console.warn('No ingredients available');
                        Swal.fire({
                            icon: 'info',
                            title: 'No Ingredients',
                            text: 'No ingredients available in the system',
                            toast: true,
                            position: 'top',
                            timer: 3000,
                            showConfirmButton: false,
                        });
                        return;
                    }

                    const grouped = {};
                    data.ingredients.forEach(ing => {
                        const cat = ing.category ? ing.category.toLowerCase() : 'other';
                        if (!grouped[cat]) {
                            grouped[cat] = [];
                        }
                        grouped[cat].push(ing);
                    });

                    console.log('Grouped ingredients:', grouped);

                    fetch(`{{ url('menu') }}/${currentMenuId}/existing-ingredients`)
                        .then(res => {
                            console.log('Existing ingredients response status:', res.status);
                            return res.json();
                        })
                        .then(existingData => {
                            console.log('Existing ingredients data:', existingData);

                            const existingIds = existingData.ingredient_ids || [];
                            console.log('Existing ingredient IDs:', existingIds);

                            let options = '<option value="">Select an ingredient</option>';
                            let hasOptions = false;

                            const sortedCategories = Object.keys(grouped).sort();

                            sortedCategories.forEach(category => {
                                const available = grouped[category].filter(ing => {
                                    return !existingIds.includes(ing.id);
                                });

                                if (available.length === 0) {
                                    console.log(`No available ingredients in category: ${category}`);
                                    return;
                                }

                                hasOptions = true;
                                const categoryName = category.charAt(0).toUpperCase() + category.slice(1);
                                options += `<optgroup label="${categoryName}">`;

                                available.forEach(ing => {
                                    options += `<option value="${ing.id}" data-unit="${ing.unit}" data-stocks="${ing.stocks}">
                                ${ing.name}
                            </option>`;
                                });

                                options += '</optgroup>';
                            });

                            console.log('Has options:', hasOptions);
                            console.log('Final options HTML length:', options.length);

                            if (!hasOptions) {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'All Ingredients Added',
                                    text: 'All available ingredients have already been added to this menu',
                                    toast: true,
                                    position: 'top',
                                    timer: 3000,
                                    showConfirmButton: false,
                                });
                                return;
                            }

                            $('#ingredientSelect').html(options);
                            $('#addIngredientModal').modal('show');
                        })
                        .catch(err => {
                            console.error('Error fetching existing ingredients:', err);

                            let options = '<option value="">Select an ingredient</option>';

                            Object.keys(grouped).sort().forEach(category => {
                                const categoryName = category.charAt(0).toUpperCase() + category.slice(1);
                                options += `<optgroup label="${categoryName}">`;

                                grouped[category].forEach(ing => {
                                    options += `<option value="${ing.id}"">
                                ${ing.name})
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
                        text: 'Failed to load ingredients. Please try again.',
                        toast: true,
                        position: 'top',
                        timer: 3000,
                        showConfirmButton: false,
                        background: '#f8d7da',
                        color: '#721c24'
                    });
                });
        });

        $(document).on('change', '#ingredientSelect', function () {
            const selected = $(this).find('option:selected');
            const unit = selected.data('unit');
            const ingredientQtyInput = $('#ingredientQty');
            const unitLabel = $('#unitLabel');

            if (!unit) {
                ingredientQtyInput.val('');
                unitLabel.text('');
                ingredientQtyInput.attr('step', '0.01');
                ingredientQtyInput.attr('min', '0.01');
                return;
            }

            const isPieces = ['pcs', 'pieces', 'piece', 'pc'].includes(unit.toLowerCase());

            const isKilogram = ['kg', 'kilogram', 'kilograms'].includes(unit.toLowerCase());

            if (isPieces) {
                ingredientQtyInput.val(1);
                ingredientQtyInput.attr('step', '1');
                ingredientQtyInput.attr('min', '1');
                ingredientQtyInput.attr('oninput', 'this.value = Math.floor(Math.abs(this.value))');
                unitLabel.html(` <span class="text-muted">(${unit})</span>`);
            } else if (isKilogram) {
                ingredientQtyInput.val(0.1);
                ingredientQtyInput.attr('step', '0.01');
                ingredientQtyInput.attr('min', '0.01');
                ingredientQtyInput.removeAttr('oninput');

                unitLabel.html(`
            <select id="ingredientUnitSelect" class="form-control form-control-sm d-inline-block ml-2" style="width: auto;">
                <option value="kg">kg</option>
                <option value="g">g</option>
            </select>
        `);
            } else {
                ingredientQtyInput.val(0.1);
                ingredientQtyInput.attr('step', '0.01');
                ingredientQtyInput.attr('min', '0.01');
                ingredientQtyInput.removeAttr('oninput');
                unitLabel.html(` <span class="text-muted">(${unit})</span>`);
            }
        });

        $(document).on('change', '#ingredientUnitSelect', function () {
            const ingredientQtyInput = $('#ingredientQty');
            const currentQty = parseFloat(ingredientQtyInput.val()) || 0;
            const newUnit = $(this).val();
            const previousUnit = $(this).data('previous-unit') || 'kg';

            if (newUnit !== previousUnit) {
                if (newUnit === 'g' && previousUnit === 'kg') {
                    ingredientQtyInput.val((currentQty * 1000).toFixed(2));
                } else if (newUnit === 'kg' && previousUnit === 'g') {
                    ingredientQtyInput.val((currentQty / 1000).toFixed(2));
                }
            }

            $(this).data('previous-unit', newUnit);
        });

        $('#addIngredientForm').on('submit', function (e) {
            e.preventDefault();

            const ingredientId = $('#ingredientSelect').val();
            const quantity = parseFloat($('#ingredientQty').val());
            const selectedOption = $('#ingredientSelect option:selected');
            const baseUnit = selectedOption.data('unit');

            let unit = baseUnit;
            const unitSelector = $('#ingredientUnitSelect');
            if (unitSelector.length) {
                unit = unitSelector.val();
            }

            if (!ingredientId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
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

            if (!quantity || quantity <= 0) {
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

            const isPieces = ['pcs', 'pieces', 'piece', 'pc'].includes(unit.toLowerCase());
            if (isPieces && !Number.isInteger(quantity)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Quantity',
                    text: 'Quantity must be a whole number for pieces',
                    toast: true,
                    position: 'top',
                    timer: 3000,
                    showConfirmButton: false,
                    background: '#fff3cd',
                    color: '#856404'
                });
                return;
            }

            fetch(`{{ url('menu') }}/${currentMenuId}/add-ingredient`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    ingredient_id: ingredientId,
                    quantity: quantity,
                    unit: unit
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        $('#addIngredientModal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message || 'Ingredient added successfully',
                            toast: true,
                            position: 'top',
                            timer: 3000,
                            showConfirmButton: false,
                            background: '#d4edda',
                            color: '#155724'
                        });

                        $('#menuIngredientsModal').modal('hide');
                        setTimeout(() => {
                            $('#menuIngredientsModal').modal('show');
                        }, 500);
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

    $(document).ready(function () {
        $('#addCategoryForm').on('submit', function (e) {
            e.preventDefault();

            $('#categoryNameError').hide().text('');

            $.ajax({
                url: "{{ route('storeCategory') }}",
                method: 'POST',
                data: $(this).serialize(),
                success: function (response) {
                    $('#addCategoryModal').modal('hide');
                    $('#addCategoryForm')[0].reset();

                    Swal.fire({
                        icon: 'success',
                        title: 'Category added successfully!',
                        toast: true,
                        position: 'top',
                        timer: 3000,
                        showConfirmButton: false,
                        background: '#d4edda',
                        color: '#155724'
                    });

                    location.reload();
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.name) {
                            $('#categoryNameError').text(errors.name[0]).show();
                        }
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                }
            });
        });
    });

    document.getElementById('category_select').addEventListener('change', function () {
        const categoryId = this.value;
        const selectedOption = this.options[this.selectedIndex];
        const currentStatus = selectedOption.getAttribute('data-status');

        if (categoryId) {
            document.getElementById('updateCategoryForm').action = `/update-category/${categoryId}`;

            document.getElementById('update_category_id').value = categoryId;

            document.getElementById('is_active').value = currentStatus == '1' ? '1' : '0';
        }
    });

    $('#quantityPerPlateModal').on('shown.bs.modal', function () {
        $('#qty_per_plate').focus();
    });

    $(document).ready(function () {
        let originalData = {};
        let hasChanges = false;

        $('#quantityPerPlateModal').on('shown.bs.modal', function () {
            storeOriginalData();
            $('#saveQtyPerPlateBtn').prop('disabled', true);
            hasChanges = false;
        });

        function storeOriginalData() {
            originalData = {};
            $('#quantityPerPlateContent tr').each(function () {
                let ingredientId = $(this).find('.ingredient-id').val();
                if (ingredientId) {
                    originalData[ingredientId] = {
                        quantity: $(this).find('.refill-qty').val(),
                        unit: $(this).find('.refill-unit').val()
                    };
                }
            });
        }

        function checkForChanges() {
            hasChanges = false;

            $('#quantityPerPlateContent tr').each(function () {
                let ingredientId = $(this).find('.ingredient-id').val();
                if (ingredientId && originalData[ingredientId]) {
                    let currentQty = $(this).find('.refill-qty').val();
                    let currentUnit = $(this).find('.refill-unit').val();

                    if (currentQty != originalData[ingredientId].quantity ||
                        currentUnit != originalData[ingredientId].unit) {
                        hasChanges = true;
                        return false;
                    }
                }
            });

            $('#saveQtyPerPlateBtn').prop('disabled', !hasChanges);
        }

        $(document).on('input change', '.refill-qty, .refill-unit', function () {
            checkForChanges();
        });

        $('#saveQtyPerPlateBtn').on('click', function () {
            if (!hasChanges) {
                Swal.fire({
                    icon: 'info',
                    title: 'No changes to save',
                    toast: true,
                    position: 'top',
                    timer: 3000,
                    showConfirmButton: false,
                    background: '#d1ecf1',
                    color: '#0c5460'
                });
                return;
            }

            let configurations = [];

            $('#quantityPerPlateContent tr').each(function () {
                let ingredientId = $(this).find('.ingredient-id').val();
                let quantity = $(this).find('.refill-qty').val();
                let unit = $(this).find('.refill-unit').val();

                if (ingredientId) {
                    configurations.push({
                        ingredient_id: ingredientId,
                        quantity_per_plate: parseFloat(quantity) || 0,
                        unit: unit
                    });
                }
            });

            if (configurations.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No data to save',
                    toast: true,
                    position: 'top',
                    timer: 3000,
                    showConfirmButton: false,
                    background: '#fff3cd',
                    color: '#856404'
                });
                return;
            }

            let $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

            $.ajax({
                url: '{{ route("refill-config.save") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    configurations: configurations
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Configurations saved successfully',
                            toast: true,
                            position: 'top',
                            timer: 3000,
                            showConfirmButton: false,
                            background: '#d4edda',
                            color: '#155724'
                        });

                        storeOriginalData();
                        hasChanges = false;

                        $('#quantityPerPlateModal').modal('hide');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.message || 'Failed to save',
                            toast: true,
                            position: 'top',
                            timer: 3000,
                            showConfirmButton: false,
                            background: '#f8d7da',
                            color: '#721c24'
                        });
                        $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save');
                    }
                },
                error: function (xhr) {
                    console.error('Save error:', xhr);

                    let message = 'Failed to save configurations';

                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        message = Object.values(errors).flat().join(', ');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: message,
                        toast: true,
                        position: 'top',
                        timer: 3000,
                        showConfirmButton: false,
                        background: '#f8d7da',
                        color: '#721c24'
                    });

                    $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save');
                },
                complete: function () {
                    if (hasChanges) {
                        $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save');
                    }
                }
            });
        });
    });

</script>
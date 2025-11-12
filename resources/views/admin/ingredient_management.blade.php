@include('admin.layouts.header')
@include('admin.layouts.sidebar')

<div id="content-wrapper" class="d-flex flex-column h-screen overflow-y-auto">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h1 class="h3 mb-0 text-gray-800">Stock Management</h1>
        </nav>

        <div class="container-fluid">
            <div class="card shadow mb-4">
                <div
                    class="card-header py-3 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
                    <div class="mb-3 mb-sm-0">
                        <h6 class="m-0 font-weight-bold text-primary">Ingredients</h6>
                        <ul class="nav nav-tabs mt-3 border-bottom-0" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="stocks-tab" data-toggle="tab" href="#stocks"
                                    role="tab">Stocks</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="batch-tab" data-toggle="tab" href="#batch" role="tab">Stock
                                    Batches</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" id="stock-order-tab" data-toggle="tab" href="#stock-order"
                                    role="tab">Stock Order</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" id="expired-stock-tab" data-toggle="tab" href="#expired-stock"
                                    role="tab" class="text-danger font-weight-bold ml-3"> Expired Stocks
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-primary btn-sm mr-2" data-toggle="modal"
                            data-target="#addStockModal">Restock Order</button>

                        <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#addIngredientModal">Add
                            Ingredient</button>

                        <button class="btn btn-success btn-sm" data-toggle="modal"
                            data-target="#addUnitOfMeasureModal">Add Unit of Measure</button>

                        <button class="btn btn-secondary btn-sm" data-toggle="modal"
                            data-target="#addIngredientCategoryModal">
                            Add Ingredient Category
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="tab-content">

                        <div class="tab-pane fade show active" id="stocks" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Stocks</th>
                                        </tr>
                                    </thead>
                                    <tbody id="stocksTableBody"></tbody>
                                </table>
                            </div>
                            <div class="mt-3 d-flex justify-content-center">
                                <div id="stocksPagination"></div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="batch" role="tabpanel">
                            <ul class="nav nav-pills mb-3 align-items-center 2" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="thisweek-tab" data-toggle="pill" href="#thisweek"
                                        role="tab">This Week</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="lastweek-tab" data-toggle="pill" href="#lastweek"
                                        role="tab">Previous Week</a>
                                </li>

                                <li class="nav-item ms-auto border rounded p-1">
                                    <select id="ingredientFilter" class="form-select" style="width: 200px;">
                                        <option value="all">All Ingredients</option>
                                        @foreach ($ingredients as $ingredient)
                                            <option value="{{ $ingredient->id }}">{{ $ingredient->name }}</option>
                                        @endforeach
                                    </select>
                                </li>


                            </ul>


                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="thisweek" role="tabpanel">
                                    <div id="thisWeekLoading" class="text-center py-4 text-muted d-none">
                                        <i class="fas fa-spinner fa-spin"></i> Loading...
                                    </div>
                                    <div id="thisWeekEmpty" class="text-center py-4 text-muted d-none">
                                        No stock batches added this week
                                    </div>
                                    <div id="thisWeekContent" class="d-none">
                                        <div class="table-responsive">
                                            <table class="table table-bordered ">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Batch Code</th>
                                                        <th>Ingredient</th>
                                                        <th>Quantity</th>
                                                        <th>Arrived Date</th>
                                                        <th>Expiration Date</th>
                                                        <th width="150" class="text-center">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="thisWeekTableBody"></tbody>
                                            </table>
                                        </div>
                                        <div class="mt-3 d-flex justify-content-center">
                                            <div id="thisweekPagination"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="lastweek" role="tabpanel">
                                    <div id="lastWeekLoading" class="text-center py-4 text-muted">
                                        <i class="fas fa-spinner fa-spin"></i> Loading...
                                    </div>
                                    <div id="lastWeekEmpty" class="text-center py-4 text-muted d-none">
                                        No stock batches from previous week
                                    </div>
                                    <div id="lastWeekContent" class="d-none">
                                        <div class="table-responsive">
                                            <table class="table table-bordered ">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Batch Code</th>
                                                        <th>Ingredient</th>
                                                        <th>Quantity</th>
                                                        <th>Arrived Date</th>
                                                        <th>Expiration Date</th>
                                                        <th width="150" class="text-center">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="lastWeekTableBody"></tbody>
                                            </table>
                                        </div>
                                        <div class="mt-3 d-flex justify-content-center">
                                            <div id="lastweekPagination"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="stock-order" role="tabpanel" aria-labelledby="stock-order-tab">
                            @livewire('stock-order-management')
                        </div>

                        <div class="tab-pane fade" id="expired-stock" role="tabpanel"
                            aria-labelledby="expired-stock-tab">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Request Stock Modal --}}
<div class="modal fade" id="addStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Request Stock Form</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="RequestStockForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Select Ingredient <span class="text-danger">*</span></label>
                        <select name="ingredient_id" id="request_ingredient_id" class="form-control" required>
                            <option value="">-- Select Ingredient --</option>
                            @foreach($allIngredients ?? [] as $ingredient)
                                @php
                                    // Make sure we have the unit loaded
                                    $unitAbbr = $ingredient->unit ? $ingredient->unit->abbreviation : 'unit';
                                    $reorderQty = $ingredient->stockAlertLevel ? $ingredient->stockAlertLevel->reorder_quantity : 0;
                                @endphp
                                <option value="{{ $ingredient->id }}" data-name="{{ $ingredient->name }}"
                                    data-unit="{{ $unitAbbr }}" data-reorder="{{ $reorderQty }}">
                                    {{ $ingredient->name }} ({{ $unitAbbr }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Quantity <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="quantity" id="request_quantity" class="form-control" step="0.01"
                                min="0.01" required>
                            <div class="input-group-append">
                                <span class="input-group-text" id="request_unit">unit</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Create
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- Add Ingredient Modal --}}
<div class="modal fade" id="addIngredientModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Add New Ingredient</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="addIngredientForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Ingredient Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="ingredient_name" class="form-control" required minlength="2"
                            placeholder="e.g., Meat" style="text-transform: capitalize;" pattern="[A-Za-z\s]+"
                            title="Only letters are allowed"
                            oninput="this.value = this.value.replace(/[0-9]/g, '').replace(/\b\w/g, char => char.toUpperCase())">
                        <div>
                            <small id="ingredientNameError" class="text-danger text-sm" style="display: none;"></small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Category <span class="text-danger">*</span></label>
                        <select name="category_id" id="ingredient_category" class="form-control" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <div>
                            <small id="ingredientCategoryError" class="text-danger text-sm"
                                style="display: none;"></small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Unit <span class="text-danger">*</span></label>
                        <select name="unit_id" id="ingredient_unit" class="form-control" required>
                            <option value="">Select Unit</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->abbreviation }})</option>
                            @endforeach
                        </select>
                        <div>
                            <small id="ingredientUnitError" class="text-danger text-sm" style="display: none;"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Add Ingredient</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Batch Modal --}}
<div class="modal fade" id="stockBatchModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h6 class="modal-title mb-0">Edit Batch</h6>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="editBatchForm">
                <input type="hidden" id="editBatchId">
                <input type="hidden" id="originalArrived">
                <input type="hidden" id="originalExpiry">
                <div class="modal-body">

                    <div class="form-group">
                        <label class="small">Arrived Date</label>
                        <input type="date" id="editBatchArrived" class="form-control form-control-sm" required>
                    </div>

                    <div class="form-group mb-0">
                        <label class="small">Expiration Date</label>
                        <input type="date" id="editBatchExpiry" class="form-control form-control-sm" required>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="submit" id="updateBatchBtn" class="btn btn-warning btn-sm" disabled>Update</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add Unit of Measure Modal --}}
<div class="modal fade" id="addUnitOfMeasureModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Add New Unit of Measure</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="addUnitOfMeasureForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Type of unit <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="unit_name" class="form-control" required minlength="2"
                            placeholder="e.g., Kilograms" style="text-transform: capitalize;" pattern="[A-Za-z\s]+"
                            title="Only letters are allowed"
                            oninput="this.value = this.value.replace(/[0-9]/g, '').replace(/\b\w/g, char => char.toUpperCase())">
                        <div>
                            <small id="unitNameError" class="text-danger text-sm" style="display: none;"></small>
                        </div>
                        <br>
                        <label>Abbreviation <span class="text-danger">*</span></label>
                        <input type="text" name="abbreviation" id="unit_abbreviation" class="form-control" required
                            minlength="1" placeholder="e.g., kg" pattern="[A-Za-z]+" title="Only letters are allowed"
                            maxlength="10" oninput="this.value = this.value.replace(/[0-9\s]/g, '')">
                        <div>
                            <small id="unitAbbreviationError" class="text-danger text-sm"
                                style="display: none;"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Add Unit of Measure</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add Ingredient Category Modal --}}
<div class="modal fade" id="addIngredientCategoryModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Add New Ingredient Category</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="addIngredientCategoryForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="category_name" class="form-control" required minlength="3"
                            placeholder="e.g., Main Course" style="text-transform: capitalize;" pattern="[A-Za-z\s]+"
                            title="Only letters are allowed"
                            oninput="this.value = this.value.replace(/[0-9]/g, '').replace(/\b\w/g, char => char.toUpperCase())">
                        <div>
                            <small id="CategoryNameError" class="text-danger text-sm" style="display: none;"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>



</div>

@include('admin.layouts.script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

    document.addEventListener('DOMContentLoaded', function () {
        const filterSelect = document.getElementById('ingredientFilter');
        const rows = document.querySelectorAll('#thisWeekTableBody tr, #lastWeekTableBody tr');

        filterSelect.addEventListener('change', function () {
            const selected = this.value;

            rows.forEach(row => {
                const ingredientId = row.dataset.ingredientId; // We'll use this in the table
                if (selected === 'all' || ingredientId === selected) {
                    row.classList.remove('d-none');
                } else {
                    row.classList.add('d-none');
                }
            });
        });
    });

    $(document).ready(function () {
        $('#ingredientFilter').on('change', function () {
            const ingredientId = $(this).val() || 'all';
            const activeTab = $('.nav-link.active').attr('href').replace('#', '');
            loadBatches(activeTab, 1, ingredientId);
        });
        function initializeIngredientFormValidation() {
            const ingredientNameInput = document.getElementById('ingredient_name');
            const ingredientNameError = document.getElementById('ingredientNameError');

            if (ingredientNameInput && ingredientNameError) {
                $(ingredientNameInput).off('input.ingredientName');
                $(ingredientNameInput).on('input.ingredientName', function () {
                    this.value = this.value.replace(/[^a-zA-Z0-9\s\-'\"().,&]/g, '');
                    if (this.value.length > 0) {
                        this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
                    }
                    const value = this.value.trim();
                    if (!value) {
                        ingredientNameError.textContent = '';
                        ingredientNameError.style.display = 'none';
                        this.classList.remove('is-invalid');
                        return;
                    }
                    if (value.length < 2) {
                        ingredientNameError.textContent = 'Minimum 2 characters required';
                        ingredientNameError.style.display = 'block';
                        this.classList.add('is-invalid');
                    } else {
                        ingredientNameError.textContent = '';
                        ingredientNameError.style.display = 'none';
                        this.classList.remove('is-invalid');
                    }
                });
            }

            const categoryInput = document.getElementById('ingredient_category');
            const categoryError = document.getElementById('ingredientCategoryError');

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

            const unitInput = document.getElementById('ingredient_unit');
            const unitError = document.getElementById('ingredientUnitError');

            if (unitInput && unitError) {
                $(unitInput).off('change.unit');
                $(unitInput).on('change.unit', function () {
                    const value = this.value;
                    if (!value) {
                        unitError.textContent = 'Please select a unit';
                        unitError.style.display = 'block';
                        this.classList.add('is-invalid');
                    } else {
                        unitError.textContent = '';
                        unitError.style.display = 'none';
                        this.classList.remove('is-invalid');
                    }
                });
            }
        }

        $('#addIngredientModal').on('shown.bs.modal', function () {
            initializeIngredientFormValidation();
            $('#ingredient_name').focus();
        });

        $('#addIngredientModal').on('hidden.bs.modal', function () {
            $('#addIngredientForm')[0].reset();
            $('#addIngredientForm input, #addIngredientForm select').removeClass('is-invalid');
            $('small[id*="ingredient"]').hide().text('');
        });



        function loadAvailableIngredients() {
            $.get('/ingredient_management/available-ingredients', function (data) {
                const $select = $('#request_ingredient_id');
                $select.empty();
                $select.append('<option value="">-- Select Ingredient --</option>');

                if (data.ingredients && data.ingredients.length > 0) {
                    data.ingredients.forEach(ingredient => {
                        let statusBadge = '';
                        if (ingredient.stock_status === 'critical') {
                            statusBadge = ' ⚠ CRITICAL';
                        } else if (ingredient.stock_status === 'low') {
                            statusBadge = ' ⚠ LOW';
                        }

                        $select.append(
                            $('<option></option>')
                                .val(ingredient.id)
                                .text(ingredient.name + statusBadge)
                                .attr('data-name', ingredient.name)
                                .attr('data-unit', ingredient.unit)
                                .attr('data-reorder', ingredient.reorder_quantity || 0)
                        );
                    });
                } else {
                    $select.append('<option value="" disabled>No ingredients available (all have pending orders)</option>');
                }
            }).fail(function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load ingredients',
                    toast: true,
                    position: 'top',
                    timer: 3000
                });
            });
        }

        $('#addStockModal, #addIngredientModal, #stockBatchModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });

        const today = new Date().toISOString().split('T')[0];
        $('input[name="expiration_date"]').attr('min', today);

        let currentPages = {
            stocks: 1,
            thisweek: 1,
            lastweek: 1
        };

        function loadStocks(page = 1) {
            currentPages.stocks = page;
            $.get(`/ingredient_management/stocks?page=${page}`, function (data) {
                const $tbody = $('#stocksTableBody').empty();
                if (data.ingredients.data.length) {
                    data.ingredients.data.forEach(i => {
                        const status = i.badge_text || 'Good';
                        const badgeClass = i.badge_class || 'bg-success';

                        const isPieces = ['pcs', 'pieces', 'piece', 'pc'].includes(i.unit.toLowerCase());
                        const formattedStock = isPieces ? Math.floor(i.stocks) : parseFloat(i.stocks).toFixed(2);

                        $tbody.append(`
                        <tr>
                            <td class="font-weight-bold">${i.name}</td>
                            <td class="text-capitalize">${i.category}</td>
                            <td>
                                <span class="font-semibold">${formattedStock}</span>
                                <span>${i.unit}</span>
                                <span class="ml-2 px-2 py-1 text-white text-xs font-semibold rounded ${badgeClass}">
                                    ${status}
                                </span>
                            </td>
                        </tr>
                    `);
                    });
                } else {
                    $tbody.append('<tr><td colspan="3" class="text-center text-muted">No ingredients available</td></tr>');
                }
                renderPagination('#stocksPagination', data.ingredients, 'stocks');
            });
        }

        function loadBatches(period, page = 1, ingredientId = 'all') {
            const pre = period === 'thisweek' ? 'thisWeek' : 'lastWeek';
            currentPages[period] = page;

            if (page === 1 && $(`#${pre}TableBody`).is(':empty')) {
                $(`#${pre}Loading`).removeClass('d-none');
                $(`#${pre}Empty, #${pre}Content`).addClass('d-none');
            }

            $.get(`/ingredient_management/stock-batches?period=${period}&page=${page}&ingredient=${ingredientId}`, function (data) {
                $(`#${pre}Loading`).addClass('d-none');

                if (data.batches.data && data.batches.data.length) {
                    $(`#${pre}Content`).removeClass('d-none');
                    $(`#${pre}Empty`).addClass('d-none');
                    const $tb = $(`#${pre}TableBody`).empty();

                    data.batches.data.forEach(b => {
                        const isPieces = ['pcs', 'pieces', 'piece', 'pc'].includes(b.unit.toLowerCase());
                        const formattedQty = isPieces ? Math.floor(b.quantity) : parseFloat(b.quantity).toFixed(2);
                        const expired = b.status === 'expired' || b.quantity <= 0;

                        const expirationDate = new Date(b.expiration_date);
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);
                        expirationDate.setHours(0, 0, 0, 0);
                        const daysUntilExpiry = Math.ceil((expirationDate - today) / (1000 * 60 * 60 * 24));

                        $tb.append(`
                    <tr>
                        <td><code class="text-dark bg-light px-2 py-1 rounded">${b.batch_code || 'N/A'}</code></td>
                        <td><strong>${b.ingredient_name}</strong></td>
                        <td><span class="font-weight-medium">${formattedQty}</span> ${b.unit}</td>
                        <td>${new Date(b.arrived_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</td>
                        <td>
                            <div>${new Date(b.expiration_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</div>
                            ${!expired && daysUntilExpiry <= 30 ?
                                `<small class="text-${daysUntilExpiry <= 7 ? 'danger' : 'warning'} font-weight-bold">
                                    (${daysUntilExpiry} day${daysUntilExpiry !== 1 ? 's' : ''} left)
                                </small>`
                                : ''
                            }
                        </td>
                        <td class="text-center">
                            ${expired
                                ? `<span class="badge badge-danger px-3 py-auto">
                                    <i class="fas fa-times-circle"></i> Expired
                                   </span>`
                                : `
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-sm btn-primary btn-edit-batch" 
                                        data-id="${b.id}"
                                        data-batch-code="${b.batch_code || 'N/A'}"
                                        data-ingredient="${b.ingredient_name}"
                                        data-quantity="${b.quantity}"
                                        data-arrived="${b.arrived_at}"
                                        data-expiry="${b.expiration_date}"
                                        data-unit="${b.unit}"
                                        title="Edit batch details"
                                        style="min-width: 70px;">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-expire-batch" 
                                        data-id="${b.id}" 
                                        data-name="${b.batch_code || b.ingredient_name}"
                                        title="Mark as expired"
                                        style="min-width: 80px;">
                                        <i class="fas fa-ban"></i> Expire
                                    </button>
                                </div>
                                `
                            }
                        </td>
                    </tr>
                `);
                    });

                    renderPagination(`#${period}Pagination`, data.batches, period);
                } else {
                    $(`#${pre}Empty`).removeClass('d-none');
                    $(`#${pre}Content`).addClass('d-none');
                }
            }).fail(function () {
                $(`#${pre}Loading`).addClass('d-none');
                $(`#${pre}Content, #${pre}Empty`).addClass('d-none');

                Swal.fire({
                    icon: 'error',
                    title: 'Failed to Load Batches',
                    text: 'Unable to fetch batch data. Please try again.',
                    confirmButtonColor: '#dc3545'
                });
            });
        }

        $(document).on('click', '.btn-edit-batch', function () {
            const batchId = $(this).data('id');
            const batchCode = $(this).data('batch-code');
            const arrivedAt = $(this).data('arrived');
            const expiryDate = $(this).data('expiry');

            $('#editBatchId').val(batchId);
            $('#editBatchArrived').val(arrivedAt);
            $('#editBatchExpiry').val(expiryDate);

            $('#originalArrived').val(arrivedAt);
            $('#originalExpiry').val(expiryDate);

            $('#stockBatchModal .modal-title').text(`Edit Batch: ${batchCode}`);

            const today = new Date().toISOString().split('T')[0];
            $('#editBatchExpiry').attr('min', today);

            $('#updateBatchBtn').prop('disabled', true);

            $('#stockBatchModal').modal('show');
        });

        $('#editBatchArrived, #editBatchExpiry').on('change', function () {
            const arrivedChanged = $('#editBatchArrived').val() !== $('#originalArrived').val();
            const expiryChanged = $('#editBatchExpiry').val() !== $('#originalExpiry').val();

            $('#updateBatchBtn').prop('disabled', !(arrivedChanged || expiryChanged));
        });


        function renderPagination(selector, data, section) {
            const $pagination = $(selector);
            $pagination.empty();

            if (data.last_page <= 1) return;

            const nav = $('<nav><ul class="pagination pagination-sm justify-content-center mb-0"></ul></nav>');
            const ul = nav.find('ul');

            ul.append(`
            <li class="page-item ${data.current_page <= 1 ? 'disabled' : ''}">
                <a class="page-link ${data.current_page <= 1 ? 'disabled' : ''}" 
                   href="#" data-page="${data.current_page - 1}" data-section="${section}"
                   ${data.current_page <= 1 ? 'tabindex="-1"' : ''}>‹</a>
            </li>
        `);

            let startPage = Math.max(1, data.current_page - 1);
            let endPage = Math.min(data.last_page, data.current_page + 1);

            if (endPage - startPage < 2) {
                if (startPage === 1) {
                    endPage = Math.min(3, data.last_page);
                } else if (endPage === data.last_page) {
                    startPage = Math.max(1, data.last_page - 2);
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                ul.append(`
                <li class="page-item ${i === data.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}" data-section="${section}">${i}</a>
                </li>
            `);
            }

            ul.append(`
            <li class="page-item ${data.current_page >= data.last_page ? 'disabled' : ''}">
                <a class="page-link ${data.current_page >= data.last_page ? 'disabled' : ''}" 
                   href="#" data-page="${data.current_page + 1}" data-section="${section}"
                   ${data.current_page >= data.last_page ? 'tabindex="-1"' : ''}>›</a>
            </li>
        `);

            $pagination.html(nav);
        }

        $(document).on('click', '.page-link:not(.disabled)', function (e) {
            e.preventDefault();
            const page = $(this).data('page');
            const section = $(this).data('section');

            if (!page || !section || $(this).hasClass('disabled')) return;

            switch (section) {
                case 'stocks':
                    loadStocks(page);
                    break;
                case 'thisweek':
                case 'lastweek':
                    loadBatches(section, page);
                    break;
            }
        });

        loadStocks(1);

        let tabsLoaded = {
            stocks: true,
            batch: false
        };

        $('a[href="#stocks"]').on('shown.bs.tab', function () {
            if (!tabsLoaded.stocks) {
                loadStocks(currentPages.stocks);
                tabsLoaded.stocks = true;
            }
        });

        $('a[href="#batch"]').on('shown.bs.tab', function () {
            if (!tabsLoaded.batch) {
                const activePeriod = $('#thisweek-tab').hasClass('active') ? 'thisweek' : 'lastweek';
                loadBatches(activePeriod, currentPages[activePeriod]);
                tabsLoaded.batch = true;
            }
        });

        $('a[href="#thisweek"]').on('shown.bs.tab', () => {
            const ingredientId = $('#ingredientFilter').val() || 'all';
            if (tabsLoaded.batch && $('#thisWeekTableBody').is(':empty')) {
                loadBatches('thisweek', currentPages.thisweek, ingredientId);
            }
        });

        $('a[href="#lastweek"]').on('shown.bs.tab', () => {
            const ingredientId = $('#ingredientFilter').val() || 'all';
            if (tabsLoaded.batch && $('#lastWeekTableBody').is(':empty')) {
                loadBatches('lastweek', currentPages.lastweek, ingredientId);
            }
        });


        $('#addIngredientForm').on('submit', function (e) {
            e.preventDefault();
            submitForm(this, '/ingredient_management/storeIngredient', 'Ingredient added successfully', 'stocks');
        });

        $('#editBatchForm').on('submit', function (e) {
            e.preventDefault();

            const batchId = $('#editBatchId').val();
            const arrivedAt = $('#editBatchArrived').val();
            const expiryDate = $('#editBatchExpiry').val();

            $.ajax({
                url: `/ingredient_management/stock-batches/${batchId}/update`,
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    arrived_at: arrivedAt,
                    expiration_date: expiryDate
                },
                success: function (response) {
                    if (response.success) {
                        $('#stockBatchModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Batch Updated!',
                            toast: true,
                            position: 'top-end',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        const activePeriod = $('#thisweek-tab').hasClass('active') ? 'thisweek' : 'lastweek';
                        loadBatches(activePeriod, currentPages[activePeriod]);
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Failed to update batch'
                    });
                }
            });
        });

        $('#request_ingredient_id').on('change', function () {
            const selected = $(this).find('option:selected');
            const unit = selected.data('unit');
            const reorderQty = selected.data('reorder') || 0;

            $('#request_unit').text(unit || 'unit');

            let currentQuantity = parseFloat($('#request_quantity').val()) || 0;

            if (unit && (unit.toLowerCase() === 'pieces' || unit.toLowerCase() === 'pcs' || unit.toLowerCase() === 'piece')) {
                $('#request_quantity').attr('step', '1');
                $('#request_quantity').attr('min', '1');

                if (reorderQty && !currentQuantity) {
                    $('#request_quantity').val(Math.round(reorderQty));
                } else if (currentQuantity && currentQuantity % 1 !== 0) {
                    $('#request_quantity').val(Math.round(currentQuantity));
                }
            } else {
                $('#request_quantity').attr('step', '0.01');
                $('#request_quantity').attr('min', '0.01');

                if (reorderQty && !currentQuantity) {
                    $('#request_quantity').val(reorderQty);
                }
            }
        });

        $('#request_quantity').on('input', function () {
            const unit = $('#request_unit').text();

            if (unit && (unit.toLowerCase() === 'pieces' || unit.toLowerCase() === 'pcs' || unit.toLowerCase() === 'piece')) {
                let value = $(this).val();
                if (value.includes('.')) {
                    $(this).val(Math.floor(parseFloat(value)));
                }
            }
        });

        $('#RequestStockForm').on('submit', function (e) {
            e.preventDefault();

            const ingredientId = $('#request_ingredient_id').val();
            const ingredientName = $('#request_ingredient_id option:selected').data('name');
            const quantity = $('#request_quantity').val();
            const unit = $('#request_unit').text();

            if (!ingredientId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Please select an ingredient',
                    timer: 2000
                });
                return;
            }

            if (unit && (unit.toLowerCase() === 'pieces' || unit.toLowerCase() === 'pcs' || unit.toLowerCase() === 'piece')) {
                if (parseFloat(quantity) % 1 !== 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Quantity',
                        text: 'Pieces must be a whole number',
                        timer: 2000
                    });
                    return;
                }
            }

            Swal.fire({
                title: 'Create Stock Order?',
                html: `Create order for <strong>${quantity} ${unit}</strong> of <strong>${ingredientName}</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Create Order',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/ingredient_management/stock-orders/create',
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            ingredient_id: ingredientId,
                            quantity: quantity
                        },
                        success: function (response) {
                            $('#addStockModal').modal('hide');

                            Swal.fire({
                                icon: 'success',
                                title: 'Order Created!',
                                text: 'Stock order has been created successfully',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: xhr.responseJSON?.message || 'Failed to create stock order'
                            });
                        }
                    });
                }
            });
        });

        $('#addStockModal').on('shown.bs.modal', function () {
            loadAvailableIngredients();
        });

        $('#addStockModal').on('hidden.bs.modal', function () {
            $('#RequestStockForm')[0].reset();
            $('#request_unit').text('unit');
        });

        function submitFormStockOrder(form, url) {
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    ingredient_id: $(form).find('#request_ingredient_id').val(),
                    quantity: $(form).find('#request_quantity').val()
                },
                success: (response) => {
                    if (response.success) {
                        $(form).closest('.modal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Order Created!',
                            text: 'Stock order has been created successfully',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: (xhr) => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Failed to create stock order'
                    });
                }
            });
        }

        function submitForm(form, url, msg, refreshSection) {
            $.ajax({
                url,
                method: 'POST',
                data: JSON.stringify($(form).serializeArray().reduce((o, i) => (o[i.name] = i.value, o), {})),
                contentType: 'application/json',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: (response) => {
                    if (response.success) {
                        $(form).closest('.modal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: msg,
                            toast: true,
                            position: 'top',
                            timer: 3000,
                            showConfirmButton: false
                        });
                        if (refreshSection === 'stocks') {
                            loadStocks(currentPages.stocks);
                        }
                    }
                },
                error: (xhr) => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Operation failed',
                        toast: true,
                        position: 'top',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            });
        }





        // Add Ingredient Category
        $('#addIngredientCategoryForm').on('submit', function (e) {
            e.preventDefault();
            $('#CategoryNameError').hide().text('');

            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.text();
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-2"></span>Adding...');

            $.ajax({
                url: "{{ route('ingredient.addCategory') }}",
                method: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Category added successfully',
                        toast: true,
                        position: 'top',
                        timer: 3000,
                        showConfirmButton: false
                    });

                    setTimeout(() => {
                        $('#addIngredientCategoryModal').modal('hide');
                        $('#addIngredientCategoryForm')[0].reset();
                        location.reload();
                    }, 500);
                },
                error: function (xhr) {
                    submitBtn.prop('disabled', false).text(originalText);

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.name) {
                            $('#CategoryNameError').text(errors.name[0]).show();
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred. Please try again.'
                        });
                    }
                }
            });
        });

        // Add Unit of Measure
        $('#addUnitOfMeasureForm').on('submit', function (e) {
            e.preventDefault();
            $('#unitNameError, #unitAbbreviationError').hide().text('');

            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.text();
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-2"></span>Adding...');

            $.ajax({
                url: "{{ route('ingredient.addUnit') }}",
                method: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Unit of measure added successfully',
                        toast: true,
                        position: 'top',
                        timer: 3000,
                        showConfirmButton: false
                    });

                    setTimeout(() => {
                        $('#addUnitOfMeasureModal').modal('hide');
                        $('#addUnitOfMeasureForm')[0].reset();
                        location.reload();
                    }, 500);
                },
                error: function (xhr) {
                    submitBtn.prop('disabled', false).text(originalText);

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.name) {
                            $('#unitNameError').text(errors.name[0]).show();
                        }
                        if (errors.abbreviation) {
                            $('#unitAbbreviationError').text(errors.abbreviation[0]).show();
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred. Please try again.'
                        });
                    }
                }
            });
        });
    });

    $(document).on('click', '.btn-expire-batch', function () {
        const id = $(this).data('id');
        const code = $(this).data('name');

        Swal.fire({
            title: 'Mark Batch as Expired?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-ban"></i> Yes, Mark as Expired',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/ingredient_management/stock-batches/${id}/expire`,
                    type: 'PUT',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Batch marked as expired successfully',
                            toast: true,
                            position: 'top',
                            timer: 3000,
                            showConfirmButton: false
                        });

                        setTimeout(() => {
                            const activePeriod = $('#thisweek-tab').hasClass('active') ? 'thisweek' : 'lastweek';
                            loadBatches(activePeriod, currentPages[activePeriod]);
                        }, 500);
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed!',
                            text: xhr.responseJSON?.message || 'Failed to mark batch as expired. Please try again.',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            }
        });
    });

</script>
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
                                <a class="nav-link" id="expired-tab" data-toggle="tab" href="#expired"
                                    role="tab">Expired Ingredients</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="stock-order-tab" data-toggle="tab" href="#stock-order"
                                    role="tab">Stock Order</a>
                            </li>
                        </ul>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#addStockModal">Add
                            New Stock</button>

                        <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#addIngredientModal">Add
                            Ingredient</button>
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
                            <ul class="nav nav-pills mb-3" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="thisweek-tab" data-toggle="pill" href="#thisweek"
                                        role="tab">This Week</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="lastweek-tab" data-toggle="pill" href="#lastweek"
                                        role="tab">Previous Week</a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="thisweek" role="tabpanel">
                                    <div id="thisWeekEmpty" class="text-center py-4 text-muted d-none">No stock batches
                                        added this week</div>
                                    <div id="thisWeekContent" class="d-none">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Ingredient</th>
                                                        <th>Quantity</th>
                                                        <th>Arrived Date</th>
                                                        <th>Expiration Date</th>
                                                        <th width="100">Actions</th>
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
                                    <div id="lastWeekLoading" class="text-center py-4 text-muted">Loading...</div>
                                    <div id="lastWeekEmpty" class="text-center py-4 text-muted d-none">No stock batches
                                        from previous week</div>
                                    <div id="lastWeekContent" class="d-none">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Ingredient</th>
                                                        <th>Quantity</th>
                                                        <th>Arrived Date</th>
                                                        <th>Expiration Date</th>
                                                        <th width="100">Actions</th>
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

                        {{-- Expired Tab --}}
                        <div class="tab-pane fade" id="expired" role="tabpanel">
                            <div id="expiredEmpty" class="text-center py-4 text-muted d-none">No expired ingredients in
                                history</div>
                            <div id="expiredContent" class="d-none">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Ingredient</th>
                                                <th>Quantity</th>
                                                <th>Expired at</th>
                                            </tr>
                                        </thead>
                                        <tbody id="expiredTableBody"></tbody>
                                    </table>
                                </div>
                                <div class="mt-3 d-flex justify-content-center">
                                    <div id="expiredPagination"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Order Tab -->
                        <div class="tab-pane fade" id="stock-order" role="tabpanel" aria-labelledby="stock-order-tab">

                            <!-- Low Stock Orders Alert Section -->
                            <div class="card shadow-sm mb-3 mt-3 border-warning">
                                <div class="card-header bg-warning text-dark py-2">
                                    <h6 class="mb-0 font-weight-bold">
                                        Low Stock Ingredient
                                    </h6>
                                </div>
                                <div class="card-body p-2">
                                    <div id="lowStockOrdersList">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Add Stock Modal --}}
    <div class="modal fade" id="addStockModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Confirm Add Stock</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form id="addStockForm">
                    <div class="modal-body">
                        <p class="mb-3">Are you sure you want to add stock for <strong
                                id="addStockIngredientName"></strong>?</p>

                        <input type="hidden" name="ingredient_id" id="addStock_ingredient_id">

                        <div class="form-group">
                            <label class="font-weight-bold">Enter quantity received (<span
                                    id="addStockUnit"></span>)</label>
                            <input type="number" name="quantity" id="addStockQuantity" class="form-control" step="0.01"
                                min="0.01" required>
                        </div>

                        <div class="form-group">
                            <label>Arrived Date</label>
                            <input type="date" name="arrived_at" id="addStock_arrivedDate" class="form-control"
                                required>
                        </div>

                        <div class="form-group mb-0">
                            <label>Expiration Date</label>
                            <input type="date" name="expiration_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> Confirm
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
                            <input type="text" name="name" id="ingredient_name" class="form-control" required
                                minlength="2">
                            <div>
                                <small id="ingredientNameError" class="text-danger text-sm"
                                    style="display: none;"></small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Category <span class="text-danger">*</span></label>
                            <select name="category" id="ingredient_category" class="form-control" required>
                                <option value="">Select Category</option>
                                <option value="meat">Meat</option>
                                <option value="vegetables">Vegetables</option>
                                <option value="soupbase">Soup Base</option>
                                <option value="beverage">Beverage</option>
                            </select>
                            <div>
                                <small id="ingredientCategoryError" class="text-danger text-sm"
                                    style="display: none;"></small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Unit <span class="text-danger">*</span></label>
                            <select name="unit" id="ingredient_unit" class="form-control" required>
                                <option value="">Select Unit</option>
                                <option value="kg">Kilograms</option>
                                <option value="pieces">Pieces</option>
                            </select>
                            <div>
                                <small id="ingredientUnitError" class="text-danger text-sm"
                                    style="display: none;"></small>
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
    <div class="modal fade" id="editBatchModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h6 class="modal-title mb-0">Edit Batch</h6>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form id="editBatchForm">
                    <input type="hidden" id="editBatchId">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="small">Quantity</label>
                            <input type="number" id="editBatchQty" class="form-control form-control-sm" step="0.01"
                                min="0.01" required>
                        </div>
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
                        <button type="submit" class="btn btn-warning btn-sm">Update</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@include('admin.layouts.script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

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
    $('#addStockModal, #updateStockModal, #addIngredientModal, #editBatchModal').modal({
        backdrop: 'static',
        keyboard: false,
        show: false
    });

    $(document).ready(function () {
        const today = new Date().toISOString().split('T')[0];
        $('input[name="expiration_date"]').attr('min', today);

        let currentPages = {
            stocks: 1,
            thisweek: 1,
            lastweek: 1,
            expired: 1
            ,
            stockorder: 1
        };

        function loadStocks(page = 1) {
            currentPages.stocks = page;

            if (page === 1 && $('#stocksTableBody').is(':empty')) {
                $('#stocksLoading').removeClass('d-none');
                $('#stocksContent').addClass('d-none');
            }

            $.get(`/ingredient_management/stocks?page=${page}`, function (data) {
                if ($('#stocksLoading').is(':visible')) {
                    $('#stocksLoading').addClass('d-none');
                    $('#stocksContent').removeClass('d-none');
                }

                const $tbody = $('#stocksTableBody').empty();
                if (data.ingredients.data.length) {
                    data.ingredients.data.forEach(i => {
                        const status = i.badge_text || 'Good';
                        const badgeClass = i.badge_class || 'bg-success';

                        $tbody.append(`
                            <tr>
                                <td class="font-weight-bold">${i.name}</td>
                                <td class="text-capitalize">${i.category}</td>
                                <td>
                                    <span class="font-semibold">${parseFloat(i.stocks).toFixed(2)}</span>
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

        function loadBatches(period, page = 1) {
            const pre = period === 'thisweek' ? 'thisWeek' : 'lastWeek';
            currentPages[period] = page;

            if (page === 1 && $(`#${pre}TableBody`).is(':empty')) {
                $(`#${pre}Loading`).removeClass('d-none');
                $(`#${pre}Empty, #${pre}Content`).addClass('d-none');
            }

            $.get(`/ingredient_management/stock-batches?period=${period}&page=${page}`, function (data) {
                if ($(`#${pre}Loading`).is(':visible')) {
                    $(`#${pre}Loading`).addClass('d-none');
                }

                if (data.batches.data && data.batches.data.length) {
                    $(`#${pre}Content`).removeClass('d-none');
                    $(`#${pre}Empty`).addClass('d-none');
                    const $tb = $(`#${pre}TableBody`).empty();

                    data.batches.data.forEach(b => {
                        $tb.append(`
                            <tr>
                                <td>${b.ingredient_name}</td>
                                <td>${parseFloat(b.quantity).toFixed(2)} ${b.unit}</td>
                                <td>${new Date(b.arrived_at).toLocaleDateString()}</td>
                                <td>${new Date(b.expiration_date).toLocaleDateString()}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning btn-edit-batch" data-id="${b.id}" 
                                        data-qty="${b.quantity}" data-arrived="${b.arrived_at}" data-exp="${b.expiration_date}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-del-batch" data-id="${b.id}" data-name="${b.ingredient_name}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                    });

                    renderPagination(`#${period}Pagination`, data.batches, period);
                } else {
                    $(`#${pre}Empty`).removeClass('d-none');
                    $(`#${pre}Content`).addClass('d-none');
                }
            });
        }

        function loadExpired(page = 1) {
            currentPages.expired = page;

            if (page === 1 && $('#expiredTableBody').is(':empty')) {
                $('#expiredLoading').removeClass('d-none');
                $('#expiredEmpty, #expiredContent').addClass('d-none');
            }

            $.get(`/ingredient_management/expired-only?page=${page}`, function (data) {
                if ($('#expiredLoading').is(':visible')) {
                    $('#expiredLoading').addClass('d-none');
                }

                if (data.expired_items.data && data.expired_items.data.length) {
                    $('#expiredContent').removeClass('d-none');
                    $('#expiredEmpty').addClass('d-none');
                    const $tb = $('#expiredTableBody').empty();

                    data.expired_items.data.forEach(i => {
                        $tb.append(`
                            <tr>
                                <td>${i.ingredient_name}</td>
                                <td>${parseFloat(i.quantity).toFixed(2)} ${i.unit}</td>
                                <td>${new Date(i.expiration_date).toLocaleDateString()}</td>
                            </tr>
                        `);
                    });

                    renderPagination('#expiredPagination', data.expired_items, 'expired');
                } else {
                    $('#expiredEmpty').removeClass('d-none');
                    $('#expiredContent').addClass('d-none');
                }
            });
        }

        function loadStockOrders(page = 1) {
            currentPages.stockorder = page;

            if (page === 1 && $('#stockOrderTableBody').is(':empty')) {
                $('#stockOrderLoading').removeClass('d-none');
                $('#stockOrderEmpty, #stockOrderContent').addClass('d-none');
            }

            $.get(`/ingredient_management/stock-orders?page=${page}`, function (data) {
                $('#stockOrderLoading').addClass('d-none');

                if (data.stock_orders.data && data.stock_orders.data.length) {
                    // Filter to only show triggered stock orders
                    const triggeredOrders = data.stock_orders.data.filter(order =>
                        order.ingredient.stocks <= order.reorder_point
                    );

                    if (triggeredOrders.length > 0) {
                        $('#stockOrderContent').removeClass('d-none');
                        $('#stockOrderEmpty').addClass('d-none');
                        const $tb = $('#stockOrderTableBody').empty();

                        triggeredOrders.forEach(order => {
                            const unit = order.ingredient.unit === 'pieces' ? 'pcs' : order.ingredient.unit;

                            $tb.append(`
                                <tr class="table-warning">
                                    <td>
                                        <strong>${order.ingredient.name}</strong>
                                        <br>
                                        <small class="text-muted">Current: ${parseFloat(order.ingredient.stocks).toFixed(2)} ${unit}</small>
                                    </td>
                                    <td>${parseFloat(order.reorder_point).toFixed(2)} ${unit}</td>
                                    <td class="text-center">
                                        <span class="badge badge-${getStatusBadge(order.status)}">${order.status}</span>
                                        <br><small class="text-danger font-weight-bold">⚠️ Stock Low</small>
                                    </td>
                                </tr>
                            `);
                        });

                        renderPagination('#stockOrderPagination', data.stock_orders, 'stockorder');
                    } else {
                        $('#stockOrderEmpty').removeClass('d-none');
                        $('#stockOrderContent').addClass('d-none');
                        $('#stockOrderTableBody').empty();
                    }
                } else {
                    $('#stockOrderEmpty').removeClass('d-none');
                    $('#stockOrderContent').addClass('d-none');
                }
            });
        }

        function loadLowStockOrders() {
            $.get('/ingredient_management/stock-orders/low-stock', function (data) {
                const $container = $('#lowStockOrdersList').empty();

                if (data.low_stock_orders && data.low_stock_orders.length > 0) {
                    data.low_stock_orders.forEach(order => {
                        const unit = order.ingredient.unit === 'pieces' ? 'pcs' : order.ingredient.unit;

                        const quantityNeeded = (order.reorder_point - order.ingredient.stocks).toFixed(2);

                        $container.append(`
                    <div class="card mb-2 border-warning">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 font-weight-bold">${order.ingredient.name}</h6>
                                    <div class="small text-muted">
                                        <div><strong>Current Stock:</strong> ${parseFloat(order.ingredient.stocks).toFixed(2)} ${unit}</div>
                                        
                                        <!-- FIX 2: Changed from request_alert_value to reorder_point -->
                                        <div><strong>Order Threshold:</strong> ${parseFloat(order.reorder_point).toFixed(2)} ${unit}</div>
                                        
                                        <!-- FIX 3: Changed from undefined reorder_quantity to order.reorder_quantity -->
                                        <div><strong>Need to Order:</strong> ${parseFloat(order.reorder_quantity).toFixed(2)} ${unit}</div>
                                        
                                    </div>
                                </div>
                                <div class="ml-2">
                                    <button class="btn btn-sm btn-success add-stock-btn mb-1" 
                                            data-order-id="${order.id}"
                                            data-ingredient="${order.ingredient.name}"
                                            data-ingredient-id="${order.ingredient.id}"
                                            data-current-stock="${order.ingredient.stocks}"
                                            data-reorder-quantity="${order.reorder_quantity}"
                                            data-unit="${unit}">
                                        <i class="fas fa-plus-circle"></i> Add Stock
                                    </button>
                                    <button class="btn btn-sm btn-primary print-order-btn" 
                                            data-order-id="${order.id}"
                                            data-ingredient="${order.ingredient.name}"
                                            data-quantity="${quantityNeeded}"
                                            data-unit="${unit}">
                                        <i class="fas fa-print"></i> Print
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
                    });
                } else {
                    $container.html('<div class="text-center text-muted py-3"><small>No low stock orders at the moment</small></div>');
                }
            });
        }

        function getStatusBadge(status) {
            const badges = {
                'pending': 'warning',
                'ordered': 'info',
                'received': 'success',
                'cancelled': 'danger'
            };
            return badges[status] || 'secondary';
        }

        // Print order button handler
        $(document).on('click', '.print-order-btn', function () {
            const orderId = $(this).data('order-id');
            const ingredient = $(this).data('ingredient');
            const quantity = $(this).data('quantity');
            const unit = $(this).data('unit');

            console.log('Print order request:', { orderId, ingredient, quantity, unit });

            Swal.fire({
                icon: 'info',
                title: 'Print Request',
                html: `
                    <strong>Ingredient:</strong> ${ingredient}<br>
                    <strong>Quantity:</strong> ${quantity} ${unit}
                `,
                confirmButtonText: 'OK'
            });

        });

        $(document).on('click', '.add-stock-btn', function () {
            const ingredient = $(this).data('ingredient');
            const ingredientId = $(this).data('ingredient-id');
            const reorderQuantity = $(this).data('reorder-quantity');
            const unit = $(this).data('unit');

            $('#addStockIngredientName').text(ingredient);
            $('#addStock_ingredient_id').val(ingredientId);
            $('#addStockUnit').text(unit);

            $('#addStockQuantity').val(parseFloat(reorderQuantity).toFixed(2));

            const today = new Date().toISOString().split('T')[0];
            $('#addStock_arrivedDate').val(today);

            $('#addStockModal').modal('show');
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
                       href="#" 
                       data-page="${data.current_page - 1}" 
                       data-section="${section}"
                       ${data.current_page <= 1 ? 'tabindex="-1"' : ''}>
                        ‹
                    </a>
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
                       href="#" 
                       data-page="${data.current_page + 1}" 
                       data-section="${section}"
                       ${data.current_page >= data.last_page ? 'tabindex="-1"' : ''}>
                        ›
                    </a>
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
                case 'expired':
                    loadExpired(page);
                    break;
                case 'stockorder':
                    loadStockOrders(page);
                    break;
            }
        });

        loadStocks(1);

        let tabsLoaded = {
            stocks: true,
            batch: false,
            expired: false,
            stockorder: false
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
            if (tabsLoaded.batch && $('#thisWeekTableBody').is(':empty')) {
                loadBatches('thisweek', currentPages.thisweek);
            }
        });

        $('a[href="#lastweek"]').on('shown.bs.tab', () => {
            if (tabsLoaded.batch && $('#lastWeekTableBody').is(':empty')) {
                loadBatches('lastweek', currentPages.lastweek);
            }
        });

        $('a[href="#expired"]').on('shown.bs.tab', function () {
            if (!tabsLoaded.expired) {
                loadExpired(currentPages.expired);
                tabsLoaded.expired = true;
            }
        });

        $('a[href="#stock-order"]').on('shown.bs.tab', function () {
            if (!tabsLoaded.stockorder) {
                loadStockOrders(currentPages.stockorder);
                loadLowStockOrders();
                tabsLoaded.stockorder = true;
            }
        });

        $('#addStockModal').on('show.bs.modal', () => loadIngredients('#addStockForm select[name="ingredient_id"]'));
        $('#updateStockModal').on('show.bs.modal', () => loadIngredients('#updateStockForm select[name="ingredient_id"]', true));

        $('#addStockForm').on('submit', function (e) {
            e.preventDefault();
            submitForm(this, '/ingredient_management/add-stock', 'Stock added successfully', 'stocks');
        });

        $('#updateStockForm').on('submit', function (e) {
            e.preventDefault();
            submitForm(this, '/ingredient_management/update-stock', 'Stock updated successfully', 'stocks');
        });

        $('#addIngredientForm').on('submit', function (e) {
            e.preventDefault();
            submitForm(this, '/ingredient_management/storeIngredient', 'Ingredient added successfully', 'stocks');
        });

        $('#editBatchForm').on('submit', function (e) {
            e.preventDefault();
            updateBatch();
        });

        function loadIngredients(selector, showStock = false) {
            $.get('/ingredient_management/addStockForm', data => {
                const $sel = $(selector).find('option:not(:first)').remove().end();
                const ingredientsData = {};

                data.ingredients.forEach(i => {
                    const unit = i.unit.toLowerCase() === 'pieces' ? 'pcs' : i.unit;
                    ingredientsData[i.id] = { unit: unit, name: i.name };
                    $sel.append(`<option value="${i.id}" data-unit="${unit}">${i.name}${showStock ? ` (${i.stocks} ${unit})` : ''}</option>`);
                });

                $sel.data('ingredientsData', ingredientsData);
            });
        }

        $(document).on('change', '#addStock_ingredient', function () {
            const selectedOption = $(this).find('option:selected');
            const unit = selectedOption.data('unit');

            if (unit) {
                $('#addStock_unitLabel').text(`(${unit})`);
            } else {
                $('#addStock_unitLabel').text('');
            }
        });

        $('#addStockModal').on('hidden.bs.modal', function () {
            $('#addStock_unitLabel').text('');
            $('#addStockForm')[0].reset();
        });

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
                            showConfirmButton: false,
                            background: '#d4edda',
                            color: '#155724'
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
                        showConfirmButton: false,
                        background: '#f8d7da',
                        color: '#721c24'
                    });
                }
            });
        }

        function updateBatch() {
            const id = $('#editBatchId').val();
            $.ajax({
                url: `/ingredient_management/batches/${id}`,
                method: 'PUT',
                data: JSON.stringify({
                    quantity: $('#editBatchQty').val(),
                    arrived_at: $('#editBatchArrived').val(),
                    expiration_date: $('#editBatchExpiry').val()
                }),
                contentType: 'application/json',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: (response) => {
                    if (response.success) {
                        $('#editBatchModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Batch updated successfully',
                            toast: true,
                            position: 'top',
                            timer: 3000,
                            showConfirmButton: false,
                            background: '#d4edda',
                            color: '#155724'
                        });

                        const period = $('.nav-link.active[data-toggle="pill"]').attr('href').includes('thisweek') ? 'thisweek' : 'lastweek';
                        loadBatches(period, currentPages[period]);
                    }
                },
                error: (xhr) => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Update failed',
                        toast: true,
                        position: 'top',
                        timer: 3000,
                        showConfirmButton: false,
                        background: '#f8d7da',
                        color: '#721c24'
                    });
                }
            });
        }

        $(document).on('click', '.btn-edit-batch', function () {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const minDate = tomorrow.toISOString().split('T')[0];

            $('#editBatchId').val($(this).data('id'));
            $('#editBatchQty').val($(this).data('qty'));
            $('#editBatchArrived').val($(this).data('arrived'));
            $('#editBatchExpiry').val($(this).data('exp')).attr('min', minDate);
            $('#editBatchModal').modal('show');
        });

        $(document).on('click', '.btn-del-batch', function () {
            const id = $(this).data('id');
            const name = $(this).data('name');
            Swal.fire({
                title: 'Delete Batch?',
                text: `Remove ${name} batch?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Delete'
            }).then(r => {
                if (r.isConfirmed) {
                    $.ajax({
                        url: `/ingredient_management/batches/delete`,
                        method: 'DELETE',
                        data: JSON.stringify({ batch_id: id }),
                        contentType: 'application/json',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: (response) => {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Batch removed successfully',
                                    toast: true,
                                    position: 'top',
                                    timer: 3000,
                                    showConfirmButton: false,
                                    background: '#d4edda',
                                    color: '#155724'
                                });

                                const period = $('.nav-link.active[data-toggle="pill"]').attr('href').includes('thisweek') ? 'thisweek' : 'lastweek';
                                loadBatches(period, currentPages[period]);
                            }
                        },
                        error: (xhr) => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Delete failed',
                                toast: true,
                                position: 'top',
                                timer: 3000,
                                showConfirmButton: false,
                                background: '#f8d7da',
                                color: '#721c24'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
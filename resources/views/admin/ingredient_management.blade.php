@include('admin.layouts.header')
@include('admin.layouts.sidebar')

<div id="content-wrapper" class="d-flex flex-column h-screen overflow-y-auto">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h1 class="h3 mb-0 text-gray-800">Ingredients Management</h1>
        </nav>

        <div class="container-fluid">
            <div class="card shadow mb-4">
                <div
                    class="card-header py-3 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
                    <div class="mb-3 mb-sm-0">
                        <h6 class="m-0 font-weight-bold text-primary">Ingredients</h6>
                        <ul class="nav nav-tabs mt-3 border-bottom-0" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="stocks-tab" data-toggle="tab" href="#stocks" role="tab">
                                    Stocks
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="batch-tab" data-toggle="tab" href="#batch" role="tab">
                                    Stock Batches
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="expired-tab" data-toggle="tab" href="#expired" role="tab">
                                    Expired Ingredients
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#addStockModal">
                            Add New Stock
                        </button>
                        <button class="btn btn-success btn-sm mr-2" data-toggle="modal" data-target="#updateStockModal">
                            Update Stock
                        </button>
                        <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#addIngredientModal">
                            Add Ingredient
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
                                            <th>Unit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($ingredients as $ingredient)
                                            <tr>
                                                <td class="font-weight-bold">{{ $ingredient->name }}</td>
                                                <td class="text-capitalize">{{ $ingredient->category }}</td>
                                                <td>
                                                    <span
                                                        class="badge badge-{{ $ingredient->stocks > 0 ? 'success' : 'danger' }}">
                                                        {{ $ingredient->stocks }}
                                                    </span>
                                                </td>
                                                <td>{{ $ingredient->unit }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No ingredients available</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="batch" role="tabpanel">
                            <ul class="nav nav-pills mb-3" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="thisweek-tab" data-toggle="pill" href="#thisweek"
                                        role="tab">
                                        This Week
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="lastweek-tab" data-toggle="pill" href="#lastweek"
                                        role="tab">
                                        Previous Week
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="thisweek" role="tabpanel">
                                    <div id="thisWeekLoading" class="text-center py-4 text-muted">Loading...</div>
                                    <div id="thisWeekEmpty" class="text-center py-4 text-muted d-none">No stock batches
                                        added this week</div>
                                    <div id="thisWeekContent" class="d-none">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Ingredient</th>
                                                        <th>Quantity</th>
                                                        <th>Unit</th>
                                                        <th>Expiration Date</th>
                                                        <th>Added Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="thisWeekTableBody"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="lastweek" role="tabpanel">
                                    <div id="lastWeekLoading" class="text-center py-4 text-muted">Loading...</div>
                                    <div id="lastWeekEmpty" class="text-center py-4 text-muted d-none">No stock batches
                                        from previous week</div>
                                    <div id="lastWeekContent" class="d-none">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Ingredient</th>
                                                        <th>Quantity</th>
                                                        <th>Unit</th>
                                                        <th>Expiration Date</th>
                                                        <th>Added Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="lastWeekTableBody"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="expired" role="tabpanel">
                            <div id="expiredLoading" class="text-center py-4 text-muted">Loading...</div>
                            <div id="expiredEmpty" class="text-center py-4 text-muted d-none">No expired ingredients in
                                history</div>
                            <div id="expiredContent" class="d-none">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Ingredient</th>
                                                <th>Quantity</th>
                                                <th>Unit</th>
                                                <th>Expired at</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody id="expiredTableBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addStockModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Add New Stock</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="addStockForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Ingredient</label>
                            <select name="ingredient_id" class="form-control" required>
                                <option value="">Select Ingredient</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" name="quantity" class="form-control" step="0.01" min="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>Expiration Date</label>
                            <input type="date" name="expiration_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateStockModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Update Stock</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="updateStockForm">
                    <div class="modal-body">
                        <p class="text-muted small mb-3">Correct mistakes in stock quantities to match exact amounts</p>
                        <div class="form-group">
                            <label>Ingredient</label>
                            <select name="ingredient_id" class="form-control" required>
                                <option value="">Select Ingredient</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>New Quantity</label>
                            <input type="number" name="new_quantity" class="form-control" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Update Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addIngredientModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Add New Ingredient</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="addIngredientForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Ingredient Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter ingredient name"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category" class="form-control" required>
                                <option value="">Select Category</option>
                                <option value="meat">Meat</option>
                                <option value="vegetables">Vegetables</option>
                                <option value="soupbase">Soup Base</option>
                                <option value="beverage">Beverage</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Unit</label>
                            <select name="unit" class="form-control" required>
                                <option value="">Select Unit</option>
                                <option value="grams">Grams</option>
                                <option value="pieces">Pieces</option>
                            </select>
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
</div>

@include('admin.layouts.script')
<script>
    $(document).ready(function () {
        const today = new Date().toISOString().split('T')[0];
        $('input[name="expiration_date"]').attr('min', today);

        $('#addStockModal').on('show.bs.modal', function () {
            loadIngredientsForModal('#addStockForm select[name="ingredient_id"]', 'addStockForm');
        });

        $('#updateStockModal').on('show.bs.modal', function () {
            loadIngredientsForModal('#updateStockForm select[name="ingredient_id"]', 'updateStockForm', true);
        });

        $('a[href="#batch"]').on('shown.bs.tab', function () {
            loadStockBatchesByPeriod('thisweek');
        });

        $('a[href="#thisweek"]').on('shown.bs.tab', function () {
            loadStockBatchesByPeriod('thisweek');
        });

        $('a[href="#lastweek"]').on('shown.bs.tab', function () {
            loadStockBatchesByPeriod('lastweek');
        });


        $('a[href="#expired"]').on('shown.bs.tab', function () {
            loadExpiredHistory();
        });

        $('#addStockForm').on('submit', function (e) {
            e.preventDefault();
            submitAddStock(this);
        });

        $('#updateStockForm').on('submit', function (e) {
            e.preventDefault();
            submitUpdateStock(this);
        });

        $('#addIngredientForm').on('submit', function (e) {
            e.preventDefault();
            submitAddIngredient(this);
        });

        function loadIngredientsForModal(selector, endpoint, showStocks = false) {
            $.ajax({
                url: `/ingredient_management/${endpoint}`,
                method: 'GET',
                success: function (data) {
                    const select = $(selector);
                    select.find('option:not(:first)').remove();

                    data.ingredients.forEach(function (ingredient) {
                        let optionText = ingredient.name;
                        if (showStocks) {
                            optionText += ` (Current: ${ingredient.stocks} ${ingredient.unit})`;
                        }
                        select.append(`<option value="${ingredient.id}">${optionText}</option>`);
                    });
                },
                error: function () {
                    Swal.fire('Error', 'Failed to load ingredients', 'error');
                }
            });
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

      
        function loadStockBatchesByPeriod(period) {
            const prefix = period === 'thisweek' ? 'thisWeek' : 'lastWeek';

            showLoading(prefix);

            $.ajax({
                url: `/ingredient_management/stock-batches?period=${period}`,
                method: 'GET',
                success: function (data) {
                    hideLoading(prefix);

                    if (data.batches.length === 0) {
                        showEmpty(prefix);
                    } else {
                        showContent(prefix);
                        renderStockBatchesTable(data.batches, `${prefix}TableBody`);
                    }
                },
                error: function () {
                    hideLoading(prefix);
                    Swal.fire('Error', 'Failed to load stock batches', 'error');
                }
            });
        }

        function renderStockBatchesTable(batches, tbodyId) {
            const tbody = $(`#${tbodyId}`);
            tbody.empty();

            batches.forEach(function (batch) {
                tbody.append(`
                    <tr>
                        <td class="font-weight-bold">${batch.ingredient_name}</td>
                        <td>${parseFloat(batch.quantity).toFixed(2)}</td>
                        <td>${batch.unit}</td>
                        <td>${formatDate(batch.expiration_date)}</td>
                        <td>${formatDate(batch.created_at)}</td>
                    </tr>
                `);
            });
        }

       
        function loadExpiredHistory() {
            console.log('Loading expired history from expired_ingredients table...');

            showExpiredLoading();

            $.ajax({
                url: '/ingredient_management/expired-only',
                method: 'GET',
                success: function (data) {
                    console.log('Expired history items received:', data.expired_items.length);

                    hideExpiredLoading();

                    if (data.expired_items.length === 0) {
                        showExpiredEmpty();
                    } else {
                        showExpiredContent();
                        renderExpiredHistoryTable(data.expired_items);
                    }
                },
                error: function (xhr) {
                    console.error('Error loading expired history:', xhr);
                    hideExpiredLoading();
                    Swal.fire('Error', 'Failed to load expired items', 'error');
                }
            });
        }

        function renderExpiredHistoryTable(items) {
            const tbody = $('#expiredTableBody');
            tbody.empty();

            items.forEach(function (item) {
                tbody.append(`
            <tr class="">
                <td class="font-weight-bold">${item.ingredient_name}</td>
                <td>${parseFloat(item.quantity).toFixed(2)}</td>
                <td>${item.unit}</td>
                <td>${formatDate(item.expiration_date)}</td>
                <td>
                    ${item.notes || 'N/A'}
                </td>
            </tr>
        `);
            });
        }

        window.removeBatch = function (batchId, ingredientName, quantity) {
            Swal.fire({
                title: 'Are You Sure You Want to Remove this Ingredients Batch?',
                html: `
                    <div class="alert alert-warning mb-0">
                        <strong>${ingredientName}</strong><br>
                        <span class="text-muted">Quantity: ${quantity}</span>
                    </div>
                    
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'swal2-custom-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    processExpiredBatch(batchId);
                }
            });
        };

        function processExpiredBatch(batchId) {
            Swal.fire({
                title: 'Processing...',
                html: 'Removing...',
                didOpen: () => Swal.showLoading(),
                allowOutsideClick: false
            });

            $.ajax({
                url: '/ingredient_management/remove-batch',
                method: 'POST',
                data: JSON.stringify({ batch_id: batchId }),
                contentType: 'application/json',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (data) {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Removed',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            loadExpiringSoonItems();
                            loadExpiredHistory();

                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                },
                error: function (xhr) {
                    console.error('Error:', xhr);
                    Swal.fire('Error', 'Failed to remove batch', 'error');
                }
            });
        }

        function submitAddStock(form) {
            const formData = {
                ingredient_id: $(form).find('[name="ingredient_id"]').val(),
                quantity: $(form).find('[name="quantity"]').val(),
                expiration_date: $(form).find('[name="expiration_date"]').val()
            };

            $.ajax({
                url: '/ingredient_management/add-stock',
                method: 'POST',
                data: JSON.stringify(formData),
                contentType: 'application/json',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (data) {
                    if (data.success) {
                        Swal.fire('Success', data.message, 'success');
                        $('#addStockModal').modal('hide');
                        $(form)[0].reset();
                        location.reload();
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Failed to add stock', 'error');
                }
            });
        }

        function submitUpdateStock(form) {
            const formData = {
                ingredient_id: $(form).find('[name="ingredient_id"]').val(),
                new_quantity: $(form).find('[name="new_quantity"]').val()
            };

            $.ajax({
                url: '/ingredient_management/update-stock',
                method: 'POST',
                data: JSON.stringify(formData),
                contentType: 'application/json',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (data) {
                    if (data.success) {
                        Swal.fire('Success', data.message, 'success');
                        $('#updateStockModal').modal('hide');
                        $(form)[0].reset();
                        location.reload();
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Failed to update stock', 'error');
                }
            });
        }

        function submitAddIngredient(form) {
            const formData = {
                name: $(form).find('[name="name"]').val(),
                category: $(form).find('[name="category"]').val(),
                unit: $(form).find('[name="unit"]').val()
            };

            $.ajax({
                url: '/ingredient_management/storeIngredient',
                method: 'POST',
                data: JSON.stringify(formData),
                contentType: 'application/json',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (data, textStatus, xhr) {
                    if (xhr.status === 200 || data.success) {
                        Swal.fire('Success', data.message || 'Ingredient added successfully', 'success');
                        $('#addIngredientModal').modal('hide');
                        $(form)[0].reset();
                        location.reload();
                    } else {
                        Swal.fire('Error', data.message || 'Failed to add ingredient', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Failed to add ingredient', 'error');
                }
            });
        }

        // Stock Batches UI
        function showLoading(prefix) {
            $(`#${prefix}Loading`).removeClass('d-none');
            $(`#${prefix}Empty`).addClass('d-none');
            $(`#${prefix}Content`).addClass('d-none');
        }

        function hideLoading(prefix) {
            $(`#${prefix}Loading`).addClass('d-none');
        }

        function showEmpty(prefix) {
            $(`#${prefix}Empty`).removeClass('d-none');
        }

        function showContent(prefix) {
            $(`#${prefix}Content`).removeClass('d-none');
        }

        function showExpiringSoonLoading() {
            $('#expiringSoonLoading').removeClass('d-none');
            $('#expiringSoonEmpty').addClass('d-none');
            $('#expiringSoonContent').addClass('d-none');
        }

        function hideExpiringSoonLoading() {
            $('#expiringSoonLoading').addClass('d-none');
        }

        function showExpiringSoonEmpty() {
            $('#expiringSoonEmpty').removeClass('d-none');
        }

        function showExpiringSoonContent() {
            $('#expiringSoonContent').removeClass('d-none');
        }

        function showExpiredLoading() {
            $('#expiredLoading').removeClass('d-none');
            $('#expiredEmpty').addClass('d-none');
            $('#expiredContent').addClass('d-none');
        }

        function hideExpiredLoading() {
            $('#expiredLoading').addClass('d-none');
        }

        function showExpiredEmpty() {
            $('#expiredEmpty').removeClass('d-none');
        }

        function showExpiredContent() {
            $('#expiredContent').removeClass('d-none');
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</div>
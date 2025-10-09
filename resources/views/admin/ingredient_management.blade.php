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
                        </ul>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#addStockModal">Add
                            New Stock</button>
                        <button class="btn btn-success btn-sm mr-2" data-toggle="modal"
                            data-target="#updateStockModal">Update Stock</button>
                        <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#addIngredientModal">Add
                            Ingredient</button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="tab-content">

                        {{-- Stocks Tab --}}
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
                                                    <span>{{ $ingredient->unit }}</span>
                                                </td>
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

                        {{-- Stock Batches Tab --}}
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
                                    <div id="thisWeekLoading" class="text-center py-4 text-muted">Loading...</div>
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
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Expired Tab --}}
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
                                                <th>Expired at</th>
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

    {{-- Add Stock Modal --}}
    <div class="modal fade" id="addStockModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Add New Stock</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
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
                            <label>Arrived Date</label>
                            <input type="date" name="arrived_at" class="form-control" required>
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

    {{-- Update Stock Modal --}}
    <div class="modal fade" id="updateStockModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Update Stock</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
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
                            <label>Ingredient Name</label>
                            <input type="text" name="name" class="form-control" required>
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
                                <option value="kg">Kilograms</option>
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

    $('#addStockModal, #updateStockModal, #addIngredientModal, #editBatchModal').modal({
        backdrop: 'static',
        keyboard: false,
        show: false
    });

    $(document).ready(function () {
        const today = new Date().toISOString().split('T')[0];
        $('input[name="expiration_date"]').attr('min', today);

        $('#addStockModal').on('show.bs.modal', () => loadIngredients('#addStockForm select[name="ingredient_id"]'));
        $('#updateStockModal').on('show.bs.modal', () => loadIngredients('#updateStockForm select[name="ingredient_id"]', true));

        $('a[href="#batch"]').on('shown.bs.tab', () => loadBatches('thisweek'));
        $('a[href="#thisweek"]').on('shown.bs.tab', () => loadBatches('thisweek'));
        $('a[href="#lastweek"]').on('shown.bs.tab', () => loadBatches('lastweek'));
        $('a[href="#expired"]').on('shown.bs.tab', loadExpired);

        $('#addStockForm').on('submit', function (e) { 
            e.preventDefault(); 
            submitForm(this, '/ingredient_management/add-stock', 'Stock added successfully'); 
        });
        $('#updateStockForm').on('submit', function (e) { 
            e.preventDefault(); 
            submitForm(this, '/ingredient_management/update-stock', 'Stock updated successfully'); 
        });
        $('#addIngredientForm').on('submit', function (e) { 
            e.preventDefault(); 
            submitForm(this, '/ingredient_management/storeIngredient', 'Ingredient added successfully'); 
        });
        $('#editBatchForm').on('submit', function (e) { 
            e.preventDefault(); 
            updateBatch(); 
        });

        function loadIngredients(selector, showStock = false) {
            $.get('/ingredient_management/addStockForm', data => {
                const $sel = $(selector).find('option:not(:first)').remove().end();
                data.ingredients.forEach(i => $sel.append(`<option value="${i.id}">${i.name}${showStock ? ` (${i.stocks} ${i.unit})` : ''}</option>`));
            });
        }

        function loadBatches(period) {
            const pre = period === 'thisweek' ? 'thisWeek' : 'lastWeek';
            $(`#${pre}Loading`).removeClass('d-none');
            $(`#${pre}Empty, #${pre}Content`).addClass('d-none');

            $.get(`/ingredient_management/stock-batches?period=${period}`, data => {
                $(`#${pre}Loading`).addClass('d-none');
                if (data.batches.length) {
                    $(`#${pre}Content`).removeClass('d-none');
                    const $tb = $(`#${pre}TableBody`).empty();
                    data.batches.forEach(b => $tb.append(`
                        <tr>
                            <td>${b.ingredient_name}</td>
                            <td>${parseFloat(b.quantity).toFixed(2)} ${b.unit}</td>
                            <td>${new Date(b.arrived_at).toLocaleDateString()}</td>
                            <td>${new Date(b.expiration_date).toLocaleDateString()}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning btn-edit-batch" data-id="${b.id}" data-qty="${b.quantity}" data-arrived="${b.arrived_at}" data-exp="${b.expiration_date}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-del-batch" data-id="${b.id}" data-name="${b.ingredient_name}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `));
                } else $(`#${pre}Empty`).removeClass('d-none');
            });
        }

        function loadExpired() {
            $('#expiredLoading').removeClass('d-none');
            $('#expiredEmpty, #expiredContent').addClass('d-none');

            $.get('/ingredient_management/expired-only', data => {
                $('#expiredLoading').addClass('d-none');
                if (data.expired_items.length) {
                    $('#expiredContent').removeClass('d-none');
                    const $tb = $('#expiredTableBody').empty();
                    data.expired_items.forEach(i => $tb.append(`
                        <tr>
                            <td>${i.ingredient_name}</td>
                            <td>${parseFloat(i.quantity).toFixed(2)} ${i.unit}</td>
                            <td>${new Date(i.expiration_date).toLocaleDateString()}</td>
                        </tr>
                    `));
                } else $('#expiredEmpty').removeClass('d-none');
            });
        }

        function submitForm(form, url, msg) {
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
                        
                        setTimeout(() => location.reload(), 1000);
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
                        
                        setTimeout(() => {
                            loadBatches($('.nav-link.active[data-toggle="pill"]').attr('href').includes('thisweek') ? 'thisweek' : 'lastweek');
                        }, 1000);
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
            $('#editBatchId').val($(this).data('id'));
            $('#editBatchQty').val($(this).data('qty'));
            $('#editBatchArrived').val($(this).data('arrived'));
            $('#editBatchExpiry').val($(this).data('exp'));
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
                        url: `/ingredient_management/batches/${id}`,
                        method: 'DELETE',
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
                                
                                setTimeout(() => {
                                    loadBatches($('.nav-link.active[data-toggle="pill"]').attr('href').includes('thisweek') ? 'thisweek' : 'lastweek');
                                }, 1000);
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
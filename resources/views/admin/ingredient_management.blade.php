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
                                <a class="nav-link active" id="stocks-tab" data-toggle="tab" href="#stocks" role="tab">
                                    Stocks
                                </a>
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
                            @livewire('stocks-table')
                        </div>

                        <div class="tab-pane fade" id="batch" role="tabpanel">
                            @livewire('stock-batches-management')
                        </div>

                        <div class="tab-pane fade" id="stock-order" role="tabpanel" aria-labelledby="stock-order-tab">
                            @livewire('stock-order-management')
                        </div>

                        <div class="tab-pane fade" id="expired-stock" role="tabpanel"
                            aria-labelledby="expired-stock-tab">
                            @livewire('expired-stock-table')
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
                                    $unitAbbr = $ingredient->unit?->abbreviation ?? 'unit';
                                    $reorderQty = $ingredient->stockAlertLevel?->reorder_quantity ?? 0;
                                @endphp
                                <option value="{{ $ingredient->id }}" data-name="{{ $ingredient->name }}"
                                    data-unit="{{ $unitAbbr }}" data-reorder="{{ $reorderQty }}">
                                    {{ $ingredient->name }} ({{ $unitAbbr }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Quantity (Reorder Amount) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="quantity" id="request_quantity" class="form-control" step="0.01"
                                min="0.01" readonly required>
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
                            oninput="this.value = this.value.replace(/[0-9]/g, '').replace(/\b\w/g, char => char.toUpperCase())">
                        <div>
                            <small id="unitNameError" class="text-danger text-sm" style="display: none;"></small>
                        </div>
                        <br>
                        <label>Abbreviation <span class="text-danger">*</span></label>
                        <input type="text" name="abbreviation" id="unit_abbreviation" class="form-control" required
                            minlength="1" placeholder="e.g., kg" pattern="[A-Za-z]+" style="text-transform: capitalize;"
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


    $(document).ready(function () {

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


        $('#editBatchArrived, #editBatchExpiry').on('change', function () {
            const arrivedChanged = $('#editBatchArrived').val() !== $('#originalArrived').val();
            const expiryChanged = $('#editBatchExpiry').val() !== $('#originalExpiry').val();

            $('#updateBatchBtn').prop('disabled', !(arrivedChanged || expiryChanged));
        });




        $('#addIngredientForm').on('submit', function (e) {
            e.preventDefault();
            submitForm(this, '/ingredient_management/storeIngredient', 'Ingredient added successfully', 'stocks');
        });

        $('#request_ingredient_id').on('change', function () {
            const selected = $(this).find('option:selected');
            const unit = selected.data('unit');
            const reorderQty = selected.data('reorder') || 0;

            $('#request_unit').text(unit || 'unit');

            if (unit && (unit.toLowerCase() === 'pieces' || unit.toLowerCase() === 'pcs' || unit.toLowerCase() === 'piece')) {
                $('#request_quantity').attr('step', '1');
                $('#request_quantity').attr('min', '1');
                $('#request_quantity').val(Math.round(reorderQty));
            } else {
                $('#request_quantity').attr('step', '0.01');
                $('#request_quantity').attr('min', '0.01');
                $('#request_quantity').val(reorderQty);
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


</script>
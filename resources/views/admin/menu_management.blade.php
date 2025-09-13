@include('admin.layouts.header')
@include('admin.layouts.sidebar')

<div id="content-wrapper" class="d-flex flex-column">
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
                                    <td>
                                        Regular: ₱{{ number_format($item->regular_price, 2) }} <br>
                                        @if($item->student_price)
                                            Student: ₱{{ number_format($item->student_price, 2) }} <br>
                                        @endif
                                        @if($item->govt_employee_price)
                                            Gov’t Employee: ₱{{ number_format($item->govt_employee_price, 2) }}
                                        @endif
                                    </td>

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

            <!-- Replace your addMenuModal with this simple version -->
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
                        <form action="{{ route('storeMenu') }}" method="POST">
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
                                    <label for="regular_price">Regular Price</label>
                                    <input type="number" name="regular_price" id="regular_price" class="form-control"
                                        step="0.01" min="0" required value="{{ old('regular_price') }}">
                                    @error('regular_price')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="student_price">Student Price (Optional)</label>
                                    <input type="number" name="student_price" id="student_price" class="form-control"
                                        step="0.01" min="0" value="{{ old('student_price') }}">
                                    @error('student_price')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="govt_employee_price">Gov't Employee Price (Optional)</label>
                                    <input type="number" name="govt_employee_price" id="govt_employee_price"
                                        class="form-control" step="0.01" min="0"
                                        value="{{ old('govt_employee_price') }}">
                                    @error('govt_employee_price')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
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

            {{-- Reopen modal if validation fails --}}


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
    $(document).ready(function () {

        window.showDeleteModal = function (id, itemName) {
            try {
                const deleteItemNameElement = document.getElementById('deleteItemName');
                const deleteFormElement = document.getElementById('deleteForm');

                if (deleteItemNameElement && deleteFormElement) {
                    deleteItemNameElement.textContent = itemName;
                    deleteFormElement.action = "{{ route('admin.deleteMenu', ':id') }}".replace(':id', id);
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

        @if ($errors->any())
            $('#addMenuModal').modal('show');
        @endif

        $('#addMenuModal').on('hidden.bs.modal', function () {
            try {
                $(this).find('form')[0].reset();
            } catch (error) {
                console.error('Error resetting form:', error);
            }
        });

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: {!! json_encode(session('success')) !!},
                toast: true,
                position: 'top',
                timer: 3000,
                showConfirmButton: false,
                background: '#d4edda',
                color: '#155724'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: {!! json_encode(session('error')) !!},
                toast: true,
                position: 'top',
                timer: 4000,
                showConfirmButton: false,
                background: '#f8d7da',
                color: '#721c24'
            });
        @endif

        @if($errors->has('menu_item'))
            Swal.fire({
                icon: 'error',
                title: 'Duplicate Menu Item',
                text: {!! json_encode($errors->first('menu_item')) !!},
                toast: true,
                position: 'top',
                timer: 5000,
                showConfirmButton: false,
                background: '#f8d7da',
                color: '#721c24'
            });
        @endif

            @if($errors->any() && !$errors->has('menu_item'))
                let errorMessages = [];
                @foreach($errors->all() as $error)
                    errorMessages.push({!! json_encode($error) !!});
                @endforeach

                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Errors',
                    html: errorMessages.join('<br>'),
                    toast: true,
                    position: 'top',
                    timer: 5000,
                    showConfirmButton: false,
                    background: '#fff3cd',
                    color: '#856404'
                });
            @endif

        $('form[action="{{ route('storeMenu') }}"]').on('submit', function (e) {
            const submitButton = $(this).find('button[type="submit"]');
            submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');

            setTimeout(function () {
                submitButton.prop('disabled', false).html('<i class="fas fa-save"></i> Add Menu Item');
            }, 3000);
        });

        $('form[action*="updatemenu"]').on('submit', function (e) {
            const submitButton = $(this).find('button[type="submit"]');
            submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');

            setTimeout(function () {
                submitButton.prop('disabled', false).html('<i class="fas fa-save"></i> Update');
            }, 3000);
        });

        $('#addMenuModal').on('shown.bs.modal', function () {
            $('#menu_item').focus();
        });

        $('input[type="number"][step="0.01"]').on('blur', function () {
            if (this.value && !isNaN(this.value)) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });

        $('input[type="number"][min="0"]').on('input', function () {
            if (this.value < 0) {
                this.value = 0;
            }
        });

    });
</script>
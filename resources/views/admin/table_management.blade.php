@include('admin.layouts.header')
@include('admin.layouts.sidebar')

<div id="content-wrapper" class="d-flex flex-column h-screen overflow-y-auto">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h1 class="h3 mb-0 text-gray-800">Table Management</h1>
        </nav>
        <div class="container-fluid">

            <div class="mb-3">
                <a href="{{ route('admin.table_management') }}"
                    class="btn btn-sm {{ !request()->has('show_deleted') ? 'btn-primary' : 'btn-outline-primary' }}">
                    Active Tables
                </a>
                <a href="{{ route('admin.table_management', ['show_deleted' => true]) }}"
                    class="btn btn-sm {{ request()->has('show_deleted') ? 'btn-secondary' : 'btn-outline-secondary' }}">
                    Deleted Tables
                </a>
            </div>

            <div class="card mt-2" style="max-width: 100%;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tables {{ request()->has('show_deleted') ? '(Deleted)' : '' }}</h5>
                    @if(!request()->has('show_deleted'))
                        <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#addTableModal">Add
                            Table</button>
                    @endif
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-sm text-start">
                        <thead class="thead-light">
                            <tr>
                                <th>Table Number</th>
                                <th>Capacity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tables as $table)
                                <tr>
                                    <td>{{ $table->table_number }}</td>
                                    <td>{{ $table->capacity }}</td>
                                    <td>
                                        @if($table->deleted_at)
                                            <button type="button" class="btn btn-sm btn-success"
                                                onclick="showRestoreModal({{ $table->id }}, '{{ addslashes($table->table_number) }}')"
                                                title="Restore">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="showForceDeleteModal({{ $table->id }}, '{{ addslashes($table->table_number) }}')"
                                                title="Delete Permanently">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <a href="#" title="Edit" data-toggle="modal"
                                                data-target="#editTableModal{{ $table->id }}"
                                                style="all: unset; cursor: pointer;">
                                                <i class="fas fa-edit text-primary"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-link p-0 ml-2"
                                                onclick="showDeleteModal({{ $table->id }}, '{{ addslashes($table->table_number) }}')"
                                                title="Delete" style="all: unset; cursor: pointer;">
                                                <i class="fas fa-trash text-danger"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">
                                        {{ request()->has('show_deleted') ? 'No deleted tables found' : 'No tables found' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal fade" id="addTableModal" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog" role="document">
                    <form action="{{ route('storeTable') }}" method="POST">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">Add New Table</h5>
                                <button type="button" class="close text-white"
                                    data-dismiss="modal"><span>&times;</span></button>
                            </div>

                            @php
                                $lastTableNumber = $tables->max('table_number') ?? 0;
                                $add_new_table = $lastTableNumber + 1;
                            @endphp

                            <div class="modal-body">
                                <label>Table Number</label>
                                <input type="number" name="table_number" class="form-control"
                                    value="{{ $add_new_table }}" required readonly>

                                <label class="mt-2">Capacity</label>
                                <input type="number" name="capacity" class="form-control" required>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Add</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            @foreach ($tables as $item)
                @if(!$item->deleted_at)
                    <div class="modal fade" id="editTableModal{{ $item->id }}" tabindex="-1" data-backdrop="static" data-keyboard="false"">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('admin.updatetable', $item->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">Edit Table</h5>
                                        <button type="button" class="close text-white"
                                            data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <label>Table Number</label>
                                        <input type="text" name="table_number" value="{{ $item->table_number }}"
                                            class="form-control" required>

                                        <label class="mt-2">Capacity</label>
                                        <input type="number" name="capacity" value="{{ $item->capacity }}" class="form-control"
                                            required>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            @endforeach

            <div class="modal fade" id="deleteConfirmModal" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h5>
                            <button type="button" class="close text-white"
                                data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete table <strong><span id="deleteItemName"></span></strong>
                                from
                                the list?</p>
                        </div>
                        <div class="modal-footer">
                            <form id="deleteForm" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
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
                            <h5 class="modal-title"><i class="fas fa-undo"></i> Restore Table</h5>
                            <button type="button" class="close text-white"
                                data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to restore table <strong><span
                                        id="restoreItemName"></span></strong>?</p>
                        </div>
                        <div class="modal-footer">
                            <form id="restoreForm" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success">Restore</button>
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
                            <h5 class="modal-title"><i class="fas fa-exclamation-circle"></i> CRITICAL WARNING
                            </h5>
                            <button type="button" class="close text-white"
                                data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body bg-light">
                            <div class="alert alert-danger border-danger">
                                <p class="text-center">Are you sure you want to <strong class="text-danger">permanently
                                        delete table </strong>
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
                    deleteFormElement.action = "{{ url('deletetable') }}/" + id;
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
                    restoreFormElement.action = "{{ url('restoretable') }}/" + id;
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
                    forceDeleteFormElement.action = "{{ url('forcedeletetable') }}/" + id;
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
            $('#addTableModal').modal('show');
        @endif

        $('#addTableModal').on('hidden.bs.modal', function () {
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

        @if($errors->has('table_number'))
            Swal.fire({
                icon: 'error',
                title: 'Duplicate Table Number',
                text: {!! json_encode($errors->first('table_number')) !!},
                toast: true,
                position: 'top',
                timer: 5000,
                showConfirmButton: false,
                background: '#f8d7da',
                color: '#721c24'
            });
        @endif

            @if($errors->any() && !$errors->has('table_number'))
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

        $('form[action="{{ route('storeTable') }}"]').on('submit', function (e) {
            const submitButton = $(this).find('button[type="submit"]');
            submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');

            setTimeout(function () {
                submitButton.prop('disabled', false).html('<i class="fas fa-save"></i> Add');
            }, 3000);
        });

        $('form[action*="updatetable"]').on('submit', function (e) {
            const submitButton = $(this).find('button[type="submit"]');
            submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');

            setTimeout(function () {
                submitButton.prop('disabled', false).html('<i class="fas fa-save"></i> Update');
            }, 3000);
        });

        $('#addTableModal').on('shown.bs.modal', function () {
            $('input[name="table_number"]').focus();
        });

        $('input[type="number"]').on('input', function () {
            if (this.name === 'table_number' || this.name === 'capacity') {
                this.value = this.value.replace(/\D/g, '');
            }

            if (this.value && parseInt(this.value) < 1) {
                this.value = 1;
            }
        });

        $('input[name="table_number"], input[name="capacity"]').on('blur', function () {
            if (this.value && !isNaN(this.value)) {
                this.value = Math.max(1, parseInt(this.value) || 1);
            }
        });

        $('input[name="capacity"]').on('blur', function () {
            if (this.value && parseInt(this.value) > 20) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Large Capacity',
                    text: 'Are you sure this table can seat ' + this.value + ' people? That seems quite large.',
                    toast: true,
                    position: 'top',
                    timer: 4000,
                    showConfirmButton: false,
                    background: '#fff3cd',
                    color: '#856404'
                });
            }
        });

        $('input[name="table_number"]').on('blur', function () {
            if (this.value && parseInt(this.value) > 999) {
                Swal.fire({
                    icon: 'warning',
                    title: 'High Table Number',
                    text: 'Table number ' + this.value + ' seems quite high. Are you sure this is correct?',
                    toast: true,
                    position: 'top',
                    timer: 4000,
                    showConfirmButton: false,
                    background: '#fff3cd',
                    color: '#856404'
                });
            }
        });

    });
</script>
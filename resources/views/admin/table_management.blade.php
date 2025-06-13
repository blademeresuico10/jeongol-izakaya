@include('admin.layouts.header')
@include('admin.layouts.sidebar')

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h1 class="h3 mb-0 text-gray-800">Table Management</h1>
        </nav>

        <!-- Begin Page Content -->
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card mt-2" style="max-width: 100%;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Table Management</h5>
                    <!-- Button to trigger Add Modal -->
                    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#addTableModal">Add Table</button>
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
                            @foreach ($tables as $table)
                                <tr>
                                    <td>{{ $table->table_number }}</td>
                                    <td>{{ $table->capacity }}</td>
                                    <td>
                                        <a href="#" title="Modify" data-toggle="modal" data-target="#editTableModal{{ $table->id }}" style="all: unset; cursor: pointer;">
                                            <i class="fas fa-edit text-primary"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add Table Modal -->
            <div class="modal fade" id="addTableModal" tabindex="-1" role="dialog" aria-labelledby="addTableModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form action="{{ route('storeTable') }}" method="POST">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addTableModalLabel">Add New Table</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <label>Table Number</label>
                                <input type="number" name="table_number" class="form-control" required>

                                <label class="mt-2">Capacity</label>
                                <input type="number" name="capacity" class="form-control" required>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Add</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Table Modals -->
            @foreach ($tables as $item)
                <div class="modal fade" id="editTableModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="editTableModalLabel{{ $item->id }}" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <form action="{{ route('admin.updatetable', $item->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editTableModalLabel{{ $item->id }}">Edit Table</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <label>Table Number</label>
                                    <input type="text" name="table_number" value="{{ $item->table_number }}" class="form-control" required>

                                    <label class="mt-2">Capacity</label>
                                    <input type="number" name="capacity" value="{{ $item->capacity }}" class="form-control" required>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach

        </div>
        <!-- /.container-fluid -->

    </div>
</div>

@include('admin.layouts.script')

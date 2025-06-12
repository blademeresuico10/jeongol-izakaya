@include('admin.layouts.header')
@include('admin.layouts.sidebar')

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h1 class="h3 mb-0 text-gray-800">Menu Management</h1>
        </nav>

        <!-- Begin Page Content -->
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card mt-2" style="max-width: 50%;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Menu</h5>
                    <!-- Button to trigger Add Modal -->
                    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#addMenuModal">Add Menu</button>
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
                            @foreach ($menu as $item)
                                <tr>
                                    <td>{{ $item->menu_item }}</td>
                                    <td>{{ number_format($item->price, 2) }}</td>
                                    <td>
                                        <a href="#" title="Modify" data-toggle="modal" data-target="#editMenuModal{{ $item->id }}" style="all: unset; cursor: pointer;">
                                            <i class="fas fa-edit text-primary"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add Menu Modal -->
            <div class="modal fade" id="addMenuModal" tabindex="-1" role="dialog" aria-labelledby="addMenuModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form action="" method="POST">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addMenuModalLabel">Add New Menu Item</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <label>Menu Item</label>
                                <input type="text" name="menu_item" class="form-control" required>

                                <label class="mt-2">Price</label>
                                <input type="number" name="price" class="form-control" step="0.01" min="0" required>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Add</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Menu Modals -->
            @foreach ($menu as $item)
                <div class="modal fade" id="editMenuModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="editMenuModalLabel{{ $item->id }}" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <form action="{{ route('admin.updatemenu', $item->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editMenuModalLabel{{ $item->id }}">Edit Menu Item</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <label>Menu Item</label>
                                    <input type="text" name="menu_item" value="{{ $item->menu_item }}" class="form-control" required>

                                    <label class="mt-2">Price</label>
                                    <input type="number" name="price" value="{{ $item->price }}" class="form-control" step="0.01" min="0" required>
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

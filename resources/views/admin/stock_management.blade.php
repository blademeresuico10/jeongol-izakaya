@include('admin.layouts.header')
@include('admin.layouts.sidebar')

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h1 class="h3 mb-0 text-gray-800">Stock Management</h1>
        </nav>

        <!-- Begin Page Content -->
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card mt-2" style="max-width: 100%;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Stock Items</h5>
                    <!-- Button to trigger Add Modal -->
                    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#addStockModal">Add Stock</button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-sm text-start">
                        <thead class="thead-light">
                            <tr>
                                <th>Stock Name</th>
                                <th>Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stocks as $stock)
                                <tr>
                                    <td>{{ $stock->stock_name }}</td>
                                    <td>{{ $stock->stock_quantity }}</td>
                                    <td>
                                        <a href="#" title="Modify" data-toggle="modal" data-target="#editStockModal{{ $stock->id }}" style="all: unset; cursor: pointer;">
                                            <i class="fas fa-edit text-primary"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add Modal -->
            <div class="modal fade" id="addStockModal" tabindex="-1" role="dialog" aria-labelledby="addStockModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form action="{{ route('admin.storeStock') }}" method="POST">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addStockModalLabel">Add New Stock Item</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <label>Stock Name</label>
                                <input type="text" name="stock_name" class="form-control" required>

                                <label class="mt-2">Quantity</label>
                                <input type="number" name="stock_quantity" class="form-control" step="0.01" min="0" required>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Add</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Modals -->
            @foreach ($stocks as $stock)
                <div class="modal fade" id="editStockModal{{ $stock->id }}" tabindex="-1" role="dialog" aria-labelledby="editStockModalLabel{{ $stock->id }}" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <form action="{{ route('admin.updateStock', $stock->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editStockModalLabel{{ $stock->id }}">Edit Stock</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <label>Stock Name</label>
                                    <input type="text" name="stock_name" value="{{ $stock->stock_name }}" class="form-control" required>

                                    <label class="mt-2">Quantity</label>
                                    <input type="number" name="stock_quantity" value="{{ $stock->stock_quantity }}" class="form-control" step="0.01" min="0" required>
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

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
                    <h5 class="mb-0">Stock Management</h5>
                    <!-- Button to trigger Add Modal -->
                    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#addTableModal">Add Stock</button>
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
                                        <a href="#" title="Modify" data-toggle="modal" data-target="#editTableModal{{ $stock->id }}" style="all: unset; cursor: pointer;">
                                            <i class="fas fa-edit text-primary"></i>
                                        </a>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <!-- /.container-fluid -->

    </div>
</div>

@include('admin.layouts.script')

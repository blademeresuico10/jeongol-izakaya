@include('admin.layouts.header')

<!-- Sidebar -->
@include('admin.layouts.sidebar')
<!-- End of Sidebar -->

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h1 class="h3 mb-0 text-gray-800">Add Menu</h1>
            </div>
        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">
            <div class="row justify-content-center mt-4">
                <div class="col-lg-4">
                    <div class="card shadow rounded" style="padding: 0.5rem 0;">
                        <div class="card-body py-1 px-4 ">
                            <h5 class="mb-4 text-center fw-bold" style="font-size: 1.7rem; color: black">Add Menu Item</h5>

                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('storeMenu') }}" method="POST">
                                @csrf

                                <div class="mb-2">
                                    <label for="menu_item">Menu Item</label>
                                    <input type="text" id="menu_item" name="menu_item" class="form-control form-control-sm" required>
                                </div>

                                <div class="mb-2">
                                    <label for="price">Price</label>
                                    <input type="number" id="price" name="price" class="form-control form-control-sm" step="0.01" min="0" required>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-sm btn-primary">Add</button>
                                    <a href="{{ route('menu_management') }}" class="btn btn-sm btn-secondary">Back</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

</div>
<!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

@include('admin.layouts.script')

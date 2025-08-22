@include('admin.layouts.header')

<!-- Sidebar -->
@include('admin.layouts.sidebar')
<!-- End of Sidebar -->

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <!-- Main Content -->
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>
            <div class="d-sm-flex align-items-center justify-content-between">
                <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>

            </div>
        </nav>
        <div class="container-fluid">
        </div>
    </div>
</div>
</div>
</body>

</html>


@include('admin.layouts.script')
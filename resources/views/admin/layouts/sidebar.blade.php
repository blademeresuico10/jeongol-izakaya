<ul class="navbar-nav bg-gradient-danger sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>

            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li @class(['nav-item', 'active' => request()->is('/home')])>
                <a class="nav-link" href="{{ route('admin.home') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li @class(['nav-item', 'active' => request()->is('users*')])>
                <a class="nav-link" href="{{ url('users') }}">
                    <i class="fas fa-fw fa-user"></i>
                    <span>Users</span>
                </a>
            </li>

            <li @class(['nav-item', 'active' => request()->is('menu*')])>
                <a class="nav-link" href="{{url('menu_management')}}">
                    <i class="fas fa-utensils"></i>
                    <span>Menu</span>
                </a>
            </li>

            <li @class(['nav-item', 'active' => request()->is('table*')])>
                <a class="nav-link" href="{{url('table_management')}}">
                    <i class="fas fa-utensils"></i>
                    <span>Table</span>
                </a>
            </li>

            <li @class(['nav-item', 'active' => request()->is('stock*')])>
                <a class="nav-link" href="stock.html">
                    <i class="fas fa-boxes"></i>
                    <span>Stock</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="sales.html">
                    <i class="fas fa-chart-line"></i>
                    <span>Sales</span>
                </a>
            </li>

            <li @class(['nav-item', 'active' => request()->is('reports*')])>
                <a class="nav-link" href="{{ url('reports') }}">
                    <i class="fas fa-file-alt"></i>
                    <span>Report and Analytics</span>
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link" href="feedbacks.html">
                    <i class="fas fa-comments"></i>
                    <span>Feedbacks</span>
                </a>
            </li>


            <li class="nav-item" data-dismiss="modal">
                <a class="nav-link">
                    <i class="fas fa-sign-out-alt text-gray-600"></i>
                    <span>Logout</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
           
            <!-- Sidebar Message -->
            
        </ul>


         <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>

    
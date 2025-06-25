<ul class="navbar-nav bg-gradient-danger sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-start px-3">
                <div style="width: 42px; height: 42px; background-color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
            <img src="{{ asset('logo/jeongol_logo.jpg') }}" alt="Profile" style="width: 35px; height: 35px;">
        </div>
                <div class="ml-2">
    <p class="mb-0" style="font-size: 10px;">
        {{ Auth::user()->firstname }} {{ Auth::user()->lastname }}
    </p>
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
                <a class="nav-link" href="{{url('stock_management')}}">
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


            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link d-flex align-items-center" style="background: none; border: none; width: 100%; text-align: left; color: #fff;">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="ml-2">Logout</span>
                    </button>
                </form>
            </li>



            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
           
            <!-- Sidebar Message -->
            
        </ul>


    

    
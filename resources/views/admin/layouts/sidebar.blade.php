<ul class="navbar-nav bg-gradient-danger sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-start px-3">
        <div
            style="width: 42px; height: 42px; background-color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
            <img src="{{ asset('logo/jeongol_logo.jpg') }}" alt="Profile" style="width: 35px; height: 35px;">
        </div>
        <div class="ml-2">
            <p class="mb-0" style="font-size: 14px;">
                Jeongol Izakaya
            </p>
        </div>
    </a>
    <hr class="sidebar-divider my-0">

    <li @class(['nav-item', 'active' => request()->is(patterns: 'dashboard*')])>
        <a class="nav-link" href="{{ route('admin.home') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li @class(['nav-item', 'active' => request()->is('users*')])>
        <a class="nav-link" href="{{ route('admin.users') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>Users</span>
        </a>
    </li>

    <li @class(['nav-item', 'active' => request()->is('menu*')])>
        <a class="nav-link" href="{{route('admin.menu_management')}}">
            <i class="fas fa-utensils"></i>
            <span>Menu</span>
        </a>
    </li>

    <li @class(['nav-item', 'active' => request()->is('table*')])>
        <a class="nav-link" href="{{route('admin.table_management')}}">
            <i class="fas fa-utensils"></i>
            <span>Table</span>
        </a>
    </li>

    <li @class(['nav-item', 'active' => request()->is('ingredient_management*')])>
        <a class="nav-link" href="{{ route('admin.ingredient_management') }}">
            <i class="fas fa-boxes"></i>
            <span>Stock</span>
        </a>
    </li>

    <li @class(['nav-item', 'active' => request()->is('analytics*')])>
        <a class="nav-link" href="{{ route('admin.analytics') }}">
            <i class="fas fa-chart-bar"></i>
            <span>Analytics</span>
        </a>
    </li>


    <li @class(['nav-item', 'active' => request()->is('reports*')])>
        <a class="nav-link" href="{{ route('admin.reports') }}">
            <i class="fas fa-file-alt"></i>
            <span>Reports</span>
        </a>
    </li>

    <li @class(['nav-item', 'active' => request()->is('ewallet*')])>
        <a class="nav-link" href="{{ route('admin.ewallet_management') }}">
            <i class="fas fa-wallet"></i>
            <span>E-Wallet</span>
        </a>
    </li>

    <li @class(['nav-item', 'active' => request()->is('others*')])>
        <a class="nav-link" href="{{ route('admin.others') }}">
            <i class="fas fa-layer-group"></i>
            <span>Others</span>
        </a>
    </li>

    <li @class(['nav-item', 'active' => request()->is('feedback*')])>
        <a class="nav-link" href="{{ route('admin.feedback') }}">
            <i class="fas fa-comments"></i>
            <span>Feedback</span>
        </a>
    </li>

    <li class="nav-item">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link d-flex align-items-center"
                style="background: none; border: none; width: 100%; text-align: left; color: #fff;">
                <i class="fas fa-sign-out-alt"></i>
                <span class="ml-2">Logout</span>
            </button>
        </form>
    </li>
    <hr class="sidebar-divider d-none d-md-block">

    <li @class(['nav-item', 'active' => request()->is('myprofile*')]) style="position: relative;">
        <a class="nav-link" href="{{ route('admin.profile') }}">
            <i class="fas fa-fw fa-user-alt"></i>
            <span>My Profile</span>
        </a>
    </li>


</ul>
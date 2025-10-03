@include('admin.layouts.header')
@include('admin.layouts.sidebar')
@vite(['resources/js/app.js'])



<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        </nav>

        <div class="container-fluid px-4  ">
            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="">
                                    <i class="fas fa-dollar-sign text-primary fs-4"></i>
                                </div>
                            </div>
                            <div>
                                <p class="text-muted text-uppercase fw-semibold small mb-1">Total Revenue</p>
                                <h3 class="mb-0 fw-bold" id="revenueValue">₱0.00</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="">
                                    <i class="fas fa-shopping-bag text-success fs-4"></i>
                                </div>
                            </div>
                            <div>
                                <p class="text-muted text-uppercase fw-semibold small mb-1">Total Orders</p>
                                <h3 class="mb-0 fw-bold" id="ordersValue">0</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <i class="fas fa-users text-info fs-4"></i>
                            </div>
                            <div>
                                <p class="text-muted text-uppercase fw-semibold small mb-1">Total Customers</p>
                                <h3 class="mb-0 fw-bold" id="customersValue">0</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <i class="fas fa-calendar-check text-warning fs-4"></i>
                            </div>
                            <div>
                                <p class="text-muted text-uppercase fw-semibold small mb-1">Reservations</p>
                                <h3 class="mb-0 fw-bold" id="reservationsValue">0</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-xl-8">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div 
                                        style="width: 45px; height: 45px;">
                                        <i class="fas fa-chart-line text-primary fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-0 fw-bold">Sales Overview</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueChart" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Popular Orders -->
                <div class="col-xl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 py-3">
                            <div class="d-flex align-items-center justify-content-between mb-0">
                                <div class="d-flex align-items-center">
                                    <div 
                                        style="width: 45px; height: 45px;">
                                        <i class="fas fa-utensils text-success fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-0 fw-bold">Menu Analytics</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="ordersChart" height="280"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-xl-8">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div
                                        style="width: 45px; height: 45px;">
                                        <i class="fas fa-boxes text-warning fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-0 fw-bold">Ingredients</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="reservationsChart" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 py-3">
                            <div class="d-flex align-items-center">
                                <div 
                                    style="width: 45px; height: 45px;">
                                    <i class="fas fa-clock text-info fs-5"></i>
                                </div>
                                <h5 class="card-title mb-0 fw-bold">Recent Activity</h5>
                            </div>
                        </div>
                        <div class="card-body p-0">

                        </div>
                        <div class="card-footer bg-white border-0 py-3 text-center">
                            <a href="#" class="text-decoration-none small fw-semibold">View All Activity <i
                                    class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-lift {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .avatar-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
    }

    .avatar-sm {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
    }

    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .nav-pills .nav-link {
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        padding: 0.5rem 1rem;
    }

    .nav-pills .nav-link.active {
        background-color: #0d6efd;
    }

    .list-group-item:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
</style>

@include('admin.layouts.script')
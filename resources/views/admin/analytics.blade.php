@vite('resources/css/app.css')
@include('admin.layouts.header')
@include('admin.layouts.sidebar')

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h1 class="h3 mb-0 text-gray-800">Analytics Dashboard</h1>
        </nav>

        <div class="container-fluid px-4">
            <div class="row mb-4">
                <div class="col-xl-12">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 py-3 sticky-top" style="z-index: 1;">
                            <div class="d-flex align-items-center">
                                <h5 class="card-title mb-0 fw-bold">Most Favorite Items</h5>
                            </div>
                        </div>
                        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                            <canvas id="favoriteItemsChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-xl-12">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 py-3 sticky-top" style="z-index: 1;">
                            <div class="d-flex align-items-center">
                                <h5 class="card-title mb-0 fw-bold">Sales Summary</h5>
                            </div>
                        </div>
                        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                            <canvas id="salesSummaryChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</div>
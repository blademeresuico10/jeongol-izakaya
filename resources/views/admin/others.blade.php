@include('admin.layouts.header')
@include('admin.layouts.sidebar')


<style>
    html,
    body {
        height: 100%;
        overflow: hidden;
    }

    #wrapper {
        height: 100vh;
    }

    #content-wrapper {
        height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Fix the topbar */
    .topbar {
        position: sticky;
        top: 0;
        z-index: 999;
    }

    /* Only the container-fluid scrolls */
    .container-fluid {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding-bottom: 2rem;
    }

    .container-fluid::-webkit-scrollbar {
        width: 8px;
    }

    .container-fluid::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .container-fluid::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 2px;
    }

    .calendar-header {
        text-align: center;
        font-weight: 600;
        color: #5a5c69;
        padding: 3px;
        font-size: 0.7rem;
    }

    .calendar-day {
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e3e6f0;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .calendar-day:hover:not(.empty):not(.past) {
        background: #4e73df;
        color: white;
    }

    .calendar-day.today {
        background: #4e73df;
        color: white;
        font-weight: 700;
    }

    .calendar-day.past {
        color: #d1d3e2;
        cursor: not-allowed;
        background: #f8f9fc;
    }

    .calendar-day.empty {
        border: none;
        cursor: default;
    }

    .calendar-day.has-override {
        background: #1cc88a;
        color: white;
    }

    .calendar-day.closed-override {
        background: #e74a3b;
        color: white;
    }

    .month-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }

    .month-nav button {
        border: none;
        background: #f8f9fc;
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 0.75rem;
    }

    .month-nav button:hover {
        background: #4e73df;
        color: white;
    }

    .btn-xs {
        padding: 2px 6px;
        font-size: 0.7rem;
    }
</style>

<div id="content-wrapper" class="d-flex flex-column ">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h1 class="h3 mb-0 text-gray-800">
                Miscellaneous
            </h1>
        </nav>

        <div class="container-fluid">
            <!-- OPERATING HOURS MANAGEMENT -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="mb-0 font-weight-bold">Operating Hours Management</h6>
                </div>
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-md-3">
                            @php
                                $defaultHours = $allHours->where('is_default', true)->first();
                            @endphp
                            <div class="border rounded p-2 bg-light mb-2 position-relative">
                                <small class="font-weight-bold d-block mb-1">Today's Operation Time</small>
                                @if($todayHours)
                                    @if($todayHours->is_closed)
                                        <span class="badge badge-danger">Closed Today</span>
                                    @else
                                        <div class="text-center">
                                            <strong>{{ date('g:i A', strtotime($todayHours->open_time)) }}</strong>
                                            <span class="mx-1">-</span>
                                            <strong>{{ date('g:i A', strtotime($todayHours->close_time)) }}</strong>
                                        </div>
                                    @endif
                                @else
                                    <span class="text-muted small">Not set</span>
                                @endif

                                <button class="btn btn-xs btn-primary position-absolute edit-today-hours"
                                    style="top: 5px; right: 5px; padding: 2px 5px;"
                                    onclick="openOperatingHoursModal('{{ now()->toDateString() }}', 'today')">
                                    <i class="fas fa-edit" style="font-size: 9px;"></i>
                                </button>
                            </div>

                           <div class="card border d-flex flex-column" style="height: 250px;">
    <div class="card-header bg-light py-1 px-2">
        <small class="font-weight-bold">Custom Dates</small>
    </div>
    <div class="card-body p-0 flex-grow-1" style="overflow-y: auto; max-height: 215px;">
        <table class="table table-sm table-bordered mb-0" style="table-layout: fixed;">
            <tbody>
                @forelse($hours->filter(function($hour) {
                    return \Carbon\Carbon::parse($hour->date)->isAfter(\Carbon\Carbon::today());
                })->sortBy('date') as $hour)
                    <tr>
                        <td class="py-1 px-2" style="width: 60%;">
                            <small
                                class="font-weight-bold d-block">{{ \Carbon\Carbon::parse($hour->date)->format('M d') }}</small>
                            <small class="text-muted d-block text-truncate">
                                @if($hour->is_closed)
                                    Closed
                                @else
                                    {{ date('g:i A', strtotime($hour->open_time)) }} -
                                    {{ date('g:i A', strtotime($hour->close_time)) }}
                                @endif
                            </small>
                        </td>
                        <td class="py-1 px-1 text-center" style="width: 40%;">
                            <div class="d-flex justify-content-center">
                                <button class="btn btn-xs btn-primary p-1 mr-1 edit-override"
                                    onclick="openOperatingHoursModal('{{ \Carbon\Carbon::parse($hour->date)->format('Y-m-d') }}', 'calendar')"
                                    data-id="{{ $hour->id }}" data-date="{{ $hour->date }}"
                                    data-open="{{ $hour->open_time }}"
                                    data-close="{{ $hour->close_time }}"
                                    data-closed="{{ $hour->is_closed }}"
                                    data-is-default="{{ $hour->is_default }}">
                                    <i class="fas fa-edit" style="font-size: 10px;"></i>
                                </button>
                                <form
                                    action="{{ route('admin.operating_hours.delete', $hour->id) }}"
                                    method="POST" class="d-inline delete-override-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger p-1">
                                        <i class="fas fa-trash" style="font-size: 10px;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center py-2">
                            <small class="text-muted">No custom operating hours</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
                        </div>
                        <div class="col-md-9">
                            <div id="calendar-container" class="border rounded p-2"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- OPERATING HOURS MODAL -->
            <div class="modal fade" id="operatingHoursModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white py-2">
                            <h6 class="modal-title mb-0" id="operatingHoursModalTitle">Edit Operating Hours</h6>
                            <button type="button" class="close text-white"
                                data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <form id="operatingHoursForm" method="POST">
                            @csrf
                            <input type="hidden" name="_method" id="formMethod" value="POST">
                            <input type="hidden" id="recordId">
                            <div class="modal-body p-2">

                                <div class="form-group mb-2">
                                    <label class="small mb-1">Opening</label>
                                    <input type="time" name="open_time" id="operatingOpenTime"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="form-group mb-2">
                                    <label class="small mb-1">Closing</label>
                                    <input type="time" name="close_time" id="operatingCloseTime"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="operatingClosed"
                                        name="is_closed" value="1" onchange="toggleOperatingTimes(this)">
                                    <label class="custom-control-label small" for="operatingClosed">Mark as
                                        closed</label>
                                </div>
                            </div>
                            <div class="modal-footer p-2">
                                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                <button type="button" class="btn btn-secondary btn-sm"
                                    data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- DISCOUNT MANAGEMENT -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-success text-white py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold">Discount Management</h6>
                    <button class="btn btn-sm btn-light" data-toggle="modal" data-target="#addDiscountModal">
                        <i class="fas fa-plus"></i> Add Discount
                    </button>
                </div>
                <div class="card-body p-2">
                    <div id="discount-section">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="thead-light sticky-top">
                                    <tr>
                                        <th>Menu Item</th>
                                        <th>Discount Type</th>
                                        <th>Percentage</th>
                                        <th class="text-center" width="100">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($discounts ?? [] as $discount)
                                        <tr>
                                            <td class="align-middle">{{ $discount->menu->menu_item }}</td>
                                            <td class="align-middle">
                                                <span class="badge badge-info">{{ $discount->discount_type }}</span>
                                            </td>
                                            <td class="align-middle">{{ $discount->discount_percentage }}%</td>
                                            <td class="text-center align-middle">
                                                <button class="btn btn-xs btn-primary p-1 edit-discount"
                                                    data-id="{{ $discount->id }}"
                                                    data-percentage="{{ $discount->discount_percentage }}">
                                                    <i class="fas fa-edit" style="font-size: 10px;"></i>
                                                </button>

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-3 text-muted">
                                                No discounts configured
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($discounts->hasPages())
                            <div class="mt-3 d-flex justify-content-center">
                                {{ $discounts->onEachSide(1)->links('pagination::bootstrap-4') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- STOCK LEVEL MANAGEMENT -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-secondary text-white py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold">Stock Level Management</h6>
                    <button class="btn btn-sm btn-light" data-toggle="modal" data-target="#addStockAlertModal">
                        <i class="fas fa-plus"></i> Add Alert
                    </button>
                </div>
                <div class="card-body p-2">
                    <div id="stock-section">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="thead-light sticky-top">
                                    <tr>
                                        <th>Ingredient Name</th>
                                        <th>Current Stock</th>
                                        <th>Low Level Alert</th>
                                        <th>Critical Level Alert</th>
                                        <th>Reorder Quantity</th>
                                        <th class="text-center" width="120">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($stock_level as $stocklevel)
                                        <tr
                                            class="{{ $stocklevel->ingredient->getStockStatus() === 'critical' ? 'table-danger' : ($stocklevel->ingredient->getStockStatus() === 'low' ? 'table-warning' : '') }}">
                                            <td>
                                                {{ $stocklevel->ingredient->name }}
                                                @if($stocklevel->ingredient->getStockStatus() === 'critical')
                                                    <span class="badge badge-danger badge-sm ml-1">Critical</span>
                                                @elseif($stocklevel->ingredient->getStockStatus() === 'low')
                                                    <span class="badge badge-warning badge-sm ml-1">Low</span>
                                                @endif
                                            </td>
                                            <td><strong>{{ $stocklevel->ingredient->stocks }}
                                                    {{ $stocklevel->ingredient->unit }}</strong></td>
                                            <td>{{ $stocklevel->low_stock }} {{ $stocklevel->ingredient->unit }}</td>
                                            <td>{{ $stocklevel->critical_stock ?? '—' }}
                                                {{ $stocklevel->critical_stock ? $stocklevel->ingredient->unit : '' }}
                                            </td>
                                            <td>{{ $stocklevel->reorder_quantity }} {{ $stocklevel->ingredient->unit }}</td>
                                            <td class="text-center">
                                                <button class="btn btn-xs btn-primary p-1 edit-stock"
                                                    data-id="{{ $stocklevel->id }}"
                                                    data-ingredient="{{ $stocklevel->ingredient->name }}"
                                                    data-low="{{ $stocklevel->low_stock }}"
                                                    data-critical="{{ $stocklevel->critical_stock }}"
                                                    data-reorder="{{ $stocklevel->reorder_quantity }}">
                                                    <i class="fas fa-edit" style="font-size: 10px;"></i>
                                                </button>
                                                <button class="btn btn-xs btn-danger p-1 ml-1 delete-stock"
                                                    data-id="{{ $stocklevel->id }}"
                                                    data-ingredient="{{ $stocklevel->ingredient->name }}">
                                                    <i class="fas fa-trash" style="font-size: 10px;"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No stock alerts found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($stock_level->hasPages())
                            <div class="mt-3 d-flex justify-content-center">
                                {{ $stock_level->onEachSide(1)->links('pagination::bootstrap-4') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Add Stock Alert Modal -->
            <div class="modal fade" id="addStockAlertModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-secondary text-white">
                            <h5 class="modal-title">Add Stock Alert</h5>
                            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                        </div>
                        <form id="addStockAlertForm">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Select Ingredient</label>
                                    <select class="form-control" id="add-ingredient-id" required>
                                        <option value="">Choose ingredient...</option>
                                        @foreach($ingredients_without_alerts ?? [] as $ingredient)
                                            <option value="{{ $ingredient->id }}" data-unit="{{ $ingredient->unit }}">
                                                {{ $ingredient->name }} ({{ $ingredient->stocks }} {{ $ingredient->unit }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Low Stock Alert</label>
                                    <input type="number" class="form-control" id="add-low-stock" step="0.01" required>
                                </div>
                                <div class="form-group">
                                    <label>Critical Stock Alert</label>
                                    <input type="number" class="form-control" id="add-critical-stock" step="0.01">
                                </div>
                                <div class="form-group">
                                    <label>Reorder Quantity</label>
                                    <input type="number" class="form-control" id="add-reorder-quantity" step="0.01"
                                        required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-secondary">
                                    <i class="fas fa-plus"></i> Add
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Stock Alert Modal -->
            <div class="modal fade" id="editStockModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Edit Stock Alert - <span id="modal-ingredient-name"></span></h5>
                            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                        </div>
                        <form id="editStockForm">
                            @csrf
                            <input type="hidden" id="stock-id">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Low Stock
                                        Alert</label>
                                    <input type="number" class="form-control" id="low-stock" step="0.01" required>
                                </div>
                                <div class="form-group">
                                    <label>Critical Stock
                                        Alert</label>
                                    <input type="number" class="form-control" id="critical-stock" step="0.01">
                                </div>
                                <div class="form-group">
                                    <label>Reorder Quantity</label>
                                    <input type="number" class="form-control" id="reorder-quantity" step="0.01"
                                        required>

                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ADD DISCOUNT MODAL -->
    <div class="modal fade" id="addDiscountModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white py-2">
                    <h6 class="modal-title mb-0">Add New Discount</h6>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="{{ route('admin.discounts.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-3">
                        <div class="form-group">
                            <label class="small font-weight-bold">Menu Item</label>
                            <select name="menu_id" class="form-control form-control-sm" required>
                                <option value="">Select Menu Item</option>
                                @foreach($menus ?? [] as $menu)
                                    <option value="{{ $menu->id }}">{{ $menu->menu_item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="small font-weight-bold">Discount Type</label>
                            <select name="discount_type" class="form-control form-control-sm" required>
                                <option value="">Select Type</option>
                                <option value="Student">Student</option>
                                <option value="Government Employee">Government Employee</option>
                                <option value="Senior Citizen">Senior Citizen</option>
                                <option value="PWD">PWD</option>
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label class="small font-weight-bold">Discount Percentage (%)</label>
                            <input type="number" name="discount_percentage" class="form-control form-control-sm" min="0"
                                max="100" step="0.01" required>
                        </div>
                    </div>
                    <div class="modal-footer p-2">
                        <button type="submit" class="btn btn-success btn-sm">Add Discount</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT DISCOUNT MODAL -->
    <div class="modal fade" id="editDiscountModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-success text-white py-2">
                    <h6 class="modal-title mb-0">Edit Discount</h6>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form id="editDiscountForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-3">
                        <div class="form-group mb-0">
                            <label class="small font-weight-bold">Discount Percentage (%)</label>
                            <input type="number" name="discount_percentage" id="editDiscountPercentage"
                                class="form-control form-control-sm" min="1" max="100" step="0.01" required>
                        </div>
                    </div>
                    <div class="modal-footer p-2">
                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@include('admin.layouts.script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let currentMonth = {{ now()->month }};
    let currentYear = {{ now()->year }};
    const overrides = @json($allHours);
    const defaultHours = @json($allHours->where('is_default', true)->first());

    function renderCalendar() {
        const date = new Date(currentYear, currentMonth - 1, 1);
        const daysInMonth = new Date(currentYear, currentMonth, 0).getDate();
        const firstDay = date.getDay();
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        let html = `
    <div class="month-nav">
        <button onclick="changeMonth(-1)"><i class="fas fa-chevron-left"></i></button>
        <small class="font-weight-bold">${monthNames[currentMonth - 1]} ${currentYear}</small>
        <button onclick="changeMonth(1)"><i class="fas fa-chevron-right"></i></button>
    </div>
    <div class="calendar-grid">`;

        ['S', 'M', 'T', 'W', 'T', 'F', 'S'].forEach(d => html += `<div class="calendar-header">${d}</div>`);

        for (let i = 0; i < firstDay; i++) {
            html += '<div class="calendar-day empty"></div>';
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const cellDate = new Date(currentYear, currentMonth - 1, day);
            cellDate.setHours(0, 0, 0, 0);

            const isToday = cellDate.getTime() === today.getTime();
            const isPast = cellDate < today;

            const hasOverride = overrides[dateStr] !== undefined && overrides[dateStr] !== null;


            let classes = 'calendar-day';
            if (isToday) classes += ' today';
            if (isPast) classes += ' past';
            if (hasOverride) {
                classes += overrides[dateStr].is_closed ? ' closed-override' : ' has-override';
            }

            const isClickable = !isPast && !isToday && !hasOverride;
            const onclickAttr = isClickable ? `onclick="openOperatingHoursModal('${dateStr}', 'calendar')"` : '';
            const cursorStyle = !isClickable ? 'cursor: not-allowed; opacity: 0.6; background-color: #e0e0e0; color: #9e9e9e;' : 'cursor: pointer;';

            html += `<div class="${classes}" ${onclickAttr} style="${cursorStyle}">${day}</div>`;
        }

        html += '</div>';
        document.getElementById('calendar-container').innerHTML = html;
    }

    function changeMonth(direction) {
        currentMonth += direction;
        if (currentMonth > 12) {
            currentMonth = 1;
            currentYear++;
        } else if (currentMonth < 1) {
            currentMonth = 12;
            currentYear--;
        }
        renderCalendar();
    }

    function openOperatingHoursModal(dateStr, source = 'calendar') {
        const datePart = dateStr.split(' ')[0];

        const override = overrides[datePart];
        const isToday = datePart === '{{ now()->toDateString() }}';

        if (override && override.is_default) {
            $('#operatingHoursModalTitle').text("Edit Default Operating Hours");
        } else if (source === 'today' || isToday) {
            $('#operatingHoursModalTitle').text("Edit Today's Hours");
        } else {
            const formattedDate = new Date(datePart).toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });
            $('#operatingHoursModalTitle').text(`Edit Hours - ${formattedDate}`);
        }

        let dateInput = $('#operatingDate');
        if (dateInput.length === 0) {
            $('#operatingHoursForm').prepend('<input type="hidden" name="date" id="operatingDate">');
            dateInput = $('#operatingDate');
        }
        dateInput.val(datePart);

        if (override) {
            $('#recordId').val(override.id);

            const formatTime = (time) => {
                if (!time) return '';
                return time.substring(0, 5);
            };

            $('#operatingOpenTime').val(formatTime(override.open_time));
            $('#operatingCloseTime').val(formatTime(override.close_time));
            $('#operatingClosed').prop('checked', override.is_closed);

            $('#operatingHoursForm').attr('action', `/admin/operating-hours/${override.id}`);
            $('#formMethod').val('PUT');
        } else {
            $('#recordId').val('');
            $('#operatingOpenTime').val('11:30');
            $('#operatingCloseTime').val('20:00');
            $('#operatingClosed').prop('checked', false);

            $('#operatingHoursForm').attr('action', '{{ route("admin.operating_hours.store") }}');
            $('#formMethod').val('POST');
        }

        toggleOperatingTimes(document.getElementById('operatingClosed'));
        $('#operatingHoursModal').modal('show');
    }
    function toggleOperatingTimes(checkbox) {
        const openTime = document.getElementById('operatingOpenTime');
        const closeTime = document.getElementById('operatingCloseTime');
        if (openTime && closeTime) {
            openTime.disabled = checkbox.checked;
            closeTime.disabled = checkbox.checked;
            openTime.required = !checkbox.checked;
            closeTime.required = !checkbox.checked;
        }
    }

    function handleEditDiscount(btn) {
        document.getElementById('editDiscountPercentage').value = btn.dataset.percentage;
        document.getElementById('editDiscountForm').action = `/discounts/${btn.dataset.id}`;
        $('#editDiscountModal').modal('show');
    }

    async function handlePagination(e) {
        const link = e.target.closest('.pagination a');
        if (!link) return;

        e.preventDefault();

        const url = new URL(link.href);
        let section = 'discounts';
        let sectionId = 'discount-section';

        if (link.closest('#stock-section')) {
            section = 'stock';
            sectionId = 'stock-section';
        } else if (link.closest('#stock-order-section')) {
            section = 'stock_order';
            sectionId = 'stock-order-section';
        }

        const targetDiv = document.getElementById(sectionId);

        try {
            url.searchParams.set('section', section);

            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.getElementById(sectionId);

            if (newContent) {
                targetDiv.innerHTML = newContent.innerHTML;
            }
        } catch (error) {
            console.error('Pagination failed:', error);
        }
    }

    function setupEventDelegation() {
        document.addEventListener('click', function (e) {
            if (e.target.closest('.edit-override')) {
                const btn = e.target.closest('.edit-override');
                openOperatingHoursModal(btn.dataset.date, 'list');
            }

            if (e.target.closest('.edit-discount')) {
                const btn = e.target.closest('.edit-discount');
                handleEditDiscount(btn);
            }

            if (e.target.closest('.edit-stock')) {
                const btn = e.target.closest('.edit-stock');
                const id = btn.dataset.id;
                const ingredient = btn.dataset.ingredient;
                const low = btn.dataset.low;
                const critical = btn.dataset.critical;
                const reorder = btn.dataset.reorder;

                $('#stock-id').val(id);
                $('#modal-ingredient-name').text(ingredient);
                $('#low-stock').val(low);
                $('#critical-stock').val(critical);
                $('#reorder-quantity').val(reorder);

                $('#editStockModal').modal('show');
            }
        });

        document.addEventListener('click', handlePagination);
    }

    function showSuccessMessage() {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session("success") }}',
                timer: 1000,
                showConfirmButton: false
            });
        @endif
    }

    document.addEventListener('DOMContentLoaded', function () {
        renderCalendar();
        setupEventDelegation();
        showSuccessMessage();

        const deleteForms = document.querySelectorAll('.delete-override-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Remove Custom Hours?',
                    text: 'Are you sure you want to remove this custom operating hour?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(this.action, {
                            method: 'POST',
                            body: new FormData(this),
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Removed!',
                                        text: 'Custom operating hour removed successfully',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: 'Failed to remove custom operating hour'
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'An error occurred while removing the custom operating hour'
                                });
                            });
                    }
                });
            });
        });
    });

    $(document).ready(function () {
        $('#editStockForm').on('submit', function (e) {
            e.preventDefault();
            const id = $('#stock-id').val();

            $.ajax({
                url: `/stock-alerts/${id}`,
                method: 'PUT',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    low_stock: $('#low-stock').val(),
                    critical_stock: $('#critical-stock').val(),
                    reorder_quantity: $('#reorder-quantity').val()
                },
                success: function (response) {
                    $('#editStockModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Stock alert settings updated successfully',
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to update stock alert'
                    });
                }
            });
        });

        // Add Stock Alert
        $('#addStockAlertForm').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: '/stock-alerts',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    ingredient_id: $('#add-ingredient-id').val(),
                    low_stock: $('#add-low-stock').val(),
                    critical_stock: $('#add-critical-stock').val(),
                    reorder_quantity: $('#add-reorder-quantity').val()
                },
                success: function (response) {
                    $('#addStockAlertModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Stock alert created successfully',
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function (xhr) {
                    let errorMsg = 'Failed to create stock alert';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMsg
                    });
                }
            });
        });

        // Delete Stock Alert
        $(document).on('click', '.delete-stock', function () {
            const id = $(this).data('id');
            const ingredient = $(this).data('ingredient');

            Swal.fire({
                title: 'Delete Stock Alert?',
                html: `Remove stock alert for <strong>${ingredient}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/stock-alerts/${id}`,
                        method: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Stock alert removed successfully',
                                timer: 2000
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to delete stock alert'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
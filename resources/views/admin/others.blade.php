@include('admin.layouts.header')
@include('admin.layouts.sidebar')

<div id="content-wrapper" class="d-flex flex-column h-screen overflow-y-auto">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h1 class="h3 mb-0 text-gray-800">Others</h1>
        </nav>

        <div class="container-fluid px-4">
            <div class="row justify-content-start">
                <!-- Operating Hours Card -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header text-gray-600 py-3">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="far fa-calendar-check mr-2"></i>Operating Hours Management
                            </h6>
                        </div>
                        <div class="card-body p-3" id="calendar-body"></div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header text-gray-600 py-3">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="fas fa-percentage mr-2"></i>Discount Management
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <form method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Discount Name</label>
                                    <input type="text" name="discount_name" class="form-control form-control-sm"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Discount Percentage</label>
                                    <input type="number" name="discount_percentage" class="form-control form-control-sm"
                                        min="0" max="100" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm btn-block">
                                    <i class="fas fa-save mr-1"></i>Add Discount
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .cal-header {
            font-size: 0.75rem;
            font-weight: 600;
            color: #5a5c69;
            padding: 0.5rem 0.25rem;
            border-bottom: 1px solid #e9ecef;
        }

        .cal-day {
            font-size: 0.875rem;
            font-weight: 500;
            color: #2e3338;
            padding: 0.75rem 0.25rem;
            cursor: pointer;
            transition: background 0.2s;
        }

        .cal-day:hover:not(.empty) {
            background: #d1e7fd;
            color: #4e73df;
            border-radius: 0.25rem;
        }

        .cal-day.today {
            background: rgb(77, 77, 247);
            color: white;
            font-weight: 600;
            border-radius: 0.25rem;
        }

        .cal-day.empty {
            color: transparent;
            cursor: default;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(78, 115, 223, 0.3);
        }

        .btn-light:hover {
            background: #4e73df;
            color: white;
        }
    </style>

    <script>
        let month = {{ now()->month }};
        let year = {{ now()->year }};

        function renderCalendar(m, y) {
            const date = new Date(y, m - 1, 1);
            const daysInMonth = new Date(y, m, 0).getDate();
            const firstDay = date.getDay();
            const today = new Date();

            let html = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button onclick="changeMonth(-1)" class="btn btn-sm btn-light"><i class="fas fa-chevron-left"></i></button>
            <h6 class="mb-0 font-weight-bold">${date.toLocaleDateString('en-US', { month: 'long', year: 'numeric' })}</h6>
            <button onclick="changeMonth(1)" class="btn btn-sm btn-light"><i class="fas fa-chevron-right"></i></button>
        </div>
        <table class="table table-borderless mb-0">
            <thead><tr class="text-center">
                <th class="cal-header">Su</th><th class="cal-header">Mo</th><th class="cal-header">Tu</th>
                <th class="cal-header">We</th><th class="cal-header">Th</th><th class="cal-header">Fr</th><th class="cal-header">Sa</th>
            </tr></thead><tbody>`;

            let day = 1;
            for (let week = 0; week < 6; week++) {
                html += '<tr class="text-center">';
                for (let d = 0; d < 7; d++) {
                    const cell = (week * 7) + d;
                    if (cell >= firstDay && day <= daysInMonth) {
                        const isToday = day === today.getDate() && m === (today.getMonth() + 1) && y === today.getFullYear();
                        html += `<td class="cal-day ${isToday ? 'today' : ''}">${day}</td>`;
                        day++;
                    } else {
                        html += '<td class="cal-day empty"></td>';
                    }
                }
                html += '</tr>';
                if (day > daysInMonth) break;
            }

            html += `</tbody></table>
        <div class="border rounded p-3 bg-light mt-3">
            <h6 class="text-muted mb-2 small"><i class="far fa-clock mr-1"></i>Set Operating Hours</h6>
            <form method="POST">
                @csrf
                <div class="row mb-2">
                    <div class="col-6">
                        <label class="small text-muted mb-1">Opening Time</label>
                        <input type="time" name="opening_time" class="form-control form-control-sm"  required>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted mb-1">Closing Time</label>
                        <input type="time" name="closing_time" class="form-control form-control-sm"  required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm btn-block">
                    <i class="fas fa-save mr-1"></i>Save 
                </button>
            </form>
        </div>`;

            document.getElementById('calendar-body').innerHTML = html;
        }

        function changeMonth(dir) {
            month += dir;
            if (month > 12) { month = 1; year++; }
            if (month < 1) { month = 12; year--; }
            renderCalendar(month, year);
        }

        document.addEventListener('DOMContentLoaded', () => renderCalendar(month, year));
    </script>
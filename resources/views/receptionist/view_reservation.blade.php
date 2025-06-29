<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reservations by Table</title>
  <link rel="shortcut icon" type="x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>

.table-buttons-container {
      
      gap: 10px;
      margin-bottom: 20px;
    }

    .table-btn {
      margin: 3px;
      font-size: 16px;
    }
    

    @media (max-width: 1024px) {
      .table-buttons-container {
        grid-template-columns: repeat(5, 1fr);
      }
    }

    @media (max-width: 768px) {
      
      .reservation-section table {
        font-size: 16px;
      }
      .table-btn {
        font-size: 16px;
      }
      
    }
    
  </style>
</head>
<body>
  <div class="container my-5">
    <h2 class="text-start mb-4">Reservations</h2>

    <!-- Table Buttons -->
    <div class="text-center mb-4">
      @for ($i = 1; $i <= 17; $i++)
  <button id="btn-{{ $i }}" class="btn btn-primary table-btn" onclick="showTable({{ $i }})">Table {{ $i }}</button>
@endfor

    </div>

    <!-- Reservation Tables -->
    @php use Carbon\Carbon; @endphp
    @for ($i = 1; $i <= 17; $i++)
      @php
      
        $tableReservations = $reservations->where('table_number', $i)->groupBy('reservation_id');
      @endphp

      <div id="table-{{ $i }}" class="reservation-section">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h4>Today Reservations for Table {{ $i }}</h4>
        <div>
          <a href="{{ url()->current() }}?date=today" class="btn btn-success table-btn me-2">Today's Reservations</a>
          <a href="{{ url()->current() }}?date=tomorrow" class="btn btn-success table-btn me-2">Tomorrow's Reservations</a>
          <a href="{{ url()->current() }}?date=next" class="btn btn-success table-btn">Next day's Reservations</a>
        </div>
      </div>

      @if ($tableReservations->isEmpty())
        <div class="alert alert-warning">No reservations found for this table.</div>
      @else
        <div class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th>No. of Pax</th>
                <th>Orders</th>
                <th>Customer Name</th>
                <th>Arrival Time</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($tableReservations as $reservationId => $group)
                @php
                  $first = $group->first();
                  $orders = $group->map(function ($r) {
                      return $r->menu_item ? $r->quantity . 'x ' . $r->menu_item : null;
                  })->filter()->implode(', ');
                @endphp
                <tr class="color-white">
                  <td>{{ $first->pax }}</td>
                  <td>{{ $orders ?: 'No orders' }}</td>
                  <td>{{ $first->customer_name }}</td>
                  <td>{{ Carbon::parse($first->reservation_time)->format('M d, Y h:i A') }}</td>
                  <td>
                    <a href="" class="btn btn-sm btn-outline-primary" title="Edit Orders">
                      <i class="bi bi-pencil"></i>
                    </a>
                  </td>
                </tr>

              @endforeach
            </tbody>
          </table>
          
        </div>
      @endif
    </div>

    @endfor

    <div class="mt-4">
      <a href="{{ route('receptionist.home') }}" class="btn btn-secondary">Back</a>
    </div>
  </div>

  <script>
function showTable(tableNumber) {
  document.querySelectorAll('.reservation-section').forEach(s => s.style.display = 'none');
  document.getElementById(`table-${tableNumber}`).style.display = 'block';

  document.querySelectorAll('.table-btn').forEach(btn => {
    btn.classList.remove('bg-secondary', 'text-white');
    btn.classList.add('btn-primary');
  });

  const selectedBtn = document.getElementById(`btn-${tableNumber}`);
  selectedBtn.classList.remove('btn-primary');
  selectedBtn.classList.add('bg-secondary', 'text-white');

  
  localStorage.setItem('selectedTable', tableNumber);
}

document.addEventListener("DOMContentLoaded", function() {
  
  const savedTable = localStorage.getItem('selectedTable') || 1;
  showTable(savedTable);
});
</script>



</body>
</html>

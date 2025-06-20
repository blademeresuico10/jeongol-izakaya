<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reservations by Table</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .table-btn {
      margin: 5px;
    }
    .reservation-section {
      display: none;
      margin-top: 20px;
    }

    .selectedSection{
      background-color: red;
    }
  </style>
</head>
<body>
  <div class="container my-5">
    <h2 class="text-center mb-4">Reservations</h2>

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
        <h4>Reservations for Table {{ $i }}</h4>
        @if ($tableReservations->isEmpty())
          <div class="alert alert-warning">No reservations found for this table.</div>
        @else
          <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
              <thead class="table-light">
                <tr>
                  <th>No. of Pax</th>
                  <th>Orders</th>
                  <th>Customer Name</th>
                  <th>Arrival Time</th>
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
                  <tr>
                    <td>{{ $first->pax }}</td>
                    <td>{{ $orders ?: 'No orders' }}</td>
                    <td>{{ $first->customer_name }}</td>
                    <td>{{ Carbon::parse($first->reservation_time)->format('M d, Y h:i A') }}</td>
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

    document.querySelectorAll('.table-btn').forEach(btn => btn.classList.remove('btn-danger'));
    document.getElementById(`btn-${tableNumber}`).classList.add('btn-danger');
  }
</script>

</body>
</html>

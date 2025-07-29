<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Kitchen</title>
  <link rel="shortcut icon" type="x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f9f9f9;
    }

    .back-button {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: #dc3545;
      color: white;
      padding: 10px 15px;
      border-radius: 5px;
      text-decoration: none;
      font-weight: bold;
      z-index: 1000;
    }

    .back-button:hover {
      background: #c82333;
    }

    thead th {
      background-color: #0d6efd !important;
      color: #fff;
    }
  </style>
</head>
<body>

<div class="reservation-section">
  <div class="items-center mt-4 mb-3">
    <div class="flex-1 text-center">
      <h2 style="font-size: 2rem; font-weight: 700;">Reservations</h2>
    </div>
  </div>
  @php
  $reservationGroups = $reservations->groupBy('reservation_id');
  @endphp

  @if ($reservationGroups->isEmpty())
    <div class="alert alert-warning">No reservations found for today.</div>
  @else
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th>Table Number</th>
            <th>No. of Pax</th>
            <th>Orders/Quantity</th>
            <th>Note</th>
            <th>Order Time</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($reservationGroups as $reservationId => $group)
            @php
              $first = $group->first();
              $orders = $group->map(function ($r) {
                  if (!$r->menu_item) return null;
                  $cleanName = str_replace([' Lunch', ' Dinner'], '', $r->menu_item);
                  return $r->quantity . 'x ' . $cleanName;
              })->filter()->implode(', ');
              $notes = $group->pluck('order_notes')->filter()->unique()->implode(', ');
            @endphp
            <tr>
              <td>{{ $first->table_number }}</td>
              <td>{{ $first->pax }}</td>
              <td>{{ $orders ?: 'No orders' }}</td>
              <td>{{ $notes ?: 'None' }}</td>
              <td>{{ \Carbon\Carbon::parse($first->reservation_time)->format('h:i A') }}</td>
              <td>
                <button class="btn bg-success btn-sm text-white">Completed</button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>

<a class="back-button" href="{{ route('receptionist.home') }} ">
  Back to main page
</a>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

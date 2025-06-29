<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Kitchen</title>
  <link rel="shortcut icon" type="x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
      background: #f9f9f9;
    }

    .meat-stock-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #fff;
      padding: 15px;
      border: 1px solid #ccc;
      margin-bottom: 20px;
    }

    .update-stock-btn {
      padding: 10px 15px;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .logout-button {
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

    .logout-button:hover {
      background: #c82333;
    }

   
    .progress-vertical {
      width: 20px;
      height: 60px;
      position: relative;
      background-color: #e9ecef;
      border-radius: 4px;
      overflow: hidden;
      display: flex;
      align-items: flex-end;
    }

    .progress-bar-vertical {
      width: 100%;
      transition: height 0.4s;
    }

   

  </style>
</head>
<body>

<div class="meat-stock-header">
  <h2 class="text-start text-nowrap">Meat Stock Levels</h2>

  <div class="d-flex ms-4">
    @foreach ($stock as $item)
      @php
        $qty = $item->stock_quantity;
        $statusColor = $qty >= 60 ? 'bg-success' :
                      ($qty >= 30 ? 'bg-warning' : 'bg-danger');
      @endphp
      <div class="d-flex flex-column align-items-center mx-3">
        <div class="progress-vertical mb-1">
          <div class="progress-bar-vertical {{ $statusColor }}" style="height: {{ $qty }}%;"></div>
        </div>
        <div class="fw-medium">{{ $item->stock_name }}</div>
      </div>
    @endforeach
  </div>

  <div class="d-flex flex-column align-items-start ms-4">
    <div class="d-flex align-items-center mb-1">
      <div style="width: 20px; height: 10px; background-color: green;"></div>
      <span class="ms-2 text-nowrap">60kg up</span>
    </div>
    <div class="d-flex align-items-center mb-1">
      <div style="width: 20px; height: 10px; background-color: orange;"></div>
      <span class="ms-2 text-nowrap">30kg - 59kg</span>
    </div>
    <div class="d-flex align-items-center">
      <div style="width: 20px; height: 10px; background-color: red;"></div>
      <span class="ms-2 text-nowrap">Below 29kg</span>
    </div>
  </div>

  <button class="update-stock-btn ms-4" data-bs-toggle="modal" data-bs-target="#updateStockModal">
    Update Stock
  </button>
</div>



@php
$reservationGroups = $reservations->groupBy('reservation_id');
@endphp

<div class="reservation-section">
  <h4>Order List</h4>
  @if ($reservationGroups->isEmpty())
    <div class="alert alert-warning">No reservations found for today.</div>
  @else
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
          <tr>
            <th>Customer Name</th>
            <th>Table Number</th>
            <th>No. of Pax</th>
            <th>Orders/Quantity</th>
            <th>Order Time</th>
            <th>Notes</th>
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
            @endphp
            <tr>
              <td>{{ $first->customer_name }}</td>
              <td>{{ $first->table_number }}</td>
              <td>{{ $first->pax }}</td>
              <td>{{ $orders ?: 'No orders' }}</td>
              <td>{{ \Carbon\Carbon::parse($first->reservation_time)->format('h:i A') }}</td>
              <td>{{ $first->notes ?? 'None' }}</td>
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

<!-- Modal -->
<div class="modal fade" id="updateStockModal" tabindex="-1" aria-labelledby="updateStockModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="{{ route('kitchen.updateStock') }}" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Update Stock Levels</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <table class="table align-middle table-bordered text-center">
            <thead class="table-light">
              <tr>
                <th>Item</th>
                <th>Stock Level (kg)</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($stock as $item)
              @php
                $qty = $item->stock_quantity;
                $statusColor = $qty >= 60 ? 'bg-success' :
                              ($qty >= 30 ? 'bg-warning' : 'bg-danger');
              @endphp
              <tr>
                <td>{{ $item->stock_name }}</td>
                <td>
                  <input 
                    type="number" 
                    name="stocks[{{ $item->id }}]" 
                    value="{{ $qty }}" 
                    min="0" 
                    max="100"
                    class="form-control text-center stock-input" 
                    style="width: 80px; margin: 0 auto;" 
                    readonly 
                    required>
                </td>
                <td>
                  <div class="progress-vertical">
                    <div class="progress-bar-vertical {{ $statusColor }}" style="height: {{ $qty }}%;"></div>
                  </div>
                </td>
                <td>
                  <i class="bi bi-pencil-fill text-primary edit-stock" style="cursor: pointer;" data-id="{{ $item->id }}"></i>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save Changes</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<a class="logout-button" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
  Logout
</a>
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.edit-stock').forEach(icon => {
      icon.addEventListener('click', function () {
        const id = this.dataset.id;
        const input = document.querySelector(`input[name="stocks[${id}]"]`);
        if (input) {
          input.removeAttribute('readonly');
          input.focus();
        }
      });
    });
  });

</script>

</body>
</html>

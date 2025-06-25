<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Kitchen</title>
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

    .stock-indicator {
      display: inline-block;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      background-color: gray;
      margin-right: 6px;
      vertical-align: middle;
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
  </style>
</head>
<body>

<div class="meat-stock-header">
  <h2>Meat Stock Levels</h2>
  <table style="width: 50%; text-align: center;">
    <tr class="text-center">
      @foreach (['Beef', 'Pork', 'Chicken', 'Shrimp', 'Vegetables', 'Fish'] as $item)
        @php $qty = $stock->firstWhere('stock_name', $item)->stock_quantity ?? null; @endphp
        <td class="px-4 py-2">
          <div class="d-flex align-items-center justify-content-center gap-2">
            <span class="stock-indicator" data-quantity="{{ $qty }}"></span>
            <span class="fw-medium">{{ $item }}</span>
          </div>
        </td>
      @endforeach
    </tr>
  </table>

  <button class="update-stock-btn" data-bs-toggle="modal" data-bs-target="#updateStockModal">
    Update Meat Stock
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
                return $r->menu_item ? $r->quantity . 'x ' . $r->menu_item : null;
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
                <button class="btn btn-warning btn-sm">Preparing</button>
                <button class="btn btn-success btn-sm">Ready</button>
                <button class="btn btn-dark btn-sm">Completed</button>
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
                <th>Stock Level</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($stock as $item)
              @php
                $qty = $item->stock_quantity;
                $statusColor = 'bg-secondary';
                if ($qty >= 80) $statusColor = 'bg-success';
                elseif ($qty >= 60) $statusColor = 'bg-success bg-opacity-50';
                elseif ($qty >= 40) $statusColor = 'bg-warning bg-opacity-75';
                elseif ($qty >= 20) $statusColor = 'bg-warning';
                else $statusColor = 'bg-danger';
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
                  <span class="d-inline-block rounded-circle {{ $statusColor }}" 
                        style="width: 16px; height: 16px;"></span>
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

<!-- Footer logout -->
<a class="logout-button" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
  Logout
</a>
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    // Status dot color logic
    document.querySelectorAll('.stock-indicator').forEach(indicator => {
      const qty = parseInt(indicator.dataset.quantity);
      if (isNaN(qty)) return indicator.style.backgroundColor = 'gray';
      indicator.style.backgroundColor = qty <= 20 ? 'red' : qty <= 35 ? 'orange' : 'green';
    });

    // Enable editing on pencil click
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

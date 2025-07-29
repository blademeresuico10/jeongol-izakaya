<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Customer Orders</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  @vite('resources/css/app.css')
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
  
</head>

<style>
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
</style>
<body class="bg-white">
  <div class="items-center mt-4 mb-2">
    <div class="flex-1 text-center">
      <h2 style="font-size: 2rem; font-weight: 700;">Customer Orders</h2>
    </div>
</div>

  <div class="flex justify-center mt-4 mb-4">
    <div class="flex items-center">
      <input 
        type="text" 
        id="searchInput" 
        placeholder="Search by customer name" 
        class="block w-full max-w-xs border border-gray-400 rounded px-4 py-2"
        style="width: 350px;"
      />
      <span class="ml-2">
        <i class="fas fa-search text-gray-700"></i>
      </span>
    </div>
  </div>
<div class="flex justify-center">
  <hr class="solid rounded" style="border-top: 3px solid #a5a5a5; border-radius: 5px; width: 90%;">
</div>

<div class="flex justify-center mb-3 ml-4">
  @if ($order_details->isEmpty())
    <div class="bg-yellow-400 text-yellow-900 px-4 py-2 rounded shadow">
      No orders found for today.
    </div> 
  @else
    <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded shadow">
      <strong>Not Completed Orders:</strong> {{ $order_details->count() }}
    </div>
  @endif
</div>


    

    <div class="mt-2">
      <table class="w-full border border-gray-300 text-sm text-gray-800">
      <thead class="bg-color">
        <tr>
          <th>Customer name</th>
          <th>Table Number</th>
          <th>No. of Pax</th>
          <th>Orders/Quantity</th>
          <th>Note</th>
        </tr>
      </thead>
      <tbody >
        @foreach($order_details as $order)
          <tr>
            <td>{{ $order->customer_name }}</td>
            <td>{{ $order->table_number }}</td>
            <td>{{ $order->pax }}</td>
            <td>
              {{ $order->menu_item }} ({{ $order->quantity }})
            </td>
            <td>{{ $order->order_notes }}</td>
          </tr>
        @endforeach

      </tbody>

    </table>
      <!-- Custom scrollbar control -->
      <div style="position: absolute; top: 0; right: 0; width: 16px; height: 100%; pointer-events: none;">
      <div style="width: 100%; height: 100%; background: linear-gradient(to bottom, #e9ecef 0%, #dee2e6 100%); border-radius: 8px; opacity: 0.5;"></div>
      </div>
    </div>

<a class="back-button" href="{{ route('receptionist.home') }} ">
  Back to main page
</a>

</body>
</html>

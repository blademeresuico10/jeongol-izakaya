<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Kitchen</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="shortcut icon" type="x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
</head>

<body class="bg-gray-100 min-h-screen">
  <div class="reservation-section">
    <div class="flex items-center justify-center mt-4 mb-3">
      <h2 class="text-3xl font-bold text-gray-800">Reservations</h2>
    </div>

    @php
    $reservationGroups = $reservations->groupBy('reservation_id');
    @endphp

    <div class="flex justify-center mb-4 px-4">
     
        <div class="border border-gray-200 rounded-lg shadow-md bg-white p-3 w-full max-w-8xl" style="max-height:500px; overflow:auto;">
          <div class="overflow-x-auto">
            <table class="w-full border-collapse"> 
              <thead>
                <tr class="bg-green-600 text-white">
                  <th class="border border-gray-300 px-4 py-3 text-left font-semibold">Table Number</th>
                  <th class="border border-gray-300 px-4 py-3 text-left font-semibold">No. of Pax</th>
                  <th class="border border-gray-300 px-4 py-3 text-left font-semibold">Orders</th>
                  <th class="border border-gray-300 px-4 py-3 text-left font-semibold">Quantity</th>
                  <th class="border border-gray-300 px-4 py-3 text-left font-semibold">Order Time</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($reservationGroups as $reservationId => $group)
                  @php
                    $first = $group->first();
                    $orders = $group->map(function ($r) {
                      if (!$r->menu_item)
                        return null;
                      $cleanName = str_replace([' Lunch', ' Dinner'], '', $r->menu_item);
                      return $r->quantity . 'x ' . $cleanName;
                    })->filter()->implode(', ');

                    $notes = $group->pluck('order_notes')->filter()->unique()->implode(', ');
                  @endphp
                  <tr class="hover:bg-gray-50">
                    <td class="border border-gray-300 px-4 py-3">{{ $first->table_number }}</td>
                    <td class="border border-gray-300 px-4 py-3">{{ $first->pax }}</td>
                    <td class="border border-gray-300 px-4 py-3">{{ $orders ?: 'No orders' }}</td>
                    <td class="border border-gray-300 px-4 py-3">{{ $group->sum('quantity') }}</td>
                    <td class="border border-gray-300 px-4 py-3">{{ \Carbon\Carbon::parse($first->reservation_time)->format('h:i A') }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
     
    </div>
  </div>

  <!-- Back Button -->
  <a class="fixed bottom-5 right-5 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded z-50 transition-colors duration-200 
           md:bottom-4 md:right-4 md:py-3 md:px-5
           sm:bottom-3 sm:right-3 sm:py-2 sm:px-3 sm:text-sm
           max-sm:bottom-2 max-sm:right-2 max-sm:py-1 max-sm:px-2 max-sm:text-xs" 
     href="{{ route('receptionist.home') }}">
    Back to main page
  </a>

</body>

</html>
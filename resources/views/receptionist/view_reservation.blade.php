<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h2 class="text-center mb-4">Reservation List</h2>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Table</th>
                        <th scope="col">No. of Pax</th>
                        <th scope="col">Orders</th>
                        <th scope="col">Customer Name</th>
                        <th scope="col">Arrival Time</th>
                    </tr>
                </thead>
               <tbody>
    @forelse ($reservations as $reservation)
        <tr>
            <td>{{ $reservation->table_number }}</td>
            <td>{{ $reservation->pax }}</td>
            <td>--</td> {{-- Placeholder for orders if not implemented yet --}}
            <td>{{ $reservation->customer_name }}</td>
            <td>{{ \Carbon\Carbon::parse($reservation->reservation_time)->format('M d, Y h:i A') }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center">No reservations found.</td>
        </tr>
    @endforelse
</tbody>

            </table>

            <div>
                <a href="{{route('receptionist.home')}}">Back</a>
            </div>
        </div>
    </div>
</body>
</html>

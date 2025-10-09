<div>
    <h3 class="text-lg font-bold mb-3">Recent Orders</h3>

    <table class="table-auto w-full border border-gray-300 text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th>ID</th>
                <th>Menu</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Table</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->menu->name ?? 'N/A' }}</td>
                    <td>{{ $order->quantity }}</td>
                    <td>{{ $order->status }}</td>
                    <td>{{ $order->reservation->table->table_number ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

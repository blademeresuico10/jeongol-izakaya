<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Stocks Report</title>
    <style>
        body {
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
            font-family: Arial, 'DejaVu Sans', sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .report-title {
            font-size: 22px;
            font-weight: bold;
            color: #2c3e50;
        }

        .section-title {
            font-size: 15px;
            font-weight: bold;
            color: #2c3e50;
            border-bottom: 1px solid #ccc;
            margin-top: 25px;
            margin-bottom: 10px;
            padding-bottom: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 20px;
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="report-title">STOCKS REPORT</div>
        <div class="report-title">JEONGOL IZAKAYA</div>
        <div class="report-period">
            @php
                $asOfLabel = match (request('filter')) {
                    'daily' => 'As of Today',
                    'weekly' => 'As of This Week',
                    'monthly' => 'As of This Month',
                    'yearly' => 'As of This Year',
                    default => 'As of ' . $dateFrom->format('F j, Y') . ' - ' . $dateTo->format('F j, Y'),
                };
            @endphp

            <strong>{{ $asOfLabel }}</strong>
        </div>
        <div class="generated-info">
            <strong>Generated on:</strong> {{ $generatedAt->format('F j, Y g:i A') }}
        </div>
    </div>

    <!-- Current Stocks Section -->
    <div class="section-title">Current Stocks</div>
    @if($currentStocks && $currentStocks->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 35%;">Ingredient Name</th>
                    <th style="width: 25%;">Category</th>
                    <th class="text-center" style="width: 15%;">Unit</th>
                    <th class="text-right" style="width: 25%;">Current Stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach($currentStocks as $stock)
                    <tr>
                        <td>{{ $stock->name }}</td>
                        <td>{{ $stock->category }}</td>
                        <td class="text-center">{{ $stock->unit }}</td>
                        <td class="text-right">{{ number_format($stock->stocks, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">No stock data available.</div>
    @endif

    <!-- Stock In Section -->
    <div class="section-title">Stock In (Arrivals)</div>
    @if($stockIns && $stockIns->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 35%;">Ingredient Name</th>
                    <th class="text-center" style="width: 20%;">Arrived Date</th>
                    <th class="text-center" style="width: 20%;">Expiration Date</th>
                    <th class="text-right" style="width: 25%;">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockIns as $stockIn)
                    <tr>
                        <td>{{ $stockIn->ingredient->name ?? 'N/A' }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($stockIn->arrived_at)->format('M d, Y') }}</td>
                        <td class="text-center">
                            {{ $stockIn->expiration_date ? \Carbon\Carbon::parse($stockIn->expiration_date)->format('M d, Y') : 'N/A' }}
                        </td>
                        <td class="text-right">{{ number_format($stockIn->quantity, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">No stock arrivals in this period.</div>
    @endif

    <!-- Consumed Stocks Section -->
    <div class="section-title">Consumed Stocks</div>
    @if($consumedStocks && $consumedStocks->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 35%;">Ingredient Name</th>
                    <th class="text-center" style="width: 20%;">Date Used</th>
                    <th style="width: 20%;">Order #</th>
                    <th class="text-right" style="width: 25%;">Quantity Used</th>
                </tr>
            </thead>
            <tbody>
                @foreach($consumedStocks as $consumed)
                    <tr>
                        <td>{{ $consumed->ingredient->name ?? 'N/A' }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($consumed->created_at)->format('M d, Y') }}</td>
                        <td>{{ $consumed->order_id ? '#' . $consumed->order_id : 'N/A' }}</td>
                        <td class="text-right">{{ number_format($consumed->quantity, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">No stocks consumed in this period.</div>
    @endif

    <!-- Expired Stocks Section -->
    <div class="section-title">Expired Stocks</div>
    @if($expiredStocks && $expiredStocks->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Ingredient Name</th>
                    <th class="text-center" style="width: 25%;">Expired Date</th>
                    <th class="text-right" style="width: 25%;">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expiredStocks as $expired)
                    <tr>
                        <td>{{ $expired->ingredient->name ?? 'N/A' }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($expired->expired_at)->format('M d, Y') }}</td>
                        <td class="text-right">{{ number_format($expired->quantity, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">No expired stocks in this period.</div>
    @endif

    <div class="footer">
        JEONGOL IZAKAYA • Stocks Report • {{ now()->format('F j, Y') }}
    </div>
</body>

</html>
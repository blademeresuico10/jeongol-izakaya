<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Stock Report</title>
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

        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
        }

        .date-range {
            font-size: 13px;
            color: #555;
            margin-bottom: 5px;
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
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
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
        <div class="report-title">STOCK REPORT</div>
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

    {{-- Current Stock --}}
    @if($currentStocks->count() > 0)
        <div class="section-title">Current Stocks</div>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th class="text-right">Stock Left</th>
                    <th class="text-center">Unit</th>
                    <th class="text-center">Date Added</th>
                    <th class="text-center">Batch</th>
                    <th class="text-center">Expiry</th>
                </tr>
            </thead>
            <tbody>
                @foreach($currentStocks as $stock)
                    <tr>
                        <td>{{ $stock->name }}</td>
                        <td>{{ $stock->category }}</td>
                        <td class="text-right">{{ $stock->stock_left }}</td>
                        <td class="text-center">{{ $stock->unit }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($stock->date_added)->format('m-d-Y') }}</td>
                        <td class="text-center">{{ $stock->batch }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($stock->expiry)->format('m-d-Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Stock Consumed --}}
    @if($consumedStocks->count() > 0)
        <div class="section-title">Stock Consumed (Today)</div>
        <table>
            <thead>
                <tr>
                    <th>Stock Name</th>
                    <th>Category</th>
                    <th class="text-right">Consumed</th>
                    <th class="text-center">Unit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($consumedStocks as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category }}</td>
                        <td class="text-right">{{ $item->consumed }}</td>
                        <td class="text-center">{{ $item->unit }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Stock In --}}
    @if($stockIns->count() > 0)
        <div class="section-title">Stock-In</div>
        <table>
            <thead>
                <tr>
                    <th>Stock Name</th>
                    <th>Category</th>
                    <th class="text-right">Quantity Added</th>
                    <th class="text-center">Unit</th>
                    <th class="text-center">Date Added</th>
                    <th class="text-center">Batch</th>
                    <th class="text-center">Expiry Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockIns as $in)
                    <tr>
                        <td>{{ $in->name }}</td>
                        <td>{{ $in->category }}</td>
                        <td class="text-right">{{ $in->quantity_added }}</td>
                        <td class="text-center">{{ $in->unit }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($in->date_added)->format('m-d-Y') }}</td>
                        <td class="text-center">{{ $in->batch }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($in->expiry_date)->format('m-d-Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($expiredStocks->count() > 0)
        <div class="section-title">Expired Stocks</div>
        <table>
            <thead>
                <tr>
                    <th>Stock Name</th>
                    <th>Category</th>
                    <th class="text-right">Loss</th>
                    <th class="text-center">Date Added</th>
                    <th class="text-center">Batch</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expiredStocks as $expired)
                    <tr>
                        <td>{{ $expired->name }}</td>
                        <td>{{ $expired->category }}</td>
                        <td class="text-right">{{ $expired->loss }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($expired->date_added)->format('m-d-Y') }}</td>
                        <td class="text-center">{{ $expired->batch }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($currentStocks->isEmpty() && $consumedStocks->isEmpty() && $stockIns->isEmpty() && $expiredStocks->isEmpty())
        <div class="no-data">Empty.</div>
    @endif

    <div class="footer">
        JEONGOL IZAKAYA • Stock Report • {{ now()->format('F j, Y') }}
    </div>
</body>

</html>

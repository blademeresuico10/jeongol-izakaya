<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Stock Report - {{ $dateFrom->format('M d, Y') }} to {{ $dateTo->format('M d, Y') }}</title>
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
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .date-range {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        .generated-info {
            font-size: 10px;
            color: #888;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
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

        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="report-title">Stock Report</div>
        <div class="company-name">JEONGOL IZAKAYA</div>
        <div class="date-range">{{ $dateFrom->format('F j, Y') }} - {{ $dateTo->format('F j, Y') }}</div>
    </div>

    <div class="section-title">Stock Overview</div>
    <table>
        <tr>
            <th>Total Items</th>
            <td>{{ $totalItems }}</td>
        </tr>
        <tr>
            <th>Total Quantity</th>
            <td>{{ number_format($totalQuantity, 2) }} kg</td>
        </tr>
        <tr>
            <th>Items Below Threshold</th>
            <td>{{ $lowStockCount }}</td>
        </tr>
    </table>

    <div class="section-title">Item Details</div>
    @if($stocks->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Unit</th>
                    <th>Quantity (kg)</th>
                    <th>Last Updated</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stocks as $index => $stock)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $stock->item_name }}</td>
                        <td>{{ ucfirst($stock->category ?? 'General') }}</td>
                        <td>{{ $stock->unit ?? 'pcs' }}</td>
                        <td class="text-right">{{ number_format($stock->quantity, 2) }}</td>
                        <td>{{ $stock->updated_at->format('M d, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">No stock records found for the selected range.</div>
    @endif

 
</body>

</html>

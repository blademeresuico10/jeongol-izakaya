<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        body {
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
            font-family: Arial, 'DejaVu Sans', sans-serif;
        }

        .currency {
            font-family: DejaVu Sans, 'Courier New', monospace;
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

        .summary-table {
            width: 60%;
            margin: 0 auto 20px auto;
            border-collapse: collapse;
            font-size: 13px;
        }

        .summary-table th,
        .summary-table td {
            border: 1px solid #ccc;
            padding: 8px 10px;
        }

        .summary-table th {
            text-align: left;
            background: #f4f6f8;
            color: #2c3e50;
            width: 70%;
        }

        .summary-table td {
            text-align: right;
            font-weight: bold;
            background: #fff;
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
.

<body>
    <div class="header">
        <div class="report-title">SALES REPORT</div>
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

    @if($groupedSales->count() > 0)
        <div class="section-title">Sales Summary</div>
        <table class="summary-table">
            <tr>
                <th>Gross Sales</th>
                <td class="text-right currency">{{ number_format($grossSales, 2) }}</td>
            </tr>
            <tr>
                <th>Net Sales</th>
                <td class="text-right currency">{{ number_format($netSales, 2) }}</td>
            </tr>
            <tr>
                <th>Total Discount</th>
                <td class="text-right currency">{{ number_format($totalDiscounts, 2) }}</td>
            </tr>
            <tr>
                <th>Total Customers</th>
                <td class="text-right">{{ $totalCustomers }}</td>
            </tr>
        </table>


        <div class="section-title">Order Details</div>
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th class="text-center">Quantity</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groupedSales as $sale)
                    <tr>
                        <td>{{ $sale['item_name'] }}</td>
                        <td class="text-center">{{ $sale['quantity'] }}</td>
                        <td class="text-right currency">{{ number_format($sale['total'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @else
        <div class="no-data">Empty.</div>
    @endif

    <div class="footer">
        JEONGOL IZAKAYA • Sales Report • {{ now()->format('F j, Y') }}
    </div>
</body>

</html>
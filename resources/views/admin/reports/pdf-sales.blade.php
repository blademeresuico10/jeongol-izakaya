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

        .summary-section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .summary-card {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 12px;
            border-radius: 5px;
            text-align: center;
        }

        .summary-label {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
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

        .page-break {
            page-break-before: always;
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

        .currency {
            font-family: 'Courier New', monospace;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="report-title">Sales Report</div>
        <div class="company-name">JEONGOL IZAKAYA</div>
        <div class="date-range">As of {{ $filterDate }}</div>
    </div>

    @if($groupedSales->count() > 0)
        {{-- Sales Summary Table --}}
        <table class="summary-table">
            <thead>
                <tr>
                    <th>Gross Sales</th>
                    <th>Net Sales</th>
                    <th>Total Discounted</th>
                    <th>Total Customers</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-right currency">{{ number_format($grossSales, 2) }}</td>
                    <td class="text-right currency">{{ number_format($netSales, 2) }}</td>
                    <td class="text-right currency">{{ number_format($totalDiscounts, 2) }}</td>
                    <td class="text-center">{{ $totalCustomers }}</td>
                </tr>
            </tbody>
        </table>

        <h3 class="section-title">Order Details</h3>
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Quantity</th>
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
        <div class="no-data">No transactions found for the selected period.</div>
    @endif
</body>


</html>
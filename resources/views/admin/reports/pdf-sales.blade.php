<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        body {
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 15px;
            font-family: Arial, sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .date-range {
            font-size: 11px;
            margin-bottom: 3px;
        }

        .generated-info {
            font-size: 10px;
            color: #666;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background-color: #e8e8e8;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .summary-box {
            border: 2px solid #000;
            padding: 10px;
            margin-bottom: 15px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            border-bottom: 1px dotted #ccc;
        }

        .summary-row:last-child {
            border-bottom: none;
            border-top: 2px solid #000;
            margin-top: 5px;
            padding-top: 8px;
            font-weight: bold;
            font-size: 12px;
        }

        .summary-label {
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #000;
            padding-top: 8px;
        }

        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 15px;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="report-title">SALES REPORT</div>
        <div class="company-name">JEONGOL IZAKAYA</div>
        <div class="date-range">
            <strong>Period:</strong> {{ $dateFrom->format('F j, Y') }} - {{ $dateTo->format('F j, Y') }}
        </div>
        <div class="generated-info">
            Generated on: {{ $generatedAt->format('F j, Y g:i A') }}
        </div>
    </div>

    <!-- Summary Section -->
    <div class="section-title">Sales Summary</div>
    <div class="summary-box">
        <div class="summary-row">
            <span class="summary-label">Gross Sales:</span>
            <span>{{ number_format($grossSales, 2) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Discounts:</span>
            <span>{{ number_format($totalDiscounts, 2) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Net Sales:</span>
            <span>{{ number_format($netSales, 2) }}</span>
        </div>
    </div>

    <div class="summary-box">
        <div class="summary-row">
            <span class="summary-label">Total Orders:</span>
            <span>{{ number_format($totalOrders) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Customers Served:</span>
            <span>{{ number_format($totalCustomers) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Average Order Value:</span>
            <span>{{ $totalOrders > 0 ? number_format($netSales / $totalOrders, 2) : '0.00' }}</span>
        </div>
    </div>

    <!-- E-wallet Payments -->
    <div class="section-title">E-Wallet Payments</div>
    <table>
        <thead>
            <tr>
                <th>Payment Method</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>GCash</td>
                <td class="text-right">{{ number_format($gcashTotal, 2) }}</td>
            </tr>
            <tr>
                <td>Maya</td>
                <td class="text-right">{{ number_format($mayaTotal, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>Total E-wallet Payments</td>
                <td class="text-right">{{ number_format($ewalletTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Daily Breakdown -->
    <div class="section-title">Daily Sales Breakdown</div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th class="text-center">Orders</th>
                <th class="text-right">Gross Sales</th>
                <th class="text-right">Discounts</th>
                <th class="text-right">Net Sales</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dailyBreakdown as $day)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</td>
                    <td class="text-center">{{ $day->orders }}</td>
                    <td class="text-right">{{ number_format($day->gross_sales, 2) }}</td>
                    <td class="text-right">{{ number_format($day->discounts, 2) }}</td>
                    <td class="text-right">{{ number_format($day->sales, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="no-data">No sales data for this period</td>
                </tr>
            @endforelse
        </tbody>
        @if($dailyBreakdown->count() > 0)
            <tfoot>
                <tr class="total-row">
                    <td><strong>TOTAL</strong></td>
                    <td class="text-center"><strong>{{ $totalOrders }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($grossSales, 2) }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalDiscounts, 2) }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($netSales, 2) }}</strong></td>
                </tr>
            </tfoot>
        @endif
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>JEONGOL IZAKAYA • Sales Report • {{ now()->format('F j, Y') }}</p>
    </div>
</body>

</html>
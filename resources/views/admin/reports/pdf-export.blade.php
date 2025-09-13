<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Sales Report - {{ $dateFrom->format('M d, Y') }} to {{ $dateTo->format('M d, Y') }}</title>
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
    <!-- Header -->
    <div class="header">
        <div class="company-name">JEONGOL IZAKAYA</div>
        <div class="report-title">Sales Report</div>
        <div class="date-range">{{ $dateFrom->format('F j, Y') }} - {{ $dateTo->format('F j, Y') }}</div>
        <div class="generated-info">Generated on {{ $generatedAt->format('F j, Y \a\t g:i A') }}</div>
    </div>

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="section-title">Executive Summary</div>
        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-label">Total Sales</div>
                <div class="summary-value currency">{{ number_format($totalSales, 2) }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Total Customers</div>
                <div class="summary-value">{{ number_format($totalPax) }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Transactions</div>
                <div class="summary-value">{{ number_format($transactionCount) }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Avg. Order Value</div>
                <div class="summary-value currency">{{ number_format($averageOrderValue, 2) }}</div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 15px;">
            <div style="display: inline-block; margin: 0 20px;">
                <div class="summary-label">Total Discounts Given</div>
                <div class="summary-value currency">{{ number_format($totalDiscounts, 2) }}</div>
            </div>
        </div>
    </div>


    <!-- Sales Transactions Section -->
    <div class="section-title">Transaction Details</div>
    @if($sales->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">Transaction #</th>
                    <th style="width: 10%;">Date</th>
                    <th style="width: 8%;">Time</th>
                    <th style="width: 8%;">Table</th>
                    <th style="width: 15%;">Customer</th>
                    <th style="width: 6%;">Pax</th>
                    <th style="width: 12%;">Subtotal</th>
                    <th style="width: 10%;">Discount</th>
                    <th style="width: 12%;">Total</th>
                    <th style="width: 7%;">Payment</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $sale)
                    <tr>
                        <td>{{ $sale->transaction_no ?? '#' . $sale->id }}</td>
                        <td>{{ $sale->date }}</td>
                        <td>{{ $sale->time ?? 'N/A' }}</td>
                        <td class="text-center">{{ $sale->table_number }}</td>
                        <td>{{ $sale->customer_name }}</td>
                        <td class="text-center">{{ $sale->pax }}</td>
                        <td class="text-right currency">{{ number_format($sale->subtotal, 2) }}</td>
                        <td class="text-right currency">{{ number_format($sale->discount_total ?? 0, 2) }}</td>
                        <td class="text-right currency">{{ number_format($sale->total, 2) }}</td>
                        <td class="text-center">{{ $sale->payment_method ?? 'Cash' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">No transactions found for the selected period.</div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div>Generated by Jeongol Izakaya Management System</div>
    </div>
</body>

</html>
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
    <div class="header">
        <div class="report-title">Sales Report</div>
        <div class="company-name">JEONGOL IZAKAYA</div>
        <div class="date-range">{{ $dateFrom->format('F j, Y') }} - {{ $dateTo->format('F j, Y') }}</div>
    </div>

    <div class="section-title">Transaction Details</div>
    @if($sales->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Transaction #</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Customer</th>
                    <th>Table / Type</th>
                    <th>Pax</th>
                    <th class="text-right">Subtotal (₱)</th>
                    <th class="text-right">Discount (₱)</th>
                    <th class="text-right">Advance (₱)</th>
                    <th class="text-right">Total (₱)</th>
                    <th>Payment</th>
                    <th>Cashier</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @foreach($sales as $sale)
                    <tr>
                        <td>{{ $sale->transaction_no ?? '#' . $sale->id }}</td>
                        <td>{{ $sale->created_at ? $sale->created_at->format('Y-m-d') : 'N/A' }}</td>
                        <td>{{ $sale->created_at ? $sale->created_at->format('h:i A') : 'N/A' }}</td>
                        <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                        <td>
                            @if($sale->reservation)
                                Table {{ $sale->reservation->table_number }}
                            @elseif($sale->walkin)
                                Walk-in
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $sale->reservation->pax ?? $sale->walkin->pax ?? '-' }}
                        </td>
                        <td class="text-right currency">{{ number_format($sale->orders_total, 2) }}</td>
                        <td class="text-right currency">{{ number_format($sale->discount_total ?? 0, 2) }}</td>
                        <td class="text-right currency">{{ number_format($sale->advance_payment ?? 0, 2) }}</td>
                        <td class="text-right currency">{{ number_format($sale->grand_total, 2) }}</td>
                        <td class="text-center">{{ ucfirst($sale->payment_method ?? 'Cash') }}</td>
                        <td>{{ $sale->cashier->name ?? 'N/A' }}</td>
                        <td class="text-center">
                            @if($sale->status === 'paid')
                                <span style="color: green;">Paid</span>
                            @elseif($sale->status === 'pending')
                                <span style="color: orange;">Pending</span>
                            @else
                                <span style="color: gray;">{{ ucfirst($sale->status ?? 'N/A') }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    @else
        <div class="no-data">No transactions found for the selected period.</div>
    @endif

</body>

</html>
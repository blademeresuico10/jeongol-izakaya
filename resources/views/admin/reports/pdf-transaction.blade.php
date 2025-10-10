<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Transaction Report</title>
    <style>
        body {
            font-size: 12px;
            font-family: Arial, sans-serif;
            padding: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            font-size: 11px;
        }

        th {
            background-color: #f4f4f4;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .section-title {
            font-weight: bold;
            margin: 15px 0 5px;
            font-size: 13px;
            border-bottom: 1px solid #ddd;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="report-title">TRANSACTION REPORT</div>
        <div class=".company-name">JEONGOL IZAKAYA</div>
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


    @forelse($groupedTransactions as $cashier => $transactions)
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cashier</th>
                    <th>Date</th>
                    <th>Transaction No.</th>
                    <th>Customer</th>
                    <th>Payment Method</th>
                    <th class="text-center">Pax</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $staffTotal = 0; @endphp
                @foreach($transactions as $i => $t)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $cashier }}</td>
                        <td>{{ $t->date }}</td>
                        <td>{{ $t->transaction_no }}</td>
                        <td>{{ $t->customer_name }}</td>
                        <td>{{ $t->payment_method }}</td>
                        <td class="text-center">{{ $t->pax }}</td>
                        <td class="text-right">{{ number_format($t->total_amount, 2) }}</td>
                    </tr>
                    @php $staffTotal += $t->total_amount; @endphp
                @endforeach
                <tr>
                    <td></td>
                    <td colspan="6" class="text-right"><strong>Total</strong></td>
                    <td class="text-right"><strong>{{ number_format($staffTotal, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    @empty
        <p style="text-align:center; color:#777;">No transactions found for this period.</p>
    @endforelse

    <div class="footer">
        JEONGOL IZAKAYA • Transaction Report • {{ now()->format('F j, Y') }}
    </div>
</body>

</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction Report</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            margin: 30px;
            color: #333;
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 20px;
        }
        .report-title {
            font-size: 16px;
            font-weight: bold;
        }
        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 8px;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }
        .summary-box {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 15px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th, td {
            padding: 6px 8px;
            border: 1px solid #999;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .no-data {
            text-align: center;
            font-style: italic;
            color: #666;
            padding: 10px 0;
        }
        .footer p {
            font-size: 10px;
            color: #555;
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="report-title">TRANSACTION REPORT</div>
        <div class="company-name">JEONGOL IZAKAYA</div>
        <div class="date-range">
            <strong>Period:</strong> {{ $dateFrom->format('F j, Y') }} - {{ $dateTo->format('F j, Y') }}
        </div>
        <div class="generated-info">
            Generated on: {{ $generatedAt->format('F j, Y g:i A') }}
        </div>
    </div>


    <!-- Cashier Logs -->
    <div class="section-title">Cashier Logs</div>
    <table>
        <thead>
            <tr>
                <th style="width: 40%;">Cashier Name</th>
                <th style="width: 30%;" class="text-right">Transaction Count</th>
                <th style="width: 30%;" class="text-right">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['cashier_summary'] as $cashier)
                <tr>
                    <td>{{ $cashier['cashier_name'] }}</td>
                    <td class="text-right">{{ number_format($cashier['transaction_count']) }}</td>
                    <td class="text-right">₱{{ number_format($cashier['total_amount'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="no-data">No cashier logs for this period</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>JEONGOL IZAKAYA • Transaction Report • {{ now()->format('F j, Y') }}</p>
    </div>
</body>
</html>

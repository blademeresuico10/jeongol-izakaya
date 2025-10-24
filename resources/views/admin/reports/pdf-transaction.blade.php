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
        .header {
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
        .cashier-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .cashier-header {
            background-color: #f4f4f4;
            padding: 10px;
            border: 1px solid #999;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .cashier-stats {
            display: flex;
            justify-content: space-between;
            padding: 5px 10px;
            background-color: #fafafa;
            border: 1px solid #ddd;
            border-top: none;
            margin-bottom: 10px;
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
        .text-center {
            text-align: center;
        }
        .no-data {
            text-align: center;
            font-style: italic;
            color: #666;
            padding: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
        }
        .footer p {
            font-size: 10px;
            color: #555;
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
    
    @forelse($data['cashier_summary'] as $cashier)
        <div class="cashier-section">
            <div class="cashier-header">
                {{ $cashier['cashier_name'] }}
            </div>
            <div class="cashier-stats">
                <span><strong>Transactions:</strong> {{ number_format($cashier['transaction_count']) }}</span>
                <span><strong>Total Orders:</strong> {{ number_format($cashier['total_orders_count']) }}</span>
                <span><strong>Total Amount:</strong> {{ number_format($cashier['total_amount'], 2) }}</span>
            </div>
            
            <!-- Orders Breakdown Table -->
            <table>
                <thead>
                    <tr>
                        <th style="width: 50%;">Menu Item</th>
                        <th style="width: 15%;" class="text-center">Quantity</th>
                        <th style="width: 17.5%;" class="text-right">Unit Price</th>
                        <th style="width: 17.5%;" class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cashier['orders_breakdown'] as $order)
                        <tr>
                            <td>{{ $order['menu_name'] }}</td>
                            <td class="text-center">{{ number_format($order['quantity']) }}</td>
                            <td class="text-right">{{ number_format($order['price'], 2) }}</td>
                            <td class="text-right">{{ number_format($order['total'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="no-data">No orders found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @empty
        <div class="no-data">No cashier logs for this period</div>
    @endforelse

    <!-- Footer -->
    <div class="footer">
        <p>JEONGOL IZAKAYA • Transaction Report • {{ now()->format('F j, Y') }}</p>
    </div>
</body>
</html>
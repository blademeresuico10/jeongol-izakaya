<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order</title>
    <style>
        body { 
            font-family: Arial, sans-serif;
            padding: 40px;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        
        .header h3 {
            margin: 10px 0 0 0;
            font-weight: normal;
        }

        .header .date-info {
            margin-top: 15px;
            font-size: 12px;
            color: #666;
        }
        
        .info {
            margin: 30px 0;
        }
        
        .info p {
            margin: 8px 0;
            font-size: 14px;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin: 20px 0;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 150px;
            padding: 5px 0;
        }

        .info-value {
            display: table-cell;
            padding: 5px 0;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-pending {
            background: #ffc107;
            color: #000;
        }

        .status-completed {
            background: #28a745;
            color: #fff;
        }

        .status-critical {
            background: #dc3545;
            color: #fff;
        }

        .status-low {
            background: #ffc107;
            color: #000;
        }
        
        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        
        .order-table th,
        .order-table td {
            border: 1px solid #000;
            padding: 15px;
            text-align: left;
        }
        
        .order-table th {
            background: #f0f0f0;
            font-weight: bold;
        }
        
        .quantity {
            font-size: 18px;
            font-weight: bold;
        }

        .stock-warning {
            color: #dc3545;
            font-weight: bold;
        }

        .meta-info {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .meta-info p {
            margin: 5px 0;
            font-size: 13px;
        }

        .meta-info strong {
            color: #495057;
        }
        
        .signature {
            margin-top: 60px;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            width: 300px;
            margin-top: 50px;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
        }

        .signature-box {
            width: 45%;
            margin-bottom: 40px;
        }

        .signature-box .signature-line {
            border-bottom: 1px solid #000;
            margin-top: 40px;
            padding-top: 5px;

        }

        .signature-box p {
            margin: 5px 0;
            font-size: 12px;
        }

        .footer-note {
            margin-top: 40px;
            padding: 15px;
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>JEONGOL IZAKAYA</h1>
        <h3>Purchase Order</h3>
    </div>

    <table class="order-table">
        <thead>
            <tr>
                <th>Item Description</th>
                <th style="width: 150px; text-align: center;">Order Quantity</th>
                <th style="width: 150px; text-align: center;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ $ingredient['name'] }}</strong>
                </td>
               
                <td class="quantity" style="text-align: center;">
                    {{ $order['quantity'] }} {{ $ingredient['unit'] }}
                </td>
                <td style="text-align: center;"></td>
            </tr>
        </tbody>
    </table>

    @if(isset($order['notes']) && $order['notes'])
    <div class="footer-note">
        <strong>Notes:</strong> {{ $order['notes'] }}
    </div>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <p><strong>Prepared By:</strong></p>
            <div class="signature-line"></div>
            <p><strong>{{ $requestedBy ?? Auth::user()->name }}</strong></p>
            <p>{{ $date ?? now()->format('F d, Y') }}</p>
        </div>

        <div class="signature-box">
            <p><strong>Received By (Supplier):</strong></p>
            <div class="signature-line"></div>
            <p>Date: _______________</p>
        </div>
    </div>

</body>
</html>
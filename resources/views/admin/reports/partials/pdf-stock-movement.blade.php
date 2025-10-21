<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Stock Movement Report</title>
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
        }

        .summary-label {
            font-weight: bold;
        }

        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-in {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-out {
            background-color: #f8d7da;
            color: #721c24;
        }

        .badge-expired {
            background-color: #fff3cd;
            color: #856404;
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
        <div class="report-title">STOCK MOVEMENT REPORT</div>
        <div class="company-name">JEONGOL IZAKAYA</div>
        <div class="date-range">
            <strong>Period:</strong> {{ $dateFrom->format('F j, Y') }} - {{ $dateTo->format('F j, Y') }}
        </div>
        <div class="generated-info">
            Generated on: {{ $generatedAt->format('F j, Y g:i A') }}
        </div>
    </div>

    <!-- Summary Box -->
    <div class="summary-box">
        <div class="summary-row">
            <span class="summary-label">Total Stock In:</span>
            <span>{{ number_format($reportData['summary']['stock_in']) }} ({{ number_format($reportData['summary']['stock_in_qty'], 2) }} {{ $reportData['summary']['unit'] }})</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Stock Out:</span>
            <span>{{ number_format($reportData['summary']['stock_out']) }} ({{ number_format($reportData['summary']['stock_out_qty'], 2) }} {{ $reportData['summary']['unit'] }})</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Expired Items:</span>
            <span>{{ number_format($reportData['summary']['expired']) }} ({{ number_format($reportData['summary']['expired_qty'], 2) }} {{ $reportData['summary']['unit'] }})</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Movements:</span>
            <span>{{ number_format($reportData['summary']['total_movements']) }}</span>
        </div>
    </div>

    <!-- Movement Details -->
    <div class="section-title">Movement Details</div>

    @if(isset($reportData['movements']) && count($reportData['movements']) > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 12%;">Date</th>
                <th style="width: 25%;">Ingredient</th>
                <th style="width: 13%;">Category</th>
                <th class="text-center" style="width: 10%;">Type</th>
                <th class="text-right" style="width: 12%;">Quantity</th>
                <th class="text-right" style="width: 14%;">Before</th>
                <th class="text-right" style="width: 14%;">After</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['movements'] as $move)
            <tr>
                <td>{{ $move['date'] }}</td>
                <td>{{ $move['ingredient'] }}</td>
                <td>{{ ucfirst($move['category']) }}</td>
                <td class="text-center">
                    @if($move['type'] === 'stock_in')
                        <span class="badge badge-in">IN</span>
                    @elseif(in_array($move['type'], ['stock_out', 'used']))
                        <span class="badge badge-out">OUT</span>
                    @else
                        <span class="badge badge-expired">EXP</span>
                    @endif
                </td>
                <td class="text-right">{{ number_format($move['quantity'], 2) }} {{ $move['unit'] }}</td>
                <td class="text-right">{{ number_format($move['stock_before'], 2) }}</td>
                <td class="text-right">{{ number_format($move['stock_after'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        No stock movements for this period.
    </div>
    @endif

    <div class="footer">
        <p>JEONGOL IZAKAYA • Stock Movement Report • {{ now()->format('F j, Y') }}</p>
    </div>
</body>

</html>
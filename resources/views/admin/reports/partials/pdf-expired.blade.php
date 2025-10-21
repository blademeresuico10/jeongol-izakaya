<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Expired Items Report</title>
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
        <div class="report-title">EXPIRED ITEMS REPORT</div>
        <div class="company-name">JEONGOL IZAKAYA</div>
        <div class="date-range">
            <strong>Period:</strong> {{ $dateFrom->format('F j, Y') }} - {{ $dateTo->format('F j, Y') }}
        </div>
        <div class="generated-info">
            Generated on: {{ $generatedAt->format('F j, Y g:i A') }}
        </div>
    </div>

    <!-- Summary Section -->
    <div class="section-title">Expiration Summary</div>
    <div class="summary-box">
        <div class="summary-row">
            <span class="summary-label">Total Expired Items:</span>
            <span>{{ number_format($reportData['summary']['expired_count']) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Items Expiring Soon (7 days):</span>
            <span>{{ number_format($reportData['summary']['expiring_soon_count']) }}</span>
        </div>
    </div>

    <!-- Category Breakdown -->
    @if(isset($reportData['summary']['by_category']) && count($reportData['summary']['by_category']) > 0)
    <div class="section-title">Waste by Category</div>
    <table>
        <thead>
            <tr>
                <th style="width: 60%;">Category</th>
                <th class="text-right" style="width: 40%;">Items Expired</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['summary']['by_category'] as $category)
                <tr>
                    <td>{{ ucfirst($category['name']) }}</td>
                    <td class="text-right">{{ number_format($category['count']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Expired Items Details -->
    <div class="section-title">Expired Items Details</div>
    <table>
        <thead>
            <tr>
                <th style="width: 30%;">Ingredient</th>
                <th style="width: 20%;">Category</th>
                <th class="text-center" style="width: 15%;">Batch ID</th>
                <th class="text-right" style="width: 15%;">Quantity</th>
                <th class="text-center" style="width: 20%;">Expired On</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData['expired_items'] as $item)
                <tr>
                    <td>{{ $item['name'] ?? 'N/A' }}</td>
                    <td>{{ ucfirst($item['category'] ?? 'N/A') }}</td>
                    <td class="text-center">#{{ $item['batch_id'] }}</td>
                    <td class="text-right">{{ number_format($item['quantity'], 2) }} {{ $item['unit'] ?? 'kg' }}</td>
                    <td class="text-center">{{ $item['expiration_date'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="no-data">No expired items for this period</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Expiring Soon Alert -->
    @if(isset($reportData['expiring_soon']) && count($reportData['expiring_soon']) > 0)
    <div class="section-title">⚠ Items Expiring Soon (Within 7 Days)</div>
    <table>
        <thead>
            <tr>
                <th style="width: 40%;">Ingredient</th>
                <th style="width: 30%;">Category</th>
                <th class="text-center" style="width: 30%;">Expiration Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['expiring_soon'] as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ ucfirst($item['category']) }}</td>
                    <td class="text-center">{{ $item['expiration_date'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        <p>JEONGOL IZAKAYA • Expired Items Report • {{ now()->format('F j, Y') }}</p>
    </div>
</body>

</html>
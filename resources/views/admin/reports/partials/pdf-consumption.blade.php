<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Consumption Report</title>
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
        <div class="report-title">CONSUMPTION REPORT</div>
        <div class="company-name">JEONGOL IZAKAYA</div>
        <div class="date-range">
            <strong>Period:</strong> {{ $dateFrom->format('F j, Y') }} - {{ $dateTo->format('F j, Y') }}
        </div>
        <div class="generated-info">
            Generated on: {{ $generatedAt->format('F j, Y g:i A') }}
        </div>
    </div>

    <!-- Category Breakdown -->
    @if(isset($reportData['summary']['by_category']) && count($reportData['summary']['by_category']) > 0)
    <div class="section-title">Consumption by Category</div>
    <table>
        <thead>
            <tr>
                <th style="width: 40%;">Category</th>
                <th class="text-right" style="width: 30%;">Quantity</th>
                <th class="text-right" style="width: 30%;">Percentage</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['summary']['by_category'] as $category)
                <tr>
                    <td>{{ ucfirst($category['name']) }}</td>
                    <td class="text-right">{{ number_format($category['quantity'], 2) }} kg/pcs</td>
                    <td class="text-right">{{ number_format($category['percentage'], 1) }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Top Consumed Items -->
    <div class="section-title">Most Consumed Items</div>
    @if(isset($reportData['top_consumed']) && count($reportData['top_consumed']) > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 35%;">Ingredient</th>
                <th style="width: 25%;">Category</th>
                <th class="text-right" style="width: 20%;">Quantity</th>
                <th class="text-right" style="width: 15%;">Times Used</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['top_consumed'] as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ ucfirst($item['category']) }}</td>
                    <td class="text-right">{{ number_format($item['total_consumed'], 2) }} {{ $item['unit'] }}</td>
                    <td class="text-right">{{ number_format($item['usage_count']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">No consumption data available</div>
    @endif

    <!-- Consumption Log -->
    <div class="section-title">Consumption Log</div>
    @if(isset($reportData['consumption_data']) && count($reportData['consumption_data']) > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Date</th>
                <th style="width: 20%;">Category</th>
                <th class="text-right" style="width: 20%;">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['consumption_data'] as $item)
                <tr>
                    <td>{{ $item['date'] }}</td>
                    <td>{{ ucfirst($item['category'] ?? 'N/A') }}</td>
                    <td class="text-right">{{ number_format($item['quantity'], 2) }} {{ $item['unit'] ?? 'kg' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">No consumption data for this period</div>
    @endif

    <div class="footer">
        <p>JEONGOL IZAKAYA • Consumption Report • {{ now()->format('F j, Y') }}</p>
    </div>
</body>

</html>
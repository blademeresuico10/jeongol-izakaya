<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Menu Performance Report</title>
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
        <div class="company-name">JEONGOL IZAKAYA</div>
        <div class="report-title">MENU PERFORMANCE REPORT</div>
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
            <span class="summary-label">Total Items Sold:</span>
            <span>{{ number_format($totalItemsSold) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Revenue:</span>
            <span>{{ number_format($totalRevenue, 2) }}</span>
        </div>
        @if($bestSelling)
        <div class="summary-row">
            <span class="summary-label">Best Selling Item:</span>
            <span>{{ $bestSelling['menu_item'] }} ({{ $bestSelling['quantity'] }} sold)</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Best Seller Revenue:</span>
            <span>{{ number_format($bestSelling['revenue'], 2) }}</span>
        </div>
        @else
        <div class="summary-row">
            <span class="summary-label">Best Selling Item:</span>
            <span>No sales data available</span>
        </div>
        @endif
    </div>

    <!-- Menu Performance Table -->
    <div class="section-title">Menu Item Performance</div>

    @if($menuItems->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 8%;" class="text-center">Rank</th>
                <th style="width: 52%;">Menu Item</th>
                <th style="width: 20%;" class="text-right">Quantity Sold</th>
                <th style="width: 20%;" class="text-right">Revenue</th>
            </tr>
        </thead>
        <tbody>
            @php
                $hasAnySales = $menuItems->where('quantity', '>', 0)->count() > 0;
            @endphp
            
            @foreach($menuItems as $index => $item)
            <tr>
                <td class="text-center">
                    @if($item['quantity'] > 0)
                        {{ $index + 1 }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    {{ $item['menu_item'] }}
                    @if($item['quantity'] == 0)
                        <span style="font-size: 9px;"> (No sales)</span>
                    @endif
                </td>
                <td class="text-right">
                    {{ number_format($item['quantity']) }}
                </td>
                <td class="text-right">
                    {{ number_format($item['revenue'], 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
        @if($hasAnySales)
        <tfoot>
            <tr class="total-row">
                <td colspan="2" class="text-right"><strong>TOTAL:</strong></td>
                <td class="text-right"><strong>{{ number_format($totalItemsSold) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalRevenue, 2) }}</strong></td>
            </tr>
        </tfoot>
        @endif
    </table>
    @else
    <div class="no-data">
        No menu performance data available for this period.
    </div>
    @endif

    <div class="footer">
        <p>JEONGOL IZAKAYA • Menu Performance Report • {{ now()->format('F j, Y') }}</p>
    </div>
</body>

</html>
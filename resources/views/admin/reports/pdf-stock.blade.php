<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Inventory Report</title>
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

        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 9px;
            border-radius: 3px;
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
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="report-title">
            @if($reportType === 'stock-movement')
                STOCK MOVEMENT REPORT
            @elseif($reportType === 'expired')
                EXPIRED ITEMS REPORT
            @elseif($reportType === 'consumption')
                CONSUMPTION REPORT
            @endif
        </div>
        <div class="company-name">JEONGOL IZAKAYA</div>
        <div class="date-range">
            <strong>Period:</strong> {{ $dateFrom->format('F j, Y') }} - {{ $dateTo->format('F j, Y') }}
        </div>
        <div class="generated-info">
            Generated on: {{ $generatedAt->format('F j, Y g:i A') }}
        </div>
    </div>

    @if($reportType === 'stock-movement')
        @include('admin.reports.partials.pdf-stock-movement', ['data' => $reportData])
    @elseif($reportType === 'expired')
        @include('admin.reports.partials.pdf-expired', ['data' => $reportData])
    @elseif($reportType === 'consumption')
        @include('admin.reports.partials.pdf-consumption', ['data' => $reportData])
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>JEONGOL IZAKAYA • Inventory Report • {{ now()->format('F j, Y') }}</p>
    </div>
</body>

</html>
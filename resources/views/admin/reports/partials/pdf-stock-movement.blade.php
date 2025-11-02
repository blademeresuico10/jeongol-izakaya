<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Stock In Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .report-title { font-size: 18px; font-weight: bold; color: #111; }
        .company-name { font-size: 14px; margin-bottom: 5px; }
        .date-range, .generated-info { font-size: 12px; margin-bottom: 3px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; }
        th { background-color: #f7f7f7; text-align: left; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .section-title { margin-top: 20px; font-weight: bold; font-size: 14px; border-bottom: 1px solid #aaa; }
        .footer { text-align: center; margin-top: 30px; font-size: 11px; color: #777; }
        .summary-box { margin: 10px 0; }
        .summary-row { display: flex; justify-content: space-between; padding: 3px 0; }
        .summary-label { font-weight: bold; }
        .no-data { text-align: center; margin-top: 20px; color: #777; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="report-title">STOCK IN REPORT</div>
        <div class="company-name">JEONGOL IZAKAYA</div>
        <div class="date-range"><strong>Period:</strong> {{ $dateFrom->format('F j, Y') }} - {{ $dateTo->format('F j, Y') }}</div>
        <div class="generated-info">Generated on: {{ $generatedAt->format('F j, Y g:i A') }}</div>
    </div>

    <!-- Summary -->
    <div class="summary-box">
        <div class="summary-row"><span class="summary-label">Stock In (Kg):</span> <span>{{ number_format($reportData['summary']['stock_in_kg'] ?? 0, 2) }}</span></div>
        <div class="summary-row"><span class="summary-label">Stock In (Pcs):</span> <span>{{ number_format($reportData['summary']['stock_in_pcs'] ?? 0, 2) }}</span></div>
    </div>

    <div class="section-title">Stock In History</div>

    @php
        $stockInMovements = collect($reportData['movements'] ?? [])->filter(function($m) {
            return ($m['type'] ?? '') === 'stock_in';
        });
    @endphp

    @if($stockInMovements->isEmpty())
        <div class="no-data">
            No stock in movements for this period.
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>Category</th>
                    <th class="text-right">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockInMovements as $row)
                    <tr>
                        <td>{{ $row['date'] ?? 'N/A' }}</td>
                        <td>{{ $row['category'] ?? 'Unknown' }}</td>
                        <td class="text-right">
                            @if(($row['unit'] ?? 'kg') === 'pieces')
                                {{ number_format($row['quantity'] ?? 0, 0) }}
                            @else
                                {{ number_format($row['quantity'] ?? 0, 2) }}
                            @endif
                            {{ $row['unit'] ?? 'kg' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        JEONGOL IZAKAYA • Stock In Report • {{ now()->format('F j, Y') }}
    </div>
</body>
</html>
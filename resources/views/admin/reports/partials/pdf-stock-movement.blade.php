<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Stock Movement Report</title>
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
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="report-title">STOCK MOVEMENT REPORT</div>
        <div class="company-name">JEONGOL IZAKAYA</div>
        <div class="date-range"><strong>Period:</strong> {{ $dateFrom->format('F j, Y') }} - {{ $dateTo->format('F j, Y') }}</div>
        <div class="generated-info">Generated on: {{ $generatedAt->format('F j, Y g:i A') }}</div>
    </div>

    <!-- Summary -->
    <div class="summary-box">
        <div class="summary-row"><span class="summary-label">Stock In (Kg):</span> <span>{{ number_format($reportData['summary']['stock_in_kg'] ?? 0, 2) }}</span></div>
        <div class="summary-row"><span class="summary-label">Stock In (Pcs):</span> <span>{{ number_format($reportData['summary']['stock_in_pcs'] ?? 0, 2) }}</span></div>
        <div class="summary-row"><span class="summary-label">Stock Out (Kg):</span> <span>{{ number_format($reportData['summary']['stock_out_kg'] ?? 0, 2) }}</span></div>
        <div class="summary-row"><span class="summary-label">Stock Out (Pcs):</span> <span>{{ number_format($reportData['summary']['stock_out_pcs'] ?? 0, 2) }}</span></div>
    </div>

    <div class="section-title">Movement Summary by Category</div>

    @php
        $grouped = [];
        foreach ($reportData['movements'] ?? [] as $m) {
            $category = $m['category'] ?? 'Unknown';
            $type = $m['type'] ?? 'N/A';
            $unit = $m['unit'] ?? 'kg';
            $key = $category . '-' . $type;

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'category' => $category,
                    'type' => $type,
                    'totalQty' => 0,
                    'unit' => $unit,
                ];
            }
            $grouped[$key]['totalQty'] += $m['quantity'] ?? 0;
        }

        $grouped = collect($grouped)->sortBy(['category', 'type']);
    @endphp

    @if($grouped->isEmpty())
        <div class="no-data" style="text-align:center; margin-top:20px; color:#777;">
            No stock movements for this period.
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th class="text-center">Type</th>
                    <th class="text-right">Total Quantity</th>
                    <th class="text-center">Unit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grouped as $row)
                    @php
                        switch ($row['type']) {
                            case 'stock_in': $label = 'Stock In'; break;
                            case 'stock_out': $label = 'Stock Out'; break;
                            case 'used': $label = 'Used'; break;
                            case 'expired': $label = 'Expired'; break;
                            default: $label = ucfirst($row['type']);
                        }
                    @endphp
                    <tr>
                        <td>{{ $row['category'] }}</td>
                        <td class="text-center">{{ $label }}</td>
                        <td class="text-right">{{ number_format($row['totalQty'], 2) }}</td>
                        <td class="text-center">{{ $row['unit'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        JEONGOL IZAKAYA • Stock Movement Report • {{ now()->format('F j, Y') }}
    </div>
</body>
</html>

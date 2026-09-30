<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Z Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 20px; text-transform: uppercase; }
        .meta { margin-bottom: 20px; font-size: 11px; }
        .meta table { width: 100%; border-collapse: collapse; }
        .meta td { padding: 4px 0; }
        .section-title { font-size: 13px; font-weight: bold; background: #f2f2f2; padding: 5px 8px; margin-top: 15px; margin-bottom: 8px; border-left: 3px solid #333; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .table th, .table td { padding: 6px 8px; border: 1px solid #ddd; text-align: left; }
        .table th { background-color: #f9f9f9; font-weight: bold; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #777; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Z REPORT</h1>
        <p style="margin: 5px 0 0 0; color: #666;">End of Day / Period Financial Summary</p>
    </div>

    <div class="meta">
        <table>
            <tr>
                <td><strong>Date From:</strong> {{ $filters['date_from'] ?? 'All' }}</td>
                <td><strong>Date To:</strong> {{ $filters['date_to'] ?? 'All' }}</td>
                <td class="text-right"><strong>Generated At:</strong> {{ now()->format('Y-m-d H:i:s') }}</td>
            </tr>
        </table>
    </div>

    @php
        $summary = $reportData['summary'];
        $payments = $reportData['payments'];
        $taxBreakdown = $reportData['taxBreakdown'];
    @endphp

    <div class="section-title">1. Sales Summary</div>
    <table class="table">
        <tr>
            <td>Gross Sales (Untaxed)</td>
            <td class="text-right">${{ number_format($summary['gross_sales_untaxed'], 2) }}</td>
        </tr>
        <tr>
            <td>Gross Tax Collected</td>
            <td class="text-right">${{ number_format($summary['gross_sales_tax'], 2) }}</td>
        </tr>
        <tr class="font-bold">
            <td>Gross Sales Total ({{ $summary['total_invoices_count'] }} Invoices)</td>
            <td class="text-right">${{ number_format($summary['gross_sales_total'], 2) }}</td>
        </tr>
        <tr>
            <td>Less: Refunds / Returns ({{ $summary['total_refunds_count'] }} Credit Notes)</td>
            <td class="text-right">-${{ number_format($summary['refunds_total'], 2) }}</td>
        </tr>
        <tr class="font-bold" style="background-color: #eef2ff;">
            <td>NET SALES TOTAL</td>
            <td class="text-right">${{ number_format($summary['net_sales_total'], 2) }}</td>
        </tr>
    </table>

    <div class="section-title">2. Tax Breakdown</div>
    <table class="table">
        <thead>
            <tr>
                <th>Tax Description</th>
                <th class="text-right">Tax Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($taxBreakdown as $tax)
                <tr>
                    <td>{{ $tax['name'] }}</td>
                    <td class="text-right">${{ number_format($tax['amount'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="text-align: center; color: #888;">No tax records recorded for selected period.</td>
                </tr>
            @endforelse
            <tr class="font-bold">
                <td>Total Net Tax</td>
                <td class="text-right">${{ number_format($summary['net_sales_tax'], 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">3. Payments Collected by Journal / Method</div>
    <table class="table">
        <thead>
            <tr>
                <th>Payment Journal</th>
                <th class="text-right">Total Collected</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $pay)
                <tr>
                    <td>{{ $pay['journal'] }}</td>
                    <td class="text-right">${{ number_format($pay['amount'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="text-align: center; color: #888;">No payments collected for selected period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Z Report generated automatically by Nuvis ERP X.
    </div>
</body>
</html>

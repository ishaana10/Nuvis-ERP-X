<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice Report - {{ now()->format('Y-m-d') }}</title>
    <style>
        html, body, table, th, td, div, span, p, b, strong {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif !important;
        }

        @page {
            margin: 1cm 1cm;
            size: A4 landscape;
        }

        body {
            font-size: 10pt;
            color: #1f2937;
            line-height: 1.3;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #1f2937;
            padding-bottom: 8px;
        }

        .header h1 {
            margin: 0;
            font-size: 16pt;
            font-weight: bold;
            color: #111827;
        }

        .header p {
            margin: 4px 0 0 0;
            font-size: 9pt;
            color: #6b7280;
        }

        .summary-box {
            width: 100%;
            margin-bottom: 15px;
            border: 1px solid #d1d5db;
            background-color: #f9fafb;
            padding: 8px;
            border-radius: 4px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            width: 25%;
            text-align: center;
            border: none;
            padding: 4px;
        }

        .summary-title {
            font-size: 8pt;
            color: #6b7280;
            text-transform: uppercase;
        }

        .summary-value {
            font-size: 12pt;
            font-weight: bold;
            color: #111827;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table.data-table th {
            background-color: #f3f4f6;
            padding: 6px 4px;
            font-weight: bold;
            font-size: 8pt;
            text-transform: uppercase;
            color: #4b5563;
            border-bottom: 2px solid #d1d5db;
            text-align: left;
        }

        table.data-table th.right, table.data-table td.right {
            text-align: right;
        }

        table.data-table th.center, table.data-table td.center {
            text-align: center;
        }

        table.data-table td {
            padding: 5px 4px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 8pt;
        }

        .group-header {
            background-color: #e5e7eb;
            font-weight: bold;
            font-size: 9pt;
        }

        .total-row {
            font-weight: bold;
            background-color: #f3f4f6;
            border-top: 2px solid #374151;
            font-size: 9pt;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    @php
        $invoices = $reportData['invoices'];
        $groupedData = $reportData['groupedData'];
        $groupBy = $reportData['groupBy'];
        $stats = $reportData['stats'];
    @endphp

    <div class="header">
        <h1>Invoice Report</h1>
        <p>Generated on {{ now()->format('F j, Y, g:i A') }}</p>
    </div>

    <div class="summary-box">
        <table class="summary-table">
            <tr>
                <td>
                    <div class="summary-title">Total Invoices</div>
                    <div class="summary-value">{{ number_format($stats['total_count']) }}</div>
                </td>
                <td>
                    <div class="summary-title">Total Invoiced</div>
                    <div class="summary-value">${{ number_format($stats['total_amount'], 2) }}</div>
                </td>
                <td>
                    <div class="summary-title">Total Paid</div>
                    <div class="summary-value" style="color: #059669;">${{ number_format($stats['total_paid'], 2) }}</div>
                </td>
                <td>
                    <div class="summary-title">Outstanding Balance</div>
                    <div class="summary-value" style="color: #d97706;">${{ number_format($stats['total_residual'], 2) }}</div>
                </td>
            </tr>
        </table>
    </div>

    @if($invoices->isEmpty())
        <div style="text-align: center; padding: 20px; color: #6b7280;">
            No invoices match the selected report criteria.
        </div>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Invoice Date</th>
                    <th>Due Date</th>
                    <th>Type</th>
                    <th class="right">Total Amount</th>
                    <th class="right">Paid Amount</th>
                    <th class="right">Outstanding</th>
                    <th class="center">Payment State</th>
                    <th class="center">Status</th>
                </tr>
            </thead>
            <tbody>
                @if($groupBy !== 'none' && !empty($groupedData))
                    @foreach($groupedData as $groupName => $groupInvoices)
                        @php
                            $groupTotal = $groupInvoices->sum('amount_total');
                            $groupResidual = $groupInvoices->sum('amount_residual');
                            $groupPaid = $groupTotal - $groupResidual;
                        @endphp
                        <tr class="group-header">
                            <td colspan="5">{{ $groupName }} ({{ $groupInvoices->count() }} invoices)</td>
                            <td class="right">${{ number_format($groupTotal, 2) }}</td>
                            <td class="right">${{ number_format($groupPaid, 2) }}</td>
                            <td class="right">${{ number_format($groupResidual, 2) }}</td>
                            <td colspan="2"></td>
                        </tr>
                        @foreach($groupInvoices as $invoice)
                            @php
                                $paidAmount = $invoice->amount_total - $invoice->amount_residual;
                            @endphp
                            <tr>
                                <td>{{ $invoice->name ?: '#' . $invoice->id }}</td>
                                <td>{{ $invoice->partner?->name ?? 'N/A' }}</td>
                                <td>{{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') : 'N/A' }}</td>
                                <td>{{ $invoice->invoice_date_due ? \Carbon\Carbon::parse($invoice->invoice_date_due)->format('Y-m-d') : 'N/A' }}</td>
                                <td>{{ $invoice->move_type?->getLabel() ?? $invoice->move_type }}</td>
                                <td class="right">${{ number_format($invoice->amount_total, 2) }}</td>
                                <td class="right">${{ number_format($paidAmount, 2) }}</td>
                                <td class="right">${{ number_format($invoice->amount_residual, 2) }}</td>
                                <td class="center">{{ $invoice->payment_state?->getLabel() ?? 'N/A' }}</td>
                                <td class="center">{{ $invoice->state?->getLabel() ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @else
                    @foreach($invoices as $invoice)
                        @php
                            $paidAmount = $invoice->amount_total - $invoice->amount_residual;
                        @endphp
                        <tr>
                            <td>{{ $invoice->name ?: '#' . $invoice->id }}</td>
                            <td>{{ $invoice->partner?->name ?? 'N/A' }}</td>
                            <td>{{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') : 'N/A' }}</td>
                            <td>{{ $invoice->invoice_date_due ? \Carbon\Carbon::parse($invoice->invoice_date_due)->format('Y-m-d') : 'N/A' }}</td>
                            <td>{{ $invoice->move_type?->getLabel() ?? $invoice->move_type }}</td>
                            <td class="right">${{ number_format($invoice->amount_total, 2) }}</td>
                            <td class="right">${{ number_format($paidAmount, 2) }}</td>
                            <td class="right">${{ number_format($invoice->amount_residual, 2) }}</td>
                            <td class="center">{{ $invoice->payment_state?->getLabel() ?? 'N/A' }}</td>
                            <td class="center">{{ $invoice->state?->getLabel() ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                @endif
                <tr class="total-row">
                    <td colspan="5">Grand Total ({{ $stats['total_count'] }} invoices)</td>
                    <td class="right">${{ number_format($stats['total_amount'], 2) }}</td>
                    <td class="right">${{ number_format($stats['total_paid'], 2) }}</td>
                    <td class="right">${{ number_format($stats['total_residual'], 2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
    @endif

    <div class="footer">
        Nuvis ERP X — Invoice Report
    </div>
</body>
</html>

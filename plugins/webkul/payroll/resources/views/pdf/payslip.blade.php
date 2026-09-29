<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip - {{ $payslip->number ?? $payslip->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; line-height: 1.4; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #0070F2; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 20px; color: #0070F2; }
        .header p { margin: 2px 0; color: #666; }
        .details-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .details-table td { padding: 6px; vertical-align: top; }
        .details-table td.label { font-weight: bold; width: 20%; color: #555; }
        .lines-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .lines-table th, .lines-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .lines-table th { background-color: #f5f5f5; font-weight: bold; }
        .lines-table td.amount { text-align: right; }
        .summary-box { width: 40%; float: right; margin-top: 10px; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { padding: 6px; }
        .summary-table td.label { font-weight: bold; text-align: left; }
        .summary-table td.val { text-align: right; font-weight: bold; }
        .summary-table tr.total td { font-size: 14px; color: #0070F2; border-top: 2px solid #0070F2; }
        .clear { clear: both; }
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #888; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $payslip->company?->name ?? 'Company Payslip' }}</h1>
        <p>Payslip #{{ $payslip->number ?? $payslip->id }} | Period: {{ $payslip->start_date?->format('Y-m-d') }} to {{ $payslip->end_date?->format('Y-m-d') }}</p>
    </div>

    <table class="details-table">
        <tr>
            <td class="label">Employee:</td>
            <td>{{ $payslip->employee?->name }} (ID: {{ $payslip->employee?->id }})</td>
            <td class="label">Contract:</td>
            <td>{{ $payslip->contract?->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Department:</td>
            <td>{{ $payslip->employee?->department?->name ?? 'N/A' }}</td>
            <td class="label">Job Title:</td>
            <td>{{ $payslip->employee?->job_title ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Status:</td>
            <td>{{ strtoupper($payslip->state?->value ?? $payslip->state) }}</td>
            <td class="label">Paid Date:</td>
            <td>{{ $payslip->paid_date?->format('Y-m-d') ?? 'Pending' }}</td>
        </tr>
    </table>

    <h3>Salary Computation Details</h3>
    <table class="lines-table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Name / Category</th>
                <th style="text-align: right;">Quantity</th>
                <th style="text-align: right;">Rate (%)</th>
                <th style="text-align: right;">Amount</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payslip->lines as $line)
                <tr>
                    <td>{{ $line->code }}</td>
                    <td>{{ $line->name }}</td>
                    <td class="amount">{{ number_format($line->quantity, 2) }}</td>
                    <td class="amount">{{ number_format($line->rate, 2) }}%</td>
                    <td class="amount">{{ number_format($line->amount, 2) }}</td>
                    <td class="amount">{{ number_format($line->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        <table class="summary-table">
            <tr>
                <td class="label">Basic Salary:</td>
                <td class="val">{{ number_format($payslip->basic_wage, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Gross Wage:</td>
                <td class="val">{{ number_format($payslip->gross_wage, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Total Deductions:</td>
                <td class="val">-{{ number_format($payslip->total_deductions, 2) }}</td>
            </tr>
            <tr class="total">
                <td class="label">Net Payable Salary:</td>
                <td class="val">{{ number_format($payslip->net_wage, 2) }}</td>
            </tr>
            @if($payslip->total_employer_contributions > 0)
            <tr>
                <td class="label" style="font-size: 11px; color: #666;">Employer Contributions:</td>
                <td class="val" style="font-size: 11px; color: #666;">{{ number_format($payslip->total_employer_contributions, 2) }}</td>
            </tr>
            @endif
        </table>
    </div>
    <div class="clear"></div>

    <div class="footer">
        This is a computer generated document. Generated on {{ now()->format('Y-m-d H:i') }}.
    </div>
</body>
</html>

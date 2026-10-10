<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Salary Slip - {{ $slip->employee_name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; margin: 20px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #2563eb; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #1e40af; }
        .section { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px 12px; text-align: left; }
        th { background-color: #f3f4f6; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #f8fafc; }
    </style>
</head>
<body>
    <div class="header">
        <h2>FIJI SALARY SLIP</h2>
        <p>{{ $slip->company?->name ?? 'Nuvis ERP X' }} | Pay Period: {{ $slip->payrollRun?->period_start?->format('d M Y') }} - {{ $slip->payrollRun?->period_end?->format('d M Y') }}</p>
    </div>

    <div class="section">
        <h3>Employee Details</h3>
        <table>
            <tr>
                <td><strong>Employee Name:</strong> {{ $slip->employee_name }}</td>
                <td><strong>Pay Frequency:</strong> {{ ucfirst($slip->pay_frequency?->value ?? 'monthly') }}</td>
            </tr>
            <tr>
                <td><strong>Fiji Resident:</strong> {{ $slip->is_resident ? 'Yes' : 'No' }}</td>
                <td><strong>Pay Run Title:</strong> {{ $slip->payrollRun?->title }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>Earnings & Deductions Summary</h3>
        <table>
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th class="text-right">Earnings ($)</th>
                    <th class="text-right">Deductions ($)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Basic Salary</td>
                    <td class="text-right">{{ number_format($slip->basic_salary, 2) }}</td>
                    <td class="text-right">-</td>
                </tr>
                <tr>
                    <td>Overtime</td>
                    <td class="text-right">{{ number_format($slip->overtime, 2) }}</td>
                    <td class="text-right">-</td>
                </tr>
                <tr>
                    <td>Allowances</td>
                    <td class="text-right">{{ number_format($slip->allowances, 2) }}</td>
                    <td class="text-right">-</td>
                </tr>
                <tr>
                    <td>FNPF Employee Contribution (8%)</td>
                    <td class="text-right">-</td>
                    <td class="text-right">{{ number_format($slip->fnpf_employee, 2) }}</td>
                </tr>
                <tr>
                    <td>PAYE Income Tax</td>
                    <td class="text-right">-</td>
                    <td class="text-right">{{ number_format($slip->paye_tax, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Gross Pay / Total Deductions</td>
                    <td class="text-right">${{ number_format($slip->gross_pay, 2) }}</td>
                    <td class="text-right">${{ number_format($slip->fnpf_employee + $slip->paye_tax, 2) }}</td>
                </tr>
                <tr class="total-row" style="background-color:#e0f2fe;">
                    <td>NET PAYABLE AMOUNT</td>
                    <td colspan="2" class="text-right" style="font-size:16px;color:#0369a1;">${{ number_format($slip->net_pay, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>Employer Contributions & Statutory Levies</h3>
        <table>
            <tr>
                <td>FNPF Employer Contribution (8%)</td>
                <td class="text-right">${{ number_format($slip->fnpf_employer, 2) }}</td>
            </tr>
            <tr>
                <td>WorkCare Levy (1%)</td>
                <td class="text-right">${{ number_format($slip->workcare_levy, 2) }}</td>
            </tr>
            <tr>
                <td>FNAP Training Levy (1%)</td>
                <td class="text-right">${{ number_format($slip->training_levy, 2) }}</td>
            </tr>
        </table>
    </div>
</body>
</html>

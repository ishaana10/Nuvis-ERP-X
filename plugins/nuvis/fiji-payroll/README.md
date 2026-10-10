# Fiji Payroll Plugin for Nuvis ERP X

Fiji tax, FNPF, WorkCare, and Training levy compliance extension for Nuvis ERP X.

## Features
- **FNPF Calculation**: 8% employee contribution and 8% employer contribution.
- **PAYE Tax Engine**: Annualized Fiji resident progressive tax brackets ($0-$30,000 @ 0%, $30,001-$50,000 @ 18%, $50,001-$270,000 @ $3,600 + 20%, >$270,000 progressive) and flat 20% non-resident rate.
- **Pay Frequencies**: Supports Monthly, Fortnightly, and Weekly pay runs.
- **Levies & Costs**: WorkCare levy, National Training and Productivity (FNAP) levy, and total employer cost tracking.
- **Filament Integration**: Dedicated `PayrollRunResource` for managing pay runs and generating salary slips.

## Usage
Run calculations programmatically:
```php
use Nuvis\FijiPayroll\Enums\PayFrequency;
use Nuvis\FijiPayroll\Services\PayrollCalculator;

$result = app(PayrollCalculator::class)->calculate([
    'basic'       => 3000,
    'overtime'    => 250,
    'allowances'  => 150,
    'is_resident' => true,
    'frequency'   => PayFrequency::Monthly,
]);
```

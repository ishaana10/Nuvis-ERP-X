<?php

use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
use Nuvis\FijiPayroll\Enums\PayFrequency;
use Nuvis\FijiPayroll\Enums\PayrollStatus;
use Nuvis\FijiPayroll\Filament\Resources\PayrollRunResource\Pages\CreatePayrollRun;
use Nuvis\FijiPayroll\Filament\Resources\PayrollRunResource\Pages\ListPayrollRuns;
use Nuvis\FijiPayroll\Models\PayrollRun;
use Nuvis\FijiPayroll\Models\SalarySlip;
use Nuvis\FijiPayroll\Services\FnpfService;
use Nuvis\FijiPayroll\Services\PayeCalculator;
use Nuvis\FijiPayroll\Services\PayrollCalculator;

require_once __DIR__.'/../../../../webkul/support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../webkul/support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensureERPInstalled();
    config()->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
    Artisan::call('migrate', [
        '--path'     => 'plugins/nuvis/fiji-payroll/database/migrations',
        '--realpath' => true,
    ]);
});

it('calculates FNPF contributions accurately at 8% rate', function () {
    $fnpf = new FnpfService;

    $wage = $fnpf->getEligibleWageBase(2000, 200, 100);
    expect($wage)->toBe(2300.0);

    expect($fnpf->calculateEmployeeContribution(2300))->toBe(184.0);
    expect($fnpf->calculateEmployerContribution(2300))->toBe(184.0);
});

it('calculates PAYE tax correctly for resident progressive brackets across frequencies', function () {
    $paye = new PayeCalculator;

    // Monthly resident: $3000 taxable income ($36,000 annual => ($36,000-$30,000)*0.18 = $1,080 / 12 = $90)
    $monthlyTax = $paye->calculateTax(3000, true, PayFrequency::Monthly);
    expect($monthlyTax)->toBe(90.0);

    // Fortnightly resident: $1500 taxable income ($39,000 annual => ($39,000-$30,000)*0.18 = $1,620 / 26 = $62.31)
    $fortnightlyTax = $paye->calculateTax(1500, true, PayFrequency::Fortnightly);
    expect($fortnightlyTax)->toBe(62.31);

    // Non-resident flat 20%
    $nonResidentTax = $paye->calculateTax(3000, false, PayFrequency::Monthly);
    expect($nonResidentTax)->toBe(600.0);
});

it('orchestrates complete Fiji payroll breakdown via PayrollCalculator', function () {
    $calc = app(PayrollCalculator::class)->calculate([
        'basic'       => 3000,
        'overtime'    => 250,
        'allowances'  => 150,
        'is_resident' => true,
        'frequency'   => PayFrequency::Monthly,
    ]);

    expect($calc['gross'])->toBe(3400.0);
    expect($calc['fnpf_employee'])->toBe(272.0);
    expect($calc['fnpf_employer'])->toBe(272.0);
    expect($calc['taxable_income'])->toBe(3128.0);
    expect($calc['paye'])->toBe(113.04);
    expect($calc['net_pay'])->toBe(3014.96);
    expect($calc['workcare_levy'])->toBe(34.0);
    expect($calc['training_levy'])->toBe(34.0);
    expect($calc['total_employer_cost'])->toBe(3740.0);
});

it('automatically calculates salary slip amounts and updates payroll run totals', function () {
    $run = PayrollRun::create([
        'title'        => 'Test August Run',
        'period_start' => '2026-08-01',
        'period_end'   => '2026-08-31',
        'pay_frequency'=> PayFrequency::Monthly,
        'status'       => PayrollStatus::Draft,
    ]);

    $slip = SalarySlip::create([
        'payroll_run_id' => $run->id,
        'employee_name'  => 'John Doe',
        'is_resident'    => true,
        'basic_salary'   => 3000,
        'overtime'       => 200,
        'allowances'     => 100,
    ]);

    expect((float) $slip->gross_pay)->toBe(3300.0);
    expect((float) $slip->fnpf_employee)->toBe(264.0);
    expect((float) $slip->net_pay)->toBeGreaterThan(0);

    $run->refresh();
    expect((float) $run->total_gross)->toBe(3300.0);
    expect($run->status)->toBe(PayrollStatus::Calculated);
});

it('can render Fiji PayrollRunResource pages in Filament', function () {
    $user = SecurityHelper::authenticateWithPermissions(['page_fiji_payroll_run'], true);
    $this->actingAs($user);

    Livewire::test(ListPayrollRuns::class)
        ->assertSuccessful();

    Livewire::test(CreatePayrollRun::class)
        ->assertSuccessful();
});

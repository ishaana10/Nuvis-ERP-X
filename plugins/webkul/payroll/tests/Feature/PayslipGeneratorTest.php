<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

use Webkul\Employee\Models\Employee;
use Webkul\Payroll\Enums\AmountType;
use Webkul\Payroll\Enums\ContractState;
use Webkul\Payroll\Enums\RuleCategory;
use Webkul\Payroll\Models\EmployeeContract;
use Webkul\Payroll\Models\PayrollPeriod;
use Webkul\Payroll\Models\PayrollRun;
use Webkul\Payroll\Models\PayrollStructure;
use Webkul\Payroll\Models\SalaryRule;
use Webkul\Payroll\Services\PayslipGenerator;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    TestBootstrapHelper::ensurePluginInstalled('payroll');
});

it('generates payslips for all active contracts in a payroll run', function () {
    $employee = Employee::create([
        'name'       => 'Jane Smith Gen',
        'work_email' => 'jane.gen@example.com',
    ]);

    $structure = PayrollStructure::create([
        'name'      => 'Standard Structure Gen',
        'code'      => 'STD_GEN',
        'is_active' => true,
    ]);

    SalaryRule::create([
        'structure_id'           => $structure->id,
        'name'                   => 'Basic Salary',
        'code'                   => 'BASIC',
        'category'               => RuleCategory::BASIC,
        'sequence'               => 1,
        'amount_type'            => AmountType::PERCENTAGE,
        'amount_percentage'      => 100,
        'amount_percentage_base' => 'wage',
    ]);

    EmployeeContract::create([
        'name'         => 'Jane Contract Gen',
        'employee_id'  => $employee->id,
        'structure_id' => $structure->id,
        'wage'         => 4000,
        'state'        => ContractState::OPEN,
        'start_date'   => now()->toDateString(),
    ]);

    $period = PayrollPeriod::create([
        'name'       => 'January 2026 Gen',
        'start_date' => '2026-01-01',
        'end_date'   => '2026-01-31',
    ]);

    $run = PayrollRun::create([
        'name'      => 'Run Jan 2026 Gen',
        'period_id' => $period->id,
    ]);

    $generator = new PayslipGenerator;
    $payslips = $generator->generateForRun($run);

    expect($payslips->count())->toBeGreaterThanOrEqual(1);
    $payslip = $payslips->where('employee_id', $employee->id)->first();
    expect($payslip)->not->toBeNull();
    expect((float) $payslip->basic_wage)->toBe(4000.0);
    expect((float) $payslip->net_wage)->toBe(4000.0);
    expect($payslip->lines->count())->toBe(1);
});

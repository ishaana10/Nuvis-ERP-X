<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

use Webkul\Employee\Models\Employee;
use Webkul\Payroll\Enums\AmountType;
use Webkul\Payroll\Enums\RuleCategory;
use Webkul\Payroll\Models\EmployeeContract;
use Webkul\Payroll\Models\PayrollStructure;
use Webkul\Payroll\Models\Payslip;
use Webkul\Payroll\Models\SalaryRule;
use Webkul\Payroll\Services\PayrollCalculator;
use Webkul\Payroll\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    TestBootstrapHelper::ensurePluginInstalled('payroll');
});

it('calculates basic, gross, deductions, and net wage correctly', function () {
    $employee = Employee::create([
        'name'       => 'John Doe Calculator',
        'work_email' => 'john.calc@example.com',
    ]);

    $structure = PayrollStructure::create([
        'name'      => 'Standard Structure Calc',
        'code'      => 'STD_CALC',
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

    SalaryRule::create([
        'structure_id' => $structure->id,
        'name'         => 'Housing Allowance',
        'code'         => 'HOU',
        'category'     => RuleCategory::ALLOWANCE,
        'sequence'     => 10,
        'amount_type'  => AmountType::FIXED,
        'amount_fix'   => 500,
    ]);

    SalaryRule::create([
        'structure_id'           => $structure->id,
        'name'                   => 'Income Tax',
        'code'                   => 'TAX',
        'category'               => RuleCategory::TAX,
        'sequence'               => 100,
        'amount_type'            => AmountType::PERCENTAGE,
        'amount_percentage'      => 10,
        'amount_percentage_base' => 'gross',
    ]);

    $contract = EmployeeContract::create([
        'name'         => 'John Contract Calc',
        'employee_id'  => $employee->id,
        'structure_id' => $structure->id,
        'wage'         => 5000,
        'start_date'   => now()->toDateString(),
    ]);

    $payslip = Payslip::create([
        'name'        => 'Test Payslip Calc',
        'employee_id' => $employee->id,
        'contract_id' => $contract->id,
        'start_date'  => now()->startOfMonth()->toDateString(),
        'end_date'    => now()->endOfMonth()->toDateString(),
    ]);

    $calculator = new PayrollCalculator;
    $result = $calculator->calculate($payslip);

    expect((float) $result['basic_wage'])->toBe(5000.0);
    expect((float) $result['gross_wage'])->toBe(5500.0);
    expect((float) $result['total_deductions'])->toBe(550.0);
    expect((float) $result['net_wage'])->toBe(4950.0);
    expect(count($result['lines']))->toBe(3);
});

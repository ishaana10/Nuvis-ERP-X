<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Employee\Models\Employee;
use Webkul\Payroll\Enums\PayslipState;
use Webkul\Payroll\Models\EmployeeContract;
use Webkul\Payroll\Models\Payslip;
use Webkul\Payroll\Services\AccountingPoster;
use Webkul\Payroll\Settings\PayrollSettings;
use Webkul\Support\Models\Company;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    TestBootstrapHelper::ensurePluginInstalled('payroll');
});

it('creates posted journal entry for confirmed payslip', function () {
    $company = Company::first() ?? Company::create(['name' => 'Payroll Company']);

    $journal = Journal::create([
        'name'       => 'Payroll Journal Post',
        'code'       => 'PAYPOST',
        'company_id' => $company->id,
        'type'       => JournalType::GENERAL,
    ]);

    $salaryAccount = Account::create([
        'name'         => 'Salary Expense Post',
        'code'         => '600000POST',
        'account_type' => AccountType::EXPENSE,
    ]);

    $payableAccount = Account::create([
        'name'         => 'Employee Payable Post',
        'code'         => '200000POST',
        'account_type' => AccountType::LIABILITY_PAYABLE,
    ]);

    $settings = app(PayrollSettings::class);
    $settings->default_journal_id = $journal->id;
    $settings->default_salary_expense_account_id = $salaryAccount->id;
    $settings->default_employee_payable_account_id = $payableAccount->id;
    $settings->save();

    $employee = Employee::create([
        'name'       => 'Mark Post',
        'work_email' => 'mark.post@example.com',
        'company_id' => $company->id,
    ]);

    $contract = EmployeeContract::create([
        'name'        => 'Mark Contract Post',
        'employee_id' => $employee->id,
        'journal_id'  => $journal->id,
        'company_id'  => $company->id,
        'wage'        => 3000,
        'start_date'  => now()->toDateString(),
    ]);

    $payslip = Payslip::create([
        'name'                         => 'Mark Payslip Post',
        'employee_id'                  => $employee->id,
        'contract_id'                  => $contract->id,
        'company_id'                   => $company->id,
        'basic_wage'                   => 3000,
        'gross_wage'                   => 3000,
        'net_wage'                     => 3000,
        'total_deductions'             => 0,
        'total_employer_contributions' => 0,
        'start_date'                   => now()->startOfMonth()->toDateString(),
        'end_date'                     => now()->endOfMonth()->toDateString(),
    ]);

    $poster = new AccountingPoster;
    $move = $poster->postPayslip($payslip);

    expect($move)->not->toBeNull();
    expect($payslip->fresh()->state)->toBe(PayslipState::CONFIRMED);
    expect($move->state)->toBe(MoveState::POSTED);
    expect($move->lines->count())->toBe(2);
});

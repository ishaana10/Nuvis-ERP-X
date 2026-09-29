<?php

namespace Webkul\Payroll\Settings;

use Spatie\LaravelSettings\Settings;

class PayrollSettings extends Settings
{
    public ?int $default_journal_id;

    public ?int $default_salary_expense_account_id;

    public ?int $default_employee_payable_account_id;

    public ?int $default_tax_payable_account_id;

    public ?int $default_employer_social_account_id;

    public ?int $default_structure_id;

    public static function group(): string
    {
        return 'payroll';
    }
}

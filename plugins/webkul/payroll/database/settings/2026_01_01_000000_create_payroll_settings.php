<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('payroll.default_journal_id', null);
        $this->migrator->add('payroll.default_salary_expense_account_id', null);
        $this->migrator->add('payroll.default_employee_payable_account_id', null);
        $this->migrator->add('payroll.default_tax_payable_account_id', null);
        $this->migrator->add('payroll.default_employer_social_account_id', null);
        $this->migrator->add('payroll.default_structure_id', null);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('payroll.default_journal_id');
        $this->migrator->deleteIfExists('payroll.default_salary_expense_account_id');
        $this->migrator->deleteIfExists('payroll.default_employee_payable_account_id');
        $this->migrator->deleteIfExists('payroll.default_tax_payable_account_id');
        $this->migrator->deleteIfExists('payroll.default_employer_social_account_id');
        $this->migrator->deleteIfExists('payroll.default_structure_id');
    }
};

<?php

namespace Webkul\Payroll\Filament\Admin\Pages;

use Filament\Forms\Components\Select;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Payroll\Models\PayrollStructure;
use Webkul\Payroll\Settings\PayrollSettings;

class PayrollSettingsPage extends SettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|\UnitEnum|null $navigationGroup = 'Payroll';

    protected static ?string $title = 'Payroll Settings';

    protected static ?int $navigationSort = 9;

    protected static string $settings = PayrollSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('default_journal_id')
                    ->label('Default Payroll Journal')
                    ->options(fn () => Journal::pluck('name', 'id'))
                    ->searchable(),
                Select::make('default_salary_expense_account_id')
                    ->label('Salary Expense Account')
                    ->options(fn () => Account::pluck('name', 'id'))
                    ->searchable(),
                Select::make('default_employee_payable_account_id')
                    ->label('Employee Payable Account')
                    ->options(fn () => Account::pluck('name', 'id'))
                    ->searchable(),
                Select::make('default_tax_payable_account_id')
                    ->label('Tax & Statutory Liabilities Account')
                    ->options(fn () => Account::pluck('name', 'id'))
                    ->searchable(),
                Select::make('default_employer_social_account_id')
                    ->label('Employer Social Expense Account')
                    ->options(fn () => Account::pluck('name', 'id'))
                    ->searchable(),
                Select::make('default_structure_id')
                    ->label('Default Salary Structure')
                    ->options(fn () => PayrollStructure::pluck('name', 'id'))
                    ->searchable(),
            ]);
    }
}

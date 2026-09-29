<?php

namespace Webkul\Payroll;

use Filament\Panel;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class PayrollServiceProvider extends PackageServiceProvider
{
    public static string $name = 'payroll';

    public static string $viewNamespace = 'payroll';

    public function configureCustomPackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                '2026_01_01_000001_create_payroll_contribution_registers_table',
                '2026_01_01_000002_create_payroll_structures_table',
                '2026_01_01_000003_create_salary_rules_table',
                '2026_01_01_000004_create_employee_contracts_table',
                '2026_01_01_000005_create_payroll_periods_table',
                '2026_01_01_000006_create_payroll_runs_table',
                '2026_01_01_000007_create_payslips_table',
                '2026_01_01_000008_create_payslip_lines_table',
            ])
            ->runsMigrations()
            ->hasSettings([
                '2026_01_01_000000_create_payroll_settings',
            ])
            ->runsSettings()
            ->hasDependencies([
                'employees',
                'time-off',
                'timesheets',
                'accounts',
            ])
            ->hasSeeder('Webkul\\Payroll\\Database\\Seeders\\PayrollSeeder')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->installDependencies()
                    ->runsMigrations()
                    ->runsSeeders();
            })
            ->hasUninstallCommand(function ($command) {})
            ->icon('payroll');
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(PayrollPlugin::make());
        });
    }

    public function packageBooted(): void
    {
        //
    }
}

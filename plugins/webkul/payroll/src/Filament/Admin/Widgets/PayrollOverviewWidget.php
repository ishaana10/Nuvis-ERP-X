<?php

namespace Webkul\Payroll\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Payroll\Enums\ContractState;
use Webkul\Payroll\Enums\PayslipState;
use Webkul\Payroll\Models\EmployeeContract;
use Webkul\Payroll\Models\PayrollRun;
use Webkul\Payroll\Models\Payslip;

class PayrollOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $activeContracts = EmployeeContract::where('state', ContractState::OPEN)->count();
        $pendingPayslips = Payslip::where('state', PayslipState::DRAFT)->count();
        $totalPayrollCost = Payslip::where('state', '!=', PayslipState::CANCELLED)->sum('gross_wage');
        $payrollRuns = PayrollRun::count();

        return [
            Stat::make('Active Contracts', $activeContracts)
                ->icon('heroicon-o-document-text')
                ->color('success'),
            Stat::make('Pending Payslips', $pendingPayslips)
                ->icon('heroicon-o-clock')
                ->color('warning'),
            Stat::make('Total Gross Payroll Cost', '$'.number_format($totalPayrollCost, 2))
                ->icon('heroicon-o-currency-dollar')
                ->color('info'),
            Stat::make('Payroll Runs', $payrollRuns)
                ->icon('heroicon-o-play-circle')
                ->color('primary'),
        ];
    }
}

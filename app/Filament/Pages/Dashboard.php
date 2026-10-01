<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Executive Dashboard';

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\ExecutiveStatsWidget::class,
            \App\Filament\Widgets\InvoiceFunnelWidget::class,
            \App\Filament\Widgets\RevenuePerformanceChartWidget::class,
            \App\Filament\Widgets\RecentInvoicesTableWidget::class,
            \App\Filament\Widgets\FrictionDriversWidget::class,
        ];
    }
}

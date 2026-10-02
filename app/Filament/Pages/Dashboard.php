<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Webkul\Support\Enums\NavigationGroup;

class Dashboard extends BaseDashboard
{
    protected static ?string $slug = 'executive-dashboard';

    protected static ?string $title = 'Executive Dashboard';

    public static function getNavigationLabel(): string
    {
        return 'Executive Dashboard';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return NavigationGroup::Dashboard;
    }

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return 'heroicon-o-chart-bar';
    }

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

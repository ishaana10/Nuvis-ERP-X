<?php

namespace Webkul\Payroll\Filament\Admin\Clusters;

use Filament\Clusters\Cluster;
use Filament\Panel;
use Webkul\Support\Enums\NavigationGroup;

class Reports extends Cluster
{
    protected static ?int $navigationSort = 7;

    public static function getSlug(?Panel $panel = null): string
    {
        return 'payroll/reports';
    }

    public static function getNavigationLabel(): string
    {
        return 'Reporting';
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Payroll;
    }
}

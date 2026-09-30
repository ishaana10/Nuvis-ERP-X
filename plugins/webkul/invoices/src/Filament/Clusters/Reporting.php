<?php

namespace Webkul\Invoice\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class Reporting extends Cluster
{
    protected static ?string $slug = 'invoices/reporting';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/reporting.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Invoice;
    }
}

<?php

namespace Webkul\Payroll;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Webkul\PluginManager\Package;

class PayrollPlugin implements Plugin
{
    public function getId(): string
    {
        return 'payroll';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        if (! Package::isPluginInstalled($this->getId())) {
            return;
        }

        $panel->when($panel->getId() === 'admin', function (Panel $panel) {
            $panel
                ->discoverResources(
                    in: __DIR__.'/Filament/Admin/Resources',
                    for: 'Webkul\\Payroll\\Filament\\Admin\\Resources'
                )
                ->discoverPages(
                    in: __DIR__.'/Filament/Admin/Pages',
                    for: 'Webkul\\Payroll\\Filament\\Admin\\Pages'
                )
                ->discoverClusters(
                    in: __DIR__.'/Filament/Admin/Clusters',
                    for: 'Webkul\\Payroll\\Filament\\Admin\\Clusters'
                )
                ->discoverWidgets(
                    in: __DIR__.'/Filament/Admin/Widgets',
                    for: 'Webkul\\Payroll\\Filament\\Admin\\Widgets'
                );
        });
    }

    public function boot(Panel $panel): void
    {
        //
    }
}

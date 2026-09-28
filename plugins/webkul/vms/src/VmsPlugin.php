<?php

namespace Webkul\Vms;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Webkul\PluginManager\Package;

class VmsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'vms';
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

        $panel
            ->when($panel->getId() == 'admin', function (Panel $panel) {
                $panel
                    ->resources([
                        \Webkul\Vms\Filament\Resources\VmsFiscalInvoiceResource::class,
                        \Webkul\Vms\Filament\Resources\VmsTaxRateResource::class,
                        \Webkul\Vms\Filament\Resources\VmsAuditLogResource::class,
                    ])
                    ->pages([
                        \Webkul\Vms\Filament\Pages\VmsSettingsPage::class,
                        \Webkul\Vms\Filament\Clusters\Settings\Pages\ManageVms::class,
                    ]);
            });
    }

    public function boot(Panel $panel): void
    {
        //
    }
}

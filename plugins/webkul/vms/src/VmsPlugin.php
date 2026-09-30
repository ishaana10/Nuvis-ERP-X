<?php

namespace Webkul\Vms;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Webkul\PluginManager\Package;
use Webkul\Vms\Filament\Clusters\Settings\Pages\ManageVms;
use Webkul\Vms\Filament\Pages\VmsSettingsPage;
use Webkul\Vms\Filament\Resources\VmsAuditLogResource;
use Webkul\Vms\Filament\Resources\VmsFiscalInvoiceResource;
use Webkul\Vms\Filament\Resources\VmsTaxRateResource;

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
                        VmsFiscalInvoiceResource::class,
                        VmsTaxRateResource::class,
                        VmsAuditLogResource::class,
                    ])
                    ->pages([
                        VmsSettingsPage::class,
                        ManageVms::class,
                    ]);
            });
    }

    public function boot(Panel $panel): void
    {
        //
    }
}

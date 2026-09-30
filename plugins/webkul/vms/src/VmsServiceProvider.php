<?php

namespace Webkul\Vms;

use Filament\Panel;
use Webkul\Account\Models\Move;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Vms\Observers\AccountMoveObserver;

class VmsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'vms';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasTranslations()
            ->hasViews()
            ->hasMigrations([
                '2026_04_01_000001_create_vms_tables',
                '2026_04_01_000002_register_vms_plugin',
            ])
            ->hasSettings([
                '2026_04_01_000003_create_vms_settings',
            ])
            ->runsMigrations()
            ->hasDependencies([
                'invoices',
                'accounts',
            ])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->installDependencies()
                    ->runsSeeders();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {})
            ->icon('vms');
    }

    public function packageBooted(): void
    {
        Move::observe(AccountMoveObserver::class);
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(VmsPlugin::make());
        });
    }
}

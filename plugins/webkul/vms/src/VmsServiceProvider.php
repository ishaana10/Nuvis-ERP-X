<?php

namespace Webkul\Vms;

use Filament\Panel;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class VmsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'vms';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasTranslations()
            ->hasMigrations([
                '2026_04_01_000001_create_vms_tables',
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
            ->icon('heroicon-o-shield-check');
    }

    public function packageBooted(): void
    {
        //
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(VmsPlugin::make());
        });
    }
}

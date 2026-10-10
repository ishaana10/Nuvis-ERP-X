<?php

namespace Nuvis\FijiPayroll;

use Filament\Panel;
use Illuminate\Support\ServiceProvider;

class FijiPayrollServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/fiji-payroll.php',
            'fiji-payroll'
        );
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'fiji-payroll');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/fiji-payroll.php' => config_path('fiji-payroll.php'),
            ], 'fiji-payroll-config');
        }

        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(FijiPayrollPlugin::make());
        });
    }
}

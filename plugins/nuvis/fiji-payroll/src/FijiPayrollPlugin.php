<?php

namespace Nuvis\FijiPayroll;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Nuvis\FijiPayroll\Filament\Resources\PayrollRunResource;

class FijiPayrollPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'fiji-payroll';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            PayrollRunResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}

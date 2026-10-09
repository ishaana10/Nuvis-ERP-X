<?php

namespace Webkul\Support\Settings;

use Spatie\LaravelSettings\Settings;

class MultiTenantSettings extends Settings
{
    public bool $enable_multi_tenancy;

    public string $tenant_switch_mode;

    public bool $show_tenant_switcher;

    public bool $strict_tenant_isolation;

    public bool $allow_self_service_tenant_creation;

    public ?int $default_tenant_id;

    public static function group(): string
    {
        return 'multi_tenant';
    }
}

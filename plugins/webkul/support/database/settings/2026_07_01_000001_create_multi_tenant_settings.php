<?php

use Illuminate\Support\Facades\Schema;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('multi_tenant.enable_multi_tenancy', true);
        $this->migrator->add('multi_tenant.tenant_switch_mode', 'multi');
        $this->migrator->add('multi_tenant.show_tenant_switcher', true);
        $this->migrator->add('multi_tenant.strict_tenant_isolation', false);
        $this->migrator->add('multi_tenant.allow_self_service_tenant_creation', true);
        $this->migrator->add('multi_tenant.default_tenant_id', null);
    }

    public function down(): void
    {
        if (Schema::hasTable('settings')) {
            $this->migrator->delete('multi_tenant.enable_multi_tenancy');
            $this->migrator->delete('multi_tenant.tenant_switch_mode');
            $this->migrator->delete('multi_tenant.show_tenant_switcher');
            $this->migrator->delete('multi_tenant.strict_tenant_isolation');
            $this->migrator->delete('multi_tenant.allow_self_service_tenant_creation');
            $this->migrator->delete('multi_tenant.default_tenant_id');
        }
    }
};

<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('vms.tin', '502579006');
        $this->migrator->add('vms.mrc', 'MRC-12345678');
        $this->migrator->add('vms.pac', '');
        $this->migrator->add('vms.pos_number', 'POS-001/1.0');
        $this->migrator->add('vms.environment', 'sandbox');
        $this->migrator->add('vms.sdc_type', 'V-SDC');
        $this->migrator->add('vms.api_url', 'https://tap.sandbox.vms.frcs.org.fj');
        $this->migrator->add('vms.is_active', true);
        $this->migrator->add('vms.pfx_certificate', '');
        $this->migrator->add('vms.certificate_password', '');
    }

    public function down(): void
    {
        $this->migrator->delete('vms.tin');
        $this->migrator->delete('vms.mrc');
        $this->migrator->delete('vms.pac');
        $this->migrator->delete('vms.pos_number');
        $this->migrator->delete('vms.environment');
        $this->migrator->delete('vms.sdc_type');
        $this->migrator->delete('vms.api_url');
        $this->migrator->delete('vms.is_active');
        $this->migrator->delete('vms.pfx_certificate');
        $this->migrator->delete('vms.certificate_password');
    }
};

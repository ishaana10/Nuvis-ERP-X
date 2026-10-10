<?php

use Illuminate\Support\Facades\Schema;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('branding.app_name', 'Nuvis ERP X');
        $this->migrator->add('branding.theme_preset', 'nuvis_blue');
        $this->migrator->add('branding.font_family', 'inter');
        $this->migrator->add('branding.footer_text', '© Nuvis ERP X. All rights reserved.');
        $this->migrator->add('branding.custom_css', null);
    }

    public function down(): void
    {
        if (Schema::hasTable('settings')) {
            $this->migrator->delete('branding.app_name');
            $this->migrator->delete('branding.theme_preset');
            $this->migrator->delete('branding.font_family');
            $this->migrator->delete('branding.footer_text');
            $this->migrator->delete('branding.custom_css');
        }
    }
};

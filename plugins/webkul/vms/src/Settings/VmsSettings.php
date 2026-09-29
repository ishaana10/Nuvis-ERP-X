<?php

namespace Webkul\Vms\Settings;

use Spatie\LaravelSettings\Settings;

class VmsSettings extends Settings
{
    public string $tin;

    public string $mrc;

    public ?string $pac;

    public string $pos_number;

    public string $environment;

    public string $sdc_type;

    public string $api_url;

    public bool $is_active;

    public ?string $pfx_certificate;

    public ?string $certificate_password;

    public static function group(): string
    {
        return 'vms';
    }
}

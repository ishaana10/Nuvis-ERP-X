<?php

namespace Webkul\Support\Settings;

use Spatie\LaravelSettings\Settings;

class BrandSettings extends Settings
{
    public ?string $app_name;

    public ?string $theme_preset;

    public ?string $font_family;

    public ?string $footer_text;

    public ?string $custom_css;

    public ?string $primary_color;

    public ?string $gray_color;

    public ?string $danger_color;

    public ?string $info_color;

    public ?string $success_color;

    public ?string $warning_color;

    public ?string $light_logo;

    public ?string $dark_logo;

    public ?string $favicon;

    public ?string $logo_height;

    public static function group(): string
    {
        return 'branding';
    }
}

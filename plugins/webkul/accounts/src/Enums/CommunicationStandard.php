<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasLabel;

enum CommunicationStandard: string implements HasLabel
{
    case NUVIS = 'nuvis';

    case EUROPEAN = 'european';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NUVIS    => __('accounts::enums/communication-standard.nuvis'),
            self::EUROPEAN => __('accounts::enums/communication-standard.european'),
        };
    }

    public static function options(): array
    {
        return [
            self::NUVIS->value    => __('accounts::enums/communication-standard.nuvis'),
            self::EUROPEAN->value => __('accounts::enums/communication-standard.european'),
        ];
    }
}

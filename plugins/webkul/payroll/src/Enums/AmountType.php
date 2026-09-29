<?php

namespace Webkul\Payroll\Enums;

use Filament\Support\Contracts\HasLabel;

enum AmountType: string implements HasLabel
{
    case FIXED = 'fixed';
    case PERCENTAGE = 'percentage';
    case FORMULA = 'formula';

    public function getLabel(): string
    {
        return match ($this) {
            self::FIXED      => 'Fixed Amount',
            self::PERCENTAGE => 'Percentage',
            self::FORMULA    => 'Formula / Expression',
        };
    }
}

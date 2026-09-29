<?php

namespace Webkul\Payroll\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PayslipState: string implements HasColor, HasIcon, HasLabel
{
    case DRAFT = 'draft';
    case CONFIRMED = 'confirmed';
    case PAID = 'paid';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT     => 'Draft',
            self::CONFIRMED => 'Confirmed',
            self::PAID      => 'Paid',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DRAFT     => 'warning',
            self::CONFIRMED => 'info',
            self::PAID      => 'success',
            self::CANCELLED => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::DRAFT     => 'heroicon-o-pencil',
            self::CONFIRMED => 'heroicon-o-check',
            self::PAID      => 'heroicon-o-currency-dollar',
            self::CANCELLED => 'heroicon-o-x-circle',
        };
    }
}

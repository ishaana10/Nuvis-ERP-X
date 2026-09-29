<?php

namespace Webkul\Payroll\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PayrollRunState: string implements HasColor, HasIcon, HasLabel
{
    case DRAFT = 'draft';
    case CONFIRMED = 'confirmed';
    case DONE = 'done';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT     => 'Draft',
            self::CONFIRMED => 'Confirmed',
            self::DONE      => 'Done',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DRAFT     => 'warning',
            self::CONFIRMED => 'info',
            self::DONE      => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::DRAFT     => 'heroicon-o-document',
            self::CONFIRMED => 'heroicon-o-clock',
            self::DONE      => 'heroicon-o-check-badge',
        };
    }
}

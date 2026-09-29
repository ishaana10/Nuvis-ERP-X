<?php

namespace Webkul\Payroll\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ContractState: string implements HasColor, HasIcon, HasLabel
{
    case DRAFT = 'draft';
    case OPEN = 'open';
    case CLOSE = 'close';
    case CANCEL = 'cancel';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT  => 'Draft',
            self::OPEN   => 'Running',
            self::CLOSE  => 'Expired',
            self::CANCEL => 'Cancelled',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DRAFT  => 'warning',
            self::OPEN   => 'success',
            self::CLOSE  => 'danger',
            self::CANCEL => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::DRAFT  => 'heroicon-o-document-text',
            self::OPEN   => 'heroicon-o-check-circle',
            self::CLOSE  => 'heroicon-o-x-circle',
            self::CANCEL => 'heroicon-o-no-symbol',
        };
    }
}

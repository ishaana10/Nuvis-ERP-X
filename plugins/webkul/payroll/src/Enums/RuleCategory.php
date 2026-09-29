<?php

namespace Webkul\Payroll\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum RuleCategory: string implements HasColor, HasLabel
{
    case BASIC = 'basic';
    case ALLOWANCE = 'allowance';
    case GROSS = 'gross';
    case DEDUCTION = 'deduction';
    case TAX = 'tax';
    case SOCIAL = 'social';
    case NET = 'net';

    public function getLabel(): string
    {
        return match ($this) {
            self::BASIC     => 'Basic Salary',
            self::ALLOWANCE => 'Allowance',
            self::GROSS     => 'Gross',
            self::DEDUCTION => 'Deduction',
            self::TAX       => 'Tax',
            self::SOCIAL    => 'Social Security / Statutory',
            self::NET       => 'Net Salary',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::BASIC     => 'info',
            self::ALLOWANCE => 'success',
            self::GROSS     => 'primary',
            self::DEDUCTION => 'warning',
            self::TAX       => 'danger',
            self::SOCIAL    => 'gray',
            self::NET       => 'success',
        };
    }
}

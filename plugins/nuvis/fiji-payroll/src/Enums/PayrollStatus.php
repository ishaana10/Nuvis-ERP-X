<?php

namespace Nuvis\FijiPayroll\Enums;

enum PayrollStatus: string
{
    case Draft = 'draft';
    case Calculated = 'calculated';
    case Approved = 'approved';
    case Paid = 'paid';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft      => 'Draft',
            self::Calculated => 'Calculated',
            self::Approved   => 'Approved',
            self::Paid       => 'Paid',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Draft      => 'gray',
            self::Calculated => 'info',
            self::Approved   => 'warning',
            self::Paid       => 'success',
        };
    }
}

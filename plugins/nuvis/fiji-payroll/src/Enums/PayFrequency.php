<?php

namespace Nuvis\FijiPayroll\Enums;

enum PayFrequency: string
{
    case Monthly = 'monthly';
    case Fortnightly = 'fortnightly';
    case Weekly = 'weekly';

    public function getPeriodsPerYear(): int
    {
        return match ($this) {
            self::Monthly     => 12,
            self::Fortnightly => 26,
            self::Weekly      => 52,
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Monthly     => 'Monthly (12 periods)',
            self::Fortnightly => 'Fortnightly (26 periods)',
            self::Weekly      => 'Weekly (52 periods)',
        };
    }

    public static function fromValue(string|self $value): self
    {
        if ($value instanceof self) {
            return $value;
        }

        return self::tryFrom(strtolower($value)) ?? self::Monthly;
    }
}

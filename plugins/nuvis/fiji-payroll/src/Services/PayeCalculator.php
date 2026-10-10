<?php

namespace Nuvis\FijiPayroll\Services;

use Nuvis\FijiPayroll\Enums\PayFrequency;

class PayeCalculator
{
    public function calculateTax(
        float $taxableIncome,
        bool $isResident = true,
        PayFrequency|string $frequency = PayFrequency::Monthly
    ): float {
        if ($taxableIncome <= 0) {
            return 0.0;
        }

        $payFrequency = PayFrequency::fromValue($frequency);
        $periods = $payFrequency->getPeriodsPerYear();

        $annualTaxable = $taxableIncome * $periods;

        if (! $isResident) {
            $nonResidentRate = (float) config('fiji-payroll.paye.non_resident_rate', 0.20);
            $annualTax = $annualTaxable * $nonResidentRate;

            return round($annualTax / $periods, 2);
        }

        $annualTax = $this->calculateAnnualResidentTax($annualTaxable);

        return round($annualTax / $periods, 2);
    }

    public function calculateAnnualResidentTax(float $annualTaxable): float
    {
        if ($annualTaxable <= 30000) {
            return 0.0;
        }

        if ($annualTaxable <= 50000) {
            return ($annualTaxable - 30000) * 0.18;
        }

        if ($annualTaxable <= 270000) {
            return 3600.0 + (($annualTaxable - 50000) * 0.20);
        }

        return 47600.0 + (($annualTaxable - 270000) * 0.39);
    }
}

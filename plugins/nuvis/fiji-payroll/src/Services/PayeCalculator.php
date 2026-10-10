<?php

namespace Nuvis\FijiPayroll\Services;

use Nuvis\FijiPayroll\Enums\PayFrequency;

/**
 * Fiji PAYE (Pay As You Earn) Calculator – Enhanced
 */
class PayeCalculator
{
    /**
     * Calculate PAYE for a single pay period.
     */
    public function calculate(
        float $taxableIncome,
        PayFrequency|string $frequency = PayFrequency::Monthly,
        bool $isResident = true,
        float $yearToDateTaxable = 0.0
    ): array {
        $freqEnum = $frequency instanceof PayFrequency ? $frequency : PayFrequency::fromValue($frequency);

        if ($taxableIncome <= 0) {
            return $this->emptyResult();
        }

        $periods = $freqEnum->periodsPerYear();
        $annualEquivalent = $taxableIncome * $periods;

        if (! $isResident) {
            $paye = round($taxableIncome * 0.20, 2);

            return [
                'paye' => $paye,
                'basic_tax' => $paye,
                'srt' => 0.0,
                'effective_rate' => 0.20,
                'annual_equivalent' => $annualEquivalent,
                'annual_tax' => $paye * $periods,
            ];
        }

        $annualBreakdown = $this->calculateAnnualTaxBreakdown($annualEquivalent);

        $periodTax = round($annualBreakdown['total'] / $periods, 2);
        $periodBasic = round($annualBreakdown['basic'] / $periods, 2);
        $periodSrt = round($annualBreakdown['srt'] / $periods, 2);

        $effectiveRate = $taxableIncome > 0
            ? round($periodTax / $taxableIncome, 4)
            : 0.0;

        return [
            'paye' => $periodTax,
            'basic_tax' => $periodBasic,
            'srt' => $periodSrt,
            'effective_rate' => $effectiveRate,
            'annual_equivalent' => $annualEquivalent,
            'annual_tax' => $annualBreakdown['total'],
        ];
    }

    /**
     * Alias method for calculateTax.
     */
    public function calculateTax(
        float $taxableIncome,
        bool $isResident = true,
        PayFrequency|string $frequency = PayFrequency::Monthly
    ): float {
        return $this->calculate($taxableIncome, $frequency, $isResident)['paye'];
    }

    /**
     * Full annual tax breakdown including progressive SRT.
     */
    public function calculateAnnualTaxBreakdown(float $income): array
    {
        if ($income <= 0) {
            return ['basic' => 0.0, 'srt' => 0.0, 'total' => 0.0];
        }

        // ----- Basic Income Tax -----
        $basic = 0.0;

        if ($income <= 30000) {
            $basic = 0.0;
        } elseif ($income <= 50000) {
            $basic = ($income - 30000) * 0.18;
        } elseif ($income <= 270000) {
            $basic = 3600 + ($income - 50000) * 0.20;
        } else {
            // Tax up to 270k = 3,600 + (220,000 * 0.20) = 47,600
            $basic = 47600 + ($income - 270000) * 0.20;
        }

        // ----- Social Responsibility Tax (SRT) -----
        $srt = 0.0;

        if ($income > 270000) {
            $srt = $this->calculateSrt($income);
        }

        $total = round($basic + $srt, 2);

        return [
            'basic' => round($basic, 2),
            'srt' => round($srt, 2),
            'total' => $total,
        ];
    }

    /**
     * Progressive SRT calculation above FJD 270,000.
     */
    protected function calculateSrt(float $income): float
    {
        $srt = 0.0;
        $remaining = $income - 270000;

        // SRT bands (excess over 270k)
        $bands = [
            // [band_size, rate]
            [30000,  0.13], // 270k – 300k
            [50000,  0.14], // 300k – 350k
            [50000,  0.15], // 350k – 400k
            [50000,  0.16], // 400k – 450k
            [50000,  0.17], // 450k – 500k
            [500000, 0.18], // 500k – 1m
            [null,   0.19], // 1m+
        ];

        foreach ($bands as [$size, $rate]) {
            if ($remaining <= 0) {
                break;
            }

            if ($size === null) {
                $srt += $remaining * $rate;
                break;
            }

            $taxable = min($remaining, $size);
            $srt += $taxable * $rate;
            $remaining -= $taxable;
        }

        return $srt;
    }

    /**
     * Convenience: monthly resident PAYE only.
     */
    public function monthlyResident(float $monthlyTaxable): float
    {
        return $this->calculate($monthlyTaxable, PayFrequency::Monthly, true)['paye'];
    }

    /**
     * Full annual tax (basic + SRT).
     */
    public function calculateAnnualTax(float $annualChargeableIncome): float
    {
        return $this->calculateAnnualTaxBreakdown($annualChargeableIncome)['total'];
    }

    protected function emptyResult(): array
    {
        return [
            'paye' => 0.0,
            'basic_tax' => 0.0,
            'srt' => 0.0,
            'effective_rate' => 0.0,
            'annual_equivalent' => 0.0,
            'annual_tax' => 0.0,
        ];
    }
}

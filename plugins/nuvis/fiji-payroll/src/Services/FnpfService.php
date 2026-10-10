<?php

namespace Nuvis\FijiPayroll\Services;

class FnpfService
{
    /**
     * Get the employee FNPF contribution rate (8%).
     */
    public function getEmployeeRate(): float
    {
        return (float) config('fiji-payroll.fnpf.employee_rate', 0.08);
    }

    /**
     * Get the employer FNPF contribution rate (8%).
     */
    public function getEmployerRate(): float
    {
        return (float) config('fiji-payroll.fnpf.employer_rate', 0.08);
    }

    /**
     * Determine the FNPF-eligible wage base.
     * Supports both positional floats (basic, overtime, allowances, other)
     * and array input ['basic' => ..., 'overtime' => ..., 'allowances' => ...].
     */
    public function getEligibleWageBase(array|float $basic = 0, float $overtime = 0, float $allowances = 0, float $other = 0): float
    {
        if (is_array($basic)) {
            $b = $basic['basic'] ?? 0;
            $o = $basic['overtime'] ?? 0;
            $a = $basic['allowances'] ?? 0;
            $ot = $basic['other_ordinary'] ?? $basic['other'] ?? 0;
            return round($b + $o + $a + $ot, 2);
        }

        return round($basic + $overtime + $allowances + $other, 2);
    }

    /**
     * Calculate employee FNPF contribution (8% of eligible wage base).
     */
    public function calculateEmployeeContribution(float $fnpfBase): float
    {
        return round($fnpfBase * $this->getEmployeeRate(), 2);
    }

    /**
     * Calculate employer FNPF contribution (8% of eligible wage base).
     */
    public function calculateEmployerContribution(float $fnpfBase): float
    {
        return round($fnpfBase * $this->getEmployerRate(), 2);
    }
}

<?php

namespace Nuvis\FijiPayroll\Services;

class FnpfService
{
    protected float $employeeRate;

    protected float $employerRate;

    public function __construct(?float $employeeRate = null, ?float $employerRate = null)
    {
        $this->employeeRate = $employeeRate ?? (float) config('fiji-payroll.fnpf.employee_rate', 0.08);
        $this->employerRate = $employerRate ?? (float) config('fiji-payroll.fnpf.employer_rate', 0.08);
    }

    public function getEligibleWageBase(float $basic, float $overtime = 0, float $allowances = 0): float
    {
        return max(0, $basic + $overtime + $allowances);
    }

    public function calculateEmployeeContribution(float $eligibleWage): float
    {
        return round($eligibleWage * $this->employeeRate, 2);
    }

    public function calculateEmployerContribution(float $eligibleWage): float
    {
        return round($eligibleWage * $this->employerRate, 2);
    }

    public function getEmployeeRate(): float
    {
        return $this->employeeRate;
    }

    public function getEmployerRate(): float
    {
        return $this->employerRate;
    }
}

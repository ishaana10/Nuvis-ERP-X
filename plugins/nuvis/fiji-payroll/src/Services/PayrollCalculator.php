<?php

namespace Nuvis\FijiPayroll\Services;

use Nuvis\FijiPayroll\Enums\PayFrequency;

class PayrollCalculator
{
    protected FnpfService $fnpfService;

    protected PayeCalculator $payeCalculator;

    public function __construct(?FnpfService $fnpfService = null, ?PayeCalculator $payeCalculator = null)
    {
        $this->fnpfService = $fnpfService ?? new FnpfService;
        $this->payeCalculator = $payeCalculator ?? new PayeCalculator;
    }

    public function calculate(array $data): array
    {
        $basic = max(0, (float) ($data['basic'] ?? 0));
        $overtime = max(0, (float) ($data['overtime'] ?? 0));
        $allowances = max(0, (float) ($data['allowances'] ?? 0));

        $isResident = (bool) ($data['is_resident'] ?? true);
        $frequency = PayFrequency::fromValue($data['frequency'] ?? PayFrequency::Monthly);

        $gross = round($basic + $overtime + $allowances, 2);

        $eligibleWage = $this->fnpfService->getEligibleWageBase($basic, $overtime, $allowances);
        $fnpfEmployee = $this->fnpfService->calculateEmployeeContribution($eligibleWage);
        $fnpfEmployer = $this->fnpfService->calculateEmployerContribution($eligibleWage);

        $taxableIncome = max(0, round($gross - $fnpfEmployee, 2));

        $paye = $this->payeCalculator->calculateTax($taxableIncome, $isResident, $frequency);

        $workcareRate = (float) config('fiji-payroll.levies.workcare_rate', 0.01);
        $trainingRate = (float) config('fiji-payroll.levies.training_rate', 0.01);

        $workcareLevy = round($gross * $workcareRate, 2);
        $trainingLevy = round($gross * $trainingRate, 2);

        $netPay = max(0, round($gross - $fnpfEmployee - $paye, 2));

        $totalEmployerCost = round($gross + $fnpfEmployer + $workcareLevy + $trainingLevy, 2);

        $rateSnapshot = [
            'fnpf_employee_rate' => $this->fnpfService->getEmployeeRate(),
            'fnpf_employer_rate' => $this->fnpfService->getEmployerRate(),
            'workcare_rate'      => $workcareRate,
            'training_rate'      => $trainingRate,
            'pay_frequency'      => $frequency->value,
            'is_resident'        => $isResident,
            'calculated_at'      => now()->toIso8601String(),
        ];

        return [
            'basic'               => $basic,
            'overtime'            => $overtime,
            'allowances'          => $allowances,
            'gross'               => $gross,
            'eligible_wage_base'  => $eligibleWage,
            'fnpf_employee'       => $fnpfEmployee,
            'fnpf_employer'       => $fnpfEmployer,
            'taxable_income'      => $taxableIncome,
            'paye'                => $paye,
            'workcare_levy'       => $workcareLevy,
            'training_levy'       => $trainingLevy,
            'net_pay'             => $netPay,
            'total_employer_cost' => $totalEmployerCost,
            'rate_snapshot'       => $rateSnapshot,
        ];
    }
}

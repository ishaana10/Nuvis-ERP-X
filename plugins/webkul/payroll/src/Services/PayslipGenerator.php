<?php

namespace Webkul\Payroll\Services;

use Illuminate\Support\Collection;
use Webkul\Payroll\Enums\ContractState;
use Webkul\Payroll\Enums\PayslipState;
use Webkul\Payroll\Models\EmployeeContract;
use Webkul\Payroll\Models\PayrollRun;
use Webkul\Payroll\Models\Payslip;

class PayslipGenerator
{
    public function __construct(
        protected PayrollCalculator $calculator = new PayrollCalculator
    ) {}

    /**
     * Compute and populate lines for a single payslip.
     */
    public function computePayslip(Payslip $payslip): Payslip
    {
        $result = $this->calculator->calculate($payslip);

        $payslip->lines()->delete();

        foreach ($result['lines'] as $lineData) {
            $payslip->lines()->create($lineData);
        }

        $payslip->update([
            'basic_wage'                  => $result['basic_wage'],
            'gross_wage'                  => $result['gross_wage'],
            'total_deductions'            => $result['total_deductions'],
            'total_employer_contributions'=> $result['total_employer_contributions'],
            'net_wage'                    => $result['net_wage'],
        ]);

        return $payslip->fresh(['lines']);
    }

    /**
     * Generate payslips for all active employee contracts in a payroll run.
     *
     * @return Collection<int, Payslip>
     */
    public function generateForRun(PayrollRun $run)
    {
        $period = $run->period;
        $companyId = $run->company_id;

        $contracts = EmployeeContract::query()
            ->where('state', ContractState::OPEN)
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->get();

        $generated = collect();

        foreach ($contracts as $contract) {
            $payslip = Payslip::create([
                'name'        => 'Payslip - '.$contract->employee?->name.' ('.$run->name.')',
                'number'      => 'PS/'.now()->format('Ym').'/'.str_pad($contract->employee_id, 4, '0', STR_PAD_LEFT),
                'employee_id' => $contract->employee_id,
                'contract_id' => $contract->id,
                'run_id'      => $run->id,
                'period_id'   => $period?->id,
                'company_id'  => $companyId ?? $contract->company_id,
                'state'       => PayslipState::DRAFT,
                'start_date'  => $period?->start_date ?? now()->startOfMonth()->toDateString(),
                'end_date'    => $period?->end_date ?? now()->endOfMonth()->toDateString(),
            ]);

            $this->computePayslip($payslip);
            $generated->push($payslip);
        }

        return $generated;
    }
}

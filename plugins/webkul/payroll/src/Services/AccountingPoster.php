<?php

namespace Webkul\Payroll\Services;

use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Payroll\Enums\PayslipState;
use Webkul\Payroll\Models\PayrollRun;
use Webkul\Payroll\Models\Payslip;
use Webkul\Payroll\Settings\PayrollSettings;

class AccountingPoster
{
    /**
     * Post accounting entry for a single payslip.
     */
    public function postPayslip(Payslip $payslip): ?Move
    {
        if ($payslip->move_id) {
            return $payslip->move;
        }

        $settings = app(PayrollSettings::class);

        $journalId = $payslip->contract?->journal_id ?? $settings->default_journal_id;

        if (! $journalId) {
            $journal = Journal::where('company_id', $payslip->company_id)->first();
            $journalId = $journal?->id;
        }

        if (! $journalId) {
            return null;
        }

        $salaryExpenseAccountId = $settings->default_salary_expense_account_id;
        $employeePayableAccountId = $settings->default_employee_payable_account_id;
        $taxPayableAccountId = $settings->default_tax_payable_account_id;
        $employerSocialAccountId = $settings->default_employer_social_account_id;

        $move = Move::create([
            'journal_id'    => $journalId,
            'company_id'    => $payslip->company_id,
            'partner_id'    => $payslip->employee?->partner_id,
            'move_type'     => MoveType::ENTRY,
            'state'         => MoveState::DRAFT,
            'date'          => $payslip->end_date ?? now()->toDateString(),
            'ref'           => 'Payroll - '.$payslip->name,
            'narration'     => 'Payslip entry for '.$payslip->employee?->name,
        ]);

        $lineSort = 1;

        // Debit: Gross Wage / Salary Expense
        if ($payslip->gross_wage > 0 && $salaryExpenseAccountId) {
            $move->lines()->create([
                'move_id'    => $move->id,
                'account_id' => $salaryExpenseAccountId,
                'partner_id' => $payslip->employee?->partner_id,
                'name'       => 'Gross Salary - '.$payslip->employee?->name,
                'debit'      => $payslip->gross_wage,
                'credit'     => 0.0,
                'balance'    => $payslip->gross_wage,
                'sort'       => $lineSort++,
            ]);
        }

        // Debit: Employer Social Contribution Expense
        if ($payslip->total_employer_contributions > 0 && $employerSocialAccountId) {
            $move->lines()->create([
                'move_id'    => $move->id,
                'account_id' => $employerSocialAccountId,
                'partner_id' => $payslip->employee?->partner_id,
                'name'       => 'Employer Contributions - '.$payslip->employee?->name,
                'debit'      => $payslip->total_employer_contributions,
                'credit'     => 0.0,
                'balance'    => $payslip->total_employer_contributions,
                'sort'       => $lineSort++,
            ]);
        }

        // Credit: Employee Payable (Net Wage)
        if ($payslip->net_wage > 0 && $employeePayableAccountId) {
            $move->lines()->create([
                'move_id'    => $move->id,
                'account_id' => $employeePayableAccountId,
                'partner_id' => $payslip->employee?->partner_id,
                'name'       => 'Net Payable - '.$payslip->employee?->name,
                'debit'      => 0.0,
                'credit'     => $payslip->net_wage,
                'balance'    => -$payslip->net_wage,
                'sort'       => $lineSort++,
            ]);
        }

        // Credit: Tax & Social Liabilities (Total Deductions + Employer Contributions)
        $totalLiabilities = $payslip->total_deductions + $payslip->total_employer_contributions;
        if ($totalLiabilities > 0 && $taxPayableAccountId) {
            $move->lines()->create([
                'move_id'    => $move->id,
                'account_id' => $taxPayableAccountId,
                'partner_id' => $payslip->employee?->partner_id,
                'name'       => 'Tax & Statutory Liabilities - '.$payslip->employee?->name,
                'debit'      => 0.0,
                'credit'     => $totalLiabilities,
                'balance'    => -$totalLiabilities,
                'sort'       => $lineSort++,
            ]);
        }

        $move->update(['state' => MoveState::POSTED]);

        $payslip->update([
            'move_id' => $move->id,
            'state'   => PayslipState::CONFIRMED,
        ]);

        return $move;
    }

    /**
     * Post accounting entries for all confirmed/draft payslips in a payroll run.
     */
    public function postRun(PayrollRun $run): void
    {
        foreach ($run->payslips as $payslip) {
            $this->postPayslip($payslip);
        }
    }
}

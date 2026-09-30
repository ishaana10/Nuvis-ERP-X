<?php

namespace Webkul\Payroll\Services;

use Webkul\Payroll\Enums\AmountType;
use Webkul\Payroll\Enums\RuleCategory;
use Webkul\Payroll\Models\EmployeeContract;
use Webkul\Payroll\Models\Payslip;
use Webkul\Payroll\Models\SalaryRule;

class PayrollCalculator
{
    /**
     * Calculate payslip rule results.
     */
    public function calculate(Payslip $payslip): array
    {
        /** @var EmployeeContract|null $contract */
        $contract = $payslip->contract;
        $wage = (float) ($contract?->wage ?? 0.0);
        $structure = $contract?->structure;

        if (! $structure) {
            return [
                'basic_wage'                  => $wage,
                'gross_wage'                  => $wage,
                'total_deductions'            => 0.0,
                'total_employer_contributions'=> 0.0,
                'net_wage'                    => $wage,
                'lines'                       => [],
            ];
        }

        $rules = $structure->rules()
            ->where('is_active', true)
            ->orderBy('sequence')
            ->get();

        $context = [
            'wage'  => $wage,
            'BASIC' => $wage,
            'GROSS' => $wage,
            'NET'   => $wage,
            'rules' => [],
        ];

        $lines = [];
        $basicWage = $wage;
        $grossWage = $wage;
        $totalDeductions = 0.0;
        $totalEmployerContributions = 0.0;

        foreach ($rules as $rule) {
            if (! $this->evalCondition($rule, $context)) {
                continue;
            }

            $amount = $this->computeRuleAmount($rule, $context);
            $qty = 1.0;
            $rate = 100.0;
            $total = round(($amount * $qty * ($rate / 100.0)), 4);

            $categoryValue = $rule->category instanceof RuleCategory
                ? $rule->category->value
                : (string) $rule->category;

            $context['rules'][$rule->code] = $total;

            if ($categoryValue === RuleCategory::BASIC->value) {
                $basicWage = $total;
                $context['BASIC'] = $basicWage;
            } elseif ($categoryValue === RuleCategory::GROSS->value) {
                $grossWage = $total;
                $context['GROSS'] = $grossWage;
            } elseif ($categoryValue === RuleCategory::ALLOWANCE->value) {
                $grossWage += $total;
                $context['GROSS'] = $grossWage;
            } elseif (in_array($categoryValue, [RuleCategory::DEDUCTION->value, RuleCategory::TAX->value])) {
                if (! $rule->is_employer) {
                    $totalDeductions += $total;
                }
            } elseif ($categoryValue === RuleCategory::SOCIAL->value) {
                if ($rule->is_employer) {
                    $totalEmployerContributions += $total;
                } else {
                    $totalDeductions += $total;
                }
            }

            $net = $grossWage - $totalDeductions;
            $context['NET'] = $net;

            $lines[] = [
                'salary_rule_id' => $rule->id,
                'name'           => $rule->name,
                'code'           => $rule->code,
                'category'       => $categoryValue,
                'sequence'       => $rule->sequence,
                'quantity'       => $qty,
                'rate'           => $rate,
                'amount'         => $amount,
                'total'          => $total,
                'is_employer'    => (bool) $rule->is_employer,
            ];
        }

        $netWage = max(0.0, $grossWage - $totalDeductions);

        return [
            'basic_wage'                  => round($basicWage, 4),
            'gross_wage'                  => round($grossWage, 4),
            'total_deductions'            => round($totalDeductions, 4),
            'total_employer_contributions'=> round($totalEmployerContributions, 4),
            'net_wage'                    => round($netWage, 4),
            'lines'                       => $lines,
        ];
    }

    protected function evalCondition(SalaryRule $rule, array $context): bool
    {
        if ($rule->condition_select === 'range') {
            $value = $context['BASIC'] ?? 0.0;
            if ($rule->condition_range_min !== null && $value < (float) $rule->condition_range_min) {
                return false;
            }
            if ($rule->condition_range_max !== null && $value > (float) $rule->condition_range_max) {
                return false;
            }
        } elseif ($rule->condition_select === 'python' && ! empty($rule->condition_python)) {
            return $this->safeEval($rule->condition_python, $context) > 0;
        }

        return true;
    }

    protected function computeRuleAmount(SalaryRule $rule, array $context): float
    {
        $amountType = $rule->amount_type instanceof AmountType
            ? $rule->amount_type->value
            : (string) $rule->amount_type;

        if ($amountType === AmountType::FIXED->value) {
            return (float) $rule->amount_fix;
        }

        if ($amountType === AmountType::PERCENTAGE->value) {
            $baseKey = strtoupper($rule->amount_percentage_base ?: 'basic');
            $baseValue = $context[$baseKey] ?? $context['BASIC'] ?? $context['wage'] ?? 0.0;

            return (float) ($baseValue * ((float) $rule->amount_percentage / 100.0));
        }

        if ($amountType === AmountType::FORMULA->value && ! empty($rule->amount_python_compute)) {
            return $this->safeEval($rule->amount_python_compute, $context);
        }

        return 0.0;
    }

    protected function safeEval(string $expression, array $context): float
    {
        $expression = trim($expression);
        if ($expression === '') {
            return 0.0;
        }

        $replacements = [
            'wage'  => $context['wage'] ?? 0.0,
            'BASIC' => $context['BASIC'] ?? 0.0,
            'GROSS' => $context['GROSS'] ?? 0.0,
            'NET'   => $context['NET'] ?? 0.0,
        ];

        foreach ($context['rules'] ?? [] as $code => $val) {
            $replacements[$code] = $val;
        }

        foreach ($replacements as $key => $val) {
            $expression = preg_replace('/\b'.preg_quote($key, '/').'\b/', (string) ((float) $val), $expression);
        }

        if (preg_match('/[^0-9\+\-\*\/\(\)\.\s]/', $expression)) {
            return 0.0;
        }

        return $this->evalMath($expression);
    }

    protected function evalMath(string $expr): float
    {
        try {
            $expr = str_replace(' ', '', $expr);
            if ($expr === '') {
                return 0.0;
            }

            while (preg_match('/\(([^\(\)]+)\)/', $expr, $matches)) {
                $subResult = $this->evalMath($matches[1]);
                $expr = str_replace($matches[0], (string) $subResult, $expr);
            }

            while (preg_match('/([0-9\.]+)([\*\/])([0-9\.]+)/', $expr, $m)) {
                $a = (float) $m[1];
                $op = $m[2];
                $b = (float) $m[3];
                $res = $op === '*' ? $a * $b : ($b != 0.0 ? $a / $b : 0.0);
                $expr = str_replace($m[0], (string) $res, $expr);
            }

            while (preg_match('/([0-9\.]+)([\+\-])([0-9\.]+)/', $expr, $m)) {
                $a = (float) $m[1];
                $op = $m[2];
                $b = (float) $m[3];
                $res = $op === '+' ? $a + $b : $a - $b;
                $expr = str_replace($m[0], (string) $res, $expr);
            }

            return (float) $expr;
        } catch (\Throwable) {
            return 0.0;
        }
    }
}

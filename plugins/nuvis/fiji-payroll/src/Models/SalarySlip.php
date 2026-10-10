<?php

namespace Nuvis\FijiPayroll\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nuvis\FijiPayroll\Enums\PayFrequency;
use Nuvis\FijiPayroll\Enums\PayrollStatus;
use Nuvis\FijiPayroll\Services\PayrollCalculator;

class SalarySlip extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'payroll_run_id',
        'employee_id',
        'employee_name',
        'employee_number',
        'tin',
        'fnpf_number',
        'tax_code',
        'is_resident',
        'basic',
        'basic_salary',
        'overtime',
        'allowances',
        'other_earnings',
        'gross',
        'fnpf_base',
        'employee_fnpf',
        'employer_fnpf',
        'taxable_income',
        'paye',
        'other_deductions',
        'net_pay',
        'workcare_levy',
        'training_levy',
        'employer_cost',
        'employee_fnpf_rate',
        'employer_fnpf_rate',
        'calculation_details',
        'status',
    ];

    protected $casts = [
        'is_resident' => 'boolean',
        'basic' => 'decimal:2',
        'overtime' => 'decimal:2',
        'allowances' => 'decimal:2',
        'other_earnings' => 'decimal:2',
        'gross' => 'decimal:2',
        'fnpf_base' => 'decimal:2',
        'employee_fnpf' => 'decimal:2',
        'employer_fnpf' => 'decimal:2',
        'taxable_income' => 'decimal:2',
        'paye' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'workcare_levy' => 'decimal:2',
        'training_levy' => 'decimal:2',
        'employer_cost' => 'decimal:2',
        'employee_fnpf_rate' => 'decimal:4',
        'employer_fnpf_rate' => 'decimal:4',
        'calculation_details' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (SalarySlip $slip) {
            unset($slip->attributes['basic_salary']);

            if (($slip->basic > 0 || $slip->overtime > 0 || $slip->allowances > 0) && (empty($slip->gross) || $slip->gross == 0)) {
                $calc = app(PayrollCalculator::class)->calculate([
                    'basic' => $slip->basic,
                    'overtime' => $slip->overtime,
                    'allowances' => $slip->allowances,
                    'is_resident' => $slip->is_resident,
                    'frequency' => $slip->payrollRun?->frequency ?? PayFrequency::Monthly,
                ]);

                $slip->gross = $calc['gross'];
                $slip->fnpf_base = $calc['fnpf_base'];
                $slip->employee_fnpf = $calc['employee_fnpf'];
                $slip->employer_fnpf = $calc['employer_fnpf'];
                $slip->taxable_income = $calc['taxable_income'];
                $slip->paye = $calc['paye'];
                $slip->net_pay = $calc['net_pay'];
                $slip->workcare_levy = $calc['workcare_levy'];
                $slip->training_levy = $calc['training_levy'];
                $slip->employer_cost = $calc['employer_cost'];
                $slip->employee_fnpf_rate = $calc['rates']['employee_fnpf_rate'];
                $slip->employer_fnpf_rate = $calc['rates']['employer_fnpf_rate'];
            }
        });

        static::saved(function (SalarySlip $slip) {
            if ($slip->payrollRun) {
                $run = $slip->payrollRun;
                $slips = $run->salarySlips;
                $run->update([
                    'employee_count' => $slips->count(),
                    'total_gross' => $slips->sum('gross'),
                    'total_employee_fnpf' => $slips->sum('employee_fnpf'),
                    'total_employer_fnpf' => $slips->sum('employer_fnpf'),
                    'total_paye' => $slips->sum('paye'),
                    'total_net' => $slips->sum('net_pay'),
                    'total_employer_cost' => $slips->sum('employer_cost'),
                    'status' => PayrollStatus::Calculated,
                ]);
            }
        });
    }

    public function getGrossPayAttribute()
    {
        return $this->gross;
    }

    public function getFnpfEmployeeAttribute()
    {
        return $this->employee_fnpf;
    }

    public function getFnpfEmployerAttribute()
    {
        return $this->employer_fnpf;
    }

    public function setBasicSalaryAttribute($value)
    {
        $this->attributes['basic'] = $value;
        unset($this->attributes['basic_salary']);
    }

    public function payrollRun(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class);
    }

    public function employee(): BelongsTo
    {
        $model = config('fiji-payroll.employee_model', \Webkul\Employee\Models\Employee::class);
        return $this->belongsTo($model, 'employee_id');
    }
}

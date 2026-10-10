<?php

namespace Nuvis\FijiPayroll\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nuvis\FijiPayroll\Enums\PayFrequency;
use Nuvis\FijiPayroll\Services\PayrollCalculator;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

class SalarySlip extends Model
{
    use BelongsToCompany, HasFactory, SoftDeletes;

    protected $table = 'fiji_salary_slips';

    protected $fillable = [
        'payroll_run_id',
        'employee_id',
        'employee_name',
        'is_resident',
        'pay_frequency',
        'basic_salary',
        'overtime',
        'allowances',
        'gross_pay',
        'taxable_income',
        'fnpf_employee',
        'fnpf_employer',
        'paye_tax',
        'workcare_levy',
        'training_levy',
        'net_pay',
        'rate_snapshot',
        'company_id',
    ];

    protected $casts = [
        'is_resident'   => 'boolean',
        'pay_frequency' => PayFrequency::class,
        'rate_snapshot' => 'array',
    ];

    public function payrollRun(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class, 'payroll_run_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function (SalarySlip $slip) {
            $calc = app(PayrollCalculator::class)->calculate([
                'basic'       => $slip->basic_salary,
                'overtime'    => $slip->overtime,
                'allowances'  => $slip->allowances,
                'is_resident' => $slip->is_resident,
                'frequency'   => $slip->pay_frequency ?? $slip->payrollRun?->pay_frequency ?? PayFrequency::Monthly,
            ]);

            $slip->gross_pay = $calc['gross'];
            $slip->taxable_income = $calc['taxable_income'];
            $slip->fnpf_employee = $calc['fnpf_employee'];
            $slip->fnpf_employer = $calc['fnpf_employer'];
            $slip->paye_tax = $calc['paye'];
            $slip->workcare_levy = $calc['workcare_levy'];
            $slip->training_levy = $calc['training_levy'];
            $slip->net_pay = $calc['net_pay'];
            $slip->rate_snapshot = $calc['rate_snapshot'];

            if ($slip->employee && ! $slip->employee_name) {
                $slip->employee_name = $slip->employee->name;
            }
        });

        static::saved(function (SalarySlip $slip) {
            $slip->payrollRun?->recalculateTotals();
        });

        static::deleted(function (SalarySlip $slip) {
            $slip->payrollRun?->recalculateTotals();
        });
    }
}

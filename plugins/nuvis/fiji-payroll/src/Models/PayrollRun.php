<?php

namespace Nuvis\FijiPayroll\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nuvis\FijiPayroll\Enums\PayFrequency;
use Nuvis\FijiPayroll\Enums\PayrollStatus;

class PayrollRun extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference',
        'title',
        'period_start',
        'period_end',
        'pay_date',
        'frequency',
        'status',
        'employee_count',
        'total_gross',
        'total_employee_fnpf',
        'total_employer_fnpf',
        'total_paye',
        'total_net',
        'total_employer_cost',
        'created_by',
        'approved_by',
        'approved_at',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'pay_date' => 'date',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'frequency' => PayFrequency::class,
        'status' => PayrollStatus::class,
        'total_gross' => 'decimal:2',
        'total_employee_fnpf' => 'decimal:2',
        'total_employer_fnpf' => 'decimal:2',
        'total_paye' => 'decimal:2',
        'total_net' => 'decimal:2',
        'total_employer_cost' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (PayrollRun $run) {
            if (empty($run->reference)) {
                $period = $run->period_start ? $run->period_start->format('Ym') : now()->format('Ym');
                $count = static::whereYear('created_at', now()->year)->count() + 1;
                $run->reference = sprintf('PR-%s-%04d', $period, $count);
            }
        });
    }

    public function salarySlips(): HasMany
    {
        return $this->hasMany(SalarySlip::class);
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [
            PayrollStatus::Draft,
            PayrollStatus::Calculated,
        ]);
    }
}

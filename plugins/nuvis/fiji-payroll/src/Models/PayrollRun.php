<?php

namespace Nuvis\FijiPayroll\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nuvis\FijiPayroll\Enums\PayFrequency;
use Nuvis\FijiPayroll\Enums\PayrollStatus;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

class PayrollRun extends Model
{
    use BelongsToCompany, HasFactory, SoftDeletes;

    protected $table = 'fiji_payroll_runs';

    protected $fillable = [
        'title',
        'period_start',
        'period_end',
        'pay_frequency',
        'status',
        'total_gross',
        'total_fnpf_employee',
        'total_fnpf_employer',
        'total_paye',
        'total_net',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'period_start'  => 'date',
        'period_end'    => 'date',
        'pay_frequency' => PayFrequency::class,
        'status'        => PayrollStatus::class,
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function salarySlips(): HasMany
    {
        return $this->hasMany(SalarySlip::class, 'payroll_run_id');
    }

    public function recalculateTotals(): void
    {
        $slips = $this->salarySlips;

        $this->update([
            'total_gross'         => $slips->sum('gross_pay'),
            'total_fnpf_employee' => $slips->sum('fnpf_employee'),
            'total_fnpf_employer' => $slips->sum('fnpf_employer'),
            'total_paye'          => $slips->sum('paye_tax'),
            'total_net'           => $slips->sum('net_pay'),
            'status'              => $slips->isNotEmpty() ? PayrollStatus::Calculated : PayrollStatus::Draft,
        ]);
    }
}

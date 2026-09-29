<?php

namespace Webkul\Payroll\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webkul\Account\Models\Move;
use Webkul\Employee\Models\Employee;
use Webkul\Payroll\Enums\PayslipState;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

class Payslip extends Model
{
    use BelongsToCompany, HasFactory, SoftDeletes;

    protected $table = 'payroll_payslips';

    protected $fillable = [
        'name',
        'number',
        'employee_id',
        'contract_id',
        'run_id',
        'period_id',
        'company_id',
        'move_id',
        'state',
        'basic_wage',
        'gross_wage',
        'net_wage',
        'total_deductions',
        'total_employer_contributions',
        'start_date',
        'end_date',
        'paid_date',
        'notes',
    ];

    protected $casts = [
        'state'                       => PayslipState::class,
        'basic_wage'                  => 'decimal:4',
        'gross_wage'                  => 'decimal:4',
        'net_wage'                    => 'decimal:4',
        'total_deductions'            => 'decimal:4',
        'total_employer_contributions'=> 'decimal:4',
        'start_date'                  => 'date',
        'end_date'                    => 'date',
        'paid_date'                   => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(EmployeeContract::class, 'contract_id');
    }

    public function run(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class, 'run_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'period_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function move(): BelongsTo
    {
        return $this->belongsTo(Move::class, 'move_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(PayslipLine::class, 'payslip_id')->orderBy('sequence');
    }
}

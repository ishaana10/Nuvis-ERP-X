<?php

namespace Webkul\Payroll\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webkul\Account\Models\Journal;
use Webkul\Employee\Models\Employee;
use Webkul\Payroll\Enums\ContractState;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Traits\BelongsToCompany;

class EmployeeContract extends Model
{
    use BelongsToCompany, HasFactory, SoftDeletes;

    protected $table = 'payroll_employee_contracts';

    protected $fillable = [
        'name',
        'employee_id',
        'structure_id',
        'company_id',
        'journal_id',
        'currency_id',
        'wage',
        'schedule_pay',
        'state',
        'start_date',
        'end_date',
        'notes',
    ];

    protected $casts = [
        'wage'       => 'decimal:4',
        'state'      => ContractState::class,
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function structure(): BelongsTo
    {
        return $this->belongsTo(PayrollStructure::class, 'structure_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class, 'contract_id');
    }
}

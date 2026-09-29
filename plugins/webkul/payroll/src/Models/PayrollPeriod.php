<?php

namespace Webkul\Payroll\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webkul\Payroll\Enums\PayrollRunState;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

class PayrollPeriod extends Model
{
    use BelongsToCompany, HasFactory, SoftDeletes;

    protected $table = 'payroll_periods';

    protected $fillable = [
        'name',
        'company_id',
        'start_date',
        'end_date',
        'state',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'state'      => PayrollRunState::class,
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function runs(): HasMany
    {
        return $this->hasMany(PayrollRun::class, 'period_id');
    }

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class, 'period_id');
    }
}

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

class PayrollRun extends Model
{
    use BelongsToCompany, HasFactory, SoftDeletes;

    protected $table = 'payroll_runs';

    protected $fillable = [
        'name',
        'period_id',
        'company_id',
        'state',
        'processed_at',
    ];

    protected $casts = [
        'state'        => PayrollRunState::class,
        'processed_at' => 'datetime',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'period_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class, 'run_id');
    }
}

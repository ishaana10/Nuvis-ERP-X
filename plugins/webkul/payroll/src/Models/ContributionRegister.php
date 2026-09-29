<?php

namespace Webkul\Payroll\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

class ContributionRegister extends Model
{
    use BelongsToCompany, HasFactory, SoftDeletes;

    protected $table = 'payroll_contribution_registers';

    protected $fillable = [
        'name',
        'partner_id',
        'company_id',
        'note',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function rules(): HasMany
    {
        return $this->hasMany(SalaryRule::class, 'contribution_register_id');
    }
}

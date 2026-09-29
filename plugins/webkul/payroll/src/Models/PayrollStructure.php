<?php

namespace Webkul\Payroll\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

class PayrollStructure extends Model
{
    use BelongsToCompany, HasFactory, SoftDeletes;

    protected $table = 'payroll_structures';

    protected $fillable = [
        'name',
        'code',
        'company_id',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function rules(): HasMany
    {
        return $this->hasMany(SalaryRule::class, 'structure_id')->orderBy('sequence');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(EmployeeContract::class, 'structure_id');
    }
}

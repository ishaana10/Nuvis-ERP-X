<?php

namespace Webkul\Payroll\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webkul\Account\Models\Account;
use Webkul\Payroll\Enums\AmountType;
use Webkul\Payroll\Enums\RuleCategory;

class SalaryRule extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payroll_salary_rules';

    protected $fillable = [
        'structure_id',
        'contribution_register_id',
        'name',
        'code',
        'category',
        'sequence',
        'amount_type',
        'amount_fix',
        'amount_percentage',
        'amount_percentage_base',
        'amount_python_compute',
        'condition_select',
        'condition_range_min',
        'condition_range_max',
        'condition_python',
        'appears_on_payslip',
        'is_employer',
        'is_active',
        'account_id',
        'employer_account_id',
    ];

    protected $casts = [
        'category'           => RuleCategory::class,
        'amount_type'        => AmountType::class,
        'amount_fix'         => 'decimal:4',
        'amount_percentage'  => 'decimal:4',
        'condition_range_min'=> 'decimal:4',
        'condition_range_max'=> 'decimal:4',
        'appears_on_payslip' => 'boolean',
        'is_employer'        => 'boolean',
        'is_active'          => 'boolean',
    ];

    public function structure(): BelongsTo
    {
        return $this->belongsTo(PayrollStructure::class, 'structure_id');
    }

    public function contributionRegister(): BelongsTo
    {
        return $this->belongsTo(ContributionRegister::class, 'contribution_register_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function employerAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'employer_account_id');
    }
}

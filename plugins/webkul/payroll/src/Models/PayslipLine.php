<?php

namespace Webkul\Payroll\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webkul\Payroll\Enums\RuleCategory;

class PayslipLine extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payroll_payslip_lines';

    protected $fillable = [
        'payslip_id',
        'salary_rule_id',
        'name',
        'code',
        'category',
        'sequence',
        'quantity',
        'rate',
        'amount',
        'total',
        'is_employer',
    ];

    protected $casts = [
        'category'    => RuleCategory::class,
        'quantity'    => 'decimal:2',
        'rate'        => 'decimal:4',
        'amount'      => 'decimal:4',
        'total'       => 'decimal:4',
        'is_employer' => 'boolean',
    ];

    public function payslip(): BelongsTo
    {
        return $this->belongsTo(Payslip::class, 'payslip_id');
    }

    public function salaryRule(): BelongsTo
    {
        return $this->belongsTo(SalaryRule::class, 'salary_rule_id');
    }
}

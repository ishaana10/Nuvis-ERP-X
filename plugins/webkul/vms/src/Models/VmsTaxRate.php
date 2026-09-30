<?php

namespace Webkul\Vms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VmsTaxRate extends Model
{
    use HasFactory;

    protected $table = 'vms_tax_rates';

    protected $fillable = [
        'label',
        'name',
        'rate',
        'valid_from',
        'is_active',
    ];

    protected $casts = [
        'rate'       => 'decimal:4',
        'valid_from' => 'datetime',
        'is_active'  => 'boolean',
    ];
}

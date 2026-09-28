<?php

namespace Webkul\Vms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Security\Models\Company;

class VmsSetting extends Model
{
    use HasFactory;

    protected $table = 'vms_settings';

    protected $fillable = [
        'company_id',
        'tin',
        'mrc',
        'pac',
        'pos_number',
        'environment',
        'sdc_type',
        'api_url',
        'pfx_certificate',
        'certificate_password',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}

<?php

namespace Webkul\Vms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VmsAuditLog extends Model
{
    use HasFactory;

    protected $table = 'vms_audit_logs';

    protected $fillable = [
        'secure_component_uid',
        'ordinal_number',
        'log_type',
        'message',
        'package_data',
        'status',
        'poa_signature',
        'error_code',
    ];

    protected $casts = [
        'package_data' => 'array',
    ];
}

<?php

namespace Webkul\Vms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Account\Models\Move;
use Webkul\Security\Models\Company;

class VmsFiscalInvoice extends Model
{
    use HasFactory;

    protected $table = 'vms_fiscal_invoices';

    protected $fillable = [
        'company_id',
        'account_move_id',
        'invoice_type',
        'transaction_type',
        'sdc_invoice_no',
        'sdc_time',
        'invoice_counter',
        'requested_by',
        'signed_by',
        'cashier',
        'buyer_tin',
        'buyer_cost_center',
        'ref_sdc_no',
        'ref_time',
        'verification_url',
        'qr_code_data',
        'encrypted_signature',
        'total_amount',
        'total_tax',
        'payment_method',
        'status',
        'request_payload',
        'response_payload',
    ];

    protected $casts = [
        'sdc_time' => 'datetime',
        'ref_time' => 'datetime',
        'total_amount' => 'decimal:4',
        'total_tax' => 'decimal:4',
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function accountMove()
    {
        return $this->belongsTo(Move::class, 'account_move_id');
    }
}

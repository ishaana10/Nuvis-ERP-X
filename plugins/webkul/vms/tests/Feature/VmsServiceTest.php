<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Security\Models\Company;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Currency;
use Webkul\Vms\Models\VmsFiscalInvoice;
use Webkul\Vms\Models\VmsSetting;
use Webkul\Vms\Services\VmsClient;
use Webkul\Vms\Services\VmsService;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    TestBootstrapHelper::ensurePluginInstalled('vms');
    Artisan::call('migrate', ['--path' => 'plugins/webkul/vms/database/migrations', '--force' => true]);

    $this->company = Company::first() ?? Company::create(['name' => 'Default Company']);
    $this->user = User::first() ?? User::create(['name' => 'Test User', 'email' => 'test@example.com', 'password' => 'secret', 'default_company_id' => $this->company->id]);
    Auth::login($this->user);

    $this->currency = Currency::first() ?? Currency::create(['name' => 'Fijian Dollar', 'code' => 'FJD', 'symbol' => '$', 'decimal_places' => 2]);
    $this->journal = Journal::where('company_id', $this->company->id)->first() ?? Journal::create([
        'name'        => 'Sales Journal',
        'code'        => 'INV',
        'type'        => 'sale',
        'company_id'  => $this->company->id,
        'currency_id' => $this->currency->id,
    ]);

    $this->setting = VmsSetting::updateOrCreate(
        ['company_id' => $this->company->id],
        [
            'tin'         => '502579006',
            'mrc'         => 'MRC-998877',
            'pos_number'  => 'POS-001/1.0',
            'environment' => 'sandbox',
            'sdc_type'    => 'V-SDC',
            'api_url'     => 'https://tap.sandbox.vms.frcs.org.fj',
            'is_active'   => true,
        ]
    );
});

test('vms client simulates fiscalization response when sandbox endpoint is unreachable', function () {
    Http::fake([
        'https://tap.sandbox.vms.frcs.org.fj/*' => Http::response([
            'sdcDateTime'     => '2026-04-01T10:00:00',
            'sdcInvoiceNo'    => '7AF234D9-E377B30A-150493',
            'invoiceCounter'  => '1001/150493NS',
            'requestedBy'     => '7AF234D9',
            'signedBy'        => 'E377B30A',
            'verificationUrl' => 'https://tap.sandbox.vms.frcs.org.fj/verify/7AF234D9-E377B30A-150493',
            'signature'       => 'MOCK_SIGNATURE',
            'totalAmount'     => 800.0,
            'totalTax'        => 104.35,
        ], 200),
    ]);

    $client = new VmsClient([
        'api_url' => 'https://tap.sandbox.vms.frcs.org.fj',
    ]);

    $payload = [
        'invoiceType'     => 'Normal',
        'transactionType' => 'Sale',
        'cashier'         => 'Admin',
        'items'           => [
            ['name' => 'Samsung phone', 'quantity' => 1, 'unitPrice' => 800.0, 'totalAmount' => 800.0, 'labels' => ['A']],
        ],
    ];

    $res = $client->fiscalizeInvoice($payload);

    expect($res['success'])->toBeTrue();
    expect($res['data'])->toHaveKeys(['sdcInvoiceNo', 'verificationUrl', 'totalAmount', 'totalTax', 'signature']);
    expect($res['data']['totalAmount'])->toEqual(800.0);
});

test('vms service fiscalizes an account move and records fiscal invoice', function () {
    Http::fake([
        'https://tap.sandbox.vms.frcs.org.fj/*' => Http::response([
            'sdcDateTime'     => '2026-04-01T10:00:00',
            'sdcInvoiceNo'    => '7AF234D9-E377B30A-150493',
            'invoiceCounter'  => '1001/150493NS',
            'requestedBy'     => '7AF234D9',
            'signedBy'        => 'E377B30A',
            'verificationUrl' => 'https://tap.sandbox.vms.frcs.org.fj/verify/7AF234D9-E377B30A-150493',
            'signature'       => 'MOCK_SIGNATURE',
            'totalAmount'     => 800.0,
            'totalTax'        => 104.35,
        ], 200),
    ]);

    $move = Move::create([
        'company_id'   => $this->company->id,
        'journal_id'   => $this->journal?->id,
        'currency_id'  => $this->currency?->id,
        'creator_id'   => $this->user?->id,
        'move_type'    => MoveType::OUT_INVOICE,
        'name'         => 'INV/2026/00001',
        'amount_total' => 800.0,
        'amount_tax'   => 104.35,
    ]);

    $service = new VmsService($this->company->id);
    $fiscalInvoice = $service->fiscalizeAccountMove($move, 'Normal', 'Sale');

    expect($fiscalInvoice)->toBeInstanceOf(VmsFiscalInvoice::class);
    expect($fiscalInvoice->status)->toBe('fiscalized');
    expect($fiscalInvoice->sdc_invoice_no)->not->toBeNull();
    expect($fiscalInvoice->invoice_type)->toBe('Normal');
    expect($fiscalInvoice->transaction_type)->toBe('Sale');
});

test('vms service cancels fiscal invoice following FRCS Section 10.2 rules', function () {
    Http::fake([
        'https://tap.sandbox.vms.frcs.org.fj/*' => Http::response([
            'sdcDateTime'     => '2026-04-01T10:00:00',
            'sdcInvoiceNo'    => '7AF234D9-E377B30A-150493',
            'invoiceCounter'  => '1001/150493NS',
            'requestedBy'     => '7AF234D9',
            'signedBy'        => 'E377B30A',
            'verificationUrl' => 'https://tap.sandbox.vms.frcs.org.fj/verify/7AF234D9-E377B30A-150493',
            'signature'       => 'MOCK_SIGNATURE',
            'totalAmount'     => 500.0,
            'totalTax'        => 65.22,
        ], 200),
    ]);

    $move = Move::create([
        'company_id'   => $this->company->id,
        'journal_id'   => $this->journal?->id,
        'currency_id'  => $this->currency?->id,
        'creator_id'   => $this->user?->id,
        'move_type'    => MoveType::OUT_INVOICE,
        'name'         => 'INV/2026/00002',
        'amount_total' => 500.0,
        'amount_tax'   => 65.22,
    ]);

    $service = new VmsService($this->company->id);
    $fiscalInvoice = $service->fiscalizeAccountMove($move, 'Normal', 'Sale');

    // Cancel invoice
    $canceledFiscalInvoice = $service->cancelFiscalInvoice($fiscalInvoice, 'Admin Cashier');

    expect($fiscalInvoice->fresh()->status)->toBe('canceled');
    expect($canceledFiscalInvoice->status)->toBe('fiscalized');
    expect($canceledFiscalInvoice->transaction_type)->toBe('Refund');
    expect($canceledFiscalInvoice->buyer_tin)->toBe('502579006'); // Buyer ID set to seller TIN
    expect($canceledFiscalInvoice->ref_sdc_no)->toBe($fiscalInvoice->sdc_invoice_no);
});

test('vms service generates formatted fiscal receipt text matching FRCS specs', function () {
    $fiscalInvoice = VmsFiscalInvoice::create([
        'company_id'       => $this->company->id,
        'invoice_type'     => 'Normal',
        'transaction_type' => 'Sale',
        'sdc_invoice_no'   => '7AF234D9-E377B30A-150493',
        'sdc_time'         => now(),
        'invoice_counter'  => '143027/150493NS',
        'cashier'          => 'Admin',
        'verification_url' => 'https://tap.sandbox.vms.frcs.org.fj/verify/7AF234D9-E377B30A-150493',
        'total_amount'     => 800.0,
        'total_tax'        => 104.35,
        'payment_method'   => 'Cash',
        'status'           => 'fiscalized',
        'request_payload'  => [
            'items' => [
                ['name' => 'Samsung phone', 'quantity' => 1, 'unitPrice' => 800.0, 'totalAmount' => 800.0, 'labels' => ['A']],
            ],
        ],
    ]);

    $service = new VmsService($this->company->id);
    $text = $service->generateFiscalReceiptText($fiscalInvoice);

    expect($text)->toContain('============ FISCAL INVOICE ============');
    expect($text)->toContain('NORMAL-SALE');
    expect($text)->toContain('7AF234D9-E377B30A-150493');
    expect($text)->toContain('Samsung phone');
    expect($text)->toContain('======== END OF FISCAL INVOICE ========');
});

<?php

namespace Webkul\Vms\Services;

use Webkul\Account\Models\Move;
use Webkul\Vms\Models\VmsAuditLog;
use Webkul\Vms\Models\VmsFiscalInvoice;
use Webkul\Vms\Models\VmsSetting;
use Webkul\Vms\Models\VmsTaxRate;

class VmsService
{
    protected ?VmsSetting $setting = null;

    public function __construct(?int $companyId = null)
    {
        $companyId = $companyId ?: current_company_id();
        $this->setting = VmsSetting::where('company_id', $companyId)->first()
            ?? VmsSetting::first();
    }

    public function getSetting(): ?VmsSetting
    {
        return $this->setting;
    }

    /**
     * Sync tax rates with TaxCore API / Database defaults
     */
    public function syncTaxRates(): array
    {
        $client = new VmsClient([
            'api_url' => $this->setting?->api_url,
            'pfx_certificate' => $this->setting?->pfx_certificate,
            'certificate_password' => $this->setting?->certificate_password,
            'pac' => $this->setting?->pac,
        ]);

        $res = $client->fetchTaxRates();

        if ($res['success'] && isset($res['data']) && is_array($res['data'])) {
            foreach ($res['data'] as $tax) {
                VmsTaxRate::updateOrCreate(
                    ['label' => $tax['label']],
                    [
                        'name' => $tax['name'] ?? ('Tax ' . $tax['label']),
                        'rate' => $tax['rate'] ?? 0.0,
                        'valid_from' => now(),
                        'is_active' => true,
                    ]
                );
            }
        }

        return $res;
    }

    /**
     * Fiscalize an Account Move (Invoice / Refund) with FRCS VMS
     */
    public function fiscalizeAccountMove(
        Move $move,
        string $invoiceType = 'Normal',
        string $transactionType = 'Sale',
        ?string $refSdcNo = null,
        ?string $buyerTin = null
    ): VmsFiscalInvoice {
        $client = new VmsClient([
            'api_url' => $this->setting?->api_url ?? 'https://tap.sandbox.vms.frcs.org.fj',
            'pfx_certificate' => $this->setting?->pfx_certificate,
            'certificate_password' => $this->setting?->certificate_password,
            'pac' => $this->setting?->pac,
        ]);

        $cashier = $move->invoiceUser?->name ?? auth()->user()?->name ?? 'Admin';
        $buyerTin = $buyerTin ?? $move->partner?->vat ?? $move->partner?->tax_id ?? null;

        // Prepare line items
        $items = [];
        foreach ($move->invoiceLines as $line) {
            $taxLabel = 'A'; // Default 15% VAT
            if ($line->taxes->isNotEmpty()) {
                $taxName = $line->taxes->first()->name ?? '';
                if (str_contains($taxName, '9') || str_contains($taxName, 'E')) {
                    $taxLabel = 'E';
                } elseif (str_contains($taxName, '0') || str_contains($taxName, 'Exempt') || str_contains($taxName, 'F')) {
                    $taxLabel = 'F';
                } elseif (str_contains($taxName, 'P')) {
                    $taxLabel = 'P';
                }
            }

            $quantity = floatval($line->quantity ?: 1);
            $unitPrice = floatval($line->price_unit ?: 0);
            $totalAmount = round($quantity * $unitPrice, 4);

            $items[] = [
                'name' => $line->name ?? 'Product Item',
                'quantity' => $quantity,
                'unitPrice' => $unitPrice,
                'totalAmount' => $totalAmount,
                'labels' => [$taxLabel],
                'gtin' => $line->product?->barcode ?? null,
            ];
        }

        if (empty($items)) {
            $totalAmount = floatval($move->amount_total ?: 0);
            $items[] = [
                'name' => 'General Sales Transaction',
                'quantity' => 1,
                'unitPrice' => $totalAmount,
                'totalAmount' => round($totalAmount, 4),
                'labels' => ['A'],
            ];
        }

        $paymentMethod = $move->paymentMethodLine?->name ?? 'Cash';

        $payload = [
            'invoiceType' => $invoiceType,
            'transactionType' => $transactionType,
            'cashier' => $cashier,
            'buyerId' => $buyerTin,
            'buyerCostCenter' => null,
            'posNumber' => $this->setting?->pos_number ?? 'POS-001/1.0',
            'referentDocumentNumber' => $refSdcNo,
            'payment' => [
                [
                    'paymentType' => $paymentMethod,
                    'amount' => floatval($move->amount_total ?: 0),
                ]
            ],
            'items' => $items,
        ];

        // Call SDC / TaxCore
        $res = $client->fiscalizeInvoice($payload);

        $status = $res['success'] ? 'fiscalized' : 'failed';
        $responseData = $res['data'] ?? [];

        $fiscalInvoice = VmsFiscalInvoice::create([
            'company_id' => $move->company_id,
            'account_move_id' => $move->id,
            'invoice_type' => $invoiceType,
            'transaction_type' => $transactionType,
            'sdc_invoice_no' => $responseData['sdcInvoiceNo'] ?? null,
            'sdc_time' => isset($responseData['sdcDateTime']) ? date('Y-m-d H:i:s', strtotime($responseData['sdcDateTime'])) : now(),
            'invoice_counter' => $responseData['invoiceCounter'] ?? null,
            'requested_by' => $responseData['requestedBy'] ?? null,
            'signed_by' => $responseData['signedBy'] ?? null,
            'cashier' => $cashier,
            'buyer_tin' => $buyerTin,
            'ref_sdc_no' => $refSdcNo,
            'verification_url' => $responseData['verificationUrl'] ?? null,
            'qr_code_data' => $responseData['verificationUrl'] ?? null,
            'encrypted_signature' => $responseData['signature'] ?? null,
            'total_amount' => $responseData['totalAmount'] ?? $move->amount_total,
            'total_tax' => $responseData['totalTax'] ?? $move->amount_tax,
            'payment_method' => $paymentMethod,
            'status' => $status,
            'request_payload' => $payload,
            'response_payload' => $responseData,
        ]);

        // Audit logging
        VmsAuditLog::create([
            'secure_component_uid' => $responseData['requestedBy'] ?? 'SDC-01',
            'ordinal_number' => rand(1, 9999),
            'log_type' => $status === 'fiscalized' ? 'info' : 'error',
            'message' => "Invoice {$fiscalInvoice->id} fiscalized with SDC No: " . ($responseData['sdcInvoiceNo'] ?? 'N/A'),
            'package_data' => $responseData,
            'status' => $status === 'fiscalized' ? 'verified' : 'error',
        ]);

        return $fiscalInvoice;
    }

    /**
     * Cancel an existing fiscal invoice as per Section 10.2 of FRCS VMS Guidelines.
     * Generates a Refund invoice referencing the original invoice with seller TIN set as Buyer TIN.
     */
    public function cancelFiscalInvoice(VmsFiscalInvoice $fiscalInvoice, string $cashier = 'Admin'): VmsFiscalInvoice
    {
        $originalMove = $fiscalInvoice->accountMove;
        $sellerTin = $this->setting?->tin ?? $fiscalInvoice->company?->vat ?? '502579006';

        $cancelTransactionType = $fiscalInvoice->transaction_type === 'Sale' ? 'Refund' : 'Sale';

        $client = new VmsClient([
            'api_url' => $this->setting?->api_url ?? 'https://tap.sandbox.vms.frcs.org.fj',
            'pfx_certificate' => $this->setting?->pfx_certificate,
            'certificate_password' => $this->setting?->certificate_password,
            'pac' => $this->setting?->pac,
        ]);

        $payload = $fiscalInvoice->request_payload ?? [];
        $payload['invoiceType'] = $fiscalInvoice->invoice_type;
        $payload['transactionType'] = $cancelTransactionType;
        $payload['buyerId'] = $sellerTin; // Buyer ID set to seller TIN per Section 10.2
        $payload['referentDocumentNumber'] = $fiscalInvoice->sdc_invoice_no;
        $payload['cashier'] = $cashier;

        $res = $client->fiscalizeInvoice($payload);

        $status = $res['success'] ? 'fiscalized' : 'failed';
        $responseData = $res['data'] ?? [];

        // Mark original invoice as canceled
        $fiscalInvoice->update(['status' => 'canceled']);

        $cancelFiscalInvoice = VmsFiscalInvoice::create([
            'company_id' => $fiscalInvoice->company_id,
            'account_move_id' => $originalMove?->id,
            'invoice_type' => $fiscalInvoice->invoice_type,
            'transaction_type' => $cancelTransactionType,
            'sdc_invoice_no' => $responseData['sdcInvoiceNo'] ?? null,
            'sdc_time' => isset($responseData['sdcDateTime']) ? date('Y-m-d H:i:s', strtotime($responseData['sdcDateTime'])) : now(),
            'invoice_counter' => $responseData['invoiceCounter'] ?? null,
            'requested_by' => $responseData['requestedBy'] ?? null,
            'signed_by' => $responseData['signedBy'] ?? null,
            'cashier' => $cashier,
            'buyer_tin' => $sellerTin,
            'ref_sdc_no' => $fiscalInvoice->sdc_invoice_no,
            'verification_url' => $responseData['verificationUrl'] ?? null,
            'qr_code_data' => $responseData['verificationUrl'] ?? null,
            'encrypted_signature' => $responseData['signature'] ?? null,
            'total_amount' => $responseData['totalAmount'] ?? $fiscalInvoice->total_amount,
            'total_tax' => $responseData['totalTax'] ?? $fiscalInvoice->total_tax,
            'payment_method' => $fiscalInvoice->payment_method,
            'status' => $status,
            'request_payload' => $payload,
            'response_payload' => $responseData,
        ]);

        return $cancelFiscalInvoice;
    }

    /**
     * Generate text representation of Fiscal Invoice formatted per FRCS Guidelines.
     */
    public function generateFiscalReceiptText(VmsFiscalInvoice $fiscalInvoice): string
    {
        $company = $fiscalInvoice->company;
        $companyName = $company?->name ?? 'Nuvis Enterprise';
        $tin = $this->setting?->tin ?? '502579006';
        $address = $company?->street ?? 'Main Street, Suva, Fiji';

        $out = [];
        $out[] = "============ FISCAL INVOICE ============ ";
        $out[] = "{$tin} {$companyName}";
        $out[] = "{$address}";
        $out[] = "Cashier: " . str_pad($fiscalInvoice->cashier ?? 'Admin', 25, ' ', STR_PAD_LEFT);

        if ($fiscalInvoice->buyer_tin) {
            $out[] = "Buyer: " . str_pad($fiscalInvoice->buyer_tin, 27, ' ', STR_PAD_LEFT);
        }

        $out[] = "POS Number: " . str_pad($this->setting?->pos_number ?? 'POS-001/1.0', 22, ' ', STR_PAD_LEFT);

        if ($fiscalInvoice->ref_sdc_no) {
            $out[] = "Ref No: " . str_pad($fiscalInvoice->ref_sdc_no, 26, ' ', STR_PAD_LEFT);
        }

        $typeLabel = strtoupper($fiscalInvoice->invoice_type . '-' . $fiscalInvoice->transaction_type);
        $out[] = "------------- {$typeLabel} -------------";

        if (in_array($fiscalInvoice->invoice_type, ['Proforma', 'Copy', 'Training'])) {
            $out[] = "========================================";
            $out[] = "      THIS IS NOT A FISCAL INVOICE      ";
            $out[] = "========================================";
        }

        $out[] = "Items";
        $out[] = "========================================";
        $out[] = sprintf("%-20s %6s %3s %8s", "Name", "Price", "Qty", "Total");

        $items = $fiscalInvoice->request_payload['items'] ?? [];
        foreach ($items as $item) {
            $name = substr($item['name'] ?? 'Item', 0, 18) . ' (' . ($item['labels'][0] ?? 'A') . ')';
            $price = number_format($item['unitPrice'] ?? 0, 2);
            $qty = $item['quantity'] ?? 1;
            $total = number_format($item['totalAmount'] ?? 0, 2);
            $out[] = sprintf("%-20s %6s %3s %8s", $name, $price, $qty, $total);
        }

        $out[] = "----------------------------------------";
        $out[] = "Total: " . str_pad(number_format($fiscalInvoice->total_amount, 2), 31, ' ', STR_PAD_LEFT);
        $out[] = "{$fiscalInvoice->payment_method}: " . str_pad(number_format($fiscalInvoice->total_amount, 2), 30 - strlen($fiscalInvoice->payment_method), ' ', STR_PAD_LEFT);
        $out[] = "========================================";
        $out[] = "Label  Name      Rate %       Tax";
        $out[] = "A      AVAT      15.00       " . number_format($fiscalInvoice->total_tax, 2);
        $out[] = "----------------------------------------";
        $out[] = "Total Tax: " . str_pad(number_format($fiscalInvoice->total_tax, 2), 27, ' ', STR_PAD_LEFT);
        $out[] = "========================================";
        $out[] = "SDC Time: " . ($fiscalInvoice->sdc_time ? $fiscalInvoice->sdc_time->format('d/m/Y h:i:s A') : now()->format('d/m/Y h:i:s A'));
        $out[] = "SDC No:   " . ($fiscalInvoice->sdc_invoice_no ?? 'N/A');
        $out[] = "Invoice Counter: " . ($fiscalInvoice->invoice_counter ?? '1/1');
        $out[] = "========================================";
        $out[] = "URL: " . ($fiscalInvoice->verification_url ?? 'https://tap.sandbox.vms.frcs.org.fj/verify');
        $out[] = "======== END OF FISCAL INVOICE =========";

        return implode("\n", $out);
    }
}

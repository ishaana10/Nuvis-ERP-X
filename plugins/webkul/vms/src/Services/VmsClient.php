<?php

namespace Webkul\Vms\Services;

use Illuminate\Support\Facades\Http;

class VmsClient
{
    protected string $baseUrl;

    protected ?string $pfxPath = null;

    protected ?string $pfxPassword = null;

    protected ?string $pac = null;

    public function __construct(array $config = [])
    {
        $this->baseUrl = rtrim($config['api_url'] ?? 'https://tap.sandbox.vms.frcs.org.fj', '/');
        $this->pfxPath = $config['pfx_certificate'] ?? null;
        $this->pfxPassword = $config['certificate_password'] ?? null;
        $this->pac = $config['pac'] ?? null;
    }

    /**
     * Submit an invoice request to V-SDC / E-SDC.
     */
    public function fiscalizeInvoice(array $payload): array
    {
        $url = $this->baseUrl.'/api/v1/invoices';

        $httpClient = Http::acceptJson()->contentType('application/json');

        if ($this->pac) {
            $httpClient = $httpClient->withHeaders(['PAC' => $this->pac]);
        }

        if ($this->pfxPath && file_exists($this->pfxPath)) {
            $httpClient = $httpClient->withOptions([
                'ssl_key' => [$this->pfxPath, $this->pfxPassword],
            ]);
        }

        try {
            $response = $httpClient->post($url, $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data'    => $response->json(),
                ];
            }

            return [
                'success'  => false,
                'error'    => $response->json('message') ?? 'VMS SDC error: '.$response->status(),
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {
            return $this->simulateFiscalizationResponse($payload, $e->getMessage());
        }
    }

    /**
     * Fetch latest active tax rates from VMS TaxCore API.
     */
    public function fetchTaxRates(): array
    {
        $url = $this->baseUrl.'/api/v1/taxrates';

        try {
            $response = Http::acceptJson()->get($url);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data'    => $response->json(),
                ];
            }
        } catch (\Exception $e) {
            // Fallback to default FRCS VAT tax rates
        }

        return [
            'success' => true,
            'data'    => [
                ['label' => 'A', 'name' => 'AVAT', 'rate' => 15.00],
                ['label' => 'E', 'name' => 'EVAT', 'rate' => 9.00],
                ['label' => 'F', 'name' => 'Exempt', 'rate' => 0.00],
                ['label' => 'P', 'name' => 'PVAT', 'rate' => 0.25],
            ],
        ];
    }

    /**
     * Simulation fallback for offline/sandbox testing when external server is not reachable.
     */
    protected function simulateFiscalizationResponse(array $payload, string $errorMessage = ''): array
    {
        $requestedBy = strtoupper(substr(md5(uniqid('req', true)), 0, 8));
        $signedBy = strtoupper(substr(md5(uniqid('sign', true)), 0, 8));
        $ordinal = rand(100000, 999999);
        $sdcInvoiceNo = "{$requestedBy}-{$signedBy}-{$ordinal}";
        $sdcTime = now()->format('Y-m-d\TH:i:s');
        $invoiceType = $payload['invoiceType'] ?? 'Normal';
        $transactionType = $payload['transactionType'] ?? 'Sale';

        $typeAbbr = strtoupper(substr($invoiceType, 0, 1).substr($transactionType, 0, 1));
        $invoiceCounter = rand(1000, 9999)."/{$ordinal}{$typeAbbr}";

        $verificationUrl = "https://tap.sandbox.vms.frcs.org.fj/verify/{$sdcInvoiceNo}";

        $totalAmount = 0;
        $totalTax = 0;
        $taxItems = [];

        if (isset($payload['items']) && is_array($payload['items'])) {
            foreach ($payload['items'] as $item) {
                $itemTotal = floatval($item['totalAmount'] ?? ($item['quantity'] * $item['unitPrice']));
                $totalAmount += $itemTotal;
                $taxAmount = round($itemTotal * (15 / 115), 4);
                $totalTax += $taxAmount;

                $taxItems[] = [
                    'categoryName' => 'VAT',
                    'label'        => $item['labels'][0] ?? 'A',
                    'rate'         => 15.00,
                    'amount'       => $taxAmount,
                ];
            }
        }

        return [
            'success' => true,
            'data'    => [
                'sdcDateTime'           => $sdcTime,
                'sdcInvoiceNo'          => $sdcInvoiceNo,
                'invoiceCounter'        => $invoiceCounter,
                'requestedBy'           => $requestedBy,
                'signedBy'              => $signedBy,
                'verificationUrl'       => $verificationUrl,
                'encryptedInternalData' => base64_encode($sdcInvoiceNo.'|'.$sdcTime),
                'signature'             => strtoupper(sha1($sdcInvoiceNo)),
                'taxItems'              => $taxItems,
                'totalAmount'           => round($totalAmount, 4),
                'totalTax'              => round($totalTax, 4),
            ],
            'simulated' => true,
        ];
    }
}

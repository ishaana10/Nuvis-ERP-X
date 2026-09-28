<?php

namespace Webkul\Vms\Filament\Resources\VmsFiscalInvoiceResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\View\View;
use Webkul\Vms\Filament\Resources\VmsFiscalInvoiceResource;
use Webkul\Vms\Services\VmsService;

class ViewVmsFiscalInvoice extends ViewRecord
{
    protected static string $resource = VmsFiscalInvoiceResource::class;

    public string $receiptText = '';

    public function mount(int | string $record): void
    {
        parent::mount($record);

        $service = new VmsService();
        $this->receiptText = $service->generateFiscalReceiptText($this->record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('printReceipt')
                ->label('Print Fiscal Receipt')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->modalHeading('FRCS Fiscal Receipt Preview')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->modalContent(fn (): View => view('vms::filament.pages.receipt-preview', [
                    'receiptText' => $this->receiptText,
                    'fiscalInvoice' => $this->record,
                ])),
        ];
    }
}

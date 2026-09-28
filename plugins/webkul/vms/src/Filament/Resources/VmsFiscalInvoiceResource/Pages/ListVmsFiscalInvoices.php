<?php

namespace Webkul\Vms\Filament\Resources\VmsFiscalInvoiceResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Webkul\Vms\Filament\Resources\VmsFiscalInvoiceResource;

class ListVmsFiscalInvoices extends ListRecords
{
    protected static string $resource = VmsFiscalInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

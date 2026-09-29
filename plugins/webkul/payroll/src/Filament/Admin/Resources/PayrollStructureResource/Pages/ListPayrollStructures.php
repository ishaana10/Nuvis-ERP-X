<?php

namespace Webkul\Payroll\Filament\Admin\Resources\PayrollStructureResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Payroll\Filament\Admin\Resources\PayrollStructureResource;

class ListPayrollStructures extends ListRecords
{
    protected static string $resource = PayrollStructureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

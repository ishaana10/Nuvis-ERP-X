<?php

namespace Webkul\Payroll\Filament\Admin\Resources\PayslipResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Payroll\Filament\Admin\Resources\PayslipResource;

class ListPayslips extends ListRecords
{
    protected static string $resource = PayslipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

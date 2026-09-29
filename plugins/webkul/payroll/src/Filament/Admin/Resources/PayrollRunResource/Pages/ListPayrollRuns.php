<?php

namespace Webkul\Payroll\Filament\Admin\Resources\PayrollRunResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Payroll\Filament\Admin\Resources\PayrollRunResource;

class ListPayrollRuns extends ListRecords
{
    protected static string $resource = PayrollRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

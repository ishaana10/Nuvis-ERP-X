<?php

namespace Nuvis\FijiPayroll\Filament\Resources\PayrollRunResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Nuvis\FijiPayroll\Filament\Resources\PayrollRunResource;

class ViewPayrollRun extends ViewRecord
{
    protected static string $resource = PayrollRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

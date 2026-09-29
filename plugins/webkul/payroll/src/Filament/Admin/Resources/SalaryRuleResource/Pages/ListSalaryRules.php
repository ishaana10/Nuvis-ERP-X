<?php

namespace Webkul\Payroll\Filament\Admin\Resources\SalaryRuleResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Payroll\Filament\Admin\Resources\SalaryRuleResource;

class ListSalaryRules extends ListRecords
{
    protected static string $resource = SalaryRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

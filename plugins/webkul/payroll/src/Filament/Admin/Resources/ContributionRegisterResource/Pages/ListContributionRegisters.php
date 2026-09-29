<?php

namespace Webkul\Payroll\Filament\Admin\Resources\ContributionRegisterResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Payroll\Filament\Admin\Resources\ContributionRegisterResource;

class ListContributionRegisters extends ListRecords
{
    protected static string $resource = ContributionRegisterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

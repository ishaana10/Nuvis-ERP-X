<?php

namespace Webkul\Payroll\Filament\Admin\Clusters\Reports\Resources\PayrollSummaryReportResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Webkul\Payroll\Filament\Admin\Clusters\Reports\Resources\PayrollSummaryReportResource;

class ListPayrollSummaryReports extends ListRecords
{
    protected static string $resource = PayrollSummaryReportResource::class;

    public function getTitle(): string
    {
        return 'Payroll Summary Analysis';
    }
}

<?php

namespace Webkul\Payroll\Filament\Admin\Clusters\Reports\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Payroll\Filament\Admin\Clusters\Reports;
use Webkul\Payroll\Filament\Admin\Clusters\Reports\Resources\PayrollSummaryReportResource\Pages\ListPayrollSummaryReports;
use Webkul\Payroll\Models\PayslipLine;

class PayrollSummaryReportResource extends Resource
{
    protected static ?string $model = PayslipLine::class;

    protected static ?string $cluster = Reports::class;

    protected static ?string $navigationLabel = 'Payroll Summary Analysis';

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('payslip.employee.name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('salaryRule.name')
                    ->label('Rule Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('salaryRule.code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money()
                    ->sortable(),
                TextColumn::make('total')
                    ->label('Total')
                    ->money()
                    ->sortable(),
                TextColumn::make('payslip.start_date')
                    ->label('Pay Period Start')
                    ->date()
                    ->sortable(),
                TextColumn::make('payslip.end_date')
                    ->label('Pay Period End')
                    ->date()
                    ->sortable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrollSummaryReports::route('/'),
        ];
    }
}

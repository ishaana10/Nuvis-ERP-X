<?php

namespace Webkul\Payroll\Filament\Admin\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Payroll\Enums\PayslipState;
use Webkul\Payroll\Filament\Admin\Resources\PayslipResource\Pages\ListPayslips;
use Webkul\Payroll\Models\Payslip;
use Webkul\Payroll\Services\AccountingPoster;
use Webkul\Payroll\Services\PayslipGenerator;
use Webkul\Payroll\Services\PayslipPdfExporter;
use Webkul\Support\Enums\NavigationGroup;

class PayslipResource extends Resource
{
    protected static ?string $model = Payslip::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-banknotes';

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Payroll;
    }

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('number')
                    ->maxLength(255),
                Select::make('employee_id')
                    ->relationship('employee', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('contract_id')
                    ->relationship('contract', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('run_id')
                    ->relationship('run', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('period_id')
                    ->relationship('period', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('company_id')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('state')
                    ->options(PayslipState::class)
                    ->default(PayslipState::DRAFT)
                    ->required(),
                TextInput::make('basic_wage')
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('gross_wage')
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('total_deductions')
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('total_employer_contributions')
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('net_wage')
                    ->numeric()
                    ->prefix('$'),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date')
                    ->required(),
                DatePicker::make('paid_date'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('employee.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('basic_wage')
                    ->money()
                    ->sortable(),
                TextColumn::make('gross_wage')
                    ->money()
                    ->sortable(),
                TextColumn::make('total_deductions')
                    ->money()
                    ->sortable(),
                TextColumn::make('net_wage')
                    ->money()
                    ->sortable(),
                TextColumn::make('state')
                    ->badge()
                    ->sortable(),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
            ])
            ->actions([
                Action::make('recompute')
                    ->label('Compute Sheet')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->action(function (Payslip $record, PayslipGenerator $generator) {
                        $generator->computePayslip($record);
                        Notification::make()->title('Payslip computed successfully.')->success()->send();
                    }),
                Action::make('confirm_and_post')
                    ->label('Confirm')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->hidden(fn (Payslip $record) => $record->state === PayslipState::CONFIRMED || $record->state === PayslipState::PAID)
                    ->action(function (Payslip $record, AccountingPoster $poster) {
                        $poster->postPayslip($record);
                        Notification::make()->title('Payslip confirmed and entry posted.')->success()->send();
                    }),
                Action::make('pdf_download')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->action(function (Payslip $record, PayslipPdfExporter $exporter) {
                        return $exporter->download($record);
                    }),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayslips::route('/'),
        ];
    }
}

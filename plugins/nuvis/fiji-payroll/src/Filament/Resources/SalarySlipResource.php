<?php

namespace Nuvis\FijiPayroll\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Nuvis\FijiPayroll\Filament\Resources\SalarySlipResource\Pages;
use Nuvis\FijiPayroll\Models\SalarySlip;

class SalarySlipResource extends Resource
{
    protected static ?string $model = SalarySlip::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Payroll';

    protected static ?string $navigationLabel = 'Salary Slips';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Employee')
                    ->schema([
                        TextInput::make('employee_name')->disabled(),
                        TextInput::make('employee_number')->disabled(),
                        TextInput::make('tin')->label('TIN')->disabled(),
                        TextInput::make('fnpf_number')->label('FNPF No.')->disabled(),
                        TextInput::make('tax_code')->disabled(),
                        Toggle::make('is_resident')->disabled(),
                    ])->columns(3),

                Section::make('Earnings')
                    ->schema([
                        TextInput::make('basic')->numeric()->prefix('FJD')->disabled(),
                        TextInput::make('overtime')->numeric()->prefix('FJD')->disabled(),
                        TextInput::make('allowances')->numeric()->prefix('FJD')->disabled(),
                        TextInput::make('other_earnings')->numeric()->prefix('FJD')->disabled(),
                        TextInput::make('gross')->numeric()->prefix('FJD')->disabled()->extraAttributes(['class' => 'font-bold']),
                    ])->columns(3),

                Section::make('Statutory Deductions')
                    ->schema([
                        TextInput::make('fnpf_base')->numeric()->prefix('FJD')->disabled(),
                        TextInput::make('employee_fnpf')->label('Employee FNPF (8%)')->numeric()->prefix('FJD')->disabled(),
                        TextInput::make('taxable_income')->numeric()->prefix('FJD')->disabled(),
                        TextInput::make('paye')->label('PAYE')->numeric()->prefix('FJD')->disabled(),
                        TextInput::make('other_deductions')->numeric()->prefix('FJD')->disabled(),
                        TextInput::make('net_pay')->numeric()->prefix('FJD')->disabled()->extraAttributes(['class' => 'font-bold']),
                    ])->columns(3),

                Section::make('Employer Cost')
                    ->schema([
                        TextInput::make('employer_fnpf')->label('Employer FNPF (8%)')->numeric()->prefix('FJD')->disabled(),
                        TextInput::make('workcare_levy')->numeric()->prefix('FJD')->disabled(),
                        TextInput::make('training_levy')->numeric()->prefix('FJD')->disabled(),
                        TextInput::make('employer_cost')->numeric()->prefix('FJD')->disabled()->extraAttributes(['class' => 'font-bold']),
                    ])->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payrollRun.reference')
                    ->label('Payroll Run')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('employee_number')->toggleable(),
                Tables\Columns\TextColumn::make('gross')->money('FJD')->sortable(),
                Tables\Columns\TextColumn::make('employee_fnpf')->label('Emp FNPF')->money('FJD'),
                Tables\Columns\TextColumn::make('paye')->money('FJD'),
                Tables\Columns\TextColumn::make('net_pay')->money('FJD')->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('employer_cost')->money('FJD')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payroll_run_id')
                    ->relationship('payrollRun', 'reference')
                    ->label('Payroll Run'),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('download_payslip')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (SalarySlip $record) => route('fiji-payroll.payslip.pdf', $record))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListSalarySlips::route('/'),
            'view' => Pages\ViewSalarySlip::route('/{record}'),
        ];
    }
}

<?php

namespace Nuvis\FijiPayroll\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Nuvis\FijiPayroll\Enums\PayFrequency;
use Nuvis\FijiPayroll\Enums\PayrollStatus;
use Nuvis\FijiPayroll\Filament\Resources\PayrollRunResource\Pages;
use Nuvis\FijiPayroll\Filament\Resources\PayrollRunResource\RelationManagers;
use Nuvis\FijiPayroll\Models\PayrollRun;

class PayrollRunResource extends Resource
{
    protected static ?string $model = PayrollRun::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Payroll';

    protected static ?string $navigationLabel = 'Payroll Runs';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Period Details')
                    ->schema([
                        TextInput::make('reference')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('PR-2026-10'),
                        TextInput::make('title')
                            ->placeholder('October 2026 Payroll'),
                        DatePicker::make('period_start')->required(),
                        DatePicker::make('period_end')->required(),
                        DatePicker::make('pay_date')->required(),
                        Select::make('frequency')
                            ->options(collect(PayFrequency::cases())->mapWithKeys(
                                fn ($case) => [$case->value => $case->label()]
                            ))
                            ->default(PayFrequency::Monthly->value)
                            ->required(),
                        Select::make('status')
                            ->options(collect(PayrollStatus::cases())->mapWithKeys(
                                fn ($case) => [$case->value => $case->label()]
                            ))
                            ->default(PayrollStatus::Draft->value)
                            ->required(),
                        Textarea::make('notes')->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('period_start')->date()->sortable(),
                Tables\Columns\TextColumn::make('period_end')->date(),
                Tables\Columns\TextColumn::make('pay_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('frequency')->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (PayrollStatus $state) => $state->color()),
                Tables\Columns\TextColumn::make('employee_count')->label('Employees'),
                Tables\Columns\TextColumn::make('total_gross')->money('FJD'),
                Tables\Columns\TextColumn::make('total_net')->money('FJD'),
                Tables\Columns\TextColumn::make('total_employer_cost')->money('FJD')->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(PayrollStatus::cases())->mapWithKeys(
                        fn ($case) => [$case->value => $case->label()]
                    )),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('export_fnpf')
                    ->label('FNPF Export')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(fn (PayrollRun $record) => \Nuvis\FijiPayroll\Jobs\GenerateFnpfScheduleJob::dispatch($record)),
                Action::make('export_bank')
                    ->label('Bank File')
                    ->icon('heroicon-o-building-library')
                    ->action(fn (PayrollRun $record) => \Nuvis\FijiPayroll\Jobs\GenerateBankFileJob::dispatch($record)),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SalarySlipsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayrollRuns::route('/'),
            'create' => Pages\CreatePayrollRun::route('/create'),
            'view' => Pages\ViewPayrollRun::route('/{record}'),
            'edit' => Pages\EditPayrollRun::route('/{record}/edit'),
        ];
    }
}

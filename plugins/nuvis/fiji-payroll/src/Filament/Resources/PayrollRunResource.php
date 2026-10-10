<?php

namespace Nuvis\FijiPayroll\Filament\Resources;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Schema as DbSchema;
use Nuvis\FijiPayroll\Enums\PayFrequency;
use Nuvis\FijiPayroll\Enums\PayrollStatus;
use Nuvis\FijiPayroll\Filament\Resources\PayrollRunResource\Pages\CreatePayrollRun;
use Nuvis\FijiPayroll\Filament\Resources\PayrollRunResource\Pages\EditPayrollRun;
use Nuvis\FijiPayroll\Filament\Resources\PayrollRunResource\Pages\ListPayrollRuns;
use Nuvis\FijiPayroll\Filament\Resources\PayrollRunResource\Pages\ViewPayrollRun;
use Nuvis\FijiPayroll\Models\PayrollRun;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Enums\NavigationGroup;

class PayrollRunResource extends Resource
{
    use HasPageShield;

    protected static ?string $model = PayrollRun::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 1;

    protected static function getPagePermission(): ?string
    {
        return 'page_fiji_payroll_run';
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Payroll;
    }

    public static function getNavigationLabel(): string
    {
        return 'Fiji Payroll Runs';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payroll Run Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Run Title')
                            ->placeholder('e.g. August 2026 Monthly Payroll')
                            ->required()
                            ->columnSpanFull(),
                        DatePicker::make('period_start')
                            ->label('Period Start')
                            ->required(),
                        DatePicker::make('period_end')
                            ->label('Period End')
                            ->required(),
                        Select::make('pay_frequency')
                            ->label('Pay Frequency')
                            ->options(
                                collect(PayFrequency::cases())->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])->toArray()
                            )
                            ->default('monthly')
                            ->required(),
                        Select::make('status')
                            ->label('Status')
                            ->options(
                                collect(PayrollStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])->toArray()
                            )
                            ->default('draft')
                            ->required(),
                    ]),

                Section::make('Salary Slips')
                    ->schema([
                        Repeater::make('salarySlips')
                            ->relationship('salarySlips')
                            ->columns(4)
                            ->schema([
                                Select::make('employee_id')
                                    ->label('Employee')
                                    ->options(fn () => DbSchema::hasTable('employees_employees') ? Employee::query()->pluck('name', 'id') : [])
                                    ->searchable()
                                    ->nullable(),
                                Toggle::make('is_resident')
                                    ->label('Fiji Resident')
                                    ->default(true),
                                TextInput::make('basic_salary')
                                    ->label('Basic Salary')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0)
                                    ->required(),
                                TextInput::make('overtime')
                                    ->label('Overtime')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0),
                                TextInput::make('allowances')
                                    ->label('Allowances')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Run Title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pay_frequency')
                    ->label('Frequency')
                    ->badge(),
                TextColumn::make('period_start')
                    ->label('Start')
                    ->date(),
                TextColumn::make('period_end')
                    ->label('End')
                    ->date(),
                TextColumn::make('total_gross')
                    ->label('Total Gross')
                    ->money('FJD')
                    ->sortable(),
                TextColumn::make('total_paye')
                    ->label('Total PAYE')
                    ->money('FJD'),
                TextColumn::make('total_net')
                    ->label('Total Net')
                    ->money('FJD')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (PayrollRun $record) => $record->status === PayrollStatus::Calculated || $record->status === PayrollStatus::Draft)
                    ->action(fn (PayrollRun $record) => $record->update(['status' => PayrollStatus::Approved])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPayrollRuns::route('/'),
            'create' => CreatePayrollRun::route('/create'),
            'view'   => ViewPayrollRun::route('/{record}'),
            'edit'   => EditPayrollRun::route('/{record}/edit'),
        ];
    }
}

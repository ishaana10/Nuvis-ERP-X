<?php

namespace Webkul\Payroll\Filament\Admin\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Payroll\Enums\ContractState;
use Webkul\Payroll\Filament\Admin\Resources\EmployeeContractResource\Pages\ListEmployeeContracts;
use Webkul\Payroll\Models\EmployeeContract;

class EmployeeContractResource extends Resource
{
    protected static ?string $model = EmployeeContract::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return \Webkul\Support\Enums\NavigationGroup::Payroll;
    }

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('employee_id')
                    ->relationship('employee', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('structure_id')
                    ->relationship('structure', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('company_id')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('journal_id')
                    ->relationship('journal', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('wage')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Select::make('schedule_pay')
                    ->options([
                        'monthly'   => 'Monthly',
                        'weekly'    => 'Weekly',
                        'bi-weekly' => 'Bi-Weekly',
                    ])
                    ->default('monthly')
                    ->required(),
                Select::make('state')
                    ->options(ContractState::class)
                    ->required()
                    ->default(ContractState::OPEN),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('employee.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('structure.name')
                    ->sortable(),
                TextColumn::make('wage')
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
            'index' => ListEmployeeContracts::route('/'),
        ];
    }
}

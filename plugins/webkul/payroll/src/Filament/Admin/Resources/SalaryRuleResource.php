<?php

namespace Webkul\Payroll\Filament\Admin\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Payroll\Enums\AmountType;
use Webkul\Payroll\Enums\RuleCategory;
use Webkul\Payroll\Filament\Admin\Resources\SalaryRuleResource\Pages\ListSalaryRules;
use Webkul\Payroll\Models\SalaryRule;

class SalaryRuleResource extends Resource
{
    protected static ?string $model = SalaryRule::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-calculator';

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return \Webkul\Support\Enums\NavigationGroup::Payroll;
    }

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('structure_id')
                    ->relationship('structure', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('contribution_register_id')
                    ->relationship('contributionRegister', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->required()
                    ->maxLength(255),
                Select::make('category')
                    ->options(RuleCategory::class)
                    ->required()
                    ->default(RuleCategory::BASIC),
                TextInput::make('sequence')
                    ->numeric()
                    ->default(10),
                Select::make('amount_type')
                    ->options(AmountType::class)
                    ->required()
                    ->default(AmountType::FIXED),
                TextInput::make('amount_fix')
                    ->numeric()
                    ->default(0),
                TextInput::make('amount_percentage')
                    ->numeric()
                    ->default(0),
                TextInput::make('amount_percentage_base')
                    ->default('basic'),
                Textarea::make('amount_python_compute')
                    ->label('Formula / Expression')
                    ->columnSpanFull(),
                Select::make('account_id')
                    ->relationship('account', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('employer_account_id')
                    ->relationship('employerAccount', 'name')
                    ->searchable()
                    ->preload(),
                Toggle::make('appears_on_payslip')
                    ->default(true),
                Toggle::make('is_employer')
                    ->label('Employer Contribution'),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('structure.name')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('category')
                    ->badge()
                    ->sortable(),
                TextColumn::make('amount_type')
                    ->sortable(),
                TextColumn::make('sequence')
                    ->sortable(),
                IconColumn::make('is_employer')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->actions([
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
            'index' => ListSalaryRules::route('/'),
        ];
    }
}

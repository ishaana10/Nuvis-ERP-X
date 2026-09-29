<?php

namespace Webkul\Payroll\Filament\Admin\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Payroll\Filament\Admin\Resources\PayrollStructureResource\Pages\ListPayrollStructures;
use Webkul\Payroll\Models\PayrollStructure;

class PayrollStructureResource extends Resource
{
    protected static ?string $model = PayrollStructure::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-queue-list';

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return \Webkul\Support\Enums\NavigationGroup::Payroll;
    }

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->maxLength(255),
                Select::make('company_id')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
                Toggle::make('is_active')
                    ->default(true),
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
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('company.name')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('rules_count')
                    ->counts('rules')
                    ->label('Rules'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => ListPayrollStructures::route('/'),
        ];
    }
}

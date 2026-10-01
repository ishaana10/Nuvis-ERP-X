<?php

namespace Webkul\Payroll\Filament\Admin\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Payroll\Filament\Admin\Resources\ContributionRegisterResource\Pages\ListContributionRegisters;
use Webkul\Payroll\Models\ContributionRegister;
use Webkul\Support\Enums\NavigationGroup;

class ContributionRegisterResource extends Resource
{
    protected static ?string $model = ContributionRegister::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-building-library';

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Payroll;
    }

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('partner_id')
                    ->relationship('partner', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('company_id')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
                Textarea::make('note')
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
                TextColumn::make('partner.name')
                    ->label('Partner / Authority')
                    ->sortable(),
                TextColumn::make('company.name')
                    ->sortable(),
                TextColumn::make('rules_count')
                    ->counts('rules')
                    ->label('Rules'),
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
            'index' => ListContributionRegisters::route('/'),
        ];
    }
}

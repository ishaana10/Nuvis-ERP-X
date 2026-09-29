<?php

namespace Webkul\Vms\Filament\Resources;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Vms\Filament\Resources\VmsTaxRateResource\Pages\ListVmsTaxRates;
use Webkul\Vms\Models\VmsTaxRate;

class VmsTaxRateResource extends Resource
{
    protected static ?string $model = VmsTaxRate::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static string|\UnitEnum|null $navigationGroup = 'VAT Monitoring System (VMS)';

    protected static ?string $navigationLabel = 'Tax Rates';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tax Rate Details')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('label')
                                ->label('Tax Label (e.g., A, E, F, P)')
                                ->required(),
                            TextInput::make('name')
                                ->label('Tax Name')
                                ->required(),
                            TextInput::make('rate')
                                ->label('Rate (%)')
                                ->numeric()
                                ->required(),
                            Toggle::make('is_active')
                                ->label('Active')
                                ->default(true),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label('Label')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('name')
                    ->label('Tax Name')
                    ->searchable(),
                TextColumn::make('rate')
                    ->label('Rate (%)')
                    ->formatStateUsing(fn ($state) => number_format($state, 2) . '%'),
                TextColumn::make('valid_from')
                    ->label('Valid From')
                    ->dateTime(),
                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Active' : 'Inactive')
                    ->color(fn ($state) => $state ? 'success' : 'gray'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVmsTaxRates::route('/'),
        ];
    }
}

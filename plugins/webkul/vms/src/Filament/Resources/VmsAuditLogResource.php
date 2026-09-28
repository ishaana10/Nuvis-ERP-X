<?php

namespace Webkul\Vms\Filament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Vms\Filament\Resources\VmsAuditLogResource\Pages\ListVmsAuditLogs;
use Webkul\Vms\Models\VmsAuditLog;

class VmsAuditLogResource extends Resource
{
    protected static ?string $model = VmsAuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationGroup = 'VAT Monitoring System (VMS)';

    protected static ?string $navigationLabel = 'Audit Logs';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Audit Record')
                    ->schema([
                        TextInput::make('secure_component_uid')->disabled(),
                        TextInput::make('log_type')->disabled(),
                        TextInput::make('message')->disabled(),
                        TextInput::make('status')->disabled(),
                        KeyValue::make('package_data')->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('Timestamp')->dateTime()->sortable(),
                TextColumn::make('secure_component_uid')->label('SDC / Component UID')->searchable(),
                TextColumn::make('log_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'info' => 'info',
                        'error' => 'danger',
                        'warning' => 'warning',
                        default => 'secondary',
                    }),
                TextColumn::make('message')->label('Message')->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'verified' => 'success',
                        'error' => 'danger',
                        default => 'secondary',
                    }),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVmsAuditLogs::route('/'),
        ];
    }
}

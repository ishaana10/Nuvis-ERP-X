<?php

namespace Webkul\Vms\Filament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Vms\Filament\Resources\VmsFiscalInvoiceResource\Pages\ListVmsFiscalInvoices;
use Webkul\Vms\Filament\Resources\VmsFiscalInvoiceResource\Pages\ViewVmsFiscalInvoice;
use Webkul\Vms\Models\VmsFiscalInvoice;
use Webkul\Vms\Services\VmsService;

class VmsFiscalInvoiceResource extends Resource
{
    protected static ?string $model = VmsFiscalInvoice::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'VAT Monitoring System (VMS)';

    protected static ?string $navigationLabel = 'Fiscal Invoices';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Fiscalization Overview')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('sdc_invoice_no')
                                ->label('SDC Invoice No')
                                ->disabled(),
                            TextInput::make('invoice_type')
                                ->label('Invoice Type')
                                ->disabled(),
                            TextInput::make('transaction_type')
                                ->label('Transaction Type')
                                ->disabled(),
                            TextInput::make('sdc_time')
                                ->label('SDC Time')
                                ->disabled(),
                            TextInput::make('invoice_counter')
                                ->label('Invoice Counter')
                                ->disabled(),
                            TextInput::make('status')
                                ->label('Status')
                                ->disabled(),
                            TextInput::make('buyer_tin')
                                ->label('Buyer TIN')
                                ->disabled(),
                            TextInput::make('ref_sdc_no')
                                ->label('Ref SDC No')
                                ->disabled(),
                            TextInput::make('cashier')
                                ->label('Cashier')
                                ->disabled(),
                            TextInput::make('total_amount')
                                ->label('Total Amount (FJD)')
                                ->disabled(),
                            TextInput::make('total_tax')
                                ->label('Total Tax (FJD)')
                                ->disabled(),
                            TextInput::make('payment_method')
                                ->label('Payment Method')
                                ->disabled(),
                        ]),
                        TextInput::make('verification_url')
                            ->label('QR Code Verification URL')
                            ->columnSpanFull()
                            ->disabled(),
                    ]),
                Section::make('Raw Payloads & Cryptographic Signature')
                    ->collapsed()
                    ->schema([
                        Textarea::make('encrypted_signature')
                            ->label('Electronic Digital Signature')
                            ->rows(2)
                            ->disabled(),
                        KeyValue::make('request_payload')
                            ->label('Request Payload (JSON)')
                            ->disabled(),
                        KeyValue::make('response_payload')
                            ->label('Response Payload (JSON)')
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sdc_invoice_no')
                    ->label('SDC Invoice No')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('accountMove.name')
                    ->label('ERP Invoice No')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('invoice_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Normal'   => 'primary',
                        'Advance'  => 'warning',
                        'Copy'     => 'gray',
                        'Proforma' => 'info',
                        default    => 'secondary',
                    }),
                TextColumn::make('transaction_type')
                    ->label('Tx Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Sale'   => 'success',
                        'Refund' => 'danger',
                        default  => 'secondary',
                    }),
                TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->money('FJD')
                    ->sortable(),
                TextColumn::make('total_tax')
                    ->label('Tax')
                    ->money('FJD')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'fiscalized' => 'success',
                        'failed'     => 'danger',
                        'canceled'   => 'gray',
                        default      => 'info',
                    }),
                TextColumn::make('sdc_time')
                    ->label('SDC Date & Time')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('cancelInvoice')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (VmsFiscalInvoice $record) => $record->status === 'fiscalized' && ! in_array($record->invoice_type, ['Proforma', 'Copy', 'Training']))
                    ->action(function (VmsFiscalInvoice $record) {
                        $service = new VmsService;
                        $service->cancelFiscalInvoice($record);
                    }),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVmsFiscalInvoices::route('/'),
            'view'  => ViewVmsFiscalInvoice::route('/{record}'),
        ];
    }
}

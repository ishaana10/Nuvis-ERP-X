<?php

namespace Webkul\Vms\Filament\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Select;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\Toggle;
use Filament\Schemas\Schema;
use Webkul\Vms\Models\VmsSetting;
use Webkul\Vms\Services\VmsService;

class VmsSettingsPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|\UnitEnum|null $navigationGroup = 'VAT Monitoring System (VMS)';

    protected static ?string $navigationLabel = 'VMS Settings';

    protected static ?int $navigationSort = 1;

    protected string $view = 'vms::filament.pages.vms-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $setting = VmsSetting::where('company_id', current_company_id())->first()
            ?? VmsSetting::first();

        if ($setting) {
            $this->form->fill($setting->toArray());
        } else {
            $this->form->fill([
                'company_id' => current_company_id(),
                'pos_number' => 'POS-001/1.0',
                'environment' => 'sandbox',
                'sdc_type' => 'V-SDC',
                'api_url' => 'https://tap.sandbox.vms.frcs.org.fj',
                'is_active' => true,
            ]);
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('FRCS EFD & VMS Credentials')
                    ->description('Configure your Fiji Revenue & Customs Service (FRCS) Electronic Fiscal Device details.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('tin')
                                ->label('Taxpayer Identification Number (TIN)')
                                ->placeholder('502579006')
                                ->required(),
                            TextInput::make('mrc')
                                ->label('Machine Registration Code (MRC)')
                                ->placeholder('MRC-12345678')
                                ->required(),
                            TextInput::make('pac')
                                ->label('PKE / PAC Code')
                                ->password()
                                ->revealable(),
                            TextInput::make('pos_number')
                                ->label('POS Accreditation Number / Version')
                                ->default('POS-001/1.0')
                                ->required(),
                            Select::make('environment')
                                ->label('Environment')
                                ->options([
                                    'sandbox' => 'Sandbox / Accreditation',
                                    'production' => 'Production / Live',
                                ])
                                ->default('sandbox')
                                ->required(),
                            Select::make('sdc_type')
                                ->label('SDC Type')
                                ->options([
                                    'V-SDC' => 'V-SDC (Virtual SDC via Cloud/HTTPS)',
                                    'E-SDC' => 'E-SDC (External Hardware/Local SDC)',
                                ])
                                ->default('V-SDC')
                                ->required(),
                            TextInput::make('api_url')
                                ->label('VMS SDC API Endpoint URL')
                                ->default('https://tap.sandbox.vms.frcs.org.fj')
                                ->url()
                                ->required(),
                            Toggle::make('is_active')
                                ->label('Enable VMS Fiscalization')
                                ->default(true),
                        ]),
                    ]),
                Section::make('Digital Certificate (PKI) Security')
                    ->description('Upload PFX / P12 certificate issued by FRCS for signing fiscal invoices.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('pfx_certificate')
                                ->label('Certificate Path / File')
                                ->placeholder('/path/to/certificate.pfx'),
                            TextInput::make('certificate_password')
                                ->label('Certificate Password')
                                ->password()
                                ->revealable(),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $data['company_id'] = current_company_id();

        VmsSetting::updateOrCreate(
            ['company_id' => current_company_id()],
            $data
        );

        Notification::make()
            ->title('VMS Settings saved successfully.')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('syncTaxRates')
                ->label('Sync FRCS Tax Rates')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->action(function () {
                    $service = new VmsService();
                    $res = $service->syncTaxRates();

                    if ($res['success']) {
                        Notification::make()
                            ->title('Tax rates synchronized with FRCS TaxCore successfully.')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Failed to sync tax rates.')
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}

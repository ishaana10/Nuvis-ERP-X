<?php

namespace Webkul\Vms\Filament\Clusters\Settings\Pages;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Webkul\Support\Filament\Clusters\Settings;
use Webkul\Vms\Models\VmsSetting;
use Webkul\Vms\Settings\VmsSettings;

class ManageVms extends SettingsPage
{
    protected static ?string $slug = 'vms/manage-vms';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'VMS';

    protected static ?int $navigationSort = 10;

    protected static ?string $cluster = Settings::class;

    protected static string $settings = VmsSettings::class;

    public static function getNavigationLabel(): string
    {
        return 'Manage VMS';
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
        ;
    }
}

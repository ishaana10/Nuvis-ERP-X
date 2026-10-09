<?php

namespace Webkul\Support\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Webkul\Support\Filament\Clusters\Settings;
use Webkul\Support\Settings\BrandSettings;

class ManageBranding extends SettingsPage
{
    use HasPageShield;

    protected static ?string $cluster = Settings::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-swatch';

    protected static ?int $navigationSort = 10;

    protected static string $settings = BrandSettings::class;

    protected static function getPagePermission(): ?string
    {
        return 'page_support_manage_branding';
    }

    public static function getNavigationGroup(): string
    {
        return __('support::filament/clusters/manage-branding.group');
    }

    public function getBreadcrumbs(): array
    {
        return [
            __('support::filament/clusters/manage-branding.breadcrumb'),
        ];
    }

    public function getTitle(): string
    {
        return __('support::filament/clusters/manage-branding.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('support::filament/clusters/manage-branding.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('support::filament/clusters/manage-branding.form.sections.identity.title'))
                    ->description(__('support::filament/clusters/manage-branding.form.sections.identity.description'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('app_name')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.app-name'))
                            ->helperText(__('support::filament/clusters/manage-branding.form.fields.app-name-helper'))
                            ->placeholder('Nuvis ERP X')
                            ->columnSpanFull(),
                        FileUpload::make('light_logo')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.light-logo'))
                            ->helperText(__('support::filament/clusters/manage-branding.form.fields.light-logo-helper'))
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public'),
                        FileUpload::make('dark_logo')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.dark-logo'))
                            ->helperText(__('support::filament/clusters/manage-branding.form.fields.dark-logo-helper'))
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public'),
                        FileUpload::make('favicon')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.favicon'))
                            ->helperText(__('support::filament/clusters/manage-branding.form.fields.favicon-helper'))
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public'),
                        TextInput::make('logo_height')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.logo-height'))
                            ->helperText(__('support::filament/clusters/manage-branding.form.fields.logo-height-helper'))
                            ->placeholder('2rem'),
                    ]),

                Section::make(__('support::filament/clusters/manage-branding.form.sections.theme.title'))
                    ->description(__('support::filament/clusters/manage-branding.form.sections.theme.description'))
                    ->columns(3)
                    ->headerActions([
                        Action::make('reset')
                            ->label(__('support::filament/clusters/manage-branding.actions.reset.label'))
                            ->icon('heroicon-o-arrow-path')
                            ->color('gray')
                            ->link()
                            ->requiresConfirmation()
                            ->action(function (Set $set): void {
                                foreach ($this->getDefaultColors() as $field => $hex) {
                                    $set($field, $hex);
                                }
                            }),
                    ])
                    ->schema([
                        Select::make('theme_preset')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.theme-preset'))
                            ->helperText(__('support::filament/clusters/manage-branding.form.fields.theme-preset-helper'))
                            ->options([
                                'nuvis_blue' => __('support::filament/clusters/manage-branding.form.presets.nuvis_blue'),
                                'emerald'    => __('support::filament/clusters/manage-branding.form.presets.emerald'),
                                'indigo'     => __('support::filament/clusters/manage-branding.form.presets.indigo'),
                                'slate'      => __('support::filament/clusters/manage-branding.form.presets.slate'),
                                'amber'      => __('support::filament/clusters/manage-branding.form.presets.amber'),
                                'rose'       => __('support::filament/clusters/manage-branding.form.presets.rose'),
                            ])
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set): void {
                                $presetColors = $this->getPresetColors($state);
                                foreach ($presetColors as $field => $hex) {
                                    $set($field, $hex);
                                }
                            })
                            ->columnSpanFull(),
                        ColorPicker::make('primary_color')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.primary-color'))
                            ->hexColor(),
                        ColorPicker::make('gray_color')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.gray-color'))
                            ->hexColor(),
                        ColorPicker::make('danger_color')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.danger-color'))
                            ->hexColor(),
                        ColorPicker::make('info_color')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.info-color'))
                            ->hexColor(),
                        ColorPicker::make('success_color')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.success-color'))
                            ->hexColor(),
                        ColorPicker::make('warning_color')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.warning-color'))
                            ->hexColor(),
                    ]),

                Section::make(__('support::filament/clusters/manage-branding.form.sections.typography.title'))
                    ->description(__('support::filament/clusters/manage-branding.form.sections.typography.description'))
                    ->columns(2)
                    ->schema([
                        Select::make('font_family')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.font-family'))
                            ->helperText(__('support::filament/clusters/manage-branding.form.fields.font-family-helper'))
                            ->options([
                                'inter'             => __('support::filament/clusters/manage-branding.form.fonts.inter'),
                                'plus_jakarta_sans' => __('support::filament/clusters/manage-branding.form.fonts.plus_jakarta_sans'),
                                'roboto'            => __('support::filament/clusters/manage-branding.form.fonts.roboto'),
                                'outfit'            => __('support::filament/clusters/manage-branding.form.fonts.outfit'),
                                'system'            => __('support::filament/clusters/manage-branding.form.fonts.system'),
                            ])
                            ->default('inter'),
                        TextInput::make('footer_text')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.footer-text'))
                            ->helperText(__('support::filament/clusters/manage-branding.form.fields.footer-text-helper'))
                            ->placeholder('© Nuvis ERP X. All rights reserved.'),
                    ]),

                Section::make(__('support::filament/clusters/manage-branding.form.sections.advanced.title'))
                    ->description(__('support::filament/clusters/manage-branding.form.sections.advanced.description'))
                    ->columns(1)
                    ->schema([
                        Textarea::make('custom_css')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.custom-css'))
                            ->helperText(__('support::filament/clusters/manage-branding.form.fields.custom-css-helper'))
                            ->rows(5)
                            ->placeholder("/* Custom UI styles */\n.fi-topbar { box-shadow: 0 1px 3px rgba(0,0,0,0.05); }"),
                    ]),
            ]);
    }

    private function getDefaultColors(): array
    {
        return [
            'primary_color' => Color::convertToHex(Color::Blue[600]),
            'gray_color'    => Color::convertToHex(Color::Zinc[600]),
            'danger_color'  => Color::convertToHex(Color::Red[600]),
            'info_color'    => Color::convertToHex(Color::Blue[600]),
            'success_color' => Color::convertToHex(Color::Green[600]),
            'warning_color' => Color::convertToHex(Color::Amber[600]),
        ];
    }

    private function getPresetColors(?string $preset): array
    {
        return match ($preset) {
            'emerald' => [
                'primary_color' => '#059669',
                'gray_color'    => '#52525b',
                'danger_color'  => '#e11d48',
                'info_color'    => '#0284c7',
                'success_color' => '#059669',
                'warning_color' => '#d97706',
            ],
            'indigo' => [
                'primary_color' => '#4f46e5',
                'gray_color'    => '#4b5563',
                'danger_color'  => '#e11d48',
                'info_color'    => '#0284c7',
                'success_color' => '#16a34a',
                'warning_color' => '#d97706',
            ],
            'slate' => [
                'primary_color' => '#0f172a',
                'gray_color'    => '#64748b',
                'danger_color'  => '#ef4444',
                'info_color'    => '#38bdf8',
                'success_color' => '#22c55e',
                'warning_color' => '#f59e0b',
            ],
            'amber' => [
                'primary_color' => '#d97706',
                'gray_color'    => '#52525b',
                'danger_color'  => '#dc2626',
                'info_color'    => '#2563eb',
                'success_color' => '#16a34a',
                'warning_color' => '#d97706',
            ],
            'rose' => [
                'primary_color' => '#e11d48',
                'gray_color'    => '#52525b',
                'danger_color'  => '#dc2626',
                'info_color'    => '#0284c7',
                'success_color' => '#16a34a',
                'warning_color' => '#f59e0b',
            ],
            default => $this->getDefaultColors(),
        };
    }
}

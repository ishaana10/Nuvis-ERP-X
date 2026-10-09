<?php

namespace Webkul\Support\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Support\Filament\Clusters\Settings;
use Webkul\Support\Models\Company;
use Webkul\Support\Settings\MultiTenantSettings;

class ManageMultiTenancy extends SettingsPage
{
    use HasPageShield;

    protected static ?string $cluster = Settings::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?int $navigationSort = 5;

    protected static string $settings = MultiTenantSettings::class;

    protected static function getPagePermission(): ?string
    {
        return 'page_support_manage_multi_tenancy';
    }

    public static function getNavigationGroup(): string
    {
        return __('support::filament/clusters/manage-multi-tenancy.group');
    }

    public function getBreadcrumbs(): array
    {
        return [
            __('support::filament/clusters/manage-multi-tenancy.breadcrumb'),
        ];
    }

    public function getTitle(): string
    {
        return __('support::filament/clusters/manage-multi-tenancy.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('support::filament/clusters/manage-multi-tenancy.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('support::filament/clusters/manage-multi-tenancy.form.sections.tenancy.title'))
                    ->description(__('support::filament/clusters/manage-multi-tenancy.form.sections.tenancy.description'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('enable_multi_tenancy')
                            ->label(__('support::filament/clusters/manage-multi-tenancy.form.fields.enable-multi-tenancy'))
                            ->helperText(__('support::filament/clusters/manage-multi-tenancy.form.fields.enable-multi-tenancy-helper'))
                            ->default(true),
                        Toggle::make('show_tenant_switcher')
                            ->label(__('support::filament/clusters/manage-multi-tenancy.form.fields.show-tenant-switcher'))
                            ->helperText(__('support::filament/clusters/manage-multi-tenancy.form.fields.show-tenant-switcher-helper'))
                            ->default(true),
                        Radio::make('tenant_switch_mode')
                            ->label(__('support::filament/clusters/manage-multi-tenancy.form.fields.tenant-switch-mode'))
                            ->helperText(__('support::filament/clusters/manage-multi-tenancy.form.fields.tenant-switch-mode-helper'))
                            ->options([
                                'multi'  => __('support::filament/clusters/manage-multi-tenancy.form.options.switch-mode-multi'),
                                'single' => __('support::filament/clusters/manage-multi-tenancy.form.options.switch-mode-single'),
                            ])
                            ->default('multi')
                            ->columnSpanFull(),
                    ]),

                Section::make(__('support::filament/clusters/manage-multi-tenancy.form.sections.isolation.title'))
                    ->description(__('support::filament/clusters/manage-multi-tenancy.form.sections.isolation.description'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('strict_tenant_isolation')
                            ->label(__('support::filament/clusters/manage-multi-tenancy.form.fields.strict-tenant-isolation'))
                            ->helperText(__('support::filament/clusters/manage-multi-tenancy.form.fields.strict-tenant-isolation-helper'))
                            ->default(false),
                        Toggle::make('allow_self_service_tenant_creation')
                            ->label(__('support::filament/clusters/manage-multi-tenancy.form.fields.allow-self-service-tenant-creation'))
                            ->helperText(__('support::filament/clusters/manage-multi-tenancy.form.fields.allow-self-service-tenant-creation-helper'))
                            ->default(true),
                        Select::make('default_tenant_id')
                            ->label(__('support::filament/clusters/manage-multi-tenancy.form.fields.default-tenant'))
                            ->helperText(__('support::filament/clusters/manage-multi-tenancy.form.fields.default-tenant-helper'))
                            ->options(fn () => Company::query()->pluck('name', 'id'))
                            ->searchable()
                            ->nullable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

<?php

use App\Http\Middleware\ApplyBrandSettings;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Livewire\Livewire;
use Webkul\Support\Filament\Clusters\Settings\Pages\ManageBranding;
use Webkul\Support\Filament\Clusters\Settings\Pages\ManageMultiTenancy;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;
use Webkul\Support\Settings\BrandSettings;
use Webkul\Support\Settings\MultiTenantSettings;

require_once __DIR__.'/../../Helpers/SecurityHelper.php';
require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensureERPInstalled();
    config()->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
});

it('can resolve default multi tenant settings', function () {
    $settings = settings(MultiTenantSettings::class);

    expect($settings->enable_multi_tenancy)->toBeTrue();
    expect($settings->tenant_switch_mode)->toBe('multi');
    expect($settings->show_tenant_switcher)->toBeTrue();
    expect($settings->strict_tenant_isolation)->toBeFalse();
    expect($settings->allow_self_service_tenant_creation)->toBeTrue();
});

it('can update multi tenant settings', function () {
    $settings = settings(MultiTenantSettings::class);
    $settings->enable_multi_tenancy = false;
    $settings->tenant_switch_mode = 'single';
    $settings->save();

    $freshSettings = settings(MultiTenantSettings::class);
    expect($freshSettings->enable_multi_tenancy)->toBeFalse();
    expect($freshSettings->tenant_switch_mode)->toBe('single');
});

it('restricts active company IDs in single tenant switch mode', function () {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();

    $user = SecurityHelper::authenticateWithPermissions([], true);
    $user->allowedCompanies()->sync([$companyA->id, $companyB->id]);
    $this->actingAs($user);

    session([CompanyContext::SESSION_KEY => [$companyA->id, $companyB->id]]);

    $multiSettings = settings(MultiTenantSettings::class);
    $multiSettings->tenant_switch_mode = 'single';
    $multiSettings->save();

    $context = app(CompanyContext::class);
    expect($context->activeIds())->toBe([$companyA->id]);
});

it('can resolve and update extended brand settings', function () {
    $brand = settings(BrandSettings::class);

    expect($brand->app_name)->toBe('Nuvis ERP X');
    expect($brand->font_family)->toBe('inter');

    $brand->app_name = 'Custom Acme ERP';
    $brand->theme_preset = 'emerald';
    $brand->font_family = 'plus_jakarta_sans';
    $brand->footer_text = '© Acme Corp';
    $brand->custom_css = '.test-class { color: red; }';
    $brand->save();

    $freshBrand = settings(BrandSettings::class);
    expect($freshBrand->app_name)->toBe('Custom Acme ERP');
    expect($freshBrand->theme_preset)->toBe('emerald');
    expect($freshBrand->font_family)->toBe('plus_jakarta_sans');
    expect($freshBrand->footer_text)->toBe('© Acme Corp');
    expect($freshBrand->custom_css)->toBe('.test-class { color: red; }');
});

it('applies custom brand settings via ApplyBrandSettings middleware', function () {
    $brand = settings(BrandSettings::class);
    $brand->app_name = 'Custom Enterprise ERP';
    $brand->footer_text = 'Powered by Custom Enterprise';
    $brand->save();

    $panel = Filament::getPanel('admin');
    Filament::setCurrentPanel($panel);

    $request = Request::create('/admin', 'GET');
    $middleware = new ApplyBrandSettings;

    $response = $middleware->handle($request, fn () => response('ok'));

    expect($response->getContent())->toBe('ok');
    expect($panel->getBrandName())->toBe('Custom Enterprise ERP');
});

it('can render manage multi tenancy settings page component', function () {
    $user = SecurityHelper::authenticateWithPermissions(['page_support_manage_multi_tenancy'], true);
    $this->actingAs($user);

    Livewire::test(ManageMultiTenancy::class)
        ->assertSuccessful();
});

it('can render manage branding settings page component', function () {
    $user = SecurityHelper::authenticateWithPermissions(['page_support_manage_branding'], true);
    $this->actingAs($user);

    Livewire::test(ManageBranding::class)
        ->assertSuccessful();
});

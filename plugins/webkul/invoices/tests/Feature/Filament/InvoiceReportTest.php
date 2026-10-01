<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Invoice\Filament\Clusters\Reporting\Pages\InvoiceReport;
use Webkul\Invoice\Models\Invoice;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('invoices');

    DB::table('plugins')->whereIn('name', ['accounts', 'invoices', 'partners'])->update([
        'is_installed' => true,
        'is_active'    => true,
        'updated_at'   => now(),
    ]);

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    FilamentHelper::actingAs(['page_invoice_invoice_report']);
});

it('renders the invoice report page', function () {
    Livewire::test(InvoiceReport::class)->assertOk();
});

it('calculates summary stats correctly for invoices', function () {
    $partner = Partner::factory()->create();

    $invoice1 = Invoice::factory()->create([
        'partner_id'      => $partner->id,
        'move_type'       => MoveType::OUT_INVOICE,
        'state'           => MoveState::POSTED,
        'amount_total'    => 1000.00,
        'amount_residual' => 1000.00,
        'invoice_date'    => now()->toDateString(),
    ]);

    $invoice2 = Invoice::factory()->create([
        'partner_id'      => $partner->id,
        'move_type'       => MoveType::OUT_INVOICE,
        'state'           => MoveState::POSTED,
        'amount_total'    => 500.00,
        'amount_residual' => 200.00,
        'invoice_date'    => now()->toDateString(),
    ]);

    $component = Livewire::test(InvoiceReport::class);

    $reportData = $component->get('reportData');

    expect($reportData['stats']['total_count'])->toBeGreaterThanOrEqual(2)
        ->and($reportData['stats']['total_amount'])->toBeGreaterThanOrEqual(1500.00)
        ->and($reportData['stats']['total_residual'])->toBeGreaterThanOrEqual(1200.00)
        ->and($reportData['stats']['total_paid'])->toBeGreaterThanOrEqual(300.00);
});

it('filters invoices by unpaid preset', function () {
    $partner = Partner::factory()->create();

    $unpaidInvoice = Invoice::factory()->create([
        'partner_id'      => $partner->id,
        'move_type'       => MoveType::OUT_INVOICE,
        'state'           => MoveState::POSTED,
        'amount_total'    => 800.00,
        'amount_residual' => 800.00,
    ]);

    $paidInvoice = Invoice::factory()->create([
        'partner_id'      => $partner->id,
        'move_type'       => MoveType::OUT_INVOICE,
        'state'           => MoveState::POSTED,
        'amount_total'    => 400.00,
        'amount_residual' => 0.00,
    ]);
    $paidInvoice->forceFill(['payment_state' => PaymentState::PAID])->saveQuietly();

    $component = Livewire::test(InvoiceReport::class)
        ->set('data.preset', 'unpaid');

    $reportData = $component->get('reportData');

    $ids = $reportData['invoices']->pluck('id')->all();

    expect($ids)->toContain($unpaidInvoice->id)
        ->and($ids)->not->toContain($paidInvoice->id);
});

it('filters invoices by short paid preset', function () {
    $partner = Partner::factory()->create();

    $shortPaidInvoice = Invoice::factory()->create([
        'partner_id'      => $partner->id,
        'move_type'       => MoveType::OUT_INVOICE,
        'state'           => MoveState::POSTED,
        'amount_total'    => 1000.00,
        'amount_residual' => 300.00,
    ]);
    $shortPaidInvoice->forceFill(['payment_state' => PaymentState::PARTIAL])->saveQuietly();

    $unpaidInvoice = Invoice::factory()->create([
        'partner_id'      => $partner->id,
        'move_type'       => MoveType::OUT_INVOICE,
        'state'           => MoveState::POSTED,
        'amount_total'    => 1000.00,
        'amount_residual' => 1000.00,
    ]);
    $unpaidInvoice->forceFill(['payment_state' => PaymentState::NOT_PAID])->saveQuietly();

    $component = Livewire::test(InvoiceReport::class)
        ->set('data.preset', 'short_paid');

    $reportData = $component->get('reportData');

    $ids = $reportData['invoices']->pluck('id')->all();

    expect($ids)->toContain($shortPaidInvoice->id)
        ->and($ids)->not->toContain($unpaidInvoice->id);
});

it('supports excel and pdf header action calls', function () {
    Livewire::test(InvoiceReport::class)
        ->callAction('excel')
        ->assertFileDownloaded();

    Livewire::test(InvoiceReport::class)
        ->callAction('pdf')
        ->assertFileDownloaded();
});

<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Journal;
use Webkul\Invoice\Filament\Clusters\Reporting\Pages\ZReport;
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

    FilamentHelper::actingAs(['page_invoice_z_report']);
});

it('renders the Z report page', function () {
    Livewire::test(ZReport::class)->assertOk();
});

it('calculates Z report summary stats correctly', function () {
    $partner = Partner::factory()->create();
    $journal = Journal::factory()->create([
        'type' => JournalType::SALE,
    ]);

    $invoice = Invoice::factory()->create([
        'partner_id'      => $partner->id,
        'journal_id'      => $journal->id,
        'move_type'       => MoveType::OUT_INVOICE,
        'state'           => MoveState::POSTED,
        'invoice_date'    => now()->toDateString(),
        'amount_untaxed'  => 1000.00,
        'amount_tax'      => 150.00,
        'amount_total'    => 1150.00,
    ]);

    $refund = Invoice::factory()->create([
        'partner_id'      => $partner->id,
        'journal_id'      => $journal->id,
        'move_type'       => MoveType::OUT_REFUND,
        'state'           => MoveState::POSTED,
        'invoice_date'    => now()->toDateString(),
        'amount_untaxed'  => 200.00,
        'amount_tax'      => 30.00,
        'amount_total'    => 230.00,
    ]);

    $component = Livewire::test(ZReport::class)
        ->fillForm([
            'date_from' => now()->toDateString(),
            'date_to'   => now()->toDateString(),
        ]);

    $reportData = $component->get('reportData');

    expect($reportData['summary']['gross_sales_total'])->toBeGreaterThanOrEqual(1150.00)
        ->and($reportData['summary']['refunds_total'])->toBeGreaterThanOrEqual(230.00)
        ->and($reportData['summary']['net_sales_total'])->toBeGreaterThanOrEqual(920.00);
});

it('supports excel and pdf export actions on Z report', function () {
    Livewire::test(ZReport::class)
        ->callAction('excel')
        ->assertFileDownloaded();

    Livewire::test(ZReport::class)
        ->callAction('pdf')
        ->assertFileDownloaded();
});

<?php

namespace Webkul\Invoice\Filament\Clusters\Reporting\Pages;

use Barryvdh\DomPDF\Facade\Pdf;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Maatwebsite\Excel\Facades\Excel;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\PartialReconcile;
use Webkul\Invoice\Filament\Clusters\Reporting;
use Webkul\Invoice\Filament\Clusters\Reporting\Pages\Exports\ZReportExport;
use Webkul\Invoice\Models\Invoice;

class ZReport extends Page implements HasForms
{
    use HasPageShield, InteractsWithForms;

    protected string $view = 'invoices::filament.clusters.reporting.pages.z-report';

    protected static ?string $cluster = Reporting::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    protected static function getPagePermission(): ?string
    {
        return 'page_z_report';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('invoices::filament/clusters/reporting.pages.z-report.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/reporting.pages.z-report.navigation.title');
    }

    public function getTitle(): string
    {
        return __('invoices::filament/clusters/reporting.pages.z-report.navigation.title');
    }

    public function mount(): void
    {
        $this->form->fill([
            'date_from'  => now()->toDateString(),
            'date_to'    => now()->toDateString(),
            'journal_id' => null,
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('excel')
                ->label(__('invoices::filament/clusters/reporting.pages.z-report.actions.export-excel'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $reportData = $this->reportData;

                    $fileName = 'z-report-'.now()->format('Y-m-d-His').'.xlsx';

                    return Excel::download(
                        new ZReportExport($reportData, $this->form->getState()),
                        $fileName
                    );
                }),

            Action::make('pdf')
                ->label(__('invoices::filament/clusters/reporting.pages.z-report.actions.export-pdf'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('danger')
                ->action(function () {
                    $reportData = $this->reportData;
                    $filters = $this->form->getState();

                    $pdf = Pdf::loadView('invoices::filament.clusters.reporting.pages.pdfs.z-report', [
                        'reportData' => $reportData,
                        'filters'    => $filters,
                    ])->setPaper('a4', 'portrait');

                    $fileName = 'z-report-'.now()->format('Y-m-d-His').'.pdf';

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, $fileName);
                }),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make()
                ->columns([
                    'default' => 1,
                    'sm'      => 3,
                ])
                ->schema([
                    DatePicker::make('date_from')
                        ->label(__('invoices::filament/clusters/reporting.pages.z-report.filters.date-from'))
                        ->native(false)
                        ->suffixIcon('heroicon-o-calendar')
                        ->live(),

                    DatePicker::make('date_to')
                        ->label(__('invoices::filament/clusters/reporting.pages.z-report.filters.date-to'))
                        ->native(false)
                        ->suffixIcon('heroicon-o-calendar')
                        ->live(),

                    Select::make('journal_id')
                        ->label(__('invoices::filament/clusters/reporting.pages.z-report.filters.journal'))
                        ->options(Journal::pluck('name', 'id'))
                        ->placeholder(__('invoices::filament/clusters/reporting.pages.z-report.filters.all-journals'))
                        ->searchable()
                        ->live(),
                ])
                ->columnSpanFull(),
        ];
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    #[Computed]
    public function reportData(): array
    {
        $state = $this->form->getState();

        $query = Invoice::query()
            ->where('state', MoveState::POSTED->value)
            ->whereIn('move_type', [MoveType::OUT_INVOICE->value, MoveType::OUT_REFUND->value]);

        if (! empty($state['date_from'])) {
            $query->whereDate('invoice_date', '>=', $state['date_from']);
        }

        if (! empty($state['date_to'])) {
            $query->whereDate('invoice_date', '<=', $state['date_to']);
        }

        if (! empty($state['journal_id'])) {
            $query->where('journal_id', $state['journal_id']);
        }

        $invoices = $query->with(['lines', 'lines.tax', 'partner'])->get();

        $outInvoices = $invoices->filter(fn ($inv) => ($inv->move_type instanceof MoveType ? $inv->move_type->value : $inv->move_type) === MoveType::OUT_INVOICE->value);
        $outRefunds = $invoices->filter(fn ($inv) => ($inv->move_type instanceof MoveType ? $inv->move_type->value : $inv->move_type) === MoveType::OUT_REFUND->value);

        $grossSalesUntaxed = $outInvoices->sum('amount_untaxed');
        $grossSalesTax = $outInvoices->sum('amount_tax');
        $grossSalesTotal = $outInvoices->sum('amount_total');

        $refundsUntaxed = $outRefunds->sum('amount_untaxed');
        $refundsTax = $outRefunds->sum('amount_tax');
        $refundsTotal = $outRefunds->sum('amount_total');

        $netSalesUntaxed = $grossSalesUntaxed - $refundsUntaxed;
        $netSalesTax = $grossSalesTax - $refundsTax;
        $netSalesTotal = $grossSalesTotal - $refundsTotal;

        // Payment Methods / Journals Breakdown
        $invoiceIds = $invoices->pluck('id')->toArray();
        $paymentsByJournal = [];

        if (! empty($invoiceIds)) {
            $partialReconcileTable = PartialReconcile::getModel()->getTable();

            $payments = DB::table($partialReconcileTable.' as pr')
                ->join('accounts_account_move_lines as credit_line', 'pr.credit_move_id', '=', 'credit_line.id')
                ->join('accounts_account_moves as payment_move', 'credit_line.move_id', '=', 'payment_move.id')
                ->leftJoin('accounts_journals as j', 'payment_move.journal_id', '=', 'j.id')
                ->whereIn('pr.debit_move_id', function ($q) use ($invoiceIds) {
                    $q->select('id')
                        ->from('accounts_account_move_lines')
                        ->whereIn('move_id', $invoiceIds);
                })
                ->select(
                    'j.name as journal_name',
                    DB::raw('SUM(pr.amount) as total_paid')
                )
                ->groupBy('j.name')
                ->get();

            foreach ($payments as $p) {
                $paymentsByJournal[] = [
                    'journal' => $p->journal_name ?? 'Direct Payment',
                    'amount'  => (float) $p->total_paid,
                ];
            }
        }

        // Tax Breakdown
        $taxBreakdown = [];
        foreach ($invoices as $inv) {
            foreach ($inv->lines as $line) {
                if ($line->tax_id && $line->tax) {
                    $taxName = $line->tax->name;
                    $taxAmount = $line->tax_amount ?? 0;

                    $invMoveType = $inv->move_type instanceof MoveType ? $inv->move_type->value : $inv->move_type;

                    if ($invMoveType === MoveType::OUT_REFUND->value) {
                        $taxAmount = -$taxAmount;
                    }

                    if (! isset($taxBreakdown[$taxName])) {
                        $taxBreakdown[$taxName] = [
                            'name'   => $taxName,
                            'amount' => 0,
                        ];
                    }
                    $taxBreakdown[$taxName]['amount'] += $taxAmount;
                }
            }
        }

        return [
            'summary' => [
                'total_invoices_count' => $outInvoices->count(),
                'total_refunds_count'  => $outRefunds->count(),
                'gross_sales_untaxed'  => $grossSalesUntaxed,
                'gross_sales_tax'      => $grossSalesTax,
                'gross_sales_total'    => $grossSalesTotal,
                'refunds_untaxed'      => $refundsUntaxed,
                'refunds_tax'          => $refundsTax,
                'refunds_total'        => $refundsTotal,
                'net_sales_untaxed'    => $netSalesUntaxed,
                'net_sales_tax'        => $netSalesTax,
                'net_sales_total'      => $netSalesTotal,
            ],
            'payments'     => $paymentsByJournal,
            'taxBreakdown' => array_values($taxBreakdown),
            'invoices'     => $invoices,
        ];
    }
}

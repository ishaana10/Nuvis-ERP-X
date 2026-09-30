<?php

namespace Webkul\Invoice\Filament\Clusters\Reporting\Pages;

use Barryvdh\DomPDF\Facade\Pdf;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Maatwebsite\Excel\Facades\Excel;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Invoice\Filament\Clusters\Reporting;
use Webkul\Invoice\Filament\Clusters\Reporting\Pages\Exports\InvoiceReportExport;
use Webkul\Invoice\Models\Invoice;
use Webkul\Partner\Models\Partner;

class InvoiceReport extends Page implements HasForms
{
    use HasPageShield, InteractsWithForms;

    protected string $view = 'invoices::filament.clusters.reporting.pages.invoice-report';

    protected static ?string $cluster = Reporting::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    protected static function getPagePermission(): ?string
    {
        return 'page_invoice_report';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('invoices::filament/clusters/reporting.pages.invoice-report.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/reporting.pages.invoice-report.navigation.title');
    }

    public function getTitle(): string
    {
        return __('invoices::filament/clusters/reporting.pages.invoice-report.navigation.title');
    }

    public function mount(): void
    {
        $this->form->fill([
            'preset'         => 'all',
            'date_from'      => null,
            'date_to'        => null,
            'due_date_from'  => null,
            'due_date_to'    => null,
            'partners'       => [],
            'payment_states' => [],
            'move_states'    => [MoveState::POSTED->value],
            'move_types'     => [MoveType::OUT_INVOICE->value],
            'group_by'       => 'none',
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('excel')
                ->label(__('invoices::filament/clusters/reporting.pages.invoice-report.actions.export-excel'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $reportData = $this->reportData;

                    $fileName = 'invoice-report-'.now()->format('Y-m-d-His').'.xlsx';

                    return Excel::download(
                        new InvoiceReportExport($reportData, $this->form->getState()),
                        $fileName
                    );
                }),

            Action::make('pdf')
                ->label(__('invoices::filament/clusters/reporting.pages.invoice-report.actions.export-pdf'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('danger')
                ->action(function () {
                    $reportData = $this->reportData;
                    $filters = $this->form->getState();

                    $pdf = Pdf::loadView('invoices::filament.clusters.reporting.pages.pdfs.invoice-report', [
                        'reportData' => $reportData,
                        'filters'    => $filters,
                    ])->setPaper('a4', 'landscape');

                    $fileName = 'invoice-report-'.now()->format('Y-m-d-His').'.pdf';

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
                    'sm'      => 2,
                    'md'      => 4,
                ])
                ->schema([
                    Select::make('preset')
                        ->label(__('invoices::filament/clusters/reporting.pages.invoice-report.filters.preset'))
                        ->options([
                            'all'        => __('invoices::filament/clusters/reporting.pages.invoice-report.filters.preset-options.all'),
                            'unpaid'     => __('invoices::filament/clusters/reporting.pages.invoice-report.filters.preset-options.unpaid'),
                            'short_paid' => __('invoices::filament/clusters/reporting.pages.invoice-report.filters.preset-options.short_paid'),
                            'overdue'    => __('invoices::filament/clusters/reporting.pages.invoice-report.filters.preset-options.overdue'),
                            'customer'   => __('invoices::filament/clusters/reporting.pages.invoice-report.filters.preset-options.customer'),
                        ])
                        ->default('all')
                        ->live()
                        ->afterStateUpdated(function ($state) {
                            $this->applyPreset($state);
                        }),

                    Select::make('group_by')
                        ->label(__('invoices::filament/clusters/reporting.pages.invoice-report.filters.group-by'))
                        ->options([
                            'none'          => __('invoices::filament/clusters/reporting.pages.invoice-report.filters.group-by-options.none'),
                            'customer'      => __('invoices::filament/clusters/reporting.pages.invoice-report.filters.group-by-options.customer'),
                            'payment_state' => __('invoices::filament/clusters/reporting.pages.invoice-report.filters.group-by-options.payment_state'),
                            'status'        => __('invoices::filament/clusters/reporting.pages.invoice-report.filters.group-by-options.status'),
                        ])
                        ->default('none')
                        ->live(),

                    DatePicker::make('date_from')
                        ->label(__('invoices::filament/clusters/reporting.pages.invoice-report.filters.date-from'))
                        ->native(false)
                        ->suffixIcon('heroicon-o-calendar')
                        ->live(),

                    DatePicker::make('date_to')
                        ->label(__('invoices::filament/clusters/reporting.pages.invoice-report.filters.date-to'))
                        ->native(false)
                        ->suffixIcon('heroicon-o-calendar')
                        ->live(),

                    DatePicker::make('due_date_from')
                        ->label(__('invoices::filament/clusters/reporting.pages.invoice-report.filters.due-date-from'))
                        ->native(false)
                        ->suffixIcon('heroicon-o-calendar')
                        ->live(),

                    DatePicker::make('due_date_to')
                        ->label(__('invoices::filament/clusters/reporting.pages.invoice-report.filters.due-date-to'))
                        ->native(false)
                        ->suffixIcon('heroicon-o-calendar')
                        ->live(),

                    Select::make('partners')
                        ->label(__('invoices::filament/clusters/reporting.pages.invoice-report.filters.customers'))
                        ->multiple()
                        ->options(Partner::pluck('name', 'id'))
                        ->searchable()
                        ->live(),

                    Select::make('payment_states')
                        ->label(__('invoices::filament/clusters/reporting.pages.invoice-report.filters.payment-state'))
                        ->multiple()
                        ->options([
                            PaymentState::NOT_PAID->value => PaymentState::NOT_PAID->getLabel(),
                            PaymentState::PARTIAL->value  => PaymentState::PARTIAL->getLabel(),
                            PaymentState::PAID->value     => PaymentState::PAID->getLabel(),
                            PaymentState::REVERSED->value => PaymentState::REVERSED->getLabel(),
                            PaymentState::BLOCKED->value  => PaymentState::BLOCKED->getLabel(),
                        ])
                        ->searchable()
                        ->live(),

                    Select::make('move_states')
                        ->label(__('invoices::filament/clusters/reporting.pages.invoice-report.filters.move-state'))
                        ->multiple()
                        ->options([
                            MoveState::DRAFT->value  => MoveState::DRAFT->getLabel(),
                            MoveState::POSTED->value => MoveState::POSTED->getLabel(),
                            MoveState::CANCEL->value => MoveState::CANCEL->getLabel(),
                        ])
                        ->searchable()
                        ->live(),

                    Select::make('move_types')
                        ->label(__('invoices::filament/clusters/reporting.pages.invoice-report.filters.move-type'))
                        ->multiple()
                        ->options([
                            MoveType::OUT_INVOICE->value => MoveType::OUT_INVOICE->getLabel(),
                            MoveType::OUT_REFUND->value  => MoveType::OUT_REFUND->getLabel(),
                            MoveType::IN_INVOICE->value  => MoveType::IN_INVOICE->getLabel(),
                            MoveType::IN_REFUND->value   => MoveType::IN_REFUND->getLabel(),
                        ])
                        ->searchable()
                        ->live(),
                ])
                ->columnSpanFull(),
        ];
    }

    protected function applyPreset(?string $preset): void
    {
        $currentState = $this->form->getState();

        switch ($preset) {
            case 'unpaid':
                $this->form->fill(array_merge($currentState, [
                    'preset'         => 'unpaid',
                    'payment_states' => [PaymentState::NOT_PAID->value],
                    'due_date_to'    => null,
                ]));
                break;

            case 'short_paid':
                $this->form->fill(array_merge($currentState, [
                    'preset'         => 'short_paid',
                    'payment_states' => [PaymentState::PARTIAL->value],
                    'due_date_to'    => null,
                ]));
                break;

            case 'overdue':
                $this->form->fill(array_merge($currentState, [
                    'preset'         => 'overdue',
                    'payment_states' => [PaymentState::NOT_PAID->value, PaymentState::PARTIAL->value],
                    'move_states'    => [MoveState::POSTED->value],
                    'due_date_to'    => now()->toDateString(),
                ]));
                break;

            case 'customer':
                $this->form->fill(array_merge($currentState, [
                    'preset'         => 'customer',
                    'group_by'       => 'customer',
                    'payment_states' => [],
                    'due_date_to'    => null,
                ]));
                break;

            case 'all':
            default:
                $this->form->fill(array_merge($currentState, [
                    'preset'         => 'all',
                    'payment_states' => [],
                    'due_date_to'    => null,
                ]));
                break;
        }
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
            ->with(['partner', 'currency', 'company']);

        if (! empty($state['date_from'])) {
            $query->whereDate('invoice_date', '>=', $state['date_from']);
        }

        if (! empty($state['date_to'])) {
            $query->whereDate('invoice_date', '<=', $state['date_to']);
        }

        if (! empty($state['due_date_from'])) {
            $query->whereDate('invoice_date_due', '>=', $state['due_date_from']);
        }

        if (! empty($state['due_date_to'])) {
            $query->whereDate('invoice_date_due', '<=', $state['due_date_to']);
        }

        if (! empty($state['partners'])) {
            $query->whereIn('partner_id', $state['partners']);
        }

        if (! empty($state['payment_states'])) {
            $query->whereIn('payment_state', $state['payment_states']);
        }

        if (! empty($state['move_states'])) {
            $query->whereIn('state', $state['move_states']);
        }

        if (! empty($state['move_types'])) {
            $query->whereIn('move_type', $state['move_types']);
        } else {
            $query->whereIn('move_type', [MoveType::OUT_INVOICE->value, MoveType::OUT_REFUND->value]);
        }

        $invoices = $query->orderBy('invoice_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $totalInvoices = $invoices->count();
        $totalAmount = $invoices->sum('amount_total');
        $totalResidual = $invoices->sum('amount_residual');
        $totalPaid = $totalAmount - $totalResidual;

        $groupBy = $state['group_by'] ?? 'none';
        $groupedData = [];

        if ($groupBy === 'customer') {
            $groupedData = $invoices->groupBy(fn ($invoice) => $invoice->partner?->name ?? 'Unassigned Customer');
        } elseif ($groupBy === 'payment_state') {
            $groupedData = $invoices->groupBy(fn ($invoice) => $invoice->payment_state?->getLabel() ?? 'Unknown');
        } elseif ($groupBy === 'status') {
            $groupedData = $invoices->groupBy(fn ($invoice) => $invoice->state?->getLabel() ?? 'Unknown');
        }

        return [
            'invoices'        => $invoices,
            'groupedData'     => $groupedData,
            'groupBy'         => $groupBy,
            'stats'           => [
                'total_count'    => $totalInvoices,
                'total_amount'   => $totalAmount,
                'total_paid'     => $totalPaid,
                'total_residual' => $totalResidual,
            ],
        ];
    }
}
